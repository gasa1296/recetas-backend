<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\LoginResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
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
        if ($user->suspended_at) {
            return $this->error(__('messages.auth.user_suspended'), [], 403);
        }
        $user->load(['rooms', 'specialties']);

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
