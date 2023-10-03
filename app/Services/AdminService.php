<?php

namespace App\Services;

use App\Dto\AdminCredential;
use App\Dto\AdminData;
use App\Dto\ProfileData;
use App\Enums\LimitType;
use App\Http\Requests\AdminRequest\AdminRequest;
use App\Models\Admin;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminService
{
    public function index()
    {
        $admin = Admin::all();
        return $admin;
    }

    public function register(AdminRequest $adminRequest)
    {
        $admin = new Admin();
        $admin->name = $adminRequest->name;
        $admin->email = $adminRequest->email;
        $admin->password =  Hash::make($adminRequest->password);
        $admin->save();
        $token = Auth::user()->createToken("phone")->plainTextToken;
        return $token;
    }

    public function findByEmail(string $email): ?Admin
    {
        return Admin::query()->where('email', $email)->first();
    }

    public function create(AdminData $data): Admin
    {
        $admin = Admin::query()->create(
            $data->toArray()
        );

        $admin->assignRole($data->roles);

        return $admin;
    }

    public function updateProfile(Admin $admin, ProfileData $data): bool
    {
        return $admin->update($data->toArray());
    }

    public function updatePassword(Admin $admin, AdminCredential $data): bool
    {
        if (!Hash::check($data->oldPassword, $admin->password)) {
            throw new Exception('Old password is wrong!');
        }

        return $admin->update($data->toArray());
    }

    public function delete(Admin $admin): bool
    {
        $admin->roles()->detach();

        $admin->update(['email' => $admin->email . '_' . now()->timestamp]);

        return $admin->delete();
    }

    public function getAll(): LengthAwarePaginator
    {
        return Admin::query()->with(['roles'])->paginate(LimitType::PAGINATE->value);
    }

    public function assignRoles(Admin $admin, array $roles)
    {
        return $admin->syncRoles($roles);
    }
}
