<?php

namespace App\Models;

use Database\Factories\UniversityFactory;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Table(timestamps: false)]
class University extends Model
{
    /** @use HasFactory<UniversityFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'alpha_two_code',
        'country',
    ];
}
