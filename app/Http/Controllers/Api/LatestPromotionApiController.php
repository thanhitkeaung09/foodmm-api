<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\Promotion;
use App\Services\LatestPromotionService;
use Illuminate\Http\Request;

class LatestPromotionApiController extends Controller
{
    public function getLatestPromotion(LatestPromotionService $latestPromotionService)
    {
        return new ApiSuccessResponse($latestPromotionService->latestPromotion());
    }
}
