<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use App\Services\FoodTypeService;
use App\Services\RestaurantService;
use Illuminate\Http\Request;

class FoodTypeController extends Controller
{
    public function __construct(
        private readonly FoodTypeService $service,
        private readonly RestaurantService $restaurantService,
    ) {
    }

    public function show(string $slug): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->restaurantService->getAllByFoodType($slug, request('city_id'), request('query')),
        );
    }
}
