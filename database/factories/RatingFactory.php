<?php

namespace Database\Factories;

use App\Models\Food;
use App\Models\RatingType;
use App\Models\Restaurant;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rating>
 */
class RatingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'rate' => fake()->numberBetween(0, 5),
            'rating_type_id' => RatingType::factory(),
            'rateable_type' => Food::class,
            'rateable_id' => Food::factory(),
            'user_id' => User::factory(),
        ];
    }

    public function toFood()
    {
        return $this->state(fn (array $attributes) => [
            'rateable_type' => Food::class,
            'rateable_id' => Food::factory(),
        ]);
    }

    public function toRestaurant()
    {
        return $this->state(fn (array $attributes) => [
            'rateable_type' => Restaurant::class,
            'rateable_id' => Restaurant::factory(),
        ]);
    }

    public function toShop()
    {
        return $this->state(fn (array $attributes) => [
            'rateable_type' => Shop::class,
            'rateable_id' => Shop::factory(),
        ]);
    }
}
