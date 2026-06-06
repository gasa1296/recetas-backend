<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'first_name',
        'last_name1',
        'last_name2',
        'phone',
        'gender',
        'fesa',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'email_verified_at',
        'password',
        'is_admin',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin;
    }

    /**
     * Get the consulting rooms of the medics.
     */
    public function fullName(): string
    {
        return "$this->first_name $this->last_name1 $this->last_name2";
    }

    /**
     * Get the consulting rooms of the medics.
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(ConsultingRoom::class);
    }

    /**
     * Get the consulting rooms of the medics.
     */
    public function specializations(): HasMany
    {
        return $this->hasMany(Specialization::class);
    }

    /**
     * Get the prescriptions of the medics.
     */
    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    /**
     * Get the prescriptions of the medics.
     */
    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }
}
