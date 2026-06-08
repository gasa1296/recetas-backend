<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetRequest;
use App\Http\Requests\ResetRequestRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;

class ResetController extends Controller
{
    public function request(ResetRequestRequest $request): JsonResponse
    {
        ResetPasswordNotification::createUrlUsing(function (
            $notifiable,
            $token,
        ) {
            $frontend = config('frontend.frontend_url') ?: config('app.url');

            return rtrim($frontend, '/').
                '/reset-password?token='.
                $token.
                '&email='.
                urlencode($notifiable->getEmailForPasswordReset());
        });

        $status = Password::sendResetLink($request->only('email'));
        if ($status !== Password::RESET_LINK_SENT) {
            return $this->error(
                __('messages.reset.link_sent_failed'),
                ['email' => __($status)],
                400,
            );
        }

        return $this->success(__('messages.operation_success'));
    }

    public function reset(ResetRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->only(
                'email',
                'password',
                'password_confirmation',
                'token',
            ),
            function (User $user, string $password) {
                $user->fill([
                    'password' => Hash::make($password),
                ]);

                $user->save();

                event(new PasswordReset($user));
            },
        );
        if ($status !== Password::PASSWORD_RESET) {
            return $this->error(__('messages.reset.failed'));
        }
        $user = User::where('email', $request->email)->first();
        $user->tokens()->delete();

        return $this->success(__('messages.operation_success'));
    }
}
