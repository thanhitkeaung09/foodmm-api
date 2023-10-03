<?php

namespace App\Http\Controllers\Admin;

use App\Dto\PlanData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpsertPlanRequest;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\Plan;
use App\Models\User;
use App\Services\PlanService;

class PlanController extends Controller
{
    public function __construct(
        private readonly PlanService $service,
    ) {
    }

    /**
     * Get Upcomming Plans
     */
    public function index(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getAll(),
        );
    }

    public function show(Plan $plan): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $plan->load(['user', 'restaurant', 'shop', 'foods', 'collection']),
        );
    }

    public function update(Plan $plan, UpsertPlanRequest $request): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->update(
                $plan,
                PlanData::fromRequest($request->validated())
            ),
        );
    }

    public function destroy(Plan $plan): ApiSuccessResponse
    {
        $this->service->delete($plan);

        return new ApiSuccessResponse(
            data: null,
        );
    }

    public function getUserPlans(User $user): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getAll(userId: $user->id),
        );
    }

    public function history(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getHistory(),
        );
    }

    public function today(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getToday(),
        );
    }
}
