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
        $discountPercent = Session::has('offer_percentage') ? Session::get('offer_percentage') : 0;
        $discountAmount = ($cartTotal * $discountPercent) / 100;
        $total = currency($cartTotal - $discountAmount);
        $coupon = Session::has('coupon_code') ? Session::get('coupon_code') : '';
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
                        $localU4s = file_exists(public_path("uploads/courses/u4s/{$api_course_id}.png")) ? "uploads/courses/u4s/{$api_course_id}.png" : null;
                        $thumb = $localU4s ?: ($coverFile ? "{$apiBase}/assets/images/event/cover/{$coverFile}" : 'designs/img/TTT-1.png');
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

    function applyCoupon(Request $request) {
        $rules = [
            'coupon' => 'required',
        ];
        $customMessages = [
            'coupon.required' => __('Coupon is required'),
        ];

        $request->validate($rules, $customMessages);

        $coupon = Coupon::where(['coupon_code' => $request->coupon, 'status' => 'active'])->first();

        if (!$coupon) {
            $notification = __('Invalid coupon');

            return response()->json(['message' => $notification], 403);
        }

        if ($coupon->expired_date < date('Y-m-d')) {
            $notification = __('Coupon already expired');

            return response()->json(['message' => $notification], 403);
        }

        if ($this->cartTotal() < $coupon->min_price) {
            $notification = __('Minimum order amount should be :amount', ['amount' => currency($coupon->min_price)]);

            return response()->json(['message' => $notification], 403);
        }
        if ($this->cartTotal() <= 0) {
            $notification = __('Cart amount should be greater than 0');

            return response()->json(['message' => $notification], 403);
        }

        $discountAmount = currency(($this->cartTotal() * $coupon->offer_percentage) / 100);
        $total = currency($this->cartTotal() - ($this->cartTotal() * $coupon->offer_percentage) / 100);

        /** when coupon will be handle for particular seller or author , above condition will be used  */
        Session::put('coupon_code', $coupon->coupon_code);
        Session::put('offer_percentage', $coupon->offer_percentage);
        Session::put('coupon_discount_amount', ($this->cartTotal() * $coupon->offer_percentage) / 100);

        $notification = __('Coupon applied successful');

        return response()->json(['message' => $notification, 'coupon_code' => $coupon->coupon_code, 'offer_percentage' => $coupon->offer_percentage, 'discount_amount' => $discountAmount, 'total' => $total]);
    }

    function updateCouponDiscountAmount() {
        if (!Session::has('coupon_code')) {
            return;
        }

        $coupon = Coupon::where(['coupon_code' => Session::get('coupon_code'), 'status' => 'active'])->first();
        // update discount amount
        Session::put('coupon_discount_amount', ($this->cartTotal() * $coupon->offer_percentage) / 100);
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
        Session::forget('coupon_code');
        Session::forget('offer_percentage');
        Session::forget('coupon_discount_amount');
    }
}
