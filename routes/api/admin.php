<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ApplicationKeyController;
use App\Http\Controllers\Admin\AppVersionController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CountController;
use App\Http\Controllers\Admin\FlashScreenController;
use App\Http\Controllers\Admin\FoodCategoryController;
use App\Http\Controllers\Admin\FoodController;
use App\Http\Controllers\Admin\FoodTypeController;
use App\Http\Controllers\Admin\HelpController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\RestaurantCategoryController;
use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ShopCategoryController;
use App\Http\Controllers\Admin\ShopController;
use App\Http\Controllers\Admin\StateController;
use App\Http\Controllers\Admin\TownshipController;
use App\Http\Controllers\Api\FallbackRouteController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WebsiteController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\RestaurantController as ApiRestaurantController;
use App\Http\Controllers\Api\ShopController as ApiShopController;
use Illuminate\Support\Facades\Route;

Route::get('/websites', WebsiteController::class);

Route::middleware('auth:admin')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::get('/users', [UserController::class, 'index'])->middleware('can:view-users');
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::patch('/users/{user}', [UserController::class, 'update'])->middleware('can:edit-users');

    Route::get('/admins', [AdminController::class, 'index'])->middleware('can:view-admins');
    Route::get('/admins/{admin}', [AdminController::class, 'show']);
    Route::post('/admins', [AdminController::class, 'store'])->middleware('can:create-admins');
    Route::delete('/admins/{admin}', [AdminController::class, 'destroy'])->middleware('can:create-admins');

    Route::put('/admins/{admin}/profile', [AdminController::class, 'updateProfile'])
        ->middleware('can:edit-admins-profile');

    Route::put('/admins/{admin}/password', [AdminController::class, 'updatePassword'])
        ->middleware('can:edit-admins-password');

    Route::post('/admins/{admin}/roles', [AdminController::class, 'assignRoles'])
        ->middleware('can:assign-roles');

    Route::get('/admin-permissions', [AdminController::class, 'getPermissions']);

    Route::get('/roles', [RoleController::class, 'index'])->middleware('can:view-roles');
    Route::post('/roles', [RoleController::class, 'store'])->middleware('can:create-roles');
    Route::get('/roles/{role}', [RoleController::class, 'show']);
    Route::patch('/roles/{role}', [RoleController::class, 'update'])->middleware('can:edit-roles');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->middleware('can:delete-roles');

    Route::get('/permissions', [RoleController::class, 'getPermissions']);

    Route::get('/plans', [PlanController::class, 'index'])->middleware('can:view-plans');

    Route::get('/plans-history', [PlanController::class, 'history']);
    Route::get('/plans-today', [PlanController::class, 'today']);
    Route::get('/users/{user}/plans', [PlanController::class, 'getUserPlans']);

    Route::get('/foods', [FoodController::class, 'index'])->middleware('can:view-foods');
    Route::get('/foods/{food}', [FoodController::class, 'show']);
    Route::post('/foods', [FoodController::class, 'store'])->middleware('can:create-foods');
    Route::patch('/foods/{food}', [FoodController::class, 'update'])->middleware('can:edit-foods');
    Route::delete('/foods/{food}', [FoodController::class, 'destroy'])->middleware('can:delete-foods');

    Route::get('/foods-all', [FoodController::class, 'all']);
    Route::get('/foods/{food}/popular', [FoodController::class, 'popular']);
    Route::get('/foods/{foodId}/reviews', [FoodController::class, 'reviews']);
    Route::get('/foods/{food}/ratings', [FoodController::class, 'ratings']);

    Route::get('/food-categories', [FoodCategoryController::class, 'index'])->middleware('can:view-foods-categories');
    Route::get('/food-categories/{food_category}', [FoodCategoryController::class, 'show']);
    Route::post('/food-categories', [FoodCategoryController::class, 'store'])->middleware('can:create-foods-categories');
    Route::patch('/food-categories/{food_category}', [FoodCategoryController::class, 'update'])->middleware('can:edit-foods-categories');
    Route::delete('/food-categories/{food_category}', [FoodCategoryController::class, 'destroy'])->middleware('can:delete-foods-categories');

    Route::get('/food-categories/{foodCategory}/recommend', [FoodCategoryController::class, 'recommend']);
    Route::get('/food-categories/{foodCategory}/unrecommend', [FoodCategoryController::class, 'unrecommend']);
    Route::get('/food-categories-all', [FoodCategoryController::class, 'all']);
    Route::get('/cuisine-category', [FoodCategoryController::class, 'cuisine']);

    Route::get('/food-types', [FoodTypeController::class, 'index'])->middleware('can:view-foods-types');
    Route::get('/food-types/{food_type}', [FoodTypeController::class, 'show']);
    Route::post('/food-types', [FoodTypeController::class, 'store'])->middleware('can:create-foods-types');
    Route::patch('/food-types/{food_type}', [FoodTypeController::class, 'update'])->middleware('can:edit-foods-types');
    Route::delete('/food-types/{food_type}', [FoodTypeController::class, 'destroy'])->middleware('can:delete-foods-types');

    Route::get('/food-types-all', [FoodTypeController::class, 'all']);

    Route::get('/reviews/{review}', [ReviewController::class, 'show']);
    Route::patch('/reviews/{review}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);

    Route::get('/cuisines', [FoodController::class, 'cuisines'])->middleware('can:view-cuisines');
    Route::get('/cuisine-types', [FoodTypeController::class, 'cuisineTypes'])->middleware('can:view-foods-types');
    Route::get('/cuisine-types-all', [FoodTypeController::class, 'getCuisineTypes']);

    Route::get('/states', [StateController::class, 'index'])->middleware('can:view-states');
    Route::get('/states/{state}', [StateController::class, 'show']);
    Route::post('/states', [StateController::class, 'store'])->middleware('can:create-states');
    Route::patch('/states/{state}', [StateController::class, 'update'])->middleware('can:edit-states');
    Route::delete('/states/{state}', [StateController::class, 'destroy'])->middleware('can:delete-states');

    Route::get('/states-all', [StateController::class, 'all']);
    Route::get('/states/{state}/cities', [StateController::class, 'getCities']);

    Route::get('/cities', [CityController::class, 'index'])->middleware('can:view-cities');
    Route::get('/cities/{city}', [CityController::class, 'show']);
    Route::post('/cities', [CityController::class, 'store'])->middleware('can:create-cities');
    Route::patch('/cities/{city}', [CityController::class, 'update'])->middleware('can:edit-cities');
    Route::delete('/cities/{city}', [CityController::class, 'destroy'])->middleware('can:delete-cities');

    Route::get('/cities-all', [CityController::class, 'all']);
    Route::get('/cities/{city}/townships', [CityController::class, 'getTownships']);

    Route::get('/townships', [TownshipController::class, 'index'])->middleware('can:view-townships');
    Route::get('/townships/{township}', [TownshipController::class, 'show']);
    Route::post('/townships', [TownshipController::class, 'store'])->middleware('can:create-townships');
    Route::patch('/townships/{township}', [TownshipController::class, 'update'])->middleware('can:edit-townships');
    Route::delete('/townships/{township}', [TownshipController::class, 'destroy'])->middleware('can:delete-townships');

    Route::get('/locations', LocationController::class);

    Route::get('/restaurants', [RestaurantController::class, 'index'])->middleware('can:view-restaurants');
    Route::get('/restaurants/{restaurant}', [RestaurantController::class, 'show']);
    Route::post('/restaurants', [RestaurantController::class, 'store'])->middleware('can:create-restaurants');
    Route::patch('/restaurants/{restaurant}', [RestaurantController::class, 'update'])->middleware('can:edit-restaurants');
    Route::delete('/restaurants/{restaurant}', [RestaurantController::class, 'destroy'])->middleware('can:delete-restaurants');

    Route::get('/restaurants-all', [RestaurantController::class, 'all']);
    Route::get('/restaurants/{id}/reviews', [RestaurantController::class, 'reviews']);
    Route::get('/restaurants/{restaurant}/ratings', [RestaurantController::class, 'ratings']);
    Route::post('/restaurants/{restaurant}/menus', [RestaurantController::class, 'createMenus']);
    Route::delete('/restaurants/{restaurant}/menus', [RestaurantController::class, 'removeMenu']);

    Route::get('/restaurant-categories', [RestaurantCategoryController::class, 'index'])->middleware('can:view-restaurant-categories');
    Route::get('/restaurant-categories/{restaurant_category}', [RestaurantCategoryController::class, 'show']);
    Route::post('/restaurant-categories', [RestaurantCategoryController::class, 'store'])->middleware('can:create-restaurant-categories');
    Route::patch('/restaurant-categories/{restaurant_category}', [RestaurantCategoryController::class, 'update'])->middleware('can:edit-restaurant-categories');
    Route::delete('/restaurant-categories/{restaurant_category}', [RestaurantCategoryController::class, 'destroy'])->middleware('can:delete-restaurant-categories');

    Route::get('/restaurant-categories-all', [RestaurantCategoryController::class, 'all']);

    Route::get('/shops', [ShopController::class, 'index'])->middleware('can:view-shops');
    Route::get('/shops/{shop}', [ShopController::class, 'show']);
    Route::post('/shops', [ShopController::class, 'store'])->middleware('can:create-shops');
    Route::patch('/shops/{shop}', [ShopController::class, 'update'])->middleware('can:edit-shops');
    Route::delete('/shops/{shop}', [ShopController::class, 'destroy'])->middleware('can:delete-shops');

    Route::get('/shops-all', [ShopController::class, 'all']);
    Route::get('/shops/{id}/reviews', [ShopController::class, 'reviews']);
    Route::get('/shops/{shop}/ratings', [ShopController::class, 'ratings']);
    Route::post('/shops/{shop}/items', [ShopController::class, 'createItems']);
    Route::delete('/shops/{shop}/items', [ShopController::class, 'removeItem']);

    Route::get('/shop-categories', [ShopCategoryController::class, 'index'])->middleware('can:view-shop-categories');
    Route::get('/shop-categories/{shop_category}', [ShopCategoryController::class, 'show']);
    Route::post('/shop-categories', [ShopCategoryController::class, 'store'])->middleware('can:create-shop-categories');
    Route::patch('/shop-categories/{shop_category}', [ShopCategoryController::class, 'update'])->middleware('can:edit-shop-categories');
    Route::delete('/shop-categories/{shop_category}', [ShopCategoryController::class, 'destroy'])->middleware('can:delete-shop-categories');

    Route::get('/shop-categories-all', [ShopCategoryController::class, 'all']);

    Route::get('/flash-screens', [FlashScreenController::class, 'index'])->middleware('can:view-flash-screens');
    Route::get('/flash-screens/{flash_screen}', [FlashScreenController::class, 'show']);
    Route::post('/flash-screens', [FlashScreenController::class, 'store'])->middleware('can:create-flash-screens');
    Route::patch('/flash-screens/{flash_screen}', [FlashScreenController::class, 'update'])->middleware('can:edit-flash-screens');
    Route::delete('/flash-screens/{flash_screen}', [FlashScreenController::class, 'destroy'])->middleware('can:delete-flash-screens');

    Route::get('/blogs', [BlogController::class, 'index'])->middleware('can:view-blogs');
    Route::get('/blogs/{blog}', [BlogController::class, 'show']);
    Route::post('/blogs', [BlogController::class, 'store'])->middleware('can:create-blogs');
    Route::patch('/blogs/{blog}', [BlogController::class, 'update'])->middleware('can:edit-blogs');
    Route::delete('/blogs/{blog}', [BlogController::class, 'destroy'])->middleware('can:delete-blogs');

    Route::get('/promotions', [PromotionController::class, 'index'])->middleware('can:view-promotions');
    Route::get('/promotions/{promotion}', [PromotionController::class, 'show']);
    Route::post('/promotions', [PromotionController::class, 'store'])->middleware('can:create-promotions');
    Route::patch('/promotions/{promotion}', [PromotionController::class, 'update'])->middleware('can:edit-promotions');
    Route::delete('/promotions/{promotion}', [PromotionController::class, 'destroy'])->middleware('can:delete-promotions');

    Route::delete('/promotions/{promotion}/items', [PromotionController::class, 'removeItem']);

    Route::get('/counts', CountController::class);

    Route::apiResource('settings', SettingController::class)->only(['index', 'show', 'update']);

    Route::apiResource('application-keys', ApplicationKeyController::class)->only(['index', 'show', 'store']);

    Route::get('/application-keys/{applicationKey}/used', [ApplicationKeyController::class, 'used']);
    Route::get('/application-keys/{applicationKey}/obsoleted', [ApplicationKeyController::class, 'obsoleted']);

    // Menus
    Route::get('/restaurants/{restaurant}/menus', [ApiRestaurantController::class, 'getMenus'])
        ->name('restaurants:menus');

    Route::get('/shops/{shop}/menus', [ApiShopController::class, 'getMenus'])
        ->name('shops:menus');

    Route::get('/app-versions', [AppVersionController::class, 'index']);
    Route::get('/app-versions/{appVersion}', [AppVersionController::class, 'show']);
    Route::patch('/app-versions/{appVersion}', [AppVersionController::class, 'update']);

    Route::apiResource('helps', HelpController::class);

    Route::delete('/images/{image}', [ImageController::class, 'destroy']);
});

Route::post('/auth/login', [AuthController::class, 'login']);

Route::fallback(FallbackRouteController::class);
