<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicPrescriptionController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(Request $request, string|int $prescription)
    {
        if (config('app.debug')) {
            $prescription = Prescription::where('id', $prescription)->orWhere('prescription_hash', $prescription)->firstOrFail();
        } else {
            $prescription = Prescription::where('prescription_hash', $prescription)->firstOrFail();
        }

        // Return JSON when explicitly requested via query parameter (e.g., ?format=json or ?json=1)
        if ($request->query('format') === 'json' || $request->has('json')) {
            return $this->jsonResponse($prescription);
        }

        // Default: return the PDF document inline
        $file = $prescription->signed_file ?? $prescription->unsigned_file;
        if ($file) {
            $path = Storage::disk('local')->path($file->path);
            if (file_exists($path)) {
                return response()->file($path, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="receta_'.$prescription->id.'.pdf"',
                ]);
            }
        }

        // Fallback: If no PDF file was generated yet (e.g. in test mocks or drafts), return JSON
        return $this->jsonResponse($prescription);
    }

    private function jsonResponse(Prescription $prescription)
    {
        $prescription->loadMissing(['medicaments', 'patient', 'room', 'specialty', 'user']);
        $isValid = $prescription->status == config('custom.prescription.status_keys.active') &&
            (! $prescription->expires_at || now()->lessThanOrEqualTo($prescription->expires_at));

        return $this->success(__('messages.operation_success'), [
            'id' => $prescription->id,
            'prescription_hash' => $prescription->prescription_hash,
            'status' => $prescription->status,
            'status_label' => config("custom.prescription.status.{$prescription->status}"),
            'is_valid' => $isValid,
            'expires_at' => $prescription->expires_at,
            'doctor' => [
                'name' => $prescription->user ? "{$prescription->user->first_name} {$prescription->user->last_name}" : null,
                'identification' => $prescription->user?->identification,
                'specialty' => $prescription->specialty?->name,
            ],
            'patient' => [
                'name' => $prescription->patient ? "{$prescription->patient->first_name} {$prescription->patient->last_name}" : null,
                'identification' => $prescription->patient?->identification,
            ],
            'room' => [
                'name' => $prescription->room?->name,
                'address' => $prescription->room?->address,
            ],
            'medicaments' => $prescription->medicaments->map(fn ($m) => [
                'active_ingredient' => $m->active_ingredient,
                'dosage' => $m->pivot->dosage,
                'frequency' => $m->pivot->frequency,
                'duration' => $m->pivot->duration,
                'medicament_quantity' => $m->pivot->medicament_quantity,
                'recommended_brand' => $m->pivot->recommended_brand,
            ]),
        ]);
    }

    /**
     * Mark a prescription as dispensed.
     */
    public function dispense(Request $request, string|int $prescription)
    {
        $request->validate([
            'mode' => 'nullable|string|in:full,partial',
        ]);

        if (config('app.debug')) {
            $prescription = Prescription::where('id', $prescription)->orWhere('prescription_hash', $prescription)->firstOrFail();
        } else {
            $prescription = Prescription::where('prescription_hash', $prescription)->firstOrFail();
        }

        $activeStatus = config('custom.prescription.status_keys.active');
        $partialStatus = config('custom.prescription.status_keys.partially_dispensed');
        $fullStatus = config('custom.prescription.status_keys.fully_dispensed');

        if ($prescription->status != $activeStatus && $prescription->status != $partialStatus) {
            return $this->error(__('messages.operation_failed'), [
                'status' => ['Prescription cannot be dispensed in its current state.'],
            ], 422);
        }

        if ($prescription->expires_at && now()->greaterThan($prescription->expires_at)) {
            return $this->error(__('messages.prescription_expired'), [], 422);
        }

        $newStatus = $request->input('mode') === 'partial' ? $partialStatus : $fullStatus;
        $prescription->update(['status' => $newStatus]);

        return $this->success(__('messages.operation_success'), [
            'status' => $prescription->status,
            'status_label' => config("custom.prescription.status.{$prescription->status}"),
        ]);
    }
}
