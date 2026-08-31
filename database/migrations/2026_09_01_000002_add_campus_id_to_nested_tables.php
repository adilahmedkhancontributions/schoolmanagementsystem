<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Wire the campus concept into every entity nested under a school so a
     * single school can operate across multiple campuses.
     */
    public function up(): void
    {
        Schema::table('school_classes', function (Blueprint $table) {
            $table->foreignId('campus_id')->nullable()->after('school_id')->constrained('campuses')->nullOnDelete();
        });

        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('campus_id')->nullable()->after('school_id')->constrained('campuses')->nullOnDelete();
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->foreignId('campus_id')->nullable()->after('school_id')->constrained('campuses')->nullOnDelete();
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->foreignId('campus_id')->nullable()->after('school_id')->constrained('campuses')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('school_classes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('campus_id');
        });
        Schema::table('students', function (Blueprint $table) {
            $table->dropConstrainedForeignId('campus_id');
        });
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('campus_id');
        });
        Schema::table('staff', function (Blueprint $table) {
            $table->dropConstrainedForeignId('campus_id');
        });
    }
};
