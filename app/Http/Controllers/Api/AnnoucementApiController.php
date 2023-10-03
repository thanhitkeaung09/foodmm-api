<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\Blog;
use App\Models\Promotion;
use App\Services\AnnoucementService;
use Illuminate\Http\Request;

class AnnoucementApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(AnnoucementService $annoucementService)
    {

        return new ApiSuccessResponse($annoucementService->index(request('city_id')));
    }
}
