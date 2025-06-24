<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassSubjectSemester;
use App\Models\Feedback;
use App\Models\Notification;
use App\Models\User;
use App\Services\AcademicYearService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected AcademicYearService $academicYearService;

    public function __construct(AcademicYearService $academicYearService)
    {
        $this->academicYearService = $academicYearService;
    }

    public function count()
    {
        $count = [
            'students' => User::hasStudent()->count(),
            'teachers' => User::hasTeacher()->count(),
            'parents' => User::hasParent()->count(),
            'feedback' => Feedback::count(),
            'notifications' => Notification::count(),
        ];

        return response()->success($count, 'Thống kê thành công');
    }

    public function topScore()
    {
        $year = request()->has('year') ? $this->academicYearService->find(request()->get('year')) : $this->academicYearService->getCurrent();

        if (!$year) {
            return response()->error('Năm học không hợp lệ', 400);
        }

        $semesters = $year->semesters;

        $css = ClassSubjectSemester::with(['scores.student.currentClass'])
            ->whereIn('semester_id', $semesters->pluck('id'))
            ->get();

        $topScores = $css->map(function ($cs) {
            return $cs->scores->map(function ($score) {
                return [
                    'student_id' => $score->student_id,
                    'full_name' => $score->student->full_name,
                    'score' => $score->score,
                    'class_name' => $score->student->currentClass->name
                ];
            });
        });

        $return = [];

        foreach ($topScores as $scores) {
            foreach ($scores as $score) {
                if (!isset($return[$score['student_id']])) {
                    $return[$score['student_id']] = [
                        'student_id' => $score['student_id'],
                        'full_name' => $score['full_name'],
                        'class_name' => $score['class_name'],
                        'total_score' => 0,
                        'count' => 0
                    ];
                }
                $return[$score['student_id']]['total_score'] += $score['score'];
                $return[$score['student_id']]['count']++;
            }
        }
        $return = array_values($return);
        usort($return, function ($a, $b) {
            return $b['total_score'] / $b['count'] <=> $a['total_score'] / $a['count'];
        });
        $return = array_map(function ($item) {
            $item['score'] = round($item['total_score'] / $item['count'], 2);
            unset($item['total_score'], $item['count']);
            return $item;
        }, $return);
        $return = array_slice($return, 0, 10);

        return response()->success($return, 'Lấy danh sách điểm cao nhất thành công');
    }

    public function feedback()
    {
        $year = (int) (request()->has('year') ? request()->get('year') : now()->year);
        $from = now()->setYear($year)->startOfYear();
        $to = now()->setYear($year)->endOfYear();
        $feedbacks = Feedback::whereBetween('created_at', [$from, $to])
            ->groupByRaw('MONTH(created_at)')
            ->selectRaw('COUNT(*) as count, MONTH(created_at) as month')
            ->pluck('count', 'month');

        $return = [];
        for ($i = 1; $i <= 12; $i++) {
            $return[$i] = [
                'key' => $i,
                'value' => $feedbacks[$i] ?? 0
            ];
        }

        return response()->success(array_values($return), 'Lấy thống kê phản hồi thành công');
    }

    public function students()
    {
        $years = AcademicYear::withCount('students')->orderBy('from')->orderBy('to')->get();
        
        $return = $years->map(function ($year) {
            return [
                'key' => $year->year,
                'value' => $year->students_count
            ];
        });

        return response()->success($return->toArray(), 'Lấy thống kê học sinh thành công');
    }
}
