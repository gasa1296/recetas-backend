<?php

namespace App\Models;

use Database\Factories\PatientFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
    /** @use HasFactory<PatientFactory> */
    use HasFactory, SoftDeletes;

    public function medicament(): BelongsTo
    {
        return $this->belongsTo(Medicament::class);
    }

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }
}
