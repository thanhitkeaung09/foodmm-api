<?php

namespace App\Services;

use App\Models\Blog;
use App\Models\Promotion;
use Carbon\Carbon;

class AnnoucementService
{

    public function index(int $cityId)
    {
        $blogs = Blog::with("blogImg")->where("status", true)->get();
        $blogs->transform(function ($blog) {
            $date = $blog->created_at->format("m/d/Y");
            $month = Carbon::createFromFormat('m/d/Y', $date);
            $monthName = $month->format("F d Y");
            $blog->date = $monthName;
            return $blog;
        });

        $promotions = Promotion::query()->with("promotionable.location.township")->where("status", true)->get();

        $promotions = $promotions->filter(function ($promotion) use ($cityId) {
            return $promotion->promotionable->location->township->city_id === $cityId;
        })->values();

        return ["blogs" => $blogs, "promotions" => $promotions];
    }
}
