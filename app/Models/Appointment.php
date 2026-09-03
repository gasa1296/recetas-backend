<?php

namespace App\Models;

use Database\Factories\AppointmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

#[Fillable([
    'user_id',
    'patient_id',
    'room_id',
    'specialty_id',
    'starts_at',
    'ends_at',
    'reason',
    'status',
    'notes',
    'reminder_channel',
    'reminder_enabled',
    'reminder_sent_at',
])]
class Appointment extends Model
{
    /** @use HasFactory<AppointmentFactory> */
    use HasFactory, SoftDeletes;

    protected $attributes = [
        'status' => self::STATUS_SCHEDULED,
        'reminder_channel' => 'email',
        'reminder_enabled' => true,
    ];

    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_IN_WAITING_ROOM = 'in_waiting_room';
    public const STATUS_IN_CONSULTATION = 'in_consultation';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_NO_SHOW = 'no_show';

    public const VALID_STATUSES = [
        self::STATUS_SCHEDULED,
        self::STATUS_CONFIRMED,
        self::STATUS_IN_WAITING_ROOM,
        self::STATUS_IN_CONSULTATION,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED,
        self::STATUS_NO_SHOW,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'reminder_sent_at' => 'datetime',
            'reminder_enabled' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    /**
     * Scope a query to check for overlapping active appointments.
     */
    public function scopeOverlapping(Builder $query, $startsAt, $endsAt, $excludeId = null): Builder
    {
        $start = Carbon::parse($startsAt)->format('Y-m-d H:i:s');
        $end = Carbon::parse($endsAt)->format('Y-m-d H:i:s');

        return $query->where('status', '!=', self::STATUS_CANCELLED)
            ->where(function (Builder $q) use ($start, $end) {
                $q->where('starts_at', '<', $end)
                    ->where('ends_at', '>', $start);
            })
            ->when($excludeId, fn (Builder $q) => $q->where('id', '!=', $excludeId));
    }

    /**
     * Scope a query to find upcoming appointments needing reminder.
     */
    public function scopeUpcomingReminders(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_SCHEDULED, self::STATUS_CONFIRMED])
            ->where('reminder_enabled', true)
            ->whereNull('reminder_sent_at')
            ->whereBetween('starts_at', [now(), now()->addDay()]);
    }
}
