<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\AppVersionData;
use App\Enums\LimitType;
use App\Models\AppVersion;
use Illuminate\Pagination\LengthAwarePaginator;

class AppVersionService
{
    public function getPaginate(): LengthAwarePaginator
    {
        return AppVersion::query()->paginate(LimitType::PAGINATE->value);
    }

    public function first(): AppVersion
    {
        return AppVersion::query()->first();
    }

    public function update(AppVersion $appVersion, AppVersionData $data): bool
    {
        return $appVersion->update($data->toArray());
    }
}
