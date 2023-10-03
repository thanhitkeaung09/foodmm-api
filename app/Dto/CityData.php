<?php

declare(strict_types=1);

namespace App\Dto;

class CityData implements Dto
{
    public function __construct(
        private readonly string $name,
        private readonly int $stateId,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new static(
            name: $data['name'],
            stateId: $data['state_id'],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'state_id' => $this->stateId,
        ];
    }
}
