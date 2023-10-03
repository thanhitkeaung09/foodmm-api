<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateNotiRequest;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\Promotion;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __invoke(CreateNotiRequest $request): ApiSuccessResponse
    {
        if ($request->type === 'promotion') {
            $promotion = Promotion::query()->findOrFail($request->id);

            $promotion->notification()->create([
                'user_id' => $request->user_id,
                'is_viewed' => true,
            ]);
        }

        return new ApiSuccessResponse(
            data: true,
        );
    }
}
