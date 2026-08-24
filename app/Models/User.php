<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

#[Fillable([
    'first_name',
    'last_name',
    'identification',
    'phone',
    'email',
    'password',
    'signature_hash',
    'certificate_path',
    'certificate_key_path',
    'certificate_expires_at',
])]
#[Hidden(['password', 'remember_token', 'signature_hash', 'certificate_path', 'certificate_key_path'])]
class User extends Authenticatable implements FilamentUser
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

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin;
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function specialty(): HasOne
    {
        return $this->hasOne(Specialty::class);
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function prescriptionTemplates(): HasMany
    {
        return $this->hasMany(PrescriptionTemplate::class);
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['first_name'].' '.$attributes['last_name'],
        );
    }

    public function hasValidCertificate(): bool
    {
        if (is_null($this->certificate_path) || is_null($this->certificate_key_path)) {
            return false;
        }

        $certPath = Storage::disk('local')->path($this->certificate_path);
        $keyPath = Storage::disk('local')->path($this->certificate_key_path);

        if (! file_exists($certPath) || ! file_exists($keyPath)) {
            return false;
        }

        if (! is_null($this->certificate_expires_at)) {
            return Carbon::parse($this->certificate_expires_at)->isFuture();
        }

        return true;
    }

    public function getCertificatePath(): string
    {
        return Storage::disk('local')->path($this->certificate_path);
    }

    public function getCertificateKeyPath(): string
    {
        return Storage::disk('local')->path($this->certificate_key_path);
    }
}
