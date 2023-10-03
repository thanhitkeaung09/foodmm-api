<?php

declare(strict_types=1);

namespace App\Dto;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class ReviewData implements Dto
{
    /** @param Collection<UploadedFile> $images */
    public function __construct(
        public readonly string $text,
        public readonly int $userId,
        public readonly string $reviewableType,
        public readonly int $reviewableId,
        public readonly Collection $images,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new static(
            text: $data['text'],
            userId: (int) $data['user_id'],
            reviewableType: $data['reviewable_type'],
            reviewableId: (int) $data['reviewable_id'],
            images: array_key_exists('images', $data) ? collect($data['images']) : collect(),
        );
    }

    public function toArray(): array
    {
        return [
            'text' => $this->text,
            'user_id' => $this->userId,
            'reviewable_type' => $this->reviewableType,
            'reviewable_id' => $this->reviewableId
        ];
    }
}
