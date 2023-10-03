<?php

declare(strict_types=1);

namespace App\Dto;

use Illuminate\Support\Facades\Hash;

class AppVersionData implements Dto
{
    public function __construct(
        public readonly string $version,
        public readonly string $buildNo,
        public readonly bool $isForcedUpdated,
        public readonly string $iosLink,
        public readonly string $androidLink,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new static(
            version: $data['version'],
            buildNo: $data['build_no'],
            isForcedUpdated: $data['is_forced_updated'],
            iosLink: $data['ios_link'],
            androidLink: $data['android_link'],
        );
    }

    public function toArray(): array
    {
        return [
            'version' => $this->version,
            'build_no' => $this->buildNo,
            'is_forced_updated' => $this->isForcedUpdated,
            'ios_link' => $this->iosLink,
            'android_link' => $this->androidLink,
        ];
    }
}
