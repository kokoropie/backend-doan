<?php

namespace App\Http\Controllers\Api;

use App\Enums\FeedbackStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Feedback\StoreFeedbackRequest;
use App\Http\Requests\ListResourceRequest;
use App\Http\Resources\FeedbackResource;
use App\Models\Feedback;
use App\Models\Score;
use App\Services\FeedbackService;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    protected FeedbackService $feedbackService;

    public function __construct(FeedbackService $feedbackService)
    {
        parent::__construct();
        $this->feedbackService = $feedbackService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ListResourceRequest $request)
    {
        return response()->success(FeedbackResource::collection(
            $this->feedbackService->paginate(
                $this->feedbackService->getModel()::with(['parent', 'student', 'teacher', 'score' => function ($query) {
                    $query->with('subject', 'semester', 'class.academicYear');
                }])
                    ->when($this->_USER->isTeacher(), function ($query) {
                        $query->where('teacher_id', $this->_USER->id);
                    })->when($this->_USER->isParent(), function ($query) {
                        $query->where('parent_id', $this->_USER->id);
                    })
                    ->where('status', $request->get('status', FeedbackStatus::PENDING))
                    ->latest('updated_at')
            , $request)
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFeedbackRequest $request)
    {
        $data = $request->only(['score', 'message']);

        $score = Score::find($data['score']);
        $data['parent_id'] = $this->_USER->id;
        $data['score_id'] = $score ? $score->id : null;
        $data['student_id'] = $score ? $score->student_id : null;
        $data['teacher_id'] = $score ? $score->teacher->id : null;

        $feedback = $this->feedbackService->save($data);

        return response()->success(new FeedbackResource($feedback), 'Gửi phản hồi thành công');
    }

    /**
     * Display the specified resource.
     */
    public function show(Feedback $feedback)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Feedback $feedback)
    {
        if ($request->has('status')) {
            $return = FeedbackStatus::tryFrom($request->status) ? $feedback->update(['status' => $request->status]) : null;
            if (!$return && $request->status === 'rejected') {
                $return = $feedback->update([
                    'status' => FeedbackStatus::RESOLVED,
                    'message' => "Từ chối: {$request->message} ($feedback->message)"
                ]);
            }
            if ($return) {
                return response()->success($feedback->status, 'Cập nhật trạng thái phản hồi thành công');
            } else {
                return response()->error('Cập nhật trạng thái phản hồi thất bại', 500);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Feedback $feedback)
    {
        //
    }
}
