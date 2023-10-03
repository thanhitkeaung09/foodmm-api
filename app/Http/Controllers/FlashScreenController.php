<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFlashScreenRequest;
use App\Http\Requests\UpdateFlashScreenRequest;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\FlashScreen;

class FlashScreenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreFlashScreenRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFlashScreenRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FlashScreen  $flashScreen
     * @return \Illuminate\Http\Response
     */
    public function show(FlashScreen $flashScreen)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\FlashScreen  $flashScreen
     * @return \Illuminate\Http\Response
     */
    public function edit(FlashScreen $flashScreen)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateFlashScreenRequest  $request
     * @param  \App\Models\FlashScreen  $flashScreen
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFlashScreenRequest $request, FlashScreen $flashScreen)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FlashScreen  $flashScreen
     * @return \Illuminate\Http\Response
     */
    public function destroy(FlashScreen $flashScreen)
    {
        //
    }
}
