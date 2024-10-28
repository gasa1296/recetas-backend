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
            'base_uri' => env('LIKENUUK_URL', ''),
            'verify' => env('VERIFY_FILE', false)
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
    public function sendMessage(Prescription $prescription): JsonResponse
    {
        $login = $this->login();
        if ($login->getStatusCode() >= 300) {
            return $login;
        }
        $loginDecoded = $login->getData(true);
        $patient = $prescription->patient;
        $medic = $prescription->medic;
        if (empty($prescription->file)) {
            return response()->json(['file' => 'archivo no encontrado'], 500);
        }
        try {
            $documents = explode(';', $prescription->document_id);
            foreach ($documents as $document) {
                $res = $this->client->post('/api/message/send', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $loginDecoded['token']
                    ],
                    'json' => [
                        "campaign" => "Envio de Recetas",
                        "origin" => "Receta",
                        "phone" => json_decode($patient->phone1, true)[0],
                        "channel" => "whatsapp",
                        "templateName" => "surtir_receta_5",
                        "params" => [
                            $patient->first_name . ' ' . $patient->last_name1 ?? '' . ' ' . $patient->last_name2 ?? '',
                            $medic->first_name . ' ' . $medic->last_name1 ?? '' . ' ' . $medic->last_name2 ?? '',
                            (new Carbon($prescription->createdAt))->toDateString(),
                            $prescription->code,
                            str_replace([
                                'https://app.farmaciasespecializadas.com/',
                                'https://appfesaqa.farmaciasespecializadas.com/'
                            ],'',$this->generateAppLink($prescription))
                        ],
                        'media' => [
                            'type' => 'document',
                            'url' => $prescription->file . '?document_id=' . $document,
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

    private function generateAppLink(Prescription $prescription): String
    {
        $client = new Client([
            'verify' => env('VERIFY_FILE', false)
        ]);
        try {
            $res = $client->post(env('URL_LINK', '') . '/api/fesa-auth/Auth/AuthWebhook', [
                'json' => [
                    'UserName' => env('USR_LINK', ''),
                    'Password' => env('PASS_LINK', '')
                ]
            ]);

            $resBody = json_decode($res->getBody(), true);
            $token = $resBody['data']['token'];


            $res = $client->post(env('URL_LINK', '') . '/api/webhook/Recetas/CrearShortUrl', [
                'headers' => [
                    'Authorization' => "Bearer $token",
                ],
                'json' => [
                    'idFolio' => $prescription->code
                ]
            ]);

            $resBody = json_decode($res->getBody(), true);
            return $resBody['data']['shortLink'];
        } catch (ClientException | ServerException $e) {
            return '';
        }
    }
}
