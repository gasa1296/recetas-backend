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
            'dose' => $this->dose,
            'way' => $this->way,
            'frequency' => $this->frequency,
            'duration' => $this->duration,
            'medicament' => $this->medicament,
        ];
    }
}
