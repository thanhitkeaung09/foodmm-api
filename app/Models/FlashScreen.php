<?php

namespace App\Models;

use App\Models\Concerns\HiddenDates;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlashScreen extends Model
{
    use HasFactory;
    use HiddenDates;

    protected $guarded = [];

    // protected $casts = [
    //     'flash_screen_status' => 'boolean',
    // ];

    protected function flashScreenImage(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => route('api:v1:images:show', $value),
        );
    }
}
