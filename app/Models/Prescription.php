<?php

namespace App\Models;

use Database\Factories\PrescriptionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
    'status',
])]
class Prescription extends Model
{
    /** @use HasFactory<PrescriptionFactory> */
    use HasFactory, SoftDeletes;

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
            ->withPivot('dose', 'frequency', 'duration');
    }

    /**
     * Used for file uploads, abstracting the actual storage mechanism.
     */
    public function file(): MorphOne
    {
        return $this->morphOne(File::class, 'model');
    }

    public function handleUploadFile(string|UploadedFile $file): bool
    {
        if ($oldFile = $this->file) {
            Storage::disk('private')->delete($oldFile->path);
            $oldFile->delete();
        }
        $path = date('Y').'/'.date('m').'/'.Str::uuid().'.pdf';
        Storage::disk('private')->put($path, $file);

        return $this->file()->create([
            'path' => $path,
            'type' => 'profile',
            'location' => 'private',
            'filename' => $file->getClientOriginalName(),
        ]) ? true : false;
    }
}
