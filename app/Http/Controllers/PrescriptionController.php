<?php

namespace App\Http\Controllers;

use App\Http\Requests\PrescriptionRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\PrescriptionResource;
use Illuminate\Http\JsonResponse;

class PrescriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SearchRequest $request): JsonResponse
    {
        $prescriptions = auth()->user()->prescriptions();
        if (! $request->has('search')) {
            $prescriptions = $prescriptions->paginate(10);

            return $this->success(
                __('messages.operation_success'),
                PrescriptionResource::collection($prescriptions),
            );
        }
        $search = $request->input('search');
        $prescriptions = $prescriptions
            ->whereLike('description', "%$search%", false)
            ->paginate(10);

        return $this->success(
            __('messages.operation_success'),
            PrescriptionResource::collection($prescriptions),
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PrescriptionRequest $request): JsonResponse
    {
        $prescription = auth()
            ->user()
            ->prescriptions()
            ->create($request->validated());
        $medicaments = $request->input('medicament_ids', []);
        $prescription->medicaments()->sync($medicaments);

        return $this->success(
            __('messages.operation_success'),
            new PrescriptionResource(
                $prescription->load(['medicaments', 'patient', 'room']),
            ),
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(int $prescription): JsonResponse
    {
        $prescription = auth()
            ->user()
            ->prescriptions()
            ->findOrFail($prescription);

        return $this->success(
            __('messages.operation_success'),
            new PrescriptionResource(
                $prescription->load(['medicaments', 'patient', 'room']),
            ),
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        PrescriptionRequest $request,
        int $prescription,
    ): JsonResponse {
        $prescription = auth()
            ->user()
            ->prescriptions()
            ->where('status', 'pending')
            ->findOrFail($prescription);
        $prescription->update($request->validated());
        $medicaments = $request->input('medicament_ids', []);
        $prescription->medicaments()->sync($medicaments);

        return $this->success(
            __('messages.operation_success'),
            new PrescriptionResource(
                $prescription->load(['medicaments', 'patient', 'room']),
            ),
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $prescription): JsonResponse
    {
        $prescription = auth()
            ->user()
            ->prescriptions()
            ->where('status', 'pending')
            ->findOrFail($prescription);
        $prescription->delete();

        return $this->success(
            __('messages.operation_success'),
        );
    }
}
