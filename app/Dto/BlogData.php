<?php

declare(strict_types=1);

namespace App\Dto;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\UploadedFile;

class BlogData implements Dto
{
    public function __construct(
        public readonly string $title,
        public readonly string $body,
        public readonly string $readingTime,
        public readonly string $type,
        public readonly bool $status,
        public readonly int $adminId,
        public readonly ?UploadedFile $image,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new static(
            title: $data['title'],
            body: $data['body'],
            readingTime: $data['blogs_reading_time'],
            type: 'blog',
            adminId: auth()->user()->id,
            status: $data['status'] === 'true',
            image: array_key_exists('images', $data) ? $data['images'][0] : null
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'blogs_reading_time' => $this->readingTime,
            'type' => $this->type,
            'admin_id' => $this->adminId,
            'status' => $this->status,
        ];
    }
}
