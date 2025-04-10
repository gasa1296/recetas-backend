<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\{ClientException, ServerException};

class ResetController extends Controller
{
    private Client $client;
    public function __construct()
    {
        $this->client = new Client(['verify' => env('VERIFY_FILE', false)]);
    }
    public function request(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );
        if ($status !== Password::RESET_LINK_SENT) {
            response()->json(['email' => __($status)], 400);
        }
        return response()->json();
    }
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ]);

                $user->save();

                event(new PasswordReset($user));
            }
        );
        if ($status !== Password::PASSWORD_RESET) {
            response()->json(['email' => __($status)], 400);
        }
    }
    public function resetPasswordMagento(Request $request)
    {
        try {
            $res = $this->client->post(env('MAGENTO_URL') . '/ic/api/integration/v1/flows/rest/RESETPASSWORDMAGENTO/1.0/app_resetpwd', [
                'auth' => [
                    'rx_user_dev',
                    'Farmacos2020dev'
                ],
                'json' => [
                    'login' => $request->email,
                ]
            ]);
            $decodedRes = json_decode($res->getBody(), true);
            return response()->json($decodedRes, $res->getStatusCode());
        } catch (ClientException | ServerException$e) {
            $response = $e->getResponse();
            $decodedRes = json_decode($response->getBody(), true);
            return response()->json($decodedRes, $$response->getStatusCode());
        }
    }
}
