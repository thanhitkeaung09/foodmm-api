<?php

namespace App\Http\Controllers\Admin;

use App\Dto\SettingData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingRequest;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\Setting;
use App\Services\SettingService;

class SettingController extends Controller
{
    public function __construct(
        private readonly SettingService $service,
    ) {
    }

    public function index(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getPaginate()
        );
    }

    public function show(Setting $setting): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $setting
        );
    }

    public function update(Setting $setting, UpdateSettingRequest $request): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->update(
                setting: $setting,
                data: SettingData::fromRequest($request->validated())
            ),
        );
    }
}
