<?php

namespace App\Models;

use App\Enums\Language;
use App\Models\Concerns\HiddenDates;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use HiddenDates;

    protected $hidden = ['password'];

    protected $guarded = [];

    protected $casts = [
        'language' => Language::class,
    ];

    protected function profileImage(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => route('api:v1:images:show', $value),
        );
    }

    public function appRating(): HasOne
    {
        return $this->hasOne(AppRating::class, 'user_id', 'id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'user_id', 'id');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class, 'user_id', 'id');
    }

    public function preferred(): HasMany
    {
        return $this->hasMany(UserPreferr::class, 'user_id', 'id');
    }

    public function collections(): HasMany
    {
        return $this->hasMany(Collection::class, 'user_id', 'id');
    }

    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class, 'user_id', 'id');
    }
}
