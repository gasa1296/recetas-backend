<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage)
                ->subject('Verificacion de usuario')
                ->markdown('mail.email', [
                    'message' => 'Para comenzar, es importante que verifiques tu cuenta haciendo clic en el boton a continuacion',
                    'title' => 'Verificacion de usuario',
                    'url' => $url,
                    'button' => 'Verificar cuenta'
                ]);
        });
        ResetPassword::toMailUsing(function ($notifiable, $token) {
            $url = route('password.reset', $token) . '?email=' . $notifiable->getEmailForPasswordReset();
            return (new MailMessage())
                ->subject('Restablecer contraseña')
                ->markdown('mail.email', [
                    'message' => 'Recibimos una solicitud para restablecer la contraseña de su cuenta. Si realizó esta solicitud, haga clic en el siguiente enlace para cambiar su contraseña:',
                    'title' => 'Restablecer contraseña',
                    'url' => $url,
                    'button' => 'Restablecer contraseña'
                ]);
        });
    }
}
