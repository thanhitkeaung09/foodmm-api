<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AppVersionController;
use App\Http\Controllers\Api\AdminApiController;
use App\Http\Controllers\Api\AnnoucementApiController;
use App\Http\Controllers\Api\AppRatingController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BestController;
use App\Http\Controllers\Api\BlogApiController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CollectionApiController;
use App\Http\Controllers\Api\CountApiController;
use App\Http\Controllers\Api\DiscountApiController;
use App\Http\Controllers\Api\FallbackRouteController;
use App\Http\Controllers\Api\FlashScreenApiController;
use App\Http\Controllers\Api\FoodCategoryController;
use App\Http\Controllers\Api\FoodController;
use App\Http\Controllers\Api\FoodRatingController;
use App\Http\Controllers\Api\FoodTypeController;
use App\Http\Controllers\Api\HelpCenterController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\LatestPromotionApiController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\PromotionApiController;
use App\Http\Controllers\Api\RecommendController;
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Controllers\Api\RestaurantRatingController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\ShopRatingController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\BlogImageApiController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'check.application.key'])->group(function () {
    // Logout
    Route::delete('/auth/logout', [AuthController::class, 'logout'])
        ->name('logout');

    // Profile
    Route::get('/users/{user}', [UserController::class, 'show'])
        ->name('users:profile');

    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->name('users:destroy');

    Route::get('/users/{user}/reviews', [UserController::class, 'getReviews'])
        ->name('users:reviews');

    Route::patch('/users/{user}/name', [UserController::class, 'updateName'])
        ->name('users:update:name');

    Route::post('/users/{user}/image', [UserController::class, 'updateImage'])
        ->name('users:update:image');

    Route::patch('/users/{user}/language', [UserController::class, 'updateLanguage'])
        ->name('users:update:language');

    // App Rating
    Route::patch('/app/rating', AppRatingController::class)
        ->name('app:rating');

    // Raings & Reviews
    Route::post('/foods/{food}/ratings', FoodRatingController::class);
    Route::post('/restaurants/{restaurant}/ratings', RestaurantRatingController::class);
    Route::post('/shops/{shop}/ratings', ShopRatingController::class);

    // Plan & History
    Route::apiResource('plans', PlanController::class);

    Route::get('/plans-history', [PlanController::class, 'history'])
        ->name('plans:history');

    Route::get('/plans-today', [PlanController::class, 'today'])
        ->name('plans:today');

    //Collection
    Route::apiResource('collection', CollectionApiController::class);

    //Collection Search by User_id
    Route::get('collection-by-userId/{collection}', [CollectionApiController::class, 'collectionByUser'])->name('collection-by-userId.collectionByUser');

    //Collection Multiple Delete
    Route::post('multiple-delete', [CollectionApiController::class, 'collectionMultipleDelete'])->name('multiple-delete.collectionMultipleDelete');

    //Admin
    Route::get('admin', [AdminApiController::class, 'index'])->name('admin.index');

    Route::post('admin', [AdminApiController::class, 'store'])->name('admin.store');

    // Food categories + User Preferrs selected
    Route::get('/food-categories', [FoodCategoryController::class, 'index'])
        ->name('food:categories:index');

    Route::patch('/food-categories', [FoodCategoryController::class, 'update'])
        ->name('food:categories:update');
});

