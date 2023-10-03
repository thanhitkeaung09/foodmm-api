<?php

declare(strict_types=1);

namespace App\Dto;

use Illuminate\Support\Facades\Hash;

class AdminData implements Dto
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $password,
        public readonly array $roles,
        public readonly ?string $oldPassword = null,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new static(
            name: $data['name'],
            email: $data['email'],
            password: array_key_exists('password', $data) ? Hash::make($data['password']) : null,
            roles: $data['roles'],
        );
    }

    public function toArray(): array
    {
        $attributes = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if ($this->password) {
            $attributes['password'] = $this->password;
        }

        return $attributes;
    }
}
