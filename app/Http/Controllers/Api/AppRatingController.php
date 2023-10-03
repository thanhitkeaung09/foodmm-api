<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AppRatingRequest;
use App\Http\Responses\ApiSuccessResponse;
use App\Services\AppRatingService;

class AppRatingController extends Controller
{
    public function __construct(
        private readonly AppRatingService $service,
    ) {
    }

    public function __invoke(AppRatingRequest $request): ApiSuccessResponse
    {
        $model = $this->service->make(
            rate: $request->validated('rate'),
        );

        return new ApiSuccessResponse(
            data: $model,
        );
    }
}
