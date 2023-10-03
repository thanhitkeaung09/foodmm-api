<?php

declare(strict_types=1);

namespace App\Services;

use App\Builders\RestaurantBuilder;
use App\Builders\ShopBuilder;
use App\Dto\CountData;
use App\Dto\RestaurantData;
use App\Enums\LimitType;
use App\Models\Food;
use App\Models\FoodType;
use App\Models\Rating;
use App\Models\Restaurant;
use App\Models\RestaurantCategory;
use App\Models\Review;
use App\Models\Shop;
use App\Services\FileStorage\FileStorageService;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RestaurantService
{
    public function __construct(
        private readonly RestaurantCategoryService $categoryService,
        private readonly FoodService $foodService,
        private readonly FoodCategoryService $foodCategoryService,
        private readonly FoodTypeService $foodTypeService,
        private readonly RatingService $ratingService,
        private readonly SettingService $settingService,
        private readonly LocationService $locationService,
        private readonly CountService $countService,
        private readonly FileStorageService $fileStorageService,
    ) {
    }

    public function getAllNames(?int $cityId, ?string $name): array
    {
        $restaurants = Restaurant::query()
            ->when($cityId, function (RestaurantBuilder $q, int $cityId) {
                $q->whereCityId($cityId);
            })
            ->when($name, function (RestaurantBuilder $q, string $name) {
                $q->where('name', 'like', "%{$name}%");
            })
            ->without('images')
            ->get(['id', 'name']);

        $shops = Shop::query()
            ->when($cityId, function (ShopBuilder $q, int $cityId) {
                $q->whereCityId($cityId);
            })
            ->when($name, function (ShopBuilder $q, string $name) {
                $q->where('name', 'like', "%{$name}%");
            })
            ->without('images')
            ->get(['id', 'name']);

        return [
            'restaurants' => $restaurants,
            'shops' => $shops,
        ];
    }

    public function getMenus(Restaurant $restaurant, ?string $name, ?bool $special): Collection
    {
        $menus = $restaurant->menus()
            ->when($name, function (Builder $q, $name) {
                $q->where('name', 'like', "%{$name}%");
            })
            ->select('food_id as id', 'name', 'food_type_id as see_all_id')->get();

        if (is_null($special)) {
            return $this->calculateMenusRating($menus);
        }

        return $this->calculateMenusRating($menus)
            ->filter(fn ($menu) => (bool)$menu->pivot->is_special === $special)
            ->values();
    }

    public function findById(int $id): Restaurant
    {
        $restaurant = Restaurant::query()->with([
            'reviews', 'reviews.user:id,name,profile_image',
            'location.township.city.state',
            'menus.ratings', 'ratings',
        ])->findOrFail($id);

        $menus = $this->calculateMenusRating($restaurant->menus);

        unset($restaurant->menus);

        $restaurant->specialMenus = $menus->filter(fn ($menu) => $menu->pivot->is_special)->values();

        $restaurant->menus = $menus->filter(fn ($menu) => !$menu->pivot->is_special)->values();

        $restaurant->averageRating = $restaurant->averageRating();

        $this->mergeReviewAndRating($restaurant);

        unset($restaurant->ratings);

        $restaurant->type = 'restaurants';

        return $restaurant;
    }

    public function findBestSlider(int $cityId): Collection
    {
        return $this->getBestRestaurantsSliderQuery($cityId)->limit(20)->get();
    }

    public function findBest(
        int $cityId,
        ?string $query = null,
        LimitType $limitType = LimitType::PAGINATE,
    ): LengthAwarePaginator|Collection {
        $query = $query ?? '';
        $userId = (int) request()->query('user_id');

        if ($limitType === LimitType::PAGINATE) {
            return $this->getBestRestaurantsQuery($cityId, $query)
                ->paginate($limitType->value)
                ->through(function ($restaurant) use ($userId) {
                    if ($userId === 0) {
                        $restaurant->is_planned = false;
                    } else {
                        $plans = $restaurant->plans->filter(function ($plan) use ($userId) {
                            if ($plan->user_id !== $userId) {
                                return false;
                            }
                            return now()->lessThanOrEqualTo(
                                Carbon::parse($plan->planed_at)
                            );
                        });
                        $restaurant->is_planned = count($plans) > 0;
                    }
                    unset($restaurant->plans);
                });
        }

        return $this->getBestRestaurantsQuery($cityId, $query)
            ->limit($limitType->value)
            ->get()
            ->each(function ($restaurant) use ($userId) {
                if ($userId === 0) {
                    $restaurant->is_planned = false;
                } else {
                    $plans = $restaurant->plans->filter(function ($plan) use ($userId) {
                        if ($plan->user_id !== $userId) {
                            return false;
                        }
                        return now()->lessThanOrEqualTo(
                            Carbon::parse($plan->planed_at)
                        );
                    });
                    $restaurant->is_planned = count($plans) > 0;
                }
                unset($restaurant->plans);
            });
    }

    public function getGroupBy(int $cityId): Collection
    {
        $userId = (int) request()->query('user_id');
        $categories = $this->categoryService->getAll(['id', 'slug']);

        return $categories->map(function (RestaurantCategory $category) use ($cityId, $userId) {
            $restaurants = $this->getBestRestaurantsQuery($cityId)
                ->whereCategoryId($category->id)
                ->limit(4)->get()->each(function ($restaurant) use ($userId) {
                    if ($userId === 0) {
                        $restaurant->is_planned = false;
                    } else {
                        $plans = $restaurant->plans->filter(function ($plan) use ($userId) {
                            if ($plan->user_id !== $userId) {
                                return false;
                            }
                            return now()->lessThanOrEqualTo(
                                Carbon::parse($plan->planed_at)
                            );
                        });
                        $restaurant->is_planned = count($plans) > 0;
                    }
                    unset($restaurant->plans);
                });

            return ['lists' => $restaurants, 'category_name' => $category->slug];
        });
    }

    public function getGroupByCuisineType(int $cityId): Collection
    {
        $userId = (int) request()->query('user_id');
        $types = $this->foodTypeService->getCuisineTypes();

        if ($types->count() === 0) {
            return collect();
        }

        $data = $types->map(function (FoodType $type) use ($cityId, $userId) {
            $cuisines = $this->getBestRestaurantsQuery($cityId)
                ->whereTypeId($type->id)
                ->limit(4)->get()->each(function ($food) use ($userId) {
                    if ($userId === 0) {
                        $food->is_planned = false;
                    } else {
                        $plans = $food->plans->filter(function ($plan) use ($userId) {
                            if ($plan->user_id !== $userId) {
                                return false;
                            }
                            return now()->lessThanOrEqualTo(
                                Carbon::parse($plan->planed_at)
                            );
                        });
                        $food->is_planned = count($plans) > 0;
                    }
                    unset($food->plans);
                });

            return ['lists' => $cuisines, 'category_name' => $type->slug];
        });

        $data->push([
            'lists' => $this->getBestRestaurantsQuery($cityId)->limit(4)->get(),
            'category_name' => 'all'
        ]);

        return $data;
    }

    public function getAllByCategory(
        string $slug,
        int $cityId,
        ?string $query,
        LimitType $limitType = LimitType::PAGINATE,
    ): LengthAwarePaginator|Collection {
        $name = $query ?? '';

        $builder = $this->getBestRestaurantsQuery($cityId, $name)
            ->when($slug !== 'all', function ($q) use ($slug) {
                $q->whereHas('category', function ($q) use ($slug) {
                    $q->where('slug', $slug);
                });
            });

        if ($limitType === LimitType::PAGINATE) {
            return $builder->paginate(LimitType::PAGINATE->value);
        }

        return $builder->limit($limitType->value)->get();
    }

    public function getAllByFoodType(
        string $slug,
        int $cityId,
        ?string $query,
        LimitType $limitType = LimitType::PAGINATE,
    ): LengthAwarePaginator|Collection {
        $name = $query ?? '';

        $builder = $this->getBestRestaurantsQuery($cityId, $name)
            ->when($slug !== 'all', function ($q) use ($slug) {
                $cuisineId = $this->foodCategoryService->findCuisine()?->id;

                $q->whereHas('menus.type', function ($q) use ($slug, $cuisineId) {
                    $q->where('food_category_id', $cuisineId)->where('slug', $slug);
                });
            });

        if ($limitType === LimitType::PAGINATE) {
            return $builder->paginate(LimitType::PAGINATE->value);
        }

        return $builder->limit($limitType->value)->get();
    }

    public function getAll(array $column = ['*']): Collection
    {
        return Restaurant::query()->get(
            columns: $column,
        );
    }

    public function getPaginate(array $column = ['*']): LengthAwarePaginator
    {
        return Restaurant::query()->with([
            'location', 'category', 'menus'
        ])->paginate(
            columns: $column,
            perPage: LimitType::PAGINATE->value,
        );
    }

    public function create(RestaurantData $data): Restaurant
    {
        $location = $this->locationService->create($data->location);

        $this->countService->increase($location->township->city_id, new CountData(restaurantCount: 1));

        $restaurant = Restaurant::query()->create([
            ...$data->toArray(),
            'location_id' => $location->id,
        ]);

        $this->createImages($data->images, $restaurant);

        return $restaurant;
    }

    public function update(Restaurant $restaurant, RestaurantData $data): bool
    {
        if ($data->images->count() > 0 && $data->images->count() < 4) {
            // $restaurant->images()->forceDelete();

            $this->createImages($data->images, $restaurant);
        }

        $restaurant->location()->update($data->location->toArray());

        return $restaurant->update($data->toArray());
    }

    public function delete(Restaurant $restaurant): bool
    {
        $this->countService->decrease($restaurant->location->township->city_id, new CountData(shopCount: 1));

        $restaurant->location()->delete();

        $restaurant->menus()->delete();

        $restaurant->reviews()->delete();

        $restaurant->update(['name' => $restaurant->name . '_' . now()->timestamp]);

        return $restaurant->delete();
    }

    public function removeMenu(Restaurant $restaurant, int $foodId): bool
    {
        try {
            $restaurant->menus()->detach($foodId);

            $this->decreaseCount($restaurant, $foodId);

            return true;
        } catch (Exception) {

            return false;
        }
    }

    public function createMenus(Restaurant $restaurant, array $foodIds): bool
    {
        $exists = $this->foodService->existsRestaurantOrShop($foodIds);

        if ($exists) {
            throw new Exception('Those food items have restaurants or shops!');
        }

        $this->increaseCount($restaurant, $foodIds);

        try {
            $restaurant->menus()->attach($foodIds);

            return true;
        } catch (Exception) {

            return false;
        }
    }

    public function getReviews(int $id): LengthAwarePaginator
    {
        return Review::query()
            ->with('user')
            ->where('reviewable_type', Restaurant::class)
            ->where('reviewable_id', $id)
            ->paginate(LimitType::PAGINATE->value);
    }

    public function getRatings(int $id)
    {
        return Rating::query()
            ->with('user')
            ->where('rateable_type', Restaurant::class)
            ->where('rateable_id', $id)
            ->paginate(LimitType::PAGINATE->value)
            ->through(function ($rating) {
                $rating->rating = $rating->rate;
                return $rating;
            });
    }

    private function increaseCount(Restaurant $restaurant, array $ids): void
    {
        $cuisineId = $this->foodCategoryService->findCuisine()?->id;

        $cuisines = $this->foodService->findByIds($ids)->filter(
            fn (Food $f) =>
            $f->type->food_category_id === $cuisineId
        )->count();

        if ($cuisines > 0) {
            $this->countService->increase($restaurant->location->township->city_id, new CountData(cuisineCount: count($ids)));
        } else {
            $this->countService->increase($restaurant->location->township->city_id, new CountData(foodCount: count($ids)));
        }
    }

    private function decreaseCount(Restaurant $restaurant, int $id): void
    {
        $cuisineId = $this->foodCategoryService->findCuisine()?->id;

        $food = Food::query()->with('type')->find($id);

        if ($food->type->food_category_id === $cuisineId) {
            $this->countService->increase($restaurant->location->township->city_id, new CountData(cuisineCount: 1));
        } else {
            $this->countService->increase($restaurant->location->township->city_id, new CountData(foodCount: 1));
        }
    }

    private function createImages(Collection $images, Restaurant $restaurant): void
    {
        if ($images->count() > 0) {
            $paths = $images->map(function ($image) {
                return [
                    'path' => $this->fileStorageService->upload(
                        \config('filesystems.folders.restaurants'),
                        $image
                    )
                ];
            });

            $restaurant->images()->createMany($paths);
        }
    }

    private function updateAverageRating(RatingData $data): void
    {
        $ratings = $data->model->ratings;

        if ($ratings->count() > 0) {
            $averageRating = $this->ratingService->calculateTotalAverage($ratings);
        }

        if ($ratings->count() === 0) {
            $averageRating = $data->ratings->sum() / $data->ratings->count();
        }

        $data->model->ratings()->update(['average_rate' => $averageRating]);
    }

    private function createRatings(RatingData $data): void
    {
        $ratingTypes = RatingType::query()->get();

        $data->ratings->each(function ($item, $key) use ($data, $ratingTypes) {
            $rateType = $ratingTypes->where('name', str_replace('_', ' ', Str::title($key)))
                ->first();

            $data->model->ratings()->updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'rating_type_id' => $rateType->id,
                ],
                [
                    'rate' => $item,
                ]
            );
        });
    }

    private function mergeReviewAndRating(Restaurant $restaurant): void
    {
        $restaurant->reviews->each(function ($review) use ($restaurant) {
            $review->rating = $this->ratingService->averagePerUser($restaurant->ratings, $review->user_id);
        });
    }

    private function calculateMenusRating(Collection $menus): Collection
    {
        return $menus->map(function ($menu) {
            $menu->averageRating = $menu->averageRating();

            unset($menu->ratings);

            return $menu;
        });
    }

    private function getBaseQuery(int $cityId, string $name = '', bool $isRecommended = false): RestaurantBuilder
    {
        return Restaurant::query()
            ->when($isRecommended, function (RestaurantBuilder $q) {
                $q->whereRecommendedFoods();
            })
            ->with(['location'])
            ->searchByName($name)
            ->findBest()
            ->whereCityId($cityId);
    }

    private function getBestRestaurantsSliderQuery(int $cityId): RestaurantBuilder
    {
        return $this->getBaseQuery($cityId, isRecommended: false)
            ->select([
                DB::raw("restaurants.id, ANY_VALUE(restaurants.name) as name, ANY_VALUE(restaurants.description) as description, ANY_VALUE(restaurants.phones) as phones, ANY_VALUE(restaurants.opening_hours) as opening_hours, ANY_VALUE(restaurants.location_id) as location_id, ANY_VALUE(restaurants.category_id) as see_all_id"),
                DB::raw('ANY_VALUE(ra.average_rate) as average_rate')
            ])
            ->withCount('reviews');
    }

    private function getBestRestaurantsQuery(int $cityId, string $name = ''): RestaurantBuilder
    {
        $isRecommended = $this->settingService->isRecommended();

        return $this->getBaseQuery($cityId, $name, $isRecommended)
            ->select([
                DB::raw("restaurants.id, ANY_VALUE(restaurants.name) as name, ANY_VALUE(restaurants.description) as description, ANY_VALUE(restaurants.phones) as phones, ANY_VALUE(restaurants.opening_hours) as opening_hours, ANY_VALUE(restaurants.location_id) as location_id, ANY_VALUE(restaurants.category_id) as see_all_id"),
                DB::raw('ANY_VALUE(ra.average_rate) as average_rate')
            ])
            ->withCount('reviews');
    }
}
