<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Appointment $appointment,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $doctorName = $this->appointment->user ? "Dr(a). {$this->appointment->user->name}" : 'su médico';
        $formattedDate = $this->appointment->starts_at?->format('d/m/Y H:i') ?? 'N/A';

        $mail = (new MailMessage)
            ->subject('Recordatorio de Cita Médica')
            ->greeting("Hola {$notifiable->first_name},")
            ->line('Le recordamos que tiene una cita médica programada próximamente.')
            ->line("Médico: {$doctorName}")
            ->line("Fecha y Hora: {$formattedDate}");

        if ($this->appointment->room) {
            $roomInfo = $this->appointment->room->name;
            if ($this->appointment->room->address) {
                $roomInfo .= " ({$this->appointment->room->address})";
            }
            $mail->line("Lugar / Consultorio: {$roomInfo}");
        }

        if ($this->appointment->reason) {
            $mail->line("Motivo de consulta: {$this->appointment->reason}");
        }

        $mail->line('Por favor, acuda 10 minutos antes de la hora acordada.')
            ->line('Gracias por confiar en nuestro servicio de atención médica.');

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'starts_at' => $this->appointment->starts_at?->toIso8601String(),
            'status' => $this->appointment->status,
        ];
    }
}
