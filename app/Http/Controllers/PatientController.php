<?php

namespace App\Http\Controllers;

use App\Http\Requests\Patient\StoreRequest;
use App\Http\Requests\Patient\UpdateRequest;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        if (! empty($request->search)) {
            $search = strtoupper($request->search);
            $instances = Patient::where('user_id', '=', auth()->id())
                ->where(function ($query) use ($operator, $search) {
                    $query->whereRaw('UPPER(patients.first_name) LIKE '."'%$search%'")
                        ->orWhereRaw("UPPER(patients.first_name) $operator ' ' $operator UPPER(patients.last_name1) LIKE '%$search%'")
                        ->orWhereRaw("UPPER(patients.first_name) $operator ' ' $operator UPPER(patients.last_name1) $operator ' ' $operator UPPER(patients.last_name2) LIKE '%$search%'")
                        ->orWhereRaw("UPPER(patients.email) LIKE '%$search%'")
                        ->orWhere('phone1', 'LIKE', "%$search%")
                        ->orWhere('phone2', 'LIKE', "%$search%")
                        ->orWhere(function ($query) use ($search) {
                            $query->whereHas('prescriptions', function ($query) use ($search) {
                                $query->where('code', '=', strtoupper($search));
                            });
                        });
                });

            return PatientResource::collection($instances->paginate(10))->response();
        } else {
            return PatientResource::collection(Patient::where('user_id', '=', auth()->id())->paginate(10))->response();
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $inputs = $request->validated();
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
    public function update(UpdateRequest $request, Patient $patient): JsonResponse
    {
        if ($patient->user_id != auth()->id()) {
            return response()->json([], 404);
        }
        $inputs = $request->validated();
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
