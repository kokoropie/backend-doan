<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListResourceRequest;
use App\Http\Resources\ClassResource;
use App\Services\ScoreService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    protected ScoreService $scoreService;
    public function __construct(ScoreService $scoreService)
    {
        parent::__construct();
        $this->scoreService = $scoreService;
    }

    public function count(Request $request)
    {
        $class = null;
        if ($request->has('year')) {
            $class = $this->_USER->classes()->where('classes.academic_year_id', $request->year)->first();
        }
        $class = $class ?: $this->_USER->currentClass;
        $class->load('primaryTeacher', 'scores');
        $totalStudents = $class->students()->count();
        

        $count = [
            'class' => ClassResource::make($class),
            'total_students' => $totalStudents,
            'avg_score' => $class->avg_score,
        ];

        return response()->success($count, 'Thống kê thành công');
    }

    public function scores(ListResourceRequest $request)
    {
        $class = null;
        if ($request->has('year')) {
            $class = $this->_USER->classes()->where('classes.academic_year_id', $request->year)->first();
        }
        $class = $class ?: $this->_USER->currentClass;

        $scoress = $class->scores()->with('subject')->where('scores.student_id', $this->_USER->id)->get()->groupBy('class_subject_semester_id');
        $return = [];

        foreach ($scoress as $scores) {
            $avg = $this->scoreService->getAvg($scores);
            $subject = $scores->first()->subject;
            if (!isset($return[$subject->id])) {
                $return[$subject->id] = [
                    'subject' => $subject,
                    'scores' => [],
                ];
            }
            $return[$subject->id]['scores'][] = $avg;
        }

        $return = array_map(function ($item) {
            return [
                'id' => $item['subject']->id,
                'subject' => $item['subject']->name,
                'score' => round(array_sum($item['scores']) / count($item['scores']), 2),
            ];
        }, $return);

        return response()->success(array_values($return), 'Lấy điểm thành công');
    }

    public function charts(Request $request)
    {
        $classes = $this->_USER->classes()
            ->with(['academicYear', 'scores' => function ($query) use ($request) {
                $query->where('student_id', $this->_USER->id)
                    ->whereHas('subject', function ($query) use ($request) {
                        if ($request->has('subject') && $request->subject) {
                            $query->where('subjects.id', $request->subject);
                        }
                    });
            }])
            ->get();

        $return = [];
        foreach ($classes as $class) {
            $avg = $this->scoreService->getAvg($class->scores);
            $return[] = [
                'key' => $class->academicYear->year,
                'value' => round($avg, 2),
            ];
        }

        usort($return, function ($a, $b) {
            return $a['key'] <=> $b['key'];
        });

        return response()->success(array_values($return), 'Lấy biểu đồ thành công');
    }
}
