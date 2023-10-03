<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Count>
 */
class CountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "city_id"=>City::factory(),
            "shop_count"=>fake()->numberBetween(100,200),
            "restaurant_count"=>fake()->numberBetween(100,200),
            "food_count"=>fake()->numberBetween(100,200),
            "counsine_count"=>fake()->numberBetween(100,200)
        ];
    }
}
