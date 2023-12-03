<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Specialization extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'identification',
        'university',
        'logo',
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
