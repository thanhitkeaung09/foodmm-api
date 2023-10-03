<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\CollectionData;
use App\Dto\LoginData;
use App\Dto\RegisterData;
use App\Dto\UserData;
use App\Models\Admin;
use App\Models\User;
use App\Services\FileStorage\FileStorageService;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(
        private readonly UserService $userService,
        private readonly AdminService $adminService,
        private readonly CollectionService $collectionService,
        private readonly FileStorageService $fileStorageService,
    ) {
    }

    public function register(RegisterData $registerData): User
    {
        $path = $this->fileStorageService->upload(
            folder: \config('filesystems.folders.profiles'),
            file: $registerData->profile
        );

        $user = User::query()->create([
            ...$registerData->toArray(),
            'profile_image' => $path,
        ])->refresh();

        // Create Default Collection which is called 'All'
        $this->collectionService->create($user, new CollectionData('All'));

        return $user;
    }

    public function loginWithEmail(LoginData $data): User
    {
        $user = User::query()->where('email', $data->email)->first();

        if (isset($user) && Hash::check($data->password, $user->password)) {
            return $this->generateToken($user, $user->email);
        }

        throw new Exception('User credentials did not match!');
    }

    public function login(UserData $userData): User
    {
        $user = $this->userService->findBySocialType(
            type: $userData->socialType,
            id: $userData->socialId,
        );

        if (is_null($user)) {
            $user = $this->userService->create($userData);

            // Create Default Collection which is called 'All'
            $this->collectionService->create($user, new CollectionData('All'));
        } else {
            $user->update(['device_token' => $userData->deviceToken]);

            $this->fileStorageService->update(
                $user->getRawOriginal('profile_image'),
                $userData->profile
            );
        }

        return $this->generateToken($user, $userData->socialId);
    }

    public function logout(User|Admin $user): void
    {
        if ($user instanceof User) {
            $user->update(['device_token' => null]);
        }

        $this->revokeTokens($user);
    }

    public function adminLogin(LoginData $data): Admin
    {
        $admin = $this->adminService->findByEmail($data->email);

        if (!$admin) {
            throw new AuthenticationException('The user email or password was incorrect');
        }

        if (!Hash::check($data->password, $admin->password)) {
            throw new AuthenticationException('The user email or password was incorrect');
        }

        return $this->generateToken($admin->load(['roles.permissions']), $data->email);
    }

    private function generateToken(User|Admin $model, string $unique): User|Admin
    {
        return tap($model, function ($model) use ($unique) {
            // $this->revokeTokens($model);

            $model->token = $model->createToken($unique)->plainTextToken;
        });
    }

    private function revokeTokens(User|Admin $user): void
    {
        $user->tokens()->delete();
    }
}
