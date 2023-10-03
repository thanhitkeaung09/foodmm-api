<?php

namespace App\Http\Controllers\Admin;

use App\Dto\AdminLogData;
use App\Dto\FoodCategoryData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpsertFoodCategoryRequest;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\FoodCategory;
use App\Services\AdminLogService;
use App\Services\FoodCategoryService;
use Exception;
use Illuminate\Http\Response;

class FoodCategoryController extends Controller
{
    public function __construct(
        private readonly FoodCategoryService $service,
        private readonly AdminLogService $adminLogService,
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

    public function cuisine(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->findCuisine(),
        );
    }

    public function show(FoodCategory $foodCategory): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $foodCategory,
        );
    }

    public function store(UpsertFoodCategoryRequest $request): ApiSuccessResponse
    {
        $this->adminLogService->add(
            AdminLogData::fromRequest('create_food_categories', $request->all())
        );

        return new ApiSuccessResponse(
            data: $this->service->create(
                FoodCategoryData::fromRequest($request->validated())
            ),
        );
    }

    public function update(
        FoodCategory $foodCategory,
        UpsertFoodCategoryRequest $request,
    ): ApiSuccessResponse {
        $this->adminLogService->add(
            AdminLogData::fromRequest('update_food_categories', $request->all())
        );

        return new ApiSuccessResponse(
            data: $this->service->update(
                foodCategory: $foodCategory,
                data: FoodCategoryData::fromRequest($request->validated())
            ),
        );
    }

    public function destroy(FoodCategory $foodCategory): ApiSuccessResponse|ApiErrorResponse
    {
        $this->adminLogService->add(
            AdminLogData::fromRequest(
                'delete_food_categories',
                $foodCategory->toArray()
            )
        );

        try {
            return new ApiSuccessResponse(
                data: $this->service->delete($foodCategory),
            );
        } catch (Exception $e) {
            return new ApiErrorResponse(
                message: $e->getMessage(),
                status: Response::HTTP_BAD_REQUEST,
            );
        }
    }

    public function recommend(FoodCategory $foodCategory)
    {
        return new ApiSuccessResponse(
            data: $this->service->recommend(
                $foodCategory
            ),
        );
    }

    public function unrecommend(FoodCategory $foodCategory)
    {
        return new ApiSuccessResponse(
            data: $this->service->unrecommend(
                $foodCategory
            ),
        );
    }
}
