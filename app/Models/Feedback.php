<?php

namespace App\Models;

use App\Enums\FeedbackStatus;
use App\Models\Traits\UseTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feedback extends Model
{
    use SoftDeletes, UseTimestamps;
    
    protected $table = 'feedback';

    protected $fillable = [
        'teacher_id',
        'student_id',
        'score_id',
        'parent_id',
        'message',
        'status',
    ];

    protected function casts()
    {
        return [
            'status' => FeedbackStatus::class,
        ];
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function score()
    {
        return $this->belongsTo(Score::class, 'score_id');
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function class()
    {
        return $this->student()->currentClass();
    }
}
