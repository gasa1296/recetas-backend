<?php

namespace App\Http\Controllers;

use Validator;
use App\Http\Resources\PrescriptionResource;
use App\Models\Prescription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
    {
        return PrescriptionResource::collection(Prescription::where('user_id', auth()->id())->paginate(10))->response();
    }

    /**
     * Store a newly created resource in storage.
     * @todo Add validations
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
            'file' => 'file',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $inputs = $request->all();
        $inputs['user_id'] = auth()->id();
        if ($request->file('file')) {
            $file = $request->file('file')->store('prescriptions');
            $inputs['file'] = $file;
        }
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
     * @todo Add validations
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
            'file' => 'file',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        if ($request->file('file')) {
            $file = $request->file('file')->store('prescriptions');
            $inputs['file'] = $file;
            if ($inputs['file']) {
                Storage::delete($prescription->file);
            }
        }
        $prescription->update($request->all());
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
}
