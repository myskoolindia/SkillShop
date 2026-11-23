<?php

namespace App\Libraries;

class LaravelShopOrderSync
{
    /**
     * Safely sync a Club Shop order to Laravel shop_orders table.
     */
    public static function syncOrderSafely($orderId): void
    {
        try {
            $db = \Config\Database::connect();
            $order = $db->table('orders')->where('id', (int)$orderId)->get()->getRow();
            if (empty($order)) {
                return;
            }

            $laravel = \Config\Database::connect('laravel');

            $orderNumber = 'SKILLSHOP-' . $order->order_number;

            $shipping = @unserialize($order->shipping);
            if (!is_object($shipping)) {
                $shipping = (object)[];
            }

            $firstName = $shipping->sFirstName ?? ($shipping->bFirstName ?? '');
            $lastName  = $shipping->sLastName ?? ($shipping->bLastName ?? '');
            $fullName  = trim("{$firstName} {$lastName}");

            $email = $shipping->sEmail ?? ($shipping->bEmail ?? null);
            $phone = $shipping->sPhoneNumber ?? ($shipping->bPhoneNumber ?? null);

            $userId = null;
            if ($order->buyer_id) {
                $shopUser = $db->table('users')->where('id', $order->buyer_id)->get()->getRow();
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
                    if (!empty($shopUser->email)) {
                        $lmsUser = $laravel->table('users')->select('id')->where('email', $shopUser->email)->get()->getRow();
                        if ($lmsUser) {
                            $userId = $lmsUser->id;
                        }
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

            $paymentStatus = match ($order->payment_status) {
                'payment_received', 'completed', 'paid' => 'paid',
                'pending_payment', 'pending'            => 'pending',
                'payment_failed', 'failed'              => 'failed',
                'refunded'                              => 'refunded',
                default                                 => 'paid',
            };

            $orderStatus = match ((string)$order->status) {
                '1', 'completed', 'delivered'           => 'delivered',
                '2', 'cancelled'                        => 'cancelled',
                'processing', 'order_processing'        => 'processing',
                'shipped'                               => 'shipped',
                default                                 => 'pending',
            };

            $subtotal     = (float)($order->price_subtotal ?? 0);
            $shippingCost = (float)($order->price_shipping ?? 0);
            $discount     = (float)($order->coupon_discount ?? 0);
            $totalAmount  = (float)($order->price_total ?? ($subtotal + $shippingCost - $discount));

            $existing = $laravel->table('shop_orders')->where('order_number', $orderNumber)->get()->getRow();
            $orderData = [
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
                'updated_at'      => date('Y-m-d H:i:s'),
            ];

            if ($existing) {
                $laravel->table('shop_orders')->where('id', $existing->id)->update($orderData);
                $shopOrderId = $existing->id;
            } else {
                $orderData['order_number'] = $orderNumber;
                $orderData['created_at']   = $order->created_at ?? date('Y-m-d H:i:s');
                $laravel->table('shop_orders')->insert($orderData);
                $shopOrderId = $laravel->insertID();
            }

            // Sync items
            $items = $db->table('order_items')->where('order_id', (int)$orderId)->get()->getResult();
            $laravel->table('shop_order_items')->where('shop_order_id', $shopOrderId)->delete();

            foreach ($items as $item) {
                $imageData = null;
                if (!empty($item->image_data)) {
                    $img = json_decode($item->image_data, true);
                    $imageData = $img['path'] ?? null;
                }

                $laravel->table('shop_order_items')->insert([
                    'shop_order_id' => $shopOrderId,
                    'product_id'    => $item->product_id ?? null,
                    'product_title' => $item->product_title ?? 'Lab Component Item',
                    'product_image' => $imageData,
                    'unit_price'    => (float)($item->product_unit_price ?? 0),
                    'quantity'      => (int)($item->product_quantity ?? 1),
                    'total_price'   => (float)($item->product_total_price ?? ($item->product_unit_price * ($item->product_quantity ?? 1))),
                    'created_at'    => $item->created_at ?? date('Y-m-d H:i:s'),
                    'updated_at'    => $item->updated_at ?? date('Y-m-d H:i:s'),
                ]);
            }
        } catch (\Throwable $e) {
            log_message('error', '[LaravelShopOrderSync] syncOrderSafely error: ' . $e->getMessage());
        }
    }
}

