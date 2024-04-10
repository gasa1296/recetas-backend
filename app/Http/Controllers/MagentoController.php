<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use GuzzleHttp\Exception\{ClientException, ServerException};

class MagentoController extends Controller
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
    public function verifyFESA(string $fesa): bool
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
    public function registerMagento(Request $request): JsonResponse
    {
        return $this->registerMagentoRepo($request->all());
    }
    public function registerMagentoRepo(array $inputs): JsonResponse
    {
        $req = [
            'idContact' => $inputs['idCX'],
            'email' => $inputs['email'],
            'firstname' => $inputs['first_name'],
            'lastname' => $inputs['last_name1'],
            'middleName' => $inputs['last_name2'],
            'password' => $inputs['password'],
            'gender' => $inputs['gender'],
            "typeUsage" => "Celular"

        ];
        if (strtoupper($inputs['gender']) == 'M') {
            $req['gender'] = 'Masculino';
        } elseif (strtoupper($inputs['gender']) == 'F') {
            $req['gender'] = 'Femenino';
        } else {
            $req['gender'] = 'Indefinido';
        }
        if (!empty($inputs['phones'])) {
            $req['phone'] = $inputs['phones'][0]['phone'];
        }
        try {
            $res = $this->client->post($this->magentoUrl . '/ic/api/integration/v1/flows/rest/CREATEPROFILEMAGENTO/1.0/magento/profile', [
                'auth' => $this->magentoAuth,
                'json' => $req
            ]);
            $decodedRes = json_decode($res->getBody(), true);
            if ($decodedRes['success']) {
                return response()->json($decodedRes);
            } else {
                return response()->json($decodedRes, 400);
            }
        } catch (ClientException | ServerException $e) {
            return response()->json(json_decode($e->getResponse()->getBody(), true) + ['magento', 'req' => json_encode($req)], $e->getResponse()->getStatusCode());
        }
    }
    public function registerCX(Request $request): JsonResponse
    {
        $inputs = $request->all();
        if (strtoupper($inputs['gender']) == 'M') {
            $inputs['gender'] = 'Masculino';
        } elseif (strtoupper($inputs['gender']) == 'F') {
            $inputs['gender'] = 'Femenino';
        } else {
            $inputs['gender'] = 'Indefinido';
        }
        try {
            $res = $this->client->post('https://cxoicdevcc-idxyuubrquuo-ia.integration.ocp.oraclecloud.com/ic/api/integration/v1/flows/rest/GESTIONCLIENTEREST/1.0/gestionClienteRest', [
                'auth' => $this->magentoAuth,
                'json' => [
                    [
                        "origen" => "Receta Medica Electronica",
                        "nombre" => $inputs['first_name'],
                        "apellidoPaterno" => $inputs['last_name1'],
                        "apellidoMaterno" => $inputs['last_name2'],
                        "canalInscripcion" => "Interface",
                        "correoElectronico" => $inputs['email'],
                        "sexo" => $inputs['gender'],
                        "segmento" => "Mostrador",
                        "unidadOperativa" => "FESA",
                        "sistemaPOS" => "Seus",
                        "status" => "Activo",
                        "tipo" => "Medico",
                        'listaCedulas' => array_map(function ($esp) {
                            return [
                                'cedulaProfesional' => $esp['identification'],
                                'especialidad' => $esp['name']
                            ];
                        }, $inputs['specializations'])
                    ],
                ]
            ]);
            $decodedRes = json_decode($res->getBody(), true);
            return response()->json($decodedRes);
        } catch (ClientException | ServerException $e) {
            return response()->json(json_decode($e->getResponse()->getBody(), true) + ['CX'], $e->getResponse()->getStatusCode());
        }
    }
    public function updateMagento(Request $request): JsonResponse
    {
        $inputs = $request->all();
        if (strtoupper($inputs['gender']) == 'M') {
            $inputs['gender'] == 'Masculino';
        } elseif (strtoupper($inputs['gender']) == 'F') {
            $inputs['gender'] == 'Femenino';
        } else {
            $inputs['gender'] == 'Indefinido';
        }
        try {
            $res = $this->client->post($this->magentoUrl . '/ic/api/integration/v1/flows/rest/UPDATEPROFILEMAGENTO/1.0/updateprofile', [
                'auth' => $this->magentoAuth,
                'json' => [
                    'idContact' => $inputs['idCX'],
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
    public function generateMagentoToken(Request $request): JsonResponse
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
    public function getUserByTokenMagento(Request $request): JsonResponse
    {
        try {
            $res = $this->client->get('https://mcstaging.farmaciasespecializadas.com/rest/V1/customers/me', [
                'headers' => [
                    'Authorization' => "Bearer $request->token",
                ],
            ]);
            $decodedRes = json_decode($res->getBody(), true);
            $instance = User::where('email', '=', $decodedRes['email'])->first();
            if ($instance) {
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
