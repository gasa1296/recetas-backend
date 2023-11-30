<?php

namespace App\Http\Controllers;

use App\Http\Resources\PrescriptionResource;
use App\Models\Prescription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     * @todo add search
     */
    public function index(): JsonResponse
    {
        return PrescriptionResource::collection(Prescription::paginate(10))->response();
    }

    /**
     * Store a newly created resource in storage.
     * @todo upload file
     * @todo Add validations
     */
    public function store(Request $request): JsonResponse
    {
        $inputs = $request->all();
        $inputs['user_id'] = auth()->id();
        $instance = Prescription::create($inputs);

        return (new PrescriptionResource($instance))->response();
    }

    /**
     * Display the specified resource.
     */
    public function show(Prescription $prescription): JsonResponse
    {
        if ($prescription->user_id != auth()->id()) {
            return response()->json([],404);
        }
        return (new PrescriptionResource($prescription))->response();
    }

    /**
     * Update the specified resource in storage.
     * @todo upload file
     * @todo Add validations
     */
    public function update(Request $request, Prescription $prescription): JsonResponse
    {
        if ($prescription->user_id != auth()->id()) {
            return response()->json([], 404);
        }
        $prescription->update($request->all());
        return (new PrescriptionResource($prescription))->response();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prescription $prescription)
    {
        if ($prescription->user_id != auth()->id()) {
            return response()->json([], 404);
        }
        $prescription->delete();
        return response()->json();
    }
}
