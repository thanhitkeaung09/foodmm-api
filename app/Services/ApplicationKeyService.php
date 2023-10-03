<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\LimitType;
use App\Models\ApplicationKey;
use Illuminate\Pagination\LengthAwarePaginator;

class ApplicationKeyService
{
    public function getPaginate($column = ['*']): LengthAwarePaginator
    {
        return ApplicationKey::query()->paginate(
            columns: $column,
            perPage: LimitType::PAGINATE->value
        );
    }

    public function create(string $name): ApplicationKey
    {
        return ApplicationKey::query()->create([
            'name' => $name,
            'app_id' => generateAppId(),
            'app_secrete' => generateAppSecrete(),
            'obsoleted' => false,
        ]);
    }
}
