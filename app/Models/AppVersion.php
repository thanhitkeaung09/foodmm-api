<?php

namespace App\Models;

use App\Models\Concerns\HiddenDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppVersion extends Model
{
    use HasFactory;
    use HiddenDates;

    protected $guarded = [];

    protected $casts = [
        'is_forced_updated' => 'boolean',
    ];
}
