<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Order\app\Models\Order;

class RoboquestOrdersApiController extends Controller
{
    /**
     * List orders from https://devpro.skillvation.com/admin/orders for RoboQuest.
     *
     * Supports:
     * - keyword/search: search invoice ID, buyer name, email, phone, course title
     * - order_status / status: completed, processing, pending, declined
     * - payment_status: paid, pending, cancelled
     * - payment_method / gateway: e.g. razorpay, bank
     * - buyer_id / user_id / school_id / email / phone
     * - per_page / limit: default 20 (or 'all')
     * - page: default 1
     * - order_by: desc (default) or asc
     * - sort_by: id, created_at, paid_amount
     */
    public function index(Request $request): JsonResponse
    {
        $keyword = trim((string)($request->input('keyword') ?: $request->input('search') ?: ''));
        $orderStatus = trim((string)($request->input('order_status') ?: $request->input('status') ?: ''));
        $paymentStatus = trim((string)($request->input('payment_status') ?: ''));
        $paymentMethod = trim((string)($request->input('payment_method') ?: $request->input('gateway') ?: ''));
        $buyerId = $request->input('buyer_id') ?: $request->input('user_id') ?: $request->input('school_user_id');
        $email = trim((string)($request->input('email') ?: ''));
        $phone = trim((string)($request->input('phone') ?: ''));
        $schoolId = trim((string)($request->input('school_id') ?: $request->input('schoolId') ?: ''));

        $query = Order::query()->with([
            'user:id,name,email,phone,school_name,role,image',
            'orderItems.course:id,title,slug,type,api_course_id,price'
        ]);

        // Filter by keyword across invoice, buyer name/email/phone, and item course title
        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('invoice_id', 'like', "%{$keyword}%")
                  ->orWhere('payment_method', 'like', "%{$keyword}%")
                  ->orWhere('transaction_id', 'like', "%{$keyword}%")
                  ->orWhereHas('user', function ($uq) use ($keyword) {
                      $uq->where('name', 'like', "%{$keyword}%")
                         ->orWhere('email', 'like', "%{$keyword}%")
                         ->orWhere('phone', 'like', "%{$keyword}%")
                         ->orWhere('school_name', 'like', "%{$keyword}%");
                  })
                  ->orWhereHas('orderItems.course', function ($cq) use ($keyword) {
                      $cq->where('title', 'like', "%{$keyword}%");
                  });
            });
        }

        // Filter by order status
        if ($orderStatus !== '') {
            $query->where('status', $orderStatus);
        }

        // Filter by payment status
        if ($paymentStatus !== '') {
            $query->where('payment_status', $paymentStatus);
        }

        // Filter by payment method
        if ($paymentMethod !== '') {
            $query->where('payment_method', 'like', "%{$paymentMethod}%");
        }

        // Filter by buyer ID
        if (!empty($buyerId)) {
            $query->where('buyer_id', (int)$buyerId);
        }

        // Filter by email
        if ($email !== '') {
            $query->whereHas('user', function ($uq) use ($email) {
                $uq->whereRaw('LOWER(email) = ?', [mb_strtolower($email)]);
            });
        }

        // Filter by phone
        if ($phone !== '') {
            $cleanPhone = preg_replace('/\D/', '', $phone);
            $query->whereHas('user', function ($uq) use ($cleanPhone) {
                $uq->where('phone', 'like', "%{$cleanPhone}%");
            });
        }

        // Filter by school ID
        if ($schoolId !== '') {
            $schoolUser = User::where('role', 'school')
                ->where(function ($sq) use ($schoolId) {
                    $sq->where('id', $schoolId)
                       ->orWhere('email', $schoolId)
                       ->orWhere('name', 'like', "%{$schoolId}%")
                       ->orWhere('school_name', 'like', "%{$schoolId}%");
                })->first();

            if ($schoolUser) {
                $query->where('buyer_id', $schoolUser->id);
            }
        }

        // Sorting
        $sortBy = in_array($request->input('sort_by'), ['id', 'created_at', 'paid_amount', 'status'], true)
            ? $request->input('sort_by')
            : 'id';
        $orderBy = strtolower((string)$request->input('order_by')) === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $orderBy);

        // Pagination
        $perPageInput = $request->input('per_page') ?: $request->input('limit') ?: $request->input('par-page');
        $isAll = $perPageInput === 'all';
        $perPage = $isAll ? 1000 : max(1, min(100, (int)($perPageInput ?: 20)));

        $paginator = $query->paginate($perPage);

        $formattedOrders = collect($paginator->items())->map(function (Order $order) {
            return $this->formatOrder($order);
        });

        return response()->json([
            'status'     => 'success',
            'orders'     => $formattedOrders,
            'data'       => [
                'total'        => $paginator->total(),
                'per_page'     => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'from'         => $paginator->firstItem(),
                'to'           => $paginator->lastItem(),
                'orders'       => $formattedOrders,
            ],
            'pagination' => [
                'total'        => $paginator->total(),
                'per_page'     => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
            ],
        ], 200, [
            'Access-Control-Allow-Origin'  => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
            'Access-Control-Allow-Headers' => '*',
            'Cache-Control'                => 'no-store, no-cache, must-revalidate',
        ]);
    }

    /**
     * Get a single order detail by ID or Invoice ID.
     */
    public function show(string $idOrInvoice): JsonResponse
    {
        $order = Order::with([
            'user:id,name,email,phone,school_name,role,image',
            'orderItems.course:id,title,slug,type,api_course_id,price,instructor_id'
        ])
        ->where(function ($q) use ($idOrInvoice) {
            if (is_numeric($idOrInvoice)) {
                $q->where('id', (int)$idOrInvoice)->orWhere('invoice_id', $idOrInvoice);
            } else {
                $q->where('invoice_id', $idOrInvoice);
            }
        })
        ->first();

        if (!$order) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Order not found.',
            ], 404, [
                'Access-Control-Allow-Origin' => '*',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'order'  => $this->formatOrder($order),
            'data'   => $this->formatOrder($order),
        ], 200, [
            'Access-Control-Allow-Origin'  => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
            'Access-Control-Allow-Headers' => '*',
            'Cache-Control'                => 'no-store, no-cache, must-revalidate',
        ]);
    }

    /**
     * Standard order transformer for RoboQuest.
     */
    private function formatOrder(Order $order): array
    {
        $items = $order->orderItems->map(function ($item) {
            $course = $item->course;
            return [
                'id'              => (int)$item->id,
                'order_id'        => (int)$item->order_id,
                'course_id'       => (int)$item->course_id,
                'title'           => $course ? $course->title : 'Course #' . $item->course_id,
                'course_title'    => $course ? $course->title : 'Course #' . $item->course_id,
                'course_slug'     => $course ? $course->slug : null,
                'api_course_id'   => $course ? $course->api_course_id : null,
                'price'           => (float)$item->price,
                'commission_rate' => (float)$item->commission_rate,
            ];
        })->values();

        $buyer = $order->user ? [
            'id'          => (int)$order->user->id,
            'name'        => (string)$order->user->name,
            'email'       => (string)$order->user->email,
            'phone'       => $order->user->phone,
            'school_name' => $order->user->school_name ?? ($order->user->role === 'school' ? $order->user->name : null),
            'role'        => (string)$order->user->role,
            'image'       => $order->user->image,
        ] : null;

        return [
            'id'                     => (int)$order->id,
            'invoice_id'             => (string)$order->invoice_id,
            'order_number'           => (string)$order->invoice_id,
            'buyer_id'               => (int)$order->buyer_id,
            'buyer'                  => $buyer,
            'user'                   => $buyer,
            'paid_amount'            => (float)$order->paid_amount,
            'payable_amount'         => (float)$order->payable_amount,
            'amount'                 => (float)$order->paid_amount,
            'payable_currency'       => (string)($order->payable_currency ?: 'INR'),
            'currency'               => (string)($order->payable_currency ?: 'INR'),
            'payment_method'         => (string)$order->payment_method,
            'gateway'                => ucfirst((string)$order->payment_method),
            'status'                 => (string)$order->status,
            'payment_status'         => (string)$order->payment_status,
            'transaction_id'         => $order->transaction_id,
            'has_coupon'             => (bool)$order->has_coupon,
            'coupon_code'            => $order->coupon_code,
            'coupon_discount_amount' => (float)$order->coupon_discount_amount,
            'created_at'             => (string)$order->created_at,
            'date'                   => $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : null,
            'items_count'            => $items->count(),
            'items'                  => $items,
        ];
    }
}
