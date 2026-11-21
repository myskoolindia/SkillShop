<?php

namespace Modules\Coupon\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Coupon\app\Models\Coupon;
use Modules\Coupon\app\Models\CouponHistory;

class CouponController extends Controller
{
    public function index()
    {
        checkAdminHasPermissionAndThrowException('coupon.management');

        $coupons = Coupon::where(['author_id' => 0])->latest()->get();
        $courses = class_exists('\App\Models\Course')
            ? \App\Models\Course::select('id', 'title', 'price', 'api_course_id')->orderBy('id')->get()
            : collect();

        return view('coupon::index', compact('coupons', 'courses'));
    }

    public function store(Request $request)
    {
        checkAdminHasPermissionAndThrowException('coupon.management');

        $rules = [
            'coupon_code'          => 'required|string|max:50|unique:coupons,coupon_code',
            'discount_type'        => 'required|in:percentage,fixed',
            'offer_percentage'     => 'nullable|numeric|min:0|max:100',
            'discount_amount'      => 'nullable|numeric|min:0',
            'max_discount_amount'  => 'nullable|numeric|min:0',
            'min_price'            => 'nullable|numeric|min:0',
            'course_ids'           => 'nullable|array',
            'start_date'           => 'nullable|date',
            'expired_date'         => 'nullable|date',
            'usage_limit_total'    => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'status'               => 'required|in:active,inactive',
        ];

        $customMessages = [
            'coupon_code.required' => __('Coupon code is required'),
            'coupon_code.unique'   => __('Coupon already exists'),
            'discount_type.in'     => __('Invalid discount type selected'),
        ];

        $this->validate($request, $rules, $customMessages);

        $discountType = $request->discount_type ?: 'percentage';
        $offerPercent = $discountType === 'percentage' ? (float)($request->offer_percentage ?? 0) : 0;
        $discountAmount = $discountType === 'fixed' ? (float)($request->discount_amount ?? 0) : null;

        $coupon = new Coupon();
        $coupon->author_id            = 0;
        $coupon->coupon_code          = strtoupper(trim($request->coupon_code));
        $coupon->discount_type        = $discountType;
        $coupon->offer_percentage     = $offerPercent;
        $coupon->discount_amount      = $discountAmount;
        $coupon->max_discount_amount  = $request->filled('max_discount_amount') ? (float)$request->max_discount_amount : null;
        $coupon->min_price            = $request->filled('min_price') ? (float)$request->min_price : 0.00;
        $coupon->course_ids           = !empty($request->course_ids) ? array_values(array_filter($request->course_ids)) : null;
        $coupon->start_date           = $request->filled('start_date') ? $request->start_date : null;
        $coupon->expired_date         = $request->filled('expired_date') ? $request->expired_date : null;
        $coupon->usage_limit_total    = $request->filled('usage_limit_total') ? (int)$request->usage_limit_total : null;
        $coupon->usage_limit_per_user = $request->filled('usage_limit_per_user') ? (int)$request->usage_limit_per_user : 1;
        $coupon->times_used           = 0;
        $coupon->status               = $request->status ?: 'active';
        $coupon->save();

        $notification = ['messege' => __('Created Successfully'), 'alert-type' => 'success'];
        return redirect()->back()->with($notification);
    }

    public function update(Request $request, $id)
    {
        checkAdminHasPermissionAndThrowException('coupon.management');

        $rules = [
            'coupon_code'          => 'required|string|max:50|unique:coupons,coupon_code,' . $id,
            'discount_type'        => 'required|in:percentage,fixed',
            'offer_percentage'     => 'nullable|numeric|min:0|max:100',
            'discount_amount'      => 'nullable|numeric|min:0',
            'max_discount_amount'  => 'nullable|numeric|min:0',
            'min_price'            => 'nullable|numeric|min:0',
            'course_ids'           => 'nullable|array',
            'start_date'           => 'nullable|date',
            'expired_date'         => 'nullable|date',
            'usage_limit_total'    => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'status'               => 'required|in:active,inactive',
        ];

        $customMessages = [
            'coupon_code.required' => __('Coupon code is required'),
            'coupon_code.unique'   => __('Coupon already exists'),
            'discount_type.in'     => __('Invalid discount type selected'),
        ];

        $this->validate($request, $rules, $customMessages);

        $coupon = Coupon::findOrFail($id);

        $discountType = $request->discount_type ?: 'percentage';
        $offerPercent = $discountType === 'percentage' ? (float)($request->offer_percentage ?? 0) : 0;
        $discountAmount = $discountType === 'fixed' ? (float)($request->discount_amount ?? 0) : null;

        $coupon->coupon_code          = strtoupper(trim($request->coupon_code));
        $coupon->discount_type        = $discountType;
        $coupon->offer_percentage     = $offerPercent;
        $coupon->discount_amount      = $discountAmount;
        $coupon->max_discount_amount  = $request->filled('max_discount_amount') ? (float)$request->max_discount_amount : null;
        $coupon->min_price            = $request->filled('min_price') ? (float)$request->min_price : 0.00;
        $coupon->course_ids           = !empty($request->course_ids) ? array_values(array_filter($request->course_ids)) : null;
        $coupon->start_date           = $request->filled('start_date') ? $request->start_date : null;
        $coupon->expired_date         = $request->filled('expired_date') ? $request->expired_date : null;
        $coupon->usage_limit_total    = $request->filled('usage_limit_total') ? (int)$request->usage_limit_total : null;
        $coupon->usage_limit_per_user = $request->filled('usage_limit_per_user') ? (int)$request->usage_limit_per_user : 1;
        $coupon->status               = $request->status ?: 'active';
        $coupon->save();

        $notification = ['messege' => __('Updated Successfully'), 'alert-type' => 'success'];
        return redirect()->back()->with($notification);
    }

    public function destroy($id)
    {
        checkAdminHasPermissionAndThrowException('coupon.management');

        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        $notification = ['messege' => __('Deleted Successfully'), 'alert-type' => 'success'];
        return redirect()->back()->with($notification);
    }

    public function coupon_history()
    {
        checkAdminHasPermissionAndThrowException('coupon.management');

        $histories = CouponHistory::with(['coupon', 'user'])->latest()->paginate(25);
        return view('coupon::history', compact('histories'));
    }
}
