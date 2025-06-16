<?php

namespace App\Exports;

use App\Models\ClassModel;
use App\Models\Score;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ClassScoreExport implements WithMultipleSheets
{
    public function __construct(
        private ClassModel $class
    ) {}

    public function sheets(): array
    {
        $sheets = [];
        $this->class->load(['students' => function ($query) {
            $query->limit(10);
        }, 'subjects']);
        $this->class->loadCount('students');
        $css = $this->class->subjectsWithMore()->pluck('id');
        $allScores = Score::whereIn('class_subject_semester_id', $css)
            ->get()
            ->groupBy('class_subject_semester_id');
        foreach ($this->class->students as $student) {
            $sheets[] = new ClassScoreSheet($this->class, $student, $this->class->subjects, $allScores, $css);
        }
        return $sheets;
    }
}
