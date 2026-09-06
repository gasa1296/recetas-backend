<?php

namespace App\Services;

use App\Contracts\CertificateManagerInterface;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CertificateService implements CertificateManagerInterface
{
    /**
     * Generate a self-signed X.509 certificate for a user.
     *
     * @return array{certificate_path: string, key_path: string, expires_at: string}
     */
    public function generateForUser(User $user, ?int $validityDays = null): array
    {
        $validityDays ??= config('custom.prescription.certificate.validity_days', 365);

        $dn = [
            'countryName' => 'VE',
            'stateOrProvinceName' => 'Distrito Capital',
            'localityName' => 'Caracas',
            'organizationName' => config('app.name'),
            'organizationalUnitName' => 'Medicina',
            'commonName' => "{$user->first_name} {$user->last_name}",
            'emailAddress' => $user->email,
        ];

        $candidates = array_filter([
            config('custom.prescription.certificate.openssl_conf'),
            getenv('OPENSSL_CONF') ?: null,
            '/etc/pki/tls/openssl.cnf',
            '/etc/ssl/openssl.cnf',
            dirname(PHP_BINARY).'/extras/ssl/openssl.cnf',
            dirname(PHP_BINARY).'/openssl.cnf',
        ]);
        $opensslConfig = null;
        foreach ($candidates as $cand) {
            if ($cand && file_exists($cand)) {
                $opensslConfig = $cand;
                break;
            }
        }

        $keyConfig = [
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];
        if ($opensslConfig) {
            $keyConfig['config'] = $opensslConfig;
        }

        $privateKey = openssl_pkey_new($keyConfig);

        if ($privateKey === false) {
            Log::error('Failed to generate private key', ['user_id' => $user->id]);

            throw new \RuntimeException('Failed to generate private key');
        }

        $csrConfig = ['digest_alg' => 'sha256'];
        if ($opensslConfig) {
            $csrConfig['config'] = $opensslConfig;
        }

        $csr = openssl_csr_new($dn, $privateKey, $csrConfig);

        if (! $csr instanceof \OpenSSLCertificateSigningRequest) {
            Log::error('Failed to generate CSR', ['user_id' => $user->id]);

            throw new \RuntimeException('Failed to generate CSR');
        }

        // Self-sign the certificate
        $signConfig = [
            'digest_alg' => 'sha256',
            'x509_extensions' => [
                'basicConstraints' => 'CA:FALSE',
                'keyUsage' => 'digitalSignature, nonRepudiation',
                'extendedKeyUsage' => 'emailProtection, clientAuth',
            ],
        ];
        if ($opensslConfig) {
            $signConfig['config'] = $opensslConfig;
        }

        $certificate = openssl_csr_sign($csr, null, $privateKey, $validityDays, $signConfig);

        if (! $certificate instanceof \OpenSSLCertificate) {
            Log::error('Failed to generate certificate', ['user_id' => $user->id]);

            throw new \RuntimeException('Failed to generate certificate');
        }

        // Export certificate
        $certPem = openssl_x509_export($certificate, $certContent);

        if (! $certPem) {
            Log::error('Failed to export certificate', ['user_id' => $user->id]);

            throw new \RuntimeException('Failed to export certificate');
        }

        // Export private key
        $exportConfig = [];
        if ($opensslConfig) {
            $exportConfig['config'] = $opensslConfig;
        }

        $passphrase = $this->getPrivateKeyPassphrase($user);
        $keyPem = openssl_pkey_export($privateKey, $keyContent, $passphrase, $exportConfig);

        if (! $keyPem) {
            Log::error('Failed to export private key', ['user_id' => $user->id]);

            throw new \RuntimeException('Failed to export private key');
        }

        // Store files
        $certDir = "certificates/{$user->id}";
        $certPath = "{$certDir}/certificate.pem";
        $keyPath = "{$certDir}/private.key";

        Storage::disk('local')->put($certPath, $certContent);
        Storage::disk('local')->put($keyPath, $keyContent);

        $expiresAt = now()->addDays($validityDays);

        return [
            'certificate_path' => $certPath,
            'key_path' => $keyPath,
            'expires_at' => $expiresAt->toDateTimeString(),
        ];
    }

    /**
     * Get the symmetric passphrase used to protect a user's private key.
     */
    public function getPrivateKeyPassphrase(User $user): string
    {
        return hash_hmac('sha256', (string) $user->id, (string) config('app.key'));
    }

    /**
     * Check if a certificate needs refresh (expires within given days).
     */
    public function needsRefresh(User $user, int $daysBeforeExpiry = 5): bool
    {
        if (is_null($user->certificate_expires_at)) {
            return true;
        }

        $expiresAt = Carbon::parse($user->certificate_expires_at);

        // Need refresh if expires within N days but not already expired
        return $expiresAt->isFuture() && $expiresAt->lte(now()->addDays($daysBeforeExpiry));
    }

    /**
     * Refresh a user's certificate.
     */
    public function refreshForUser(User $user, ?int $validityDays = null): array
    {
        $validityDays ??= config('custom.prescription.certificate.validity_days', 365);

        // Delete old certificate files if they exist
        if ($user->certificate_path && Storage::disk('local')->exists($user->certificate_path)) {
            Storage::disk('local')->delete($user->certificate_path);
        }
        if ($user->certificate_key_path && Storage::disk('local')->exists($user->certificate_key_path)) {
            Storage::disk('local')->delete($user->certificate_key_path);
        }

        return $this->generateForUser($user, $validityDays);
    }

    /**
     * Delete a user's certificate files.
     */
    public function deleteForUser(User $user): void
    {
        if ($user->certificate_path && Storage::disk('local')->exists($user->certificate_path)) {
            Storage::disk('local')->delete($user->certificate_path);
        }
        if ($user->certificate_key_path && Storage::disk('local')->exists($user->certificate_key_path)) {
            Storage::disk('local')->delete($user->certificate_key_path);
        }
    }
}
