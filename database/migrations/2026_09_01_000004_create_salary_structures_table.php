<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->morphs('payable'); // Teacher or Staff
            $table->decimal('basic_salary', 12, 2)->default(0);
            $table->decimal('house_allowance', 12, 2)->default(0);
            $table->decimal('transport_allowance', 12, 2)->default(0);
            $table->decimal('other_allowance', 12, 2)->default(0);
            $table->decimal('deduction', 12, 2)->default(0);
            $table->date('effective_from')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->unique(['payable_type', 'payable_id', 'effective_from']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_structures');
    }
};
