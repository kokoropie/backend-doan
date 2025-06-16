<?php

namespace App\Exports;

use App\Enums\ScoreType;
use App\Models\ClassModel;
use App\Services\ScoreService;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ClassScoreExport implements WithMultipleSheets
{
    private $students = [];
    private $ranks = [];
    private $css;
    
    public function __construct(
        private ClassModel $class
    ) {}

    public function sheets(): array
    {
        $sheets = [];
        $this->class->load(['students' => function ($query) {
            $query->orderBy('first_name')->orderBy('last_name');
        }, 'subjects']);
        $this->class->loadCount('students');
        $this->css = $this->class->subjectsWithMore()->pluck('id');
        $this->students = $this->class->students;
        $data = [];
        foreach ($this->students as $student) {
            $data[$student->id] = $this->calcStudentScores($student);
        }
        $this->handleRanks();
        foreach ($this->students as $student) {
            $sheets[] = new ClassScoreSheet($student->full_name, $data[$student->id], $this->ranks);
        }

        return $sheets;
    }

    private function handleRanks()
    {
        $this->ranks = collect($this->ranks)
            ->map(function ($item) {
                return collect($item)->sortByDesc('score')->values()->map(function($value, $key) {
                    $value['rank'] = $key + 1;
                    return $value;
                })->toArray();
            })
            ->toArray();
    }

    private function calcStudentScores($student)
    {
        $scores = $student->scores()
            ->with(['subject'])
            ->whereIn('class_subject_semester_id', $this->css)
            ->get();

        $return = [];

        foreach ($scores as $score) {
            if (!isset($return[$score->subject->id])) {
                $return[$score->subject->id] = [];
                if (!isset($this->ranks[$score->subject->id])) {
                    $this->ranks[$score->subject->id] = [];
                }

                $this->ranks[$score->subject->id][] = [
                    "id" => $student->id,
                    "score" => resolve(ScoreService::class)->getAvg($scores->where(fn ($item) => $item->subject->id == $score->subject->id) ?? collect())
                ];
            }
            $type = $score->type;
            if ($type instanceof ScoreType) {
                $type = $type->value;
            }
            if (!isset($return[$score->subject->id][$type])) {
                $return[$score->subject->id][$type] = [];
            }
            $return[$score->subject->id][$type][] = [
                'id' => $score->id,
                'score' => $score->score
            ];
        }

        $results = [];

        foreach ($this->class->subjects as $subject) {
            $subjectScores = $return[$subject->id] ?? [];

            $results[] = [
                $subject->name,
                isset($subjectScores['other']) ? implode('|', array_column($subjectScores['other'], 'score')) : '',
                isset($subjectScores['short_test']) ? implode('|', array_column($subjectScores['short_test'], 'score')) : '',
                isset($subjectScores['long_test']) ? implode('|', array_column($subjectScores['long_test'], 'score')) : '',
                isset($subjectScores['midterm']) ? implode('|', array_column($subjectScores['midterm'], 'score')) : '',
                isset($subjectScores['final']) ? implode('|', array_column($subjectScores['final'], 'score')) : '',
                '',
                $subject->id . '_' . $student->id
            ];
        }
        
        return $results;
    }
}
