<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\DefaultMail;
use Modules\GlobalSetting\app\Models\Setting;
use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use App\Services\ShopApiService;
use App\Services\WabaOtpService;
use App\Traits\MailSenderTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ShopOrderController extends Controller
{
    use MailSenderTrait;
    public function index(Request $request)
    {
        if (function_exists('checkAdminHasPermissionAndThrowException')) {
            checkAdminHasPermissionAndThrowException('order.management');
        }

        $query = ShopOrder::with(['items', 'user']);

        if ($request->filled('keyword')) {
            $kw = trim($request->keyword);
            $query->where(function ($q) use ($kw) {
                $q->where('order_number', 'like', "%{$kw}%")
                  ->orWhere('customer_name', 'like', "%{$kw}%")
                  ->orWhere('customer_phone', 'like', "%{$kw}%")
                  ->orWhere('customer_email', 'like', "%{$kw}%")
                  ->orWhere('payment_id', 'like', "%{$kw}%")
                  ->orWhere('pincode', 'like', "%{$kw}%")
                  ->orWhere('city', 'like', "%{$kw}%");
            });
        }

        if ($request->filled('order_status')) {
            $query->where('order_status', $request->order_status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orderBy = ($request->get('order_by') == '1' || $request->get('order_by') === 'asc') ? 'asc' : 'desc';
        $perPage = $request->get('par-page', 10);
        $perPage = ($perPage === 'all') ? 500 : (int)$perPage;

        $orders = $query->orderBy('id', $orderBy)->paginate($perPage)->withQueryString();
        $title = __('Shop Orders (SkillBox)');

        return view('admin.shop-orders.index', compact('orders', 'title'));
    }

    public function pending(Request $request)
    {
        if (function_exists('checkAdminHasPermissionAndThrowException')) {
            checkAdminHasPermissionAndThrowException('order.management');
        }

        $orders = ShopOrder::with(['items', 'user'])
            ->where('order_status', 'pending')
            ->orWhere('payment_status', 'pending')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $title = __('Pending Shop Orders');

        return view('admin.shop-orders.index', compact('orders', 'title'));
    }

    public function show($id)
    {
        if (function_exists('checkAdminHasPermissionAndThrowException')) {
            checkAdminHasPermissionAndThrowException('order.management');
        }

        $order = ShopOrder::with(['items', 'user'])->findOrFail($id);
        $title = __('Shop Order Details') . ' #' . $order->order_number;

        return view('admin.shop-orders.show', compact('order', 'title'));
    }

    public function updateStatus(Request $request, $id)
    {
        if (function_exists('checkAdminHasPermissionAndThrowException')) {
            checkAdminHasPermissionAndThrowException('order.management');
        }

        $order = ShopOrder::with('items')->findOrFail($id);

        $request->validate([
            'order_status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'tracking_number' => 'nullable|string|max:255',
            'courier_partner' => 'nullable|string|max:255',
            'admin_notes' => 'nullable|string',
        ]);

        $wasPaid = ($order->payment_status === 'paid');

        $order->order_status = $request->order_status;
        $order->payment_status = $request->payment_status;
        $order->tracking_number = $request->tracking_number;
        $order->courier_partner = $request->courier_partner;
        $order->admin_notes = $request->admin_notes;
        $order->save();

        if (!$wasPaid && $order->payment_status === 'paid') {
            ShopApiService::syncOrderToPos($order);
        }

        $notification = ['messege' => __('Order status updated successfully'), 'alert-type' => 'success'];
        return redirect()->back()->with($notification);
    }

    public function syncPos($id)
    {
        if (function_exists('checkAdminHasPermissionAndThrowException')) {
            checkAdminHasPermissionAndThrowException('order.management');
        }

        $order = ShopOrder::with('items')->findOrFail($id);
        $result = ShopApiService::syncOrderToPos($order);

        if ($result['success']) {
            $notification = ['messege' => __('POS order created successfully in remote system'), 'alert-type' => 'success'];
        } else {
            $notification = ['messege' => __('Failed to sync with POS API: ') . ($result['message'] ?? 'Unknown error'), 'alert-type' => 'error'];
        }

        return redirect()->back()->with($notification);
    }

    public function destroy($id)
    {
        if (function_exists('checkAdminHasPermissionAndThrowException')) {
            checkAdminHasPermissionAndThrowException('order.management');
        }

        $order = ShopOrder::findOrFail($id);
        $order->delete();

        $notification = ['messege' => __('Order deleted successfully'), 'alert-type' => 'success'];
        return redirect()->route('admin.shop-orders.index')->with($notification);
    }

    public function printInvoice($id)
    {
        if (function_exists('checkAdminHasPermissionAndThrowException')) {
            checkAdminHasPermissionAndThrowException('order.management');
        }

        $order = ShopOrder::with('items')->findOrFail($id);
        return view('admin.shop-orders.invoice', compact('order'));
    }

    /**
     * Store new shop order from frontend
     */
    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|string|max:255',
            'pincode' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'address_type' => 'nullable|string|max:50',
            'payment_method' => 'nullable|string|max:50',
            'payment_id' => 'nullable|string|max:255',
            'payment_status' => 'nullable|in:pending,paid,failed,refunded',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable',
            'items.*.title' => 'required|string',
            'items.*.image' => 'nullable|string',
            'items.*.unit_price' => 'required|numeric',
            'items.*.quantity' => 'required|integer|min:1',
            'total_amount' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'admin_notes' => 'nullable|string',
        ]);

        $orderNumber = 'SKILLBOX-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        $subtotal = 0;
        foreach ($validated['items'] as $item) {
            $subtotal += ($item['unit_price'] * $item['quantity']);
        }

        $totalAmount = $validated['total_amount'] ?? $subtotal;

        $shopOrder = ShopOrder::create([
            'order_number' => $orderNumber,
            'user_id' => auth()->id() ?? null,
            'customer_name' => !empty($validated['customer_name']) ? $validated['customer_name'] : 'School Representative',
            'customer_email' => !empty($validated['customer_email']) ? $validated['customer_email'] : (auth()->user()?->email ?? 'procurement@school.local'),
            'customer_phone' => $validated['customer_phone'],
            'pincode' => !empty($validated['pincode']) ? $validated['pincode'] : '560001',
            'city' => !empty($validated['city']) ? $validated['city'] : 'Bengaluru',
            'state' => !empty($validated['state']) ? $validated['state'] : 'Karnataka',
            'country' => $validated['country'] ?? 'India',
            'address' => !empty($validated['address']) ? $validated['address'] : 'Address to be confirmed on coordinator call',
            'address_type' => $validated['address_type'] ?? 'School / Institution',
            'payment_method' => $validated['payment_method'] ?? 'razorpay',
            'payment_id' => $validated['payment_id'] ?? null,
            'payment_status' => $validated['payment_status'] ?? 'paid',
            'order_status' => 'pending',
            'subtotal' => $subtotal,
            'shipping_cost' => 0,
            'discount' => (float)$request->input('discount', 0),
            'total_amount' => $totalAmount,
            'currency' => 'INR',
            'admin_notes' => $request->input('admin_notes'),
        ]);

        foreach ($validated['items'] as $item) {
            ShopOrderItem::create([
                'shop_order_id' => $shopOrder->id,
                'product_id' => $item['product_id'] ?? null,
                'product_title' => $item['title'],
                'product_image' => $item['image'] ?? null,
                'unit_price' => $item['unit_price'],
                'quantity' => $item['quantity'],
                'total_price' => $item['unit_price'] * $item['quantity'],
            ]);
        }

        // Post order details to POS API if payment is completed
        $posResult = null;
        if ($shopOrder->payment_status === 'paid') {
            $shopOrder->load('items');
            $posResult = ShopApiService::syncOrderToPos($shopOrder);
        }

        // ── 1. Send Order Confirmation Email ────────────────────────
        try {
            self::setMailConfig();
            $adminEmail = Setting::where('key', 'contact_message_receiver_mail')->value('value') ?? config('mail.from.address');
            $custEmail = $shopOrder->customer_email;
            $itemsHtml = '';
            foreach ($shopOrder->items as $it) {
                $itemsHtml .= '<tr><td style="padding:8px;border:1px solid #e2e8f0;">' . htmlspecialchars($it->product_title) . '</td>'
                    . '<td style="padding:8px;border:1px solid #e2e8f0;text-align:center;">' . $it->quantity . '</td>'
                    . '<td style="padding:8px;border:1px solid #e2e8f0;text-align:right;">₹' . number_format((float)$it->unit_price) . '</td>'
                    . '<td style="padding:8px;border:1px solid #e2e8f0;text-align:right;font-weight:bold;">₹' . number_format((float)$it->total_price) . '</td></tr>';
            }

            $orderTable = '<table style="width:100%;border-collapse:collapse;margin:16px 0;font-size:13px;font-family:sans-serif;">'
                . '<thead><tr style="background:#1e1b4b;color:#fff;"><th style="padding:8px;text-align:left;">Item</th><th style="padding:8px;text-align:center;">Qty</th><th style="padding:8px;text-align:right;">Price</th><th style="padding:8px;text-align:right;">Total</th></tr></thead>'
                . '<tbody>' . $itemsHtml . '</tbody>'
                . '<tfoot><tr style="background:#f8fafc;font-weight:bold;"><td colspan="3" style="padding:8px;text-align:right;border:1px solid #e2e8f0;">Total Amount Paid / Due:</td><td style="padding:8px;text-align:right;border:1px solid #e2e8f0;color:#1e1b4b;">₹' . number_format((float)$shopOrder->total_amount) . '</td></tr></tfoot>'
                . '</table>';

            $mailSubject = "Skillvation Lab Order Confirmed: {$orderNumber}";
            $mailBody = "<div style='font-family:sans-serif;color:#1e293b;max-width:600px;margin:0 auto;line-height:1.6;'>"
                . "<h2 style='color:#1e1b4b;'>Order Confirmed!</h2>"
                . "<p>Dear <strong>" . htmlspecialchars($shopOrder->customer_name) . "</strong>,</p>"
                . "<p>Thank you for placing your lab order with Skillvation. Your order reference is <strong>{$orderNumber}</strong>.</p>"
                . "<p><strong>Payment Status:</strong> " . ucfirst($shopOrder->payment_status) . " (" . strtoupper($shopOrder->payment_method) . ")</p>"
                . "<p><strong>Delivery Location:</strong> " . htmlspecialchars($shopOrder->address . ', ' . $shopOrder->city . ', ' . $shopOrder->state . ' - ' . $shopOrder->pincode) . "</p>"
                . $orderTable
                . "<div style='background:#eff6ff;border:1px solid #bfdbfe;padding:12px;border-radius:8px;font-size:12px;color:#1e40af;margin-top:16px;'>"
                . "<strong>Next Steps:</strong> Our technical deployment lead will contact you within 24 hours to schedule lab blueprinting and delivery logistics."
                . "</div>"
                . "<p style='margin-top:20px;font-size:12px;color:#64748b;'>Skillvation — MySkool Learning Private Limited<br>+91 99456 12226 | admissions@skillvation.com | www.skillvation.com</p>"
                . "</div>";

            if (!empty($custEmail) && !str_ends_with($custEmail, '@school.local')) {
                Mail::to($custEmail)->send(new DefaultMail(['subject' => $mailSubject], $mailBody));
            }
            if (!empty($adminEmail)) {
                Mail::to($adminEmail)->send(new DefaultMail(['subject' => "[Admin] New Lab Order: {$orderNumber} - " . $shopOrder->customer_name], $mailBody));
            }
        } catch (\Throwable $me) {
            Log::warning("[ShopOrder] Confirmation email failed: " . $me->getMessage());
        }

        // ── 2. Send WhatsApp Notification ──────────────────────────
        $whatsappUrl = null;
        if (!empty($shopOrder->customer_phone)) {
            try {
                $waba = app(WabaOtpService::class);
                $cleanPhone = $waba->sanitizePhone($shopOrder->customer_phone);
                $waText = "*Skillvation Lab Order Confirmed!*\n\n"
                    . "Dear {$shopOrder->customer_name},\n\n"
                    . "Your lab booking order has been successfully registered.\n\n"
                    . "📦 *Order Number:* {$orderNumber}\n"
                    . "💰 *Amount:* ₹" . number_format((float)$shopOrder->total_amount) . "\n"
                    . "💳 *Status:* " . ucfirst($shopOrder->payment_status) . " (" . strtoupper($shopOrder->payment_method) . ")\n"
                    . "📍 *Location:* {$shopOrder->city}, {$shopOrder->state}\n\n"
                    . "Our academic implementation team will contact you within 24 hours.\n\n"
                    . "_Skillvation — MySkool Learning Private Limited_\n"
                    . "Official Portal: https://skillvation.com";

                $whatsappUrl = "https://api.whatsapp.com/send?phone={$cleanPhone}&text=" . rawurlencode($waText);

                try {
                    $wabaPayload = [
                        'name' => $shopOrder->customer_name,
                        'school' => $shopOrder->customer_name,
                        'package' => $shopOrder->items->first()?->product_title ?? 'School Lab Order',
                        'request_type' => 'Lab Order Confirmation',
                        'total' => $shopOrder->total_amount,
                        'pdf_url' => url('/lab-offer'),
                        'product_url' => url('/lab-offer'),
                        'cart_url' => url('/lab-offer'),
                    ];
                    $waba->sendProposalMessage($shopOrder->customer_phone, $wabaPayload);
                } catch (\Throwable $e) {}

            } catch (\Throwable $we) {
                Log::warning("[ShopOrder] WhatsApp dispatch failed: " . $we->getMessage());
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Shop order created successfully',
            'order_number' => $orderNumber,
            'order_id' => $shopOrder->id,
            'whatsapp_url' => $whatsappUrl,
            'pos_sync' => $posResult,
            'data' => $shopOrder->load('items'),
        ]);
    }
}
