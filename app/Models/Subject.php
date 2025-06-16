<?php

namespace App\Models;

use App\Models\Traits\UseTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use SoftDeletes, UseTimestamps;

    protected $table = 'subjects';

    protected $fillable = [
        'name',
    ];

    public function classes()
    {
        return $this->hasManyThrough(
            ClassModel::class,
            ClassSubjectSemester::class,
            'subject_id',
            'id',
            'id',
            'class_id'
        );
    }

    public function semesters()
    {
        return $this->hasManyThrough(
            Semester::class,
            ClassSubjectSemester::class,
            'subject_id',
            'id',
            'id',
            'semester_id'
        );
    }

    public function teachers()
    {
        return $this->hasManyThrough(
            User::class,
            ClassSubjectSemester::class,
            'subject_id',
            'id',
            'id',
            'teacher_id'
        );
    }

    public function scores()
    {
        return $this->hasManyThrough(
            Score::class,
            ClassSubjectSemester::class,
            'subject_id',
            'class_subject_semester_id',
            'id',
            'id'
        );
    }
}
