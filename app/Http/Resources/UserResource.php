<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name1' => $this->last_name1,
            'last_name2' => $this->last_name2,
            'phone1' => $this->phone1,
            'phone2' => $this->phone2,
            'gender' => $this->gender,
            'fesa' => $this->fesa,
            'email' => $this->email,
            'specialization' => $this->specializations->first(),
        ];
    }
}
