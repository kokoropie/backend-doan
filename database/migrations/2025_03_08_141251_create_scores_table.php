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
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('score', false, true);
            $table->enum('type', values_of_enum(\App\Enums\ScoreType::class));
            $table->foreignId('student_id')->constrained('users');
            $table->foreignId('class_subject_semester_id')->constrained('class_subject_semester');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};
