<?php

declare(strict_types=1);

namespace App\Dto;

use App\Models\Restaurant;
use App\Models\Shop;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\UploadedFile;

class PromotionData implements Dto
{
    public function __construct(
        public readonly string $label,
        public readonly string $period,
        public readonly bool $status,
        public readonly string $description,
        public readonly ?int $shopId,
        public readonly ?int $restaurantId,
        public readonly array $foodIds,
        public readonly ?UploadedFile $image,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new static(
            label: $data['label'],
            period: $data['period'],
            status: $data['status'] === 'true',
            description: $data['description'],
            shopId: $data['shop_id'] ? (int) $data['shop_id'] : null,
            restaurantId: $data['restaurant_id'] ? (int) $data['restaurant_id'] : null,
            foodIds: $data['food_ids'],
            image: array_key_exists('images', $data) ? $data['images'][0] : null
        );
    }

    public function toArray(): array
    {
        if ($this->shopId !== 0) {
            $promotionableId = $this->shopId;
            $promotionableType = Shop::class;
        } else {
            $promotionableId = $this->restaurantId;
            $promotionableType = Restaurant::class;
        }

        return [
            'label' => $this->label,
            'period' => $this->period,
            'status' => $this->status,
            'promotionable_id' => $promotionableId,
            'promotionable_type' => $promotionableType,
            'description' => $this->description,
        ];
    }
}
