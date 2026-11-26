<?php

namespace App\Console\Commands;

use App\Models\ShopOrder;
use App\Services\ShopPaymentVerifier;
use Illuminate\Console\Command;

/**
 * Checks storefront orders placed before payments were verified (or whose check could not run) with Razorpay.
 * A confirmed payment marks the order paid and verified, so its products unlock content. Orders that do not verify keep
 * their status; the reason is added to their admin notes. Safe to run again.
 */
class VerifyShopOrders extends Command
{
    protected $signature = 'shop-orders:verify {--days=90 : how far back to check}';
    protected $description = 'Verify storefront (product page) Razorpay payments that are not verified yet';

    public function handle(ShopPaymentVerifier $verifier): int
    {
        $orders = ShopOrder::whereNull('payment_verified_at')->where('payment_method', 'razorpay')->whereNotNull('payment_id')
            ->where('order_number', 'not like', 'SKILLSHOP-%')->where('order_status', '<>', 'cancelled')
            ->where('created_at', '>=', now()->subDays((int) $this->option('days')))->orderBy('id')->get();
        $ok = 0;
        foreach ($orders as $order) {
            $reason = $verifier->verify($order, false);
            if ($reason === null) $ok++;
            $this->line(sprintf('%-28s %s', $order->order_number, $reason === null ? 'verified' : 'not verified: ' . $reason));
        }
        $this->info("{$ok} of {$orders->count()} orders verified.");
        return self::SUCCESS;
    }
}
