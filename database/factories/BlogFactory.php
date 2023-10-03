<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Blog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Auth;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\blogs>
 */
class BlogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "title" => fake()->word(3),
            "body" => fake()->word(50),
            "admin_id" => Admin::factory(),
            "status" => fake()->randomElement([true, false]),
            "blogs_reading_time" => "3 min",
            "type" => "blog"
        ];
    }
}
