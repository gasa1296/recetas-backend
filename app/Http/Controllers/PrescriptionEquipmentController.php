<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\Equipment;
use App\Models\PrescriptionEquipment;
use App\Models\Prescription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PrescriptionEquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Prescription $prescription): JsonResponse
    {
        if ($prescription->user_id != auth()->id()) {
            return response()->json([], 404);
        }
        $instances = $prescription->equipment();
        return response()->json($instances->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Prescription $prescription): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'data' => ['required', 'array'],
            'data.*.add' => ['nullable', 'string'],
            'data.*.equipment_id' => ['required', 'numeric'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $inputs = $validator->safe()->all();
        $instance = $prescription->equipment()->createMany($inputs['data']);
        return response()->json($instance);
    }

    /**
     * Display the specified resource.
     */
    public function show(Prescription $prescription, Equipment $equipment): JsonResponse
    {
        $instance = PrescriptionEquipment
            ::whereRelation("prescription", "user_id", auth()->id())
            ->where('prescription_id', $prescription->id)
            ->where('equipment_id', $equipment->id)
            ->firstOrFail();
        return response()->json($instance);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Prescription $prescription, Equipment $equipment): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'add' => ['nullable', 'string'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $inputs = $validator->safe()->all();
        $instance = PrescriptionEquipment
            ::whereRelation("prescription", "user_id", auth()->id())
            ->where('prescription_id', $prescription->id)
            ->where('equipment_id', $equipment->id)
            ->update($inputs);
        return response()->json($instance);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prescription $prescription, Equipment $equipment): JsonResponse
    {
        $instance = PrescriptionEquipment
            ::whereRelation("prescription", "user_id", auth()->id())
            ->where('prescription_id', $prescription->id)
            ->where('equipment_id', $equipment->id)
            ->delete();
        return response()->json();
    }
}
