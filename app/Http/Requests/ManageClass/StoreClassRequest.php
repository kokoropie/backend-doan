<?php

namespace App\Http\Requests\ManageClass;

use App\Enums\UserRole;
use App\Models\AcademicYear;
use App\Models\User;
use App\Services\ClassService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClassRequest extends FormRequest
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
                function ($attribute, $value, $fail) {
                    if (resolve(ClassService::class)->isClassExists($value, $this->integer('academic_year_id'), $this->route()->parameter('class')?->id)) {
                        $fail(__('class.reason.exists'));
                    }
                }
            ],
            'academic_year_id' => [
                'required',
                Rule::exists(AcademicYear::class, 'id')
                    ->where('deleted_at', null)
            ],
            'teacher_id' => [
                'required', 
                Rule::exists(User::class, 'id')->where('role', UserRole::TEACHER->value)
                    ->where('deleted_at', null)
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
            'name.required' => __('validation.required', ['attribute' => __('class.name')]),
            'name.string' => __('validation.string', ['attribute' => __('class.name')]),
            'name.max' => __('validation.max.string', [
                'attribute' => __('class.name'),
                'max' => 20
            ]),
            'academic_year_id.required' => __('validation.required', ['attribute' => __('common.attribute.academic_year')]),
            'academic_year_id.exists' => __('validation.exists', ['attribute' => __('common.attribute.academic_year')]),
            'teacher_id.required' => __('validation.required', ['attribute' => __('class.primary_teacher')]),
            'teacher_id.exists' => __('validation.exists', ['attribute' => __('class.primary_teacher')]),
        ];
    }
}
