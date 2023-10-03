<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Plan;
use App\Services\Firebase\FCMService;

class RemindService
{
    public function __construct()
    {
    }

    public function remind()
    {
        $plans = Plan::query()->with(['user', 'restaurant', 'shop'])->whereReminded()->get();

        $plans->reject(fn ($plan) => $plan->user->device_token === null)->each(function ($plan) {
            $user = $plan->user;

            $title = $plan->restaurant ? $plan->restaurant->name : $plan->shop->name;
            $body = $plan->description;

            FCMService::of($user->device_token)
                ->withData([
                    'type' => 'plan',
                    'id' => $plan->id,
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
