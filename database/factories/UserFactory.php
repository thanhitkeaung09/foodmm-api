<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'social_id' => fake()->unique()->uuid(),
            'social_type' => fake()->randomElement(['google', 'facebook']),
            'profile_image' => fake()->image(),
        ];
    }

    public function google()
    {
        return $this->state(fn (array $attributes) => [
            'social_type' => 'google',
        ]);
    }

    public function facebook()
    {
        return $this->state(fn (array $attributes) => [
            'social_type' => 'facebook',
        ]);
    }
}
