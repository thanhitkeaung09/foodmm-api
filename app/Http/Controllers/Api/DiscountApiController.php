<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountApiController extends Controller
{
    public function getAllDiscountItems()
    {
        return Discount::all();
    }

    public function getSingleDiscountItem($id)
    {
        $discountItem = Discount::find($id);
        return $discountItem->foods;
    }

}
