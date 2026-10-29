<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class ShopAudience {
    public function handle(Request $request, Closure $next) {
        $path = strtolower(trim($request->path(), '/'));
        $student = in_array($path,['shop','skillbox']) || preg_match('~^(productdetails|product-details|api/shop-products|api/shop-categories|api/product-details|api/shop-orders)(/|$)~',$path);
        $staff = preg_match('~^(lab-design(/|$))~', $path);
        if (!$student && !$staff) return $next($request);
        $user = $request->user();
        if (!$user) {
            if ($request->expectsJson() || str_starts_with($path,'api/')) return response()->json(['message'=>'Sign in with the appropriate school, teacher or student account.'],401);
            return redirect()->guest(route('login',['redirect'=>$request->fullUrl()]));
        }
        $isTeacherMember = \App\Models\SchoolMember::where('user_id', $user->id)->where('role_in_school', 'teacher')->where('status', 'active')->exists();
        $isStaff = in_array($user->role, ['school', 'teacher', 'instructor', 'admin']) || $isTeacherMember;
        abort_unless($student ? $user->role === 'student' : $isStaff, 403, $student ? 'The student shop is for students only.' : 'Lab shopping is for schools and teachers only.');
        return $next($request);
    }
}
