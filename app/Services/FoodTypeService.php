<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\FoodTypeData;
use App\Enums\LimitType;
use App\Models\FoodType;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class FoodTypeService
{
    public function __construct(
        private readonly FoodCategoryService $foodCategoryService,
    ) {
    }

    public function getCuisineTypes(): Collection
    {
        return $this->foodCategoryService->findCuisine('types')?->types ?? collect();
    }

    public function cuisineTypes(): LengthAwarePaginator
    {
        $cuisineId = $this->foodCategoryService->findCuisine()?->id;

        return FoodType::query()->where('food_category_id', $cuisineId)->paginate(
            perPage: LimitType::PAGINATE->value,
        );
    }

    public function getAll($column = ['*']): Collection
    {
        return FoodType::query()->get($column);
    }

    public function getPaginate($column = ['*']): LengthAwarePaginator
    {
        return FoodType::query()->with('category:id,name')->paginate(
            columns: $column,
            perPage: LimitType::PAGINATE->value,
        );
    }

    public function create(FoodTypeData $data): FoodType
    {
        return FoodType::query()->create($data->toArray());
    }

    public function update(FoodType $foodType, FoodTypeData $data): bool
    {
        return $foodType->update($data->toArray());
    }

    public function delete(FoodType $foodType): bool
    {
        if ($foodType->foods()->count() > 0) {
            throw new Exception("You can't delete it because it has foods!");
        }

        $foodType->update(['name' => $foodType->name . '_' . now()->timestamp]);

        return $foodType->delete();
    }
}
