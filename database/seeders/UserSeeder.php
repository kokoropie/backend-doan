<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\ParentStudent;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // seed admin
        $admin = new User();
        $admin->full_name = 'Admin';
        $admin->email = 'admin@admin.com';
        $admin->password = bcrypt('password');
        $admin->role = UserRole::ADMIN;
        $admin->save();

        $fileTeachers = database_path('seeders/teachers.json');
        if (file_exists($fileTeachers)) {
            $teachers = json_decode(file_get_contents($fileTeachers), true);
            foreach ($teachers as $teacher) {
                $user = new User();
                $user->first_name = $teacher['first_name'];
                $user->last_name = $teacher['last_name'];
                $user->email = \Str::slug($teacher['first_name']) . '.' . \Str::slug($teacher['last_name'], '') . '@actvn.edu.vn';
                $user->password = bcrypt('password');
                $user->role = UserRole::TEACHER;
                $user->save();
            }
        }

        $fileStudents = database_path('seeders/students.json');
        if (file_exists($fileStudents)) {
            $students = json_decode(file_get_contents($fileStudents), true);
            foreach ($students as $student) {
                $user = new User();
                $user->first_name = $student['first_name'];
                $user->last_name = $student['last_name'];
                $user->email = \Str::lower($student['email']);
                $user->password = bcrypt('password');
                $user->role = UserRole::STUDENT;
                $user->save();

                $parent = new User();
                $parent->first_name = fake('vi_VN')->firstName();
                $parent->last_name = fake('vi_VN')->lastName() . " " . fake('vi_VN')->lastName();
                $parent->email = \Str::slug($parent->first_name) . '.' . \Str::slug($parent->last_name, '') . '@parent.actvn.edu.vn';
                $parent->password = bcrypt('password');
                $parent->role = UserRole::PARENT;
                $parent->save();

                $parentStudent = new ParentStudent();
                $parentStudent->parent_id = $parent->id;
                $parentStudent->student_id = $user->id;
                $parentStudent->save();
            }
        }
    }
}
