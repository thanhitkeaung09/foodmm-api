<?php

namespace App\Models;

use App\Builders\SettingBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public static function query(): SettingBuilder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query)
    {
        return new SettingBuilder($query);
    }
}
