<?php

namespace App\Http\Controllers\Admin;

use App\Dto\AdminLogData;
use App\Dto\HelpData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpsertHelpRequest;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\HelpCenter;
use App\Services\AdminLogService;
use App\Services\HelpService;

class HelpController extends Controller
{
    public function __construct(
        private readonly HelpService $service,
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

    public function show(HelpCenter $help): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $help,
        );
    }

    public function store(UpsertHelpRequest $request): ApiSuccessResponse
    {
        $this->adminLogService->add(
            AdminLogData::fromRequest('create_helps', $request->all())
        );

        return new ApiSuccessResponse(
            data: $this->service->create(
                HelpData::fromRequest($request->validated())
            ),
        );
    }

    public function update(
        HelpCenter $help,
        UpsertHelpRequest $request,
    ): ApiSuccessResponse {
        $this->adminLogService->add(
            AdminLogData::fromRequest('update_helps', $request->all())
        );

        return new ApiSuccessResponse(
            data: $this->service->update(
                help: $help,
                data: HelpData::fromRequest($request->validated())
            ),
        );
    }

    public function destroy(HelpCenter $help): ApiSuccessResponse
    {
        $this->adminLogService->add(
            AdminLogData::fromRequest(
                'delete_helps',
                $help->toArray()
            )
        );

        return new ApiSuccessResponse(
            data: $this->service->delete($help),
        );
    }
}
