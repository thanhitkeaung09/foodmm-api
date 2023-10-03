<?php

namespace App\Http\Controllers\Api;

use App\Dto\RatingData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RatingRequest;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\Restaurant;
use App\Services\RatingService;

class RestaurantRatingController extends Controller
{
    public function __construct(
        private readonly RatingService $service,
    ) {
    }

    public function __invoke(RatingRequest $request, Restaurant $restaurant): ApiSuccessResponse
    {
        $data = RatingData::fromRequest($restaurant, $request->validated());

        return new ApiSuccessResponse(
            data: $this->service->addRatingAndReviews($data),
            message: __('messages.rate_success'),
        );
    }
}
