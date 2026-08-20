<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timetable_change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('timetable_entry_id')->nullable()->constrained('timetable_entries')->nullOnDelete();

            $table->foreignId('current_section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->foreignId('current_subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->foreignId('current_timetable_slot_id')->nullable()->constrained('timetable_slots')->nullOnDelete();
            $table->unsignedTinyInteger('current_day_of_week')->nullable();

            $table->foreignId('requested_section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->foreignId('requested_subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->foreignId('requested_timetable_slot_id')->nullable()->constrained('timetable_slots')->nullOnDelete();
            $table->unsignedTinyInteger('requested_day_of_week')->nullable();

            $table->text('reason')->nullable();
            $table->string('status')->default('pending');
            $table->text('admin_note')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetable_change_requests');
    }
};
