<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\PatientResource;
use App\Http\Resources\PatientCollection;
use Illuminate\Http\JsonResponse;
use App\Models\Patient;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SearchRequest $request): JsonResponse
    {
        $patients = Patient::orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $search = $request->input('search');
            $patients = $patients->where('identification', 'LIKE', "%{$search}%");
        }

        return (new PatientCollection($patients->paginate(10)))->response();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PatientRequest $request): JsonResponse
    {
        $patients = Patient::create($request->validated());

        return $this->success(__('messages.operation_success'), new PatientResource($patients));
    }

    /**
     * Display the specified resource.
     */
    public function show(int $patient): JsonResponse
    {
        $patients = Patient::findOrFail($patient);

        return $this->success(__('messages.operation_success'), new PatientResource($patients));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PatientRequest $request, int $patient): JsonResponse
    {
        $patients = Patient::findOrFail($patient);
        $patients->update($request->validated());

        return $this->success(__('messages.operation_success'), new PatientResource($patients));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $patient): JsonResponse
    {
        $patients = Patient::findOrFail($patient);
        $patients->delete();

        return $this->success(__('messages.operation_success'));
    }
}
