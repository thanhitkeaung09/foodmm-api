<?php

declare(strict_types=1);

namespace App\Dto;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;

class RegisterData implements Dto
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly UploadedFile $profile,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new static(
            name: $data['name'],
            email: $data['email'],
            password: $data['password'],
            profile: $data['profile'],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'social_type' => 'email',
        ];
    }
}
