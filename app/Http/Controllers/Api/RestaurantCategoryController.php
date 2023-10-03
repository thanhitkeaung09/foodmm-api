<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use App\Services\RestaurantCategoryService;
use Illuminate\Http\Request;

class RestaurantCategoryController extends Controller
{
    public function __construct(
        private readonly RestaurantCategoryService $service,
    ) {
    }

    public function show(string $slug): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getRestaurants($slug, request('query')),
        );
    }
}
