<?php

namespace App\Http\Requests\Subject;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubjectRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:20',
                Rule::unique('subjects', 'name')
                    ->where('deleted_at', null)
                    ->ignore($this->route()->parameter('subject')),
            ],
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
            'name.required' => __('validation.required', ['attribute' => __('subject.name')]),
            'name.string' => __('validation.string', ['attribute' => __('subject.name')]),
            'name.max' => __('validation.max.string', [
                'attribute' => __('subject.name'),
                'max' => 20
            ]),
            'name.unique' => __('validation.unique', ['attribute' => __('subject.name')]),
        ];
    }
}
