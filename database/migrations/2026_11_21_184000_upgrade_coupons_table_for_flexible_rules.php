<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            if (!Schema::hasColumn('coupons', 'discount_type')) {
                $table->string('discount_type', 20)->default('percentage')->after('coupon_code');
            }
            if (!Schema::hasColumn('coupons', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->nullable()->after('offer_percentage');
            }
            if (!Schema::hasColumn('coupons', 'max_discount_amount')) {
                $table->decimal('max_discount_amount', 10, 2)->nullable()->after('discount_amount');
            }
            if (!Schema::hasColumn('coupons', 'course_ids')) {
                $table->text('course_ids')->nullable()->after('min_price');
            }
            if (!Schema::hasColumn('coupons', 'category_ids')) {
                $table->text('category_ids')->nullable()->after('course_ids');
            }
            if (!Schema::hasColumn('coupons', 'start_date')) {
                $table->date('start_date')->nullable()->after('category_ids');
            }
            if (!Schema::hasColumn('coupons', 'usage_limit_total')) {
                $table->integer('usage_limit_total')->nullable()->after('expired_date');
            }
            if (!Schema::hasColumn('coupons', 'usage_limit_per_user')) {
                $table->integer('usage_limit_per_user')->default(1)->nullable()->after('usage_limit_total');
            }
            if (!Schema::hasColumn('coupons', 'times_used')) {
                $table->integer('times_used')->default(0)->after('usage_limit_per_user');
            }
        });

        // Make min_price and expired_date nullable if not already
        try {
            DB::statement("ALTER TABLE `coupons` MODIFY `min_price` DECIMAL(10,2) NULL DEFAULT 0.00");
            DB::statement("ALTER TABLE `coupons` MODIFY `expired_date` DATE NULL");
            DB::statement("ALTER TABLE `coupons` MODIFY `offer_percentage` DECIMAL(10,2) NULL DEFAULT 0.00");
        } catch (\Throwable $e) {
            // Ignore if DB dialect differences
        }

        // Revive and update existing test coupons so they are valid and can be applied immediately
        DB::table('coupons')->where('coupon_code', 'PRO001')->update([
            'status' => 'active',
            'discount_type' => 'percentage',
            'offer_percentage' => 99.00,
            'min_price' => 1.00,
            'expired_date' => '2030-12-31',
            'updated_at' => now(),
        ]);

        DB::table('coupons')->where('coupon_code', 'ANY2')->update([
            'status' => 'active',
            'discount_type' => 'percentage',
            'offer_percentage' => 50.00,
            'min_price' => 5000.00,
            'expired_date' => '2030-12-31',
            'updated_at' => now(),
        ]);

        DB::table('coupons')->where('coupon_code', '9998')->update([
            'status' => 'active',
            'discount_type' => 'percentage',
            'offer_percentage' => 10.00,
            'min_price' => 5000.00,
            'expired_date' => '2030-12-31',
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $cols = [
                'discount_type',
                'discount_amount',
                'max_discount_amount',
                'course_ids',
                'category_ids',
                'start_date',
                'usage_limit_total',
                'usage_limit_per_user',
                'times_used',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('coupons', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
