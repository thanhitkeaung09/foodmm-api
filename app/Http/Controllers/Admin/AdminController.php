<?php

namespace App\Http\Controllers\Admin;

use App\Dto\AdminCredential;
use App\Dto\AdminData;
use App\Dto\ProfileData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminCredentialRequest;
use App\Http\Requests\Admin\AssignRolesRequest;
use App\Http\Requests\Admin\StoreAdminRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;
use App\Http\Requests\Admin\UpdateProfileRequest;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\Admin;
use App\Services\AdminService;
use Exception;
use Illuminate\Http\Response;

class AdminController extends Controller
{
    public function __construct(
        private readonly AdminService $service,
    ) {
    }

    public function index(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getAll(),
        );
    }

    public function show(Admin $admin): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $admin->load('roles')
        );
    }

    public function store(StoreAdminRequest $request): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->create(
                AdminData::fromRequest($request->validated()),
            )
        );
    }

    public function updateProfile(Admin $admin, UpdateProfileRequest $request): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->updateProfile(
                $admin,
                ProfileData::fromRequest($request->validated()),
            )
        );
    }

    public function updatePassword(
        Admin $admin,
        AdminCredentialRequest $request,
    ): ApiSuccessResponse|ApiErrorResponse {
        try {
            return new ApiSuccessResponse(
                data: $this->service->updatePassword(
                    $admin,
                    AdminCredential::fromRequest($request->validated()),
                )
            );
        } catch (Exception $e) {
            return new ApiErrorResponse(
                message: $e->getMessage(),
                status: Response::HTTP_BAD_REQUEST,
            );
        }
    }

    public function destroy(Admin $admin): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->delete($admin)
        );
    }

    public function assignRoles(Admin $admin, AssignRolesRequest $request): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->assignRoles(
                admin: $admin,
                roles: $request->validated('roles')
            ),
        );
    }

    public function getPermissions(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: auth()->user()->load('roles.permissions'),
        );
    }
}
