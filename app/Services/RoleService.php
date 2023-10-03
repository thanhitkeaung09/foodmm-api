<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\RoleData;
use Exception;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleService
{
    public function getAll(): Collection
    {
        return Role::query()->with('permissions')->get();
    }

    public function getPermissions(): Collection
    {
        return Permission::query()->get();
    }

    public function create(RoleData $data): Role
    {
        $role = Role::query()->create($data->toArray());

        $role->givePermissionTo($data->permissions);

        return $role;
    }

    public function update(Role $role, RoleData $data): bool
    {
        $role->syncPermissions($data->permissions);

        return $role->update($data->toArray());
    }

    public function delete(Role $role): void
    {
        if ($role->users()->count() > 0) {
            throw new Exception('This role can not delete because it has already been used!');
        }

        $role->permissions()->detach();

        $role->update(['name' => $role->name . '_' . now()->timestamp]);

        $role->delete();
    }
}
