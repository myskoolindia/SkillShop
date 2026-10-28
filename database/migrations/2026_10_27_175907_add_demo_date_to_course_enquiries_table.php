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
        if (Schema::hasColumn('course_enquiries', 'demo_date')) {
            return;
        }

        Schema::table('course_enquiries', function (Blueprint $table) {
            // Preferred demo date & time, set only when the enquiry is a
            // "Book a Skill Lab Visit" or "Schedule a Demo with Academic Team" request.
            $table->dateTime('demo_date')->nullable()->after('message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_enquiries', function (Blueprint $table) {
            $table->dropColumn('demo_date');
        });
    }
};
