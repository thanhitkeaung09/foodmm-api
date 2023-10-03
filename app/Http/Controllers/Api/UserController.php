<?php

namespace App\Http\Controllers\Api;

use App\Enums\Language;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateLanguageRequest;
use App\Http\Requests\Api\UpdateUserImageRequest;
use App\Http\Requests\Api\UpdateUserNameRequest;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function __construct(
        private UserService $service,
    ) {
    }

    public function show(User $user): ApiSuccessResponse
    {
        $this->checkOwner($user);

        return new ApiSuccessResponse(
            data: $this->service->getProfile(),
        );
    }

    public function updateName(UpdateUserNameRequest $request, User $user): ApiSuccessResponse
    {
        $this->checkOwner($user);

        return new ApiSuccessResponse(
            data: $this->service->updateName($user, $request->validated('name')),
        );
    }

    public function updateImage(UpdateUserImageRequest $request, User $user): ApiSuccessResponse
    {
        $this->checkOwner($user);

        return new ApiSuccessResponse(
            data: $this->service->updateImage(
                user: $user,
                newImage: $request->validated('profile_image')
            ),
        );
    }

    public function updateLanguage(UpdateLanguageRequest $request, User $user): ApiSuccessResponse
    {
        $this->checkOwner($user);

        return new ApiSuccessResponse(
            data: $this->service->updateLanguage(
                user: $user,
                language: Language::from($request->validated('language'))
            ),
        );
    }

    public function getReviews(User $user): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getReviews($user),
        );
    }

    public function updateDeviceToken(User $user, Request $request): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->updateDeviceToken($user, $request->device_token),
        );
    }

    public function destroy(User $user): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->delete($user),
        );
    }

    private function checkOwner(User $user): void
    {
        abort_unless(
            auth()->user()->is($user),
            Response::HTTP_FORBIDDEN,
            __('messages.without_permission')
        );
    }
}
