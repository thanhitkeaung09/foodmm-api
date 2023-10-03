<?php

declare(strict_types=1);

namespace App\Services;

use App\Builders\ShopBuilder;
use App\Dto\CountData;
use App\Dto\ShopData;
use App\Enums\LimitType;
use App\Models\Food;
use App\Models\Rating;
use App\Models\Review;
use App\Models\Shop;
use App\Models\ShopCategory;
use App\Services\FileStorage\FileStorageService;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ShopService
{
    public function __construct(
        private readonly RatingService $ratingService,
        private readonly FoodService $foodService,
        private readonly FoodCategoryService $foodCategoryService,
        private readonly ShopCategoryService $categoryService,
        private readonly SettingService $settingService,
        private readonly LocationService $locationService,
        private readonly CountService $countService,
        private readonly FileStorageService $fileStorageService,
    ) {
    }

    public function getMenus(Shop $shop, ?string $name, ?bool $special): Collection
    {
        $items =  $shop->items()
            ->when($name, function (Builder $q, $name) {
                $q->where('name', 'like', "%{$name}%");
            })
            ->select('food_id as id', 'name', 'food_type_id as see_all_id')->get();

        if (is_null($special)) {
            return  $this->calculateItemsRating($items);
        }

        return $this->calculateItemsRating($items)
            ->filter(fn ($item) => (bool)$item->pivot->is_special === $special)
            ->values();
    }

    public function findById(int $id): Shop
    {
        $shop = Shop::query()->with([
            'reviews', 'reviews.user:id,name,profile_image',
            'location.township.city.state',
            'items.ratings', 'ratings',
        ])->findOrFail($id);

        $items = $this->calculateItemsRating($shop->items);
        $shop->specialMenus = $items->filter(fn ($item) => $item->pivot->is_special)->values();
        $shop->menus = $items->filter(fn ($item) => !$item->pivot->is_special)->values();

        $this->mergeReviewAndRating($shop);

        $shop->averageRating = $shop->averageRating();

        unset($shop->ratings, $shop->items);

        $shop->type = 'shops';

        return $shop;
    }

    public function findBestSlider(int $cityId): Collection
    {
        return $this->getBestShopsSliderQuery($cityId)->limit(20)->get();
    }

    public function findBest(
        int $cityId,
        ?string $query = null,
        LimitType $limitType = LimitType::PAGINATE,
    ): LengthAwarePaginator|Collection {
        $query = $query ?? '';
        $userId = (int) request()->query('user_id');

        if ($limitType === LimitType::PAGINATE) {
            return $this->getBestShopsQuery($cityId, $query)
                ->paginate($limitType->value)
                ->through(function ($shop) use ($userId) {
                    if ($userId === 0) {
                        $shop->is_planned = false;
                    } else {
                        $plans = $shop->plans->filter(function ($plan) use ($userId) {
                            if ($plan->user_id !== $userId) {
                                return false;
                            }
                            return now()->lessThanOrEqualTo(
                                Carbon::parse($plan->planed_at)
                            );
                        });
                        $shop->is_planned = count($plans) > 0;
                    }
                    unset($shop->plans);
                });
        }

        return $this->getBestShopsQuery($cityId, $query)
            ->limit($limitType->value)
            ->get()
            ->each(function ($shop) use ($userId) {
                if ($userId === 0) {
                    $shop->is_planned = false;
                } else {
                    $plans = $shop->plans->filter(function ($plan) use ($userId) {
                        if ($plan->user_id !== $userId) {
                            return false;
                        }
                        return now()->lessThanOrEqualTo(
                            Carbon::parse($plan->planed_at)
                        );
                    });
                    $shop->is_planned = count($plans) > 0;
                }
                unset($shop->plans);
            });
    }

    public function getGroupBy(int $cityId): Collection
    {
        $userId = (int) request()->query('user_id');
        $categories = $this->categoryService->getAll(['id', 'slug']);

        return $categories->map(function (ShopCategory $category) use ($cityId, $userId) {
            $shops = $this->getBestShopsQuery($cityId)
                ->whereCategoryId($category->id)
                ->limit(4)->get()->each(function ($shop) use ($userId) {
                    if ($userId === 0) {
                        $shop->is_planned = false;
                    } else {
                        $plans = $shop->plans->filter(function ($plan) use ($userId) {
                            if ($plan->user_id !== $userId) {
                                return false;
                            }
                            return now()->lessThanOrEqualTo(
                                Carbon::parse($plan->planed_at)
                            );
                        });
                        $shop->is_planned = count($plans) > 0;
                    }
                    unset($shop->plans);
                });

            return ['lists' => $shops, 'category_name' => $category->slug];
        });
    }

    public function getAllByCategory(
        string $slug,
        int $cityId,
        ?string $query,
        LimitType $limitType = LimitType::PAGINATE,
    ): LengthAwarePaginator|Collection {
        $name = $query ?? '';

        $builder = $this->getBestShopsQuery($cityId, $name)
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

    public function getAll(array $column = ['*']): Collection
    {
        return Shop::query()->get(
            columns: $column,
        );
    }

    public function getPaginate(array $column = ['*']): LengthAwarePaginator
    {
        return Shop::query()->with(['location', 'category', 'items'])->paginate(
            columns: $column,
            perPage: LimitType::PAGINATE->value,
        );
    }

    public function create(ShopData $data): Shop
    {
        $location = $this->locationService->create($data->location);

        $this->countService->increase($location->township->city_id, new CountData(shopCount: 1));

        $shop = Shop::query()->create([
            ...$data->toArray(),
            'location_id' => $location->id,
        ]);

        $this->createImages($data->images, $shop);

        return $shop;
    }

    public function update(Shop $shop, ShopData $data): bool
    {
        if ($data->images->count() > 0 && $data->images->count() < 4) {
            // $shop->images()->forceDelete();

            $this->createImages($data->images, $shop);
        }

        $shop->location()->update($data->location->toArray());

        return $shop->update($data->toArray());
    }

    public function delete(Shop $shop): bool
    {
        $this->countService->decrease($shop->location->township->city_id, new CountData(restaurantCount: 1));

        $shop->location()->delete();

        $shop->items()->delete();

        $shop->reviews()->delete();

        $shop->update(['name' => $shop->name . '_' . now()->timestamp]);

        return $shop->delete();
    }

    public function createItems(Shop $shop, array $foodIds): bool
    {
        $exists = $this->foodService->existsRestaurantOrShop($foodIds);

        if ($exists) {
            throw new Exception('Those food items have restaurants or shops!');
        }

        $this->increaseCount($shop, $foodIds);

        try {
            $shop->items()->attach($foodIds);

            return true;
        } catch (Exception) {

            return false;
        }
    }

    public function removeItem(Shop $shop, int $foodId): bool
    {
        try {
            $shop->items()->detach($foodId);

            $this->decreaseCount($shop, $foodId);

            return true;
        } catch (Exception) {

            return false;
        }
    }

    public function getReviews(int $id): LengthAwarePaginator
    {
        return Review::query()
            ->with('user')
            ->where('reviewable_type', Shop::class)
            ->where('reviewable_id', $id)
            ->paginate(LimitType::PAGINATE->value);
    }

    public function getRatings(int $id)
    {
        return Rating::query()
            ->with('user')
            ->where('rateable_type', Shop::class)
            ->where('rateable_id', $id)
            ->paginate(LimitType::PAGINATE->value)
            ->through(function ($rating) {
                $rating->rating = $rating->rate;
                return $rating;
            });
    }

    private function increaseCount(Shop $shop, array $ids): void
    {
        $cuisineId = $this->foodCategoryService->findCuisine()?->id;

        $cuisines = $this->foodService->findByIds($ids)->filter(
            fn (Food $f) =>
            $f->type->food_category_id === $cuisineId
        )->count();

        if ($cuisines > 0) {
            $this->countService->increase($shop->location->township->city_id, new CountData(cuisineCount: count($ids)));
        } else {
            $this->countService->increase($shop->location->township->city_id, new CountData(foodCount: count($ids)));
        }
    }

    private function decreaseCount(Shop $shop, int $id): void
    {
        $cuisineId = $this->foodCategoryService->findCuisine()?->id;

        $food = Food::query()->with('type')->find($id);

        if ($food->type->food_category_id === $cuisineId) {
            $this->countService->increase($shop->location->township->city_id, new CountData(cuisineCount: 1));
        } else {
            $this->countService->increase($shop->location->township->city_id, new CountData(foodCount: 1));
        }
    }

    private function createImages(Collection $images, Shop $shop): void
    {
        if ($images->count() > 0) {
            $paths = $images->map(function ($image) {
                return [
                    'path' => $this->fileStorageService->upload(
                        \config('filesystems.folders.shops'),
                        $image
                    )
                ];
            });

            $shop->images()->createMany($paths);
        }
    }

    private function mergeReviewAndRating($shop): void
    {
        $shop->reviews->each(function ($review) use ($shop) {
            $review->rating = $shop->ratings->where(
                fn ($rating) => $rating->user_id === $review->user_id
            )->first()?->average_rate ?? '0.0';
        });
    }

    private function calculateItemsRating(Collection $items): Collection
    {
        return $items->map(function ($item) {
            $item->averageRating = $item->averageRating();

            unset($item->ratings);

            return $item;
        });
    }

    private function getBaseQuery(int $cityId, string $name = '', bool $isRecommended = false): ShopBuilder
    {
        return Shop::query()
            ->when($isRecommended, function (ShopBuilder $q) {
                $q->whereRecommendedFoods();
            })
            ->with(['location'])
            ->searchByName($name)
            ->findBest()
            ->whereCityId($cityId);
    }

    private function getBestShopsSliderQuery(int $cityId): ShopBuilder
    {
        return $this->getBaseQuery($cityId, isRecommended: false)
            ->select([
                DB::raw("shops.id, ANY_VALUE(shops.name) as name, ANY_VALUE(shops.description) as description, ANY_VALUE(shops.phones) as phones, ANY_VALUE(shops.opening_hours) as opening_hours, ANY_VALUE(shops.location_id) as location_id, ANY_VALUE(shops.category_id) as see_all_id"),
                DB::raw('ANY_VALUE(ra.average_rate) as average_rate')
            ])
            ->withCount('reviews');
    }

    private function getBestShopsQuery(int $cityId, string $name = ''): ShopBuilder
    {
        $isRecommended = $this->settingService->isRecommended();

        return $this->getBaseQuery($cityId, $name, $isRecommended)
            ->select([
                DB::raw("shops.id, ANY_VALUE(shops.name) as name, ANY_VALUE(shops.description) as description, ANY_VALUE(shops.phones) as phones, ANY_VALUE(shops.opening_hours) as opening_hours, ANY_VALUE(shops.location_id) as location_id, ANY_VALUE(shops.category_id) as see_all_id"),
                DB::raw('ANY_VALUE(ra.average_rate) as average_rate')
            ])
            ->withCount('reviews');
    }
}
