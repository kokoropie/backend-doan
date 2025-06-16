<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScoreResource extends JsonResource
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
            'student' => new UserResource($this->whenLoaded('student')),
            'class' => new ClassResource($this->whenLoaded('class')),
            'subject' => new SubjectResource($this->whenLoaded('subject')),
            'semester' => new SemesterResource($this->whenLoaded('semester')),
            'type' => $this->type,
            'score' => $this->score,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
