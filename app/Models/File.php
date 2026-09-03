<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use HasFactory, SoftDeletes;

    public const CATEGORY_RX = 'rx';
    public const CATEGORY_SKIN_LESION = 'skin_lesion';
    public const CATEGORY_CLINICAL_PHOTO = 'clinical_photo';
    public const CATEGORY_PROCEDURE_VIDEO = 'procedure_video';
    public const CATEGORY_EXAMINATION = 'examination';
    public const CATEGORY_GENERAL = 'general';

    public const VALID_CATEGORIES = [
        self::CATEGORY_RX,
        self::CATEGORY_SKIN_LESION,
        self::CATEGORY_CLINICAL_PHOTO,
        self::CATEGORY_PROCEDURE_VIDEO,
        self::CATEGORY_EXAMINATION,
        self::CATEGORY_GENERAL,
    ];

    public const STAGE_BEFORE_TREATMENT = 'before_treatment';
    public const STAGE_EVOLUTION_CONTROL = 'evolution_control';
    public const STAGE_AFTER_TREATMENT = 'after_treatment';

    public const VALID_STAGES = [
        self::STAGE_BEFORE_TREATMENT,
        self::STAGE_EVOLUTION_CONTROL,
        self::STAGE_AFTER_TREATMENT,
    ];

    protected $fillable = [
        'type',
        'category',
        'title',
        'description',
        'mime_type',
        'size',
        'meta',
        'user_id',
        'location',
        'path',
        'filename',
    ];

    protected $hidden = [
        'location',
        'type',
        'model_id',
        'model_type',
        'deleted_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'size' => 'integer',
        ];
    }

    public function getUrlAttribute()
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => asset('storage/'.$attributes['path']),
        );
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    public function scopeEvolutionStage(Builder $query, string $stage): Builder
    {
        return $query->where('meta->evolution_stage', $stage);
    }

    public function getIsImageAttribute(): bool
    {
        return str_starts_with($this->mime_type ?? '', 'image/')
            || in_array($this->category, [self::CATEGORY_RX, self::CATEGORY_SKIN_LESION, self::CATEGORY_CLINICAL_PHOTO]);
    }

    public function getIsVideoAttribute(): bool
    {
        return str_starts_with($this->mime_type ?? '', 'video/')
            || $this->category === self::CATEGORY_PROCEDURE_VIDEO;
    }

    public function getHumanSizeAttribute(): string
    {
        $bytes = $this->size ?? 0;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2).' KB';
        }

        return $bytes.' B';
    }
}
