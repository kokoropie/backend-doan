<?php

namespace Database\Seeders;

use App\Enums\ScoreType;
use App\Models\AcademicYear;
use App\Models\ClassStudent;
use App\Models\ClassSubjectSemester;
use App\Models\Score;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = ScoreType::cases();

        $years = AcademicYear::get();
        foreach ($years as $year) {
            $semesters = $year->semesters()->pluck('id')->toArray();
            $css = ClassSubjectSemester::whereIn('semester_id', $semesters)->get();
            $students = ClassStudent::whereIn('class_id', $css->pluck('class_id'))->pluck('student_id')->toArray();
    
            foreach ($css as $cs) {
                foreach ($students as $studentId) {
                    foreach ($types as $type) {
                        Score::create([
                            'class_subject_semester_id' => $cs->id,
                            'student_id' => $studentId,
                            'score' => rand(5, 10),
                            'type' => $type
                        ]);
                    }
                }
            }
        }
    }
}
