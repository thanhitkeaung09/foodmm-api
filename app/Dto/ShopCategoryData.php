<?php

declare(strict_types=1);

namespace App\Dto;

class ShopCategoryData implements Dto
{
    public function __construct(
        public readonly string $name,
        public readonly string $slug,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new static(
            name: $data['name'],
            slug: (string) str($data['name'])->slug(),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
        ];
    }
}
