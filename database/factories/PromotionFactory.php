<?php

namespace Database\Factories;

use App\Models\Restaurant;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Promotion>
 */
class PromotionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "image" => fake()->imageUrl(),
            "label" => fake()->word(5, true),
            "status" => fake()->randomElement([true, false]),
            "description" => fake()->text(30),
            "period" => "Feb 12 to Feb 14",
            "promotionable_type" => Shop::class,
            "promotionable_id" => Shop::factory(),
        ];
    }

    public function toRestaurant()
    {
        return $this->state(fn (array $attributes) => [
            'promotionable_type' => Restaurant::class,
            'promotionable_id' => Restaurant::factory(),
        ]);
    }
}
