<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequest\AdminRequest;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\Admin;
use App\Services\AdminService;
use Illuminate\Http\Request;

class AdminApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(AdminService $adminService)
    {
        return new ApiSuccessResponse($adminService->index());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminRequest $adminRequest,AdminService $adminService)
    {
        $admin = $adminService->register($adminRequest);
        return new ApiSuccessResponse($admin);
    }

}
