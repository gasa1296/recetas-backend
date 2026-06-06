<?php

namespace App\Http\Controllers;

use App\Models\Specialization;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\SpecializationRequest;
use Illuminate\Support\Facades\Storage;
use Validator;

class SpecializationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $user = auth()->user();
        $specializations = $user->specializations()->paginate(10);

        return response()->json($specializations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SpecializationRequest $request): JsonResponse
    {
        $user = auth()->user();
        
        $inputs = $request->validated();
        if ($request->file('logo')) {
            $inputs['logo'] = $request->file('logo')->store('medics/'.auth()->id(), 'public');
        }
        $specialization = $user->specializations()->create($inputs);

        return response()->json($specialization);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $specialization): JsonResponse
    {
        $user = auth()->user();
        $specialization = $user->specializations()->findOrFail($specialization);

        return response()->json($specialization);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SpecializationRequest $request, int $specialization): JsonResponse
    {
        $user = auth()->user();
        $specialization = $user->specializations()->findOrFail($specialization);

        $inputs = $request->validated();
        if ($request->file('logo')) {
            if (! empty($specialization->logo)) {
                Storage::delete($specialization->logo);
            }
            $inputs['logo'] = $request->file('logo')->store('medics/'.auth()->id(), 'public');
        }
        $specialization->update($inputs);

        return response()->json($specialization);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Specialization $specialization): JsonResponse
    {
        $user = auth()->user();
        $specialization = $user->specializations()->findOrFail($specialization);
        
        if (! empty($specialization->logo)) {
            Storage::delete($specialization->logo);
        }
        $specialization->delete();

        return response()->json();
    }
}
