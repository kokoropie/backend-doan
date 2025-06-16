<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListResourceRequest;
use App\Http\Requests\Semester\DestroySemesterRequest;
use App\Http\Requests\Semester\StoreSemesterRequest;
use App\Http\Requests\Semester\UpdateSemesterRequest;
use App\Http\Resources\SemesterResource;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Services\SemesterService;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    protected SemesterService $semesterService;

    public function __construct(SemesterService $semesterService)
    {
        $this->semesterService = $semesterService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ListResourceRequest $request, AcademicYear $academicYear)
    {
        $semesters = $this->semesterService->getList($request, $academicYear);

        return response()->success(SemesterResource::collection($semesters));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AcademicYear $academicYear, StoreSemesterRequest $request)
    {
        try {
            $semester = $this->semesterService->save($request);

            return response()->success(new SemesterResource($semester), __('common.success.store', ['name' => __('common.attribute.semester')]), 201);
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AcademicYear $academicYear, Semester $semester)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSemesterRequest $request, AcademicYear $academicYear, Semester $semester)
    {
        try {
            $semester = $this->semesterService->update($request, $semester);

            return response()->success(new SemesterResource($semester), __('common.success.update', ['name' => __('common.attribute.semester')]));
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroySemesterRequest $request, AcademicYear $academicYear, int $semester)
    {
        try {
            $this->semesterService->deleteById($semester);

            return response()->success(null, __('common.success.delete', ['name' => __('common.attribute.class')]));
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }
}
