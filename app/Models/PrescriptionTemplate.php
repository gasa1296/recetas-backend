<?php

namespace App\Models;

use Database\Factories\PrescriptionTemplateFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'name',
    'user_id',
])]
class PrescriptionTemplate extends Model
{
    /** @use HasFactory<PrescriptionTemplateFactory> */
    use HasFactory, SoftDeletes;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function medicaments(): BelongsToMany
    {
        return $this->belongsToMany(Medicament::class, MedicamentPrescriptionTemplate::class)
            ->withPivot('dosage', 'frequency', 'duration', 'medicament_quantity', 'medicament_quantity_letters', 'recommended_brand');
    }
}
