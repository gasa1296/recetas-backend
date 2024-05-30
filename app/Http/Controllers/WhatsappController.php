<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Models\Prescription;
use Illuminate\Http\JsonResponse;
use GuzzleHttp\Exception\{ClientException, ServerException};

class WhatsappController extends Controller
{
    private Client $client;
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => env('LIKENUUK_URL', '')
        ]);
    }
    private function login(): JsonResponse
    {
        try {
            $res = $this->client->post('/user/token', [
                'json' => [
                    'username' => env('LIKENUUK_USR', ''),
                    'password' => env('LIKENUUK_PSS', ''),
                ]
            ]);
            $decodedRes = json_decode($res->getBody(), true);
            return response()->json($decodedRes);
        } catch (ClientException | ServerException $e) {
            return response()->json(json_decode($e->getResponse()->getBody(), true), $e->getResponse()->getStatusCode());
        }
    }
    public function sendMessage (Prescription $prescription): JsonResponse
    {
        $login = $this->login();
        if ($login->getStatusCode() >= 300) {
            return $login;
        }
        $loginDecoded = $login->getData(true);
        $patient = $prescription->patient;
        try {
            $documents = explode(';', $prescription->document_id);
            foreach( $documents as $document) {
                $res = $this->client->post('/api/message/send', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $loginDecoded['token']
                    ],
                    'json' => [
                        "campaign" => "Envio de Recetas",
                        "origin" => "Receta",
                        "phone" => json_decode($patient->phone1, true)[0],
                        "channel" => "whatsapp",
                        "templateName" => "envio_receta",
                        "params" => [
                            $patient->first_name . ' ' . $patient->last_name1 ?? '' . ' ' . $patient->last_name2 ?? '',
                            (new Carbon($prescription->createdAt))->toDateString()
                        ],
                        'media' => [
                            'type' => 'document',
                            'url' => 'https://testapireceta.farmaciasespecializadas.com/api/receta/LWPUFDR4/file?document_id=' . $document,
                        ]
                    ]
                ]);
            }
            $decodedRes = json_decode($res->getBody(), true);
            return response()->json($decodedRes);
        } catch (ClientException | ServerException $e) {
            return response()->json(json_decode($e->getResponse()->getBody(), true), $e->getResponse()->getStatusCode());
        }
    }
    public function getTemplates(): JsonResponse
    {
        $login = $this->login();
        if ($login->getStatusCode() >= 300) {
            return $login;
        }
        $loginDecoded = $login->getData(true);
        try {
            $res = $this->client->post('/api/templates', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $loginDecoded['token']
                ],
            ]);
            $decodedRes = json_decode($res->getBody(), true);
            return response()->json($decodedRes);
        } catch (ClientException | ServerException $e) {
            return response()->json(json_decode($e->getResponse()->getBody(), true), $e->getResponse()->getStatusCode());
        }
    }
}
