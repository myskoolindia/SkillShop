<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Traits\RedirectHelperTrait;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Modules\Coupon\app\Models\Coupon;

class CartController extends Controller {
    use RedirectHelperTrait;

    protected array $skill2schoolGradeApiIds = [27, 2, 52, 53, 54];
    protected array $skill2schoolGradeCourseIds = [10, 1, 5, 13, 14];

    public function isSkill2SchoolGradeCourse($course): bool {
        if (!$course) {
            return false;
        }
        if (in_array((int)($course->api_course_id ?? 0), $this->skill2schoolGradeApiIds, true)) {
            return true;
        }
        if (in_array((int)($course->id ?? 0), $this->skill2schoolGradeCourseIds, true)) {
            return true;
        }
        $title = strtolower(trim($course->title ?? ''));
        if (preg_match('/^grade\s*[1-5](\b|$)/i', $title) && !str_contains($title, 'teacher') && !str_contains($title, 'upskill')) {
            return true;
        }
        return false;
    }

    public function addSkill2SchoolBundle(Request $request) {
        $courses = Course::active()
            ->whereIn('api_course_id', $this->skill2schoolGradeApiIds)
            ->get();

        if ($courses->isEmpty()) {
            $courses = Course::active()
                ->whereIn('id', $this->skill2schoolGradeCourseIds)
                ->get();
        }

        if ($courses->isEmpty()) {
            return response([
                'status'  => 'error',
                'message' => __('Skill 2 Skool courses could not be found!'),
            ]);
        }

        $allAlreadyInCart = true;
        $addedAny = false;

        foreach ($courses as $c) {
            if ($this->checkIfOwnCourse($c->id)) {
                continue;
            }
            if (!$this->checkItemExist($c->id)) {
                $allAlreadyInCart = false;
                $price = $c->discount > 0 ? $c->discount : $c->price;
                Cart::add([
                    'id'      => $c->id,
                    'name'    => $c->title,
                    'qty'     => 1,
                    'price'   => $price,
                    'weight'  => 0,
                    'options' => [
                        'image'          => $c->thumbnail,
                        'slug'           => $c->slug,
                        'real_price'     => $c->price,
                        'discount_price' => $c->discount,
                        'bundle'         => 'skill2school_5grades',
                        'api_course_id'  => $c->api_course_id,
                    ],
                ]);
                $addedAny = true;
            }
        }

        $this->updateCouponDiscountAmount();

        $cartCount = Cart::content()->count();
        $checkoutUrl = route('checkout.index');
        $cartUrl = route('cart');

        if (!$addedAny && $allAlreadyInCart) {
            return response([
                'status'       => 'already',
                'message'      => __('All 5 Skill 2 Skool grades are already in your cart!'),
                'checkout_url' => $checkoutUrl,
                'cart_url'     => $cartUrl,
                'cart_count'   => $cartCount,
            ]);
        }

        return response([
            'status'       => 'success',
            'message'      => __('Skill 2 Skool Complete Programme (All 5 Grades) added to cart!'),
            'cart_count'   => $cartCount,
            'checkout_url' => $checkoutUrl,
            'cart_url'     => $cartUrl,
        ]);
    }

