<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\LoginResource;
use App\Models\User;
use App\Services\CertificateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $inputs = $request->validated();

        $user = DB::transaction(function () use ($inputs) {
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

            $certService = app(CertificateService::class);
            $cert = $certService->generateForUser($user);

            $user->update([
                'certificate_path' => $cert['certificate_path'],
                'certificate_key_path' => $cert['key_path'],
                'certificate_expires_at' => $cert['expires_at'],
            ]);

            if ($specialtyData) {
                $user->specialty()->create($specialtyData);
            }

            if ($roomData && !empty($roomData['name'])) {
                $roomPhone = $roomData['phone'] ?? null;
                if (!is_array($roomPhone)) {
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

        $user->load(['rooms', 'specialty']);

        return $this->success(
            __('messages.auth.client_register_success'),
            new LoginResource($user),
            201,
        );
    }
    public function login(LoginRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        if (! Auth::attempt($inputs)) {
            return $this->error(
                __('messages.auth.invalid_credentials'),
                [],
                401,
            );
        }
        $user = auth()->user();
        $user->load(['rooms', 'specialty']);

        return $this->success(
            __('messages.auth.client_login_success'),
            new LoginResource($user),
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $user = auth()->user();
        $user->currentAccessToken()->delete();

        return $this->success(__('messages.auth.client_logout_success'));
    }
}
