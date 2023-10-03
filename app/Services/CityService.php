<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\CityData;
use App\Enums\LimitType;
use App\Models\City;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CityService
{
    public function __construct(
        private readonly SettingService $settingService,
    ) {
    }

    public function findByName(string $name): ?City
    {
        return City::query()->where('name', $name)->first();
    }

    public function getDefault(): City
    {
        return City::query()->where('name', $this->settingService->defaultCity())->firstOrFail();
    }

    public function getAll(array $column = ['*']): Collection
    {
        return City::query()->with('state')->get(
            columns: $column
        );
    }

    public function getPaginate(array $column = ['*']): LengthAwarePaginator
    {
        return City::query()->with('state')->paginate(
            columns: $column,
            perPage: LimitType::PAGINATE->value,
        );
    }

    public function create(CityData $data): City
    {
        return City::query()->create($data->toArray());
    }

    public function update(City $city, CityData $data): bool
    {
        return $city->update($data->toArray());
    }

    public function delete(City $city): bool
    {
        if ($city->townships()->count() > 0) {
            throw new Exception("You can't delete it because it has townships!");
        }

        $city->update(['name' => $city->name . '_' . now()->timestamp]);

        return $city->delete();
    }
}
