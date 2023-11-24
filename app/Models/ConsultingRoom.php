<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultingRoom extends Model
{
    use HasFactory, SoftDeletes;

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
    /**
     * Get the medic of the room.
     */
    public function medic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'medic_id');
    }
}
