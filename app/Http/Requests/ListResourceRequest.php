<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListResourceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return !!auth()->user();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:5000',
            'sort' => 'nullable|array',
            'sort.*' => 'string|in:asc,desc',
            'sort_by' => 'nullable|array',
            'sort_by.*' => 'string|distinct',
            'filter' => 'nullable|array',
            'filter.*' => 'string|distinct',
            'search' => 'nullable|string|max:255',
            'search_by' => 'nullable|array',
            'search_by.*' => 'string|distinct',
            'no_relations' => 'nullable|boolean',
            'no_pagination' => 'nullable|boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        $sort = $this->input('sort', ['desc']);
        $sort = is_array($sort) ? $sort : [$sort];
        $sortBy = $this->input('sort_by', ['id']);
        $sortBy = is_array($sortBy) ? $sortBy : [$sortBy];
        $this->merge([
            'sort' => $sort,
            'sort_by' => $sortBy,
        ]);
    }
}
