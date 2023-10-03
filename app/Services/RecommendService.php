<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\LimitType;

class RecommendService
{
    public function __construct(
        private readonly RatingService $ratingService,
        private readonly FoodService $foodService,
        private readonly RestaurantService $restaurantService,
        private readonly ShopService $shopService,
    ) {
    }

    public function getAll(int $cityId)
    {
        return [
            'bestFoods' => $this->foodService->findBest(
                $cityId,
                limitType: LimitType::LIMIT
            ),
            'bestRestaurants' => $this->restaurantService->findBest(
                $cityId,
                limitType: LimitType::LIMIT
            ),
            'bestShops' => $this->shopService->findBest(
                $cityId,
                limitType: LimitType::LIMIT
            ),
            'bestCuisines' => $this->foodService->findBestCuisines(
                $cityId,
                limitType: LimitType::LIMIT
            ),
        ];
    }
}
