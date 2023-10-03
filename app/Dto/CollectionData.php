<?php

declare(strict_types=1);

namespace App\Dto;

class CollectionData implements Dto
{
    public function __construct(
        public readonly string $name,
    ) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name
        ];
    }
}
