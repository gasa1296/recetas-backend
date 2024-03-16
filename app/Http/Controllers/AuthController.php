<?php

namespace App\Http\Controllers;

use App\Models\ConsultingRoom;
use App\Models\Specialization;
use GuzzleHttp\Exception\ServerException;
use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class AuthController extends Controller
{
    private array $magentoAuth;
    private string $magentoUrl;
    private Client $client;
    public function __construct()
    {
        $this->magentoUrl = env('MAGENTO_URL');
        $this->magentoAuth = [
            env('MAGENTO_USER'),
            env('MAGENTO_PASSWORD')
        ];
        $this->client = new Client(['verify' => env('VERIFY_FILE', false)]);
    }
    public function login(Request $request): JsonResponse
    {
        $instance = User::where('email', request()->email)->first();
        $okResponse = [
            'token' => $instance->createToken('recipe')->plainTextToken,
            'user' => $instance,
        ];
        if (empty($instance)) {
            $magentoToken = $this->generateMagentoToken($request);
            if ($magentoToken->getStatusCode() < 300) {
                return response()->json([
                    'recetasUser' => false,
                    'magentoEmail' => $request->email
                ]);
            }
            return response()->json([['email' => __('email incorrecto')]], 404);
        }
        if (Hash::check(request()->password, $instance->password)) {
            return response()->json($okResponse);
        }
        $magentoToken = $this->generateMagentoToken($request);
        if ($magentoToken->getStatusCode() < 300) {
            return response()->json($okResponse);
        }
        return response()->json([['password' => __('contraseña incorrecta')]], 404);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string'],
            'last_name1' => ['required', 'string'],
            'last_name2' => ['nullable', 'string'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'string'],
            'phone1' => ['nullable', 'string'],
            'phone2' => ['nullable', 'string'],
            'gender' => ['required', 'string'],
            'fesa' => ['required',],
            'rooms' => ['required', 'array'],
            'specializations' => ['required', 'array'],
            'rooms.*.name' => ['required', 'string'],
            'rooms.*.zip' => ['required', 'string'],
            'rooms.*.street' => ['required', 'string'],
            'rooms.*.colony' => ['required', 'string'],
            'rooms.*.state' => ['required', 'string'],
            'rooms.*.delegation' => ['required', 'string'],
            'rooms.*.n_exterior' => ['required',],
            'rooms.*.n_interior' => ['nullable',],
            'rooms.*.address' => ['nullable', 'string'],
            'rooms.*.phone' => ['nullable', 'string'],
            'rooms.*.design' => ['nullable', 'string'],
            'specializations.*.name' => ['required', 'string'],
            'specializations.*.identification' => ['required', 'unique:specializations'],
            'specializations.*.university' => ['nullable', 'string'],
            'logo_room' => ['nullable', 'array'],
            'logo_spec' => ['nullable', 'array'],
            'logo_room.*' => ['nullable', 'file', 'mimes:jpg,png'],
            'logo_spec.*' => ['nullable', 'file', 'mimes:jpg,png'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $inputs = $validator->safe()->all();
        if(!$this->verifyFESA($inputs['fesa'])) {
            return response()->json(['fesa' => 'Codigo de FESA invalido'], 400);
        }
        $instance = User::create($inputs);
        /*
        $medicMagento = $this->getMedic($request)->getData(true);
        if ($medicMagento['results'] == 0) {
            $res = $this->registerMagento($request);
        }*/
        event(new Registered($instance));
        foreach ($inputs['rooms'] as $key => $el) {
            if (!empty($request->file('logo_room')[$key])) {
                $file = $request->file('logo_room')[$key]->store('medics/' . $instance->id, 'public');
                $el['logo'] = $file;
            }
            $el['user_id'] = $instance->id;
            ConsultingRoom::create($el);
        }
        foreach ($inputs['specializations'] as $key => $el) {
            if (!empty($request->file('logo_spec')[$key])) {
                $file = $request->file('logo_spec')[$key]->store('medics/' . $instance->id, 'public');
                $el['logo'] = $file;
            }
            $el['user_id'] = $instance->id;
            Specialization::create($el);
        }

        return response()->json();
    }
    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        return response()->json($request->user());
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string'],
            'last_name1' => ['required', 'string'],
            'last_name2' => ['nullable', 'string'],
            'email' => ['required', 'email'],
            'password' => ['nullable', 'string'],
            'phone1' => ['nullable', 'string'],
            'phone2' => ['nullable', 'string'],
            'gender' => ['required', 'string'],
            'fesa' => ['required',],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $instance = auth()->user();
        $inputs = $validator->safe()->all();
        if(!empty($inputs['password'])) {
            $inputs['password'] = Hash::make($inputs['password']);
        }
        $instance->update($inputs);
        return response()->json($instance);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json();
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(): JsonResponse
    {
        auth()->user()->delete();
        return response()->json();
    }
    /**
     * Display the medic data if exist.
     */
    public function getMedic(Request $request): JsonResponse
    {
        try {
            $res = $this->client->get('https://cxoicdevcc-idxyuubrquuo-ia.integration.ocp.oraclecloud.com:443/ic/api/integration/v1/flows/rest/CONSULTACONTACTOREST/1.0/consultacontacto', [
                'auth' => $this->magentoAuth,
                'query' => $request->only('email', 'cedula')
            ]);
            $decodedRes = json_decode($res->getBody(), true);
            return response()->json($decodedRes);
        } catch (ClientException | ServerException $e) {
            return response()->json(json_decode($e->getResponse()->getBody(), true), $e->getResponse()->getStatusCode());
        }
    }
    /**
     * verify fesa code.
     */
    private function verifyFESA(String $fesa): bool
    {
        try {
            $res = $this->client->post('https://cxoicdevapp-idxyuubrquuo-ia.integration.ocp.oraclecloud.com:443/ic/api/integration/v1/flows/rest/VALIDARCODIGOMEDICO/1.0/medico/codigo', [
                'auth' => $this->magentoAuth,
                'json' => ['codigoMedico' => $fesa]
            ]);
            $decodedRes = json_decode($res->getBody(), true);
            if ($decodedRes['codigo'] == 8001) {
                return true;
            } else {
                return false;
            }
        } catch (ClientException $e) {
            return false;
        }
    }
    public function registerMagento(Request $request)
    {
        $inputs = $request->all();
        try {
            $res = $this->client->post($this->magentoUrl . '/ic/api/integration/v1/flows/rest/CREATEPROFILEMAGENTO/1.0/magento/profile', [
                'auth' => $this->magentoAuth,
                'json' => [
                    'email' => $inputs['email'],
                    'firstname' => $inputs['first_name'],
                    'lastname' => $inputs['last_name1'],
                    'middleName' => $inputs['last_name2'],
                    'password' => $inputs['password'],
                    'gender' => $inputs['gender'],
                    'phone' => $inputs['phone1'],
                    "typeUsage" => "Celular"

                ]
            ]);
            $decodedRes = json_decode($res->getBody(), true);
            if ($decodedRes['success']) {
            } else {
                return response()->json($decodedRes, 400);
            }
        } catch (ClientException | ServerException $e) {
            return response()->json(json_decode($e->getResponse()->getBody(), true), $e->getResponse()->getStatusCode());
        }
    }
    public function updateMagento(Request $request)
    {
        $inputs = $request->all();
        try {
            $res = $this->client->post($this->magentoUrl . '/ic/api/integration/v1/flows/rest/UPDATEPROFILEMAGENTO/1.0/updateprofile', [
                'auth' => $this->magentoAuth,
                'json' => [
                    'email' => $inputs['email'],
                    'nombre' => $inputs['first_name'],
                    'apellidoPaterno' => $inputs['last_name1'],
                    'apellidoMaterno' => $inputs['last_name2'],
                    'sexo' => $inputs['gender'],
                    'TelefonoPrincipal' => $inputs['phone1'],
                ]
            ]);
            $decodedRes = json_decode($res->getBody(), true);
            if ($decodedRes['success']) {
                return response()->json($decodedRes);
            } else {
                return response()->json($decodedRes, 400);
            }
        } catch (ClientException | ServerException $e) {
            return response()->json(json_decode($e->getResponse()->getBody(), true), $e->getResponse()->getStatusCode());
        }
    }
    public function generateMagentoToken(Request $request)
    {
        $inputs = $request->only('email', 'password');
        try {
            $res = $this->client->post('https://mcstaging.farmaciasespecializadas.com/rest/V1/integration/customer/token', [
                'auth' => $this->magentoAuth,
                'json' => [
                    'username' => $inputs['email'],
                    'password' => $inputs['password'],
                ]
            ]);
            $decodedRes = json_decode($res->getBody(), true);
            return response()->json($decodedRes);
        } catch (ClientException | ServerException $e) {
            return response()->json(json_decode($e->getResponse()->getBody(), true), $e->getResponse()->getStatusCode());
        }
    }
    public function getUserByTokenMagento(Request $request)
    {
        try {
            $res = $this->client->get('https://mcstaging.farmaciasespecializadas.com/rest/V1/customers/me', [
                'headers' => [
                    'Authorization' => "Bearer $request->token",
                ],
            ]);
            $decodedRes = json_decode($res->getBody(), true);
            $instance = User::where('email', '=', $decodedRes['email'])->first();
            if ($instance){
                return response()->json([
                    'token' => $instance->createToken('recipe')->plainTextToken,
                    'user' => $instance,
                ]);
            } else {
                return response()->json([
                    'recetasUser' => false,
                    'magentoEmail' => $decodedRes['email']
                ]);
            }
        } catch (ClientException | ServerException $e) {
            return response()->json(json_decode($e->getResponse()->getBody(), true), $e->getResponse()->getStatusCode());
        }
    }
}
