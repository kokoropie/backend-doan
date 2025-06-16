<?php
namespace App\Services;

use App\Http\Requests\ListResourceRequest;
use App\Http\Requests\Semester\StoreSemesterRequest;
use App\Http\Requests\Semester\UpdateSemesterRequest;
use App\Models\AcademicYear;
use App\Models\ClassSubjectSemester;
use App\Models\Semester;

class SemesterService extends BaseService
{
    public function __construct()
    {
        parent::__construct();

        $this->setModel(Semester::class);
    }

    public function getList(ListResourceRequest $request, AcademicYear $academicYear)
    {
        $semester = $this->getModel()::where('academic_year_id', $academicYear->id);
        return $this->paginate($semester->withCount(['classes' => function ($query) {
                $query->select(\DB::raw('count(distinct classes.id)'))
                    ->when($this->getUser()->isTeacher(), fn ($q) => $query->hasTeachers($this->getUser()->id))
                    ->when($this->getUser()->isParent(), function ($query) {
                        $query->whereHas('students.parents', function ($q) {
                            $q->where('laravel_reserved_0.id', $this->getUser()->id);
                        });
                    });
            }]), $request);
    }

    public function save($request)
    {
        $academicYear = $request->route('academic_year');
        
        $semester = $academicYear->semesters()->create($request->validated());

        return $semester;
    }

    public function update($request, $semester)
    {
        $semester->update($request->validated());

        return $semester->fresh();
    }

    public function copy($request, $class)
    {
        $subjects = ClassSubjectSemester::where('class_id', $class->id)
            ->where('semester_id', $request->from)
            ->get();

        if ($subjects->isEmpty()) {
            return null;
        }

        $now = now();
        $this->start();
        foreach ($subjects as $subject) {
            ClassSubjectSemester::updateOrInsert([
                'class_id' => $class->id,
                'subject_id' => $subject->subject_id,
                'semester_id' => $request->to,
            ], [
                'teacher_id' => $subject->teacher_id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
        $this->end();

        return [];
    }
}