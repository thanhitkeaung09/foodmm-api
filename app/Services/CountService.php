<?php

namespace App\Services;

use App\Dto\CountData;
use App\Models\Count;

class CountService
{
    public function getAllCount()
    {
        $counts = Count::with('city:id,name')->get();
        return $counts;
    }

    public function getSingleCount($id)
    {
        $singleCount = Count::with('city:id,name')->find($id);
        return $singleCount;
    }

    public function increase(int $cityId, CountData $data): void
    {
        $count = Count::query()->where('city_id', $cityId)->first();

        if (isset($count)) {
            $updateColumn = collect($data->toArray())->filter(fn ($d) => $d !== 0)->keys()->first();
            $count->{$updateColumn} = $count->{$updateColumn} + 1;
            $count->save();
        } else {
            Count::query()->create([...$data->toArray(), 'city_id' => $cityId]);
        }
    }

    public function decrease(int $cityId, CountData $data): void
    {
        $count = Count::query()->where('city_id', $cityId)->first();

        if (isset($count)) {
            $updateColumn = collect($data->toArray())->filter(fn ($d) => $d !== 0)->keys()->first();
            $count->{$updateColumn} = $count->{$updateColumn} - 1;
            $count->save();
        }
    }
}
