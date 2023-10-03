<?php

namespace Database\Factories;

use App\Models\FoodType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Food>
 */
class FoodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->text(20),
            'food_type_id' => FoodType::factory(),
            'ingredients' => fake()->text(30),
            'vitamins' => fake()->text(10),
            'calories' => 'calories ' . fake()->numberBetween(50, 600),
            'description' => fake()->text(),
            'is_popular' => fake()->randomElement([true, false]),
        ];
    }

    public function popular()
    {
        return $this->state(fn (array $attributes) => [
            'is_popular' => true,
        ]);
    }

    public function recommended()
    {
        return $this->state(fn (array $attributes) => [
            'is_recommended' => true,
        ]);
    }
}
