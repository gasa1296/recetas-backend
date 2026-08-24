<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\CertificateService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

#[Signature('certificates:refresh')]
#[Description('Refresh X.509 certificates that expire within 5 days')]
class RefreshCertificatesCommand extends Command
{
    public function handle(CertificateService $certificateService): int
    {
        $daysBeforeExpiry = (int) config('custom.certificate.refresh_days_before_expiry', 5);

        $users = User::whereNotNull('certificate_expires_at')
            ->where('certificate_expires_at', '<=', now()->addDays($daysBeforeExpiry))
            ->where('certificate_expires_at', '>', now())
            ->get();

        $refreshed = 0;

        foreach ($users as $user) {
            try {
                $certificate = $certificateService->refreshForUser($user);

                $user->update([
                    'certificate_path' => $certificate['certificate_path'],
                    'certificate_key_path' => $certificate['key_path'],
                    'certificate_expires_at' => $certificate['expires_at'],
                ]);

                $refreshed++;
                $this->info("Certificate refreshed for user: {$user->first_name} {$user->last_name}");
            } catch (\Exception $e) {
                Log::error("Failed to refresh certificate for user {$user->id}", [
                    'error' => $e->getMessage(),
                ]);
                $this->error("Failed to refresh certificate for user: {$user->first_name} {$user->last_name}");
            }
        }

        $this->info("Refreshed {$refreshed} certificate(s).");

        return self::SUCCESS;
    }
}
