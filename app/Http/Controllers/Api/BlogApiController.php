<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\Blog;
use App\Services\BlogService;
use Illuminate\Http\Request;

class BlogApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(BlogService $blogService)
    {
        return new ApiSuccessResponse($blogService->index());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, BlogService $blogService)
    {
        return new ApiSuccessResponse($blogService->show($id));
    }

}
