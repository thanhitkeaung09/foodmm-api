<?php

declare(strict_types=1);

namespace App\Models\Concerns;

trait HiddenDates
{
    public function getHidden()
    {
        return [...$this->hidden, 'created_at', 'updated_at', 'deleted_at'];
    }
}
