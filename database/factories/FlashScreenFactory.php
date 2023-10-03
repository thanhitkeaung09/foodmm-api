<?php

namespace Database\Factories;

use App\Models\FlashScreen;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FlashScreen>
 */
class FlashScreenFactory extends Factory
{
    protected $flashscreen = FlashScreen::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "flash_screen_image"=>fake()->image(),
            "flash_screen_status"=>fake()->randomElement([true,false])
        ];
    }
}
