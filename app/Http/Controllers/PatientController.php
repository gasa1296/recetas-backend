<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\PatientCollection;
use App\Http\Resources\PatientResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SearchRequest $request): JsonResponse
    {
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
        $patient = $request->user()->patients()->create($request->validated());

        return $this->success(__('messages.operation_success'), new PatientResource($patient));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, int $patient): JsonResponse
    {
        $patient = $request->user()->patients()->findOrFail($patient);

        return $this->success(__('messages.operation_success'), new PatientResource($patient));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PatientRequest $request, int $patient): JsonResponse
    {
        $patient = $request->user()->patients()->findOrFail($patient);
        $patient->update($request->validated());

        return $this->success(__('messages.operation_success'), new PatientResource($patient));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, int $patient): JsonResponse
    {
        $patient = $request->user()->patients()->findOrFail($patient);
        $patient->delete();

        return $this->success(__('messages.operation_success'));
    }
}
