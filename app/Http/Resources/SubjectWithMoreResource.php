<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectWithMoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $response = SubjectResource::make($this->whenLoaded('subject'))->toArray($request);
        $response['teacher'] = new UserResource($this->whenLoaded('teacher'));
        $response['semester'] = new SemesterResource($this->whenLoaded('semester'));
        $response['class'] = new ClassResource($this->whenLoaded('class'));
        $response['link_id'] = $this->id;
        return $response;
    }
}
