<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\LabOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LabOfferController extends Controller
{
    public function index(Request $request)
    {
        $offer = LabOffer::getActiveOffer();
        $isAdmin = Auth::guard('admin')->check();

        // If offer is disabled in admin, it shows inactive state to all visitors.
        // Admins can only force preview of the active layout by appending ?preview=1
        $isAdminPreview = $isAdmin && $request->boolean('preview');
        $isActive = (bool) ($offer && ($offer->is_active || $isAdminPreview));

        $rzpKey = DB::table('payment_gateways')->where('key', 'razorpay_key')->value('value') ?? 'rzp_test_RmNXpiPry9Pf7U';
        $user = Auth::guard('web')->user();

        $bookingActive = !empty($offer->booking_enabled);
        $tokenAmt = (float)($offer->booking_token_amount ?? 25000);
        $extraPct = (float)($offer->booking_extra_discount_percent ?? 10);

        return view('frontend.home-four.pages.lab-offer', compact(
            'offer', 'isActive', 'isAdmin', 'isAdminPreview', 'rzpKey', 'user',
            'bookingActive', 'tokenAmt', 'extraPct'
        ));
    }
}

