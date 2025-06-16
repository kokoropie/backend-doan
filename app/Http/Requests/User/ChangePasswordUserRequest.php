<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordUserRequest extends StoreUserRequest
{
    public function rules(): array
    {
        return array_intersect_key(parent::rules(), array_flip(['password', 'random_password']));
    }
}
