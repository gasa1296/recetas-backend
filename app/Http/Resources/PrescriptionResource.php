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
            'medic' => new UserResource($this->medic),
            'room' => $this->room,
            'patient' => $this->patient,
            'document_id' => $this->document_id,
            'medicaments' => $this->medicaments,
            'file' => $this->file,
            'status' => $this->status,
            'code' => $this->code,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
