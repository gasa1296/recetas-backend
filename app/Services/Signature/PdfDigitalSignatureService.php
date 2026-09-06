<?php

namespace App\Services\Signature;

use App\Contracts\CertificateManagerInterface;
use App\Contracts\DigitalSignerInterface;
use App\Models\User;
use setasign\Fpdi\Tcpdf\Fpdi;

class PdfDigitalSignatureService implements DigitalSignerInterface
{
    public function __construct(
        protected ?CertificateManagerInterface $certificateManager = null,
    ) {
        $this->certificateManager ??= app(CertificateManagerInterface::class);
    }

    /**
     * Digitally sign a PDF content using cryptographic X.509 certificate and FPDI/TCPDF.
     *
     * @param  string  $pdfContent  Raw binary content of the PDF to be signed
     * @param  User  $signer  The authenticated user signing the document
     * @param  array<string, mixed>  $metadata  Additional signature metadata
     * @return string Signed PDF binary content
     */
    public function signPdf(string $pdfContent, User $signer, array $metadata = []): string
    {
        $pdf = new Fpdi;
        $passphrase = '';

        if ($signer->hasValidCertificate()) {
            $certificate = 'file://'.$signer->getCertificatePath();
            $privateKey = 'file://'.$signer->getCertificateKeyPath();
            $passphrase = $this->certificateManager->getPrivateKeyPassphrase($signer);
        } else {
            $certificate = 'file://'.base_path(config('custom.prescription.signature.default_certificate.path'));
            $privateKey = 'file://'.base_path(config('custom.prescription.signature.default_certificate.key_path'));
        }

        $signerName = trim("{$signer->first_name} {$signer->last_name}");
        $defaultName = ! empty($signerName) ? $signerName : config('app.name');

        $info = [
            'Name' => $metadata['Name'] ?? $defaultName,
            'Location' => $metadata['Location'] ?? '',
            'Reason' => $metadata['Reason'] ?? 'Prescripción Médica',
            'ContactInfo' => $metadata['ContactInfo'] ?? $signer->email,
        ];

        $pdf->setSignature($certificate, $privateKey, $passphrase, '', 2, $info);

        $tempFile = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($tempFile, $pdfContent);

        try {
            $pageCount = $pdf->setSourceFile($tempFile);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);

                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);
            }

            return $pdf->Output('', 'S');
        } finally {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }
}
