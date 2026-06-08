<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify(Request $request)
    {
        if (! $request->hasValidSignature()) {
            return $this->error(
                __('messages.verification.verification_expired'),
                400,
            );
        }

        $user = User::findOrFail($request->user_id);

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return $this->success(
            __('messages.operation_success'),
        );
    }

    public function resend(Request $request)
    {
        $email = $request->input('email');
        if (! $email) {
            return $this->error(
                __('messages.verification.email_required'),
                400,
            );
        }
        $user = User::where('email', $email)->firstOrFail();
        if ($user->hasVerifiedEmail()) {
            return $this->error(
                __('messages.verification.already_verified'),
                400,
            );
        }

        $user->sendEmailVerificationNotification();

        return $this->success(__('messages.verification.link_sent'));
    }

    public function notice(): JsonResponse
    {
        return response()->json([
            'user' => __('messages.verification.user_not_verified'),
        ]);
    }
}
