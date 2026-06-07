<?php

namespace App\Models;

use Database\Factories\PrescriptionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'temp',
    'weight',
    'height',
    'pressure',
    'saturation',
    'ppm',
    'allergy',
    'diagnostic',
    'diet',
    'comments',
    'user_id',
    'room_id',
    'patient_id',
    'status',
])]
class Prescription extends Model
{
    /** @use HasFactory<PrescriptionFactory> */
    use HasFactory, SoftDeletes;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    public function medicaments()
    {
        return $this->belongsToMany(Medicament::class, MedicamentPrescription::class)
            ->withPivot('dose', 'frequency', 'duration');
    }
}
