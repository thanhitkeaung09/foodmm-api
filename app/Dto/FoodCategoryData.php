<?php

declare(strict_types=1);

namespace App\Dto;

class FoodCategoryData implements Dto
{
    public function __construct(
        public readonly string $name,
        public readonly bool $isRecommended,
        public readonly string $slug,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new static(
            name: $data['name'],
            isRecommended: $data['is_recommended'],
            slug: (string) str($data['name'])->slug(),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'is_recommended' => $this->isRecommended,
            'slug' => $this->slug,
        ];
    }
}
