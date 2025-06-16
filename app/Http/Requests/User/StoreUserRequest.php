<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,NULL,id,deleted_at,NULL',
            'password' => 'required_if:random_password,false|nullable|string|min:8|max:255',
            'random_password' => 'nullable|boolean',
            'role' => 'required|in:admin,teacher,student,parent',
            'relationship' => 'nullable|array',
            'relationship.*' => 'string|distinct|in:parents,classes',
            'relationship_data' => 'nullable|array'
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'last_name.required' => __('validation.required', ['attribute' => __('user.last_name')]),
            'last_name.string' => __('validation.string', ['attribute' => __('user.last_name')]),
            'last_name.max' => __('validation.max.string', [
                'attribute' => __('user.last_name'),
                'max' => 255
            ]),
            'first_name.required' => __('validation.required', ['attribute' => __('user.first_name')]),
            'first_name.string' => __('validation.string', ['attribute' => __('user.first_name')]),
            'first_name.max' => __('validation.max.string', [
                'attribute' => __('user.first_name'),
                'max' => 255
            ]),
            'email.required' => __('validation.required', ['attribute' => __('user.email')]),
            'email.email' => __('validation.email', ['attribute' => __('user.email')]),
            'email.max' => __('validation.max.string', [
                'attribute' => __('user.email'),
                'max' => 255
            ]),
            'email.unique' => __('validation.unique', ['attribute' => __('user.email')]),
            'password.required_if' => __('user.messages.input_password_or_random'),
            'password.string' => __('validation.string', ['attribute' => __('user.password')]),
            'password.min' => __('validation.min.string', [
                'attribute' => __('user.password'),
                'min' => 8
            ]),
            'password.max' => __('validation.max.string', [
                'attribute' => __('user.password'),
                'max' => 255
            ]),
            'random_password.boolean' => __('validation.boolean', ['attribute' => __('user.random_password')]),
            'role.required' => __('validation.required', ['attribute' => __('user.role')]),
            'role.in' => __('validation.in', ['attribute' => __('user.role')]),

            'relationship.array' => __('validation.array', ['attribute' => __('user.relationship')]),
            'relationship.*.in' => __('validation.in', ['attribute' => __('user.relationship')]),
            'relationship_data.array' => __('validation.array', ['attribute' => __('user.relationship_data')]),
        ];
    }
}
