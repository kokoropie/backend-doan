<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'last_name' => $this->last_name,
            'first_name' => $this->first_name,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'parents' => UserResource::collection($this->whenLoaded('parents')),
            'students' => UserResource::collection($this->whenLoaded('students')),
            'classes' => ClassResource::collection($this->whenLoaded('classes')),
            'children' => UserResource::collection($this->whenLoaded('children')),
            'sent_feedback' => FeedbackResource::collection($this->whenLoaded('sentFeedback')),
            'received_feedback' => FeedbackResource::collection($this->whenLoaded('receivedFeedback')),
            'current_class' => new ClassResource($this->whenLoaded('currentClass')),
            'class_year' => new ClassResource($this->whenLoaded('classYear')),
            'parents_count' => $this->whenCounted('parents'),
            'students_count' => $this->whenCounted('students'),
            'classes_count' => $this->whenCounted('classes'),
            'children_count' => $this->whenCounted('children'),
            'sent_feedback_count' => $this->whenCounted('sentFeedback'),
            'received_feedback_count' => $this->whenCounted('receivedFeedback'),
            'received_notifications_count' => $this->whenCounted('receivedNotifications'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
