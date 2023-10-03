<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\LocationData;
use App\Models\Location;

class LocationService
{
    public function __construct()
    {
    }

    public function create(LocationData $data): Location
    {
        return Location::query()->create($data->toArray());
    }
}
