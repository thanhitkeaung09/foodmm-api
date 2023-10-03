<?php

namespace App\Http\Controllers\Admin;

use App\Dto\ReviewData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateReviewRequest;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function __construct(
        private readonly ReviewService $service,
    ) {
    }

    public function show(Review $review): ApiSuccessResponse
    {
        $review->reviewType = $review->reviewable_type;
        $review->reviewId = $review->reviewable_id;

        return new ApiSuccessResponse(
            data: $review,
        );
    }

    public function update(Review $review, UpdateReviewRequest $request): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->update(
                review: $review,
                data: ReviewData::fromRequest($request->validated()),
            ),
        );
    }

    public function destroy(Review $review): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->delete($review),
        );
    }
}
