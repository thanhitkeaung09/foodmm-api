<?php

namespace App\Http\Controllers\Admin;

use App\Dto\LoginData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Http\Responses\ApiSuccessResponse;
use App\Services\AuthService;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $service,
    ) {
    }

    public function login(LoginRequest $request): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->adminLogin(
                LoginData::fromRequest($request->validated())
            ),
        );
    }

    public function logout(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->logout(auth()->user()),
        );
    }
}
