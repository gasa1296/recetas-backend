<?php

namespace App\Http\Controllers;

use App\Actions\Prescription\IssuePrescriptionAction;
use App\Http\Requests\FinishPrescriptionRequest;
use App\Http\Requests\PrescriptionRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\PrescriptionCollection;
use App\Http\Resources\PrescriptionResource;
use App\Notifications\PrescriptionReadyNotification;
use App\Services\Media\FileStorageService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class PrescriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SearchRequest $request): JsonResponse
    {
        $prescriptions = auth()->user()->prescriptions()
            ->with(['medicaments', 'patient', 'room', 'specialty'])
            ->orderBy('created_at', 'desc');

        if ($request->has('status') && $request->input('status') !== null && $request->input('status') !== '') {
            $prescriptions->where('status', $request->integer('status'));
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $prescriptions = $prescriptions->where(function ($q) use ($search) {
                $q->whereLike('diagnostic', "%$search%", false)
                    ->orWhereHas('patient', function ($pq) use ($search) {
                        $pq->whereLike('first_name', "%$search%", false)
                            ->orWhereLike('last_name', "%$search%", false)
                            ->orWhereLike('identification', "%$search%", false);
                    });
            });
        }

        return (new PrescriptionCollection($prescriptions->paginate(10)))->response();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PrescriptionRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['prescription_hash'] = hash('sha256', json_encode($data).Str::random(16).microtime(true));

        $prescription = auth()
            ->user()
            ->prescriptions()
            ->create($data);
        if (! empty($data['medicaments'])) {
            $prescription->medicaments()->sync($data['medicaments']);
        }

        return $this->success(
            __('messages.operation_success'),
            new PrescriptionResource(
                $prescription->load(['medicaments', 'patient', 'room', 'specialty']),
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
                $prescription->load(['medicaments', 'patient', 'room', 'specialty', 'user']),
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
            ->where('status', config('custom.prescription.status_keys.draft'))
            ->findOrFail($prescription);
        $data = $request->validated();
        $prescription->update($data);

        if (! empty($data['medicaments'])) {
            $prescription->medicaments()->sync($data['medicaments']);
        }

        return $this->success(
            __('messages.operation_success'),
            new PrescriptionResource(
                $prescription->load(['medicaments', 'patient', 'room', 'specialty']),
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
            ->where('status', config('custom.prescription.status_keys.draft'))
            ->lockForUpdate()
            ->findOrFail($prescription);
        $prescription->delete();

        return $this->success(
            __('messages.operation_success'),
        );
    }

    public function finishPrescription(
        FinishPrescriptionRequest $request,
        int $prescription,
        IssuePrescriptionAction $issuePrescription
    ): JsonResponse {
        $user = auth()->user();
        $prescriptionModel = $user
            ->prescriptions()
            ->where('status', config('custom.prescription.status_keys.draft'))
            ->findOrFail($prescription);

        $issued = $issuePrescription->execute(
            $prescriptionModel,
            $user,
            $request->input('signature'),
            $request->boolean('save_signature') && $request->filled('signature')
        );

        return $this->success(
            __('messages.operation_success'),
            new PrescriptionResource(
                $issued->load(['medicaments', 'patient', 'room', 'specialty', 'user']),
            ),
        );
    }

    public function nullPrescription(int $prescription): JsonResponse
    {
        $prescription = auth()
            ->user()
            ->prescriptions()
            ->where('status', config('custom.prescription.status_keys.active'))
            ->findOrFail($prescription);

        $prescription->update([
            'status' => config('custom.prescription.status_keys.nulled'),
        ]);

        return $this->success(
            __('messages.operation_success'),
            new PrescriptionResource(
                $prescription->load(['medicaments', 'patient', 'room', 'specialty', 'user']),
            ),
        );
    }

    /**
     * Display the specified resource.
     */
    public function getFile(string $prescription, FileStorageService $fileStorage)
    {
        $prescription = auth()
            ->user()
            ->prescriptions()
            ->where('status', config('custom.prescription.status_keys.active'))
            ->whereNotNull('expires_at')
            ->with(['signed_file', 'unsigned_file'])
            ->findOrFail($prescription);

        // If expires_at date equals updated_at date, prefer unsigned file path
        $useUnsigned = false;
        $expiresDate = Carbon::parse($prescription->expires_at)->toDateString();
        $updatedDate = Carbon::parse($prescription->updated_at)->toDateString();
        if ($expiresDate === $updatedDate) {
            $useUnsigned = true;
        }
        $file = ($useUnsigned ? $prescription->unsigned_file : $prescription->signed_file)
            ?? $prescription->signed_file
            ?? $prescription->unsigned_file;

        if (! $file || ! $fileStorage->exists($file)) {
            return $this->error(
                __('messages.not_found'),
                404
            );
        }

        return $fileStorage->response($file, "receta_{$prescription->id}.pdf", [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Resend the prescription ready notification email with signed PDF to the patient.
     */
    public function resend(int $prescription): JsonResponse
    {
        $user = auth()->user();
        $prescription = $user->prescriptions()
            ->with(['patient', 'signed_file'])
            ->findOrFail($prescription);

        if ((int) $prescription->status !== (int) config('custom.prescription.status_keys.active')) {
            return $this->error(
                'Solo se pueden reenviar recetas médicas emitidas y activas.',
                [],
                422
            );
        }

        if (empty($prescription->patient?->email)) {
            return $this->error(
                'El paciente no tiene una dirección de correo electrónico registrada para el reenvío.',
                [],
                422
            );
        }

        $prescription->patient->notify(new PrescriptionReadyNotification($prescription));

        return $this->success(
            __('messages.operation_success')
        );
    }
}
