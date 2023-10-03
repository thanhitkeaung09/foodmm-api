<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\RatingData;
use App\Models\RatingType;
use App\Services\FileStorage\SpaceStorage;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class RatingService
{
    public function __construct(
        private readonly SpaceStorage $spaceStorage,
    ) {
    }

    public function calculateTotalAverage(Collection $ratings): float
    {
        if ($ratings->count() === 0) {
            return round(0, 1);
        }

        $usersCount = $ratings->groupBy('user_id')->count();

        $totalRate = $ratings->sum('rate');

        $averageRate = $totalRate / $ratings->count();

        return round($averageRate / $usersCount, 1);
    }

    public function averagePerUser(Collection $ratings, int $userId): string
    {
        $ratingsByUser = $ratings->where('user_id', $userId);

        if ($ratings->count() === 0 || $ratingsByUser->count() === 0) {
            return "0.0";
        }

        return (string) round($ratingsByUser->sum('rate') / $ratingsByUser->count(), 1);
    }

    public function addRatingAndReviews(RatingData $data): bool
    {
        $this->createRatings($data);

        $this->updateAverageRating($data);

        $this->createReviews($data);

        return true;
    }

    private function createReviews(RatingData $data): void
    {
        $review = $data->model->reviews()->create([
            'user_id' => auth()->id(),
            'text' => $data->review,
        ]);

        if ($data->images->count() > 0) {
            $images = $data->images->map(function ($image) {
                return ['path' => $this->spaceStorage->upload(\config('filesystems.folders.reviews'), $image)];
            });

            $review->images()->createMany($images);
        }
    }

    private function updateAverageRating(RatingData $data): void
    {
        $ratings = $data->model->ratings;

        if ($ratings->count() > 0) {
            $averageRating = $this->calculateTotalAverage($ratings);
        }

        if ($ratings->count() === 0) {
            $averageRating = $data->ratings->sum() / $data->ratings->count();
        }

        $data->model->ratings()->update(['average_rate' => $averageRating]);
    }

    private function createRatings(RatingData $data): void
    {
        $ratingTypes = RatingType::query()->get();

        $data->ratings->each(function ($item, $key) use ($data, $ratingTypes) {
            $rateType = $ratingTypes->where('name', str_replace('_', ' ', Str::title($key)))
                ->first();

            $data->model->ratings()->updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'rating_type_id' => $rateType->id,
                ],
                [
                    'rate' => $item,
                ]
            );
        });
    }
}
