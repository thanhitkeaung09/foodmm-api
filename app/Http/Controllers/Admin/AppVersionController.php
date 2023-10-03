<?php

namespace App\Http\Controllers\Admin;

use App\Dto\AppVersionData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpsertAppVersionRequest;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\AppVersion;
use App\Services\AppVersionService;
use Illuminate\Http\Request;

class AppVersionController extends Controller
{
    public function __construct(
        private readonly AppVersionService $service
    ) {
    }

    public function index(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getPaginate(),
        );
    }

    public function show(AppVersion $appVersion): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $appVersion,
        );
    }

    public function first(): ApiSuccessResponse
    {
        $appVersion = $this->service->first();

        return new ApiSuccessResponse(
            data: [
                'id' => $appVersion->id,
                'version' => $appVersion->version,
                'build_no' => $appVersion->build_no,
                'is_forced_updated' => $appVersion->is_forced_updated,
                'ios_app_id' => $appVersion->ios_link,
                'android_app_id' => $appVersion->android_link,
            ],
        );
    }

    public function update(AppVersion $appVersion, UpsertAppVersionRequest $request): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->update(
                $appVersion,
                AppVersionData::fromRequest($request->validated()),
            ),
        );
    }
}
