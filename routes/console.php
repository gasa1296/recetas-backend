<?php

use App\Jobs\RefreshExpiringCertificatesJob;
use App\Jobs\SendAppointmentRemindersJob;
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

Schedule::job(new SendAppointmentRemindersJob)
    ->everyThirtyMinutes()
    ->withoutOverlapping()
    ->onOneServer()
    ->name('send-appointment-reminders');
