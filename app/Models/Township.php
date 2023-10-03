<?php

namespace App\Models;

use App\Models\Concerns\HiddenDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Township extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HiddenDates;

    protected $guarded = [];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class, 'township_id', 'id');
    }
}
