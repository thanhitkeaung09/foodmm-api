<?php

declare(strict_types=1);

namespace App\Dto;

use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;

class PlanData implements Dto
{
    public function __construct(
        public readonly ?string $description,
        public readonly ?int $restaurantId,
        public readonly ?int $shopId,
        public readonly int $collectionId,
        public readonly array $foods,
        public readonly CarbonImmutable $planedAt,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        $date = (new Carbon($data['plan_date']))->setTimezone('Asia/Yangon')->setTimeFromTimeString($data['plan_time'])->startOfMinute()->toImmutable();

        return new self(
            description: $data['description'] ?? null,
            restaurantId: $data['restaurant_id'] ?? null,
            shopId: $data['shop_id'] ?? null,
            collectionId: $data['collection_id'],
            foods: $data['foods'],
            planedAt: $date,
        );
    }

    public function toArray(): array
    {
        return [
            'description' => $this->description,
            'shop_id' => $this->shopId,
            'restaurant_id' => $this->restaurantId,
            'collection_id' => $this->collectionId,
            'reminded_at' => $this->planedAt->subHours(\config('plan.remind_before_hours')),
            'planed_at' => $this->planedAt,
            'user_id' => auth()->id(),
        ];
    }
}
