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
        if (Schema::hasTable('course_enquiries')) {
            Schema::table('course_enquiries', function (Blueprint $table) {
                if (!Schema::hasColumn('course_enquiries', 'invoice')) {
                    $table->longText('invoice')->nullable()->after('proforma_invoice');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('course_enquiries')) {
            Schema::table('course_enquiries', function (Blueprint $table) {
                if (Schema::hasColumn('course_enquiries', 'invoice')) {
                    $table->dropColumn('invoice');
                }
            });
        }
    }
};
