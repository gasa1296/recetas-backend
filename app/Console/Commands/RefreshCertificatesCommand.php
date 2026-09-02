<?php

namespace App\Console\Commands;

use App\Jobs\RefreshExpiringCertificatesJob;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('certificates:refresh')]
#[Description('Refresh X.509 certificates that expire within the configured threshold')]
class RefreshCertificatesCommand extends Command
{
    public function handle(): int
    {
        RefreshExpiringCertificatesJob::dispatch();

        $this->info('Certificate refresh job dispatched.');

        return self::SUCCESS;
    }
}
