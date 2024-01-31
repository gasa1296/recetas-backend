<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrescriptionMedicamentResource extends JsonResource
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
            'add' => $this->add,
            'dose' => $this->dose,
            'way' => $this->way,
            'frequency' => $this->frequency,
            'duration' => $this->duration,
            'quantity' => $this->quantity,
            'quantity_exp' => $this->quantity_exp,
            'name' => $this->name,
            'type' => $this->type,
            'family' => $this->family,
            'group' => $this->group,
            'medicament_id' => $this->medicament_id,
        ];
    }
}
