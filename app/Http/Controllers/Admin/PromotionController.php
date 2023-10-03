<?php

namespace App\Http\Controllers\Admin;

use App\Dto\AdminLogData;
use App\Dto\PromotionData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpsertPromotionRequest;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\Promotion;
use App\Services\AdminLogService;
use App\Services\PromotionService;

class PromotionController extends Controller
{
    public function __construct(
        private readonly PromotionService $service,
        private readonly AdminLogService $adminLogService,
    ) {
    }

    public function index(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getPaginate(),
        );
    }

    public function show(Promotion $promotion): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $promotion->load(['promotionable', 'discountItems.foods']),
        );
    }

    public function store(UpsertPromotionRequest $request): ApiSuccessResponse
    {
        $this->adminLogService->add(
            AdminLogData::fromRequest('create_promotion', $request->all())
        );

        return new ApiSuccessResponse(
            data: $this->service->create(
                PromotionData::fromRequest($request->validated())
            ),
        );
    }

    public function update(
        Promotion $promotion,
        UpsertPromotionRequest $request,
    ): ApiSuccessResponse {
        $this->adminLogService->add(
            AdminLogData::fromRequest('update_promotion', $request->all())
        );

        return new ApiSuccessResponse(
            data: $this->service->update(
                promotion: $promotion,
                data: PromotionData::fromRequest($request->validated())
            ),
        );
    }

    public function destroy(Promotion $promotion): ApiSuccessResponse
    {
        $this->adminLogService->add(
            AdminLogData::fromRequest(
                'delete_promotion',
                $promotion->toArray()
            )
        );

        return new ApiSuccessResponse(
            data: $this->service->delete($promotion),
        );
    }
    public function removeItem(Promotion $promotion): ApiSuccessResponse
    {
        $this->adminLogService->add(
            AdminLogData::fromRequest(
                'remove_item_from_promotion',
                request()->all()
            )
        );

        return new ApiSuccessResponse(
            data: $this->service->removeItem($promotion, request('item_id')),
        );
    }
}
