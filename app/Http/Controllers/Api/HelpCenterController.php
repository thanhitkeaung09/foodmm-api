<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use App\Services\HelpCenterService;
use Illuminate\Http\Request;

class HelpCenterController extends Controller
{
    public function __construct(
        private readonly HelpCenterService $service,
    ) {
    }

    public function __invoke(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getAll([
                'id', 'question', 'answer'
            ])
        );
    }
}
