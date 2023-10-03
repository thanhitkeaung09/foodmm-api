<?php

namespace App\Http\Controllers\Api;

use App\Dto\CollectionData;
use App\Http\Controllers\Controller;
use App\Http\Requests\CollectRequest\DeleteCollectionRequest;
use App\Http\Requests\CollectRequest\UpsertCollectionRequest;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\Collection;
use App\Services\CollectionByUserIdService;
use App\Services\CollectionService;
use Illuminate\Http\Request;

class CollectionApiController extends Controller
{
    public function __construct(
        private readonly CollectionService $service,
    ) {
    }

    public function index(): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            data: $this->service->getAll(['id', 'name'])
        );
    }

    public function store(UpsertCollectionRequest $request)
    {
        return new ApiSuccessResponse(
            data: $this->service->create(
                user: auth()->user(),
                data: new CollectionData($request->validated('name'))
            ),
        );
    }

    public function show(Collection $collection)
    {
        return new ApiSuccessResponse(
            data: $collection,
        );
    }

    public function update(UpsertCollectionRequest $request, Collection $collection)
    {
        return new ApiSuccessResponse(
            data: $this->service->update(
                collection: $collection,
                data: new CollectionData($request->validated('name'))
            )
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, CollectionService $collectionService)
    {
        $collect_item = $collectionService->destroy($id);
        return new ApiSuccessResponse($collect_item);
    }

    public function collectionByUser($id, CollectionByUserIdService $collectionByUserIdService)
    {
        return new ApiSuccessResponse($collectionByUserIdService->searchByUserId($id));
    }

    public function collectionMultipleDelete(DeleteCollectionRequest $request)
    {
        $this->service->multipleDelete($request->validated('data'));

        return response()->json([
            "message" => "Successfully deleted",
            "status" => 404
        ]);
    }
}
