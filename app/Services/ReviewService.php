<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\ReviewData;
use App\Models\Review;
use App\Services\FileStorage\FileStorageService;
use Illuminate\Support\Collection;

class ReviewService
{
    public function __construct(
        private readonly FileStorageService $fileStorageService,
    ) {
    }

    public function all(): Collection
    {
        return Review::query()
            ->without('images')
            ->selectRaw('reviewable_type as type, COUNT(*) as count')
            ->groupBy('reviewable_type')
            ->get();
    }

    public function update(Review $review, ReviewData $data): bool
    {
        if ($data->images->count() > 0) {
            $review->images()->forceDelete();

            $this->createImages($data->images, $review);
        }

        return $review->update($data->toArray());
    }

    public function delete(Review $review): bool
    {
        return $review->delete();
    }

    private function createImages(Collection $images, Review $review): void
    {
        if ($images->count() > 0) {
            $paths = $images->map(function ($image) {
                return [
                    'path' => $this->fileStorageService->upload(
                        \config('filesystems.folders.reviews'),
                        $image
                    )
                ];
            });

            $review->images()->createMany($paths);
        }
    }
}
