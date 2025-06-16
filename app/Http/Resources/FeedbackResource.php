<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedbackResource extends JsonResource
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
            'score' => new ScoreResource($this->whenLoaded('score')),
            'student' => new UserResource($this->whenLoaded('student')),
            'parent' => new UserResource($this->whenLoaded('parent')),
            'teacher' => new UserResource($this->whenLoaded('teacher')),
            'class' => new ClassResource($this->whenLoaded('class')),
            'message' => $this->message,
            'status' => $this->status,
            'created_at' => $this->created_date,
            'updated_at' => $this->updated_date,
        ];
    }
}
