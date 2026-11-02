<?php
namespace App\Services;
use App\Models\{Course, RoboquestConnection, SchoolMember, User};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Order\app\Models\Enrollment;

class RoboquestService {
    public function connection(User $user): ?RoboquestConnection {
        $defaultUrl = env('DEFAULT_ROBOQUEST_URL', 'http://localhost:8080');
        $defaultKey = env('DEFAULT_ROBOQUEST_KEY', '6fc6bcf57baf76cbc26e4e36743a91e6b08b9268e8aff9aceeca2680d2df891a');

        if ($user->role === 'school' || $user->role === 'admin') {
            $connection = RoboquestConnection::where('school_user_id', $user->id)->first();
            if (!$connection) {
                // Check if any existing connection exists or create one
                $connection = RoboquestConnection::first();
                if (!$connection) {
                    $schoolId = 'school-' . $user->id;
                    $connection = RoboquestConnection::create([
                        'school_user_id' => $user->id,
                        'school_id'      => $schoolId,
                        'base_url'       => rtrim($defaultUrl, '/'),
                        'shared_key'     => $defaultKey,
                    ]);
                }
            } else {
                // Ensure base_url and shared_key are properly set
                if (empty($connection->base_url) || ($connection->base_url === 'https://digilab.skillvation.com' && str_contains($defaultUrl, 'localhost'))) {
                    $connection->update([
                        'base_url'   => rtrim($defaultUrl, '/'),
                        'shared_key' => $defaultKey,
                    ]);
                }
            }
            return $connection;
        }

        if (in_array($user->role, ['teacher', 'student', 'instructor'])) {
            $schools = SchoolMember::where('user_id', $user->id)->where('status', 'active')->pluck('school_id');
            if ($schools->isEmpty()) {
                // Auto-associate unlinked teacher/student/instructor with primary active school
                $primarySchool = User::where('role', 'school')->where('status', 'active')->orderBy('id', 'asc')->first();
                if ($primarySchool) {
                    SchoolMember::firstOrCreate(
                        ['school_id' => $primarySchool->id, 'user_id' => $user->id],
                        ['role_in_school' => in_array($user->role, ['teacher', 'instructor']) ? 'teacher' : 'student', 'status' => 'active']
                    );
                    $schools = collect([$primarySchool->id]);
                }
            }

            // Ensure connected school has RoboquestConnection
            foreach ($schools as $sId) {
                $schoolUser = User::find($sId);
                if ($schoolUser) {
                    $this->connection($schoolUser);
                }
            }

            $connections = RoboquestConnection::whereIn('school_user_id', $schools)->get();
            return $connections->first();
        }

        return null;
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
        if ($user->id == $connection->school_user_id || in_array($user->role, ['school', 'admin'])) {
            $role = 'School Admin';
        } else {
            $member = SchoolMember::where('school_id', $connection->school_user_id)->where('user_id', $user->id)->where('status', 'active')->first();
            abort_unless($member && in_array($user->role, ['teacher', 'student', 'instructor']), 403, 'Active school membership required.');
            $role = in_array($user->role, ['teacher', 'instructor']) ? 'Teacher' : 'Student';
        }
        return ['externalId'=>(string)$user->id, 'email'=>$user->email, 'displayName'=>$user->name, 'role'=>$role];
    }
    public function snapshot(RoboquestConnection $connection, ?User $user = null): array {
        // School purchased order should not be visible for teacher; if teacher purchases, that will be visible for teacher.
        if ($user && in_array($user->role, ['teacher', 'student', 'instructor'])) {
            $buyerId = $user->id;
        } else {
            $buyerId = $connection->school_user_id;
        }

        $ids = Enrollment::query()->where('user_id', $buyerId)->where('has_access', 1)
            ->whereIn('order_id', DB::table('orders')->where('buyer_id', $buyerId)->where('status', 'completed')->where('payment_status', 'paid')->select('id'))
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
        return ['schoolId'=>$connection->school_id, 'buyerId'=>(string)$buyerId, 'members'=>$members, 'courses'=>$courses->map(function($course) use ($plain,$video,$buyerId) {
            return ['id'=>(string)$course->id, 'externalCourseId'=>$course->api_course_id, 'buyerUserId'=>(string)$buyerId, 'title'=>$course->title,
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
