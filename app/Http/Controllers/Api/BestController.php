<?php

namespace App\Http\Controllers\Api;

use App\Enums\LimitType;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use App\Services\FoodService;
use App\Services\RestaurantService;
use App\Services\ShopService;
use Illuminate\Http\Request;

class BestController extends Controller
{
    public function __construct(
        private readonly FoodService $foodService,
        private readonly RestaurantService $restaurantService,
        private readonly ShopService $shopService,
    ) {
    }

    public function __invoke(string $type): ApiSuccessResponse
    {
        // see_all_id is restaurants|shops|foods|cuisines 's category_id
        switch ($type) {
            case 'restaurants':
                $data = $this->restaurantService->findBestSlider(request('city_id'));
                $data->transform(function ($item) {
                    $location = "{$item->location->address}, {$item->location->township->name}, {$item->location->township->city->name}, {$item->location->township->city->state->name}";
                    unset($item->location);
                    $item->location = $location;
                    $item->type = 'restaurants';
                    return $item;
                });
                break;
            case 'shops':
                $data = $this->shopService->findBestSlider(request('city_id'));
                $data->transform(function ($item) {
                    $location = "{$item->location->address}, {$item->location->township->name}, {$item->location->township->city->name}, {$item->location->township->city->state->name}";
                    unset($item->location);
                    $item->location = $location;
                    $item->type = 'shops';
                    return $item;
                });
                break;
            case 'foods':
                $data = $this->foodService->findBestSlider(request('city_id'));
                $data->transform(function ($item) {
                    $item->type = 'foods';
                    return $item;
                });
                break;
            case 'cuisines':
                $data = $this->restaurantService->findBestSlider(request('city_id'));
                $data->transform(function ($item) {
                    $location = "{$item->location->address}, {$item->location->township->name}, {$item->location->township->city->name}, {$item->location->township->city->state->name}";
                    unset($item->location);
                    $item->location = $location;
                    $item->type = 'restaurants';
                    return $item;
                });
                break;
        }

        return new ApiSuccessResponse(
            data: $data->filter(fn ($d) => $d->see_all_id === (int) request('see_all_id'))->values(),
        );
    }
}
