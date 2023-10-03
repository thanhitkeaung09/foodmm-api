<?php

declare(strict_types=1);

namespace App\Dto;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class FoodData implements Dto
{
    /** @param Collection<UploadedFile> $images */
    public function __construct(
        public readonly string $name,
        public readonly int $foodTypeId,
        public readonly string $description,
        public readonly Collection $images,
        public readonly ?string $ingredients = null,
        public readonly ?string $vitamins = null,
        public readonly ?string $calories = null,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new static(
            name: $data['name'],
            foodTypeId: (int) $data['food_type_id'],
            description: $data['description'],
            ingredients: $data['ingredients'],
            vitamins: $data['vitamins'],
            calories: $data['calories'],
            images: array_key_exists('images', $data) ? collect($data['images']) : collect(),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'food_type_id' => $this->foodTypeId,
            'description' => $this->description,
            'ingredients' => $this->ingredients,
            'vitamins' => $this->vitamins,
            'calories' => $this->calories,
        ];
    }
}
