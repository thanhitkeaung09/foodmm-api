<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\StateData;
use App\Enums\LimitType;
use App\Models\State;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class StateService
{
    public function __construct()
    {
    }

    public function getAll($column = ['*']): Collection
    {
        return State::query()->get($column);
    }

    public function getPaginate($column = ['*']): LengthAwarePaginator
    {
        return State::query()->paginate(
            columns: $column,
            perPage: LimitType::PAGINATE->value,
        );
    }

    public function create(StateData $data): State
    {
        return State::query()->create($data->toArray());
    }

    public function update(State $state, StateData $data): bool
    {
        return $state->update($data->toArray());
    }

    public function delete(State $state): bool
    {
        if ($state->cities()->count() > 0) {
            throw new Exception("You can't delete it because it has cities!");
        }

        $state->update(['name' => $state->name . '_' . now()->timestamp]);

        return $state->delete();
    }
}
