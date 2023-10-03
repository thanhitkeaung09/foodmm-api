<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use App\Services\ShopCategoryService;
use App\Services\ShopService;

class ShopCategoryController extends Controller
{
    public function __construct(
        private ShopService $service,
    ) {
    }

    public function show(string $slug): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getAllByCategory($slug, request('city_id'), request('query')),
        );
    }
}
