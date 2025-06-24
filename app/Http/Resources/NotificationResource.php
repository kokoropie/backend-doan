<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
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
            'sender' => new UserResource($this->whenLoaded('sender')),
            'title' => $this->title,
            'message' => $this->message,
            'receiver_type' => $this->receiver_type,
            'is_read' => $this->is_read ?? false,
            'created_at' => $this->created_date,
            'updated_at' => $this->updated_date,
        ];
    }
}
