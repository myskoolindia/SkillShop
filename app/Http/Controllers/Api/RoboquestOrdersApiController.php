<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RoboquestConnection;
use App\Models\SchoolMember;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Order\app\Models\Order;

class RoboquestOrdersApiController extends Controller
{
    /**
     * Authenticate the caller and determine their authorization scope.
     *
     * Orders contain confidential customer & financial data and CANNOT be public.
     * Allowed authentication methods:
     * 1. Admin Session / Guard (`auth('admin')`)
     * 2. Sanctum Bearer Token (`auth:sanctum`)
     * 3. RoboQuest Platform Secret (`X-RoboQuest-Platform-Key` or platform HMAC)
     * 4. RoboQuest School-signed HMAC (`X-RoboQuest-Signature` with school connection shared_key)
     * 5. Internal API Secret (`X-API-SECRET`)
     *
     * @return array [
     *   'authorized'        => bool,
     *   'is_admin'          => bool,
     *   'allowed_buyer_ids' => int[] | null (null means all; array means strictly scoped),
     *   'caller_info'       => string,
     *   'error'             => string|null,
     *   'status_code'       => int
     * ]
     */
    private function authorizeCaller(Request $request): array
    {
        // 1. Admin session / guard
        if (auth('admin')->check()) {
            return [
                'authorized'        => true,
                'is_admin'          => true,
                'allowed_buyer_ids' => null,
                'caller_info'       => 'admin_session:' . auth('admin')->id(),
                'error'             => null,
                'status_code'       => 200,
            ];
        }

        // 2. Sanctum token authenticated user
        $sanctumUser = $request->user('sanctum') ?? $request->user('web');
        if ($sanctumUser) {
            if ($sanctumUser->role === 'admin' || (method_exists($sanctumUser, 'hasRole') && $sanctumUser->hasRole('admin'))) {
                return [
                    'authorized'        => true,
                    'is_admin'          => true,
                    'allowed_buyer_ids' => null,
                    'caller_info'       => 'sanctum_admin:' . $sanctumUser->id,
                    'error'             => null,
                    'status_code'       => 200,
                ];
            }

            // School account: restricted strictly to own institutional orders + affiliated members
            if ($sanctumUser->role === 'school') {
                $memberUserIds = SchoolMember::where('school_id', $sanctumUser->id)
                    ->where('status', 'active')
                    ->pluck('user_id')
                    ->toArray();
                $allowedIds = array_unique(array_merge([(int)$sanctumUser->id], $memberUserIds));

                return [
                    'authorized'        => true,
                    'is_admin'          => false,
                    'allowed_buyer_ids' => $allowedIds,
                    'caller_info'       => 'sanctum_school:' . $sanctumUser->id,
                    'error'             => null,
                    'status_code'       => 200,
                ];
            }

            // Individual student/teacher: restricted strictly to own orders
            return [
                'authorized'        => true,
                'is_admin'          => false,
                'allowed_buyer_ids' => [(int)$sanctumUser->id],
                'caller_info'       => 'sanctum_user:' . $sanctumUser->id,
                'error'             => null,
                'status_code'       => 200,
            ];
        }

        // 3. RoboQuest Platform Key or CRRM API Secret
        $platformKey = (string)config('services.roboquest.platform_key');
        $defaultKey = (string)env('DEFAULT_ROBOQUEST_KEY');
        $crrmSecret = (string)config('services.crrm.api_secret');

        $providedKey = (string)($request->header('X-RoboQuest-Platform-Key')
            ?: $request->header('X-API-SECRET')
            ?: $request->header('X-RoboQuest-Key')
            ?: $request->bearerToken()
            ?: '');

        if ($providedKey !== '') {
            $isMatch = (!empty($platformKey) && hash_equals($platformKey, $providedKey))
                || (!empty($defaultKey) && hash_equals($defaultKey, $providedKey))
                || (!empty($crrmSecret) && hash_equals($crrmSecret, $providedKey));

            if ($isMatch) {
                return [
                    'authorized'        => true,
                    'is_admin'          => true,
                    'allowed_buyer_ids' => null,
                    'caller_info'       => 'platform_key',
                    'error'             => null,
                    'status_code'       => 200,
                ];
            }
        }

        // 4. RoboQuest HMAC Signature (Platform-signed or School-signed)
        $signature = (string)$request->header('X-RoboQuest-Signature');
        $schoolId = (string)($request->input('schoolId') ?: $request->input('school_id') ?: $request->header('X-RoboQuest-School-Id') ?: '');
        $timestamp = (int)($request->input('timestamp') ?: $request->header('X-RoboQuest-Timestamp') ?: 0);
        $nonce = (string)($request->input('nonce') ?: $request->header('X-RoboQuest-Nonce') ?: '');

        if ($signature !== '' && $timestamp > 0) {
            // Replay protection: max 120 seconds clock drift
            if (abs(time() - $timestamp) > 120) {
                return [
                    'authorized'  => false,
                    'error'       => 'Request signature has expired.',
                    'status_code' => 401,
                ];
            }

            $payload = (string)$request->getContent();

            // 4a. Check if signed by Platform Key
            if (!empty($platformKey) && strlen($platformKey) >= 32) {
                if (hash_equals(hash_hmac('sha256', $payload, $platformKey), $signature)) {
                    return [
                        'authorized'        => true,
                        'is_admin'          => true,
                        'allowed_buyer_ids' => null,
                        'caller_info'       => 'hmac_platform',
                        'error'             => null,
                        'status_code'       => 200,
                    ];
                }
            }

            // 4b. Check if signed by School Connection Key
            if ($schoolId !== '') {
                $connection = RoboquestConnection::where('school_id', $schoolId)->first();
                if ($connection && !empty($connection->shared_key)) {
                    if (hash_equals(hash_hmac('sha256', $payload, $connection->shared_key), $signature)) {
                        // Anti-replay nonce check
                        if ($nonce !== '') {
                            DB::table('roboquest_request_nonces')->where('expires_at', '<', now())->delete();
                            $saved = DB::table('roboquest_request_nonces')->insertOrIgnore([
                                'connection_id' => $connection->id,
                                'nonce'         => 'orders:' . $nonce,
                                'expires_at'    => now()->addMinutes(3),
                            ]);
                            if (!$saved) {
                                return [
                                    'authorized'  => false,
                                    'error'       => 'Nonce has already been used.',
                                    'status_code' => 409,
                                ];
                            }
                        }

                        // Strictly restrict to this school's own buyers
                        $schoolUserId = (int)$connection->school_user_id;
                        $memberUserIds = SchoolMember::where('school_id', $schoolUserId)
                            ->where('status', 'active')
                            ->pluck('user_id')
                            ->toArray();
                        $allowedIds = array_unique(array_merge([$schoolUserId], $memberUserIds));

                        return [
                            'authorized'        => true,
                            'is_admin'          => false,
                            'allowed_buyer_ids' => $allowedIds,
                            'caller_info'       => 'hmac_school:' . $schoolId,
                            'error'             => null,
                            'status_code'       => 200,
                        ];
                    }
                }
            }
        }

        // 5. Unauthenticated / Unauthorized
        return [
            'authorized'  => false,
            'is_admin'    => false,
            'error'       => 'Unauthorized. Order information is confidential and requires valid authentication credentials.',
            'status_code' => 401,
        ];
    }

