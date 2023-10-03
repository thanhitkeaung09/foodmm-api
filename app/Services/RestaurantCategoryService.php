<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\RestaurantCategoryData;
use App\Enums\LimitType;
use App\Models\Restaurant;
use App\Models\RestaurantCategory;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class RestaurantCategoryService
{
    public function getAll($column = ['*']): Collection
    {
        return RestaurantCategory::query()->get($column);
    }

    public function getRestaurants(
        string $slug,
        ?string $query,
        LimitType $limitType = LimitType::PAGINATE,
    ): LengthAwarePaginator|Collection {
        $name = $query ?? '';

        $builder = Restaurant::query()
            ->searchByName($name)
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

    public function getPaginate(array $columns = ['*'])
    {
        return RestaurantCategory::query()->paginate(
            columns: $columns,
            perPage: LimitType::PAGINATE->value,
        );
    }

    public function create(RestaurantCategoryData $data): RestaurantCategory
    {
        return RestaurantCategory::query()->create($data->toArray());
    }

    public function update(RestaurantCategory $restaurantCategory,  RestaurantCategoryData $data): bool
    {
        return $restaurantCategory->update($data->toArray());
    }

    public function delete(RestaurantCategory $restaurantCategory): bool
    {
        if ($restaurantCategory->restaurants()->count() > 0) {
            throw new Exception("You can't delete it because it has restaurants!");
        }

        $restaurantCategory->update([
            'name' => $restaurantCategory->name . '_' . now()->timestamp,
            'slug' => $restaurantCategory->slug . '_' . now()->timestamp,
        ]);

        return $restaurantCategory->delete();
    }
}
