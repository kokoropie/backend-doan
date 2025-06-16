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
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users');
            $table->foreignId('parent_id')->constrained('users');
            $table->foreignId('score_id')->nullable()->constrained('scores');
            $table->foreignId('teacher_id')->constrained('users');
            $table->text('message');
            $table->enum('status', values_of_enum(\App\Enums\FeedbackStatus::class))->default(\App\Enums\FeedbackStatus::PENDING);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
