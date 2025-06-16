<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Enums\FeedbackStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListResourceRequest;
use App\Http\Resources\FeedbackResource;
use App\Models\Feedback;
use App\Models\Notification;
use App\Services\FeedbackService;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    protected FeedbackService $feedbackService;

    public function __construct(FeedbackService $feedbackService)
    {
        parent::__construct();
        $this->feedbackService = $feedbackService;
    }

    public function count()
    {
        $count = [
            'feedback' => Feedback::where('teacher_id', $this->_USER->id)->where('status', FeedbackStatus::PENDING)->count(),
            'notifications' => Notification::where('sender_id', $this->_USER->id)->count(),
        ];

        return response()->success($count, 'Thống kê thành công');
    }

    public function feedback(ListResourceRequest $request)
    {
        $feedbacks = $this->feedbackService->getModel()::with(['parent', 'student', 'teacher', 'score' => function ($query) {
                $query->with('subject', 'semester', 'class.academicYear');
            }])
            ->where('teacher_id', $this->_USER->id)
            ->where('status', FeedbackStatus::PENDING)
            ->latest();

        return response()->success(FeedbackResource::collection($this->feedbackService->paginate($feedbacks, $request)), 'Lấy danh sách phản hồi thành công');
    }
}
