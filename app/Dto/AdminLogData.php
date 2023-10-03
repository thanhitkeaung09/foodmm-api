<?php

declare(strict_types=1);

namespace App\Dto;

class AdminLogData implements Dto
{
    public function __construct(
        public readonly string $action,
        public readonly array $payload,
        public readonly int $adminId,
    ) {
    }

    public static function fromRequest(string $action, array $data): self
    {
        return new static(
            action: $action,
            payload: $data,
            adminId: auth()->user()->id,
        );
    }

    public function toArray(): array
    {
        return [
            'action' => $this->action,
            'payload' => $this->payload,
            'admin_id' => $this->adminId,
        ];
    }
}
