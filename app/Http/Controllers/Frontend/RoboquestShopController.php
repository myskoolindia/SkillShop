<?php
namespace App\Http\Controllers\Frontend;

use App\Models\{SchoolMember, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Http, Log};
use Illuminate\Support\Str;

/**
 * The teacher and student shops inside RoboQuest.
 *   POST /api/roboquest/shop/products  (school-signed)  products of the teacher (lab) shop or the student shop
 *   POST /api/roboquest/shop/link      (school-signed)  a one-time link that opens the shop signed in as that member
 *   POST /api/roboquest/shop/orders    (school-signed)  that member's own Club Shop orders (My orders in RoboQuest)
 *   POST /api/roboquest/class-invite   (school-signed)  WhatsApp invitations to an instructor's course class
 *   GET  /roboquest/shop-open?code=…                    signs the member in and continues to the Club Shop page
 * RoboQuest shows the products itself and opens the link in a frame for cart and payment.
 */
class RoboquestShopController extends RoboquestController
{
    private function catalogBase(string $audience): string
    {
        $club = config('services.club_shop.api_url') ?: url('/club-shop/api');
        $base = $audience === 'student' ? (env('STUDENT_SHOP_API_BASE_URL') ?: $club) : $club;
        return rtrim($base, '/') . '/';
    }

    public function products(Request $request)
    {
        $this->authenticate($request);
        $input = $request->validate(['audience' => 'required|in:teacher,student', 'categoryId' => 'nullable|integer|min:1', 'search' => 'nullable|string|max:80', 'page' => 'nullable|integer|min:1|max:200', 'perPage' => 'nullable|integer|min:1|max:60']);
        $base = $this->catalogBase($input['audience']);
        try {
            $categories = collect(Http::timeout(8)->get($base . 'categories')->json('data') ?? [])->map(fn ($c) => ['id' => (int) ($c['id'] ?? 0), 'name' => (string) ($c['name'] ?? $c['title'] ?? '')])->filter(fn ($c) => $c['id'] && $c['name'])->values();
            $path = !empty($input['categoryId']) ? "categories/{$input['categoryId']}/products" : 'products';
            $response = Http::timeout(8)->get($base . $path, ['page' => $input['page'] ?? 1, 'per_page' => $input['perPage'] ?? 24, 'search' => $input['search'] ?? null]);
        } catch (\Throwable $e) {
            Log::warning('[RoboQuest shop] catalogue unreachable', ['base' => $base, 'error' => $e->getMessage()]);
            abort(502, 'The shop catalogue is unavailable.');
        }
        if (!$response->successful()) Log::warning('[RoboQuest shop] catalogue refused', ['base' => $base, 'status' => $response->status()]);
        abort_unless($response->successful(), 502, 'The shop catalogue is unavailable.');
        $search = mb_strtolower(trim($input['search'] ?? ''));
        $products = collect($response->json('data') ?? [])
            ->filter(fn ($p) => $search === '' || str_contains(mb_strtolower(($p['title'] ?? '') . ' ' . ($p['sku'] ?? '')), $search))
            ->map(fn ($p) => ['id' => (int) $p['id'], 'title' => (string) ($p['title'] ?? ''), 'slug' => (string) ($p['slug'] ?? $p['id']), 'sku' => $p['sku'] ?? null,
                'price' => isset($p['price']) ? (float) $p['price'] : null, 'priceDiscounted' => isset($p['price_discounted']) ? (float) $p['price_discounted'] : null, 'currency' => $p['currency'] ?? 'INR',
                'image' => $p['image'] ?? null, 'categoryId' => isset($p['category_id']) ? (int) $p['category_id'] : null, 'inStock' => !isset($p['stock']) || (int) $p['stock'] > 0])
            ->values();
        $pagination = $response->json('pagination') ?? [];
        return response()->json(['audience' => $input['audience'], 'categories' => $categories, 'products' => $products,
            'page' => (int) ($pagination['page'] ?? $input['page'] ?? 1), 'pages' => (int) ($pagination['pages'] ?? 1), 'total' => (int) ($pagination['total'] ?? $products->count())])->header('Cache-Control', 'no-store');
    }

