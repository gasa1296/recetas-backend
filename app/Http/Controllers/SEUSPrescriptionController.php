<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ZipArchive;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\PrescriptionResource;
use App\Models\Prescription;
use GuzzleHttp\Client;
use Validator;
use Carbon\Carbon;
class SEUSPrescriptionController extends Controller
{
    private Client $client;
    public function __construct()
    {
        $this->client = new Client(['verify' => env('VERIFY_FILE', false)]);
    }
    /**
     * Display the specified resource.
     */
    public function show(Request $request, Prescription $prescription)
    {
        $token = $request->bearerToken();
        if ($token != env('PUBLIC_KEY', '')) {
            return response()->json(['token' => 'token invalido'], 403);
        }
        return(new PrescriptionResource($prescription))->response();
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
        $instance->file = env('APP_URL') . '/api/receta/' . $instance->code . '/file';
        $instance->save();
        Log::debug('prescription', ['file' => $instance->file]);
        return(new PrescriptionResource($instance))->response();
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
        return(new PrescriptionResource($prescription))->response();
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
        $completeds = [];
        $errors = [];
        $inputs = $validator->safe()->all();
        foreach ($prescription->medicaments as $medicament) {
            $med_id = $medicament->medicament_id;
            if (!empty($inputs[$med_id])) {
                $medicament->quantity_exp += $inputs[$med_id]['total_exp'];
                if ($medicament->group == 'RESTRICCION ANTIBIOTICOS') {
                    if ($medicament->quantity_exp > $medicament->quantity) {
                        $errors[$med_id . '.total_exp'] = 'No se puede expedir mas de lo recetado';
                        continue;
                    }
                    if ($medicament->quantity_exp == $medicament->quantity) {
                        $completeds[$med_id] = true;
                    }
                }
            }
        }
        if (!empty($errors)) {
            return response()->json($errors, 400);
        }
        if (!empty($completeds) && $prescription->medicaments->count() == count($completeds)) {
            $prescription->status = 2;
        } else {
            $prescription->status = 1;
        }
        $prescription->save();
        $prescription->push();

        return(new PrescriptionResource($prescription))->response();
    }
}
