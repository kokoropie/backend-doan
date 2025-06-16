<?php

namespace App\Models;

use App\Models\Traits\UseTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Semester extends Model
{
    use SoftDeletes, UseTimestamps;

    protected $table = 'semesters';

    protected $fillable = [
        'academic_year_id',
        'name'
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function classes()
    {
        return $this->belongsToMany(
            ClassModel::class, 
            ClassSubjectSemester::class,
            'semester_id',
            'class_id',
            'id', 
            'id'
        )->distinct('classes.id');
    }

    public function subjects()
    {
        return $this->hasManyThrough(
            Subject::class, 
            ClassSubjectSemester::class,
            'semester_id',
            'id',
            'id', 
            'subject_id' 
        )->distinct('subjects.id');
    }

    public function hasClasses()
    {
        return $this->classes()->exists();
    }

    public function hasSubjects()
    {
        return $this->subjects()->exists();
    }
}
