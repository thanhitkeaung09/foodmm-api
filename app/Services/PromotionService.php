<?php

namespace App\Services;

use App\Dto\PromotionData;
use App\Models\Discount;
use App\Models\Promotion;
use App\Models\User;
use App\Services\FileStorage\FileStorageService;
use App\Services\Firebase\FCMService;

class PromotionService
{
    public function __construct(
        private readonly FileStorageService $fileStorageService,
    ) {
    }

    public function getAll(?int $cityId)
    {
        $promotions = Promotion::with(['promotionable', 'notification'])->where('status', true)->get();

        $promotions->transform(function ($promotion) {
            $promotion->type = \config('promotionable.types')[$promotion->promotionable_type];
            $promotion->is_viewed = $promotion->notification?->is_viewed ?? false;
            unset($promotion->notification);
            return $promotion;
        });

        $promotions = $promotions->filter(function ($promotion) use ($cityId) {
            if ($cityId !== null) {
                return $promotion->promotionable->location->township->city_id === $cityId;
            }
            return true;
        })->values();

        return ["promotions" => $promotions];
    }

    public function singlePromotion($id)
    {
        $item = Promotion::with(['discountItems.foods', 'promotionable.location.township.city.state', 'promotionable'])->find($id);
        //        $item->shop = $item->promotionable;
        return $item;
    }

    public function getPaginate(array $column = ['*'])
    {
        return Promotion::query()
            ->with(['promotionable', 'discountItems'])
            ->paginate(columns: $column);
    }

    public function create(PromotionData $data): Promotion
    {
        $path = $this->fileStorageService->upload(
            \config('filesystems.folders.promotions'),
            $data->image
        );

        $promotion = Promotion::query()->create([
            ...$data->toArray(),
            'image' => $path,
        ]);

        $promotion->discountItems()->createMany(
            collect($data->foodIds)->map(fn ($foodId) => ['food_id' => $foodId])->toArray()
        );

        $this->sendNotification($promotion);

        return $promotion;
    }

    public function update(Promotion $promotion, PromotionData $data): bool
    {
        $attributes = $data->toArray();

        if ($data->image) {
            $this->fileStorageService->delete($promotion->getRawOriginal('image'));

            $attributes['image'] = $this->fileStorageService->upload(
                \config('filesystems.folders.promotions'),
                $data->image
            );
        }

        $promotion->discountItems()->delete();

        $promotion->discountItems()->createMany(
            collect($data->foodIds)->map(fn ($foodId) => ['food_id' => $foodId])->toArray()
        );

        return $promotion->update($attributes);
    }

    public function delete(Promotion $promotion): bool
    {
        $this->fileStorageService->delete($promotion->getRawOriginal('image'));

        $promotion->discountItems()->delete();

        return $promotion->delete();
    }

    public function removeItem(Promotion $promotion, int $itemId): bool
    {
        return Discount::query()->where('id', $itemId)->delete();
    }

    private function sendNotification(Promotion $promotion)
    {
        $users = User::query()->whereNotNull('device_token')->get();

        $users->each(function ($user) use ($promotion) {
            $title = $promotion->label . '(' . $promotion->period . ')';
            $body = $promotion->description;

            FCMService::of($user->device_token)
                ->withData([
                    'type' => 'promotion',
                    'id' => $promotion->id,
                    'title' => $title,
                    'body' => $body,
                ])
                ->withNotification(
                    title: $title,
                    body: $body,
                )
                ->send();
        });
    }
}
