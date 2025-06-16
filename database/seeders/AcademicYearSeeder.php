<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $academicYear = new AcademicYear();
        $academicYear->from = Carbon::now()->subYears(4)->day(1)->month(9);
        $academicYear->to = $academicYear->from->copy()->day(31)->month(7)->addYear();
        $academicYear->current = false;
        $academicYear->save();

        $academicYear->semesters()->createMany([
            ['name' => 'Học kỳ 1'],
            ['name' => 'Học kỳ 2'],
        ]);
    }
}
