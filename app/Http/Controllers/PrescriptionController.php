<?php

namespace App\Http\Controllers;

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
        $token = $request->bearerToken();
        if ($token != env('PUBLIC_KEY', '')) {
            return response()->json(['token' => 'token invalido'], 403);
        }
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
        $token = $request->bearerToken();
        if ($token != env('PUBLIC_KEY', '')) {
            return response()->json(['token' => 'token invalido'], 403);
        }
        $inputs = $request->all();
        Log::debug('Webhook', $inputs);
        $instance = Prescription::where('document_id', '=', $inputs['document']['id'])
            ->whereNull('file')
            ->firstOrFail();

        $instance->file = Storage::disk('public')->put("medics/$instance->user_id/prescriptions/$instance->id.zip", $inputs['zip']);
        $instance->save();
        return (new PrescriptionResource($instance))->response();
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
            if (in_array($medicament->group, ['Grupo II', 'Grupo III', 'RESTRICCION ANTIBIOTICOS'])) {
                $errors[$medicament->medicament_id] = 'grupo no valido para enviar receta';
            }
        }
        if(!empty($errors)) {
            return response()->json($errors, 400);
        }
        $prescription->patient->notify(new PrescriptionSignedEmail($prescription));
        return response()->json();
    }
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
    private function createDocument(Prescription $prescription, string $token): array
    {
        try {
            $meds = $prescription->medicaments->toArray();
            $res = $this->client->post(env('LEGALARIO_URL') . '/v2/documents', [
                'headers' => [
                    'Authorization' => "Bearer $token",
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'json' => [
                    'name' => 'Receta',
                    'type' => 'template',
                    'template_id' => /*$prescription->room->design*/"659c7a5d3ce79e0f44521cb9",
                    'sequence' => array_map(function ($key, $medicament) {
                        return [
                            [
                                'key' => $key + 1,
                                'name' => $medicament["name"] . ':',
                                'value' => "$medicament[name], $medicament[way], $medicament[dose], $medicament[frequency], $medicament[duration], $medicament[quantity]"
                            ]
                        ];
                    }, array_keys($meds), $meds),
                ]
            ]);
            return json_decode($res->getBody(), true);
        } catch(ClientException $e) {
            return json_decode($e->getResponse()->getBody(), true);
        }
    }
    public function createSigner(Prescription $prescription): JsonResponse
    {
        $res = $this->legalarioToken();
        if (!$res['success']) {
            return response()->json($res, 400);
        }
        $token = $res['data']['access_token'];
        $res = $this->createDocument($prescription, $token);
        if (!$res['success']) {
            return response()->json($res, 400);
        }
        $prescription->document_id = $res['data']['id'];
        $prescription->save();
        try {
            $medic = $prescription->medic;
            $res = $this->client->post(env('LEGALARIO_URL') . '/v2/signers', [
                'headers' => [
                    'Authorization' => "Bearer $token",
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'json' => [
                    'document_id' => $res['data']['id'],
                    'workflow' => true,
                    'use_whatsapp' => false,
                    'signers' => [
                        [
                            'fullname' => "$medic->first_name $medic->last_name1 $medic->last_name2",
                            'email' => $medic->email,
                            'type' => 'FIRMA'
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
