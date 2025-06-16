<?php
namespace App\Services;

use App\Enums\UserRole;
use App\Http\Requests\ListResourceRequest;
use App\Models\ClassStudent;
use App\Models\User;

class UserService extends BaseService
{
    public function __construct()
    {
        parent::__construct();

        $this->setModel(User::class);
    }

    public function getList(ListResourceRequest $request)
    {
        $query = $this->getModel()::query();
        return $this->paginate($query->when($request->has('roles'), function ($query) use ($request) {
                $query->hasRoles($request->roles);
                if (collect($request->get('roles'))->contains(UserRole::STUDENT->value)) {
                    if ($request->has('class_id')) {
                        $query->inClass($request->get('class_id'));
                    }
                    if ($request->has('parent_id')) {
                        $query->childrenOf($request->get('parent_id'));
                    }
                }
            }), $request);
    }

    public function save($request)
    {
        if ($request->has('random_password') && $request->boolean('random_password')) {
            $request->merge(['password' => \Str::random(10)]);
        }

        /** @var User */
        $user = parent::save($request->except('random_password'));
        $user->plain_password = $request->password;

        if ($request->has('relationship') && $request->has('relationship_data')) {
            if ($user->isStudent()) {
                $relationships = array_intersect($request->relationship, User::STUDENT_RELATIONSHIPS);
                foreach ($relationships as $index => $relationship) {
                    switch ($relationship) {
                        case 'parents':
                            $user->parentsToSync()->sync($request->relationship_data[$index]);
                            break;
                        case 'classes':
                            $user->classesToSync()->sync($request->relationship_data[$index]);
                            break;
                    }
                }
            }
        }

        return $user;
    }

    public function changePassword($request, User $user)
    {
        if ($request->has('random_password') && $request->boolean('random_password')) {
            $request->merge(['password' => \Str::random(10)]);
        }

        $user = $this->update($request, $user);
        $user->plain_password = $request->password;

        return $user;
    }

    public function changeClass($request, User $user)
    {
        if ($user->isStudent()) {
            /** @var \App\Models\ClassModel $newClass */
            $newClass = resolve(ClassService::class)->getModel()::find($request->class);
            /** @var \App\Models\ClassModel $currentClass */
            $currentClass = $user->currentClass;

            if ($currentClass && $currentClass->id !== $newClass->id) {
                $scores = $user->scores()
                    ->whereHas('class', fn ($q) 
                        => $q->where('classes.id', $currentClass->id)
                    )->get();
                $curentCsses = $currentClass->subjectsWithMore()->get()->keyBy('id');
                $csses = $newClass->subjectsWithMore()->get();
                foreach ($scores as $score) {
                    $css = $curentCsses->get($score->class_subject_semester_id);
                    if ($css) {
                        $newCss = $csses->where(
                            fn ($item) => $item->subject_id == $css->subject_id && $item->semester_id == $css->semester_id
                        )->first();
                        if ($newCss) {
                            $score->class_subject_semester_id = $newCss->id;
                            $score->save();
                        }
                    }
                }

                ClassStudent::where('student_id', $user->id)
                    ->where('class_id', $currentClass->id)
                    ->delete();
            }

            ClassStudent::updateOrCreate(
                ['student_id' => $user->id, 'class_id' => $newClass->id],
                []
            );
        }

        return $user;
    }
}