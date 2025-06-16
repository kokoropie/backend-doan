<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AcademicYear\DestroyAcademicYearRequest;
use App\Http\Requests\AcademicYear\StoreAcademicYearRequest;
use App\Http\Requests\AcademicYear\UpdateAcademicYearRequest;
use App\Http\Requests\ListResourceRequest;
use App\Http\Resources\AcademicYearResource;
use App\Models\AcademicYear;
use App\Services\AcademicYearService;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    protected AcademicYearService $academicYearService;
    
    public function __construct(AcademicYearService $academicYearService)
    {
        $this->academicYearService = $academicYearService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ListResourceRequest $request)
    {
        $academicYears = $this->academicYearService->getList($request);

        return response()->success(AcademicYearResource::collection($academicYears));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAcademicYearRequest $request)
    {
        try {
            $academicYear = $this->academicYearService->save($request);

            return response()->success(new AcademicYearResource($academicYear), __('common.success.store', ['name' => __('common.attribute.academic_year')]), 201);
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AcademicYear $academicYear)
    {
        return response()->success(new AcademicYearResource($academicYear));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAcademicYearRequest $request, AcademicYear $academicYear)
    {
        try {
            $academicYear = $this->academicYearService->update($request, $academicYear);

            return response()->success(new AcademicYearResource($academicYear), __('common.success.update', ['name' => __('common.attribute.academic_year')]));
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyAcademicYearRequest $request, int $academicYear)
    {
        try {
            $this->academicYearService->deleteById($academicYear);

            return response()->success(null, __('common.success.delete', ['name' => __('common.attribute.academic_year')]));
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }

    public function setYear(Request $request, AcademicYear $academicYear)
    {
        try {
            $current = $this->academicYearService->getModel()::query()->where('current', true)->first();

            if ($current) {
                $this->academicYearService->changeYear($current, $academicYear, !$request->boolean('current_teachers', true));
            }

            return response()->success(null, __('common.success.update', ['name' => __('common.attribute.academic_year')]));
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }
}
