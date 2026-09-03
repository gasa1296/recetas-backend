<?php

namespace App\Console\Commands;

use App\Models\Prescription;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Console\Command;
use setasign\Fpdi\Tcpdf\Fpdi;

class ResignPrescriptionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prescriptions:resign {id? : Optional ID of specific prescription to re-sign}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Re-generates cryptographic digital signatures for active prescriptions without digest corruption';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $id = $this->argument('id');

        $query = Prescription::where('status', config('custom.prescription.status_keys.active'))
            ->with(['user', 'patient', 'room', 'specialty', 'medicaments', 'signed_file']);

        if ($id) {
            $query->where('id', $id);
        }

        $prescriptions = $query->get();

        if ($prescriptions->isEmpty()) {
            $this->info('No active prescriptions found to re-sign.');

            return self::SUCCESS;
        }

        $this->info("Re-signing {$prescriptions->count()} active prescriptions...");

        $successCount = 0;

        foreach ($prescriptions as $prescription) {
            try {
                $user = $prescription->user;

                $qrOptions = new QROptions;
                $qrOptions->outputType = 'png';
                $qrOptions->scale = 5;
                $qrCode = (new QRCode($qrOptions))->render(
                    route('public.prescription.show', $prescription->prescription_hash)
                );

                $signature = $user->saved_signature;

                $pdfContent = Pdf::loadView('pdf.prescription_model_1', [
                    'prescription' => $prescription,
                    'signature' => $signature,
                    'qrCode' => $qrCode,
                ])->output();

                $pdf = new Fpdi;

                if ($user->hasValidCertificate()) {
                    $certificate = 'file://'.$user->getCertificatePath();
                    $privateKey = 'file://'.$user->getCertificateKeyPath();
                } else {
                    $certificate = 'file://'.base_path(config('custom.prescription.signature.default_certificate.path'));
                    $privateKey = 'file://'.base_path(config('custom.prescription.signature.default_certificate.key_path'));
                }

                $signerName = trim("{$user->first_name} {$user->last_name}");
                $info = [
                    'Name' => ! empty($signerName) ? $signerName : config('app.name'),
                    'Location' => $prescription->room->address,
                    'Reason' => 'Prescripción Médica #'.$prescription->id.' - '.$prescription->room->name,
                    'ContactInfo' => $user->email,
                ];

                $pdf->setSignature($certificate, $privateKey, '', '', 2, $info);

                $tempFile = tempnam(sys_get_temp_dir(), 'pdf');
                file_put_contents($tempFile, $pdfContent);

                $pageCount = $pdf->setSourceFile($tempFile);
                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $templateId = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($templateId);
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($templateId);
                }
                unlink($tempFile);

                $signedPdf = $pdf->Output('', 'S');
                $prescription->handleUploadFile($signedPdf, 'signed');

                $this->line("  ✓ Prescription #{$prescription->id} re-signed successfully.");
                $successCount++;
            } catch (\Throwable $e) {
                $this->error("  ✗ Failed to re-sign Prescription #{$prescription->id}: {$e->getMessage()}");
            }
        }

        $this->info("Completed: {$successCount}/{$prescriptions->count()} prescriptions successfully re-signed.");

        return self::SUCCESS;
    }
}
