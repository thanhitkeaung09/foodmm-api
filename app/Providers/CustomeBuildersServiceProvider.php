<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CustomeBuildersServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(\App\Builders\ShopBuilder::class, function () {
            return \App\Models\Shop::query();
        });

        $this->app->bind(\App\Builders\RestaurantBuilder::class, function () {
            return \App\Models\Restaurant::query();
        });

        $this->app->bind(\App\Builders\FoodBuilder::class, function () {
            return \App\Models\Food::query();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
