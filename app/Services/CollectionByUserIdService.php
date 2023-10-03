<?php

namespace App\Services;

use App\Models\Collection;

class CollectionByUserIdService
{
    public function searchByUserId($id)
    {
        $collectedId = Collection::where("user_id", $id)->withCount('plans')->get();
        if ($collectedId) {
            return $collectedId;
        } else {
            return "Collection Not Found";
        }
    }
}
