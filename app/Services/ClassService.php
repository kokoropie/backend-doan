<?php
namespace App\Services;

use App\Http\Requests\ListResourceRequest;
use App\Http\Requests\ManageClass\StoreClassRequest;
use App\Http\Requests\ManageClass\UpdateClassRequest;
use App\Models\ClassModel;

class ClassService extends BaseService
{
    public function __construct()
    {
        parent::__construct();

        $this->setModel(ClassModel::class);
    }

    public function getList(ListResourceRequest $request)
    {
        $query = $this->getModel()::query();
        if (!$request->boolean('no_relations')) {
            $query = $query->withCount(['students'])->with(['semesters', 'academicYear', 'primaryTeacher']);
        }
        return $this->paginate($query->when($this->getUser()->isStudent(), function ($query) {
                $query->whereHas('students', function ($q) {
                    $q->where('users.id', $this->getUser()->id);
                });
            })->when($this->getUser()->isTeacher(), function ($query) {
                $query->hasTeachers($this->getUser()->id);
            })->when($request->has('year') && $request->year, function ($query) use ($request) {
                $query->where('academic_year_id', $request->year);
            }, function ($query) {
                $year = resolve(AcademicYearService::class)->getCurrent();
                if ($year) {
                    $query->where('academic_year_id', $year->id);
                }
            }), $request);
    }

    public function get(ClassModel $class, array $subjectWith = [])
    {
        return $class->load(['semesters', 'academicYear', 'primaryTeacher', 'students', 'teachers', 'subjectsWithMore' => function ($query) use ($subjectWith) {
            $query->with($subjectWith);
        }]);
    }

    public function isClassExists(string $name, int $academicYearId, int|null $exclude = null): bool
    {
        return $this->getModel()::where('name', $name)->where('academic_year_id', $academicYearId)->where('id', '<>', $exclude)->exists();
    }
}