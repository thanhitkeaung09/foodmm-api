<?php

namespace App\Http\Controllers\Admin;

use App\Dto\RoleData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpsertRoleRequest;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Services\RoleService;
use Exception;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct(
        private readonly RoleService $service,
    ) {
    }

    public function index(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getAll(),
        );
    }

    public function getPermissions(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getPermissions(),
        );
    }

    public function show(Role $role): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $role->load('permissions'),
        );
    }

    public function store(UpsertRoleRequest $request): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->create(
                RoleData::fromRequest($request->validated())
            ),
        );
    }

    public function update(Role $role, UpsertRoleRequest $request): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->update(
                role: $role,
                data: RoleData::fromRequest($request->validated()),
            )
        );
    }

    public function destroy(Role $role): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            return new ApiSuccessResponse(
                data: $this->service->delete($role)
            );
        } catch (Exception $e) {
            return new ApiErrorResponse(
                message: $e->getMessage(),
            );
        }
    }
}
