<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\ClassModel;
use App\Models\ClassSubjectSemester;
use App\Models\Notification;
use App\Models\ParentStudent;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notifications = $this->_USER->receivedNotifications()
            ->with(['sender'])
            ->withPivot('is_read')
            ->latest()
            ->get();

        return response()->success(NotificationResource::collection($notifications), 'Lấy thông báo thành công');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:all,class,student,teacher,parent',
        ]);

        $notification = Notification::create([
            'sender_id' => $this->_USER->id,
            'title' => $data['title'],
            'message' => $data['message'],
            'receiver_type' => $data['receiver_type'],
        ]);

        switch ($data['type']) {
            case 'all':
                return $this->sendAll($request, $notification);
            case 'class':
                return $this->sendClass($request, $notification);
            case 'student':
                return $this->sendStudent($request, $notification);
            case 'teacher':
                return $this->sendTeacher($request, $notification);
            case 'parent':
                return $this->sendParent($request, $notification);
            default:
                return response()->error([], 'Invalid type', 400);
        }
    }

    private function sendAll(Request $request, Notification $notification)
    {
        $users = User::where('id', '<>', $this->_USER->id)->pluck('id');
        $notification->receivers()->sync($users);

        return response()->success($notification, 'Thông báo đã được gửi đến tất cả người dùng');
    }

    private function sendClass(Request $request, Notification $notification)
    {
        $class = ClassModel::find($request->get('class_id'));
        if (!$class) {
            return response()->error([], 'Lớp không tồn tại.', 400);
        }

        $students = User::whereHas('currentClass', function ($query) use ($class) {
            $query->where('classes.id', $class->id);
        })->pluck('id');

        $parents = ParentStudent::whereIn('student_id', $students)->pluck('parent_id');

        $teachers = ClassSubjectSemester::where('class_id', $class->id)->pluck('teacher_id')->unique();

        $ids = collect([])->merge($students)
            ->merge($parents)
            ->merge($teachers)
            ->merge([$class->teacher_id])
            ->unique()
            ->filter(function ($id) {
                return $id !== $this->_USER->id;
            });

        $notification->receivers()->sync($ids);

        return response()->success($notification, 'Thông báo đã được gửi đến lớp học');
    }

    private function sendStudent(Request $request, Notification $notification)
    {
        $ids = $request->get('ids');
        if (empty($ids)) {
            $ids = User::hasStudent()
                ->where('id', '<>', $this->_USER->id)
                ->pluck('id');
        }
        $notification->receivers()->sync($ids);

        return response()->success($notification, 'Thông báo đã được gửi đến học sinh');
    }

    private function sendTeacher(Request $request, Notification $notification)
    {
        $ids = $request->get('ids');
        if (empty($ids)) {
            $ids = User::hasTeacher()
                ->where('id', '<>', $this->_USER->id)
                ->pluck('id');
        }
        $notification->receivers()->sync($ids);

        return response()->success($notification, 'Thông báo đã được gửi đến giáo viên');
    }

    private function sendParent(Request $request, Notification $notification)
    {
        $ids = $request->get('ids');
        if (empty($ids)) {
            $ids = User::hasParent()
                ->where('id', '<>', $this->_USER->id)
                ->pluck('id');
        }
        $notification->receivers()->sync($ids);

        return response()->success($notification, 'Thông báo đã được gửi đến phụ huynh');
    }

    /**
     * Display the specified resource.
     */
    public function show(Notification $notification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Notification $notification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notification $notification)
    {
        //
    }

    public function markAsRead(Request $request)
    {
        UserNotification::where('user_id', $this->_USER->id)
            ->where(['is_read' => false])
            ->update(['is_read' => true]);

        return response()->success([], 'Đánh dấu tất cả thông báo là đã đọc');
    }
}
