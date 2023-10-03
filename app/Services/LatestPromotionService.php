<?php

namespace App\Services;

use App\Models\Promotion;

class LatestPromotionService
{
    public function latestPromotion()
    {
        $latestPromotions = Promotion::with(['discountItems.foods','promotionable'])->where("status",true)->latest()->get();
        return $latestPromotions;
    }
}
