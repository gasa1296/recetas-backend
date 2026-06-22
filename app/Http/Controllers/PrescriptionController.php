<?php

namespace App\Http\Controllers;

use App\Http\Requests\PrescriptionRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\PrescriptionCollection;
use App\Http\Resources\PrescriptionResource;
use App\Models\Prescription;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class PrescriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SearchRequest $request): JsonResponse
    {
        $prescriptions = auth()->user()->prescriptions()->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $search = $request->input('search');
            $prescriptions = $prescriptions->whereLike('diagnostic', "%$search%", false);
        }

        return (new PrescriptionCollection($prescriptions->paginate(10)))->response();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PrescriptionRequest $request): JsonResponse
    {
        try {
            return DB::transaction(function () use ($request) {
                $data = $request->validated();
                $data['prescription_hash'] = hash('sha256', json_encode($data));

                $prescription = auth()
                    ->user()
                    ->prescriptions()
                    ->create($data);
                $medicaments = $request->input('medicament_data', []);

                $prescription->medicaments()->sync($medicaments);

                return $this->success(
                    __('messages.operation_success'),
                    new PrescriptionResource(
                        $prescription->load(['medicaments', 'patient', 'room', 'specialty']),
                    ),
                );
            });
        } catch (Throwable $e) {
            if ($e instanceof ModelNotFoundException) {
                throw $e;
            }

            return $this->error(__('messages.operation_failed'));
        }
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
                $prescription->load(['medicaments', 'patient', 'room', 'specialty']),
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
        try {
            return DB::transaction(function () use ($request, $prescription) {
                $prescription = auth()
                    ->user()
                    ->prescriptions()
                    ->where('status', config('custom.prescription.status_keys.draft'))
                    ->lockForUpdate()
                    ->findOrFail($prescription);
                $prescription->update($request->validated());
                $medicaments = $request->input('medicament_data', []);
                $prescription->medicaments()->sync($medicaments);

                return $this->success(
                    __('messages.operation_success'),
                    new PrescriptionResource(
                        $prescription->load(['medicaments', 'patient', 'room', 'specialty']),
                    ),
                );
            });
        } catch (Throwable $e) {
            if ($e instanceof ModelNotFoundException) {
                throw $e;
            }

            return $this->error(__('messages.operation_failed'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $prescription): JsonResponse
    {
        try {
            return DB::transaction(function () use ($prescription) {
                $prescription = auth()
                    ->user()
                    ->prescriptions()
                    ->where('status', config('custom.prescription.status_keys.draft'))
                    ->lockForUpdate()
                    ->findOrFail($prescription);
                $prescription->delete();

                return $this->success(
                    __('messages.operation_success'),
                );
            });
        } catch (Throwable $e) {
            if ($e instanceof ModelNotFoundException) {
                throw $e;
            }

            return $this->error(__('messages.operation_failed'));
        }
    }

    public function finishPrescription(int $prescription): JsonResponse
    {
        try {
            return DB::transaction(function () use ($prescription) {
                $prescription = auth()
                    ->user()
                    ->prescriptions()
                    ->where('status', config('custom.prescription.status_keys.draft'))
                    ->lockForUpdate()
                    ->findOrFail($prescription);
                if (! $this->generatePDF($prescription)) {
                    return $this->error(__('messages.operation_failed'));
                }
                $prescription->update(['status' => config('custom.prescription.status_keys.active')]);

                return $this->success(
                    __('messages.operation_success'),
                );
            });
        } catch (Throwable $e) {
            if ($e instanceof ModelNotFoundException) {
                throw $e;
            }

            return $this->error(__('messages.operation_failed'));
        }
    }

    private function generatePDF(Prescription $prescription): bool
    {
        $prescription->loadMissing(['user', 'patient', 'room', 'specialty', 'medicaments']);
        $pdf = Pdf::loadView('pdf.prescription_model_1', ['prescription' => $prescription]);

        return $prescription->handleUploadFile($pdf->output());
    }
}
