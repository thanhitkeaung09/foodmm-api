<?php

namespace App\Models\Concerns;

trait CalculateAverageRating
{
    public function averageRating(): string
    {
        return $this->ratings->first()?->average_rate ?? "0.0";
    }
}
