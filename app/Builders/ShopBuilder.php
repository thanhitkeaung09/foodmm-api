<?php

declare(strict_types=1);

namespace App\Builders;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class ShopBuilder extends Builder
{
    public function findBest()
    {
        $this->leftJoin('ratings as ra', function (JoinClause $q) {
            $q->on('ra.rateable_id', '=', 'shops.id');
            $q->where('ra.rateable_type', Shop::class);
        })
            ->groupBy('id')
            ->orderByDesc('average_rate');

        return $this;
    }

    public function whereCityId($cityId)
    {
        $this->where(function (Builder $q) use ($cityId) {
            $q->whereHas(
                'location.township',
                fn (Builder $q) => $q->where('city_id', $cityId)
            );
        });

        return $this;
    }

    public function searchByName(string $name)
    {
        $this->where(DB::raw('lower(shops.name)'), 'like', '%' . strtolower($name) . '%');

        return $this;
    }

    public function whereCategoryId(?int $categoryId)
    {
        $this->where('category_id', $categoryId);

        return $this;
    }

    public function whereRecommendedFoods()
    {
        $this->whereHas('items.type.category', function (Builder $q) {
            $q->where('is_recommended', true);
        });

        return $this;
    }
}
