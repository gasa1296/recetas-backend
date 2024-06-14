<?php

namespace App\Notifications;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\{ServerException, ClientException};
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
            'base_url' => env('APP_URL', '') . '/storage/',
            'link' => $this->generateLink()
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

    private function generateLink() {
        $client = new Client([
            'verify' => env('VERIFY_FILE', false),
            'base_url' => env('URL_LINK', '')
        ]);
        try {
            $res = $client->post('/api/fesa-auth/Auth/AuthWebhook', [
                'json' => [
                    'UserName' => env('USR_LINK', ''),
                    'Password' => env('PASS_LINK', '')
                ]
            ]);

            $resBody = json_decode($res->getBody(), true);
            $token = $resBody['data']['token'];


            $res = $client->post('/api/webhook/Recetas/CrearShortUrl', [
                'headers' => [
                    'Authorization' => "Bearer $token",
                ],
                'json' => [
                    'idFolio' => $this->prescription->code
                ]
            ]);

            $resBody = json_decode($res->getBody(), true);
            return $resBody['data']['shortLink'];
        } catch (ClientException | ServerException $e) {
            return 'error ' . $e->getMessage();
        }

    }
}
