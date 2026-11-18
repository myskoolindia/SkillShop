<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ShopAudience
{
    /**
     * Handle incoming shop requests.
     * Viewing products in the shop is public.
     * Only internal lab design studio routes require staff/school authentication.
     */
    public function handle(Request $request, Closure $next)
    {
        $path = strtolower(trim($request->path(), '/'));

        // Staff-only routes (e.g. internal lab design studio)
        $isStaffRoute = preg_match('~^(lab-design(/|$))~', $path);
        if (!$isStaffRoute) {
            return $next($request);
        }

        $user = $request->user();
        if (!$user) {
            if ($request->expectsJson() || str_starts_with($path, 'api/')) {
                return response()->json(['message' => 'Sign in with an authorized school or teacher account.'], 401);
            }
            return redirect()->guest(route('login', ['redirect' => $request->fullUrl()]));
        }

        $isTeacherMember = \App\Models\SchoolMember::where('user_id', $user->id)
            ->where('role_in_school', 'teacher')
            ->where('status', 'active')
            ->exists();
        $isStaff = in_array($user->role, ['school', 'teacher', 'instructor', 'admin']) || $isTeacherMember;

        abort_unless($isStaff, 403, 'Lab design is for schools and teachers only.');

        return $next($request);
    }
}

