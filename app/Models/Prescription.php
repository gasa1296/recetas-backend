<?php

namespace App\Models;

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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
])]
#[Hidden(['prescription_hash'])]
class Prescription extends Model
{
    /** @use HasFactory<PrescriptionFactory> */
    use HasFactory, SoftDeletes;

    protected $attributes = [
        'status' => '0',
    ];

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
            ->withPivot('dosage', 'frequency', 'duration', 'medicament_quantity', 'medicament_quantity_letters', 'recommended_brand');
    }

    /**
     * Used for file uploads, abstracting the actual storage mechanism.
     */
    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'model');
    }

    public function signed_file(): MorphOne
    {
        return $this->morphOne(File::class, 'model')->where('type', 'signed');
    }

    public function unsigned_file(): MorphOne
    {
        return $this->morphOne(File::class, 'model')->where('type', 'unsigned');
    }

    public function handleUploadFile(string|UploadedFile $file, string $type = 'unsigned'): bool
    {
        $name = Str::uuid().'.pdf';
        $path = date('Y').'/'.date('m').'/'.$name;
        Storage::disk('local')->put($path, $file);

        return $this->files()->create([
            'path' => $path,
            'type' => $type,
            'location' => 'local',
            'filename' => $name,
        ]) ? true : false;
    }

    protected function prettyStatus(): Attribute
    {
        return Attribute::make(
            get: fn () => config('custom.prescription.status.'.$this->status),
        );
    }

    /**
     * Generic percent attribute helper.
     * Stores values as integer (value * 100) and exposes as float (value / 100).
     */
    protected function percent(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value) => is_null($value) ? null : $value / 100,
            set: fn (mixed $value) => is_null($value) ? null : (int) round($value * 100),
        );
    }

    protected function saturation(): Attribute
    {
        return $this->percent();
    }

    protected function ppm(): Attribute
    {
        return $this->percent();
    }

    protected function temp(): Attribute
    {
        return $this->percent();
    }

    protected function weight(): Attribute
    {
        return $this->percent();
    }

    protected function height(): Attribute
    {
        return $this->percent();
    }

    protected function pressure(): Attribute
    {
        return $this->percent();
    }
}
