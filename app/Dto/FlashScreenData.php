<?php

declare(strict_types=1);

namespace App\Dto;

use Illuminate\Http\UploadedFile;

class FlashScreenData implements Dto
{
    public function __construct(
        public readonly bool $status,
        public readonly ?UploadedFile $image,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new static(
            status: $data['status'] === 'true',
            image: array_key_exists('images', $data) ? $data['images'][0] : null
        );
    }

    public function toArray(): array
    {
        return [
            'flash_screen_status' => $this->status,
        ];
    }
}
