<?php

declare(strict_types=1);

namespace App\Dto;

class LocationData implements Dto
{
    public function __construct(
        public readonly int $townshipId,
        public readonly string $address,
        public readonly ?string $latitude = null,
        public readonly ?string $longitude = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'township_id' => $this->townshipId,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }
}
