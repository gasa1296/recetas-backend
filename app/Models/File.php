<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'type',
        'location',
        'path',
        'filename',
    ];

    protected $hidden = [
        'location',
        'type',
        'model_id',
        'model_type',
    ];

    public function getUrlAttribute()
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => asset('storage/'.$attributes['path']),
        );
    }

    public function model()
    {
        return $this->morphTo();
    }
}
