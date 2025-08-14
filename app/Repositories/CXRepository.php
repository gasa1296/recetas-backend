<?php

namespace App\Repositories;

use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\JsonResponse;
use GuzzleHttp\Exception\{ClientException, ServerException};
use App\Repositories\Interfaces\CXrepositoryInterface;
use Illuminate\Support\Facades\Log;

class CXRepository implements CXrepositoryInterface
{
    private array $magentoAuth;
    private array $medicamentAuth;
    private Client $client;
    public function __construct()
    {
        $this->magentoAuth = [
            env('MAGENTO_USER'),
            env('MAGENTO_PASSWORD')
        ];
        $this->medicamentAuth = [
            env('MEDICAMENT_USER'),
            env('MEDICAMENT_PASS')
        ];
        $this->client = new Client(['verify' => env('VERIFY_FILE', false)]);
    }
    private function catchError(callable $callback): JsonResponse
    {
        try {
            return $callback();
        } catch (ClientException | ServerException $e) {
            $res = $e->getResponse();
            $decodedRes = json_decode($res->getBody(), true);
            $statusCode = $res->getStatusCode();
            Log::error('CXRepository error: ' . $e->getMessage(), [
                'response' => $decodedRes,
                'status_code' => $statusCode,
            ]);
            return response()->json($decodedRes, $statusCode);
        }
    }
    public function CX(array $inputs): JsonResponse
    {
        try {
            $json = [
                [
                    'idCX' => $inputs['idCX'] ?? '',
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
            ];
            $res = $this->client->post(env('URL_REGISTER_CX'), [
                'auth' => $this->magentoAuth,
                'json' => $json
            ]);
            $decodedRes = json_decode($res->getBody(), true);
            return response()->json($decodedRes);
        } catch (ClientException | ServerException $e) {
            return response()->json(json_decode($e->getResponse()->getBody(), true) + ['CX'], $e->getResponse()->getStatusCode());
        }
    }
    public function getMedic(array $inputs): JsonResponse
    {
        $inputsNew = [];
        foreach ($inputs as $key => $value) {
            if (!empty($value)) {
                $inputsNew[$key] = strtolower($value);
            }
        }
        return $this->catchError(function () use ($inputsNew) {
            $res = $this->client->get(env('URL_MEDIC'), [
                'auth' => $this->magentoAuth,
                'query' => $inputsNew
            ]);
            $decodedRes = json_decode($res->getBody(), true);
            return response()->json($decodedRes);
        });
    }
    public function medicAffiliation(array $inputs): JsonResponse
    {
        return $this->catchError(function () use ($inputs) {
            $res = $this->client->post(env('URL_AFFILIATION'), [
                'auth' => $this->magentoAuth,
                'json' => [
                    "idPrograma" => "609",
                    "idEmbajador" => $inputs['fesa'],
                    "folio" => $inputs['fesa'],
                    "canal" => "eCommerce",
                    "idExternoContact" => $inputs['idCX']
                ]
            ]);
            return response()->json(json_decode($res->getBody(), true));
        });
    }
    public function verifyAffiliation(string $fesa): bool
    {
        try {
            $res = $this->client->get(env('URL_VERIFY_FESA') . $fesa);
            $decodedRes = json_decode($res->getBody(), true);
            return $decodedRes['correcto'];
        } catch (ClientException | ServerException $e) {
            return false;
        }
    }
    public function registerFesaCode(array $inputs): JsonResponse
    {
        return $this->catchError(function () use ($inputs) {
            $res = $this->client->post(env('URL_REGISTER_FESA'), [
                'auth' => $this->magentoAuth,
                'json' => [
                    "codigoMedico" => $inputs['fesa'],
                    "correoElectronico" => $inputs['email'],
                ]
            ]);
            return response()->json(json_decode($res->getBody(), true));
        });
    }
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
        } catch (ClientException | ServerException $e) {
            return false;
        }
    }
    public function getMedicaments(array $inputs): JsonResponse
    {
        return $this->catchError(function () use ($inputs) {
            $res = $this->client->post(env('URL_MEDICAMENTS'), [
                'auth' => $this->medicamentAuth,
                'json' => ['products' => $inputs['products']]
            ]);
            return response()->json(json_decode($res->getBody(), true));
        });
    }
    public function magentoStore(array $inputs): JsonResponse
    {
        return $this->catchError(function () use ($inputs) {
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
        });
    }
    public function magentoUpdate(array $inputs): JsonResponse
    {
        return $this->catchError(function () use ($inputs) {
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
        });
    }
    public function getToken(array $inputs): JsonResponse
    {
        return $this->catchError(function () use ($inputs) {
            $res = $this->client->post(env('URL_GEN_MAGENTO_TOKEN'), [
                'auth' => $this->magentoAuth,
                'json' => [
                    'username' => $inputs['email'],
                    'password' => $inputs['password'],
                ]
            ]);
            $decodedRes = json_decode($res->getBody(), true);
            return response()->json($decodedRes);
        });
    }
    public function getUserByToken(string $token): JsonResponse
    {
        return $this->catchError(function () use ($token) {
            $res = $this->client->get(env('URL_VER_MAGENTO_TOKEN'), [
                'headers' => [
                    'Authorization' => "Bearer $token",
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
        });
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
