<?php

namespace App\Actions\Auth;

use App\Contracts\CertificateManagerInterface;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterMedicAction
{
    public function __construct(
        protected CertificateManagerInterface $certificateManager
    ) {}

    /**
     * Execute the registration of a new medic with all associated resources.
     *
     * @param  array<string, mixed>  $inputs  Validated input data
     */
    public function execute(array $inputs): User
    {
        return DB::transaction(function () use ($inputs) {
            $specialtyData = $inputs['specialty'] ?? null;
            $roomData = $inputs['room'] ?? null;
            $savedSignature = $inputs['saved_signature'] ?? null;

            $user = User::create([
                'first_name' => $inputs['first_name'],
                'last_name' => $inputs['last_name'],
                'identification' => $inputs['identification'],
                'email' => $inputs['email'],
                'phone' => $inputs['phone'] ?? null,
                'password' => Hash::make($inputs['password']),
                'signature_hash' => hash('sha256', Str::random(64)),
                'saved_signature' => $savedSignature,
                'email_verified_at' => now(),
            ]);

            $user->assignRole('medic');

            $cert = $this->certificateManager->generateForUser($user);

            $user->update([
                'certificate_path' => $cert['certificate_path'],
                'certificate_key_path' => $cert['key_path'],
                'certificate_expires_at' => $cert['expires_at'],
            ]);

            if ($specialtyData) {
                $user->specialty()->create($specialtyData);
            }

            if ($roomData && ! empty($roomData['name'])) {
                $roomPhone = $roomData['phone'] ?? null;
                if (! is_array($roomPhone)) {
                    $roomPhone = $roomPhone ? [$roomPhone] : ($user->phone ?? ['000-000-0000']);
                }

                $user->rooms()->create([
                    'name' => $roomData['name'],
                    'identification' => $roomData['identification'] ?? $user->identification,
                    'zip' => $roomData['zip'] ?? '1010',
                    'address' => $roomData['address'] ?? 'Consultorio Principal',
                    'phone' => $roomPhone,
                ]);
            }

            return $user;
        });
    }
}
