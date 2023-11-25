<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrescriptionMedicament extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'dose',
        'way',
        'frequency',
        'duration',
        'medicament_id',
        'prescription_id',
    ];
    /**
     * Get the medicament of the prescription.
     */
    public function medicament(): BelongsTo
    {
        return $this->belongsTo(Medicament::class);
    }
    /**
     * Get the prescription of the medicament.
     */
    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }
}
