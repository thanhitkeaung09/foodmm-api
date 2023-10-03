<?php

declare(strict_types=1);

namespace App\Dto;

class HelpData implements Dto
{
    public function __construct(
        private readonly string $question,
        private readonly string $answer,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new static(
            question: $data['question'],
            answer: $data['answer'],
        );
    }

    public function toArray(): array
    {
        return [
            'question' => $this->question,
            'answer' => $this->answer,
        ];
    }
}
