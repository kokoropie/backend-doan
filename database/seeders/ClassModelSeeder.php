<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\ClassStudent;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class ClassModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $yearId = AcademicYear::latest()->first()->id;
        $dan = User::where('email', 'dan.tran@actvn.edu.vn')->first();
        $classAT = new ClassModel();
        $classAT->name = 'AT18N';
        $classAT->academic_year_id = $yearId;
        $classAT->teacher_id = $dan->id;
        $classAT->save();

        $classCT = new ClassModel();
        $classCT->name = 'CT06N';
        $classCT->academic_year_id = $yearId;
        $classCT->teacher_id = $dan->id;
        $classCT->save();

        $classes = ClassModel::where('academic_year_id', $yearId)->get();
        $students = User::hasStudent()->pluck('email', 'id')->toArray();
        foreach ($classes as $class) {
            foreach ($students as $studentId => $email) {
                $className = \Str::lower($class->name);
                if (\Str::contains($email, $className)) {
                    ClassStudent::create([
                        'class_id' => $class->id,
                        'student_id' => $studentId,
                    ]);
                }
            }
        }
    }
}