    /**
     * The signed-in RoboQuest person, if they may use this connection's shop: the school itself, an active member of it,
     * or (personal space) a teacher or student who is in no school.
     */
    private function member(\App\Models\RoboquestConnection $connection, string $externalId): User
    {
        $user = User::find((int) $externalId);
        $ok = $user && $user->status === 'active' && !in_array($user->is_banned, [1, '1', 'yes'], true) && (
            \App\Services\RoboquestService::isPersonal($connection)
                ? in_array($user->role, ['teacher', 'student', 'instructor'], true) && !SchoolMember::where('user_id', $user->id)->where('status', 'active')->exists()
                : ((int) $user->id === (int) $connection->school_user_id
                    || SchoolMember::where('school_id', $connection->school_user_id)->where('user_id', $user->id)->where('status', 'active')->exists()));
        abort_unless($ok, 404, 'This person is not an active member of the school on Skillvation.');
        return $user;
    }

    /** This person's own Club Shop orders, newest first (matched by their Skillvation email, as single sign-on does). */
    public function orders(Request $request)
    {
        $connection = $this->authenticate($request);
        $input = $request->validate(['externalId' => 'required|string|max:20', 'limit' => 'nullable|integer|min:1|max:50']);
        $user = $this->member($connection, $input['externalId']);
        try {
            $shop = DB::connection('clubshop');
            $buyer = $shop->table('users')->whereRaw('LOWER(email) = ?', [mb_strtolower($user->email)])->value('id');
            $orders = $buyer ? $shop->table('orders')->where('buyer_id', $buyer)->orderByDesc('id')->limit($input['limit'] ?? 20)->get() : collect();
            $items = $orders->isEmpty() ? collect() : $shop->table('order_items')->whereIn('order_id', $orders->pluck('id'))->get()->groupBy('order_id');
        } catch (\Throwable $e) {
            Log::warning('[RoboQuest shop] orders unavailable', ['error' => $e->getMessage()]);
            abort(502, 'Your orders are unavailable right now.');
        }
        // Same wording as the Club Shop's own order list.
        $status = fn ($o) => (int) $o->status === 2 ? 'cancelled'
            : ($o->payment_status === 'pending_payment' ? ($o->payment_method === 'cash_on_delivery' ? 'processing' : 'pending_payment')
            : ((int) $o->status === 1 ? 'completed' : 'processing'));
        $list = $orders->map(fn ($o) => [
            'number' => (string) $o->order_number, 'date' => (string) $o->created_at, 'status' => $status($o),
            'total' => (float) $o->price_total, 'currency' => $o->price_currency ?: 'INR',
            'items' => $items->get($o->id, collect())->map(fn ($i) => ['title' => (string) $i->product_title, 'quantity' => (int) ($i->product_quantity ?? 1),
                'price' => (float) ($i->product_total_price ?? $i->product_unit_price ?? 0)])->values(),
        ]);
        // Orders from the product pages (shop_orders); paid once Razorpay confirmed them.
        if (\Illuminate\Support\Facades\Schema::hasTable('shop_orders')) {
            $verified = \Illuminate\Support\Facades\Schema::hasColumn('shop_orders', 'payment_verified_at');
            $store = \App\Models\ShopOrder::with('items')->where(fn ($q) => $q->where('user_id', $user->id)->orWhereRaw('LOWER(customer_email) = ?', [mb_strtolower($user->email)]))
                ->where('order_number', 'not like', 'SKILLSHOP-%')->orderByDesc('id')->limit($input['limit'] ?? 20)->get();
            $list = $list->concat($store->map(fn ($o) => [
                'number' => (string) $o->order_number, 'date' => (string) $o->created_at,
                'status' => $o->order_status === 'cancelled' ? 'cancelled' : ($o->payment_status !== 'paid' || ($verified && !$o->payment_verified_at) ? 'pending_payment'
                    : ($o->order_status === 'delivered' ? 'completed' : 'processing')),
                'total' => (float) $o->total_amount, 'currency' => $o->currency ?: 'INR',
                'items' => $o->items->map(fn ($i) => ['title' => (string) $i->product_title, 'quantity' => (int) $i->quantity, 'price' => (float) $i->total_price])->values(),
            ]))->sortByDesc('date')->take($input['limit'] ?? 20);
        }
        return response()->json(['orders' => $list->values()])->header('Cache-Control', 'no-store');
    }

