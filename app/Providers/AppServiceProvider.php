<?php

namespace App\Providers;

use App\Contracts\CertificateManagerInterface;
use App\Contracts\DigitalSignerInterface;
use App\Contracts\PrescriptionRendererInterface;
use App\Services\CertificateService;
use App\Services\Prescription\PrescriptionPdfRenderer;
use App\Services\Signature\PdfDigitalSignatureService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CertificateManagerInterface::class, CertificateService::class);
        $this->app->singleton(DigitalSignerInterface::class, PdfDigitalSignatureService::class);
        $this->app->singleton(PrescriptionRendererInterface::class, PrescriptionPdfRenderer::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
