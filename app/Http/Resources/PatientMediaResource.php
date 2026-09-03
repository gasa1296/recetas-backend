<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientMediaResource extends JsonResource
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
            'model_id' => $this->model_id,
            'model_type' => $this->model_type,
            'category' => $this->category,
            'title' => $this->title,
            'description' => $this->description,
            'filename' => $this->filename,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'human_size' => $this->human_size,
            'is_image' => $this->is_image,
            'is_video' => $this->is_video,
            'meta' => $this->meta ?? [],
            'evolution_stage' => $this->meta['evolution_stage'] ?? null,
            'user' => new MedicResource($this->whenLoaded('user')),
            'stream_url' => url("/api/patients/{$this->model_id}/media/{$this->id}/stream"),
            'download_url' => url("/api/patients/{$this->model_id}/media/{$this->id}/download"),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
