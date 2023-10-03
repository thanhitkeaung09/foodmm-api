<?php

namespace App\Http\Controllers\Api;

use App\Dto\PlanData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpsertPlanRequest;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\Plan;
use App\Services\PlanService;
use Exception;

class PlanController extends Controller
{
    public function __construct(
        private readonly PlanService $service,
    ) {
    }

    public function index(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getUpcomming(
                request('collection_id'),
                auth()->id()
            ),
        );
    }

    public function history(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getHistory(
                request('collection_id'),
                auth()->id()
            ),
        );
    }

    public function today(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getToday(
                request('collection_id'),
                auth()->id()
            ),
        );
    }

    public function store(UpsertPlanRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            return new ApiSuccessResponse(
                data: $this->service->create(PlanData::fromRequest($request->validated())),
                message: __('messages.create_plan_success'),
            );
        } catch (Exception $e) {
            return new ApiErrorResponse(
                message: $e->getMessage(),
            );
        }
    }

    public function show(Plan $plan): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->findPlan($plan),
        );
    }

    public function update(Plan $plan, UpsertPlanRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            return new ApiSuccessResponse(
                data: $this->service->update(
                    $plan,
                    PlanData::fromRequest($request->validated())
                ),
                message: __('messages.update_plan_success'),
            );
        } catch (Exception $e) {
            return new ApiErrorResponse(
                message: $e->getMessage(),
            );
        }
    }

    public function destroy(Plan $plan)
    {
        $this->service->delete($plan);

        return new ApiSuccessResponse(
            data: null,
            message: __('messages.delete_plan_success'),
        );
    }
}
