<?php

declare(strict_types=1);

namespace App\Dto;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class ShopData implements Dto
{
    /** @param Collection<UploadedFile> $images */
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly array $openingHours,
        public readonly int $categoryId,
        public readonly LocationData $location,
        public readonly ?string $phones,
        public readonly Collection $images,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new static(
            name: $data['name'],
            description: $data['description'],
            openingHours: $data['opening_hours'],
            categoryId: (int) $data['category_id'],
            location: new LocationData(
                townshipId: (int) $data['township_id'],
                address: $data['address'],
                latitude: $data['latitude'],
                longitude: $data['longitude'],
            ),
            phones: $data['phones'],
            images: array_key_exists('images', $data) ? collect($data['images']) : collect()
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'category_id' => $this->categoryId,
            'phones' => $this->phones,
            'opening_hours' => $this->openingHours,
        ];
    }
}
