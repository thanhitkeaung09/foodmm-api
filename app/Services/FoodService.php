<?php

declare(strict_types=1);

namespace App\Services;

use App\Builders\FoodBuilder;
use App\Dto\FoodData;
use App\Dto\RatingData;
use App\Enums\LimitType;
use App\Models\Food;
use App\Models\FoodCategory;
use App\Models\FoodType;
use App\Models\Rating;
use App\Models\RatingType;
use App\Models\Review;
use App\Services\FileStorage\SpaceStorage;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FoodService
{
    public function __construct(
        private readonly FoodCategoryService $foodCategoryService,
        private readonly FoodTypeService $foodTypeService,
        private readonly RatingService $ratingService,
        private readonly SpaceStorage $spaceStorage,
        private readonly SettingService $settingService,
        private readonly CountService $countService,
    ) {
    }

    public function findById(int $id)
    {
        $food = Food::query()
            ->with(['reviews', 'ratings', 'reviews.user:id,name,profile_image'])
            ->with(['restaurants' => function (BelongsToMany $q) {
                $q->select('restaurants.id', 'restaurants.name')->without('images');
            }])
            ->with(['shops' => function (BelongsToMany $q) {
                $q->select('shops.id', 'shops.name')->without('images');
            }])
            ->findOrFail($id);

        $food->averageRating = $food->averageRating();

        $this->mergeReviewAndRating($food);

        unset($food->ratings);

        $food->type = 'foods';

        return $food;
    }

    public function findByIds(array $ids): Collection
    {
        return Food::query()->with('type')->whereIn('id', $ids)->get();
    }

    public function getBestFoods(): Collection
    {
        return Food::query()
            ->findBest()
            ->with('reviews.user')
            ->select([
                DB::raw("food.id, ANY_VALUE(food.name) as name"),
                DB::raw('ANY_VALUE(ra.average_rate) as average_rate')
            ])
            ->limit(5)
            ->get();
    }

    public function findBestSlider(int $cityId): Collection
    {
        return $this->getBestFoodsSliderQuery($cityId)->limit(20)->get();
    }

    public function findBest(
        int $cityId,
        ?string $query = null,
        LimitType $limitType = LimitType::PAGINATE,
    ): LengthAwarePaginator|Collection {
        $name = $query ?? '';
        $userId = (int)request()->query('user_id');

        if ($limitType === LimitType::PAGINATE) {
            return $this->getBestFoodsQuery($cityId, $name)
                ->paginate($limitType->value)
                ->through(function ($food) use ($userId) {
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
        }

        return $this->getBestFoodsQuery($cityId, $name)
            ->limit($limitType->value)
            ->get()
            ->each(function ($food) use ($userId) {
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
    }

    public function findBestCuisinesSlider(int $cityId): Collection
    {
        return $this->getBestCuisinesSliderQuery($cityId)->limit(20)->get();
    }

    public function findBestCuisines(
        int $cityId,
        ?string $query = null,
        LimitType $limitType = LimitType::PAGINATE
    ): LengthAwarePaginator|Collection {
        $name = $query ?? '';
        $userId = (int) request()->query('user_id');

        if (LimitType::PAGINATE === $limitType) {
            return $this->getBestCuisinesQuery($cityId, $name)
                ->paginate($limitType->value)
                ->through(function ($food) use ($userId) {
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
        }

        return $this->getBestCuisinesQuery($cityId, $name)
            ->limit($limitType->value)
            ->get()
            ->each(function ($food) use ($userId) {
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
    }

    public function getGroupBy(int $cityId): Collection
    {
        $userId = (int) request()->query('user_id');
        $categories = $this->foodCategoryService->getAll(['id', 'slug']);

        return $categories->map(function (FoodCategory $category) use ($cityId, $userId) {
            $foods = $this->getBestFoodsQuery($cityId)
                ->whereCategoryId($category->id)
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

            return ['lists' => $foods, 'category_name' => $category->slug];
        });
    }

    public function getAllByCategory(
        string $slug,
        int $cityId,
        ?string $query,
        LimitType $limitType = LimitType::PAGINATE,
    ): LengthAwarePaginator|Collection {
        $name = $query ?? '';

        $builder = $this->getBestFoodsQuery($cityId, $name)
            ->when($slug !== 'all', function ($q) use ($slug) {
                $q->whereHas('type.category', function ($q) use ($slug) {
                    $q->where('slug', $slug);
                });
            });

        if ($limitType === LimitType::PAGINATE) {
            return $builder->paginate(LimitType::PAGINATE->value);
        }

        return $builder->limit($limitType->value)->get();
    }

    public function getCuisinesByType(int $cityId): Collection
    {
        $types = $this->foodTypeService->getCuisineTypes();

        if ($types->count() === 0) {
            return collect();
        }

        $data = $types->reduce(function (Collection $data, FoodType $type) use ($cityId) {
            $cuisines = $this->getBestCuisinesQuery($cityId)
                ->whereTypeId($type->id)
                ->limit(4)->get();

            $data->put($type->slug, $cuisines);

            return $data;
        }, collect());

        $data->put('all', $this->getBestCuisinesQuery($cityId)->limit(4)->get());

        return $data;
    }

    public function addRating(RatingData $data): bool
    {
        $this->createRatings($data);

        $this->updateAverageRating($data);

        $this->createReviews($data);

        return true;
    }

    public function getFreeAll($column = ['*'])
    {
        return Food::query()
            ->doesntHave('restaurants')
            ->doesntHave('shops')
            ->get($column);
    }

    public function getAll($column = ['*']): Collection
    {
        $cuisineId = $this->foodCategoryService->findCuisine()?->id;

        return Food::query()->whereNotCategoryId($cuisineId)->get($column);
    }

    public function getCuisines($column = ['*']): LengthAwarePaginator
    {
        $cuisineId = $this->foodCategoryService->findCuisine()?->id;

        return Food::query()
            ->with('type')
            ->whereCategoryId($cuisineId)
            ->orderBy('id')
            ->paginate(
                columns: $column,
                perPage: LimitType::PAGINATE->value,
            );
    }

    public function getPaginate($column = ['*']): LengthAwarePaginator
    {
        return Food::query()->with([
            'type',
            'type.category',
        ])->paginate(
            columns: $column,
            perPage: LimitType::PAGINATE->value
        );
    }

    public function create(FoodData $data): Food
    {
        $food = Food::query()->create($data->toArray());

        $this->createImages($data->images, $food);

        return $food;
    }

    public function update(Food $food, FoodData $data): bool
    {
        if ($data->images->count() > 0 && $data->images->count() < 4) {
            // $food->images()->forceDelete();

            $this->createImages($data->images, $food);
        }


        return $food->update($data->toArray());
    }

    public function delete(Food $food): bool
    {
        $food->update(['name' => $food->name . '_' . now()->timestamp]);

        return $food->delete();
    }

    public function getReviews(int $foodId)
    {
        return Review::query()
            ->with('user')
            ->where('reviewable_type', Food::class)
            ->where('reviewable_id', $foodId)
            ->paginate(LimitType::PAGINATE->value);
    }

    public function getRatings(int $id)
    {
        return Rating::query()
            ->with('user')
            ->where('rateable_type', Food::class)
            ->where('rateable_id', $id)
            ->paginate(LimitType::PAGINATE->value)
            ->through(function ($rating) {
                $rating->rating = $rating->rate;
                return $rating;
            });
    }

    public function getPopularFoods(): Collection
    {
        return Food::query()->without('images')->where('is_popular', true)->get(['name']);
    }

    public function togglePopular(Food $food): bool
    {
        return $food->update(['is_popular' => !$food->is_popular]);
    }

    public function existsRestaurantOrShop(array $foodIds): bool
    {
        return Food::query()->whereHasRestaurantOrShop($foodIds)->exists();
    }

    private function createReviews(RatingData $data): void
    {
        $review = $data->model->reviews()->create([
            'user_id' => auth()->id(),
            'text' => $data->review,
        ]);

        if ($data->images->count() > 0) {
            $images = $data->images->map(function ($image) {
                return ['path' => $this->spaceStorage->upload(\config('filesystems.folders.reviews'), $image)];
            });

            $review->images()->createMany($images);
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

    private function getBaseQuery(int $cityId, string $name = '', bool $isRecommended = false): FoodBuilder
    {
        return Food::query()
            ->when($isRecommended, function (FoodBuilder $q) {
                $q->whereRecommended();
            })
            ->with(['restaurants', 'shops'])
            ->searchByName($name)
            ->findBest()
            ->whereCityId($cityId);
    }

    private function getBestFoodsSliderQuery(int $cityId): FoodBuilder
    {
        return $this->getBaseQuery($cityId, isRecommended: false)
            ->select([
                DB::raw("food.id, ANY_VALUE(food.name) as name, ANY_VALUE(food.ingredients) as ingredients, ANY_VALUE(food.calories) as calories, ANY_VALUE(food.description) as description , ANY_VALUE(food.food_type_id) as see_all_id"),
                DB::raw('ANY_VALUE(ra.average_rate) as average_rate')
            ])
            ->withCount('reviews');
    }

    private function getBestFoodsQuery(int $cityId, string $name = ''): FoodBuilder
    {
        $cuisineId = $this->foodCategoryService->findCuisine()?->id;
        $isRecommended = $this->settingService->isRecommended();

        return $this->getBaseQuery($cityId, $name, $isRecommended)
            ->whereNotCategoryId($cuisineId)
            ->select([
                DB::raw("food.id, ANY_VALUE(food.name) as name, ANY_VALUE(food.ingredients) as ingredients, ANY_VALUE(food.calories) as calories, ANY_VALUE(food.description) as description , ANY_VALUE(food.food_type_id) as see_all_id"),
                DB::raw('ANY_VALUE(ra.average_rate) as average_rate')
            ])
            ->withCount('reviews');
    }

    private function getBestCuisinesSliderQuery(int $cityId): FoodBuilder
    {
        $cuisineId = $this->foodCategoryService->findCuisine()?->id;

        return $this->getBaseQuery($cityId, isRecommended: false)
            ->whereCategoryId($cuisineId)
            ->withType()
            ->select([
                DB::raw("food.id, ANY_VALUE(food.name) as name, ANY_VALUE(food.ingredients) as ingredients, ANY_VALUE(food.calories) as calories, ANY_VALUE(food.description) as description, ANY_VALUE(food.food_type_id) as see_all_id"),
                DB::raw('ANY_VALUE(ft.name) as food_type'),
                DB::raw('ANY_VALUE(ra.average_rate) as average_rate')
            ])
            ->withCount('reviews');
    }

    private function getBestCuisinesQuery(int $cityId, string $name = ''): FoodBuilder
    {
        $cuisineId = $this->foodCategoryService->findCuisine()?->id;
        $isRecommended = $this->settingService->isRecommended();

        return $this->getBaseQuery($cityId, $name, $isRecommended)
            ->whereCategoryId($cuisineId)
            ->withType()
            ->select([
                DB::raw("food.id, ANY_VALUE(food.name) as name, ANY_VALUE(food.ingredients) as ingredients, ANY_VALUE(food.calories) as calories, ANY_VALUE(food.description) as description, ANY_VALUE(food.food_type_id) as see_all_id"),
                DB::raw('ANY_VALUE(ft.name) as food_type'),
                DB::raw('ANY_VALUE(ra.average_rate) as average_rate')
            ])
            ->withCount('reviews');
    }

    private function mergeReviewAndRating(Food $food): void
    {
        $food->reviews->each(function ($review) use ($food) {
            $review->rating = $this->ratingService->averagePerUser($food->ratings, $review->user_id);
        });
    }

    private function createImages(Collection $images, Food $food): void
    {
        if ($images->count() > 0) {
            $paths = $images->map(function ($image) {
                return [
                    'path' => $this->spaceStorage->upload(
                        \config('filesystems.folders.foods'),
                        $image
                    )
                ];
            });

            $food->images()->createMany($paths);
        }
    }
}
