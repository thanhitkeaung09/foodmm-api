<?php

declare(strict_types=1);

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

class PlanBuilder extends Builder
{
    public function whereUpcoming()
    {
        $this->where('planed_at', '>=', now()->toDateTimeString());

        return $this;
    }

    public function whereHistory()
    {
        $this->where('planed_at', '<', now()->toDateTimeString());

        return $this;
    }

    public function whereToday()
    {
        $this->whereDate('reminded_at', '=', now());

        return $this;
    }

    public function whereReminded()
    {
        $this->whereDate('reminded_at', '=', now())
            ->whereTime('reminded_at', '=', now());

        return $this;
    }
}
