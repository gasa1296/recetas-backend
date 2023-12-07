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
            'temp' => $this->temp,
            'weight' => $this->weight,
            'height' => $this->height,
            'pressure' => $this->pressure,
            'saturation' => $this->saturation,
            'ppm' => $this->ppm,
            'allergy' => $this->allergy,
            'diagnostic' => $this->diagnostic,
            'diet' => $this->diet,
            'add' => $this->add,
            'medic' => $this->medic,
            'room' => $this->room,
            'patient' => $this->patient,
            'medicaments' => PrescriptionMedicamentResource::collection($this->medicaments),
            'equipment' => PrescriptionEquipmentResource::collection($this->equipment),
            'file' => $this->file,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
