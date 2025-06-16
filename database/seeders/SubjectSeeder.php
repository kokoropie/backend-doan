<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\ClassSubjectSemester;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fileTeachers = database_path('seeders/teachers.json');
        if (file_exists($fileTeachers)) {
            $teachers = json_decode(file_get_contents($fileTeachers), true);

            $yearId = AcademicYear::latest()->first()->id;
            $classes = ClassModel::where('academic_year_id', $yearId)->with('academicYear.semesters')->get();
            $users = User::hasTeacher()->get();

            foreach ($teachers as $teacher) {
                $user = $users->where('first_name', $teacher['first_name'])
                    ->where('last_name', $teacher['last_name'])
                    ->first();
                if ($user) {
                    $subjects = collect($teacher['subjects'] ?? [])->map(function ($subject) {
                        return Subject::firstOrCreate(['name' => $subject]);
                    });
                    foreach ($classes as $class) {
                        foreach ($class->academicYear->semesters as $semester) {
                            foreach ($subjects as $subject) {
                                $classSubjectSemester = new ClassSubjectSemester();
                                $classSubjectSemester->class_id = $class->id;
                                $classSubjectSemester->subject_id = $subject->id;
                                $classSubjectSemester->semester_id = $semester->id;
                                $classSubjectSemester->teacher_id = $user->id;
                                $classSubjectSemester->save();
                            }
                        }
                    }
                }
            }
        }
    }
}
