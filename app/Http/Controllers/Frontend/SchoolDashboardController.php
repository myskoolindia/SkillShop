<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SchoolDashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $school = userAuth();

        if (!$school->hasPurchasedCourses()) {
            return view('frontend.pages.no-courses-purchased', [
                'user' => $school,
            ]);
        }

        return redirect()->route('roboquest.launch');
    }
}
