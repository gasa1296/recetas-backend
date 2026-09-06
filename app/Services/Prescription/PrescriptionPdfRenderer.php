<?php

namespace App\Services\Prescription;

use App\Contracts\PrescriptionRendererInterface;
use App\Models\Prescription;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class PrescriptionPdfRenderer implements PrescriptionRendererInterface
{
    /**
     * Render a prescription model into raw PDF binary content.
     */
    public function render(Prescription $prescription, ?string $signature = null): string
    {
        $verificationUrl = route('public.prescription.show', $prescription->prescription_hash);
        $qrCode = $this->generateQrCode($verificationUrl);

        return Pdf::loadView('pdf.prescription_model_1', [
            'prescription' => $prescription,
            'signature' => $signature,
            'qrCode' => $qrCode,
        ])->output();
    }

    /**
     * Generate a QR code PNG data URI for the given URL.
     */
    public function generateQrCode(string $url): string
    {
        $qrOptions = new QROptions;
        $qrOptions->outputType = 'png';
        $qrOptions->scale = 5;

        return (new QRCode($qrOptions))->render($url);
    }
}
