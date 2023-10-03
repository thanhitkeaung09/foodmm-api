<?php

namespace App\Http\Controllers\Admin;

use App\Dto\AdminLogData;
use App\Dto\ShopCategoryData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpsertShopCategoryRequest;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\ShopCategory;
use App\Services\AdminLogService;
use App\Services\ShopCategoryService;
use Exception;
use Illuminate\Http\Response;

class ShopCategoryController extends Controller
{
    public function __construct(
        private readonly ShopCategoryService $service,
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

    public function show(ShopCategory $shopCategory): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $shopCategory,
        );
    }

    public function store(UpsertShopCategoryRequest $request): ApiSuccessResponse
    {
        $this->adminLogService->add(
            AdminLogData::fromRequest(
                'create_shop_categories',
                $request->all(),
            )
        );

        return new ApiSuccessResponse(
            data: $this->service->create(
                ShopCategoryData::fromRequest($request->validated())
            ),
        );
    }

    public function update(
        ShopCategory $shopCategory,
        UpsertShopCategoryRequest $request
    ): ApiSuccessResponse {
        $this->adminLogService->add(
            AdminLogData::fromRequest(
                'update_shop_categories',
                $request->all(),
            )
        );

        return new ApiSuccessResponse(
            data: $this->service->update(
                shopCategory: $shopCategory,
                data: ShopCategoryData::fromRequest($request->validated())
            )
        );
    }

    public function destroy(ShopCategory $shopCategory): ApiSuccessResponse| ApiErrorResponse
    {
        $this->adminLogService->add(
            AdminLogData::fromRequest(
                'delete_shop_categories',
                $shopCategory->toArray()
            )
        );

        try {
            return new ApiSuccessResponse(
                data: $this->service->delete($shopCategory)
            );
        } catch (Exception $e) {
            return new ApiErrorResponse(
                message: $e->getMessage(),
                status: Response::HTTP_BAD_REQUEST,
            );
        }
    }
}
