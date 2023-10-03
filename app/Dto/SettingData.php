<?php

declare(strict_types=1);

namespace App\Dto;

class SettingData implements Dto
{
    public function __construct(
        public readonly string $name,
        public readonly string $value,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new static(
            name: $data['name'],
            value: $data['value'],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'value' => $this->value,
        ];
    }
}
