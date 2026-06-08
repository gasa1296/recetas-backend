<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name1' => $this->last_name1,
            'last_name2' => $this->last_name2,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'email' => $this->email,
            'rooms' => $this->whenLoaded('rooms', RoomResource::collection($this->rooms)),
            'specialties' => $this->whenLoaded('specialties', RoomResource::collection($this->specialties)),
        ];
    }
}
