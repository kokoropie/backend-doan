<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListResourceRequest;
use App\Http\Requests\ManageClass\StoreSubjectRequest;
use App\Http\Requests\Semester\CopySemesterRequest;
use App\Http\Resources\SubjectWithMoreResource;
use App\Models\ClassModel;
use App\Models\ClassSubjectSemester;
use App\Models\Subject;
use App\Services\SemesterService;
use App\Services\SubjectService;
use Illuminate\Http\Request;

class ClassSubjectController extends Controller
{
    protected SubjectService $subjectService;

    public function __construct(SubjectService $subjectService)
    {
        $this->subjectService = $subjectService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ListResourceRequest $request, ClassModel $class)
    {
        $subjects = $this->subjectService->getListFromClass($request, $class);

        return response()->success(SubjectWithMoreResource::collection($subjects));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubjectRequest $request, ClassModel $class)
    {
        try {
            $subject = $this->subjectService->addClass($request, $class);

            return response()->success(new SubjectWithMoreResource($subject), __('common.success.store', ['name' => __('common.attribute.subject')]), 201);
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ClassModel $class, Subject $subject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreSubjectRequest $request, ClassModel $class, Subject $subject)
    {
        try {
            $subject = $this->subjectService->updateTeacher($request, $class, $subject);

            return response()->success(new SubjectWithMoreResource($subject), __('common.success.update', ['name' => __('common.attribute.subject')]), 200);
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClassModel $class, int $subject)
    {
        try {
            $this->subjectService->removeClass(ClassSubjectSemester::findOrFail($subject));

            return response()->success(null, __('common.success.delete', ['name' => __('common.attribute.subject')]), 200);
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }

    public function copy(CopySemesterRequest $request, ClassModel $class)
    {
        try {
            resolve(SemesterService::class)->copy($request, $class);

            return response()->success(null, __('common.success.copy', ['name' => __('common.attribute.semester')]), 200);
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }
}
