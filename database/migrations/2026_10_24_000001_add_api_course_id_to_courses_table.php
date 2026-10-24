<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Both course sidebars and the CourseApi controller use this link field.
        if (!Schema::hasColumn('courses', 'api_course_id')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->unsignedBigInteger('api_course_id')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('courses', 'api_course_id')) {
            Schema::table('courses', fn (Blueprint $table) => $table->dropColumn('api_course_id'));
        }
    }
};
