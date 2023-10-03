<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware(['api', 'localization'])->prefix('api')->name('api:')->group(function () {
                /**
                 * V1
                 */
                Route::prefix('v1')->name('v1:')->group(base_path('routes/api/v1.php'));
            });

            Route::middleware(['api'])->prefix('api')->name('api:')->group(function () {
                /**
                 * Admin
                 */
                Route::prefix('admin')->name('admin:')->group(base_path('routes/api/admin.php'));
            });
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
