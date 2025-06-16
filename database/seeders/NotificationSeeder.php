<?php

namespace Database\Seeders;

use App\Enums\ReceiverType;
use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\Notification;
use App\Models\ParentStudent;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $years = AcademicYear::get();
        foreach ($years as $year) {
            $admins = User::hasAdmin()->get();
            $teachers = User::hasTeacher()->get();
            $users = User::whereNotIn('id', $admins->pluck('id')->toArray())->get();
            $yearId = $year->id;
            $classes = ClassModel::where('academic_year_id', $yearId)->get();
            Notification::factory()
                ->count(10)
                ->make()
                ->each(function ($notification) use ($admins, $teachers, $users, $classes) {
                    if (in_array($notification->receiver_type, [ReceiverType::ALL, ReceiverType::_CLASS, ReceiverType::TEACHER])) {
                        $notification->sender_id = $admins->random()->id;
                    } else {
                        $notification->sender_id = $teachers->random()->id;
                    }
                    $notification->save();
    
                    if ($notification->receiver_type == ReceiverType::ALL) {
                        foreach ($users as $user) {
                            UserNotification::create([
                                'notification_id' => $notification->id,
                                'user_id' => $user->id,
                                'is_read' => fake()->boolean(),
                            ]);
                        }
                        foreach ($teachers as $user) {
                            UserNotification::create([
                                'notification_id' => $notification->id,
                                'user_id' => $user->id,
                                'is_read' => fake()->boolean(),
                            ]);
                        }
                    } elseif ($notification->receiver_type == ReceiverType::_CLASS) {
                        $class = $classes->random();
                        $students = $class->classStudents()->pluck('student_id')->toArray();
                        UserNotification::create([
                            'notification_id' => $notification->id,
                            'user_id' => $class->teacher_id,
                            'is_read' => fake()->boolean(),
                        ]);
                        foreach ($students as $user) {
                            UserNotification::create([
                                'notification_id' => $notification->id,
                                'user_id' => $user,
                                'is_read' => fake()->boolean(),
                            ]);
                        }
                        $parents = ParentStudent::whereIn('student_id', $students)->pluck('parent_id')->toArray();
                        foreach ($parents as $parent) {
                            UserNotification::create([
                                'notification_id' => $notification->id,
                                'user_id' => $parent,
                                'is_read' => fake()->boolean(),
                            ]);
                        }
                    } elseif ($notification->receiver_type == ReceiverType::TEACHER) {
                        $randomTeacher = $teachers->random($teachers->count() / 2);
                        foreach ($randomTeacher as $teacher) {
                            UserNotification::create([
                                'notification_id' => $notification->id,
                                'user_id' => $teacher->id,
                                'is_read' => fake()->boolean(),
                            ]);
                        }
                    } elseif ($notification->receiver_type == ReceiverType::STUDENT) {
                        $students = User::hasStudent()->pluck('id')->random(5);
                        foreach ($students as $student) {
                            UserNotification::create([
                                'notification_id' => $notification->id,
                                'user_id' => $student,
                                'is_read' => fake()->boolean(),
                            ]);
                        }
                    } elseif ($notification->receiver_type == ReceiverType::PARENT) {
                        $parents = User::hasParent()->pluck('id')->random(5);
                        foreach ($parents as $parent) {
                            UserNotification::create([
                                'notification_id' => $notification->id,
                                'user_id' => $parent,
                                'is_read' => fake()->boolean(),
                            ]);
                        }
                    }
                });
        }
    }
}
