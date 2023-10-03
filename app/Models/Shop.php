<?php

namespace App\Models;

use App\Builders\ShopBuilder;
use App\Models\Concerns\CalculateAverageRating;
use App\Models\Concerns\HiddenDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HiddenDates;
    use CalculateAverageRating;

    protected $guarded = [];

    protected $casts = [
        'opening_hours' => 'array',
    ];

    protected $with = ['images'];

    protected $hidden = ['pivot',];

    public function promotion()
    {
        return $this->morphMany(Promotion::class, 'promotionable');
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function ratings(): MorphMany
    {
        return $this->morphMany(Rating::class, 'rateable');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ShopCategory::class, 'category_id', 'id');
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Food::class, 'shop_items')->withPivot(['is_special']);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ShopImage::class, 'shop_id', 'id');
    }

    public static function query(): ShopBuilder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query)
    {
        return new ShopBuilder($query);
    }

    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class, 'shop_id', 'id');
    }
}
