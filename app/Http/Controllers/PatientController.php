<?php

namespace App\Http\Controllers;

use Validator;
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
        $operator = '||';
        if (env('DB_CONNECTION') == 'sqlsrv') {
            $operator = '+';
        }
        if( $request->search ){
            $search = $request->search;
            $instances = Patient::where('user_id', '=', auth()->id())
                ->where(function($query) use ($operator, $search) {
                    $query->where('first_name', 'LIKE', "%$search%")
                        ->orWhere(\DB::raw("first_name $operator ' ' $operator last_name1"), 'LIKE', "%$search%")
                        ->orWhere(\DB::raw("first_name $operator ' ' $operator last_name1 $operator ' ' $operator last_name2"), 'LIKE', "%$search%")
                        ->orWhere('email', 'LIKE', "%$search%")
                        ->orWhere('phone1', 'LIKE', "%$search%")
                        ->orWhere('phone2', 'LIKE', "%$search%");
                });
            return PatientResource::collection($instances->paginate(10))->response();
        } else {
            return PatientResource::collection(Patient::where('user_id', '=', auth()->id())->paginate(10))->response();
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string'],
            'last_name1' => ['required', 'string'],
            'last_name2' => ['nullable', 'string'],
            'email' => ['required', 'email'],
            'phone1' => ['required', 'string'],
            'phone2' => ['nullable', 'string'],
            'gender' => ['required', 'string'],
            'birth_date' => ['required', 'date'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $inputs = $validator->safe()->all();
        $inputs['user_id'] = auth()->id();
        $instance = Patient::create($inputs);
        return (new PatientResource($instance))->response();
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient): JsonResponse
    {
        if ($patient->user_id != auth()->id()) {
            return response()->json([], 404);
        }
        return (new PatientResource($patient))->response();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient): JsonResponse
    {
        if ($patient->user_id != auth()->id()) {
            return response()->json([], 404);
        }
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string'],
            'last_name1' => ['required', 'string'],
            'last_name2' => ['nullable', 'string'],
            'email' => ['required', 'email'],
            'phone1' => ['required', 'string'],
            'phone2' => ['nullable', 'string'],
            'gender' => ['required', 'string'],
            'birth_date' => ['required', 'date'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $inputs = $validator->safe()->all();
        $patient->update($inputs);
        return (new PatientResource($patient))->response();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient): JsonResponse
    {
        if ($patient->user_id != auth()->id()) {
            return response()->json([], 404);
        }
        $patient->delete();
        return response()->json();
    }
}
