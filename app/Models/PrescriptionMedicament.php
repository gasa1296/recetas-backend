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
        'add',
        'dose',
        'way',
        'frequency',
        'duration',
        'quantity',
        'quantity_exp',
        'medicament_id',
        'name',
        'type',
        'family',
        'group',
        'salt',
        'prescription_id',
    ];
    /**
     * Get the prescription of the medicament.
     */
    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }
}
