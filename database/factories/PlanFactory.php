<?php

namespace Database\Factories;

use App\Models\Collection;
use App\Models\Restaurant;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\plan>
 */
class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'description' => fake()->text(50),
            'user_id' => User::factory(),
            'restaurant_id' => Restaurant::factory(),
            'shop_id' => null,
            'collection_id' => Collection::factory(),
            'planed_at' => now(),
            'reminded_at' => now(),
        ];
    }

    public function forShop(): static
    {
        return $this->state(fn (array $attributes) => [
            'shop_id' => Shop::factory(),
            'restaurant_id' => null,
        ]);
    }
}
