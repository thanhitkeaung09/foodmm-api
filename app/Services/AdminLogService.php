<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\AdminLogData;
use App\Models\AdminLog;

class AdminLogService
{
    public function add(AdminLogData $data): void
    {
        AdminLog::query()->create($data->toArray());
    }
}
