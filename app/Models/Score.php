<?php

namespace App\Models;

use App\Enums\ScoreType;
use App\Models\Traits\UseTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Score extends Model
{
    use SoftDeletes, UseTimestamps;

    protected $table = 'scores';

    protected $fillable = [
        'score',
        'student_id',
        'class_subject_semester_id',
        'type'
    ];

    protected function casts()
    {
        return [
            'score' => 'float',
            'type' => ScoreType::class,
        ];
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function subject()
    {
        return $this->hasOneThrough(
            Subject::class,
            ClassSubjectSemester::class,
            'id',
            'id',
            'class_subject_semester_id',
            'subject_id'
        );
    }

    public function class()
    {
        return $this->hasOneThrough(
            ClassModel::class,
            ClassSubjectSemester::class,
            'id',
            'id',
            'class_subject_semester_id',
            'class_id'
        );
    }

    public function teacher()
    {
        return $this->hasOneThrough(
            User::class,
            ClassSubjectSemester::class,
            'id',
            'id',
            'class_subject_semester_id',
            'teacher_id'
        );
    }

    public function semester()
    {
        return $this->hasOneThrough(
            Semester::class,
            ClassSubjectSemester::class,
            'id',
            'id',
            'class_subject_semester_id',
            'semester_id'
        );
    }
}
