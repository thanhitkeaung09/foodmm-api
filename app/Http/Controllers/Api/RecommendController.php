<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use App\Services\RecommendService;
use Illuminate\Http\Request;

class RecommendController extends Controller
{
    public function __construct(
        private readonly RecommendService $service,
    ) {
    }

    public function index(Request $request)
    {
        return new ApiSuccessResponse(
            data: $this->service->getAll($request->city_id)
        );
    }
}
