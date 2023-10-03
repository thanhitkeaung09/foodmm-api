<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use App\Services\CityService;
use App\Services\StateService;
use App\Services\TownshipService;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function __construct(
        private readonly StateService $stateService,
        private readonly CityService $cityService,
        private readonly TownshipService $townshipService,
    ) {
    }

    public function __invoke(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: [
                'states' => $this->stateService->getAll(['id', 'name']),
                'cities' => $this->cityService->getAll(['id', 'name', 'state_id']),
                'townships' => $this->townshipService->getAll(['id', 'name', 'city_id']),
            ],
        );
    }
}