    /**
     * Instructor course class: RoboQuest sends the people of this school who have the course; each with a phone number
     * gets a WhatsApp invitation (sent after the response, so a slow provider never delays the school's sync).
     */
    public function classInvite(Request $request)
    {
        $connection = $this->authenticate($request);
        $input = $request->validate(['externalIds' => 'required|array|min:1|max:2000', 'externalIds.*' => 'string|max:20', 'topic' => 'required|string|max:120',
            'courseTitle' => 'nullable|string|max:300', 'startTime' => 'required|date', 'duration' => 'required|integer|min:15|max:240', 'hostName' => 'required|string|max:100',
            'joinUrl' => ['required', 'string', 'max:500', 'regex:#^https://([a-z0-9-]+\.)*zoom\.(us|com)/#i'], 'passcode' => 'nullable|string|max:40']);
        $people = [];
        foreach (array_unique($input['externalIds']) as $externalId) {
            try { $user = $this->member($connection, $externalId); } catch (\Symfony\Component\HttpKernel\Exception\HttpException) { continue; }
            if (!empty($user->phone)) $people[] = ['phone' => $user->phone, 'name' => $user->name];
        }
        $class = ['topic' => $input['topic'], 'course' => $input['courseTitle'] ?? '', 'host' => $input['hostName'], 'link' => $input['joinUrl'], 'passcode' => $input['passcode'] ?? '',
            'time' => \Carbon\Carbon::parse($input['startTime'])->setTimezone('Asia/Kolkata')->format('d M Y, g:i A') . ' IST', 'duration' => (int) $input['duration']];
        if ($people) dispatch(function () use ($people, $class) {
            $waba = app(\App\Services\WabaOtpService::class);
            foreach ($people as $person) $waba->sendClassInvite($person['phone'], ['name' => $person['name']] + $class);
        })->afterResponse();
        return response()->json(['queued' => count($people)])->header('Cache-Control', 'no-store');
    }

    public function link(Request $request)
    {
        $connection = $this->authenticate($request);
        $input = $request->validate(['externalId' => 'required|string|max:20', 'target' => ['required', 'string', 'max:300', 'regex:#^/[A-Za-z0-9/_\-.?=&%]*$#']]);
        abort_if(str_contains($input['target'], '..') || str_starts_with($input['target'], '//'), 422, 'Invalid shop page.');
        $user = $this->member($connection, $input['externalId']);
        // A product by its Club Shop ID (RoboQuest's Buy buttons for lab LMS come from the mapper, which knows IDs).
        if (preg_match('#^/product-id/(\d{1,10})$#', $input['target'], $m)) {
            $slug = DB::connection('clubshop')->table('products')->where('id', (int) $m[1])->where('is_deleted', 0)->value('slug');
            abort_unless($slug, 404, 'This product is not in the shop.');
            $input['target'] = '/' . $slug;
        }
        $code = bin2hex(random_bytes(32));
        DB::table('roboquest_shop_codes')->where('expires_at', '<', now()->subDay())->delete();
        DB::table('roboquest_shop_codes')->insert(['connection_id' => $connection->id, 'user_id' => $user->id, 'code_hash' => hash('sha256', $code), 'target' => $input['target'], 'expires_at' => now()->addMinutes(2), 'created_at' => now(), 'updated_at' => now()]);
        return response()->json(['url' => url('/roboquest/shop-open') . '?code=' . $code])->header('Cache-Control', 'no-store');
    }

    public function open(Request $request)
    {
        $code = (string) $request->query('code', '');
        abort_unless(preg_match('/^[a-f0-9]{64}$/', $code), 404);
        $row = DB::transaction(function () use ($code) {
            $row = DB::table('roboquest_shop_codes')->where('code_hash', hash('sha256', $code))->lockForUpdate()->first();
            if (!$row || $row->used_at || now()->greaterThan($row->expires_at)) return null;
            DB::table('roboquest_shop_codes')->where('id', $row->id)->update(['used_at' => now(), 'updated_at' => now()]);
            return $row;
        });
        abort_unless($row, 410, 'This shop link has expired. Open the shop again from RoboQuest.');
        $user = User::findOrFail($row->user_id);
        Auth::guard('web')->login($user);
        $request->session()->regenerate();
        return redirect()->route('sso.shop', ['target' => $row->target])->withHeaders(['Cache-Control' => 'no-store', 'Referrer-Policy' => 'no-referrer']);
    }
}
