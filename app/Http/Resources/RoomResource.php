<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'zip' => $this->zip,
            'street' => $this->street,
            'colony' => $this->colony,
            'state' => $this->state,
            'delegation' => $this->delegation,
            'n_exterior' => $this->n_exterior,
            'n_interior' => $this->n_interior,
            'address' => $this->address,
            'phone' => $this->phone,
            'fav' => $this->fav,
            'auto_email' => $this->auto_email,
            'auto_whatsapp' => $this->auto_whatsapp,
        ];
    }
}
