<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LabOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LabOfferController extends Controller
{
    public function index()
    {
        $offer = LabOffer::getActiveOffer();
        $title = __('Lab Offers & Promotional Campaigns');

        // Fetch available ClubShop products/bundles for quick selection
        $clubShopProducts = [];
        try {
            $clubShopProducts = DB::connection('clubshop')
                ->table('products as p')
                ->join('product_details as pd', 'pd.product_id', '=', 'p.id')
                ->select('p.id', 'pd.title', 'p.slug', 'p.price', 'p.price_discounted', 'p.is_bundle')
                ->where('p.is_deleted', 0)
                ->where('pd.lang_id', 1)
                ->where(function ($q) {
                    $q->where('p.is_bundle', 1)
                      ->orWhere('pd.title', 'like', '%Lab%')
                      ->orWhere('pd.title', 'like', '%Kit%')
                      ->orWhere('pd.title', 'like', '%Composite%');
                })
                ->orderBy('p.is_bundle', 'desc')
                ->orderBy('pd.title', 'asc')
                ->get();
        } catch (\Throwable $e) {}

        return view('admin.lab-offers.index', compact('offer', 'title', 'clubShopProducts'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'title'                          => 'required|string|max:255',
            'badge_text'                     => 'nullable|string|max:255',
            'subtitle'                       => 'nullable|string|max:1000',
            'is_active'                      => 'nullable|boolean',
            'booking_enabled'                => 'nullable|boolean',
            'booking_token_amount'           => 'nullable|numeric|min:0',
            'booking_extra_discount_percent' => 'nullable|numeric|min:0|max:100',
            'booking_title'                  => 'nullable|string|max:255',
            'booking_subtitle'               => 'nullable|string|max:1000',
            'countdown_end'                  => 'nullable|string',
            'whatsapp_number'                => 'nullable|string|max:30',
            'support_email'                  => 'nullable|email|max:255',
            'support_phone'                  => 'nullable|string|max:30',
            'highlights'                     => 'nullable|string',
            'terms'                          => 'nullable|string',
        ]);

        $offer = LabOffer::getActiveOffer();

        // Process highlights as array
        $highlights = [];
        if ($request->filled('highlights')) {
            $highlights = array_values(array_filter(array_map('trim', explode("\n", $request->input('highlights')))));
        }

        // Process products array
        $productsRaw = $request->input('products', []);
        $processedProducts = [];

        if (is_array($productsRaw)) {
            $sort = 1;
            foreach ($productsRaw as $p) {
                if (empty($p['title'])) continue;

                $features = [];
                if (!empty($p['features'])) {
                    if (is_array($p['features'])) {
                        $features = array_values(array_filter(array_map('trim', $p['features'])));
                    } else {
                        $features = array_values(array_filter(array_map('trim', explode("\n", (string)$p['features']))));
                    }
                }

                $mrp = (float)($p['mrp'] ?? 0);
                $offerPrice = (float)($p['offer_price'] ?? 0);
                $discount = $mrp > $offerPrice ? round($mrp - $offerPrice, 2) : 0;

                $processedProducts[] = [
                    'id'              => !empty($p['id']) ? $p['id'] : 'promo_' . Str::random(6),
                    'product_id'      => !empty($p['product_id']) ? (int)$p['product_id'] : null,
                    'title'           => trim($p['title']),
                    'subtitle'        => trim($p['subtitle'] ?? ''),
                    'badge'           => trim($p['badge'] ?? ''),
                    'mrp'             => $mrp,
                    'offer_price'     => $offerPrice,
                    'discount_amount' => $discount,
                    'features'        => $features,
                    'package_slug'    => trim($p['package_slug'] ?? 'skillvation-lab-stem'),
                    'cart_url'        => trim($p['cart_url'] ?? '/club-shop/skillvation-cart'),
                    'sort_order'      => isset($p['sort_order']) ? (int)$p['sort_order'] : $sort++,
                    'is_active'       => !empty($p['is_active']),
                    'stock_status'    => trim($p['stock_status'] ?? 'in_stock'),
                ];
            }
        }

        usort($processedProducts, fn($a, $b) => ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0));

        $offer->update([
            'title'                          => $request->input('title'),
            'badge_text'                     => $request->input('badge_text'),
            'subtitle'                       => $request->input('subtitle'),
            'is_active'                      => $request->boolean('is_active'),
            'booking_enabled'                => $request->boolean('booking_enabled'),
            'booking_token_amount'           => (float)$request->input('booking_token_amount', 25000),
            'booking_extra_discount_percent' => (float)$request->input('booking_extra_discount_percent', 10),
            'booking_title'                  => $request->input('booking_title', 'Special Booking & Advance Payment Offer'),
            'booking_subtitle'               => $request->input('booking_subtitle'),
            'countdown_end'                  => $request->filled('countdown_end') ? date('Y-m-d H:i:s', strtotime($request->input('countdown_end'))) : null,
            'whatsapp_number'                => $request->input('whatsapp_number'),
            'support_email'                  => $request->input('support_email'),
            'support_phone'                  => $request->input('support_phone'),
            'highlights'                     => $highlights,
            'terms'                          => $request->input('terms'),
            'products'                       => $processedProducts,
        ]);

        return redirect()->route('admin.lab-offers')
            ->with(['messege' => __('Lab offer settings & promotion products updated successfully!'), 'alert-type' => 'success']);
    }

    public function toggleStatus(Request $request)
    {
        $offer = LabOffer::getActiveOffer();
        $offer->is_active = !$offer->is_active;
        $offer->save();

        return response()->json([
            'status'    => 'success',
            'is_active' => $offer->is_active,
            'message'   => $offer->is_active ? __('Lab offer is now ENABLED') : __('Lab offer is now DISABLED'),
        ]);
    }
}

