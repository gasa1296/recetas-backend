<?php

namespace App\Http\Controllers;

use App\Http\Resources\PatientResource;
use Illuminate\Http\Request;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        if( $request->search ){
            $instances = Patient::where('first_name', 'LIKE', "%$request->search%")
                ->orWhere(\DB::raw('CONCAT(first_name, " ", last_name1)'), 'LIKE', "%$request->search%")
                ->orWhere(\DB::raw('CONCAT(first_name, " ", last_name1, " ", last_name2)'), 'LIKE', "%$request->search%")
                ->orWhere('email', 'LIKE', "%$request->search%")
                ->orWhere('phone1', 'LIKE', "%$request->search%")
                ->orWhere('phone2', 'LIKE', "%$request->search%");
            return PatientResource::collection($instances->paginate(10))->response();
        } else {
            return PatientResource::collection(Patient::paginate(10))->response();
        }
    }

    /**
     * Store a newly created resource in storage.
     * @todo Add validations
     */
    public function store(Request $request): JsonResponse
    {
        $instance = Patient::create($request->all());
        return (new PatientResource($instance))->response();
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient): JsonResponse
    {
        return (new PatientResource($patient))->response();
    }

    /**
     * Update the specified resource in storage.
     * @todo Add validations
     */
    public function update(Request $request, Patient $patient): JsonResponse
    {
        $patient->update($request->all());
        return (new PatientResource($patient))->response();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient): JsonResponse
    {
        $patient->delete();
        return response()->json();
    }
}
