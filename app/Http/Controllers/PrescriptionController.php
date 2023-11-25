<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $instances = Prescription::all();
        return response()->json($instances->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     * @todo upload file
     * @todo Add validations
     */
    public function store(Request $request)
    {
        $inputs = $request->all();
        $inputs['user_id'] = auth()->id();
        $instance = Prescription::create($inputs);
        return response()->json($instance);
    }

    /**
     * Display the specified resource.
     */
    public function show(Prescription $prescription)
    {
        if ($prescription->user_id != auth()->id()) {
            return response()->json([],404);
        }
        return response()->json($prescription);
    }

    /**
     * Update the specified resource in storage.
     * @todo upload file
     * @todo Add validations
     */
    public function update(Request $request, Prescription $prescription)
    {
        if ($prescription->user_id != auth()->id()) {
            return response()->json([], 404);
        }
        $prescription->update($request->all());
        return response()->json($prescription);
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
