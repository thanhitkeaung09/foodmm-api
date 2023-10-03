<?php

namespace App\Models;

use App\Models\Concerns\HiddenDates;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends Model
{
    use HasFactory;
    use HiddenDates;
    use SoftDeletes;

    protected $guarded = [];

    public function promotionable()
    {
        return $this->morphTo();
    }

    public function discountItems()
    {
        return $this->hasMany(Discount::class);
    }

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => route('api:v1:images:show', $value),
        );
    }

    public function notification(): MorphOne
    {
        return $this->morphOne(Notification::class, 'notiable');
    }
}
