<?php

namespace App\Http\Controllers;

use App\Models\BlogImage;
use Illuminate\Http\Request;

class BlogImageApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $blogimages = BlogImage::all();
        return $blogimages;
    }

}
