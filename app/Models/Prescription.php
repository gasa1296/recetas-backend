<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prescription extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'temp',
        'weight',
        'height',
        'pressure',
        'saturation',
        'ppm',
        'allergy',
        'diagnostic',
        'diet',
        'add',
        'client',
        'user_id',
        'room_id',
        'patient_id',
        'file',
        'status',
    ];
    /**
     * Get the medic of the prescription.
     */
    public function medic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    /**
     * Get the room of the prescription.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(ConsultingRoom::class);
    }
    /**
     * Get the patient of the prescription.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
    /**
     * Get the medicaments of the prescription.
     */
    public function medicaments(): HasMany
    {
        return $this->hasMany(PrescriptionMedicament::class);
    }
    /**
     * Get the equipment of the prescription.
     */
    public function equipment(): HasMany
    {
        return $this->hasMany(PrescriptionEquipment::class);
    }
}
