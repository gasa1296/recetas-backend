<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name',])]
class Medicament extends Model
{
    /** @use HasFactory<\Database\Factories\MedicamentFactory> */
    use HasFactory, SoftDeletes;

    public function prescriptions()
    {
        return $this->belongsToMany(Prescription::class, MedicamentPrescription::class)
            ->withPivot('dosage', 'frequency', 'duration');
    }
}
