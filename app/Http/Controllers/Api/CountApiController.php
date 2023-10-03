<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\Count;
use App\Services\CountService;
use Illuminate\Http\Request;

class CountApiController extends Controller
{
    public function getAllCount(CountService $countService)
    {
        return new ApiSuccessResponse($countService->getAllCount());
    }

    public function getSingleCount($id, CountService $countService)
    {
        return new ApiSuccessResponse($countService->getSingleCount($id));
    }

}
