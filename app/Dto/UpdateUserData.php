<?php

declare(strict_types=1);

namespace App\Dto;

use App\Enums\Language;
use Illuminate\Http\UploadedFile;

class UpdateUserData implements Dto
{
    public function __construct(
        public readonly string $name,
        public readonly ?UploadedFile $profile,
        public readonly Language $language,
        public readonly ?string $email,
        public readonly ?string $phone,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new static(
            name: $data['name'],
            email: $data['email'],
            phone: $data['phone'],
            profile: array_key_exists('images', $data) ? $data['images'][0] : null,
            language: Language::from($data['language']),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'language' => $this->language,
        ];
    }
}
