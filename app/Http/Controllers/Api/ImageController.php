<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\FoodImage;
use App\Models\RestaurantImage;
use App\Models\ShopImage;
use App\Services\FileStorage\FileStorageService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ImageController extends Controller
{
    public function __construct(
        private FileStorageService $service,
    ) {
    }

    public function show(string $path): Response
    {
        return $this->service->display($path);
    }

    public function destroy(Request $request, $id): ApiSuccessResponse
    {
        switch ($request->type) {
            case 'restaurant':
                $image = RestaurantImage::query()->where('id', $id)->first();
                break;
            case 'shop':
                $image = ShopImage::query()->where('id', $id)->first();
                break;
            case 'food':
                $image = FoodImage::query()->where('id', $id)->first();
                break;
        }

        $this->service->delete($image->getRawOriginal('path'));
        $image->forceDelete();

        return new ApiSuccessResponse(true, 'Success');
    }
}
