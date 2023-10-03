<?php

declare(strict_types=1);

namespace App\Dto;

class FoodTypeData implements Dto
{
    public function __construct(
        public readonly string $name,
        public readonly int $categoryId,
        public readonly string $slug,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new static(
            name: $data['name'],
            categoryId: $data['category_id'],
            slug: (string) str($data['name'])->slug(),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'food_category_id' => $this->categoryId,
            'slug' => $this->slug,
        ];
    }
}
