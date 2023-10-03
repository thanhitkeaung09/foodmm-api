<?php

namespace App\Http\Controllers\Admin;

use App\Dto\AdminLogData;
use App\Dto\FoodTypeData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpsertFoodTypeRequest;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\FoodType;
use App\Services\AdminLogService;
use App\Services\FoodTypeService;
use Exception;
use Illuminate\Http\Response;

class FoodTypeController extends Controller
{
    public function __construct(
        private readonly FoodTypeService $service,
        private readonly AdminLogService $adminLogService,
    ) {
    }

    public function all(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getAll(),
        );
    }

    public function getCuisineTypes(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getCuisineTypes(),
        );
    }

    public function cuisineTypes(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->cuisineTypes(),
        );
    }

    public function index(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getPaginate(),
        );
    }

    public function show(FoodType $foodType): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $foodType->load('category:id,name'),
        );
    }

    public function store(UpsertFoodTypeRequest $request): ApiSuccessResponse
    {
        $this->adminLogService->add(
            AdminLogData::fromRequest('create_food_types', $request->all())
        );

        return new ApiSuccessResponse(
            data: $this->service->create(
                FoodTypeData::fromRequest($request->validated())
            ),
        );
    }

    public function update(
        FoodType $foodType,
        UpsertFoodTypeRequest $request,
    ): ApiSuccessResponse {
        $this->adminLogService->add(
            AdminLogData::fromRequest('update_food_types', $request->all())
        );

        return new ApiSuccessResponse(
            data: $this->service->update(
                foodType: $foodType,
                data: FoodTypeData::fromRequest($request->validated())
            ),
        );
    }

    public function destroy(FoodType $foodType): ApiSuccessResponse|ApiErrorResponse
    {
        $this->adminLogService->add(
            AdminLogData::fromRequest(
                'delete_food_types',
                $foodType->toArray()
            )
        );

        try {
            return new ApiSuccessResponse(
                data: $this->service->delete($foodType),
            );
        } catch (Exception $e) {
            return new ApiErrorResponse(
                message: $e->getMessage(),
                status: Response::HTTP_BAD_REQUEST,
            );
        }
    }
}
