<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use App\Services\FoodService;

class FoodController extends Controller
{
    public function __construct(
        private readonly FoodService $service,
    ) {
    }

    public function show(int $id): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->findById($id),
        );
    }

    public function getBest(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->findBest(request('city_id'), request('query')),
        );
    }

    public function getBestCuisines(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->findBestCuisines(request('city_id'), request('query')),
        );
    }

    public function getAllByCategory(string $slug): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getAllByCategory($slug, request('city_id'), request('query')),
        );
    }

    public function getGroupBy(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getGroupBy(request('city_id')),
        );
    }

    public function popularFoods(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getPopularFoods(),
        );
    }
}