    public function getOrCreateCreditCourse(int $credits) {
        $configs = [
            10 => [
                'title'       => 'Product 1 - 10 Credits (Free Trial)',
                'slug'        => 'skill2school-product-10-credits',
                'price'       => 0,
                'course_code' => 'S2S-CREDIT-10',
                'thumbnail'   => 'designs/img/credit-10.png',
                'description' => '10 Skill2School Credits for LATAA-OS, Student Assessments, and Lesson Plans.',
            ],
            100 => [
                'title'       => 'Product 2 - 100 Credits (Standard)',
                'slug'        => 'skill2school-product-100-credits',
                'price'       => 100,
                'course_code' => 'S2S-CREDIT-100',
                'thumbnail'   => 'designs/img/credit-100.png',
                'description' => '100 Skill2School Credits for LATAA-OS, Student Assessments, and Lesson Plans.',
            ],
            1000 => [
                'title'       => 'Product 3 - 1000 Credits (Pro)',
                'slug'        => 'skill2school-product-1000-credits',
                'price'       => 1000,
                'course_code' => 'S2S-CREDIT-1000',
                'thumbnail'   => 'designs/img/credit-1000.png',
                'description' => '1000 Skill2School Credits for LATAA-OS, Student Assessments, and Lesson Plans.',
            ],
        ];

        if (!isset($configs[$credits])) {
            return null;
        }

        $cfg = $configs[$credits];

        $course = Course::where('slug', $cfg['slug'])->first();
        if (!$course) {
            $course = Course::create([
                'title'          => $cfg['title'],
                'slug'           => $cfg['slug'],
                'price'          => $cfg['price'],
                'discount'       => 0,
                'course_code'    => $cfg['course_code'],
                'instructor_id'  => 1,
                'category_id'    => 1,
                'type'           => 'credits',
                'thumbnail'      => $cfg['thumbnail'],
                'description'    => $cfg['description'],
                'seo_description'=> $cfg['description'],
                'status'         => 'active',
                'is_approved'    => 'approved',
            ]);
        }

        return $course;
    }

    public function addCreditProduct(Request $request) {
        $credits = (int)$request->input('credits', 0);
        $course = $this->getOrCreateCreditCourse($credits);

        if (!$course) {
            return response([
                'status'  => 'error',
                'message' => __('Invalid credit package selected!'),
            ]);
        }

        if ($this->checkItemExist($course->id)) {
            return response([
                'status'       => 'already',
                'message'      => __(':title is already in your cart!', ['title' => $course->title]),
                'checkout_url' => route('checkout.index'),
                'cart_url'     => route('cart'),
                'cart_count'   => Cart::content()->count(),
            ]);
        }

        $price = (float)$course->price;

        Cart::add([
            'id'      => $course->id,
            'name'    => $course->title,
            'qty'     => 1,
            'price'   => $price,
            'weight'  => 0,
            'options' => [
                'image'          => $course->thumbnail,
                'slug'           => $course->slug,
                'real_price'     => $course->price,
                'discount_price' => 0,
                'product_type'   => 'credits',
                'credits'        => $credits,
            ],
        ]);

        $this->updateCouponDiscountAmount();

        return response([
            'status'       => 'success',
            'message'      => __(':title added to cart successfully!', ['title' => $course->title]),
            'cart_count'   => Cart::content()->count(),
            'checkout_url' => route('checkout.index'),
            'cart_url'     => route('cart'),
        ]);
    }

    function index() {
        // if cart is empty then remove coupon session
        if (Cart::content()->count() == 0) {
            $this->destroyCouponSession();
        }

        $products = Cart::content();
        $cartTotal = $this->cartTotal();
        $discountAmount = (float)Session::get('coupon_discount_amount', 0);
        $total = currency(max(0, $cartTotal - $discountAmount));
        $coupon = Session::has('coupon_code') ? Session::get('coupon_code') : '';
        $discountPercent = Session::has('offer_percentage') ? Session::get('offer_percentage') : 0;
        return view('frontend.pages.cart', compact('products', 'total', 'discountAmount', 'discountPercent', 'coupon'));
    }

