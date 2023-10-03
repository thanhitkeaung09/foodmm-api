<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\FlashScreen;
use Illuminate\Http\Request;

class FlashScreenApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $flashscreens = FlashScreen::query()->where('flash_screen_status', true)->get();
        return new ApiSuccessResponse($flashscreens);
    }
}
