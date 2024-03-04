<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\Medicament;
use App\Models\Prescription;
use App\Models\PrescriptionMedicament;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @todo modify all routes without medicaments model
 */
class PrescriptionMedicamentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Prescription $prescription): JsonResponse
    {
        if ($prescription->user_id != auth()->id()) {
            return response()->json([], 404);
        }
        $instances = $prescription->medicaments();
        return response()->json($instances->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Prescription $prescription): JsonResponse
    {
        $validator = Validator::make($request->all(), [
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
        $inputs = $validator->safe()->all();
        $instance = $prescription->medicaments()->createMany($inputs);
        return response()->json($instance);
    }

    /**
     * Display the specified resource.
     */
    public function show(Prescription $prescription, int $medicament): JsonResponse
    {
        $instance = PrescriptionMedicament
            ::whereRelation("prescription", "user_id", auth()->id())
            ->where('prescription_id', $prescription->id)
            ->where('medicament_id', $medicament)
            ->firstOrFail();
        return response()->json($instance);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Prescription $prescription, int $medicament): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'add' => ['nullable', 'string'],
            'dose' => ['required', 'string'],
            'way' => ['required', 'string'],
            'frequency' => ['required', 'string'],
            'duration' => ['required', 'string'],
            'quantity' => ['required', 'numeric'],
            'name' => ['required', 'string'],
            'type' => ['required', 'string'],
            'group' => ['required', 'string'],
            'family' => ['required', 'string'],
            'salt' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $inputs = $validator->safe()->all();
        $instance = PrescriptionMedicament
            ::whereRelation("prescription", "user_id", auth()->id())
            ->where('prescription_id', $prescription->id)
            ->where('medicament_id', $medicament);
        $instance->update($inputs);
        return response()->json($instance);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prescription $prescription, int $medicament): JsonResponse
    {
        $instance = PrescriptionMedicament
            ::whereRelation("prescription", "user_id", auth()->id())
            ->where('prescription_id', $prescription->id)
            ->where('medicament_id', $medicament)
            ->delete();
        return response()->json();
    }
}
