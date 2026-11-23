<?php

namespace App\Services;

use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClubShopOrderSyncService
{
    /**
     * Sync all orders from Club Shop database into pro.local's shop_orders table.
     */
    public static function syncAll(): int
    {
        $syncedCount = 0;
        try {
            $clubShop = DB::connection('clubshop');
            $orders = $clubShop->table('orders')->get();

            foreach ($orders as $order) {
                if (self::syncOrderRow($order)) {
                    $syncedCount++;
                }
            }
        } catch (\Throwable $e) {
            Log::error('[ClubShopOrderSyncService] syncAll failed: ' . $e->getMessage());
        }

        return $syncedCount;
    }

    /**
     * Sync a single order by its Club Shop order ID.
     */
    public static function syncOrderId(int $orderId): bool
    {
        try {
            $clubShop = DB::connection('clubshop');
            $order = $clubShop->table('orders')->where('id', $orderId)->first();
            if (!$order) {
                return false;
            }

            return self::syncOrderRow($order);
        } catch (\Throwable $e) {
            Log::error("[ClubShopOrderSyncService] syncOrderId({$orderId}) failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Sync a single Club Shop order row into Laravel shop_orders.
     */
    public static function syncOrderRow(object $order): bool
    {
        try {
            $orderNumber = 'SKILLSHOP-' . $order->order_number;

            // Extract shipping details
            $shipping = @unserialize($order->shipping);
            if (!is_object($shipping)) {
                $shipping = (object)[];
            }

            $firstName = $shipping->sFirstName ?? ($shipping->bFirstName ?? '');
            $lastName  = $shipping->sLastName ?? ($shipping->bLastName ?? '');
            $fullName  = trim("{$firstName} {$lastName}");

            $email = $shipping->sEmail ?? ($shipping->bEmail ?? null);
            $phone = $shipping->sPhoneNumber ?? ($shipping->bPhoneNumber ?? null);

            // If shipping is missing phone/email, look up buyer in Club Shop / LMS users
            $userId = null;
            if ($order->buyer_id) {
                $shopUser = DB::connection('clubshop')->table('users')->where('id', $order->buyer_id)->first();
                if ($shopUser) {
                    if (empty($fullName)) {
                        $fullName = trim(($shopUser->first_name ?? '') . ' ' . ($shopUser->last_name ?? ''));
                    }
                    if (empty($email)) {
                        $email = $shopUser->email ?? null;
                    }
                    if (empty($phone)) {
                        $phone = $shopUser->phone_number ?? null;
                    }
                    // Find matching user in Laravel
                    if (!empty($shopUser->email)) {
                        $userId = User::where('email', $shopUser->email)->value('id');
                    }
                }
            }

            $fullName = !empty($fullName) ? $fullName : 'School Representative';
            $phone    = !empty($phone) ? $phone : '9999999999';
            $email    = !empty($email) ? $email : 'procurement@school.local';

            $address     = $shipping->sAddress ?? ($shipping->bAddress ?? 'Address pending confirmation');
            $city        = $shipping->sCity ?? ($shipping->bCity ?? 'Bengaluru');
            $state       = $shipping->sState ?? ($shipping->bState ?? 'Karnataka');
            $pincode     = $shipping->sZipCode ?? ($shipping->bZipCode ?? '560001');
            $country     = $shipping->sCountry ?? ($shipping->bCountry ?? 'India');
            $addressType = $shipping->sTitle ?? ($shipping->bTitle ?? 'School / Institution');

            // Map payment status
            $paymentStatus = match ($order->payment_status) {
                'payment_received', 'completed', 'paid' => 'paid',
                'pending_payment', 'pending'            => 'pending',
                'payment_failed', 'failed'              => 'failed',
                'refunded'                              => 'refunded',
                default                                 => 'paid',
            };

            // Map order status
            $orderStatus = match ((string)$order->status) {
                '1', 'completed', 'delivered'           => 'delivered',
                '2', 'cancelled'                        => 'cancelled',
                'processing', 'order_processing'        => 'processing',
                'shipped'                               => 'shipped',
                default                                 => 'pending',
            };

            $subtotal    = (float)($order->price_subtotal ?? 0);
            $shippingCost = (float)($order->price_shipping ?? 0);
            $discount    = (float)($order->coupon_discount ?? 0);
            $totalAmount = (float)($order->price_total ?? ($subtotal + $shippingCost - $discount));

            // Find or create in shop_orders
            $shopOrder = ShopOrder::updateOrCreate(
                ['order_number' => $orderNumber],
                [
                    'user_id'         => $userId,
                    'customer_name'   => $fullName,
                    'customer_email'  => $email,
                    'customer_phone'  => $phone,
                    'pincode'         => $pincode,
                    'city'            => $city,
                    'state'           => $state,
                    'country'         => $country,
                    'address'         => $address,
                    'address_type'    => $addressType,
                    'payment_method'  => $order->payment_method ?? 'razorpay',
                    'payment_id'      => $order->checkout_token ?? $order->bank_transaction_number ?? null,
                    'payment_status'  => $paymentStatus,
                    'order_status'    => $orderStatus,
                    'subtotal'        => $subtotal,
                    'shipping_cost'   => $shippingCost,
                    'discount'        => $discount,
                    'total_amount'    => $totalAmount,
                    'currency'        => $order->price_currency ?? 'INR',
                    'created_at'      => $order->created_at ?? now(),
                    'updated_at'      => $order->updated_at ?? now(),
                ]
            );

            // Fetch order items from Club Shop
            $items = DB::connection('clubshop')->table('order_items')->where('order_id', $order->id)->get();

            // Clear existing synced items for this order before re-inserting
            ShopOrderItem::where('shop_order_id', $shopOrder->id)->delete();

            foreach ($items as $item) {
                $imageData = null;
                if (!empty($item->image_data)) {
                    $img = json_decode($item->image_data, true);
                    $imageData = $img['path'] ?? null;
                }

                ShopOrderItem::create([
                    'shop_order_id' => $shopOrder->id,
                    'product_id'    => $item->product_id ?? null,
                    'product_title' => $item->product_title ?? 'Lab Component Item',
                    'product_image' => $imageData,
                    'unit_price'    => (float)($item->product_unit_price ?? 0),
                    'quantity'      => (int)($item->product_quantity ?? 1),
                    'total_price'   => (float)($item->product_total_price ?? ($item->product_unit_price * ($item->product_quantity ?? 1))),
                    'created_at'    => $item->created_at ?? now(),
                    'updated_at'    => $item->updated_at ?? now(),
                ]);
            }

            return true;
        } catch (\Throwable $e) {
            Log::error("[ClubShopOrderSyncService] Failed to sync order {$order->id}: " . $e->getMessage());
            return false;
        }
    }
}

