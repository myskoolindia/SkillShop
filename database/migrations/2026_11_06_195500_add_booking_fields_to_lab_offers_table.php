<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lab_offers', function (Blueprint $table) {
            if (!Schema::hasColumn('lab_offers', 'booking_enabled')) {
                $table->boolean('booking_enabled')->default(true)->after('is_active');
            }
            if (!Schema::hasColumn('lab_offers', 'booking_token_amount')) {
                $table->decimal('booking_token_amount', 12, 2)->default(25000.00)->after('booking_enabled');
            }
            if (!Schema::hasColumn('lab_offers', 'booking_extra_discount_percent')) {
                $table->decimal('booking_extra_discount_percent', 5, 2)->default(10.00)->after('booking_token_amount');
            }
            if (!Schema::hasColumn('lab_offers', 'booking_title')) {
                $table->string('booking_title')->nullable()->default('Special Booking Offer')->after('booking_extra_discount_percent');
            }
            if (!Schema::hasColumn('lab_offers', 'booking_subtitle')) {
                $table->text('booking_subtitle')->nullable()->after('booking_title');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lab_offers', function (Blueprint $table) {
            $table->dropColumn([
                'booking_enabled',
                'booking_token_amount',
                'booking_extra_discount_percent',
                'booking_title',
                'booking_subtitle',
            ]);
        });
    }
};

