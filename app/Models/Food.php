<?php

namespace App\Models;

use App\Builders\FoodBuilder;
use App\Models\Concerns\CalculateAverageRating;
use App\Models\Concerns\HiddenDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Food extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HiddenDates;
    use CalculateAverageRating;

    protected $guarded = [];

    protected $with = ['images'];

    protected $hidden = ['pivot'];

    public function type(): BelongsTo
    {
        return $this->belongsTo(FoodType::class, 'food_type_id', 'id');
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function images(): HasMany
    {
        return $this->hasMany(FoodImage::class, 'food_id', 'id');
    }

    public function ratings(): MorphMany
    {
        return $this->morphMany(Rating::class, 'rateable');
    }

    public function restaurants(): BelongsToMany
    {
        return $this->belongsToMany(Restaurant::class, 'restaurant_menus');
    }

    public function shops(): BelongsToMany
    {
        return $this->belongsToMany(Shop::class, 'shop_items');
    }

    public static function query(): FoodBuilder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query)
    {
        return new FoodBuilder($query);
    }

    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(Plan::class, 'plans_foods');
    }
}
