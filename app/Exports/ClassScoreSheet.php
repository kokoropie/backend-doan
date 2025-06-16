<?php

namespace App\Exports;

use App\Enums\ScoreType;
use App\Models\ClassModel;
use App\Models\Score;
use App\Models\Subject;
use App\Models\User;
use App\Services\ScoreService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ClassScoreSheet implements FromArray, WithHeadings, WithTitle
{
    public function __construct(
        private ClassModel $class,
        private User $student,
        private $subjects,
        private $allScores,
        private $css
    ) {}

    public function headings(): array
    {
        return [
            'Môn',
            'Điểm miệng',
            'Điểm 15 phút',
            'Điểm 1 tiết',
            'Điểm giữa kỹ',
            'Điểm cuối kỳ',
            'Điểm trung bình',
            'Hạng'
        ];
    }

    public function array(): array
    {
        $return = [];

        $scores = $this->student->scores()
            ->with(['subject'])
            ->whereIn('class_subject_semester_id', $this->css)
            ->get();

        $return = [];

        $allScores = $this->allScores;

        foreach ($scores as $score) {
            if (!isset($return[$score->subject->id])) {
                $return[$score->subject->id] = [];
                $avgScore = resolve(ScoreService::class)->getAvg($allScores[$score->class_subject_semester_id] ?? collect());
                $return[$score->subject->id . '_avg'] = $avgScore;
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

        foreach ($this->subjects as $subject) {
            $subjectScores = $return[$subject->id] ?? [];
            $avgScore = $return[$subject->id . '_avg'] ?? 0;
            $rank = round((1 - $avgScore / $this->class->avg_score) * $this->class->students_count, 2);
            $rank = $rank < 1 ? 1 : $rank;

            $results[] = [
                $this->headings()[0] => $subject->name,
                $this->headings()[1] => isset($subjectScores['other']) ? implode('|', array_column($subjectScores['other'], 'score')) : '',
                $this->headings()[2] => isset($subjectScores['short_test']) ? implode('|', array_column($subjectScores['short_test'], 'score')) : '',
                $this->headings()[3] => isset($subjectScores['long_test']) ? implode('|', array_column($subjectScores['long_test'], 'score')) : '',
                $this->headings()[4] => isset($subjectScores['midterm']) ? implode('|', array_column($subjectScores['midterm'], 'score')) : '',
                $this->headings()[5] => isset($subjectScores['final']) ? implode('|', array_column($subjectScores['final'], 'score')) : '',
                $this->headings()[6] => $avgScore,
                $this->headings()[7] => $rank
            ];
        }
        
        return $results;
    }

    public function title(): string
    {
        return $this->student->full_name;
    }
}
