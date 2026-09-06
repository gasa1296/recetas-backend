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
            $file = $this->prescription->signed_file;
            $disk = $file->location ?: config('filesystems.default', 'local');

            if (Storage::disk($disk)->exists($file->path)) {
                $mail->attachFromStorageDisk($disk, $file->path, $file->filename, [
                    'mime' => 'application/pdf',
                ]);
            }
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
