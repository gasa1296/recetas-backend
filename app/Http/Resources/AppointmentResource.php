<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'patient_id' => $this->patient_id,
            'room_id' => $this->room_id,
            'specialty_id' => $this->specialty_id,
            'starts_at' => $this->starts_at?->toIso8601String(),
            'ends_at' => $this->ends_at?->toIso8601String(),
            'reason' => $this->reason,
            'status' => $this->status,
            'notes' => $this->notes,
            'reminder_channel' => $this->reminder_channel,
            'reminder_enabled' => (bool) $this->reminder_enabled,
            'reminder_sent_at' => $this->reminder_sent_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'user' => $this->whenLoaded('user', fn () => new MedicResource($this->user)),
            'patient' => $this->whenLoaded('patient', fn () => new PatientResource($this->patient)),
            'room' => $this->whenLoaded('room', fn () => new RoomResource($this->room)),
            'specialty' => $this->whenLoaded('specialty', fn () => new SpecialtyResource($this->specialty)),
        ];
    }
}
