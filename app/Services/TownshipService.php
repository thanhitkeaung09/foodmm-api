<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\TownshipData;
use App\Enums\LimitType;
use App\Models\Township;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TownshipService
{
    public function __construct()
    {
    }

    public function getAll($column = ['*']): Collection
    {
        return Township::query()->get($column);
    }

    public function getPaginate($column = ['*']): LengthAwarePaginator
    {
        return Township::query()->with(['city', 'city.state'])->paginate(
            columns: $column,
            perPage: LimitType::PAGINATE->value,
        );
    }

    public function create(TownshipData $data): Township
    {
        return Township::query()->create($data->toArray());
    }

    public function update(Township $township, TownshipData $data): bool
    {
        return $township->update($data->toArray());
    }

    public function delete(Township $township): bool
    {
        $township->update(['name' => $township->name . '_' . now()->timestamp]);

        return $township->delete();
    }
}
