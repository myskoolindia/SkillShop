<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_enquiries', function (Blueprint $table) {
            $table->string('designation')->nullable()->after('name');
            $table->string('city')->nullable()->after('school');
            $table->text('address')->nullable()->after('city');
            $table->string('source')->nullable()->after('status'); // which page the enquiry came from
        });
    }

    public function down(): void
    {
        Schema::table('course_enquiries', function (Blueprint $table) {
            $table->dropColumn(['designation', 'city', 'address', 'source']);
        });
    }
};
