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
    public function index()
    {
        $instances = PrescriptionEquipment::whereRelation("prescription", "user_id", auth()->id());
        return response()->json($instances->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     * @todo Add validations
     */
    public function store(Request $request): JsonResponse
    {
        if ($request->query('bulk', 0) == 0) {
            $instance = PrescriptionEquipment::create($request->all());
            return response()->json($instance);
        } else {
            /**
             * @todo test it
             */
            $instance = PrescriptionEquipment::insert($request->all());
            return response()->json($instance);
        }
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
            ->firstOrFail();
        $instance->update($request->all());
        return response()->json($prescription);
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
            ->firstOrFail();
        $instance->delete();
        return response()->json();
    }
}
