<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('class_subject_semester', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes');
            $table->foreignId('subject_id')->constrained('subjects');
            $table->foreignId('semester_id')->constrained('semesters');
            $table->foreignId('teacher_id')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_subject_semester');
    }
};
