<?php

namespace App\Notifications;

use App\Models\Prescription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class PrescriptionReadyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Prescription $prescription,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Your prescription is ready')
            ->greeting("Hello {$notifiable->first_name},")
            ->line('Your prescription has been signed and is now ready.')
            ->line('Diagnosis: '.$this->prescription->diagnostic)
            ->action('View Prescription', route('public.prescription.show', $this->prescription->prescription_hash));

        if ($this->prescription->signed_file) {
            $path = Storage::disk('local')->path($this->prescription->signed_file->path);
            $mail->attach($path, [
                'as' => $this->prescription->signed_file->filename,
                'mime' => 'application/pdf',
            ]);
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'prescription_id' => $this->prescription->id,
            'diagnostic' => $this->prescription->diagnostic,
        ];
    }
}
