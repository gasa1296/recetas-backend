<?php

namespace App\Models;

use Database\Factories\ExaminationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'patient_id',
    'user_id',
    'prescription_id',
    'name',
    'type',
    'examined_at',
    'laboratory_name',
    'findings',
    'status',
])]
class Examination extends Model
{
    /** @use HasFactory<ExaminationFactory> */
    use HasFactory, SoftDeletes;

    public const TYPE_LABORATORY = 'laboratory';
    public const TYPE_IMAGING = 'imaging';
    public const TYPE_PATHOLOGY = 'pathology';
    public const TYPE_CARDIOLOGY = 'cardiology';
    public const TYPE_OTHER = 'other';

    public const VALID_TYPES = [
        self::TYPE_LABORATORY,
        self::TYPE_IMAGING,
        self::TYPE_PATHOLOGY,
        self::TYPE_CARDIOLOGY,
        self::TYPE_OTHER,
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_REVIEWED = 'reviewed';

    public const VALID_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_COMPLETED,
        self::STATUS_REVIEWED,
    ];

    protected $attributes = [
        'type' => self::TYPE_LABORATORY,
        'status' => self::STATUS_COMPLETED,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'examined_at' => 'date',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'model')->latest('id');
    }

    public function scopeType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeDateRange(Builder $query, $from, $to): Builder
    {
        return $query->when($from, fn (Builder $q) => $q->whereDate('examined_at', '>=', $from))
            ->when($to, fn (Builder $q) => $q->whereDate('examined_at', '<=', $to));
    }
}
