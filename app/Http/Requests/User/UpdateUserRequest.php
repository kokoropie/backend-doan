<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends StoreUserRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['email'] = 'required|email|max:255|unique:users,email,' . $this->route('user')->id;
        unset($rules['password'], $rules['random_password'], $rules['role']);
        return $rules;
    }
}
