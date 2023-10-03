<?php

namespace App\Http\Controllers\Api;

use App\Dto\RatingData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RatingRequest;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\Food;
use App\Services\RatingService;

class FoodRatingController extends Controller
{
    public function __construct(
        private readonly RatingService $service,
    ) {
    }

    public function __invoke(RatingRequest $request, Food $food): ApiSuccessResponse
    {
        $data = RatingData::fromRequest($food, $request->validated());

        return new ApiSuccessResponse(
            data: $this->service->addRatingAndReviews($data),
            message: __('messages.rate_success'),
        );
    }
}
