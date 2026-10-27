<?php

use App\Http\Controllers\Api\PurchaseApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Post-purchase APIs
|--------------------------------------------------------------------------
| Token-authenticated endpoints that expose what the purchase flow creates
| after a successful payment: orders, order items, and course enrollments.
| Obtain a token via POST /api/login, then send it as: Authorization: Bearer <token>
*/
Route::post('/login', [PurchaseApiController::class, 'login']);

Route::get('/verticals', [PurchaseApiController::class, 'verticals']);

Route::middleware('auth:sanctum')->group(function () {
    // Course purchases (Skill2Skool / Upskill 4 Teacher / TTT courses)
    Route::get('/my-orders', [PurchaseApiController::class, 'myOrders']);
    Route::get('/orders/{invoice_id}', [PurchaseApiController::class, 'orderDetail']);
    Route::get('/my-enrollments', [PurchaseApiController::class, 'myEnrollments']);
    Route::get('/my-purchases/summary', [PurchaseApiController::class, 'summary']);

    // Unified + per-vertical post-purchase APIs (covers all six verticals)
    Route::get('/purchases', [PurchaseApiController::class, 'purchases']);       // ?vertical=<key>
    Route::get('/shop-orders', [PurchaseApiController::class, 'shopOrders']);     // club-shop physical
    Route::get('/lab-enquiries', [PurchaseApiController::class, 'labEnquiries']); // composite / skillvation lab
});

Route::get('/collab-courses', function (\Illuminate\Http\Request $request) {
    $type = $request->get('type');
    
    $query = \App\Models\Course::active()
        ->with(['category.translation', 'instructor:id,name']);

    if ($type !== null && $type !== '') {
        $types = array_map('trim', explode(',', (string)$type));
        $hasExactType = \App\Models\Course::whereIn('type', $types)->exists();
        if ($hasExactType) {
            $query->whereIn('type', $types);
        } else {
            $query->where(function($q) use ($types) {
                if (in_array('8', $types)) {
                    $q->orWhereIn('category_id', [47, 53, 54, 56, 57])
                      ->orWhere('title', 'like', '%TRAINER%')
                      ->orWhere('title', 'like', '%TEACHER%')
                      ->orWhere('title', 'like', '%NLP%')
                      ->orWhere('title', 'like', '%ECE%')
                      ->orWhere('title', 'like', '%MTT%')
                      ->orWhere('title', 'like', '%NTT%')
                      ->orWhere('title', 'like', '%Grade%');
                }
                if (in_array('10', $types) || in_array('5', $types)) {
                    $q->orWhereIn('category_id', [44, 46, 49, 50, 51, 52, 58, 59])
                      ->orWhere('title', 'like', '%TTT%')
                      ->orWhere('title', 'like', '%WORKSHOP%')
                      ->orWhere('title', 'like', '%SKILL%')
                      ->orWhere('title', 'like', '%DEVELOPMENT%')
                      ->orWhere('title', 'like', '%LITERACY%')
                      ->orWhere('title', 'like', '%WONDERKIDS%')
                      ->orWhere('title', 'like', '%YEP%')
                      ->orWhere('title', 'like', '%LDP%');
                }
            });
        }
    }

    $courses = $query->orderBy('id', 'desc')
        ->get()
        ->map(function ($course) use ($type) {
            $thumb = $course->thumbnail ? asset($course->thumbnail) : asset('designs/img/TTT-1.png');
            $catName = $course->category?->translation?->name ?? 'Skill Courses';
            $assignedType = is_numeric($course->type) ? (int)$course->type : ($type ? (int)$type : 8);
            $effectivePrice = $course->discount > 0 ? (float)$course->discount : (float)$course->price;
            $originalPrice = (float)$course->price;
            $hasDiscount = $course->discount > 0 && $course->discount < $course->price;

            return [
                'id' => $course->id,
                'course_id' => $course->id,
                'type' => $assignedType,
                'course_type' => $assignedType,
                'title' => $course->title,
                'slug' => $course->slug,
                'description' => strip_tags($course->description ?? ''),
                'short_description' => $course->short_description ?? '',
                'price' => $effectivePrice,
                'formatted_price' => $effectivePrice > 0 ? '₹ ' . number_format($effectivePrice, 0) : 'Free',
                'original_price' => $originalPrice,
                'formatted_original_price' => '₹ ' . number_format($originalPrice, 0),
                'discount' => (float) $course->discount,
                'has_discount' => $hasDiscount,
                'cover_image' => $thumb,
                'image' => $thumb,
                'thumbnail' => $thumb,
                'category' => $catName,
                'category_name' => $catName,
                'instructor_name' => $course->instructor?->name ?? 'Skillvation',
                'rating' => 5,
                'reviews_count' => 12,
            ];
        });

    return response()->json([
        'status' => 'success',
        'type' => $type,
        'data' => $courses,
        'courses' => [
            'data' => $courses
        ]
    ]);
});

Route::get('/nav-menu', function () {
    $nav_menu = \Illuminate\Support\Facades\Cache::rememberForever('nav_menu', function () {
        return menuGetBySlug('nav-menu');
    });

    return response()->json([
        'status' => 'success',
        'data' => $nav_menu
    ]);
});
