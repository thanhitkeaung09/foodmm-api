<?php

namespace App\Models;

use App\Models\Concerns\HiddenDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;
    use HiddenDates;

    protected $guarded = [];

    public function foods()
    {
        return $this->belongsTo(Food::class, 'food_id', 'id');
    }
}
