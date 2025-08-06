<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use App\Repositories\Interfaces\CXrepositoryInterface;
use GuzzleHttp\Exception\{ClientException, ServerException};

class MagentoController extends Controller
{
    private $CXRepository;
    private array $magentoAuth;
    private string $magentoUrl;
    private Client $client;
    public function __construct(CXrepositoryInterface $CXRepository)
    {
        $this->CXRepository = $CXRepository;
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
        $inputs = $request->only('email', 'cedula', 'telefono', 'nombre', 'apellidoPat', 'apellidoMat', 'tarjeta', 'idVitamedica', 'numeroEmpleado', 'rfc', 'origen');
        $inputsNew = [];
        foreach ($inputs as $key => $value) {
            if (!empty($value)) {
                $inputsNew[$key] = strtolower($value);
            }
        }
        try {
            $res = $this->client->get(env('URL_MEDIC'), [
                'auth' => $this->magentoAuth,
                'query' => $inputsNew
            ]);
            $decodedRes = json_decode($res->getBody(), true);
            return response()->json($decodedRes);
        } catch (ClientException | ServerException $e) {
            return response()->json(json_decode($e->getResponse()->getBody(), true), $e->getResponse()->getStatusCode());
        }
    }
    public function getToken(Request $request): JsonResponse
    {
        $inputs = $request->only('email', 'password');
        return $this->CXRepository->getToken($inputs);
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
}