Route::middleware('check.application.key')->group(function () {
    // Social Login
    Route::post('/auth/{type}/login', [AuthController::class, 'login'])
        ->name('login');

    Route::post('/auth/register', [AuthController::class, 'register'])
        ->name('register');

    Route::post('/auth/login', [AuthController::class, 'loginWithEmail'])
        ->name('login.email');

    // Update Device Token
    Route::patch('/users/{user}/device-token', [UserController::class, 'updateDeviceToken']);

    // Help Center
    Route::get('/help-center', HelpCenterController::class)
        ->name('help-center');

    // Details
    Route::get('/foods/{food}', [FoodController::class, 'show'])
        ->name('foods:show');

    Route::get('/restaurants/{restaurant}', [RestaurantController::class, 'show'])
        ->name('restaurants:show');

    Route::get('/shops/{shop}', [ShopController::class, 'show'])
        ->name('shops:show');

    // Recommends
    Route::get('/recommends', [RecommendController::class, 'index'])
        ->name('recommends');

    // Restaurants + Shops
    Route::get('/restaurants-and-shops', [RestaurantController::class, 'index'])
        ->name('restaurants:shops');

    // Menus
    Route::get('/restaurants/{restaurant}/menus', [RestaurantController::class, 'getMenus'])
        ->name('restaurants:menus');

    Route::get('/shops/{shop}/menus', [ShopController::class, 'getMenus'])
        ->name('shops:menus');

    // Best List All
    Route::get('/best/{type}', BestController::class);

    Route::get('/best-foods', [FoodController::class, 'getBest'])
        ->name('foods:best');

    Route::get('/foods-group-by-category', [FoodController::class, 'getGroupBy']);

    Route::get('/cuisines-group-by-type', [RestaurantController::class, 'getByCuisineType']);

    Route::get('/restaurants-group-by-category', [RestaurantController::class, 'getGroupBy']);

    Route::get('/shops-group-by-category', [ShopController::class, 'getGroupBy']);

    Route::get('/foods/categories/{slug}', [FoodController::class, 'getAllByCategory']);

    Route::get('/restaurants/categories/{slug}', [RestaurantController::class, 'getAllByCategory']);

    Route::get('/shops/categories/{slug}', [ShopController::class, 'getAllByCategory']);

    Route::get('/cuisines/categories/{slug}', [FoodTypeController::class, 'show']);

    Route::get('/best-restaurants', [RestaurantController::class, 'getBest'])
        ->name('restaurants:best');

    Route::get('/best-shops', [ShopController::class, 'getBest'])
        ->name('shops:best');

    Route::get('/best-cuisines', [FoodController::class, 'getBestCuisines'])
        ->name('cuisines:best');

    //FlashScreen
    Route::get('flashscreen', [FlashScreenApiController::class, 'index'])->name('flashscreen.index');

    //Blog
    Route::get('blog', [BlogApiController::class, 'index'])->name("blog.index");

    Route::get('blog/{blog}', [BlogApiController::class, 'show'])->name('blog.show');

    //Blog Image
    Route::get('blog-image', [BlogImageApiController::class, 'index'])->name('blog-image.index');

    //Promotion
    Route::get('promotion', [PromotionApiController::class, 'index'])->name('promotion.index');

    Route::get('promotion/{promotion}', [PromotionApiController::class, 'show'])->name('promotion.show');

    //Annoucement
    Route::get("annoucement", [AnnoucementApiController::class, 'index'])->name('annoucement.index');

    Route::get('annoucement/{annoucement}', [AnnoucementApiController::class, 'show'])->name('annoucement.show');

    //Discount
    Route::get('discount-items', [DiscountApiController::class, 'getAllDiscountItems'])->name('discount-item.getAllDiscountItems');

    Route::get('discount-items/{discount}', [DiscountApiController::class, 'getSingleDiscountItem'])->name('discount-item.getSingleDiscountItems');

    //Latest Promotion
    Route::get('latest-promotion', [LatestPromotionApiController::class, 'getLatestPromotion'])->name('latest-promotion.getLatestPromotion');

    //Count List By City
    Route::get('count', [CountApiController::class, 'getAllCount'])->name('count.getAllCount');

    Route::get('count/{count}', [CountApiController::class, 'getSingleCount'])->name('count.getSingleCount');

    Route::post('/city-name', [CityController::class, 'getCity']);
    Route::get('/cities', [CityController::class, 'index']);

    Route::get('/onboarding-food-categories', [FoodCategoryController::class, 'onboard'])
        ->name('food:categories:onboard');

    Route::get('/popular-foods', [FoodController::class, 'popularFoods']);

    Route::post('/notifications', NotificationController::class);

    Route::get('/app-version', [AppVersionController::class, 'first']);

    Route::get('/manual-login', [AuthController::class, 'manualLogin']);

    Route::get('/facebook-login', [AuthController::class, 'facebookLogin']);
});

// Show Image
Route::get('/images/{path}', [ImageController::class, 'show'])
    ->name('images:show')
    ->where('path', '.+');

Route::fallback(FallbackRouteController::class);
