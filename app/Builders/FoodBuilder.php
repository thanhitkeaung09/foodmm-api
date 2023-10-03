<?php

declare(strict_types=1);

namespace App\Builders;

use App\Models\Food;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class FoodBuilder extends Builder
{
    public function findBest()
    {
        $this->leftJoin('ratings as ra', function (JoinClause $q) {
            $q->on('ra.rateable_id', '=', 'food.id');
            $q->where('ra.rateable_type', Food::class);
        })
            ->groupBy('food.id')
            ->orderByDesc('average_rate');

        return $this;
    }

    public function withType()
    {
        $this->leftJoin('food_types as ft', 'food.food_type_id', 'ft.id');

        return $this;
    }

    public function whereCityId($cityId)
    {
        $this->where(function (Builder $q) use ($cityId) {
            $q->whereHas(
                'restaurants.location.township',
                fn (Builder $q) => $q->where('city_id', $cityId)
            )
                ->orWhereHas(
                    'shops.location.township',
                    fn (Builder $q) => $q->where('city_id', $cityId)
                );
        });

        return $this;
    }

    public function searchByName(string $name)
    {
        $this->where(DB::raw('lower(food.name)'), 'like', '%' . strtolower($name) . '%');

        return $this;
    }

    public function whereCategoryId(?int $categoryId)
    {
        $this->whereHas('type.category', function ($q) use ($categoryId) {
            $q->where('id', $categoryId);
        });

        return $this;
    }

    public function whereTypeId(?int $typeId)
    {
        $this->where('food_type_id', $typeId);

        return $this;
    }

    public function whereNotCategoryId(?int $categoryId)
    {
        $this->whereHas('type.category', function (Builder $q) use ($categoryId) {
            $q->where('id', '!=', $categoryId);
        });

        return $this;
    }

    public function whereRecommended()
    {
        $this->whereHas('type.category', function (Builder $q) {
            $q->where('is_recommended', true);
        });

        return $this;
    }

    public function whereHasRestaurantOrShop(array $foodIds)
    {
        $this->whereIn('id', $foodIds)
            ->where(function ($q) {
                $q->has('restaurants')
                    ->orHas('shops');
            });

        return $this;
    }
}
