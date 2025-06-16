<?php

namespace App\Http\Controllers\Api;

use App\Enums\ScoreType;
use App\Http\Controllers\Controller;
use App\Models\Score;
use App\Models\User;
use App\Services\ScoreService;
use Illuminate\Http\Request;

class UserScoreController extends Controller
{
    public function index(Request $request, User $user)
    {
        $class = $request->year ? $user->classes()->where('classes.academic_year_id', $request->year)->first() : $user->currentClass;

        $scores = $user->scores()
            ->with(['subject', 'semester'])
            ->when($class, function ($query) use ($class) {
                $css = $class->subjectsWithMore()->pluck('id');
                $query->whereIn('class_subject_semester_id', $css);
            })->get();

        $return = [];

        $allScores = Score::whereIn('class_subject_semester_id', $scores->pluck('class_subject_semester_id'))
            ->get()
            ->groupBy('class_subject_semester_id');

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
                'score' => $score->score,
                'semester' => $score->semester->name
            ];
        }

        return response()->success($return, 'Lấy danh sách điểm thành công');
    }
}
