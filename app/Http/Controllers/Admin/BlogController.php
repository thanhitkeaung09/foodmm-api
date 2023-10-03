<?php

namespace App\Http\Controllers\Admin;

use App\Dto\AdminLogData;
use App\Dto\BlogData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpsertBlogRequest;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\Blog;
use App\Services\AdminLogService;
use App\Services\BlogService;

class BlogController extends Controller
{
    public function __construct(
        private readonly BlogService $service,
        private readonly AdminLogService $adminLogService,
    ) {
    }

    public function index(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getPaginate(),
        );
    }

    public function show(Blog $blog): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $blog,
        );
    }

    public function store(UpsertBlogRequest $request): ApiSuccessResponse
    {
        $this->adminLogService->add(
            AdminLogData::fromRequest('create_blog', $request->all())
        );

        return new ApiSuccessResponse(
            data: $this->service->create(
                BlogData::fromRequest($request->validated())
            ),
        );
    }

    public function update(
        Blog $blog,
        UpsertBlogRequest $request,
    ): ApiSuccessResponse {
        $this->adminLogService->add(
            AdminLogData::fromRequest('update_blog', $request->all())
        );

        return new ApiSuccessResponse(
            data: $this->service->update(
                blog: $blog,
                data: BlogData::fromRequest($request->validated())
            ),
        );
    }

    public function destroy(Blog $blog): ApiSuccessResponse
    {
        $this->adminLogService->add(
            AdminLogData::fromRequest(
                'delete_blog',
                $blog->toArray()
            )
        );

        return new ApiSuccessResponse(
            data: $this->service->delete($blog),
        );
    }
}
