<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->string('hero_headline')->nullable()->after('secondary_color');
            $table->string('hero_subheadline')->nullable()->after('hero_headline');
            $table->string('hero_image')->nullable()->after('hero_subheadline');
        });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn(['hero_headline', 'hero_subheadline', 'hero_image']);
        });
    }
};
