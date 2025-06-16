<?php
namespace App\Services;

use App\Http\Requests\ListResourceRequest;
use App\Http\Requests\ManageClass\StoreSubjectRequest;
use App\Models\ClassModel;
use App\Models\ClassSubjectSemester;
use App\Models\Subject;

class SubjectService extends BaseService
{
    public function __construct()
    {
        parent::__construct();

        $this->setModel(Subject::class);
    }

    public function getList(ListResourceRequest $request)
    {
        $query = $this->getUser()->subjects();

        return $this->paginate($query, $request);
    }

    public function getListFromClass(ListResourceRequest $request, ClassModel $class)
    {
        return $this->paginate($class->subjectsWithMore()->with('teacher')->when($this->getUser()->isTeacher(), function ($query) {
            $query->where('teacher_id', $this->getUser()->id);
        })->when($request->has('semester'), function ($query) use ($request) {
            $query->where('semester_id', $request->semester);
        }), $request);
    }

    public function addClass(StoreSubjectRequest $request, ClassModel $class)
    {
        $created = ClassSubjectSemester::create([
            'class_id' => $class->id,
            'subject_id' => $request->subject,
            'semester_id' => $request->semester,
            'teacher_id' => $request->teacher,
        ]);

        return $created->load('subject', 'teacher', 'semester');
    }

    public function removeClass(ClassSubjectSemester $classSubjectSemester)
    {
        return $classSubjectSemester->delete();
    }

    public function updateTeacher(StoreSubjectRequest $request, ClassModel $class, Subject $subject)
    {
        $classSubjectSemester = ClassSubjectSemester::where('subject_id', $subject->id)
            ->where('semester_id', $request->semester)
            ->where('class_id', $class->id)
            ->firstOrFail();
        $classSubjectSemester->update([
            'teacher_id' => $request->teacher,
        ]);

        return $classSubjectSemester->load('subject', 'teacher', 'semester');
    }
}