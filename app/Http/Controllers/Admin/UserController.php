<?php

namespace App\Http\Controllers\Admin;

use App\Dto\UpdateUserData;
use App\Dto\UserData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\User;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $service,
    ) {
    }

    public function index(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getPaginate(),
        );
    }

    public function show(User $user): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $user,
        );
    }

    public function update(User $user, UpdateUserRequest $request): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->update(
                user: $user,
                userData: UpdateUserData::fromRequest(
                    data: $request->validated(),
                ),
            )
        );
    }

    public function destroy(User $user): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->delete($user)
        );
    }
}
