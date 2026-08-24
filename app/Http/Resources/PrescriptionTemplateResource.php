<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrescriptionTemplateResource extends JsonResource
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
            'name' => $this->name,
            'medicaments' => $this->whenLoaded('medicaments', $this->medicaments->map(fn ($medicament) => [
                'id' => $medicament->id,
                'active_ingredient' => $medicament->active_ingredient,
                'concentration' => $medicament->concentration,
                'type' => $medicament->type,
                'group' => $medicament->group,
                'dosage' => $medicament->pivot->dosage,
                'frequency' => $medicament->pivot->frequency,
                'duration' => $medicament->pivot->duration,
                'medicament_quantity' => $medicament->pivot->medicament_quantity,
                'recommended_brand' => $medicament->pivot->recommended_brand,
            ])),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
