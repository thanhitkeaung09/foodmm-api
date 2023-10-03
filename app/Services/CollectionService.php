<?php

namespace App\Services;

use App\Dto\CollectionData;
use App\Http\Requests\CollectRequest\CollectionStoreRequest;
use App\Models\Collection;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Auth;

class CollectionService
{
    public function getAll(array $column = ['*']): EloquentCollection
    {
        return Collection::query()->get($column);
    }

    public function create(User $user, CollectionData $data): Collection
    {
        return $user->collections()->create(
            $data->toArray()
        );
    }

    public function index(): SupportCollection
    {
        return Collection::query()->get();
    }

    public function show($id)
    {
        $collection_item = Collection::findOrFail($id);
        if (!($collection_item)) {
            return "Folder Not Found";
        }
        return $collection_item;
    }

    public function update(Collection $collection, CollectionData $data): Collection
    {
        $collection->update($data->toArray());

        return $collection->fresh();
    }

    public function destroy($id)
    {
        $collection_item = Collection::findOrFail($id);
        if (!($collection_item)) {
            return "User Not Found";
        }
        $collect_item = $collection_item->delete();
        return "Deleted Successfully";
    }

    public function multipleDelete(array $ids): bool
    {
        return Collection::query()->whereIn('id', $ids)->forceDelete();
    }
}
