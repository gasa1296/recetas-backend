<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultingRoom extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'zip',
        'street',
        'colony',
        'state',
        'delegation',
        'n_exterior',
        'n_interior',
        'address',
        'phone',
        'logo',
        'desing',
        'user_id',
    ];
}
