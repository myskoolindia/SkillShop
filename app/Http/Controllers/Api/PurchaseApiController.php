<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Order\app\Models\Enrollment;
use Modules\Order\app\Models\Order;

/**
 * Post-purchase APIs.
 *
 * These endpoints expose what the purchase flow produces after a successful
 * payment across ALL six product verticals:
 *   - Skill2Skool        (Laravel courses / enrollments)
 *   - TTT                (Laravel courses + club-shop physical kits)
 *   - Upskill 4 Teacher  (Laravel courses / enrollments)
 *   - Shop               (club-shop physical product orders)
 *   - Composite Skill Lab(club-shop lab products + lab enquiries/invoices)
 *   - Skillvation Lab    (club-shop lab product + STEM enquiries)
 *
 * Data lives in three places, so the unified endpoints merge:
 *   1. Laravel  orders -> order_items -> enrollments   (course purchases)
 *   2. club-shop orders -> order_items                 (physical purchases, `clubshop` connection)
 *   3. Laravel  course_enquiries                       (lab proforma/invoice flow)
 *
 * All read endpoints are scoped to the authenticated user (Sanctum bearer
 * token). Cross-system records (club-shop, enquiries) are matched by the
 * user's email, since club-shop links to Skillvation accounts via email/SSO.
 */
class PurchaseApiController extends Controller
{
    /**
     * The six product verticals and how each is detected.
     * `enquiry_sources` map to course_enquiries.source prefixes.
     */
    public const VERTICALS = [
        'skill2skool'          => 'Skill2Skool',
        'ttt'                  => 'TTT',
        'upskill-4-teacher'    => 'Upskill 4 Teacher',
        'shop'                 => 'Shop',
        'composite-skill-lab'  => 'Composite Skill Lab',
        'skillvation-lab'      => 'Skillvation Lab',
    ];

    /**
     * Issue a Sanctum token for API clients (mobile app / external integrations).
     *
     * POST /api/login   body: { login: email|phone, password: string }
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $loginInput = trim($request->login);
        $isEmail    = filter_var($loginInput, FILTER_VALIDATE_EMAIL);

        $user = $isEmail
            ? User::where('email', $loginInput)->first()
            : User::where('phone', $loginInput)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid credentials.',
            ], 401);
        }

        if (($user->status ?? 'active') !== 'active') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Your account is inactive.',
            ], 403);
        }

        // One token per login; drop old tokens for this device name if reused.
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token'  => $token,
            'token_type' => 'Bearer',
            'user'   => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
        ]);
    }

    /**
     * List the authenticated user's completed orders (paginated).
     *
     * GET /api/my-orders?per_page=15&page=1
     */
    public function myOrders(Request $request): JsonResponse
    {
        $user = $request->user();

        $orders = Order::where('buyer_id', $user->id)
            ->withCount('orderItems')
            ->orderByDesc('id')
            ->paginate((int) $request->get('per_page', 15));

        $data = collect($orders->items())->map(fn ($order) => $this->orderSummary($order));

        return response()->json([
            'status' => 'success',
            'data'   => $data,
            'meta'   => [
                'current_page' => $orders->currentPage(),
                'last_page'    => $orders->lastPage(),
                'per_page'     => $orders->perPage(),
                'total'        => $orders->total(),
            ],
        ]);
    }

