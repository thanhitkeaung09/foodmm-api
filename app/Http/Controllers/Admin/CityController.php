<?php

namespace App\Http\Controllers\Admin;

use App\Dto\AdminLogData;
use App\Dto\CityData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpsertCityRequest;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\City;
use App\Services\AdminLogService;
use App\Services\CityService;
use Exception;
use Illuminate\Http\Response;

class CityController extends Controller
{
    public function __construct(
        private readonly CityService $service,
        private readonly AdminLogService $adminLogService,
    ) {
    }

    public function all(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getAll(),
        );
    }

    public function getTownships(City $city): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $city->townships,
        );
    }

    public function index(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getPaginate(),
        );
    }

    public function show(City $city): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $city,
        );
    }

    public function store(UpsertCityRequest $request): ApiSuccessResponse
    {
        $this->adminLogService->add(
            AdminLogData::fromRequest('create_city', $request->all())
        );

        return new ApiSuccessResponse(
            data: $this->service->create(
                CityData::fromRequest($request->validated())
            ),
        );
    }

    public function update(
        City $city,
        UpsertCityRequest $request,
    ): ApiSuccessResponse {
        $this->adminLogService->add(
            AdminLogData::fromRequest('update_city', $request->all())
        );

        return new ApiSuccessResponse(
            data: $this->service->update(
                city: $city,
                data: CityData::fromRequest($request->validated())
            ),
        );
    }

    public function destroy(City $city): ApiSuccessResponse|ApiErrorResponse
    {
        $this->adminLogService->add(
            AdminLogData::fromRequest(
                'delete_city',
                $city->toArray()
            )
        );

        try {
            return new ApiSuccessResponse(
                data: $this->service->delete($city),
            );
        } catch (Exception $e) {
            return new ApiErrorResponse(
                message: $e->getMessage(),
                status: Response::HTTP_BAD_REQUEST,
            );
        }
    }
}
