<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('lab_designs')) {
            return;
        }

        Schema::create('lab_designs', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->nullable()->index();
            $table->string('reference')->nullable()->index();
            $table->string('source')->nullable();          // which lab page started it
            $table->string('status', 30)->default('pending'); // pending | completed | failed
            $table->string('session_url')->nullable();      // link returned by the API (if any)
            $table->longText('result')->nullable();         // full API result JSON
            // Optional enquirer details (if tied to an enquiry/user)
            $table->string('name')->nullable();
            $table->string('school')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_designs');
    }
};
