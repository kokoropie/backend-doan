<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListResourceRequest;
use App\Http\Requests\ManageClass\DestroyClassRequest;
use App\Http\Requests\ManageClass\StoreClassRequest;
use App\Http\Requests\ManageClass\UpdateClassRequest;
use App\Http\Resources\ClassResource;
use App\Models\ClassModel;
use App\Services\ClassService;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    protected ClassService $classService;

    public function __construct(ClassService $classService)
    {
        $this->classService = $classService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(ListResourceRequest $request)
    {
        $classes = $this->classService->getList($request);

        return response()->success(ClassResource::collection($classes));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClassRequest $request)
    {
        try {
            $class = $this->classService->save($request);

            return response()->success(new ClassResource($class), __('common.success.store', ['name' => __('common.attribute.class')]), 201);
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ClassModel $class)
    {
        return response()->success(new ClassResource($this->classService->get($class, ['teacher'])));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClassRequest $request, ClassModel $class)
    {
        try {
            $class = $this->classService->update($request, $class);

            return response()->success(new ClassResource($class), __('common.success.update', ['name' => __('common.attribute.class')]));
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyClassRequest $request, int $class)
    {
        try {
            $this->classService->deleteById($class);

            return response()->success(null, __('common.success.delete', ['name' => __('common.attribute.class')]));
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }
}
