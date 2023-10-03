<?php

namespace App\Services;

use App\Dto\BlogData;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\Blog;
use App\Models\BlogImage;
use App\Services\FileStorage\FileStorageService;
use Carbon\Carbon;

class BlogService
{
    public function __construct(
        private readonly FileStorageService $fileStorageService,
    ) {
    }
    public function index()
    {
        $blogs = Blog::with('blogImg')->where('status', true)->get();
        $blogs->transform(function ($blog) {
            $date = $blog->created_at->format("m/d/Y");
            $month = Carbon::createFromFormat('m/d/Y', $date);
            $monthName = $month->format("F d Y");
            $blog->date = $monthName;
            return $blog;
        });
        return ["blogs" => $blogs];
    }

    public function show($id)
    {
        $blog = Blog::with("blogImg")->find($id);
        $date = $blog->created_at->format("m/d/Y");
        $month = Carbon::createFromFormat('m/d/Y', $date);
        $monthName = $month->format("F d Y");
        if ($blog) {
            $blog->date = $monthName;
            return ["blog" => $blog];
        } else {
            return response()->json([
                "message" => "Blog Not Found"
            ]);
        }
    }

    public function getPaginate(array $column = ['*'])
    {
        return Blog::query()->with('admin')->paginate(columns: $column);
    }

    public function create(BlogData $data): Blog
    {
        $blog = Blog::query()->create($data->toArray());

        $path = $this->fileStorageService->upload(
            \config('filesystems.folders.blogs'),
            $data->image
        );

        $blog->blogImg()->create(['path' => $path]);

        return $blog;
    }

    public function update(Blog $blog, BlogData $data): bool
    {
        if ($data->image) {
            if ($blog->blogImg) {
                $this->fileStorageService->delete($blog->blogImg->getRawOriginal('path'));
            }

            $path = $this->fileStorageService->upload(
                \config('filesystems.folders.blogs'),
                $data->image
            );

            BlogImage::query()->updateOrCreate(['blog_id' => $blog->id], ['path' => $path]);
        }

        return $blog->update($data->toArray());
    }

    public function delete(Blog $blog): bool
    {
        if ($blog->blogImg) {
            $this->fileStorageService->delete($blog->blogImg->getRawOriginal('path'));
        }

        $blog->blogImg()->delete();

        return $blog->delete();
    }
}
