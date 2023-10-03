<?php

namespace App\Http\Controllers\Api;

use App\Dto\LoginData;
use App\Dto\RegisterData;
use App\Dto\UserData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\EmailLoginRequest;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Services\AuthService;
use App\Services\FileStorage\FileStorageService;
use App\Services\SettingService;
use Exception;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    public function __construct(
        private FileStorageService $fileStorageService,
        private AuthService $authService,
        private SettingService $settingService,
    ) {
    }

    public function register(RegisterRequest $request): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->authService->register(
                registerData: RegisterData::fromRequest(
                    data: $request->validated(),
                ),
            )
        );
    }

    public function loginWithEmail(EmailLoginRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            return new ApiSuccessResponse(
                data: $this->authService->loginWithEmail(
                    data: LoginData::fromRequest(
                        data: $request->validated(),
                    ),
                ),
            );
        } catch (Exception $e) {
            return new ApiErrorResponse(
                message: $e->getMessage(),
                status: Response::HTTP_UNAUTHORIZED,
            );
        }
    }

    public function login(LoginRequest $request, string $type): ApiSuccessResponse
    {
        $user = $this->authService->login(
            UserData::fromRequest($request->validated(), $type)
        );

        return new ApiSuccessResponse(
            data: $user,
        );
    }

    public function logout(): ApiSuccessResponse
    {
        $this->authService->logout(auth()->user());

        return new ApiSuccessResponse(
            data: null,
            message: __('messages.logout_success'),
            status: Response::HTTP_NO_CONTENT
        );
    }

    public function manualLogin()
    {
        return new ApiSuccessResponse(
            data: $this->settingService->manualLogin()
        );
    }

    public function facebookLogin()
    {
        return new ApiSuccessResponse(
            data: $this->settingService->facebookLogin()
        );
    }
}