    function addToCart(Request $request, string $id) {
        $course = Course::active()->where('id', $id)->first();
        if (!$course) {
            $course = Course::active()->where('api_course_id', $id)->first();
        }
        if (!$course) {
            $course = Course::active()->where('slug', $id)->first();
        }

        if ($course && $this->isSkill2SchoolGradeCourse($course)) {
            return $this->addSkill2SchoolBundle($request);
        }

        if (!$course) {
            return response(['status' => 'error', 'message' => 'Course not found!']);
        }

        if ($this->checkItemExist($course->id)) {
            return response([
                'status' => 'already',
                'message' => 'Already added to cart!',
                'checkout_url' => route('checkout.index'),
                'cart_url' => route('cart'),
                'cart_count' => Cart::content()->count(),
            ]);
        }
        if ($this->checkIfOwnCourse($course->id)) {
            return response(['status' => 'error', 'message' => 'You can not add to cart your own course!']);
        }

        $price = $course->discount > 0 ? $course->discount : $course->price;

        $cartData = [];
        $cartData['id'] = $course->id;
        $cartData['name'] = $course->title;
        $cartData['qty'] = 1;
        $cartData['price'] = $price;
        $cartData['weight'] = 0;
        $cartData['options']['image'] = $course->thumbnail;
        $cartData['options']['slug'] = $course->slug;
        $cartData['options']['real_price'] = $course->price;
        $cartData['options']['discount_price'] = $course->discount;

        Cart::add($cartData);

        $this->updateCouponDiscountAmount();

        $response = [
            'status' => 'success',
            'message' => 'Added to cart successfully!',
            'cart_count' => Cart::content()->count(),
            'checkout_url' => route('checkout.index'),
            'cart_url' => route('cart'),
        ];

        $settings = cache()->get('setting');
        $marketingSettings = cache()->get('marketing_setting');
        if ($settings->google_tagmanager_status == 'active' && $marketingSettings->add_to_cart) {
            $cartData['price'] = currency($course->price);
            $cartData['options']['real_price'] = currency($course->price);
            $cartData['options']['image'] = asset($course->thumbnail);
            $cartData['options']['slug'] = route('course.show', $course->slug);
            unset($cartData['id']);
            $cartData['user'] = auth('web')->check() ? ['name' => auth('web')->user()->name, 'email' => auth('web')->user()->email] : 'guest';
            $response['dataLayer'] = $cartData;
        }

        return response($response);
    }

    function removeCartItem(string $rowId) {
        $cartItem = Cart::get($rowId);
        if ($cartItem) {
            $isBundle = ($cartItem->options?->bundle === 'skill2school_5grades')
                || in_array((int)($cartItem->options?->api_course_id ?? 0), $this->skill2schoolGradeApiIds, true)
                || in_array((int)$cartItem->id, $this->skill2schoolGradeCourseIds, true);

            if ($isBundle) {
                // If any grade in the 5-grade bundle is removed, remove all 5 grades together
                foreach (Cart::content() as $rId => $item) {
                    $itemIsBundle = ($item->options?->bundle === 'skill2school_5grades')
                        || in_array((int)($item->options?->api_course_id ?? 0), $this->skill2schoolGradeApiIds, true)
                        || in_array((int)$item->id, $this->skill2schoolGradeCourseIds, true);
                    if ($itemIsBundle) {
                        Cart::remove($rId);
                    }
                }

                $this->updateCouponDiscountAmount();

                $notification = [
                    'messege'    => __('Skill 2 Skool Complete Programme (All 5 Grades) removed from cart!'),
                    'alert-type' => 'success',
                ];
                return redirect()->back()->with($notification);
            }
        }

        $cartItemData = $cartItem?->toArray();
        if ($cartItemData) {
            unset($cartItemData['rowId'], $cartItemData['id']);
            $cartItemData['price'] = currency($cartItemData['price']);
            $cartItemData['options']['real_price'] = currency($cartItemData['options']['real_price']);
            $cartItemData['options']['image'] = asset($cartItemData['options']['image']);
            $cartItemData['options']['slug'] = route('course.show', $cartItemData['options']['slug']);
            $cartItemData['user'] = auth('web')->check() ? [
                'name'  => auth('web')->user()->name,
                'email' => auth('web')->user()->email,
            ] : 'guest';

            $settings = cache()->get('setting');
            $marketingSettings = cache()->get('marketing_setting');
            if ($settings->google_tagmanager_status == 'active' && $marketingSettings->remove_from_cart) {
                session()->put('removeFromCart', $cartItemData);
            }
        }
        Cart::remove($rowId);
        $notification = [
            'messege'    => __('Item removed from cart!'),
            'alert-type' => 'success',
        ];

        $this->updateCouponDiscountAmount();

        return redirect()->back()->with($notification);
    }

