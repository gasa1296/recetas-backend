<?php

use App\Jobs\RefreshExpiringCertificatesJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new RefreshExpiringCertificatesJob)
    ->daily()
    ->withoutOverlapping()
    ->onOneServer()
    ->name('refresh-expiring-certificates');

Schedule::job(new \App\Jobs\SendAppointmentRemindersJob)
    ->everyThirtyMinutes()
    ->withoutOverlapping()
    ->onOneServer()
    ->name('send-appointment-reminders');

