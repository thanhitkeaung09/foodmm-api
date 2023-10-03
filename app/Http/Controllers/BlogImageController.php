<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBlogImageRequest;
use App\Http\Requests\UpdateBlogImageRequest;
use App\Models\BlogImage;

class BlogImageController extends Controller
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
     * @param  \App\Http\Requests\StoreBlogImageRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBlogImageRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BlogImage  $blogImage
     * @return \Illuminate\Http\Response
     */
    public function show(BlogImage $blogImage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BlogImage  $blogImage
     * @return \Illuminate\Http\Response
     */
    public function edit(BlogImage $blogImage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBlogImageRequest  $request
     * @param  \App\Models\BlogImage  $blogImage
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBlogImageRequest $request, BlogImage $blogImage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BlogImage  $blogImage
     * @return \Illuminate\Http\Response
     */
    public function destroy(BlogImage $blogImage)
    {
        //
    }
}
