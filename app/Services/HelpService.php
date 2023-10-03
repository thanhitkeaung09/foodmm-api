<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\HelpData;
use App\Enums\LimitType;
use App\Models\HelpCenter;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class HelpService
{
    public function __construct()
    {
    }

    public function getAll($column = ['*']): Collection
    {
        return HelpCenter::query()->get($column);
    }

    public function getPaginate($column = ['*']): LengthAwarePaginator
    {
        return HelpCenter::query()->paginate(
            columns: $column,
            perPage: LimitType::PAGINATE->value,
        );
    }

    public function create(HelpData $data): HelpCenter
    {
        return HelpCenter::query()->create($data->toArray());
    }

    public function update(HelpCenter $help, HelpData $data): bool
    {
        return $help->update($data->toArray());
    }

    public function delete(HelpCenter $help): bool
    {
        return $help->delete();
    }
}
