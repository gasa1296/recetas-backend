<?php

namespace App\Services\Prescription;

use App\Models\Prescription;
use Illuminate\Support\Carbon;

class PrescriptionExpirationService
{
    /**
     * Calculate the expiration days and date for a given prescription based on its medicaments.
     *
     * @return array{days: int, expires_at: Carbon}
     */
    public function calculateExpiration(Prescription $prescription): array
    {
        $expirationDaysConf = config('custom.prescription.expiration_days', []);
        $expirationDays = $expirationDaysConf['default'] ?? 30;

        foreach ($expirationDaysConf as $type => $days) {
            if ($type === 'default') {
                continue;
            }
            if ($prescription->medicaments->contains('type', $type)) {
                $expirationDays = (int) $days;
                break;
            }
        }

        $expiresAt = now()->addDays($expirationDays);

        return [
            'days' => $expirationDays,
            'expires_at' => $expiresAt,
        ];
    }
}
