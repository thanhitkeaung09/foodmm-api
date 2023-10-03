<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use App\Services\FoodService;
use App\Services\RecommendService;
use App\Services\ReviewService;
use App\Services\UserService;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    public function __construct(
        private readonly ReviewService $reviewService,
        private readonly UserService $userService,
        private readonly FoodService $foodService,
    ) {
    }

    public function __invoke(): ApiSuccessResponse
    {
        //

        return new ApiSuccessResponse(
            data: [
                'reviews' => $this->reviewService->all(),
                'users' => $this->userService->getAllCount(),
                'best_foods' => $this->foodService->getBestFoods(),
            ],
        );
    }
}
