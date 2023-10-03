<?php

declare(strict_types=1);

namespace App\Dto;


class ProfileData implements Dto
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new static(
            name: $data['name'],
            email: $data['email'],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
        ];
    }
}
