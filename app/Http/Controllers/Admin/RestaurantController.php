<?php

namespace App\Http\Controllers\Admin;

use App\Dto\AdminLogData;
use App\Dto\RestaurantData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateMenusRequest;
use App\Http\Requests\Admin\UpsertRestaurantRequest;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\Restaurant;
use App\Services\AdminLogService;
use App\Services\RestaurantService;
use Exception;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function __construct(
        private readonly RestaurantService $service,
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

    public function show(Restaurant $restaurant): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $restaurant->load(['location', 'category:id,name', 'menus']),
        );
    }

    public function store(UpsertRestaurantRequest $request): ApiSuccessResponse
    {
        $this->adminLogService->add(
            AdminLogData::fromRequest('create_restaurant', $request->all())
        );

        return new ApiSuccessResponse(
            data: $this->service->create(
                RestaurantData::fromRequest($request->validated())
            ),
        );
    }

    public function update(
        Restaurant $restaurant,
        UpsertRestaurantRequest $request,
    ): ApiSuccessResponse {
        $this->adminLogService->add(
            AdminLogData::fromRequest('update_restaurant', $request->all())
        );

        return new ApiSuccessResponse(
            data: $this->service->update(
                restaurant: $restaurant,
                data: RestaurantData::fromRequest($request->validated())
            ),
        );
    }

    public function destroy(Restaurant $restaurant): ApiSuccessResponse
    {
        $this->adminLogService->add(
            AdminLogData::fromRequest(
                'delete_restaurant',
                $restaurant->toArray()
            )
        );

        return new ApiSuccessResponse(
            data: $this->service->delete($restaurant),
        );
    }

    public function createMenus(Restaurant $restaurant, CreateMenusRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        $this->adminLogService->add(
            AdminLogData::fromRequest(
                'add_menus_to_restaurant',
                $request->all()
            )
        );

        try {
            return new ApiSuccessResponse(
                data: $this->service->createMenus(
                    restaurant: $restaurant,
                    foodIds: $request->validated('food_ids')
                )
            );
        } catch (Exception $e) {
            return new ApiErrorResponse(
                message: $e->getMessage(),
            );
        }
    }

    public function removeMenu(Restaurant $restaurant, Request $request): ApiSuccessResponse
    {
        $this->adminLogService->add(
            AdminLogData::fromRequest(
                'remove_menus_from_restaurant',
                $request->all(),
            )
        );

        return new ApiSuccessResponse(
            data: $this->service->removeMenu(
                restaurant: $restaurant,
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
