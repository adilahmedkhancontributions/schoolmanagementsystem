<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('nationality')->nullable()->after('blood_group');
            $table->string('religion')->nullable()->after('nationality');
            $table->string('emergency_contact_name')->nullable()->after('religion');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_relation')->nullable()->after('emergency_contact_phone');
            $table->string('medical_notes')->nullable()->after('emergency_contact_relation');
            $table->text('notes')->nullable()->after('medical_notes');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'nationality',
                'religion',
                'emergency_contact_name',
                'emergency_contact_phone',
                'emergency_contact_relation',
                'medical_notes',
                'notes',
            ]);
        });
    }
};
