<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateFoodCategoryRequest;
use App\Http\Responses\ApiSuccessResponse;
use App\Services\FoodCategoryService;
use Illuminate\Http\Request;

class FoodCategoryController extends Controller
{
    public function __construct(
        private readonly FoodCategoryService $service,
    ) {
    }

    public function index(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getAllWithSelected(),
        );
    }

    public function show(string $slug): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getFoods($slug, request('query')),
        );
    }

    public function update(UpdateFoodCategoryRequest $request): ApiSuccessResponse
    {
        $data = $this->service->updateUserPreferr($request->validated('preferr_ids'));

        return new ApiSuccessResponse(
            data: $data,
        );
    }

    public function onboard(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getAll(['id', 'name']),
        );
    }
}
