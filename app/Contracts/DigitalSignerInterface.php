<?php

namespace App\Contracts;

use App\Models\User;

interface DigitalSignerInterface
{
    /**
     * Digitally sign a PDF content using cryptographic X.509 certificate.
     *
     * @param  string  $pdfContent  Raw binary content of the PDF to be signed
     * @param  User  $signer  The authenticated user signing the document
     * @param  array<string, mixed>  $metadata  Additional signature metadata (location, reason, contact, etc.)
     * @return string Signed PDF binary content
     */
    public function signPdf(string $pdfContent, User $signer, array $metadata = []): string;
}
