<?php

namespace App\Models;

use App\Models\Traits\UseTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicYear extends Model
{
    use SoftDeletes, UseTimestamps;

    protected $table = 'academic_years';

    protected $fillable = [
        'from',
        'to',
        'current',
    ];

    protected $appends = [
        'year',
    ];

    protected function casts()
    {
        return [
            'from' => 'date',
            'to' => 'date',
            'current' => 'boolean',
        ];
    }

    public function classes()
    {
        return $this->hasMany(ClassModel::class, 'academic_year_id');
    }

    public function semesters()
    {
        return $this->hasMany(Semester::class, 'academic_year_id');
    }

    public function students()
    {
        return $this->hasManyThrough(
            ClassStudent::class,
            ClassModel::class,
            'academic_year_id',
            'class_id',
            'id',
            'id'
        )->with('student');
    }

    public function getYearAttribute()
    {
        return $this->from->format('Y') . ' - ' . $this->to->format('Y');
    }

    public function hasClasses()
    {
        return $this->classes()->exists();
    }

    public function hasSemesters()
    {
        return $this->semesters()->exists();
    }
}
