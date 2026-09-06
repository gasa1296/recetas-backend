<?php

namespace App\Contracts;

use App\Models\Prescription;

interface PrescriptionRendererInterface
{
    /**
     * Render a prescription model into raw PDF binary content.
     *
     * @param  Prescription  $prescription  The prescription to render
     * @param  string|null  $signature  The doctor's handwritten signature image/base64
     * @return string Raw PDF binary string
     */
    public function render(Prescription $prescription, ?string $signature = null): string;

    /**
     * Generate a QR code data URI / image for prescription verification.
     */
    public function generateQrCode(string $url): string;
}
