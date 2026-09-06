<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\PatientCollection;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SearchRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Patient::class);

        $patients = $request->user()->patients()->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $search = $request->input('search');
            $patients = $patients->where(function ($q) use ($search) {
                $q->where('identification', 'LIKE', "%{$search}%")
                    ->orWhere('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%");
            });
        }

        $perPage = $request->integer('per_page', 10);

        return (new PatientCollection($patients->paginate($perPage)))->response();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PatientRequest $request): JsonResponse
    {
        $this->authorize('create', Patient::class);

        $patient = $request->user()->patients()->create($request->validated());

        return $this->success(__('messages.operation_success'), new PatientResource($patient));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, int $patient): JsonResponse
    {
        $patientModel = $request->user()->hasRole('admin')
            ? Patient::findOrFail($patient)
            : $request->user()->patients()->findOrFail($patient);

        $this->authorize('view', $patientModel);

        return $this->success(__('messages.operation_success'), new PatientResource($patientModel));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PatientRequest $request, int $patient): JsonResponse
    {
        $patientModel = $request->user()->hasRole('admin')
            ? Patient::findOrFail($patient)
            : $request->user()->patients()->findOrFail($patient);

        $this->authorize('update', $patientModel);
        $patientModel->update($request->validated());

        return $this->success(__('messages.operation_success'), new PatientResource($patientModel));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, int $patient): JsonResponse
    {
        $patientModel = $request->user()->hasRole('admin')
            ? Patient::findOrFail($patient)
            : $request->user()->patients()->findOrFail($patient);

        $this->authorize('delete', $patientModel);
        $patientModel->delete();

        return $this->success(__('messages.operation_success'));
    }
}
