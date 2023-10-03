<?php

declare(strict_types=1);

namespace App\Dto;

class RoleData implements Dto
{
    public function __construct(
        public readonly string $name,
        public readonly array $permissions,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new static(
            name: $data['name'],
            permissions: $data['permissions'],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
