<?php

namespace App\Http\Controllers\Admin;

use App\Dto\AdminLogData;
use App\Dto\ShopData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateMenusRequest;
use App\Http\Requests\Admin\UpsertShopRequest;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\Shop;
use App\Services\AdminLogService;
use App\Services\ShopService;
use Exception;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function __construct(
        private readonly ShopService $service,
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

    public function show(Shop $shop): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $shop->load(['location', 'category:id,name', 'items']),
        );
    }

    public function store(UpsertShopRequest $request): ApiSuccessResponse
    {
        $this->adminLogService->add(
            AdminLogData::fromRequest('create_shop', $request->all())
        );

        return new ApiSuccessResponse(
            data: $this->service->create(
                ShopData::fromRequest($request->validated())
            ),
        );
    }

    public function update(
        Shop $shop,
        UpsertShopRequest $request,
    ): ApiSuccessResponse {
        $this->adminLogService->add(
            AdminLogData::fromRequest('update_shop', $request->all())
        );

        return new ApiSuccessResponse(
            data: $this->service->update(
                shop: $shop,
                data: ShopData::fromRequest($request->validated())
            ),
        );
    }

    public function destroy(Shop $shop): ApiSuccessResponse
    {
        $this->adminLogService->add(
            AdminLogData::fromRequest(
                'delete_shop',
                $shop->toArray()
            )
        );

        return new ApiSuccessResponse(
            data: $this->service->delete($shop),
        );
    }

    public function createItems(Shop $shop, CreateMenusRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        $this->adminLogService->add(
            AdminLogData::fromRequest(
                'add_items_to_shop',
                $request->all(),
            )
        );

        try {
            return new ApiSuccessResponse(
                data: $this->service->createItems(
                    shop: $shop,
                    foodIds: $request->validated('food_ids')
                )
            );
        } catch (Exception $e) {
            return new ApiErrorResponse(
                message: $e->getMessage(),
            );
        }
    }

    public function removeItem(Shop $shop, Request $request): ApiSuccessResponse
    {
        $this->adminLogService->add(
            AdminLogData::fromRequest(
                'remove_item_from_shop',
                $request->all(),
            )
        );

        return new ApiSuccessResponse(
            data: $this->service->removeItem(
                shop: $shop,
                foodId: $request->food_id
            )
        );
    }

    public function reviews(int $id): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getReviews($id),
        );
    }

    public function ratings(int $id): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getRatings($id),
        );
    }
}
