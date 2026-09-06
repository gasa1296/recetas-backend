<?php

namespace App\Console\Commands;

use App\Contracts\DigitalSignerInterface;
use App\Contracts\PrescriptionRendererInterface;
use App\Models\Prescription;
use Illuminate\Console\Command;

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
    public function handle(
        PrescriptionRendererInterface $renderer,
        DigitalSignerInterface $signer
    ): int {
        $id = $this->argument('id');

        $query = Prescription::where('status', config('custom.prescription.status_keys.active'))
            ->with(['user', 'patient', 'room', 'specialty', 'medicaments', 'signed_file']);

        if ($id) {
            $query->where('id', $id);
        }

        $totalCount = (clone $query)->count();

        if ($totalCount === 0) {
            $this->info('No active prescriptions found to re-sign.');

            return self::SUCCESS;
        }

        $this->info("Re-signing {$totalCount} active prescription(s)...");

        $successCount = 0;

        $query->chunkById(50, function ($prescriptions) use ($renderer, $signer, &$successCount) {
            foreach ($prescriptions as $prescription) {
                try {
                    $user = $prescription->user;
                    $signature = $user->saved_signature;

                    $pdfContent = $renderer->render($prescription, $signature);

                    $signerName = trim("{$user->first_name} {$user->last_name}");
                    $metadata = [
                        'Name' => ! empty($signerName) ? $signerName : config('app.name'),
                        'Location' => $prescription->room?->address ?? '',
                        'Reason' => 'Prescripción Médica #'.$prescription->id.($prescription->room ? ' - '.$prescription->room->name : ''),
                        'ContactInfo' => $user->email,
                    ];

                    $signedPdf = $signer->signPdf($pdfContent, $user, $metadata);
                    $prescription->handleUploadFile($signedPdf, 'signed');

                    $this->line("  ✓ Prescription #{$prescription->id} re-signed successfully.");
                    $successCount++;
                } catch (\Throwable $e) {
                    $this->error("  ✗ Failed to re-sign Prescription #{$prescription->id}: {$e->getMessage()}");
                }
            }

            gc_collect_cycles();
        });

        $this->info("Completed: {$successCount}/{$totalCount} prescriptions successfully re-signed.");

        return self::SUCCESS;
    }
}
