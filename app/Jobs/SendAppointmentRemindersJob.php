<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Notifications\AppointmentReminderNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendAppointmentRemindersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): int
    {
        $appointments = Appointment::with(['patient', 'user', 'room', 'specialty'])
            ->upcomingReminders()
            ->get();

        $count = 0;
        foreach ($appointments as $appointment) {
            try {
                if ($appointment->patient && $appointment->patient->email) {
                    $appointment->patient->notify(new AppointmentReminderNotification($appointment));
                }

                $appointment->update([
                    'reminder_sent_at' => now(),
                ]);
                $count++;
            } catch (\Throwable $e) {
                Log::error("Failed to send reminder for appointment #{$appointment->id}: ".$e->getMessage());
            }
        }

        return $count;
    }
}
