<?php

namespace App\Http\Controllers\Api;

use App\Exports\ClassScoreExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListResourceRequest;
use App\Models\ClassModel;
use App\Models\ClassSubjectSemester;
use App\Models\Score;
use App\Models\Subject;
use App\Services\BaseService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ClassScoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ListResourceRequest $request, ClassModel $class)
    {
        $links = ClassSubjectSemester::select(
            'subjects.name', 
            'subjects.id as subject_id', 
            'class_subject_semester.teacher_id',
            'class_subject_semester.id as linked_id'
        )->rightJoin('subjects', 'subjects.id', '=', 'class_subject_semester.subject_id')
        ->where('class_subject_semester.class_id', $class->id)
        ->where('class_subject_semester.semester_id', $request->get('semester_id'))
        ->orderBy('subjects.name');

        $scores = Score::select(
                'scores.*', 
                'subjects.name', 
                'subjects.id as subject_id', 
                'class_subject_semester.teacher_id',
                'class_subject_semester.id as linked_id'
            )->rightJoin('class_subject_semester', 'class_subject_semester.id', '=', 'scores.class_subject_semester_id')
            ->rightJoin('subjects', 'subjects.id', '=', 'class_subject_semester.subject_id')
            ->where('class_subject_semester.class_id', $class->id)
            ->where('class_subject_semester.semester_id', $request->get('semester_id'))
            ->where('scores.type', $request->get('type'))
            ->orderBy('subjects.name');

        if ($this->_USER->isTeacher()) {
            if ($class->teacher_id != $this->_USER->id) {
                $scores->where('class_subject_semester.teacher_id', $this->_USER->id);
                $links->where('class_subject_semester.teacher_id', $this->_USER->id);
            }
        }

        $scores = $scores->get();
        $links = $links->get();

        $groupByUser = $scores->groupBy('student_id');

        foreach ($groupByUser as $studentId => $score) {
            $get = collect($links)->whereNotIn('linked_id', $score->pluck('linked_id')->toArray());
            if ($get->isNotEmpty()) {
                foreach ($get as $item) {
                    $scores->push((object)[
                        'id' => null,
                        'score' => null,
                        'student_id' => $studentId,
                        'linked_id' => $item->linked_id,
                        'type' => $request->get('type'),
                        'name' => $item->name,
                        'subject_id' => $item->subject_id,
                        'teacher_id' => $item->teacher_id
                    ]);
                }
            }
        }

        $scores = $scores->map(function ($score) use ($class) {
            if ($this->_USER->isAdmin()) {
                $score->show_actions = true;
            } else if ($this->_USER->isTeacher()) {
                if ($score->teacher_id == $this->_USER->id) {
                    $score->show_actions = true;
                }
            }
            return $score;
        });

        return response()->success($scores, 'Lấy danh sách bảng điểm thành công');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, ClassModel $class)
    {
        $request->validate([
            'score' => 'required|numeric',
            'student_id' => 'required',
            'class_subject_semester_id' => 'required',
            'type' => 'required'
        ]);

        $score = Score::create($request->all());

        return response()->success($score, 'Thêm điểm thành công', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ClassModel $class, string $id)
    {
        $request->validate([
            'score' => 'required|numeric'
        ]);

        $score = Score::findOrFail($id);
        $score->update($request->only(['score']));

        return response()->success($score, 'Cập nhật điểm thành công');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClassModel $class, string $id)
    {
        $score = Score::findOrFail($id);
        $score->delete();

        return response()->success($score, 'Xoá điểm thành công');
    }

    public function excel(Request $request, ClassModel $class)
    {
        $nameFile = 'Bảng điểm lớp ' . $class->name . '.xlsx';
        return Excel::download(new ClassScoreExport($class), $nameFile);
    }
}
