<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prescription extends Model
{
    use HasFactory;
    /**
     * Get the medic of the prescription.
     */
    public function medic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'medic_id');
    }
    /**
     * Get the patient of the prescription.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
