<?php
namespace App\Services;

use App\Enums\ScoreType;
use App\Http\Requests\ListResourceRequest;
use App\Models\ClassModel;
use App\Models\Score;

class ScoreService extends BaseService
{
    public function __construct()
    {
        parent::__construct();

        $this->setModel(Score::class);
    }

    public function getListInClass(ListResourceRequest $request, ClassModel $class)
    {
        $scores = $class->scores();
        return $this->paginate($scores, $request);
    }

    public function getAvg($scores)
    {
        $total = 0;
        $count = 0;

        foreach ($scores as $score) {
            $multiple = match ($score->type) {
                'midterm', ScoreType::MIDTERM => 2,
                'final', ScoreType::FINAL => 3,
                default => 1,
            };
            $total += $score->score * $multiple;
            $count += $multiple;
        }

        return $count > 0 ? round($total / $count, 2) : 0;
    }
}