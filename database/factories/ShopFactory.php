<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\ShopCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shop>
 */
class ShopFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->unique()->text(10),
            'description' => fake()->text(50),
            'category_id' => ShopCategory::factory(),
            'location_id' => Location::factory(),
            'phones' => fake()->e164PhoneNumber(),
            'opening_hours' => ['Mon ~ Fri - 9:00AM to 5:00PM'],
        ];
    }
}
