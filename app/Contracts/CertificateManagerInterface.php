<?php

namespace App\Contracts;

use App\Models\User;

interface CertificateManagerInterface
{
    /**
     * Generate a self-signed X.509 certificate for a user.
     *
     * @return array{certificate_path: string, key_path: string, expires_at: string}
     */
    public function generateForUser(User $user, ?int $validityDays = null): array;

    /**
     * Check if a certificate needs refresh (expires within given days).
     */
    public function needsRefresh(User $user, int $daysBeforeExpiry = 5): bool;

    /**
     * Refresh a user's certificate.
     *
     * @return array{certificate_path: string, key_path: string, expires_at: string}
     */
    public function refreshForUser(User $user, ?int $validityDays = null): array;

    /**
     * Delete a user's certificate files.
     */
    public function deleteForUser(User $user): void;
}
