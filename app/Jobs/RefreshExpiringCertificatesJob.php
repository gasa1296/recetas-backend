<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\CertificateService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class RefreshExpiringCertificatesJob implements ShouldQueue
{
    use Queueable;

    public function handle(CertificateService $service): void
    {
        $days = (int) config('custom.prescription.certificate.refresh_days_before_expiry', 5);
        $threshold = now()->addDays($days);

        User::query()
            ->whereNotNull('certificate_expires_at')
            ->where('certificate_expires_at', '<=', $threshold)
            ->chunkById(100, function ($users) use ($service) {
                foreach ($users as $user) {
                    try {
                        $certificate = $service->refreshForUser($user);

                        $user->update([
                            'certificate_path' => $certificate['certificate_path'],
                            'certificate_key_path' => $certificate['key_path'],
                            'certificate_expires_at' => $certificate['expires_at'],
                        ]);
                    } catch (\Throwable $e) {
                        Log::error('Failed to refresh certificate', [
                            'user_id' => $user->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            });
    }
}
