<?php

namespace App\Models;

use Database\Factories\MedicamentFactory;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Table(timestamps: false)]
class Medicament extends Model
{
    /** @use HasFactory<MedicamentFactory> */
    use HasFactory;

    public function prescriptions(): BelongsToMany
    {
        return $this->belongsToMany(Prescription::class, MedicamentPrescription::class)
            ->withPivot('dosage', 'frequency', 'duration', 'medicament_quantity', 'medicament_quantity_letters', 'recommended_brand', 'brand_id', 'laboratory_id');
    }
}
