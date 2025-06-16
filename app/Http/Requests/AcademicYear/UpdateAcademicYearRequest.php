<?php

namespace App\Http\Requests\AcademicYear;

use Illuminate\Validation\Validator;

class UpdateAcademicYearRequest extends StoreAcademicYearRequest
{
    public function after()
    {
        return [
            function (Validator $validator) {
                /** @var \App\Models\AcademicYear $academicYear */
                $academicYear = $this->route()->parameter('academic_year');
                if ($academicYear->hasClasses()) {
                    $validator->errors()->add('academic_year', __('common.cannot_update', [
                        'attribute' => __('common.attribute.academic_year'),
                        'reason' => __('academic_year.reason.has_classes')
                    ]));
                }
                if ($academicYear->hasSemesters()) {
                    $validator->errors()->add('academic_year', __('common.cannot_update', [
                        'attribute' => __('common.attribute.academic_year'),
                        'reason' => __('academic_year.reason.has_semesters')
                    ]));
                }
            }
        ];
    }
}
