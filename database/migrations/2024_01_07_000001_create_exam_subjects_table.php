<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->decimal('max_marks', 6, 2)->default(100);
            $table->decimal('pass_marks', 6, 2)->default(40);
            $table->date('exam_date')->nullable();
            $table->timestamps();

            $table->unique(['exam_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_subjects');
    }
};
