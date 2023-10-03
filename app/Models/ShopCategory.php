<?php

namespace App\Models;

use App\Models\Concerns\HiddenDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShopCategory extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HiddenDates;

    protected $guarded = [];

    public function shops(): HasMany
    {
        return $this->hasMany(Shop::class, 'category_id', 'id');
    }
}
