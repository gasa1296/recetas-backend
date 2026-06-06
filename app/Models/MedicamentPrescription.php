<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'dosage',
    'frequency',
    'duration',
    'medicament_id',
    'prescription_id',
])]
class MedicamentPrescription extends Model
{
    /** @use HasFactory<\Database\Factories\PatientFactory> */
    use HasFactory, SoftDeletes;

    public function medicament()
    {
        return $this->belongsTo(Medicament::class);
    }
    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }
}
