<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\Restaurant;
use App\Services\RestaurantService;

class RestaurantController extends Controller
{
    public function __construct(
        private readonly RestaurantService $service,
    ) {
    }

    public function index(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getAllNames(request('city_id'), request('query')),
        );
    }

    public function show(int $id): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->findById($id)
        );
    }

    public function getBest(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->findBest(request('city_id'), request('query'))
        );
    }

    public function getGroupBy(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getGroupBy(request('city_id')),
        );
    }

    public function getByCuisineType(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getGroupByCuisineType(request('city_id')),
        );
    }

    public function getAllByCategory(string $slug): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getAllByCategory($slug, request('city_id'), request('query')),
        );
    }

    public function getMenus(Restaurant $restaurant): ApiSuccessResponse
    {
        $special = request('special') === null ? null : request('special') === 'true';

        return new ApiSuccessResponse(
            data: $this->service->getMenus($restaurant, request('query'), $special),
        );
    }
}
