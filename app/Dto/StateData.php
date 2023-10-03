<?php

declare(strict_types=1);

namespace App\Dto;

class StateData implements Dto
{
    public function __construct(
        private readonly string $name,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new static(
            name: $data['name'],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
