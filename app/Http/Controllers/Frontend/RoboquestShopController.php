<?php
namespace App\Http\Controllers\Frontend;

use App\Models\{SchoolMember, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Http};
use Illuminate\Support\Str;

/**
 * The teacher and student shops inside RoboQuest.
 *   POST /api/roboquest/shop/products  (school-signed)  products of the teacher (lab) shop or the student shop
 *   POST /api/roboquest/shop/link      (school-signed)  a one-time link that opens the shop signed in as that member
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
            abort(502, 'The shop catalogue is unavailable.');
        }
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

    public function link(Request $request)
    {
        $connection = $this->authenticate($request);
        $input = $request->validate(['externalId' => 'required|string|max:20', 'target' => ['required', 'string', 'max:300', 'regex:#^/[A-Za-z0-9/_\-.?=&%]*$#']]);
        abort_if(str_contains($input['target'], '..') || str_starts_with($input['target'], '//'), 422, 'Invalid shop page.');
        $user = User::find((int) $input['externalId']);
        $member = $user && ((int) $user->id === (int) $connection->school_user_id
            || SchoolMember::where('school_id', $connection->school_user_id)->where('user_id', $user->id)->where('status', 'active')->exists());
        abort_unless($member && $user->status === 'active', 404, 'This person is not an active member of the school on Skillvation.');
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
