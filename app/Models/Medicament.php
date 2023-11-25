<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medicament extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'form',
        'ingredient',
        'way',
        'image',
    ];
    /**
     * Get the prescriptions of the medicaments.
     */
    public function prescriptions(): HasMany
    {
        return $this->hasMany(PrescriptionMedicament::class, 'medicament_id');
    }
}
