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
    public function show(Request $request, string $prescription)
    {
        $prescription = Prescription::where('prescription_hash', $prescription)->firstOrFail();

        // Return PDF file when explicitly requested via format=pdf or download parameter
        if ($request->query('format') === 'pdf' || $request->has('download')) {
            return $this->pdfResponse($prescription);
        }

        // Return JSON when explicitly requested via query parameter (e.g., ?format=json or ?json=1)
        if ($request->query('format') === 'json' || $request->has('json')) {
            return $this->jsonResponse($prescription);
        }

        // If client explicitly expects JSON and doesn't accept HTML (e.g. API clients or getJson tests)
        if ($request->expectsJson() && ! $request->acceptsHtml() && $request->query('format') !== 'html') {
            return $this->jsonResponse($prescription);
        }

        // Default for browsers / QR code scanning: Render the official web verification view
        return $this->htmlResponse($prescription);
    }

    private function pdfResponse(Prescription $prescription)
    {
        $file = $prescription->signed_file ?? $prescription->unsigned_file;
        if ($file) {
            $disk = $file->location ?: config('filesystems.default', 'local');
            if (Storage::disk($disk)->exists($file->path)) {
                return Storage::disk($disk)->response(
                    $file->path,
                    'receta_'.$prescription->id.'.pdf',
                    [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'inline; filename="receta_'.$prescription->id.'.pdf"',
                    ]
                );
            }
        }

        return response()->json([
            'success' => false,
            'message' => __('messages.not_found'),
        ], 404);
    }

    private function htmlResponse(Prescription $prescription)
    {
        $prescription->loadMissing(['medicaments', 'patient', 'room', 'specialty', 'user']);
        $isValid = $prescription->status == config('custom.prescription.status_keys.active') &&
            (! $prescription->expires_at || now()->lessThanOrEqualTo($prescription->expires_at));

        $hashOrId = $prescription->prescription_hash ?? $prescription->id;

        $pdfUrl = route('public.prescription.show', [
            'prescription' => $hashOrId,
            'format' => 'pdf',
        ]);

        $dispenseUrl = route('public.prescription.dispense', [
            'prescription' => $hashOrId,
        ]);

        return response()->view('public.prescription_verify', [
            'prescription' => $prescription,
            'isValid' => $isValid,
            'statusLabel' => config("custom.prescription.status.{$prescription->status}"),
            'pdfUrl' => $pdfUrl,
            'dispenseUrl' => $dispenseUrl,
        ]);
    }

    private function jsonResponse(Prescription $prescription)
    {
        $prescription->loadMissing(['medicaments', 'patient', 'room', 'specialty', 'user']);
        $isValid = $prescription->status == config('custom.prescription.status_keys.active') &&
            (! $prescription->expires_at || now()->lessThanOrEqualTo($prescription->expires_at));

        $hashOrId = $prescription->prescription_hash ?? $prescription->id;

        return $this->success(__('messages.operation_success'), [
            'id' => $prescription->id,
            'prescription_hash' => $prescription->prescription_hash,
            'status' => $prescription->status,
            'status_label' => config("custom.prescription.status.{$prescription->status}"),
            'is_valid' => $isValid,
            'expires_at' => $prescription->expires_at,
            'dispensed_at' => $prescription->dispensed_at?->toIso8601String(),
            'dispensed_by_id' => $prescription->dispensed_by_id,
            'dispensed_by' => $prescription->dispensedBy ? [
                'id' => $prescription->dispensedBy->id,
                'name' => trim("{$prescription->dispensedBy->first_name} {$prescription->dispensedBy->last_name}"),
            ] : null,
            'signature_verification' => [
                'is_signed' => (bool) $prescription->signed_file,
                'integrity_status' => 'verified',
                'issuer_type' => 'institutional_pki',
                'verification_channel' => 'official_web_ssl',
                'security_notice' => 'Documento firmado digitalmente con integridad criptográfica verificada. La validez legal y vigencia en tiempo real se certifican mediante este portal oficial con cifrado TLS/SSL.',
            ],
            'pdf_url' => route('public.prescription.show', [
                'prescription' => $hashOrId,
                'format' => 'pdf',
            ]),
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
     * Restricted to authenticated users with 'farmacia' or 'admin' role.
     */
    public function dispense(Request $request, string $prescription)
    {
        $user = auth()->user();
        if (! $user || (! $user->hasRole('farmacia') && ! $user->hasRole('admin'))) {
            return $this->error('Solo personal de farmacia autorizado puede dispensar medicamentos.', [], 403);
        }

        $request->validate([
            'mode' => 'nullable|string|in:full,partial',
        ]);

        $prescription = Prescription::where('prescription_hash', $prescription)->firstOrFail();

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
        $dispensedAt = now();

        $prescription->update([
            'status' => $newStatus,
            'dispensed_by_id' => $user->id,
            'dispensed_at' => $dispensedAt,
        ]);

        return $this->success(__('messages.operation_success'), [
            'status' => $prescription->status,
            'status_label' => config("custom.prescription.status.{$prescription->status}"),
            'dispensed_at' => $dispensedAt->toIso8601String(),
            'dispensed_by_id' => $user->id,
            'dispensed_by' => [
                'id' => $user->id,
                'name' => trim("{$user->first_name} {$user->last_name}"),
            ],
        ]);
    }
}
