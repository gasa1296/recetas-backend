<?php

namespace App\Http\Controllers;

use GuzzleHttp\Exception\{ServerException, ClientException};
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Milon\Barcode\DNS1D;
use App\Models\Prescription;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Validator;

class LegalarioController extends Controller
{
    private Client $client;
    private string $token;
    public function __construct()
    {
        $this->client = new Client(['verify' => env('VERIFY_FILE', false)]);
    }
    /**
     * Login to legalario
     */
    private function legalarioLogin(): array
    {
        try {
            $res = $this->client->post(env('LEGALARIO_URL') . '/auth/login', [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Accept' => 'application/json'
                ],
                'form_params' => [
                    'email' => env('LEGALARIO_USER', ''),
                    'password' => env('LEGALARIO_PASSWORD', ''),
                ]
            ]);
            return json_decode($res->getBody(), true);
        } catch (ClientException | ServerException $e) {
            return json_decode($e->getResponse()->getBody(), true);
        }
    }
    /**
     * Get Legalario bearer token
     */
    private function legalarioToken(): array
    {
        $res = $this->legalarioLogin();
        if (!$res['success']) {
            return $res;
        }
        try {
            $res = $this->client->post(env('LEGALARIO_URL') . '/auth/token', [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Accept' => 'application/json'
                ],
                'form_params' => [
                    'client_id' => $res['data']['client_id'],
                    'client_secret' => $res['data']['client_secret'],
                    'grant_type' => $res['data']['grant_type'],
                    'scope' => $res['data']['scopes'],
                ]
            ]);
            return json_decode($res->getBody(), true);
        } catch (ClientException | ServerException $e) {
            return json_decode($e->getResponse()->getBody(), true);
        }
    }
    /**
     * Add document id to prescription
     */
    public function createDocument(Prescription $prescription, array $medicaments): JsonResponse
    {
        $responseData = [];
        for ($page = 0; $page < ceil(count($medicaments) / 5); $page++) {
            $medicamentsPaginated = array_slice($medicaments, $page * 5, 5);
            $res = $this->requestDocument($prescription, $medicamentsPaginated);
            if ($res->getStatusCode() >= 300) {
                return $res;
            } else {
                array_push($responseData, $res->getData(true)['data']['id']);
            }
        }
        return response()->json($responseData);
    }
    private function requestDocument(Prescription $prescription, array $medicaments)
    {
        $res = $this->legalarioToken();
        if (!$res['success']) {
            return response()->json($res, 400);
        }
        $token = $res['data']['access_token'];
        $medic = $prescription->medic;
        $patient = $prescription->patient;
        $room = $prescription->room;
        try {
            $date = new Carbon($prescription->created_at);
            $res = $this->client->post(env('LEGALARIO_URL') . '/v2/documents', [
                'headers' => [
                    'Authorization' => "Bearer $token",
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'json' => [
                    'name' => 'Receta',
                    'type' => 'template',
                    'template_id' => $room->design,
                    'sequence' => [
                        [
                            [
                                'key' => 1,
                                'name' => 'name',
                                'value' => "$medic->first_name $medic->last_name1 $medic->last_name2",
                            ]
                        ],
                        [
                            [
                                'key' => 2,
                                'name' => 'identification',
                                'value' => implode(
                                    ",",
                                    array_map(function ($spec) {
                                        return "$spec[name] Céd prof: $spec[identification]";
                                    }, $medic->specializations->toArray())
                                ),
                            ]
                        ],
                        [
                            [
                                'key' => 3,
                                'name' => 'id',
                                'value' => $prescription->code,
                            ]
                        ],
                        [
                            [
                                'key' => 4,
                                'name' => 'date',
                                'value' => $date->format('d/m/Y'),
                            ]
                        ],
                        [
                            [
                                'key' => 5,
                                'name' => 'time',
                                'value' => $date->format('H:i'),
                            ]
                        ],
                        [
                            [
                                'key' => 6,
                                'name' => 'patient name',
                                'value' => "$patient->first_name $patient->last_name1 $patient->last_name2",
                            ]
                        ],
                        [
                            [
                                'key' => 7,
                                'name' => 'birth date',
                                'value' => Carbon::createFromFormat('Y-m-d', $patient->birth_date)->format('d/m/Y'),
                            ]
                        ],
                        [
                            [
                                'key' => 8,
                                'name' => 'weight',
                                'value' => $prescription->weight ?: '',
                            ]
                        ],
                        [
                            [
                                'key' => 9,
                                'name' => 'height',
                                'value' => $prescription->height ?: '',
                            ]
                        ],
                        [
                            [
                                'key' => 10,
                                'name' => 'temp',
                                'value' => $prescription->temp ?: '',
                            ]
                        ],
                        [
                            [
                                'key' => 11,
                                'name' => 'saturation',
                                'value' => $prescription->saturation ?: '',
                            ]
                        ],
                        [
                            [
                                'key' => 12,
                                'name' => 'pressure',
                                'value' => $prescription->pressure ?: '',
                            ]
                        ],
                        [
                            [
                                'key' => 13,
                                'name' => 'ppm',
                                'value' => $prescription->ppm ?: '',
                            ]
                        ],
                        [
                            [
                                'key' => 14,
                                'name' => 'diagnostic',
                                'value' => $prescription->diagnostic ?: '',
                            ]
                        ],
                        [
                            [
                                'key' => 15,
                                'name' => 'medicaments',
                                'value' => implode(
                                    "\n",
                                    array_map(function ($key, $medicament) {
                                        if (empty ($medicament['indications'])) {
                                            return "$medicament[salt] | $medicament[name] \n $medicament[dose] | $medicament[frequency] | $medicament[duration] | $medicament[way]  | $medicament[quantity] cajas | $medicament[add] \n";
                                        } else {
                                            return "$medicament[name] \n $medicament[indications] \n";
                                        }
                                    }, array_keys($medicaments), $medicaments)
                                ),
                            ]
                        ],
                        [
                            [
                                'key' => 16,
                                'name' => 'room',
                                'value' => $room->name ?: '',
                            ]
                        ],
                        [
                            [
                                'key' => 17,
                                'name' => 'address',
                                'value' => "$room->street, $room->n_exterior, $room->n_interior, $room->colony, $room->zip, $room->delegation, $room->state",
                            ]
                        ],
                        [
                            [
                                'key' => 18,
                                'name' => 'phone',
                                'value' => $medic->phone1 ?: '',
                            ]
                        ],
                        [
                            [
                                'key' => 19,
                                'name' => 'email',
                                'value' => $medic->email ?: '',
                            ]
                        ],
                        [
                            [
                                'key' => 20,
                                'name' => 'specializations',
                                'value' => $medic->specializations->first()->name ?: '',
                            ]
                        ],
                        [
                            [
                                'key' => 21,
                                'name' => 'university',
                                'value' => $medic->specializations->first()->university ?: '',
                            ]
                        ],
                        [
                            [
                                'key' => 1001,
                                'name' => 'IMAGEN_CLIENTE_UNIVERSIDAD',
                                'value' => empty ($medic->specializations->first()->logo) ? '' : base64_encode(Storage::disk('public')->get($medic->specializations->first()->logo)),
                            ]
                        ],
                        [
                            [
                                'key' => 1002,
                                'name' => 'IMAGEN_CLIENTE_HOSPITAL',
                                'value' => empty ($room->logo) ? '' : base64_encode(Storage::disk('public')->get($room->logo)),
                            ]
                        ],
                        [
                            [
                                'key' => 1003,
                                'name' => 'IMAGEN_CLIENTE_BARRAS',
                                'value' => (new DNS1D())->getBarcodePNG($prescription->code, 'C128'),
                            ]
                        ],
                    ]
                ]
            ]);
            return response()->json(json_decode($res->getBody(), true));
        } catch (ClientException | ServerException $e) {
            return response()->json(json_decode($e->getResponse()->getBody(), true), $e->getResponse()->getStatusCode());
        }

    }
    public function saveFile(string $document_id): JsonResponse
    {
        $res = $this->legalarioToken();
        if (!$res['success']) {
            return response()->json($res, 400);
        }
        $token = $res['data']['access_token'];
        try {
            $res = $this->client->get(env('LEGALARIO_URL') . '/v2/documents/download', [
                'headers' => [
                    'Authorization' => "Bearer $token",
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'json' => [
                    'document_id' => $document_id,
                    "document_type" => 'Documento sin firmas',
                    "format" => "Base64",
                ]
            ]);
            return response()->json(json_decode($res->getBody(), true));
        } catch (ClientException | ServerException $e) {
            return response()->json(json_decode($e->getResponse()->getBody(), true), $e->getResponse()->getStatusCode());
        }
    }
    /**
     * Create and return prescription signers
     */
    public function createSigner(Prescription $prescription): JsonResponse
    {
        $errors = (new PrescriptionController)->verifyPrescription($prescription->medicaments);
        if (!empty($errors)) {
            return response()->json($errors, 400);
        }
        if(!empty($prescription->file)) {
            return response()->json(['prescription' => 'receta ya fue firmada previamente'], 400);
        }
        $res = $this->legalarioToken();
        if (!$res['success']) {
            return response()->json($res, 400);
        }
        $token = $res['data']['access_token'];
        $response = [];
        foreach (explode(';', $prescription->documents_id) as $document) {
            try {
                $medic = $prescription->medic;
                $res = $this->client->post(env('LEGALARIO_URL') . '/v2/signers', [
                    'headers' => [
                        'Authorization' => "Bearer $token",
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json'
                    ],
                    'json' => [
                        'document_id' => $document,
                        'workflow' => true,
                        'use_whatsapp' => false,
                        'signers' => [
                            [
                                'fullname' => "$medic->first_name $medic->last_name1 $medic->last_name2",
                                'email' => $medic->email,
                                'type' => 'MEDICO'
                            ]
                        ],
                    ]
                ]);
                $resBody = json_decode($res->getBody(), true);
                array_push($response, $resBody);
            } catch (ClientException | ServerException $e) {
                $resBody = json_decode($res->getBody(), true);
                array_push($response, $resBody);
            }
        }
        return response()->json($response);
    }
    public function getMedicaments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'products' => ['required ', 'array'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $inputs = $validator->safe()->all();
        try {
            $res = $this->client->post('https://tsoagobiernogrfe-pub-oci.opc.oracleoutsourcing.com/farmacos/subrogation/electronic-medical-prescription/v1/products/_detail', [
                'headers' => [
                    'Authorization' => "Basic " . base64_encode("userTest:Vwq5MYEUtesVwYtK"),
                ],
                'json' => ['products' => $inputs['products']]
            ]);
            return response()->json(json_decode($res->getBody(), true));
        } catch (ClientException | ServerException $e) {
            return response()->json(json_decode($e->getResponse()->getBody(), true), $e->getResponse()->getStatusCode());
        }
    }
}
