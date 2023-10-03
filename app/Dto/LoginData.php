<?php

declare(strict_types=1);

namespace App\Dto;


class LoginData implements Dto
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new static(
            email: $data['email'],
            password: $data['password'],
        );
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
