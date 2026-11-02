<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseGrade;
use App\Models\CourseChapterItem;
use App\Models\CourseProgress;
use App\Models\CourseReview;
use App\Models\QuizResult;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Modules\CertificateBuilder\app\Models\CertificateBuilder;
use Modules\CertificateBuilder\app\Models\CertificateBuilderItem;
use Modules\Order\app\Models\Enrollment;
use Modules\Order\app\Models\Order;

class StudentDashboardController extends Controller {
    public function index() {
        if (userAuth()->role === 'school') {
            return redirect()->route('school.dashboard');
        }
        if (userAuth()->role === 'instructor') {
            return redirect()->route('instructor.dashboard');
        }

        $teacherOrderIds = Order::where('buyer_id', userAuth()->id)->where('payment_status', 'paid')->where('status', 'completed')->pluck('id');
        $totalEnrolledCourses = Enrollment::where('user_id', userAuth()->id)->whereIn('order_id', $teacherOrderIds)->count();
        $totalQuizAttempts = QuizResult::where('user_id', userAuth()->id)->count();
        $totalReviews = CourseReview::where('user_id', userAuth()->id)->count();
        $orders = Order::where('buyer_id', userAuth()->id)->orderBy('id', 'desc')->take(10)->get();
        return view('frontend.student-dashboard.index', compact(
            'totalEnrolledCourses',
            'totalQuizAttempts',
            'totalReviews',
            'orders'
        ));
    }

    function enrolledCourses() {
        $teacherOrderIds = Order::where('buyer_id', userAuth()->id)->where('payment_status', 'paid')->where('status', 'completed')->pluck('id');
        $enrolls = Enrollment::with(['course' => function ($q) {
            $q->withTrashed()->with(['category.translation', 'instructor', 'reviews']);
        }])->where('user_id', userAuth()->id)
        ->whereIn('order_id', $teacherOrderIds)
        ->orderByDesc('id')->paginate(10);

        $enrolledCourseIds = Enrollment::where('user_id', userAuth()->id)
            ->whereIn('order_id', $teacherOrderIds)
            ->pluck('course_id')->filter()->toArray();

        // Cross-selling: Recommend related active courses not enrolled by the student
        $relatedCourses = Course::where(['is_approved' => 'approved', 'status' => 'active'])
            ->whereNotIn('id', $enrolledCourseIds)
            ->with(['category.translation', 'instructor', 'reviews'])
            ->withCount(['enrollments'])
            ->inRandomOrder()
            ->take(6)
            ->get();

        return view('frontend.student-dashboard.enrolled-courses.index', compact('enrolls', 'relatedCourses'));
    }

    function enrolledCoursesGrades(string $slug) {
        // 1. Get course by slug
        $course = Course::active()->withTrashed()->where('slug', $slug)->firstOrFail();

        // 2. Get distinct grade_ids from this course's chapters
        $gradeIds = \App\Models\CourseChapter::where('course_id', $course->id)
            ->whereNotNull('grade_id')
            ->pluck('grade_id')
            ->unique()
            ->values();

        // 3. Load only those grades (by ID) with images and chapters for this course
        $grades = CourseGrade::with([
            'images',
            'chapters' => function ($q) use ($course) {
                $q->where('course_id', $course->id)
                  ->where('status', 'active')
                  ->orderBy('order')
                  ->with([
                      'chapterItems',
                      'chapterItems.lesson',
                      'chapterItems.lesson.activityFiles',
                      'chapterItems.quiz',
                  ]);
            },
        ])
        ->whereIn('id', $gradeIds)
        ->where('status', 'active')
        ->orderBy('order')
        ->get()
        ->filter(fn ($g) => $g->chapters->isNotEmpty())
        ->values();

        // echo '<pre>';
    // print_r($course);
    // echo '<br>';
    
    // print_r($course);
    // exit();
        return view('frontend.student-dashboard.enrolled-courses-grades.index', compact('course', 'grades'));
    }

    function quizAttempts() {
        Session::forget('course_slug');
        $quizAttempts = QuizResult::with(['quiz'])->where('user_id', userAuth()->id)->orderByDesc('id')->paginate(10);

        return view('frontend.student-dashboard.quiz-attempts.index', compact('quizAttempts'));
    }

    function downloadCertificate(string $id) {
        $certificate = CertificateBuilder::first();
        $certificateItems = CertificateBuilderItem::all();
        $course = Course::withTrashed()->find($id);

        $courseLectureCount = CourseChapterItem::whereHas('chapter', function ($q) use ($course) {
            $q->where('course_id', $course->id);
        })->count();

        $courseLectureCompletedByUser = CourseProgress::where('user_id', userAuth()->id)
            ->where('course_id', $course->id)->where('watched', 1)->latest();

        $completed_date = formatDate($courseLectureCompletedByUser->first()?->created_at);

        $courseLectureCompletedByUser = CourseProgress::where('user_id', userAuth()->id)
            ->where('course_id', $course->id)->where('watched', 1)->count();

        $courseCompletedPercent = $courseLectureCount > 0 ? ($courseLectureCompletedByUser / $courseLectureCount) * 100 : 0;

        if ($courseCompletedPercent != 100) {
            return abort(404);
        }

        $html = view('frontend.student-dashboard.certificate.index', compact('certificateItems', 'certificate'))->render();

        $html = str_replace('[student_name]', userAuth()->name, $html);
        $html = str_replace('[platform_name]', Cache::get('setting')->app_name, $html);
        $html = str_replace('[course]', $course->title, $html);
        $html = str_replace('[date]', formatDate($completed_date), $html);
        $html = str_replace('[instructor_name]', $course->instructor->name, $html);

        // Initialize Dompdf
        $dompdf = new Dompdf(array('enable_remote' => true));

        // Load HTML content
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();
        $dompdf->stream("certificate.pdf");
        return redirect()->back();
    }
}
