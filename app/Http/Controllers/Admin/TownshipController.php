<?php

namespace App\Http\Controllers\Admin;

use App\Dto\AdminLogData;
use App\Dto\TownshipData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpsertTownshipRequest;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\Township;
use App\Services\AdminLogService;
use App\Services\TownshipService;

class TownshipController extends Controller
{
    public function __construct(
        private readonly TownshipService $service,
        private readonly AdminLogService $adminLogService,
    ) {
    }

    public function index(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getPaginate(),
        );
    }

    public function show(Township $township): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $township->load('city:id,name'),
        );
    }

    public function store(UpsertTownshipRequest $request): ApiSuccessResponse
    {
        $this->adminLogService->add(
            AdminLogData::fromRequest('create_township', $request->all())
        );

        return new ApiSuccessResponse(
            data: $this->service->create(
                TownshipData::fromRequest($request->validated())
            ),
        );
    }

    public function update(
        Township $township,
        UpsertTownshipRequest $request,
    ): ApiSuccessResponse {
        $this->adminLogService->add(
            AdminLogData::fromRequest('update_township', $request->all())
        );

        return new ApiSuccessResponse(
            data: $this->service->update(
                township: $township,
                data: TownshipData::fromRequest($request->validated())
            ),
        );
    }

    public function destroy(Township $township): ApiSuccessResponse
    {
        $this->adminLogService->add(
            AdminLogData::fromRequest(
                'delete_township',
                $township->toArray()
            )
        );

        return new ApiSuccessResponse(
            data: $this->service->delete($township),
        );
    }
}
