<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\ApplicationKey;
use App\Services\ApplicationKeyService;
use Illuminate\Http\Request;

class ApplicationKeyController extends Controller
{
    public function __construct(
        private readonly ApplicationKeyService $service,
    ) {
    }

    public function index(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getPaginate()
        );
    }

    public function show(ApplicationKey $applicationKey): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $applicationKey,
        );
    }

    public function store(Request $request): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->create($request->name),
        );
    }

    public function used(ApplicationKey $applicationKey): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $applicationKey->update(['obsoleted' => false]),
        );
    }

    public function obsoleted(ApplicationKey $applicationKey): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $applicationKey->update(['obsoleted' => true]),
        );
    }
}
