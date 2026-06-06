<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Http\Requests\PatientRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\PatientResource;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SearchRequest $request): JsonResponse
    {
        $patients = auth()->user()->patients();
        if (!$request->has('search')) {
            $patients = $patients->paginate(10);
            return $this->success(data: new PatientResource($patients));
        }

        $search = $request->input('search');
        $patients = $patients->whereLike('name', "%$search%", false)->paginate(10);

        return $this->success(data: new PatientResource($patients));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PatientRequest $request): JsonResponse
    {
        $patients = auth()->user()->patients()->create($request->validated());

        return $this->success(data: new PatientResource($patients));
    }

    /**
     * Display the specified resource.
     */
    public function show(int $patient): JsonResponse
    {
        $patients = auth()->user()->patients()->findOrFail($patient);

        return $this->success(data: new PatientResource($patients));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PatientRequest $request, int $patient): JsonResponse
    {
        $patients = auth()->user()->patients()->findOrFail($patient);
        $patients->update($request->validated());

        return $this->success(data: new PatientResource($patients));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $patient): JsonResponse
    {
        $patients = auth()->user()->patients()->findOrFail($patient);
        $patients->delete();

        return $this->success();
    }
}
