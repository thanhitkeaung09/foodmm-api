<?php

declare(strict_types=1);

namespace App\Dto;

use Illuminate\Support\Facades\Hash;

class AdminCredential implements Dto
{
    public function __construct(
        public readonly string $oldPassword,
        public readonly string $newPassword,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new static(
            oldPassword: $data['old_password'],
            newPassword: Hash::make($data['new_password']),
        );
    }

    public function toArray(): array
    {
        return [
            'password' => $this->newPassword,
        ];
    }
}
