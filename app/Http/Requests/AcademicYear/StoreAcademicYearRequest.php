<?php

namespace App\Http\Requests\AcademicYear;

use Illuminate\Foundation\Http\FormRequest;

class StoreAcademicYearRequest extends FormRequest
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
            'from' => 'required|date|date_format:Y-m-d',
            'to' => 'required|date|date_format:Y-m-d|after:from',
            'current' => 'nullable|boolean',
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
            'from.required' => __('validation.required', ['attribute' => __('academic_year.from')]),
            'from.date' => __('validation.date', ['attribute' => __('academic_year.from')]),
            'from.date_format' => __('validation.date', ['attribute' => __('academic_year.from')]),
            'to.required' => __('validation.required', ['attribute' => __('academic_year.to')]),
            'to.date' => __('validation.date', ['attribute' => __('academic_year.to')]),
            'to.date_format' => __('validation.date', ['attribute' => __('academic_year.to')]),
            'to.after' => __('validation.after', [
                'attribute' => __('academic_year.to'),
                'other' => __('academic_year.from')
            ])
        ];
    }
}
