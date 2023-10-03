<?php

namespace App\Http\Controllers\Admin;

use App\Dto\RestaurantCategoryData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpsertRestaurantCategoryRequest;
use App\Http\Requests\Admin\UpsertRestaurantRequest;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\RestaurantCategory;
use App\Services\RestaurantCategoryService;
use Exception;

class RestaurantCategoryController extends Controller
{
    public function __construct(
        private readonly RestaurantCategoryService $service,
    ) {
    }

    public function all(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getAll(),
        );
    }

    public function index(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getPaginate(),
        );
    }

    public function show(RestaurantCategory $restaurantCategory): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $restaurantCategory,
        );
    }

    public function store(UpsertRestaurantCategoryRequest $request): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->create(
                RestaurantCategoryData::fromRequest($request->validated())
            ),
        );
    }

    public function update(
        RestaurantCategory $restaurantCategory,
        UpsertRestaurantCategoryRequest $request
    ): ApiSuccessResponse {
        return new ApiSuccessResponse(
            data: $this->service->update(
                restaurantCategory: $restaurantCategory,
                data: RestaurantCategoryData::fromRequest($request->validated())
            )
        );
    }

    public function destroy(
        RestaurantCategory $restaurantCategory,
    ): ApiSuccessResponse|ApiErrorResponse {
        try {
            return new ApiSuccessResponse(
                data: $this->service->delete($restaurantCategory)
            );
        } catch (Exception $e) {
            return new ApiErrorResponse(
                message: $e->getMessage(),
            );
        }
    }
}
