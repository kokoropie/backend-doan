<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListResourceRequest;
use App\Http\Requests\User\ChangePasswordUserRequest;
use App\Http\Requests\User\DestroyUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserCreatedResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ListResourceRequest $request)
    {
        $academicYears = $this->userService->getList($request);

        return response()->success(UserResource::collection($academicYears));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $role = $request->input('role');
            $user = $this->userService->save($request);
            
            return response()->success(new UserCreatedResource($user), __('common.success.store', ['name' => __('common.attribute.' . $role)]), 201);
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $user = $this->userService->update($request, $user);
            
            return response()->success(new UserResource($user), __('common.success.update', ['name' => __('common.attribute.' . $user->getRawOriginal('role'))]), 201);
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyUserRequest $request, User $user)
    {
        try {
            $role = $user->getRawOriginal('role');
            $this->userService->delete($user);

            return response()->success([], __('common.success.delete', ['name' => __('common.attribute.' . $role)]));
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }

    public function changePassword(ChangePasswordUserRequest $request, User $user)
    {
        try {
            $user = $this->userService->changePassword($request, $user);

            return response()->success(new UserCreatedResource($user), __('user.messages.change_password_success'));
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }

    public function changeClass(Request $request, User $user)
    {
        try {
            $user = $this->userService->changeClass($request, $user);

            return response()->success(new UserCreatedResource($user), __('user.messages.change_class_success'));
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }
}
