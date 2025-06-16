<?php

namespace App\Http\Requests\ManageClass;

class UpdateClassRequest extends StoreClassRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        unset($rules['academic_year_id']);
        return $rules;
    }
}