    /**
     * List orders securely with strict scope isolation.
     */
    public function index(Request $request): JsonResponse
    {
        $auth = $this->authorizeCaller($request);
        if (!$auth['authorized']) {
            return response()->json([
                'status'  => 'error',
                'message' => $auth['error'],
            ], $auth['status_code'], [
                'Access-Control-Allow-Origin' => '*',
            ]);
        }

        $keyword = trim((string)($request->input('keyword') ?: $request->input('search') ?: ''));
        $orderStatus = trim((string)($request->input('order_status') ?: $request->input('status') ?: ''));
        $paymentStatus = trim((string)($request->input('payment_status') ?: ''));
        $paymentMethod = trim((string)($request->input('payment_method') ?: $request->input('gateway') ?: ''));
        $requestedBuyerId = $request->input('buyer_id') ?: $request->input('user_id') ?: $request->input('school_user_id');
        $email = trim((string)($request->input('email') ?: ''));
        $phone = trim((string)($request->input('phone') ?: ''));
        $schoolId = trim((string)($request->input('school_id') ?: $request->input('schoolId') ?: ''));

        $query = Order::query()->with([
            'user:id,name,email,phone,school_name,role,image',
            'orderItems.course:id,title,slug,type,api_course_id,price'
        ]);

        // ==========================================
        // STRICT CONFIDENTIALITY SCOPE ENFORCEMENT
        // ==========================================
        if (!$auth['is_admin']) {
            // Non-admin callers (schools, educators, members) can ONLY see their own allowed records
            if (empty($auth['allowed_buyer_ids'])) {
                return response()->json([
                    'status' => 'success',
                    'orders' => [],
                    'data'   => ['total' => 0, 'orders' => []],
                ]);
            }
            $query->whereIn('buyer_id', $auth['allowed_buyer_ids']);
        } else {
            // Super Admin / Platform: Can filter by specific buyer/school if requested
            if (!empty($requestedBuyerId)) {
                $query->where('buyer_id', (int)$requestedBuyerId);
            } elseif ($schoolId !== '') {
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
        }

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

        // Filter by email (within authorized scope)
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
            'Access-Control-Allow-Headers' => 'Authorization, X-RoboQuest-Signature, X-RoboQuest-Platform-Key, X-API-SECRET, Content-Type',
            'Cache-Control'                => 'no-store, no-cache, must-revalidate',
        ]);
    }

    /**
     * Get a single order detail securely.
     */
    public function show(Request $request, string $idOrInvoice): JsonResponse
    {
        $auth = $this->authorizeCaller($request);
        if (!$auth['authorized']) {
            return response()->json([
                'status'  => 'error',
                'message' => $auth['error'],
            ], $auth['status_code'], [
                'Access-Control-Allow-Origin' => '*',
            ]);
        }

        $query = Order::with([
            'user:id,name,email,phone,school_name,role,image',
            'orderItems.course:id,title,slug,type,api_course_id,price,instructor_id'
        ])
        ->where(function ($q) use ($idOrInvoice) {
            if (is_numeric($idOrInvoice)) {
                $q->where('id', (int)$idOrInvoice)->orWhere('invoice_id', $idOrInvoice);
            } else {
                $q->where('invoice_id', $idOrInvoice);
            }
        });

        // Strict confidentiality scope enforcement
        if (!$auth['is_admin']) {
            if (empty($auth['allowed_buyer_ids'])) {
                return response()->json(['status' => 'error', 'message' => 'Order not found.'], 404);
            }
            $query->whereIn('buyer_id', $auth['allowed_buyer_ids']);
        }

        $order = $query->first();

        if (!$order) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Order not found or access denied.',
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
            'Access-Control-Allow-Headers' => 'Authorization, X-RoboQuest-Signature, X-RoboQuest-Platform-Key, X-API-SECRET, Content-Type',
            'Cache-Control'                => 'no-store, no-cache, must-revalidate',
        ]);
    }

    /**
     * Standard order transformer with sensitive payment secrets stripped.
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
