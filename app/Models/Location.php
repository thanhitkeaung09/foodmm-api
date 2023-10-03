<?php

namespace App\Models;

use App\Models\Concerns\HiddenDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HiddenDates;

    protected $guarded = [];

    protected $with = ['township', 'township.city', 'township.city.state'];

    public function township(): BelongsTo
    {
        return $this->belongsTo(Township::class, 'township_id', 'id');
    }

    public function restaurant(): HasOne
    {
        return $this->hasOne(Restaurant::class, 'location_id', 'id');
    }

    public function shop(): HasOne
    {
        return $this->hasOne(Shop::class, 'location_id', 'id');
    }
}
