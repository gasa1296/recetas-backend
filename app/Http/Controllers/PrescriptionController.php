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
use Milon\Barcode\DNS1D;


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
        $qs = Prescription::where('user_id', auth()->id())
            ->orderBy('id', 'desc');
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
            'diagnostic' => ['nullable', 'string'],
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
        $inputs1['code'] = base_convert(Carbon::now()->getPreciseTimestamp(3), 10, 36);
        
        $room = $request->user()->rooms;
        $filteredRoom = $room->where('user_id', '=', $inputs1['user_id'])->where('id', '=', $inputs1['room_id'])->first();
        if(empty($filteredRoom)) {
            return response()->json(['room' => ['consultorio no pertenece a medico']], 400);
        }
        $instance = Prescription::create($inputs1);

        if (empty($inputs1['medicaments'])) {
            return $this->storeExtra($instance);
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
            '*.salt' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $inputs2 = $validator->safe()->all();
        $instance->medicaments()->createMany($inputs2);
        return $this->storeExtra($instance);
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
            'diagnostic' => ['nullable', 'string'],
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
     * Send email notification to patient.
     */
    public function sendEmailNotification(Prescription $prescription)
    {
        $errors = $this->verifyPrescription($prescription->medicaments);
        $dir = "/storage/app/medics/$prescription->user_id/prescriptions/$prescription->id.";
        $add_med = json_decode($prescription->add_med, true);
        if (empty($errors) && empty($add_med)) {
            $zip = new ZipArchive;
            $status = $zip->open(base_path() . $dir . "zip");
            if ($status !== true) {
                return response()->json('error al obtener archivo 1', 500);
            }
            $fileData = $zip->getFromName('signed_receta.pdf');
            if ($fileData === false) {
                return response()->json('error al obtener archivo 2', 500);
            }
            $prescription->patient->notify(new PrescriptionSignedEmail($prescription, $fileData));
        } else {
            return response()->json(['prescription' => 'receta no valida para enviar por correo']);
        }
        return response()->json();
    }
    /**
     * Download precription file
     */
    public function getFile(Request $request, Prescription $prescription)
    {
        $errors = $this->verifyPrescription($prescription->medicaments);
        $dir = "/storage/app/medics/$prescription->user_id/prescriptions/$prescription->id.";
        if (!empty($errors) || $prescription->add_med != '[]') {
            return Storage::download("medics/$prescription->user_id/prescriptions/$prescription->id.pdf", 'receta.pdf');
        } else {
            $zip = new ZipArchive;
            $status = $zip->open(base_path() . $dir . "zip");
            if ($status !== true) {
                return response()->json('error al obtener archivo 1', 500);
            }
            $fileData = $zip->getFromName('signed_receta.pdf');
            if ($fileData === false) {
                return response()->json('error al obtener archivo 2', 500);
            }
            return response()->streamDownload(function () use ($fileData) {
                echo $fileData;
            }, 'receta.pdf');
        }
    }
    /**
     * Verify if prescription can be sended or signed
     */
    public function verifyPrescription($medicaments)
    {
        $errors = [];
        foreach ($medicaments as $medicament) {
            if (in_array($medicament->group, ['Grupo II', 'Grupo III'])) {
                $errors[$medicament->medicament_id] = 'grupo no valido para enviar receta';
            }
        }
        return $errors;
    }
    private function storeExtra(Prescription $instance): JsonResponse
    {
        $legalario = new LegalarioController();
        $dir = "medics/$instance->user_id/prescriptions/$instance->id.pdf";
        $document = $legalario->createDocument($instance);
        if ($document->getStatusCode() >= 300) {
            return $document;
        }
        $instance->document_id = $document->getData(true)['data']['id'];
        if (!empty($errors) || !empty(json_decode($instance->add_med, true))) {
            $instance->status = 5;
            $file = $legalario->saveFile($instance);

            if ($file->getStatusCode() >= 300) {
                return $file;
            }

            $fileData = base64_decode($file->getData(true)['data']['document']);
            if (!Storage::put($dir, $fileData)) {
                return response()->json('Error guardando archivo', 500);
            }

            $instance->file = env('APP_URL') . '/api/receta/' . $instance->code . '/file';
        }
        $instance->save();
        return(new PrescriptionResource($instance))->response();
    }
}
