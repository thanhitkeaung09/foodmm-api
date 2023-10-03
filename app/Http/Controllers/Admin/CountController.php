<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use App\Services\CountService;
use App\Services\UserService;

class CountController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
        private readonly CountService $countService
    ) {
    }

    public function __invoke()
    {
        return new ApiSuccessResponse(
            data: [
                'user_counts' => $this->userService->getAllCount(),
                'counts' => $this->countService->getAllCount(),
            ],
        );
    }
}
