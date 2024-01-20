<?php

namespace App\Http\Controllers;

use App\Notifications\PrescriptionSigned;
use Validator;
use App\Http\Resources\PrescriptionResource;
use App\Models\Prescription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use GuzzleHttp\Client;

/**
 * @todo Add update status endpoint
 * @todo Add public get endpoint
 */
class PrescriptionController extends Controller
{
    private Client $client;
    private string $token;
    public function __construct()
    {
        $this->client = new Client();        
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
            'room_id' => ['required ', 'numeric'],
            'patient_id' => ['required ', 'numeric'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $inputs = $validator->safe()->all();
        $inputs['user_id'] = auth()->id();
        $instance = Prescription::create($inputs);

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
        //return $request->bearerToken();
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
        //return $request->bearerToken();
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
        //return $request->bearerToken();
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
                if ($medicament->quantity_exp > $medicament->quantity) {
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
    public function addFile(Request $request, Prescription $prescription)
    {
        //return $request->bearerToken();
        $validator = Validator::make($request->all(), [
            'file' => ['required', 'file'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $prescription->file = $request->file('file')->store('prescriptions', 'public');
        return (new PrescriptionResource($prescription))->response();
    }
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
    public function sendEmailNotification(Prescription $prescription)
    {
        $errors = [];
        foreach ($prescription->medicaments as $medicament) {
            if (in_array($medicament->group, ['Grupo II', 'Grupo III'])) {
                $errors[$medicament->medicament_id] = 'grupo no valido para enviar receta';
            }
        }
        if(!empty($errors)) {
            return response()->json($errors, 400);
        }
        $prescription->patient->notify(new PrescriptionSigned($prescription));
        return response()->json();
    }
    private function legalarioLogin(): array
    {
        $client = new Client();
        $response = $client->post(env('LEGALARIO_URL') . '/auth/login', [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json'
            ],
            'form_params' => [
                'email' => env('LEGALARIO_USER', ''),
                'password' => env('LEGALARIO_PASSWORD', ''),
            ]
        ]);
        $body = json_decode($response->getBody(), true);
        if (!$body['success']) {
            return [];
        }
        return $body['data'];
    }
    private function legalarioToken(): string
    {
        $loginData = $this->legalarioLogin();
        if (empty($loginData)) {
            return '';
        }
        $response = $this->client->post(env('LEGALARIO_URL') . '/auth/token', [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json'
            ],
            'form_params' => [
                'client_id' => $loginData['client_id'],
                'client_secret' => $loginData['client_secret'],
                'grant_type' => $loginData['grant_type'],
                'scope' => $loginData['scope'],
            ]
        ]);
        $body = json_decode($response->getBody(), true);
        if (!$body['success']) {
            return '';
        }
        return $body['data']['access_token'];
    }
    private function createDocument(Prescription $prescription): string
    {
        if (empty($this->token)) {
            return '';
        }
        $response = $this->client->post(env('LEGALARIO_URL') . '/v2/documents', [
            'headers' => [
                'Authorization' => "Basic $this->token",
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'json' => [
                'name' => 'Receta',
                'type' => 'template',
                'template_id' => /*$prescription->room->design*/ "659c7a5d3ce79e0f44521cb9",
                'sequence' => array_map(function($medicament) {
                    return [[
                        'key' => $medicament->medicament_id,
                        'name' => $medicament->name,
                        'value' => "$medicament->name, $medicament->way, $medicament->dose, $medicament->frequency, $medicament->duration, $medicament->quantity"
                    ]];
                }, $prescription->medicaments),
            ]
        ]);
        $body = json_decode($response->getBody(), true);
        if (!$body['success']) {
            return '';
        }
        return $body['data']['id'];
    }
    public function createSigner(Prescription $prescription): array
    {
        $this->token = $this->legalarioToken();
        if (empty($this->token)) {
            return response()->json(['message' => 'token invalido'], 400);
        }
        $medic = $prescription->medic;
        $response = $this->client->post(env('LEGALARIO_URL') . '/v2/signers', [
            'headers' => [
                'Authorization' => "Basic $this->token",
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'json' => [
                'document_id' => $this->createDocument($prescription),
                'workflow' => true,
                'use_whatsapp' => true,
                'signers' => [
                    'fullname' => "$medic->first_name $medic->last_name1 $medic->last_name2",
                    'email' => $medic->email,
                    'phone' => $medic->phone1,
                    'type' => '',
                    'role' => ''
                ],
            ]
        ]);
        $body = json_decode($response->getBody(), true);
        if (!$body['success']) {
            return response()->json(['message' => $body['message']], $response->getStatusCode());
        }
        return response()->json($body['data']);
    }
}
