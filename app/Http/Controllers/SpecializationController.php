<?php

namespace App\Http\Controllers;

use App\Models\Specialization;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class SpecializationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $instances = Specialization::where("user_id", auth()->id());
        return response()->json($instances->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     * @todo Add validations
     */
    public function store(Request $request): JsonResponse
    {
        $inputs = $request->all();
        if ($request->file('logo')) {
            $inputs['logo'] = $request->file('logo')->store('logos');
        }
        $inputs["user_id"] = auth()->id();
        $instance = Specialization::create($inputs);
        return response()->json($instance);
    }

    /**
     * Display the specified resource.
     */
    public function show(Specialization $specialization): JsonResponse
    {
        if ($specialization->user_id != auth()->id()) {
            return response()->json([], 404);
        }
        return response()->json($specialization);
    }

    /**
     * Update the specified resource in storage.
     * @todo Add validations
     */
    public function update(Request $request, Specialization $specialization): JsonResponse
    {
        if ($specialization->user_id != auth()->id()) {
            return response()->json([], 404);
        }
        $inputs = $request->all();
        if ($request->file('logo')) {
            $inputs['logo'] = $request->file('logo')->store('logos');
            if ($inputs['logo']) {
                Storage::delete($specialization->image);
            }
        }
        $specialization->update($inputs);
        return response()->json($specialization);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Specialization $specialization): JsonResponse
    {
        if ($specialization->user_id != auth()->id()) {
            return response()->json([], 404);
        }
        Storage::delete($specialization->logo);
        $specialization->delete();
        return response()->json();
    }
}
