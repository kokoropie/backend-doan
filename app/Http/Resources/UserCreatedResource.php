<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class UserCreatedResource extends UserResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $array = parent::toArray($request);
        $array['password'] = $this->plain_password;

        return $array;
    }
}
