<?php

namespace App\Http\Controllers;

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
    public function index(Prescription $prescription)
    {
        if ($prescription->user_id != auth()->id()) {
            return response()->json([], 404);
        }
        $instances = $prescription->equipment();
        return response()->json($instances->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     * @todo Add validations
     */
    public function store(Request $request, Prescription $prescription): JsonResponse
    {
        $inputs = $request->all();
        $bulk = $request->query('bulk', 0);
        if ($bulk == 0) {
            $instance = $prescription->equipment()->create($inputs);
        } else {
            unset($inputs['bulk']);
            $instance = $prescription->equipment()->createMany($inputs);
        }
        return response()->json($instance);
    }

    /**
     * Display the specified resource.
     */
    public function show(Prescription $prescription, Equipment $equipment)
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
     * @todo Add validations
     */
    public function update(Request $request, Prescription $prescription, Equipment $equipment)
    {
        $instance = PrescriptionEquipment
            ::whereRelation("prescription", "user_id", auth()->id())
            ->where('prescription_id', $prescription->id)
            ->where('equipment_id', $equipment->id)
            ->update($request->all());
        return response()->json($instance);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prescription $prescription, Equipment $equipment)
    {
        $instance = PrescriptionEquipment
            ::whereRelation("prescription", "user_id", auth()->id())
            ->where('prescription_id', $prescription->id)
            ->where('equipment_id', $equipment->id)
            ->delete();
        return response()->json();
    }
}
