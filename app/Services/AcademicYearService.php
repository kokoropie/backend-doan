<?php
namespace App\Services;

use App\Http\Requests\AcademicYear\StoreAcademicYearRequest;
use App\Http\Requests\AcademicYear\UpdateAcademicYearRequest;
use App\Http\Requests\ListResourceRequest;
use App\Models\AcademicYear;

class AcademicYearService extends BaseService
{
    public function __construct()
    {
        parent::__construct();

        $this->setModel(AcademicYear::class);
    }

    public function getList(ListResourceRequest $request)
    {
        $query = $this->getModel()::query();
        if (!$request->boolean('no_relations')) {
            $query = $query->withCount(['classes' => function ($query) {
                $query->when($this->getUser()->isTeacher(), fn ($q) => 
                    $q->hasTeachers($this->getUser()->id)
                )->when($this->getUser()->isParent(), function ($query) {
                    $query->whereHas('students.parents', function ($q) {
                        $q->where('laravel_reserved_0.id', $this->getUser()->id);
                    });
                });
            }, 'students' => function ($query) {
                $query->when($this->getUser()->isParent(), function ($query) {
                    $query->whereHas('parents', function ($q) {
                        $q->where('users.id', $this->getUser()->id);
                    });
                });
            }])->with(['semesters']);
        }
        return $this->paginate($query->when($this->getUser()->isStudent(), function ($query) {
                $query->whereHas('students', function ($q) {
                    $q->where('student_id', $this->getUser()->id);
                });
            })->when($this->getUser()->isTeacher(), function ($query) {
                $query->whereHas('classes', function ($q) {
                    $q->hasTeachers($this->getUser()->id);
                });
            })->when($this->getUser()->isParent(), function ($query) {
                $query->whereHas('students.parents', function ($q) {
                    $q->where('users.id', $this->getUser()->id);
                });
            })->orderByDesc('current'), $request);
    }

    public function save($request)
    {
        if ($request->boolean('move_classes')) {
            
            $this->getModel()::query()->where('current', true)->update(['current' => false]);
        }

        $return = parent::save($request);

        if ($request->boolean('move_classes')) {
            $current = $this->getModel()::query()->where('current', true)->first();
            $this->changeYear($current, $return, !$request->boolean('current_teachers', true));
        }

        return $return;
    }

    public function changeYear(?AcademicYear $from, AcademicYear $to, bool $removeTeacher = false)
    {
        $to->current = true;
        $to->save();

        if (!$from) {
            return;
        }
        $update = [
            'academic_year_id' => $to->id,
        ];

        if ($removeTeacher) {
            $update['teacher_id'] = null;
        }
        
        $from->update(['current' => false]);

        if (resolve(ClassService::class)->getModel()::where('academic_year_id', $to->id)->doesntExist()) {
            resolve(ClassService::class)->getModel()::where('academic_year_id', $from->id)
                ->chunk(100, function ($classes) use ($update) {
                    foreach ($classes as $class) {
                        $newClass = $class->replicate()->fill($update);
                        $newClass->save();
                    }
                });
        } 

        return true;
    }

    public function getCurrent(): ?AcademicYear
    {
        return $this->getModel()::query()->where('current', true)->first();
    }

    public function delete($year)
    {
        if ($year->current) {
            $year->current = false;
            $year->save();

            $this->getModel()::query()->where('id', '<>', $year->id)->latest('updated_at')->update(['current' => true]);
        }
        return parent::delete($year);
    }
}