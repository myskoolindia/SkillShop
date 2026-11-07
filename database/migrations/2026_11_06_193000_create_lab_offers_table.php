<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('lab_offers')) {
            Schema::create('lab_offers', function (Blueprint $table) {
                $table->id();
                $table->string('title')->default('Special Skill Lab Festival Offer');
                $table->string('slug')->unique()->default('lab-offer');
                $table->string('badge_text')->nullable()->default('LIMITED TIME SCHOOL PROMOTION — SAVE UP TO 35%');
                $table->text('subtitle')->nullable();
                $table->string('banner_image')->nullable();
                $table->boolean('is_active')->default(true);
                $table->dateTime('countdown_end')->nullable();
                $table->string('whatsapp_number', 25)->nullable();
                $table->string('support_email')->nullable();
                $table->string('support_phone', 25)->nullable();
                $table->json('highlights')->nullable();
                $table->text('terms')->nullable();
                $table->json('products')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_offers');
    }
};

