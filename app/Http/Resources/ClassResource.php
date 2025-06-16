<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassResource extends JsonResource
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
            'name' => $this->name,
            'teacher' => new UserResource($this->whenLoaded('primaryTeacher')),
            'teachers' => UserResource::collection($this->whenLoaded('teachers')),
            'students' => UserResource::collection($this->whenLoaded('students')),
            $this->mergeWhen($this->whenLoaded('subjectsWithMore'), function () {
                return [
                    'subjects' => SubjectWithMoreResource::collection($this->whenLoaded('subjectsWithMore')),
                ];
            }, function () {
                return [
                    'subjects' => SubjectResource::collection($this->whenLoaded('subjects')),
                ];
            }),
            'semesters' => SemesterResource::collection($this->whenLoaded('semesters')),
            'year' => new AcademicYearResource($this->whenLoaded('academicYear')),
            'semesters_count' => $this->whenCounted('semesters') ?? 0,
            'students_count' => $this->whenCounted('students') ?? 0,
            'avg_score' => $this->whenLoaded('scores', function () {
                return $this->avg_score;
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
