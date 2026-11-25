<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'genre' => $this->genre,
            'year' => $this->year,
            'category' => $this->category,
            'type' => $this->type,
            'rating' => $this->rating,
            'total_parts' => $this->total_parts,
            'unit' => $this->unit,
            'creator' => $this->creator,
            'detail' => $this->detail,
            'users_count' => $this->whenCounted('users'),
            'users' => UserResource::collection($this->whenLoaded('users')),
            'added_at' => $this->whenPivotLoaded('media_user', fn () => $this->pivot->created_at),
            'created_at' => $this->created_at,
        ];
    }
}
