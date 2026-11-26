<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Storefront orders count as paid (POS, content unlocks) only once Razorpay confirms them (App\Services\ShopPaymentVerifier).
return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('shop_orders') && !Schema::hasColumn('shop_orders', 'payment_verified_at')) {
            Schema::table('shop_orders', fn (Blueprint $table) => $table->timestamp('payment_verified_at')->nullable()->after('payment_status'));
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('shop_orders', 'payment_verified_at')) Schema::table('shop_orders', fn (Blueprint $table) => $table->dropColumn('payment_verified_at'));
    }
};
