<?php

namespace App\Models;

use App\Models\Concerns\HiddenDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Count extends Model
{
    use HasFactory;
    use HiddenDates;

    protected $guarded = [];

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }
}
