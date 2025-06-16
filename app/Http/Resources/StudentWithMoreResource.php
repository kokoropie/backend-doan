<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentWithMoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $response = UserResource::make($this->whenLoaded('student'))->toArray($request);
        $response['parents'] = UserResource::collection($this->whenLoaded('parents'));
        $response['class'] = ClassResource::collection($this->whenLoaded('class'));
        return $response;
    }
}
