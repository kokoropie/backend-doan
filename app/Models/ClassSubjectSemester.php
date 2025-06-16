<?php

namespace App\Models;

use App\Models\Traits\UseTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassSubjectSemester extends Model
{
    use SoftDeletes, UseTimestamps;

    protected $table = 'class_subject_semester';

    protected $fillable = [
        'class_id',
        'subject_id',
        'semester_id',
        'teacher_id',
    ];

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function scores()
    {
        return $this->hasMany(Score::class, 'class_subject_semester_id');
    }
}
