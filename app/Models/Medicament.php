<?php

namespace App\Models;

use Database\Factories\MedicamentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name'])]
class Medicament extends Model
{
    /** @use HasFactory<MedicamentFactory> */
    use HasFactory;

    public function prescriptions(): BelongsToMany
    {
        return $this->belongsToMany(Prescription::class, MedicamentPrescription::class)
            ->withPivot('dosage', 'frequency', 'duration');
    }
}
