<?php

namespace App\Models;

use App\Models\Concerns\HiddenDates;
use function Symfony\Component\HttpKernel\Log\format;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FoodImage extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HiddenDates;

    protected $hidden = ['food_id'];

    protected $guarded = [];

    protected function path(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => route('api:v1:images:show', $value),
        );
    }
}
