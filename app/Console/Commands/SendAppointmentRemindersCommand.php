<?php

namespace App\Console\Commands;

use App\Jobs\SendAppointmentRemindersJob;
use Illuminate\Console\Command;

class SendAppointmentRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:send-reminders {--sync : Process reminders synchronously without queueing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send automated appointment reminders to patients';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking upcoming appointments for reminders...');

        if ($this->option('sync')) {
            $job = new SendAppointmentRemindersJob;
            $processed = $job->handle();
            $this->info("Successfully sent {$processed} appointment reminder(s).");
        } else {
            SendAppointmentRemindersJob::dispatch();
            $this->info('Appointment reminders job has been dispatched to the queue.');
        }

        return Command::SUCCESS;
    }
}
