<?php

declare(strict_types=1);

namespace App\Dto;

class TownshipData implements Dto
{
    public function __construct(
        private readonly string $name,
        private readonly int $cityId,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new static(
            name: $data['name'],
            cityId: $data['city_id'],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'city_id' => $this->cityId,
        ];
    }
}
