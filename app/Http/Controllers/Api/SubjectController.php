<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListResourceRequest;
use App\Http\Requests\Subject\DestroySubjectRequest;
use App\Http\Requests\Subject\StoreSubjectRequest;
use App\Http\Requests\Subject\UpdateSubjectRequest;
use App\Http\Resources\SubjectResource;
use App\Http\Resources\SubjectWithMoreResource;
use App\Models\Subject;
use App\Services\SubjectService;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    protected SubjectService $subjectService;
    public function __construct(SubjectService $subjectService)
    {
        $this->subjectService = $subjectService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ListResourceRequest $request)
    {
        $subjects = $this->subjectService->getList($request);

        return response()->success(SubjectResource::collection($subjects));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubjectRequest $request)
    {
        try {
            $subject = $this->subjectService->save($request);

            return response()->success(new SubjectResource($subject), __('common.success.store', ['name' => __('common.attribute.subject')]), 201);
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubjectRequest $request, Subject $subject)
    {
        try {
            $subject = $this->subjectService->update($request, $subject);

            return response()->success(new SubjectResource($subject), __('common.success.update', ['name' => __('common.attribute.subject')]));
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroySubjectRequest $request, int $subject)
    {
        try {
            $this->subjectService->deleteById($subject);

            return response()->success([], __('common.success.delete', ['name' => __('common.attribute.subject')]));
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }
}
