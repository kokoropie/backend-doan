<?php

namespace App\Models;

use App\Models\Traits\UseTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassStudent extends Model
{
    use SoftDeletes, UseTimestamps;

    protected $table = 'class_student';

    protected $fillable = [
        'class_id',
        'student_id',
        'academic_year_id',
    ];

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function parents()
    {
        return $this->hasManyThrough(
            User::class,
            ParentStudent::class,
            'student_id',
            'id',
            'student_id',
            'parent_id'
        );
    }
}
