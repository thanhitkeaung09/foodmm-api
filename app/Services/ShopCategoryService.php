<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\ShopCategoryData;
use App\Enums\LimitType;
use App\Models\Shop;
use App\Models\ShopCategory;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ShopCategoryService
{
    public function getAll($column = ['*']): Collection
    {
        return ShopCategory::query()->get($column);
    }

    public function getShops(
        string $slug,
        int $cityId,
        ?string $query,
        LimitType $limitType = LimitType::PAGINATE,
    ): LengthAwarePaginator|Collection {
        $name = $query ?? '';

        $builder = Shop::query()
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
        return ShopCategory::query()->paginate(
            columns: $columns,
            perPage: LimitType::PAGINATE->value,
        );
    }

    public function create(ShopCategoryData $data): ShopCategory
    {
        return ShopCategory::query()->create($data->toArray());
    }

    public function update(ShopCategory $shopCategory,  ShopCategoryData $data): bool
    {
        return $shopCategory->update($data->toArray());
    }

    public function delete(ShopCategory $shopCategory): bool
    {
        if ($shopCategory->shops()->count() > 0) {
            throw new Exception("You can't delete it because it has shops!");
        }

        $shopCategory->update([
            'name' => $shopCategory->name . '_' . now()->timestamp,
            'slug' => $shopCategory->slug . '_' . now()->timestamp,
        ]);

        return $shopCategory->delete();
    }
}
