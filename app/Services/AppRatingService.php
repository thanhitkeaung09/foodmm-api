<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AppRating;

class AppRatingService
{
    public function __construct(
        private UserService $userService,
    ) {
    }

    public function make(int $rate)
    {
        $user = $this->userService->firstById(auth()->id());

        $appRating = $user->appRating;

        if ($appRating) {
            $user->appRating()->update(['rate' => $rate]);
            return $appRating->fresh();
        }

        return $user->appRating()->create(['rate' => $rate]);
    }
}
