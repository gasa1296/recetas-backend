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
            'room' => $this->whenLoaded('room', new RoomResource($this->room)),
            'patient' => $this->whenLoaded('patient', new PatientResource($this->patient)),
            'medicaments' => $this->whenLoaded('medicaments', $this->medicaments->map(fn ($medicament) => [
                'id' => $medicament->id,
                'name' => $medicament->name,
                'dosage' => $medicament->pivot->dosage,
                'frequency' => $medicament->pivot->frequency,
                'duration' => $medicament->pivot->duration,
            ])),
            'status' => $this->prettyStatus,
        ];
    }
}
