<?php

namespace Database\Factories;

use App\Models\Food;
use App\Models\Restaurant;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'text' => fake()->text(),
            'user_id' => User::factory(),
            'reviewable_type' => Food::class,
            'reviewable_id' => Food::factory(),
        ];
    }

    public function toFood()
    {
        return $this->state(fn (array $attributes) => [
            'reviewable_type' => Food::class,
            'reviewable_id' => Food::factory(),
        ]);
    }

    public function toRestaurant()
    {
        return $this->state(fn (array $attributes) => [
            'reviewable_type' => Restaurant::class,
            'reviewable_id' => Restaurant::factory(),
        ]);
    }

    public function toShop()
    {
        return $this->state(fn (array $attributes) => [
            'reviewable_type' => Shop::class,
            'reviewable_id' => Shop::factory(),
        ]);
    }
}
