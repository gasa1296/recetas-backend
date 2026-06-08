<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


#[Fillable([
    'first_name',
    'last_name1',
    'last_name2',
    'phone',
    'gender',
    'email',
    'password',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'phone' => 'array',
        ];
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function specialties(): HasMany
    {
        return $this->hasMany(Specialty::class);
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }
}
