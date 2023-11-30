<?php

namespace App\Http\Controllers;

use App\Models\Medicament;
use App\Models\Prescription;
use App\Models\PrescriptionMedicament;
use Illuminate\Http\Request;

/**
 * @todo verify querys
 */
class PrescriptionMedicamentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Prescription $prescription)
    {
        if ($prescription->user_id != auth()->id()) {
            return response()->json([], 404);
        }
        $instances = $prescription->medicaments;
        return response()->json($instances->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     * @todo Add validations
     */
    public function store(Request $request, Prescription $prescription)
    {
        if ($request->query('bulk', 0) == 0) {
            $instance = $prescription->medicaments()->create($request->all());
        } else {
            $instance = $prescription->medicaments()->createMany($request->all());
        }
        return response()->json($instance);
    }

    /**
     * Display the specified resource.
     */
    public function show(Prescription $prescription, Medicament $medicament)
    {
        $instance = PrescriptionMedicament
            ::whereRelation("prescription", "user_id", auth()->id())
            ->where('prescription_id', $prescription->id)
            ->where('medicament_id', $medicament->id)
            ->firstOrFail();
        return response()->json($instance);
    }

    /**
     * Update the specified resource in storage.
     * @todo Add validations
     */
    public function update(Request $request, Prescription $prescription, Medicament $medicament)
    {
        $instance = PrescriptionMedicament
            ::whereRelation("prescription", "user_id", auth()->id())
            ->where('prescription_id', $prescription->id)
            ->where('medicament_id', $medicament->id)
            ->firstOrFail();
        $instance->update($request->all());
        return response()->json($prescription);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prescription $prescription, Medicament $medicament)
    {
        $instance = PrescriptionMedicament
            ::whereRelation("prescription", "user_id", auth()->id())
            ->where('prescription_id', $prescription->id)
            ->where('medicament_id', $medicament->id)
            ->firstOrFail();
        $instance->delete();
        return response()->json();
    }
}
