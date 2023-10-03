<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use App\Services\CityService;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function __construct(
        private readonly CityService $service,
    ) {
    }

    public function index(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getAll(),
        );
    }

    public function getCity(Request $request): ApiSuccessResponse
    {
        $data = $this->service->findByName($request->name);
        $message = __('messages.success');

        if (is_null($data)) {
            $data = $this->service->getDefault();
            $message = __('messages.city_not_found');
        }

        return new ApiSuccessResponse(
            data: $data,
            message: $message,
        );
    }
}
