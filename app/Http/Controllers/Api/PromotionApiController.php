<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\Blog;
use App\Models\Promotion;
use App\Models\Restaurant;
use App\Models\Shop;
use App\Services\PromotionService;
use Illuminate\Http\Request;

class PromotionApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PromotionService $promotionService)
    {
        return new ApiSuccessResponse($promotionService->getAll(request('city_id')));
    }

    public function show($id, PromotionService $promotionService)
    {
        return new ApiSuccessResponse($promotionService->singlePromotion($id));
    }
}
