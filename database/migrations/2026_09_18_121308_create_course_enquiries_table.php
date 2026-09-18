<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_enquiries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id')->nullable();
            $table->unsignedBigInteger('api_course_id')->nullable();
            $table->string('course_title')->nullable();
            $table->string('name');
            $table->string('designation')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('school')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->text('message')->nullable();
            $table->string('source')->nullable();
            $table->enum('status', ['new', 'read', 'replied'])->default('new');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_enquiries');
    }
};
