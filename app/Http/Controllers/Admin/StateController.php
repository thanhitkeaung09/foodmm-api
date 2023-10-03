<?php

namespace App\Http\Controllers\Admin;

use App\Dto\AdminLogData;
use App\Dto\StateData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpsertStateRequest;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\State;
use App\Services\AdminLogService;
use App\Services\StateService;
use Exception;
use Illuminate\Http\Response;

class StateController extends Controller
{
    public function __construct(
        private readonly StateService $service,
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

    public function getCities(State $state): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $state->cities,
        );
    }

    public function show(State $state): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $state,
        );
    }

    public function store(UpsertStateRequest $request): ApiSuccessResponse
    {
        $this->adminLogService->add(
            AdminLogData::fromRequest('create_state', $request->all())
        );

        return new ApiSuccessResponse(
            data: $this->service->create(
                StateData::fromRequest($request->validated())
            ),
        );
    }

    public function update(
        State $state,
        UpsertStateRequest $request,
    ): ApiSuccessResponse {
        $this->adminLogService->add(
            AdminLogData::fromRequest('update_state', $request->all())
        );

        return new ApiSuccessResponse(
            data: $this->service->update(
                state: $state,
                data: StateData::fromRequest($request->validated())
            ),
        );
    }

    public function destroy(State $state): ApiSuccessResponse|ApiErrorResponse
    {
        $this->adminLogService->add(
            AdminLogData::fromRequest(
                'delete_state',
                $state->toArray()
            )
        );

        try {
            return new ApiSuccessResponse(
                data: $this->service->delete($state),
            );
        } catch (Exception $e) {
            return new ApiErrorResponse(
                message: $e->getMessage(),
                status: Response::HTTP_BAD_REQUEST,
            );
        }
    }
}
