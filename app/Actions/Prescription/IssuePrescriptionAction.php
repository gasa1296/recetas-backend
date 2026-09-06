<?php

namespace App\Actions\Prescription;

use App\Contracts\DigitalSignerInterface;
use App\Contracts\PrescriptionRendererInterface;
use App\Models\Prescription;
use App\Models\User;
use App\Notifications\PrescriptionReadyNotification;
use App\Services\Prescription\PrescriptionExpirationService;

class IssuePrescriptionAction
{
    public function __construct(
        protected PrescriptionExpirationService $expirationService,
        protected PrescriptionRendererInterface $renderer,
        protected DigitalSignerInterface $signer,
    ) {}

    /**
     * Complete and issue a draft prescription.
     *
     * @param  Prescription  $prescription  The prescription in draft state
     * @param  User  $doctor  The issuing doctor
     * @param  string|null  $signature  Optional base64 or handwritten signature
     * @param  bool  $saveSignature  Whether to save this signature on the user profile
     */
    public function execute(
        Prescription $prescription,
        User $doctor,
        ?string $signature = null,
        bool $saveSignature = false
    ): Prescription {
        $prescription->loadMissing(['user', 'patient', 'room', 'specialty', 'medicaments']);

        $expirationData = $this->expirationService->calculateExpiration($prescription);
        $expirationDays = $expirationData['days'];
        $expiresAt = $expirationData['expires_at'];

        $effectiveSignature = $signature ?: $doctor->saved_signature;

        if ($saveSignature && ! empty($signature)) {
            $doctor->update(['saved_signature' => $signature]);
        }

        $signatureForPdf = $expirationDays != 0 ? $effectiveSignature : null;
        $pdfContent = $this->renderer->render($prescription, $signatureForPdf);

        if ($expirationDays == 0) {
            $prescription->handleUploadFile($pdfContent);
            $prescription->update([
                'status' => config('custom.prescription.status_keys.active'),
                'expires_at' => $expiresAt,
            ]);

            return $prescription;
        }

        $signerName = trim("{$doctor->first_name} {$doctor->last_name}");
        $metadata = [
            'Name' => ! empty($signerName) ? $signerName : config('app.name'),
            'Location' => $prescription->room->address,
            'Reason' => 'Prescripción Médica #'.$prescription->id.' - '.$prescription->room->name,
            'ContactInfo' => $doctor->email,
        ];

        $signedPdf = $this->signer->signPdf($pdfContent, $doctor, $metadata);
        $prescription->handleUploadFile($signedPdf, 'signed');

        $prescription->update([
            'status' => config('custom.prescription.status_keys.active'),
            'expires_at' => $expiresAt,
        ]);

        $prescription->loadMissing('patient');
        if ($prescription->patient) {
            $prescription->patient->notify(new PrescriptionReadyNotification($prescription));
        }

        return $prescription;
    }
}
