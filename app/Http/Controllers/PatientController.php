<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $instances = Patient::all();
        return response()->json($instances->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $instance = Patient::create($request->all());
        return response()->json($instance);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $instance = Patient::findOrFail($id);
        return response()->json($instance);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $instance = Patient::findOrFail($id);
        $instance->update($request->all());
        return response()->json($instance);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        Patient::findOrFail($id)->delete();
        return response()->json();
    }
}
