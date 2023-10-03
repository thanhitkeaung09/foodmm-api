<?php

namespace Database\Seeders;

use App\Models\AppRating;
use App\Models\Food;
use App\Models\FoodCategory;
use App\Models\FoodImage;
use App\Models\FoodType;
use App\Models\Restaurant;
use App\Models\RestaurantImage;
use App\Models\Shop;
use App\Models\ShopImage;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory(10)->create();

        // Users who give rate to app
        AppRating::factory(1)->create();

        Food::factory(20)->has(FoodImage::factory(3))->create();
        Restaurant::factory(20)->has(RestaurantImage::factory(2))->create();
        Shop::factory(20)->has(ShopImage::factory(4))->create();

        $restaurants = Restaurant::query()->where('id', "<", 5)->get();
        $shops = Shop::query()->where('id', "<", 5)->get();

        $restaurants->each(function ($re) {
            $re->attach(range(1, 5), ['is_special' => true]);
            $re->attach(range(6, 10), ['is_special' => false]);

            $re->reviews()->cerateMany([
                ['text' => fake()->text(20), 'user_id' => 1],
                ['text' => fake()->text(20), 'user_id' => 2],
                ['text' => fake()->text(20), 'user_id' => 3],
                ['text' => fake()->text(20), 'user_id' => 4],
                ['text' => fake()->text(20), 'user_id' => 5],
            ]);
        });

        $shops->each(function ($shop) {
            $shop->attach(range(11, 15), ['is_special' => false]);
            $shop->attach(range(16, 20), ['is_special' => true]);

            $shop->reviews()->cerateMany([
                ['text' => fake()->text(20), 'user_id' => 1],
                ['text' => fake()->text(20), 'user_id' => 2],
                ['text' => fake()->text(20), 'user_id' => 3],
                ['text' => fake()->text(20), 'user_id' => 4],
                ['text' => fake()->text(20), 'user_id' => 5],
            ]);
        });

        $fc = FoodCategory::query()->create(['name' => 'Cuisines', 'slug' => 'cuisines']);

        FoodType::query()->insertMany([
            ['name' => 'Myanmar Cuisines', 'slug' => 'myanmar-cuisines', $fc->id],
            ['name' => 'Korean Cuisines', 'slug' => 'korean-cuisines', $fc->id],
            ['name' => 'Chinese Cuisines', 'slug' => 'chinese-cuisines', $fc->id],
        ]);
    }
}
