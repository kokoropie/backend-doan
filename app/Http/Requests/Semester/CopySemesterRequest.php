<?php

namespace App\Http\Requests\Semester;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CopySemesterRequest extends FormRequest
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
            'to' => 'required|integer|exists:semesters,id',
            'from' => 'required|integer|exists:semesters,id|different:to',
        ];
    }
}
