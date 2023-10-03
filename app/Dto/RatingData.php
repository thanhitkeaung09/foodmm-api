<?php

declare(strict_types=1);

namespace App\Dto;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class RatingData implements Dto
{
    public function __construct(
        public readonly Model $model,
        public readonly Collection $ratings,
        public readonly string $review,
        public readonly ?Collection $images = null,
    ) {
    }

    public static function fromRequest(Model $model, array $data): self
    {
        return new static(
            model: $model,
            ratings: collect([
                'food_taste' => $data['food_taste_rating'],
                'customer_service' => $data['customer_service_rating'],
            ]),
            review: $data['review'],
            images: array_key_exists('images', $data) ? collect($data['images']) : collect([]),
        );
    }

    public function toArray(): array
    {
        return [
            'model' => $this->model,
            'ratings' => $this->ratings,
            'review' => $this->review,
            'images' => $this->images,
        ];
    }
}
