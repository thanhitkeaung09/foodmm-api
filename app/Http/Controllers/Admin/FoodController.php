<?php

namespace App\Http\Controllers\Admin;

use App\Dto\AdminLogData;
use App\Dto\FoodData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpsertFoodRequest;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\Food;
use App\Services\AdminLogService;
use App\Services\FoodService;

class FoodController extends Controller
{
    public function __construct(
        private readonly FoodService $service,
        private readonly AdminLogService $adminLogService,
    ) {
    }

    public function all(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getFreeAll(),
        );
    }

    public function cuisines(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getCuisines(),
        );
    }

    public function index(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getPaginate(),
        );
    }

    public function show(Food $food): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $food->load(['type', 'type.category']),
        );
    }

    public function store(UpsertFoodRequest $request): ApiSuccessResponse
    {
        $this->adminLogService->add(
            AdminLogData::fromRequest('create_food', $request->all())
        );

        return new ApiSuccessResponse(
            data: $this->service->create(
                FoodData::fromRequest($request->validated())
            ),
        );
    }

    public function update(
        Food $food,
        UpsertFoodRequest $request,
    ): ApiSuccessResponse {
        $this->adminLogService->add(
            AdminLogData::fromRequest('update_food', $request->all())
        );

        return new ApiSuccessResponse(
            data: $this->service->update(
                food: $food,
                data: FoodData::fromRequest($request->validated())
            ),
        );
    }

    public function destroy(Food $food): ApiSuccessResponse
    {
        $this->adminLogService->add(
            AdminLogData::fromRequest(
                'delete_food',
                $food->toArray()
            )
        );

        return new ApiSuccessResponse(
            data: $this->service->delete($food),
        );
    }

    public function reviews(int $foodId): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getReviews($foodId),
        );
    }

    public function ratings(int $id): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getRatings($id),
        );
    }

    public function popular(Food $food): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->togglePopular($food),
        );
    }
}
