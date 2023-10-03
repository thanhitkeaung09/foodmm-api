<?php

namespace App\Models;

use App\Models\Concerns\HiddenDates;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogImage extends Model
{
    use HasFactory;
    use HiddenDates;
    use SoftDeletes;

    protected $guarded = [];

    public function blogs()
    {
        return $this->belongsTo(Blog::class);
    }

    protected function path(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => route('api:v1:images:show', $value),
        );
    }
}
