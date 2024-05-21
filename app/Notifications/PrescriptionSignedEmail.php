<?php

namespace App\Notifications;

use App\Models\Prescription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PrescriptionSignedEmail extends Notification
{
    use Queueable;
    private Prescription $prescription;
    private array $fileData;

    /**
     * Create a new notification instance.
     */
    public function __construct(Prescription $prescription, array $fileData)
    {
        $this->prescription = $prescription;
        $this->fileData = $fileData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $email = (new MailMessage)->markdown('mail.prescription', [
            'prescription' => $this->prescription,
            'base_url' => env('APP_URL', '') . '/storage/'
        ])->subject('Receta médica electrónica ' . $this->prescription->code);
        foreach ($this->fileData as $key => $file) {
            $email = $email->attachData($file, "$key.pdf");
        }
        return $email;
    }
    public function toWhatsApp(object $notifiable)
    {
        /* return (new WhatsAppMessage)
            ->content("Your {$company} order of {$this->order->name} has shipped and should be delivered on {$deliveryDate}. Details: {$orderUrl}");
        */
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
