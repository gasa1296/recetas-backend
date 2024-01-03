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

/**
 * @todo Add update status endpoint
 * @todo Add public get endpoint
 */
class PrescriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     * @todo add search
     */
    public function index(): JsonResponse
    {   $qs = Prescription::where('user_id', auth()->id());
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
                    $errors[$med_id . '.total_exp'] = 'No se puede expedir mas de lo recetado' ;
                    continue;
                }
            }
            if ($medicament->quantity_exp != $medicament->quantity) {
                $completed = false;
            }
        }
        if(!empty($errors)){
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
    private function sendNotification(Prescription $prescription)
    {
        foreach($prescription->medicaments as $medicament) {
            //verify medicament group
        }
        $prescription->patient->notify(new PrescriptionSigned($prescription));
    }
}
