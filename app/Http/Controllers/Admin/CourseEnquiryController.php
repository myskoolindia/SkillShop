<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseEnquiry;
use Illuminate\Http\Request;

class CourseEnquiryController extends Controller
{
    public function index(Request $request)
    {
        $query = CourseEnquiry::query();

        if ($request->filled('keyword')) {
            $kw = trim($request->keyword);
            $query->where(function ($q) use ($kw) {
                $q->where('name',         'like', "%{$kw}%")
                  ->orWhere('phone',       'like', "%{$kw}%")
                  ->orWhere('email',       'like', "%{$kw}%")
                  ->orWhere('school',      'like', "%{$kw}%")
                  ->orWhere('city',        'like', "%{$kw}%")
                  ->orWhere('course_title','like', "%{$kw}%");
            });
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage    = (int) $request->get('par-page', 20);
        $enquiries  = $query->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        $sources    = CourseEnquiry::select('source')->distinct()->whereNotNull('source')->pluck('source');
        $title      = __('Course Enquiries');

        return view('admin.course-enquiries.index', compact('enquiries', 'sources', 'title'));
    }

    public function show($id)
    {
        $enquiry = CourseEnquiry::findOrFail($id);
        // Mark as read when first viewed
        if (!$enquiry->status || $enquiry->status === 'new') {
            $enquiry->update(['status' => 'read']);
        }
        $title = __('Enquiry Details');
        return view('admin.course-enquiries.show', compact('enquiry', 'title'));
    }

    public function updateStatus(Request $request, $id)
    {
        $enquiry = CourseEnquiry::findOrFail($id);
        $request->validate(['status' => ['required', 'in:new,read,contacted,closed']]);
        $enquiry->update(['status' => $request->status]);

        return response()->json(['success' => true, 'status' => $enquiry->status]);
    }

    public function destroy($id)
    {
        CourseEnquiry::findOrFail($id)->delete();

        $notification = ['messege' => __('Deleted successfully'), 'alert-type' => 'success'];
        return redirect()->route('admin.course-enquiries')->with($notification);
    }
}
