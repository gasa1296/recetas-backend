<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientRequest;
use App\Http\Resources\PatientResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();
        $patients = $user->patients();

        // TODO: search by name, email, phone, prescription code
        $operator = '||';
        if (config('database.default') == 'sqlsrv') {
            $operator = '+';
        }
        if (! empty($request->search)) {
            $search = strtoupper($request->search);
            $patients = $patients->where('user_id', '=', auth()->id())
                ->where(function ($query) use ($operator, $search) {
                    $query->orWhereRaw("UPPER(patients.first_name) $operator ' ' $operator UPPER(patients.last_name1) $operator ' ' $operator UPPER(patients.last_name2) LIKE '%$search%'")
                        ->orWhereRaw("UPPER(patients.email) LIKE '%$search%'")
                        ->orWhere(function ($query) use ($search) {
                            $query->whereHas('prescriptions', function ($query) use ($search) {
                                $query->where('code', '=', strtoupper($search));
                            });
                        });
                });

            return PatientResource::collection($patients->paginate(10))->response();
        } else {
            return PatientResource::collection($patients->paginate(10))->response();
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PatientRequest $request): JsonResponse
    {
        $user = auth()->user();

        $inputs = $request->validated();
        $patient = $user->patients()->create($inputs);

        return (new PatientResource($patient))->response();
    }

    /**
     * Display the specified resource.
     */
    public function show(int $patient): JsonResponse
    {
        $user = auth()->user();
        $patient = $user->patients()->findOrFail($patient);

        return (new PatientResource($patient))->response();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PatientRequest $request, int $patient): JsonResponse
    {
        $user = auth()->user();
        $patient = $user->patients()->findOrFail($patient);

        $inputs = $request->validated();
        $patient->update($inputs);

        return (new PatientResource($patient))->response();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $patient): JsonResponse
    {
        $user = auth()->user();
        $patient = $user->patients()->findOrFail($patient);
        $patient->delete();

        return response()->json();
    }
}
