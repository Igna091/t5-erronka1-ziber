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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('enrollments')->cascadeOnDelete();
            $table->foreignId('course_subject_id')->constrained('course_subject')->cascadeOnDelete();
            $table->unsignedTinyInteger('evaluation'); // 1, 2, 3, 4 = final
            $table->decimal('grade', 4, 2)->nullable(); // 0.00 - 10.00
            $table->timestamps();

            $table->unique(['enrollment_id', 'course_subject_id', 'evaluation']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
