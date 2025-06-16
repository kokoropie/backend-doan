<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AcademicYearResource extends JsonResource
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
            'from' => $this->from->format('Y-m-d'),
            'to' => $this->to->format('Y-m-d'),
            'year' => $this->year,
            'current' => $this->current,
            'classes' => ClassResource::collection($this->whenLoaded('classes')),
            'semesters' => SemesterResource::collection($this->whenLoaded('semesters')),
            'students' => StudentWithMoreResource::collection($this->whenLoaded('students')),
            'classes_count' => $this->whenCounted('classes') ?? 0,
            'semesters_count' => $this->whenCounted('semesters') ?? 0,
            'students_count' => $this->whenCounted('students') ?? 0,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
