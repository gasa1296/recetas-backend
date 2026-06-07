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
            // temporal url
            return $this->error(
                __('messages.verification.verification_expired'),
                400,
            );
        }

        $user = User::find($request->user_id);
        if (! $user) {
            return $this->error(
                __('messages.verification.user_not_found'),
                400,
            );
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        // temporal url
        return $this->success(
            __('messages.verification.verified_successfully'),
        );
    }

    public function resend(Request $request)
    {
        $email = $request->get('email');
        if (! $email) {
            return $this->error(
                __('messages.verification.email_required'),
                400,
            );
        }
        $user = User::where('email', $email)->first();
        if (! $user) {
            return $this->error(
                __('messages.verification.user_not_found'),
                400,
            );
        }
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
