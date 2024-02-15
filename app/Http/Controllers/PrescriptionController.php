<?php

namespace App\Http\Controllers;

use App\Models\PrescriptionMedicament;
use App\Notifications\PrescriptionSignedEmail;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Validator;
use App\Http\Resources\PrescriptionResource;
use App\Models\Prescription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use ZipArchive;
use Carbon\Carbon;


class PrescriptionController extends Controller
{
    private Client $client;
    private string $token;
    public function __construct()
    {
        $this->client = new Client(['verify' => env('VERIFY_FILE', false)]);
    }
    /**
     * Display a listing of the resource.
     * @todo add search
     */
    public function index(): JsonResponse
    {
        $qs = Prescription::where('user_id', auth()->id());
        return PrescriptionResource::collection($qs->paginate(10))->response();
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'temp' => ['nullable', 'numeric'],
            'weight' => ['nullable ', 'numeric'],
            'height' => ['nullable ', 'numeric'],
            'pressure' => ['nullable ', 'string'],
            'saturation' => ['nullable ', 'numeric'],
            'ppm' => ['nullable ', 'numeric'],
            'allergy' => ['nullable ', 'string'],
            'diagnostic' => ['required', 'string'],
            'diet' => ['nullable ', 'string'],
            'add' => ['nullable ', 'string'],
            'add_med' => ['nullable ', 'json'],
            'medicaments' => ['nullable ', 'array'],
            'room_id' => ['required ', 'numeric'],
            'patient_id' => ['required ', 'numeric'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $inputs1 = $validator->safe()->all();
        $inputs1['user_id'] = auth()->id();

        $instance = Prescription::create($inputs1);

        if(empty($inputs1['medicaments'])) {
            $document = $this->createDocument($instance);
            if ($document->getStatusCode() >= 300) {
                return $document;
            }
            return (new PrescriptionResource($instance))->response();
        }
        $validator = Validator::make($inputs1['medicaments'], [
            '*.add' => ['nullable', 'string'],
            '*.dose' => ['required', 'string'],
            '*.way' => ['required', 'string'],
            '*.frequency' => ['required', 'string'],
            '*.duration' => ['required', 'string'],
            '*.quantity' => ['required', 'numeric'],
            '*.medicament_id' => ['required', 'numeric'],
            '*.name' => ['required', 'string'],
            '*.type' => ['required', 'string'],
            '*.group' => ['required', 'string'],
            '*.family' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $inputs2 = $validator->safe()->all();
        $instance->medicaments()->createMany($inputs2);

        $document = $this->createDocument($instance);
        if($document->getStatusCode() >= 300) {
            return $document;
        }

        return (new PrescriptionResource($instance))->response();
    }
    /**
     * Display the specified resource.
     */
    public function show(Prescription $prescription): JsonResponse
    {
        return (new PrescriptionResource($prescription))->response();
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Prescription $prescription): JsonResponse
    {
        if ($prescription->user_id != auth()->id()) {
            return response()->json([], 404);
        }
        $validator = Validator::make($request->all(), [
            'temp' => ['nullable', 'numeric'],
            'weight' => ['nullable ', 'numeric'],
            'height' => ['nullable ', 'numeric'],
            'pressure' => ['nullable ', 'string'],
            'saturation' => ['nullable ', 'numeric'],
            'ppm' => ['nullable ', 'numeric'],
            'allergy' => ['nullable ', 'string'],
            'diagnostic' => ['required', 'string'],
            'diet' => ['nullable ', 'string'],
            'add' => ['nullable ', 'string'],
            'add_med' => ['nullable ', 'json'],
            'room_id' => ['required ', 'numeric'],
            'patient_id' => ['required ', 'numeric'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $inputs = $validator->safe()->all();
        $prescription->update($inputs);
        return (new PrescriptionResource($prescription))->response();
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prescription $prescription): JsonResponse
    {
        if ($prescription->user_id != auth()->id()) {
            return response()->json([], 404);
        }
        $prescription->delete();
        return response()->json();
    }
    /**
     * Update the specified resource in storage.
     */
    public function addClient(Request $request, Prescription $prescription)
    {
        $token = $request->bearerToken();
        if ($token != env('PUBLIC_KEY', '')) {
            return response()->json(['token' => 'token invalido'], 403);
        }
        if (!empty($prescription->client)) {
            return response()->json(['client' => 'Ya tiene cliente'], 400);
        }
        $validator = Validator::make($request->all(), [
            'client' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $inputs = $validator->safe()->only('client');
        $prescription->update($inputs);
        return (new PrescriptionResource($prescription))->response();
    }
    /**
     * Display a listing of the resource by client.
     */
    public function getByClient(Request $request)
    {
        $token = $request->bearerToken();
        if ($token != env('PUBLIC_KEY', '')) {
            return response()->json(['token' => 'token invalido'], 403);
        }
        $validator = Validator::make($request->all(), [
            'client' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $inputs = $validator->safe()->only('client');
        return PrescriptionResource::collection(Prescription::where('client', $inputs['client'])->paginate(10))->response();
    }
    /**
     * Display a listing of the resource by client.
     */
    public function updateStatus(Request $request, Prescription $prescription)
    {
        $token = $request->bearerToken();
        if ($token != env('PUBLIC_KEY', '')) {
            return response()->json(['token' => 'token invalido'], 403);
        }
        $validator = Validator::make($request->all(), [
            '*.total_exp' => ['required', 'numeric'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $completed = true;
        $errors = [];
        $inputs = $validator->safe()->all();
        foreach ($prescription->medicaments as $medicament) {
            $med_id = $medicament->medicament_id;
            if (!empty($inputs[$med_id])) {
                $medicament->quantity_exp += $inputs[$med_id]['total_exp'];
                if ($medicament->quantity_exp > $medicament->quantity && $medicament->group == 'RESTRICCION ANTIBIOTICOS') {
                    $errors[$med_id . '.total_exp'] = 'No se puede expedir mas de lo recetado';
                    continue;
                }
            }
            if ($medicament->quantity_exp != $medicament->quantity) {
                $completed = false;
            }
        }
        if (!empty($errors)) {
            return response()->json($errors, 400);
        }
        if ($completed) {
            $prescription->status = 2;
        } else {
            $prescription->status = 1;
        }
        $prescription->push();

        return (new PrescriptionResource($prescription))->response();
    }
    /**
     * Display a listing of the resource by client.
     */
    public function addFile(Request $request)
    {
        $inputs = $request->all();
        Log::debug('document', ['document' => $inputs['document']]);
        $token = $request->bearerToken();
        if ($token != env('PUBLIC_KEY', '')) {
            return response()->json(['token' => 'token invalido'], 403);
        }
        $instance = Prescription::where('document_id', $inputs['document']['id'])
            ->firstOrFail();
        $inputs = $request->all();
        Log::debug('prescription', ['prescription' => $instance->id]);
        $dir = "medics/$instance->user_id/prescriptions/$instance->id.zip";
        if (!Storage::put($dir, base64_decode($inputs['zip']))) {
            return response()->json('Error guardando archivo', 500);
        }
        $instance->file = '/api/receta/' . $instance->id . '/file';
        $instance->save();
        Log::debug('prescription', ['file' => $instance->file]);
        return (new PrescriptionResource($instance))->response();
    }
    /**
     * Display a medicament of the prescription.
     */
    public function getMedicament(int $desc)
    {
        $reponse = $this->client->post(
            'https://w9gkg4xp3k.execute-api.us-east-1.amazonaws.com/Prod/api/preproductos',
            [
                'json' => [
                    "hash" => "initial",
                    "descripcion" => (string) $desc,
                ]
            ]
        );
        $body = json_decode($reponse->getBody(), true);
        return response()->json($body['Respuesta'][0]);
    }
    /**
     * Send email notification to patient.
     */
    public function sendEmailNotification(Prescription $prescription)
    {
        $errors = $this->verifyPrescription($prescription->medicaments);
        if (!empty($errors)) {
            return response()->json($errors, 400);
        }
        $zip = new ZipArchive;
        $status = $zip->open(base_path() . '/storage/app/' . $prescription->file);
        if ($status !== true) {
            return response()->json('error al obtener archivo 1', 500);
        }
        $fileData = $zip->getFromName('signed_receta.pdf');
        if ($fileData === false) {
            return response()->json('error al obtener archivo 2', 500);
        }
        $prescription->patient->notify(new PrescriptionSignedEmail($prescription, $fileData));
        return response()->json();
    }
    /**
     * Download precription file
     */
    public function getFile(Request $request, Prescription $prescription)
    {
        if (empty(auth()->user())) {
            $token = $request->bearerToken();
            if ($token != env('PUBLIC_KEY', '')) {
                return response()->json(['token' => 'token invalido'], 403);
            }
        }
        $errors = $this->verifyPrescription($prescription->medicaments);
        $documentType = empty($errors) && empty($prescription->add_med)?'Documento con firmado':'Documento sin firmas';
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
                    'document_id' => $prescription->document_id,
                    "document_type" => $documentType,
                    "format" => "Base64",
                ]
            ]);
            $fileData = base64_decode(json_decode($res->getBody(), true)['data']['document']);
            return response()->streamDownload(function () use ($fileData) {
                echo $fileData;
            }, 'receta.pdf');
        } catch (ClientException $e) {
            return response()->json(json_decode($e->getResponse()->getBody(), true), 400);
        }
    }
    /**
     * Verify if prescription can be sended or signed
     */
    private function verifyPrescription($medicaments)
    {
        $errors = [];
        foreach ($medicaments as $medicament) {
            if (in_array($medicament->group, ['Grupo II', 'Grupo III', 'RESTRICCION ANTIBIOTICOS'])) {
                $errors[$medicament->medicament_id] = 'grupo no valido para enviar receta';
            }
        }
        return $errors;
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
        } catch (ClientException $e) {
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
        } catch (ClientException $e) {
            return json_decode($e->getResponse()->getBody(), true);
        }
    }
    /**
     * Add document id to prescription
     */
    public function createDocument(Prescription $prescription): JsonResponse
    {
        $res = $this->legalarioToken();
        if (!$res['success']) {
            return response()->json($res, 400);
        }
        $token = $res['data']['access_token'];
        try {
            $medic = $prescription->medic;
            $patient = $prescription->patient;
            Log::debug('timestamp', [$prescription->created_at]);
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
                    'template_id' => $prescription->room->design,
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
                                'value' => $medic->specializations->first()->identification ?: '',
                            ]
                        ],
                        [
                            [
                                'key' => 3,
                                'name' => 'id',
                                'value' => $prescription->id,
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
                                'value' => $date->diffInYears(new Carbon($patient->birth_date)),
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
                                'value' => $prescription->ppm?: '',
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
                                'value' => implode("\n",
                                    array_map(function ($medicament) {
                                        return "$medicament[name] \n $medicament[dose] | $medicament[frequency] | $medicament[duration] | $medicament[way]  | $medicament[quantity] | $medicament[add] \n";
                                    }, $prescription->medicaments->toArray())
                                ) . "\n" . implode("\n",
                                    array_map(function ($medicament) {
                                        return "$medicament[name] \n $medicament[indications] \n";
                                    }, json_decode($prescription->add_med, true)?:[])
                                ) . "\n" . $prescription->add,
                            ]
                        ],
                        [
                            [
                                'key' => 16,
                                'name' => 'room',
                                'value' => $prescription->room->name ?: '',
                            ]
                        ],
                        [
                            [
                                'key' => 17,
                                'name' => 'address',
                                'value' => $prescription->room->address,
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
                                'name' => 'bar',
                                'value' => base64_encode($prescription->id) ?: '',
                            ]
                        ],
                    ]
                ]
            ]);
            $prescription->document_id = json_decode($res->getBody(), true)['data']['id'];
            $prescription->save();

            $errors = $this->verifyPrescription($prescription->medicaments);
            if (empty($errors) && empty($prescription->add_med)) {
                $res = $this->client->get(env('LEGALARIO_URL') . '/v2/documents/download', [
                    'headers' => [
                        'Authorization' => "Bearer $token",
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json'
                    ],
                    'json' => [
                        'document_id' => $prescription->document_id,
                        "document_type" => 'Documento sin firmas',
                        "format" => "Base64",
                    ]
                ]);
                $dir = "medics/$prescription->user_id/prescriptions/$prescription->id.pdf";
                $fileData = base64_decode(json_decode($res->getBody(), true)['data']['document']);
                if (!Storage::put($dir, base64_decode($fileData))) {
                    return response()->json('Error guardando archivo', 500);
                }
                $prescription->file = '/api/receta/' . $prescription->id . '/file';
                $prescription->save();
            }
            return response()->json();
        } catch (ClientException $e) {
            return response()->json(json_decode($e->getResponse()->getBody(), true));
        }
    }
    /**
     * Create and return prescription signers
     */
    public function createSigner(Prescription $prescription): JsonResponse
    {
        $errors = $this->verifyPrescription($prescription->medicaments);
        if (!empty($errors)) {
            return response()->json($errors, 400);
        }
        /*if(!empty($prescription->file)) {
            return response()->json(['prescription' => 'receta ya fue firmada previamente'], 400);
        }*/
        $res = $this->legalarioToken();
        if (!$res['success']) {
            return response()->json($res, 400);
        }
        $token = $res['data']['access_token'];
        
        try {
            $medic = $prescription->medic;
            $res = $this->client->post(env('LEGALARIO_URL') . '/v2/signers', [
                'headers' => [
                    'Authorization' => "Bearer $token",
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'json' => [
                    'document_id' => $prescription->document_id,
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
            return response()->json(json_decode($res->getBody(), true));
        } catch (ClientException $e) {
            return response()->json(json_decode($e->getResponse()->getBody(), true), 400);
        }
    }
}
