<?php

namespace App\Http\Controllers;

use App\Models\Specialty;
use App\Http\Requests\SpecialtyRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\SpecialtyResource;

class SpecialtyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $specialties = auth()->user()->specialties()->paginate(10);
        return $this->success(data: SpecialtyResource::collection($specialties));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SpecialtyRequest $request): JsonResponse
    {
        $specialty = auth()->user()->specialties()->create($request->validated());
        return $this->success(data: new SpecialtyResource($specialty));
    }

    /**
     * Display the specified resource.
     */
    public function show(int $specialty): JsonResponse
    {
        $specialty = auth()->user()->specialties()->findOrFail($specialty);
        return $this->success(data: new SpecialtyResource($specialty));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SpecialtyRequest $request, int $specialty): JsonResponse
    {
        $specialty = auth()->user()->specialties()->findOrFail($specialty);
        $specialty->update($request->validated());
        return $this->success(data: new SpecialtyResource($specialty));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $specialty): JsonResponse
    {
        $specialty = auth()->user()->specialties()->findOrFail($specialty);
        $specialty->delete();
        return $this->success();
    }
}
