<?php

namespace App\Models;

use App\Models\Concerns\HiddenDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantCategory extends Model
{
    use HasFactory;
    use HiddenDates;
    use SoftDeletes;

    protected $guarded = [];

    public function restaurants(): HasMany
    {
        return $this->hasMany(Restaurant::class, 'category_id', 'id');
    }
}