    /**
     * Single order detail by invoice id, including purchased items.
     * Scoped to the owner so one user can't read another's invoice.
     *
     * GET /api/orders/{invoice_id}
     */
    public function orderDetail(Request $request, string $invoiceId): JsonResponse
    {
        $user = $request->user();

        $order = Order::where('buyer_id', $user->id)
            ->where('invoice_id', $invoiceId)
            ->with(['orderItems.course'])
            ->first();

        if (! $order) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Order not found.',
            ], 404);
        }

        $items = $order->orderItems->map(function ($item) {
            $course = $item->course;

            return [
                'course_id'   => $item->course_id,
                'title'       => $course?->title,
                'slug'        => $course?->slug,
                'thumbnail'   => $course?->thumbnail ? asset($course->thumbnail) : null,
                'price'       => (float) $item->price,
                'price_label' => '₹' . number_format((float) $item->price, 2),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data'   => array_merge($this->orderSummary($order), [
                'items'          => $items,
                'coupon_code'    => $order->coupon_code,
                'coupon_percent' => $order->coupon_discount_percent,
                'coupon_amount'  => $order->coupon_discount_amount,
                'gateway_charge' => (float) $order->gateway_charge,
            ]),
        ]);
    }

    /**
     * Courses the user gained access to after purchase.
     *
     * GET /api/my-enrollments
     */
    public function myEnrollments(Request $request): JsonResponse
    {
        $user = $request->user();

        $enrollments = Enrollment::where('user_id', $user->id)
            ->where('has_access', 1)
            ->with('course')
            ->orderByDesc('id')
            ->get()
            ->map(function ($enrollment) {
                $course = $enrollment->course;

                return [
                    'enrollment_id' => $enrollment->id,
                    'order_id'      => $enrollment->order_id,
                    'course_id'     => $enrollment->course_id,
                    'title'         => $course?->title,
                    'slug'          => $course?->slug,
                    'thumbnail'     => $course?->thumbnail ? asset($course->thumbnail) : null,
                    'has_access'    => (bool) $enrollment->has_access,
                    'enrolled_at'   => optional($enrollment->created_at)->toDateTimeString(),
                ];
            });

        return response()->json([
            'status' => 'success',
            'count'  => $enrollments->count(),
            'data'   => $enrollments,
        ]);
    }

    /**
     * Quick purchase summary: orders count, total spent, enrolled courses.
     *
     * GET /api/my-purchases/summary
     */
    public function summary(Request $request): JsonResponse
    {
        $user = $request->user();

        $orders = Order::where('buyer_id', $user->id);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'orders_count'     => (clone $orders)->count(),
                'paid_orders'      => (clone $orders)->where('payment_status', 'paid')->count(),
                'total_spent'      => (float) (clone $orders)->where('payment_status', 'paid')->sum('paid_amount'),
                'enrolled_courses' => Enrollment::where('user_id', $user->id)->where('has_access', 1)->count(),
            ],
        ]);
    }

    /**
     * The vertical taxonomy (valid `vertical` filter values).
     *
     * GET /api/verticals
     */
    public function verticals(): JsonResponse
    {
        $data = [];
        foreach (self::VERTICALS as $key => $label) {
            $data[] = ['key' => $key, 'label' => $label];
        }

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    /**
     * Unified list of ALL post-purchase records across every vertical,
     * each tagged with its `vertical`. Optional ?vertical=<key> filter.
     *
     * GET /api/purchases?vertical=ttt
     *
     * Merges: Laravel course orders + club-shop orders + lab enquiries.
     */
    public function purchases(Request $request): JsonResponse
    {
        $user   = $request->user();
        $filter = $request->get('vertical');

        if ($filter && ! array_key_exists($filter, self::VERTICALS)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unknown vertical. Call GET /api/verticals for valid keys.',
            ], 422);
        }

        $items = collect()
            ->merge($this->courseePurchases($user))
            ->merge($this->shopPurchases($user))
            ->merge($this->labEnquiryPurchases($user));

        if ($filter) {
            $items = $items->where('vertical', $filter)->values();
        }

        $items = $items->sortByDesc('created_at')->values();

        return response()->json([
            'status'   => 'success',
            'filter'   => $filter,
            'count'    => $items->count(),
            'by_vertical' => $items->groupBy('vertical')->map->count(),
            'data'     => $items,
        ]);
    }

    /**
     * club-shop physical product orders for this user (matched by email).
     *
     * GET /api/shop-orders
     */
    public function shopOrders(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'status' => 'success',
            'data'   => $this->shopPurchases($user)->values(),
        ]);
    }

    /**
     * Composite Skill Lab / Skillvation Lab enquiry + invoice status.
     *
     * GET /api/lab-enquiries
     */
    public function labEnquiries(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'status' => 'success',
            'data'   => $this->labEnquiryPurchases($user)->values(),
        ]);
    }

    /* -------------------------------------------------------------------- */
    /* Per-source collectors (return a Collection of normalized purchases)  */
    /* -------------------------------------------------------------------- */

    /**
     * Laravel course purchases -> normalized, with vertical derived from the
     * course title/category. Covers Skill2Skool, Upskill 4 Teacher, TTT courses.
     */
    private function courseePurchases(User $user)
    {
        return Order::where('buyer_id', $user->id)
            ->with(['orderItems.course'])
            ->orderByDesc('id')
            ->get()
            ->flatMap(function (Order $order) {
                return $order->orderItems->map(function ($item) use ($order) {
                    $course = $item->course;

                    return [
                        'source'         => 'course',
                        'vertical'       => $this->verticalForCourse($course?->title),
                        'reference'      => $order->invoice_id,
                        'transaction_id' => $order->transaction_id,
                        'title'          => $course?->title,
                        'course_id'      => $item->course_id,
                        'amount'         => (float) $item->price,
                        'currency'       => $order->payable_currency ?? 'INR',
                        'payment_method' => $order->payment_method,
                        'payment_status' => $order->payment_status,
                        'status'         => $order->status,
                        'access'         => 'enrolled',
                        'created_at'     => optional($order->created_at)->toDateTimeString(),
                    ];
                });
            });
    }

    /**
     * club-shop physical product orders -> normalized. Covers Shop, plus
     * physical TTT kits and lab hardware. Matched by the user's email.
     */
    private function shopPurchases(User $user)
    {
        // club-shop users table links to Skillvation accounts by email.
        $shopUserId = DB::connection('clubshop')->table('users')
            ->where('email', $user->email)
            ->value('id');

        if (! $shopUserId) {
            return collect();
        }

        $orders = DB::connection('clubshop')->table('orders')
            ->where('buyer_id', $shopUserId)
            ->orderByDesc('id')
            ->get();

        if ($orders->isEmpty()) {
            return collect();
        }

        $itemsByOrder = DB::connection('clubshop')->table('order_items')
            ->whereIn('order_id', $orders->pluck('id'))
            ->get()
            ->groupBy('order_id');

        return $orders->flatMap(function ($order) use ($itemsByOrder) {
            $items = $itemsByOrder->get($order->id, collect());

            return $items->map(function ($it) use ($order) {
                return [
                    'source'         => 'shop',
                    'vertical'       => $this->verticalForShopItem($it->product_title ?? ''),
                    'reference'      => $order->order_number,
                    'transaction_id' => $order->bank_transaction_number ?? $order->checkout_token ?? null,
                    'title'          => $it->product_title,
                    'product_id'     => $it->product_id,
                    'amount'         => (float) ($it->product_total_price ?? $it->product_unit_price ?? 0),
                    'currency'       => $it->product_currency ?? $order->price_currency ?? 'INR',
                    'payment_method' => $order->payment_method,
                    'payment_status' => $order->payment_status,
                    'status'         => $order->status ?? $it->order_status,
                    'access'         => 'shipped_or_fulfilled',
                    'created_at'     => (string) $order->created_at,
                ];
            });
        });
    }

    /**
     * Composite Skill Lab / Skillvation Lab enquiries that reached the
     * proforma/invoice stage -> normalized. These are the lab "purchases".
     */
    private function labEnquiryPurchases(User $user)
    {
        return DB::table('course_enquiries')
            ->where('email', $user->email)
            ->orderByDesc('id')
            ->get()
            ->map(function ($e) {
                $hasInvoice = ! empty($e->invoice);
                $hasProforma = ! empty($e->proforma_invoice);

                return [
                    'source'         => 'lab_enquiry',
                    'vertical'       => $this->verticalForEnquirySource($e->source),
                    'reference'      => 'ENQ-' . $e->id,
                    'transaction_id' => null,
                    'title'          => $e->course_title ?: 'Lab Enquiry',
                    'enquiry_id'     => $e->id,
                    'amount'         => null,
                    'currency'       => 'INR',
                    'payment_method' => null,
                    'payment_status' => $hasInvoice ? 'invoiced' : ($hasProforma ? 'proforma' : 'enquiry'),
                    'status'         => $e->status,
                    'access'         => $hasInvoice ? 'invoiced' : 'in_progress',
                    'created_at'     => (string) $e->created_at,
                ];
            });
    }

    /* -------------------------------------------------------------------- */
    /* Vertical detection helpers                                           */
    /* -------------------------------------------------------------------- */

    private function verticalForCourse(?string $title): string
    {
        $t = strtoupper((string) $title);

        return match (true) {
            str_contains($t, 'TTT') || str_contains($t, 'TRAIN THE TRAINER') => 'ttt',
            str_contains($t, 'TEACHER') || str_contains($t, 'UPSKILL')
                || str_contains($t, 'NTT') || str_contains($t, 'MTT')
                || str_contains($t, 'ECE')                                   => 'upskill-4-teacher',
            str_contains($t, 'SKILLVATION LAB') || str_contains($t, 'STEM')  => 'skillvation-lab',
            str_contains($t, 'COMPOSITE')                                    => 'composite-skill-lab',
            default                                                          => 'skill2skool',
        };
    }

    private function verticalForShopItem(?string $title): string
    {
        $t = strtoupper((string) $title);

        return match (true) {
            str_contains($t, 'COMPOSITE')                                   => 'composite-skill-lab',
            str_contains($t, 'SKILLVATION LAB') || str_contains($t, 'STEM') => 'skillvation-lab',
            str_contains($t, 'TTT')                                         => 'ttt',
            default                                                         => 'shop',
        };
    }

    private function verticalForEnquirySource(?string $source): string
    {
        $s = strtolower((string) $source);

        return match (true) {
            str_starts_with($s, 'skillvation-lab')  => 'skillvation-lab',
            str_starts_with($s, 'composite-skill')  => 'composite-skill-lab',
            default                                 => 'composite-skill-lab',
        };
    }

    /**
     * Shared order summary shape used by list + detail endpoints.
     */
    private function orderSummary(Order $order): array
    {
        return [
            'id'             => $order->id,
            'invoice_id'     => $order->invoice_id,
            'transaction_id' => $order->transaction_id,
            'status'         => $order->status,
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'payable_amount' => (float) $order->payable_amount,
            'paid_amount'    => (float) $order->paid_amount,
            'currency'       => $order->payable_currency,
            'items_count'    => $order->order_items_count ?? $order->orderItems()->count(),
            'created_at'     => optional($order->created_at)->toDateTimeString(),
        ];
    }
}
