<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SchoolMember;
use App\Models\SchoolCourseAssignment;
use Modules\Order\app\Models\Order;
use Illuminate\View\View;

class SchoolDashboardController extends Controller
{
    public function index(): View
    {
        $school = userAuth();

        $totalTeachers = SchoolMember::where('school_id', $school->id)->teachers()->active()->count();
        $totalStudents = SchoolMember::where('school_id', $school->id)->students()->active()->count();
        $totalAssignments = SchoolCourseAssignment::where('school_id', $school->id)->active()->count();
        $orders = Order::where('buyer_id', $school->id)->orderBy('id', 'desc')->take(10)->get();

        return view('frontend.school-dashboard.index', compact(
            'totalTeachers',
            'totalStudents',
            'totalAssignments',
            'orders'
        ));
    }
}
