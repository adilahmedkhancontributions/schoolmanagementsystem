<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('applicant_name');
            $table->string('father_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('address')->nullable();
            $table->foreignId('school_class_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source')->nullable();
            $table->enum('status', [
                'inquiry',
                'interview_scheduled',
                'test_scheduled',
                'offered',
                'enrolled',
                'rejected',
                'withdrawn',
            ])->default('inquiry');
            $table->dateTime('interview_date')->nullable();
            $table->text('interview_notes')->nullable();
            $table->dateTime('test_date')->nullable();
            $table->decimal('test_score', 5, 2)->nullable();
            $table->text('test_notes')->nullable();
            $table->text('decision_notes')->nullable();
            $table->foreignId('enrolled_student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admissions');
    }
};
