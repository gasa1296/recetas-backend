<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $inputs = $request->validated();

        if (! auth()->attempt($inputs)) {
            return response()->json([
                'message' => 'Credenciales incorrectas',
            ], 401);
        }
        $user = auth()->user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $inputs = $request->validated();

        $user = User::create($inputs);

        event(new Registered($user));

        return response()->json([
            'message' => 'Usuario registrado correctamente',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        return response()->json(auth()->user());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request): JsonResponse
    {
        $user = auth()->user();
        $inputs = $request->validated();
        if (! empty($inputs['password'])) {
            $inputs['password'] = Hash::make($inputs['password']);
        }
        $user->update($inputs);
        $user = $user->fresh();

        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function logout(Request $request): JsonResponse
    {
        $user = auth()->user();
        $user->currentAccessToken()->delete();

        return response()->json();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(): JsonResponse
    {
        $user = auth()->user();
        $user->delete();

        return response()->json();
    }
}
