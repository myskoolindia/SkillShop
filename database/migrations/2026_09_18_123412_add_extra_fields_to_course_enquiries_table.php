<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_enquiries', function (Blueprint $table) {
            // Fresh installations already receive these columns in the create-table migration.
            if (!Schema::hasColumn('course_enquiries', 'designation')) {
                $table->string('designation')->nullable()->after('name');
            }
            if (!Schema::hasColumn('course_enquiries', 'city')) {
                $table->string('city')->nullable()->after('school');
            }
            if (!Schema::hasColumn('course_enquiries', 'address')) {
                $table->text('address')->nullable()->after('city');
            }
            if (!Schema::hasColumn('course_enquiries', 'source')) {
                $table->string('source')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('course_enquiries', function (Blueprint $table) {
            $table->dropColumn(['designation', 'city', 'address', 'source']);
        });
    }
};
