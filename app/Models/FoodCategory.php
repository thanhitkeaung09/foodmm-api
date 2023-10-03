<?php

namespace App\Models;

use App\Builders\FoodCategoryBuilder;
use App\Models\Concerns\HiddenDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class FoodCategory extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HiddenDates;

    protected $guarded = [];

    protected $casts = [
        'is_recommended' => 'boolean',
    ];

    public function types(): HasMany
    {
        return $this->hasMany(FoodType::class, 'food_category_id', 'id');
    }

    public function preferred(): HasOne
    {
        return $this->hasOne(UserPreferr::class, 'food_category_id', 'id');
    }

    public function foods(): HasManyThrough
    {
        return $this->hasManyThrough(Food::class, FoodType::class);
    }

    public static function query(): FoodCategoryBuilder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query)
    {
        return new FoodCategoryBuilder($query);
    }
}
