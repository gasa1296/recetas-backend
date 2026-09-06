<?php

namespace App\Models;

use App\Services\Media\FileStorageService;
use Database\Factories\PrescriptionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;

#[Fillable([
    'temp',
    'weight',
    'height',
    'pressure',
    'saturation',
    'ppm',
    'allergy',
    'diagnostic',
    'diet',
    'comments',
    'user_id',
    'room_id',
    'patient_id',
    'specialty_id',
    'status',
    'prescription_hash',
    'expires_at',
    'dispensed_by_id',
    'dispensed_at',
])]
#[Hidden(['prescription_hash'])]
class Prescription extends Model
{
    /** @use HasFactory<PrescriptionFactory> */
    use HasFactory, SoftDeletes;

    protected $attributes = [
        'status' => '0',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'dispensed_at' => 'datetime',
            'expires_at' => 'date',
        ];
    }

    public function dispensedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dispensed_by_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    public function medicaments(): BelongsToMany
    {
        return $this->belongsToMany(Medicament::class, MedicamentPrescription::class)
            ->withPivot('dosage', 'frequency', 'duration', 'medicament_quantity', 'medicament_quantity_letters', 'recommended_brand', 'brand_id', 'laboratory_id');
    }

    /**
     * Used for file uploads, abstracting the actual storage mechanism.
     */
    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'model');
    }

    public function signedFile(): MorphOne
    {
        return $this->morphOne(File::class, 'model')->where('type', 'signed')->latestOfMany();
    }

    public function unsignedFile(): MorphOne
    {
        return $this->morphOne(File::class, 'model')->where('type', 'unsigned')->latestOfMany();
    }

    public function signed_file(): MorphOne
    {
        return $this->signedFile();
    }

    public function unsigned_file(): MorphOne
    {
        return $this->unsignedFile();
    }

    public function handleUploadFile(string|UploadedFile $file, string $type = 'unsigned'): bool
    {
        return (bool) app(FileStorageService::class)->storePrescriptionPdf($this, $file, $type);
    }

    protected function prettyStatus(): Attribute
    {
        return Attribute::make(
            get: fn () => config('custom.prescription.status.'.$this->status),
        );
    }

    /**
     * Generic fixed-point scaled decimal attribute helper.
     * Stores values as integer (value * 100) to preserve two decimal places
     * in integer columns, and exposes as float (value / 100).
     */
    protected function scaledDecimal(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value) => is_null($value) ? null : $value / 100,
            set: fn (mixed $value) => is_null($value) ? null : (int) round($value * 100),
        );
    }

    /**
     * Backward-compatible alias for scaledDecimal.
     */
    protected function percent(): Attribute
    {
        return $this->scaledDecimal();
    }

    protected function saturation(): Attribute
    {
        return $this->scaledDecimal();
    }

    protected function ppm(): Attribute
    {
        return $this->scaledDecimal();
    }

    protected function temp(): Attribute
    {
        return $this->scaledDecimal();
    }

    protected function weight(): Attribute
    {
        return $this->scaledDecimal();
    }

    protected function height(): Attribute
    {
        return $this->scaledDecimal();
    }

    protected function pressure(): Attribute
    {
        return $this->scaledDecimal();
    }
}
