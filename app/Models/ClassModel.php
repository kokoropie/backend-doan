<?php

namespace App\Models;

use App\Models\Traits\UseTimestamps;
use App\Services\ScoreService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassModel extends Model
{
    use SoftDeletes, HasFactory, UseTimestamps;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'academic_year_id',
        'teacher_id',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function semesters()
    {
        return $this->hasManyThrough(
            Semester::class,
            AcademicYear::class,
            'id',
            'academic_year_id',
            'academic_year_id',
            'id'
        )->distinct('semesters.id');
    }

    public function teachers()
    {
        return $this->hasManyThrough(
            User::class,
            ClassSubjectSemester::class,
            'class_id',
            'id',
            'id',
            'teacher_id'
        )->distinct('users.id');
    }

    public function primaryTeacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function subjects()
    {
        return $this->hasManyThrough(
            Subject::class,
            ClassSubjectSemester::class,
            'class_id',
            'id',
            'id',
            'subject_id'
        )->distinct('subjects.id');
    }

    public function subjectsWithMore()
    {
        return $this->hasMany(
            ClassSubjectSemester::class,
            'class_id',
            'id'
        )->with(['subject']);
    }

    public function students()
    {
        return $this->hasManyThrough(
            User::class,
            ClassStudent::class,
            'class_id',
            'id',
            'id',
            'student_id'
        )->distinct('users.id');
    }

    public function classStudents()
    {
        return $this->hasMany(ClassStudent::class, 'class_id', 'id');
    }

    public function scores()
    {
        return $this->hasManyThrough(
            Score::class,
            ClassSubjectSemester::class,
            'class_id',
            'class_subject_semester_id',
            'id',
            'id'
        );
    }

    public function scopeHasTeachers($query, $ids = null)
    {
        if (is_null($ids)) {
            return $query;
        }
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        if (count($ids) === 0) {
            return $query;
        }
        return $query->whereHas('teachers', function ($query) use ($ids) {
            $query->whereIn('users.id', $ids);
        })
        ->orWhere('classes.teacher_id', $ids);
    }

    protected static function booted()
    {
        static::addGlobalScope('hasYear', function ($builder) {
            $builder->whereHas('academicYear');
        });
    }

    public function getAvgScoreAttribute()
    {
        return resolve(ScoreService::class)->getAvg($this->scores);
    }
}
