<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApplicationKey>
 */
class ApplicationKeyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->name,
            'app_id' => generateAppId(),
            'app_secrete' => generateAppSecrete(),
            'obsoleted' => true,
        ];
    }

    public function notObsoleted(): static
    {
        return $this->state(fn (array $attributes) => [
            'obsoleted' => false,
        ]);
    }
}