    function cartTotal() {
        $cartTotal = 0;

        $cartItems = Cart::content();
        foreach ($cartItems as $key => $cartItem) {
            $cartTotal += $cartItem->price;
        }

        return $cartTotal;
    }

    function checkItemExist(string $id) {
        $cartItems = Cart::content();
        foreach ($cartItems as $key => $cartItem) {
            if ($cartItem->id == $id) {
                return true;
            }
        }
        return false;
    }

    function checkIfOwnCourse(string $id) {
        if (!userAuth()) return false;
        return Course::where('id', $id)->where('instructor_id', userAuth()?->id)->exists();
    }

    function addToCartByApiId(Request $request, string $api_course_id) {
        $course = Course::where('api_course_id', $api_course_id)
            ->where('status', 'active')
            ->first();

        if ($course && $this->isSkill2SchoolGradeCourse($course)) {
            return $this->addSkill2SchoolBundle($request);
        }

        if (!$course) {
            try {
                $apiBase = rtrim(env('APP_API_BASE', 'https://collab.myskool.club'), '/');
                $apiUrl  = env('APP_API') ?: "{$apiBase}/api/collab-courses";
                $apiRes  = \Illuminate\Support\Facades\Http::withoutVerifying()->timeout(8)->get($apiUrl);
                if ($apiRes->successful()) {
                    $apiCourses = $apiRes->json()['courses']['data'] ?? [];
                    $apiCourseData = collect($apiCourses)->firstWhere('id', (int)$api_course_id);
                    if ($apiCourseData) {
                        $coverFile = $apiCourseData['cover_image'] ?? null;
                        $localImg = file_exists(public_path("uploads/courses/camps/{$api_course_id}.png"))
                            ? "uploads/courses/camps/{$api_course_id}.png"
                            : (file_exists(public_path("uploads/courses/u4s/{$api_course_id}.png")) ? "uploads/courses/u4s/{$api_course_id}.png" : null);
                        $thumb = $localImg ?: ($coverFile ? "{$apiBase}/assets/images/event/cover/{$coverFile}" : 'designs/img/TTT-1.png');
                        $price = (float)($apiCourseData['price'] ?? 0);
                        
                        $course = Course::create([
                            'title' => $apiCourseData['title'] ?? 'Skill Course',
                            'slug' => \Illuminate\Support\Str::slug($apiCourseData['slug'] ?? $apiCourseData['title'] ?? ('course-' . $api_course_id)),
                            'api_course_id' => $api_course_id,
                            'course_code' => $apiCourseData['course_code'] ?? ('crs-' . $api_course_id),
                            'price' => $price,
                            'discount' => 0,
                            'instructor_id' => 1,
                            'category_id' => 1,
                            'type' => 'course',
                            'thumbnail' => $thumb,
                            'description' => $apiCourseData['description'] ?? '',
                            'seo_description' => strip_tags($apiCourseData['short_description'] ?? ($apiCourseData['description'] ?? '')),
                            'status' => 'active',
                            'is_approved' => 'approved',
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Auto-sync LMS course: ' . $e->getMessage());
            }
        }

        if (!$course) {
            return response(['status' => 'error', 'message' => 'Course not found.']);
        }

        if ($this->checkItemExist($course->id)) {
            return response([
                'status' => 'already',
                'message' => 'Already in cart!',
                'checkout_url' => route('checkout.index'),
                'cart_url' => route('cart'),
                'cart_count' => Cart::content()->count(),
            ]);
        }

        if ($this->checkIfOwnCourse($course->id)) {
            return response(['status' => 'error', 'message' => 'You cannot purchase your own course.']);
        }

        $price = $course->discount > 0 ? $course->discount : $course->price;

        Cart::add([
            'id'      => $course->id,
            'name'    => $course->title,
            'qty'     => 1,
            'price'   => $price,
            'weight'  => 0,
            'options' => [
                'image'          => $course->thumbnail,
                'slug'           => $course->slug,
                'real_price'     => $course->price,
                'discount_price' => $course->discount,
            ],
        ]);

        $this->updateCouponDiscountAmount();

        return response([
            'status'       => 'success',
            'message'      => 'Added to cart!',
            'checkout_url' => route('checkout.index'),
            'cart_count'   => Cart::content()->count(),
        ]);
    }

    public function calculateCouponDiscount($cartTotal, $coupon, $cartItems = null): float {
        if (!$coupon) {
            return 0.0;
        }

        $applicableTotal = (float)$cartTotal;

        // If coupon is restricted to specific courses, only calculate discount on those matching courses
        $courseIds = is_array($coupon->course_ids) ? $coupon->course_ids : (json_decode($coupon->course_ids ?? '', true) ?: []);
        if (!empty($courseIds) && $cartItems && count($cartItems) > 0) {
            $applicableTotal = 0.0;
            $courseIdsStr = array_map('strval', $courseIds);
            foreach ($cartItems as $item) {
                $itemId = (string)$item->id;
                $apiId = (string)($item->options->api_course_id ?? '');
                if (in_array($itemId, $courseIdsStr, true) || ($apiId && in_array($apiId, $courseIdsStr, true))) {
                    $applicableTotal += (float)($item->price * $item->qty);
                }
            }
        }

        if ($applicableTotal <= 0) {
            return 0.0;
        }

        $discountType = $coupon->discount_type ?? 'percentage';
        $discount = 0.0;

        if ($discountType === 'fixed') {
            $fixedVal = (float)($coupon->discount_amount ?: $coupon->offer_percentage ?: 0);
            $discount = min($fixedVal, $applicableTotal);
        } else {
            $percentage = (float)($coupon->offer_percentage ?: 0);
            $discount = ($applicableTotal * $percentage) / 100.0;

            if (!empty($coupon->max_discount_amount) && $coupon->max_discount_amount > 0) {
                $discount = min($discount, (float)$coupon->max_discount_amount);
            }
        }

        return max(0.0, round($discount, 2));
    }

    function applyCoupon(Request $request) {
        $rules = [
            'coupon' => 'required',
        ];
        $customMessages = [
            'coupon.required' => __('Coupon is required'),
        ];

        $request->validate($rules, $customMessages);

        $code = trim((string)$request->coupon);
        $coupon = Coupon::whereRaw('LOWER(coupon_code) = ?', [strtolower($code)])
            ->where('status', 'active')
            ->first();

        $isAjax = $request->ajax() || $request->wantsJson();

        if (!$coupon) {
            $msg = __('Invalid or inactive coupon code');
            return $isAjax ? response()->json(['message' => $msg], 404) : redirect()->back()->with(['messege' => $msg, 'alert-type' => 'error']);
        }

        // Validity checks
        if (!$coupon->hasStarted()) {
            $msg = __('This coupon is not yet active. Valid from :date', ['date' => date('d M Y', strtotime($coupon->start_date))]);
            return $isAjax ? response()->json(['message' => $msg], 403) : redirect()->back()->with(['messege' => $msg, 'alert-type' => 'error']);
        }

        if ($coupon->isExpired()) {
            $msg = __('Coupon already expired');
            return $isAjax ? response()->json(['message' => $msg], 403) : redirect()->back()->with(['messege' => $msg, 'alert-type' => 'error']);
        }

        if ($coupon->hasReachedTotalLimit()) {
            $msg = __('This coupon has reached its maximum total usage limit');
            return $isAjax ? response()->json(['message' => $msg], 403) : redirect()->back()->with(['messege' => $msg, 'alert-type' => 'error']);
        }

        $userId = auth()->id() ?? auth('web')->id();
        if ($userId && $coupon->hasReachedUserLimit($userId)) {
            $msg = __('You have already used this coupon the maximum allowed number of times');
            return $isAjax ? response()->json(['message' => $msg], 403) : redirect()->back()->with(['messege' => $msg, 'alert-type' => 'error']);
        }

        $cartTotal = $this->cartTotal();
        if ($cartTotal <= 0) {
            $msg = __('Cart amount should be greater than 0');
            return $isAjax ? response()->json(['message' => $msg], 403) : redirect()->back()->with(['messege' => $msg, 'alert-type' => 'error']);
        }

        $minPrice = (float)($coupon->min_price ?? 0);
        if ($minPrice > 0 && $cartTotal < $minPrice) {
            $msg = __('Minimum order amount should be :amount', ['amount' => currency($minPrice)]);
            return $isAjax ? response()->json(['message' => $msg], 403) : redirect()->back()->with(['messege' => $msg, 'alert-type' => 'error']);
        }

        // Course scope check
        $courseIds = is_array($coupon->course_ids) ? $coupon->course_ids : (json_decode($coupon->course_ids ?? '', true) ?: []);
        $cartItems = Cart::content();
        if (!empty($courseIds)) {
            $hasMatchingCourse = false;
            $courseIdsStr = array_map('strval', $courseIds);
            foreach ($cartItems as $item) {
                $itemId = (string)$item->id;
                $apiId = (string)($item->options->api_course_id ?? '');
                if (in_array($itemId, $courseIdsStr, true) || ($apiId && in_array($apiId, $courseIdsStr, true))) {
                    $hasMatchingCourse = true;
                    break;
                }
            }
            if (!$hasMatchingCourse) {
                $msg = __('This coupon is only valid for specific course(s) not currently in your cart');
                return $isAjax ? response()->json(['message' => $msg], 403) : redirect()->back()->with(['messege' => $msg, 'alert-type' => 'error']);
            }
        }

        $discountAmount = $this->calculateCouponDiscount($cartTotal, $coupon, $cartItems);
        if ($discountAmount <= 0) {
            $msg = __('This coupon could not provide a discount on the current cart');
            return $isAjax ? response()->json(['message' => $msg], 403) : redirect()->back()->with(['messege' => $msg, 'alert-type' => 'error']);
        }

        $total = max(0, $cartTotal - $discountAmount);

        Session::put('coupon_id', $coupon->id);
        Session::put('coupon_code', $coupon->coupon_code);
        Session::put('discount_type', $coupon->discount_type ?? 'percentage');
        Session::put('offer_percentage', (float)$coupon->offer_percentage);
        Session::put('coupon_discount_amount', $discountAmount);

        $notification = __('Coupon applied successfully!');

        if ($isAjax) {
            return response()->json([
                'success'         => true,
                'message'         => $notification,
                'coupon_code'     => $coupon->coupon_code,
                'discount_type'   => $coupon->discount_type ?? 'percentage',
                'offer_percentage'=> $coupon->offer_percentage,
                'discount_amount' => currency($discountAmount),
                'discount_raw'    => $discountAmount,
                'total'           => currency($total),
                'total_raw'       => $total,
            ]);
        }

        return redirect()->back()->with(['messege' => $notification, 'alert-type' => 'success']);
    }

    function updateCouponDiscountAmount() {
        if (!Session::has('coupon_code')) {
            return;
        }

        $coupon = Coupon::whereRaw('LOWER(coupon_code) = ?', [strtolower(Session::get('coupon_code'))])
            ->where('status', 'active')
            ->first();

        if (!$coupon || $coupon->isExpired() || ($coupon->min_price > 0 && $this->cartTotal() < $coupon->min_price)) {
            $this->destroyCouponSession();
            return;
        }

        $discountAmount = $this->calculateCouponDiscount($this->cartTotal(), $coupon, Cart::content());
        Session::put('coupon_discount_amount', $discountAmount);
    }

    function removeCoupon() {
        $this->destroyCouponSession();

        $notification = [
            'messege'    => __('Coupon removed successfully!'),
            'alert-type' => 'success',
        ];
        return redirect()->back()->with($notification);
    }

    function destroyCouponSession() {
        Session::forget('coupon_id');
        Session::forget('coupon_code');
        Session::forget('discount_type');
        Session::forget('offer_percentage');
        Session::forget('coupon_discount_amount');
    }
}
