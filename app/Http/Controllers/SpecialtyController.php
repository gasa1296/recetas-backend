<?php

namespace App\Http\Controllers;

use App\Http\Requests\SpecialtyRequest;
use App\Http\Resources\SpecialtyResource;
use Illuminate\Http\JsonResponse;

class SpecialtyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $specialty = auth()->user()->specialty;

        return $this->success(
            __('messages.operation_success'),
            new SpecialtyResource($specialty),
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SpecialtyRequest $request): JsonResponse
    {
        $specialty = auth()
            ->user()
            ->specialty()
            ->create($request->validated());

        return $this->success(
            __('messages.operation_success'),
            new SpecialtyResource($specialty),
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SpecialtyRequest $request): JsonResponse
    {
        $user = auth()->user();
        $specialty = $user->specialty;

        if ($specialty) {
            $specialty->update($request->validated());
        } else {
            $specialty = $user->specialty()->create($request->validated());
        }

        return $this->success(
            __('messages.operation_success'),
            new SpecialtyResource($specialty),
        );
    }
}
