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
    private Client $client;
    public function __construct(CXrepositoryInterface $CXRepository)
    {
        $this->CXRepository = $CXRepository;
        $this->client = new Client(['verify' => env('VERIFY_FILE', false)]);
    }
    /**
     * Display the medic data if exist.
     */
    public function getMedic(Request $request): JsonResponse
    {
        $inputs = $request->only('email', 'cedula', 'telefono', 'nombre', 'apellidoPat', 'apellidoMat', 'tarjeta', 'idVitamedica', 'numeroEmpleado', 'rfc', 'origen');
        return $this->CXRepository->getMedic($inputs);
    }
    public function getToken(Request $request): JsonResponse
    {
        $inputs = $request->only('email', 'password');
        return $this->CXRepository->getToken($inputs);
    }
    public function getUser(Request $request): JsonResponse
    {
        $inputs = $request->only('token');
        return $this->CXRepository->getUserByToken($inputs['token']);
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
