<?php
namespace App\Services;
use App\Models\{Course, RoboquestConnection, SchoolMember, User};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Order\app\Models\Enrollment;

class RoboquestService {
    public function connection(User $user): ?RoboquestConnection {
        if ($user->role === 'school') return RoboquestConnection::where('school_user_id', $user->id)->first();
        if (!in_array($user->role, ['teacher', 'student'])) return null;
        $schools = SchoolMember::where('user_id', $user->id)->where('status', 'active')->pluck('school_id');
        $connections = RoboquestConnection::whereIn('school_user_id', $schools)->get();
        // Never guess a destination when a user belongs to more than one school.
        return $connections->count() === 1 ? $connections->first() : null;
    }
    public function launch(User $user): ?string {
        $connection = $this->connection($user);
        if (!$connection || $user->status !== 'active' || in_array($user->is_banned, [1, '1', 'yes'], true)) return null;
        $code = bin2hex(random_bytes(32));
        DB::table('roboquest_launch_codes')->where('expires_at', '<', now())->delete();
        DB::table('roboquest_launch_codes')->insert([
            'connection_id'=>$connection->id, 'user_id'=>$user->id, 'code_hash'=>hash('sha256', $code),
            'expires_at'=>now()->addSeconds(90), 'created_at'=>now(), 'updated_at'=>now(),
        ]);
        return rtrim($connection->base_url, '/') . '/launch?code=' . rawurlencode($code);
    }
    public function claims(User $user, RoboquestConnection $connection): array {
        abort_unless($user->status === 'active' && !in_array($user->is_banned, [1, '1', 'yes'], true), 403, 'Account is inactive.');
        if ($user->id == $connection->school_user_id) $role = 'School Admin';
        else {
            $member = SchoolMember::where('school_id', $connection->school_user_id)->where('user_id', $user->id)->where('status', 'active')->first();
            abort_unless($member && in_array($user->role, ['teacher', 'student']), 403, 'Active school membership required.');
            $role = $user->role === 'teacher' ? 'Teacher' : 'Student';
        }
        return ['externalId'=>(string)$user->id, 'email'=>$user->email, 'displayName'=>$user->name, 'role'=>$role];
    }
    public function snapshot(RoboquestConnection $connection): array {
        // A browser redirect never establishes an entitlement. Only paid, completed, active enrollments do.
        $ids = Enrollment::query()->where('user_id', $connection->school_user_id)->where('has_access', 1)
            ->whereIn('order_id', DB::table('orders')->where('buyer_id', $connection->school_user_id)->where('status', 'completed')->where('payment_status', 'paid')->select('id'))
            ->pluck('course_id')->unique();
        $courses = Course::whereIn('id', $ids)->with('chapters.chapterItems.lesson')->get();
        $members = SchoolMember::where('school_id', $connection->school_user_id)->where('status', 'active')->with('user')->get()
            ->filter(fn($m) => $m->user && $m->user->status === 'active' && in_array($m->user->role, ['teacher','student']) && !in_array($m->user->is_banned, [1,'1','yes'], true))
            ->map(fn($m) => ['externalId'=>(string)$m->user_id,'email'=>$m->user->email,'displayName'=>$m->user->name,'role'=>$m->user->role === 'teacher' ? 'Teacher' : 'Student'])->values();
        $plain = fn($text) => mb_substr(trim(strip_tags(preg_replace('/<\/(p|div|li|h[1-6])>|<br\s*\/?\s*>/i', "\n", (string)$text))), 0, 12000);
        $video = function($lesson) {
            $value=$lesson->video_url;
            if (!$value && in_array($lesson->storage,['youtube','vimeo','external_link'],true) && $lesson->file_type==='video') $value=$lesson->file_path;
            $url=parse_url((string)$value);
            return filter_var($value,FILTER_VALIDATE_URL) && ($url['scheme']??'')==='https' && !isset($url['user']) && !isset($url['pass']) ? $value : null;
        };
        return ['schoolId'=>$connection->school_id, 'members'=>$members, 'courses'=>$courses->map(function($course) use ($plain,$video) {
            return ['id'=>(string)$course->id, 'externalCourseId'=>$course->api_course_id, 'title'=>$course->title,
                'summary'=>$plain($course->description), 'capacity'=>$course->capacity > 0 ? (int)$course->capacity : null,
                'chapters'=>$course->chapters->where('status', 'active')->sortBy('order')->values()->map(fn($chapter)=>[
                    'id'=>(string)$chapter->id,'title'=>$chapter->title,
                    'lessons'=>$chapter->chapterItems->filter(fn($item)=>$item->lesson)->map(fn($item)=>[
                        'id'=>(string)$item->lesson->id,'title'=>$item->lesson->title,'text'=>$plain($item->lesson->description),
                        'materials'=>$plain($item->lesson->material_required),
                        'videoUrl'=>$video($item->lesson),
                    ])->values(),
                ])];
        })->values(), 'updatedAt'=>now()->toIso8601String()];
    }
}
