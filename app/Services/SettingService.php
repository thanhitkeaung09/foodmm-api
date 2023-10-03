<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\SettingData;
use App\Enums\LimitType;
use App\Models\Setting;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SettingService
{
    public function getPaginate($column = ['*']): LengthAwarePaginator
    {
        return Setting::query()->paginate(
            columns: $column,
            perPage: LimitType::PAGINATE->value
        );
    }

    public function update(Setting $setting, SettingData $data): bool
    {
        return $setting->update($data->toArray());
    }

    public function isRecommended(): bool
    {
        $value = Setting::query()->whereRecommended()->first('value')?->value ?? 'false';

        return $value === 'true';
    }

    public function defaultCity(): ?string
    {
        return Setting::query()->whereDefaultCity()->firstOrFail('value')?->value;
    }

    public function manualLogin(): bool
    {
        $value = Setting::query()->whereManualLogin()->first('value')?->value ?? 'false';

        return $value === 'true';
    }

    public function facebookLogin(): bool
    {
        $value = Setting::query()->whereFacebookLogin()->first('value')?->value ?? 'false';

        return $value === 'true';
    }
}
