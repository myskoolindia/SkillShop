<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class SchoolDashboardController extends Controller
{
    public function index(): RedirectResponse
    {
        // All school sign-in methods and dashboard links use the same secure handoff.
        return redirect()->route('roboquest.launch');
    }
}
