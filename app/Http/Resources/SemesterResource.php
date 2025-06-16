<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SemesterResource extends JsonResource
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
            'year' => new AcademicYearResource($this->whenLoaded('academicYear')),
            'classes' => ClassResource::collection($this->whenLoaded('classes')),
            'classes_count' => $this->whenCounted('classes') ?? 0,
        ];
    }
}
