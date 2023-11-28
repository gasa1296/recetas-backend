<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'image',
    ];
    /**
     * Get the prescriptions of the equipment.
     */
    public function prescriptions(): HasMany
    {
        return $this->hasMany(PrescriptionEquipment::class, 'equipment_id');
    }
}
