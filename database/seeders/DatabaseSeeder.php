<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\ClassStudent;
use App\Models\ClassSubjectSemester;
use App\Models\ParentStudent;
use App\Models\Subject;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // UserSeeder::class,
            // AcademicYearSeeder::class,
            // ClassModelSeeder::class,
            // SubjectSeeder::class,
            ScoreSeeder::class,
            FeedbackSeeder::class,
            NotificationSeeder::class,   
        ]);
    }
}
