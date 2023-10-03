<?php

declare(strict_types=1);

namespace App\Dto;

class CountData
{
    public function __construct(
        public readonly int $shopCount = 0,
        public readonly int $restaurantCount = 0,
        public readonly int $foodCount = 0,
        public readonly int $cuisineCount = 0,
    ) {
    }

    public function toArray(): array
    {
        return [
            'shop_count' => $this->shopCount,
            'restaurant_count' => $this->restaurantCount,
            'food_count' => $this->foodCount,
            'cuisine_count' => $this->cuisineCount,
        ];
    }
}
