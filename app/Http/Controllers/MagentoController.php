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
            $res = $this->client->get(env('URL_MEDIC'), [
                'auth' => $this->magentoAuth,
                'query' => $request->only('email', 'cedula', 'telefono')
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
            $res = $this->client->post(env('URL_FESA'), [
                'auth' => $this->magentoAuth,
                'json' => ['codigoMedico' => $fesa]
            ]);
            $decodedRes = json_decode($res->getBody(), true);
            if (in_array($decodedRes['codigo'], [8001, 200])) {
                return true;
            } else {
                return false;
            }
        } catch (ClientException $e) {
            return false;
        }
    }
    public function store(array $inputs): JsonResponse
    {
        try {
            $res = $this->client->post(env('URL_REGISTER_MAGENTO'), [
                'auth' => $this->magentoAuth,
                'json' => [
                    'idContact' => $inputs['idCX'] ?? '',
                    'email' => $inputs['email'] ?? '',
                    'firstname' => $inputs['first_name'] ?? '',
                    'lastname' => $inputs['last_name1'] ?? '',
                    'middleName' => $inputs['last_name2'] ?? '',
                    'gender' => $this->setGender($inputs['gender']),
                    'password' => $inputs['password'] ?? '',
                    'phone' => json_decode($inputs['phone1'], true)[0]['phone'],
                    'typeUsage' => 'Celular'
                ]
            ]);
            $decodedRes = json_decode($res->getBody(), true);
            if (!empty($decodedRes['success'])) {
                return response()->json($decodedRes);
            } else {
                return response()->json($decodedRes, 400);
            }
        } catch (ClientException | ServerException $e) {
            return response()->json(json_decode($e->getResponse()->getBody(), true) + ['magento', 'req' => json_encode($inputs)], $e->getResponse()->getStatusCode());
        }
    }
    public function CX(array $inputs): JsonResponse
    {
        try {
            $res = $this->client->post(env('URL_REGISTER_CX'), [
                'auth' => $this->magentoAuth,
                'json' => [
                    [
                        "origen" => "Receta Medica Electronica",
                        "nombre" => $inputs['first_name'] ?? '',
                        "apellidoPaterno" => $inputs['last_name1'] ?? '',
                        "apellidoMaterno" => $inputs['last_name2'] ?? '',
                        "canalInscripcion" => "Receta Medica Electronica",
                        "correoElectronico" => $inputs['email'] ?? '',
                        "sexo" => $this->setGender($inputs['gender']),
                        "segmento" => "Mostrador",
                        "unidadOperativa" => "FESA",
                        "status" => "Activo",
                        "tipo" => "Medico",
                        "clienteEcommerce" => 'Si',
                        'listaCedulas' => array_map(function ($instance) {
                            return [
                                'id' => $instance['id_ext'] ?? '',
                                'cedulaProfesional' => $instance['identification'] ?? '',
                                'especialidad' => $instance['name'] ?? '',
                            ];
                        }, $inputs['specializations'] ?? []),
                        'listaTelefono' => array_map(function ($instance) {
                            return [
                                'id' => $instance['id_ext'] ?? '',
                                'numeroTelefonico' => $instance['phone'] ?? '',
                                'tipoDeUso' => 'Celular',
                                'status' => 'Activo'
                            ];
                        }, json_decode($inputs['phone1'], true)),
                        'listaDireccion' => array_map(function ($instance) {
                            return [
                                'id' => $instance['id_ext'] ?? '',
                                "calle" => $instance['street'] ?? '',
                                "numeroExterior" => $instance['n_exterior'] ?? '',
                                "numeroInterior" => $instance['n_interior'] ?? '',
                                "colonia" => $instance['colony'],
                                "delegacionMunicipio" => $instance['delegation'] ?? '',
                                "ciudad" => $instance['delegation'] ?? '',
                                "estado" => $instance['state'] ?? '',
                                "codigoPostal" => $instance['zip'] ?? '',
                                "pais" => "MX",
                                "tipo" => "Consultorio",
                                "estatus" => "Activo"
                            ];
                        }, $inputs['rooms'] ?? []),
                    ],
                ]
            ]);
            $decodedRes = json_decode($res->getBody(), true);
            return response()->json($decodedRes);
        } catch (ClientException | ServerException $e) {
            return response()->json(json_decode($e->getResponse()->getBody(), true) + ['CX'], $e->getResponse()->getStatusCode());
        }
    }
    public function update(array $inputs): JsonResponse
    {
        try {
            $res = $this->client->post(env('URL_UPDATE_MAGENTO'), [
                'auth' => $this->magentoAuth,
                'json' => [
                    'idContact' => $inputs['idCX'] ?? '',
                    'email' => $inputs['email'] ?? '',
                    'nombre' => $inputs['first_name'] ?? '',
                    'apellidoPaterno' => $inputs['last_name1'] ?? '',
                    'apellidoMaterno' => $inputs['last_name2'] ?? '',
                    'sexo' => $this->setGender($inputs['gender']),
                    'TelefonoPrincipal' => $inputs['phone1'] ?? '',
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
    public function getToken(Request $request): JsonResponse
    {
        $inputs = $request->only('email', 'password');
        try {
            $res = $this->client->post(env('URL_GEN_MAGENTO_TOKEN'), [
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
    public function getUser(Request $request): JsonResponse
    {
        try {
            $res = $this->client->get(env('URL_VER_MAGENTO_TOKEN'), [
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
    public function getSpecialization(): JsonResponse
    {
        try {
            $res = $this->client->get('https://rnowgrupofarmacos--tst1.custhelp.com/services/rest/connect/v1.4/queryResults/?query=select', [
                'auth' => [
                    'OICUSER2',
                    'iCSUSER2018'
                ],
            ]);
            $decodedRes = json_decode($res->getBody(), true);
            return response()->json($decodedRes);
        } catch (ClientException | ServerException $e) {
            return response()->json(json_decode($e->getResponse()->getBody(), true), $e->getResponse()->getStatusCode());
        }
    }
    public function getStates(): JsonResponse
    {
        try {
            $res = $this->client->get('https://rnowgrupofarmacos--tst1.custhelp.com/services/rest/connect/v1.4/queryResults/?query=select', [
                'auth' => [
                    'OICUSER2',
                    'iCSUSER2018'
                ],
            ]);
            $decodedRes = json_decode($res->getBody(), true);
            return response()->json($decodedRes);
        } catch (ClientException | ServerException $e) {
            return response()->json(json_decode($e->getResponse()->getBody(), true), $e->getResponse()->getStatusCode());
        }
    }
    private function setGender($gender)
    {
        if (strtoupper($gender) == 'M') {
            return 'Masculino';
        } elseif (strtoupper($gender) == 'F') {
            return 'Femenino';
        } else {
            return 'Indefinido';
        }
    }
}
