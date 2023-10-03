<?php

namespace App\Http\Controllers\Admin;

use App\Dto\AdminLogData;
use App\Dto\FlashScreenData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpsertFlashScreenRequest;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\FlashScreen;
use App\Services\AdminLogService;
use App\Services\FlashScreenService;

class FlashScreenController extends Controller
{
    public function __construct(
        private readonly FlashScreenService $service,
        private readonly AdminLogService $adminLogService,
    ) {
    }

    public function index(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getPaginate(),
        );
    }

    public function show(FlashScreen $flashScreen): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $flashScreen,
        );
    }

    public function store(UpsertFlashScreenRequest $request): ApiSuccessResponse| ApiErrorResponse
    {
        if (!$request->has('images')) {
            return new ApiErrorResponse(message: 'Image must not be empty!');
        }

        $this->adminLogService->add(
            AdminLogData::fromRequest('create_flash_screen', $request->all())
        );

        return new ApiSuccessResponse(
            data: $this->service->create(
                FlashScreenData::fromRequest($request->validated())
            ),
        );
    }

    public function update(
        FlashScreen $flashScreen,
        UpsertFlashScreenRequest $request,
    ): ApiSuccessResponse {
        $this->adminLogService->add(
            AdminLogData::fromRequest('update_flash_screen', $request->all())
        );

        return new ApiSuccessResponse(
            data: $this->service->update(
                flashScreen: $flashScreen,
                data: FlashScreenData::fromRequest($request->validated())
            ),
        );
    }

    public function destroy(FlashScreen $flashScreen): ApiSuccessResponse
    {
        $this->adminLogService->add(
            AdminLogData::fromRequest(
                'delete_flash_screen',
                $flashScreen->toArray()
            )
        );

        return new ApiSuccessResponse(
            data: $this->service->delete($flashScreen),
        );
    }
}
