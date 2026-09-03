<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExaminationResource extends JsonResource
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
            'patient_id' => $this->patient_id,
            'user_id' => $this->user_id,
            'prescription_id' => $this->prescription_id,
            'name' => $this->name,
            'type' => $this->type,
            'examined_at' => $this->examined_at?->format('Y-m-d'),
            'laboratory_name' => $this->laboratory_name,
            'findings' => $this->findings,
            'status' => $this->status,
            'files_count' => $this->files()->count(),
            'files' => PatientMediaResource::collection($this->whenLoaded('files')),
            'patient' => new PatientResource($this->whenLoaded('patient')),
            'user' => new MedicResource($this->whenLoaded('user')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
