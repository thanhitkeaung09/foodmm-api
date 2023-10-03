<?php

namespace App\Models;

use App\Builders\RestaurantBuilder;
use App\Models\Concerns\CalculateAverageRating;
use App\Models\Concerns\HiddenDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Restaurant extends Model
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(RestaurantCategory::class, 'category_id', 'id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(RestaurantImage::class, 'restaurant_id', 'id');
    }

    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Food::class, 'restaurant_menus')->withPivot(['is_special']);
    }

    public static function query(): RestaurantBuilder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query)
    {
        return new RestaurantBuilder($query);
    }

    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class, 'restaurant_id', 'id');
    }
}
