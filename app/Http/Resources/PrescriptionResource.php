<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrescriptionResource extends JsonResource
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
            'temp' => $this->temp,
            'weight' => $this->weight,
            'height' => $this->height,
            'pressure' => $this->pressure,
            'saturation' => $this->saturation,
            'ppm' => $this->ppm,
            'allergy' => $this->allergy,
            'diagnostic' => $this->diagnostic,
            'diet' => $this->diet,
            'comments' => $this->comments,
            'user' => $this->whenLoaded('user', new MedicResource($this->user)),
            'room' => $this->whenLoaded('room', new RoomResource($this->room)),
            'patient' => $this->whenLoaded('patient', new PatientResource($this->patient)),
            'specialty' => $this->whenLoaded('specialty', new SpecialtyResource($this->specialty)),
            'medicaments' => $this->whenLoaded('medicaments', $this->medicaments->map(fn ($medicament) => [
                'id' => $medicament->id,
                'active_ingredient' => $medicament->active_ingredient,
                'type' => $medicament->type,
                'group' => $medicament->group,
                'dosage' => $medicament->pivot->dosage,
                'frequency' => $medicament->pivot->frequency,
                'duration' => $medicament->pivot->duration,
                'medicament_quantity' => $medicament->pivot->medicament_quantity,
                'medicament_quantity_letters' => $medicament->pivot->medicament_quantity_letters,
                'recommended_brand' => $medicament->pivot->recommended_brand,
            ])),
            'status' => $this->status,
            'pretty_status' => $this->pretty_status,
            'prescription_hash' => $this->prescription_hash,
            'expires_at' => $this->expires_at,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
