<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CourseEnquiry;
use Illuminate\Http\Request;

class CourseEnquiryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'max:255'],
            'phone'         => ['nullable', 'string', 'max:20'],
            'designation'   => ['nullable', 'string', 'max:255'],
            'school'        => ['nullable', 'string', 'max:255'],
            'city'          => ['nullable', 'string', 'max:255'],
            'address'       => ['nullable', 'string', 'max:1000'],
            'message'       => ['nullable', 'string', 'max:2000'],
            'course_id'     => ['nullable'],
            'api_course_id' => ['nullable'],
            'course_title'  => ['nullable', 'string', 'max:255'],
            'source'        => ['nullable', 'string', 'max:100'],
        ]);

        CourseEnquiry::create($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Enquiry submitted successfully.',
        ]);
    }
}
