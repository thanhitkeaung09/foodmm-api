<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\FoodCategoryData;
use App\Enums\LimitType;
use App\Models\FoodCategory;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class FoodCategoryService
{
    public function __construct(
        private readonly AdminLogService $adminLogService,
    ) {
    }

    public function getAllWithSelected(): Collection
    {
        $foodCategories = FoodCategory::query()->with(['preferred.user'])->get();

        $userPreferred = auth()->user()->preferred;

        return $foodCategories->map(function ($foodCategory) use ($userPreferred) {
            $selected = $userPreferred->where('food_category_id', $foodCategory->id)->count() > 0;

            return [
                'id' => $foodCategory->id,
                'name' => $foodCategory->name,
                'selected' => $selected,
            ];
        })->sortByDesc('selected')->values();
    }

    public function updateUserPreferr(array $preferrIds)
    {
        $userPreferred = auth()->user()->preferred();

        $userPreferred->forceDelete();

        return $userPreferred->createMany(
            collect($preferrIds)->map(fn ($id) => ['food_category_id' => $id])
        );
    }

    public function findCuisine(array|string $with = []): ?FoodCategory
    {
        return FoodCategory::query()->with($with)->whereCuisine()->first('id');
    }

    public function getAll($column = ['*']): Collection
    {
        $cuisineId = $this->findCuisine()?->id;

        return FoodCategory::query()->whereNot('id', $cuisineId)->get($column);
    }

    public function getPaginate($column = ['*']): LengthAwarePaginator
    {
        $cuisineId = $this->findCuisine()?->id;

        return FoodCategory::query()->whereNot('id', $cuisineId)->paginate(
            columns: $column,
            perPage: LimitType::PAGINATE->value,
        );
    }

    public function recommendedExists(): bool
    {
        return FoodCategory::query()->where('is_recommended', true)->exists();
    }

    public function create(FoodCategoryData $data): FoodCategory
    {
        return FoodCategory::query()->create($data->toArray());
    }

    public function update(FoodCategory $foodCategory, FoodCategoryData $data): bool
    {
        return $foodCategory->update($data->toArray());
    }

    public function delete(FoodCategory $foodCategory): bool
    {
        if ($foodCategory->foods()->count() > 0) {
            throw new Exception("You can't delete it because it has foods!");
        }

        $foodCategory->update(['name' => $foodCategory->name . '_' . now()->timestamp]);

        return $foodCategory->delete();
    }

    public function recommend(FoodCategory $foodCategory): bool
    {
        return $foodCategory->update(['is_recommended' => true]);
    }

    public function unrecommend(FoodCategory $foodCategory): bool
    {
        return $foodCategory->update(['is_recommended' => false]);
    }
}
