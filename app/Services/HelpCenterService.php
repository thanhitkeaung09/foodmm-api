<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\HelpCenter;
use Illuminate\Database\Eloquent\Collection;

class HelpCenterService
{
    public function getAll(array $column = ['*']): Collection
    {
        return HelpCenter::query()->get($column);
    }
}
