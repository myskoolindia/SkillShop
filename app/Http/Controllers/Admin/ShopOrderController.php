<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use App\Services\ShopApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShopOrderController extends Controller
{
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

        return response()->json([
            'status' => 'success',
            'message' => 'Shop order created successfully',
            'order_number' => $orderNumber,
            'order_id' => $shopOrder->id,
            'pos_sync' => $posResult,
            'data' => $shopOrder->load('items'),
        ]);
    }
}
