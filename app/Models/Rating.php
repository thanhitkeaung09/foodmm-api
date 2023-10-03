<?php

namespace App\Models;

use App\Models\Concerns\HiddenDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rating extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HiddenDates;

    protected $guarded = [];

    protected $hidden = [
        "rate",
        "rating_type_id",
        "rateable_type",
        "rateable_id",
        "user_id",
    ];

    protected $with = ['type'];

    protected $casts = ['average_rate' => 'decimal:1'];

    public function rateable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(RatingType::class, 'rating_type_id', 'id');
    }
}
