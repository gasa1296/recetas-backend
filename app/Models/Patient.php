<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class Patient extends Model
{
    use HasFactory, SoftDeletes;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'first_name',
        'last_name1',
        'last_name2',
        'phone1',
        'phone2',
        'email',
        'birth_date',
        'user_id',
        'gender',
    ];
    /**
     * Get the prescriptions of the patient.
     */
    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }
    /**
     * Get the medic of the prescription.
     */
    public function medic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
