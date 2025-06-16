<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Traits\UseTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes, UseTimestamps;

    protected $table = 'users';
    
    protected $fillable = [
        'last_name',
        'first_name',
        'email',
        'password',
        'role'
    ];

    const STUDENT_RELATIONSHIPS = ['parents', 'classes'];
    
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function setFullNameAttribute($value)
    {
        $parts = explode(' ', trim($value));
        $this->attributes['first_name'] = array_pop($parts);
        $this->attributes['last_name'] = implode(' ', $parts);
    }

    public function getFullNameAttribute()
    {
        return trim($this->last_name . ' ' . $this->first_name);
    }

    public function setPasswordAttribute($value)
    {
        if ($value) {
            if (!password_get_info($value)['algo']) {
                $this->attributes['password'] = \Hash::make($value);
            } else {
                $this->attributes['password'] = $value;
            }
        }
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function children()
    {
        return $this->hasManyThrough(
            User::class,
            ParentStudent::class,
            'parent_id',
            'id',
            'id',
            'student_id'
        );
    }

    public function parents()
    {
        return $this->hasManyThrough(
            User::class,
            ParentStudent::class,
            'student_id',
            'id',
            'id',
            'parent_id'
        );
    }

    public function parentsToSync()
    {
        return $this->hasMany(ParentStudent::class, 'student_id');
    }

    public function classesToSync()
    {
        return $this->hasMany(ClassStudent::class, 'student_id');
    }

    public function classes()
    {
        if ($this->isStudent()) {
            return $this->belongsToMany(
                ClassModel::class, 
                'class_student',
                'student_id',
                'class_id'
            );
        } else if ($this->isTeacher()) {
            return $this->hasMany(ClassModel::class, 'teacher_id');
        } else if ($this->isParent()) {
            return ClassModel::query()
                ->join('class_student', 'class_student.class_id', '=', 'classes.id')
                ->join('users as student', 'student.id', '=', 'class_student.student_id')
                ->join('parent_student', 'student.id', '=', 'parent_student.student_id')
                ->select('classes.*')
                ->where('parent_student.parent_id', $this->id);
        } else {
            return ClassModel::query();
        }
    }

    public function subjects()
    {
        if ($this->isTeacher()) {
            return Subject::query()
                ->whereHas('teachers', function ($query) {
                    $query->where('users.id', $this->id);
                });
        } else if ($this->isStudent()) {
            return Subject::query()
                ->join('class_subject_semester', 'class_subject_semester.subject_id', '=', 'subjects.id')
                ->join('class_student', 'class_student.class_id', '=', 'class_subject_semester.class_id')
                ->where('class_student.student_id', $this->id);
        } else if ($this->isAdmin()) {
            return Subject::query();
        } 
    }

    public function sentNotifications()
    {
        return $this->hasMany(Notification::class, 'sender_id');
    }

    public function receivedNotifications()
    {
        return $this->belongsToMany(
            Notification::class,
            'user_notification',
            'user_id',
            'notification_id'
        );
    }

    public function scores()
    {
        return $this->hasMany(Score::class, 'student_id');
    }

    public function sentFeedback()
    {
        return $this->hasMany(Feedback::class, 'parent_id');
    }

    public function receivedFeedback()
    {
        return $this->hasMany(Feedback::class, 'teacher_id');
    }

    public function currentClass()
    {
        return $this->hasOneThrough(
            ClassModel::class, 
            ClassStudent::class,
            'student_id',
            'id',
            'id',
            'class_id'
        )->join('academic_years', function ($join) {
            $join->on('academic_years.id', '=', 'classes.academic_year_id')
                ->where('academic_years.current', true);
        })->select('classes.*')->withCount('students');
    }

    public function classYear()
    {
        return $this->hasOneThrough(
            ClassModel::class, 
            ClassStudent::class,
            'student_id',
            'id',
            'id',
            'class_id'
        )->where('classes.academic_year_id', request()->year)->select('classes.*')->withCount('students');
    }
    
    public function isRole($role)
    {
        if (is_string($role)) {
            $role = UserRole::tryFrom($role);
        }
        return $this->role === $role;
    }

    public function isStudent()
    {
        return $this->isRole(UserRole::STUDENT);
    }
    public function isTeacher()
    {
        return $this->isRole(UserRole::TEACHER);
    }

    public function isParent()
    {
        return $this->isRole(UserRole::PARENT);
    }

    public function isAdmin()
    {
        return $this->isRole(UserRole::ADMIN);
    }

    public function scopeHasRoles($query, $roles)
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }
        return $query->whereIn('role', $roles);
    }

    public function scopeHasTeacher($query)
    {
        return $query->hasRoles(UserRole::TEACHER->value);
    }

    public function scopeHasStudent($query)
    {
        return $query->hasRoles(UserRole::STUDENT->value);
    }

    public function scopeHasParent($query)
    {
        return $query->hasRoles(UserRole::PARENT->value);
    }

    public function scopeHasAdmin($query)
    {
        return $query->hasRoles(UserRole::ADMIN->value);
    }

    public function scopeInClass($query, $class)
    {
        return $query->whereHas('classesToSync', function ($query) use ($class) {
            $query->where('class_id', $class);
        });
    }

    public function scopeChildrenOf($query, $parent)
    {
        return $query->whereHas('parentsToSync', function ($query) use ($parent) {
            $query->where('parent_id', $parent);
        });
    }
}
