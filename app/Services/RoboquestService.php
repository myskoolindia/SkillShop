<?php
namespace App\Services;
use App\Models\{Course, RoboquestConnection, SchoolMember, User};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Order\app\Models\Enrollment;

class RoboquestService {
    public function __construct(private ProductCourseMapService $productCourses) {}
    public function connection(User $user): ?RoboquestConnection {
        $defaultUrl = env('DEFAULT_ROBOQUEST_URL');
        $defaultKey = env('DEFAULT_ROBOQUEST_KEY');

        if ($user->role === 'school' || $user->role === 'admin') {
            $connection = RoboquestConnection::where('school_user_id', $user->id)->first();
            if (!$connection) {
                // Every school gets its own connection on the shared DigiLab server (DEFAULT_ROBOQUEST_URL),
                // which hosts each school separately. Never borrow another school's connection.
                $connection = self::defaultConnection($user);
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
                // Not (or no longer) in a school: open their personal space, which shows only what they bought themselves.
                // Nobody is ever added to a school automatically.
                return self::personalConnection();
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
    // Creates the school's own connection to the shared DigiLab server, when one is configured.
    public static function defaultConnection(User $school): ?RoboquestConnection {
        $defaultUrl = env('DEFAULT_ROBOQUEST_URL');
        $defaultKey = env('DEFAULT_ROBOQUEST_KEY');
        if (!$defaultUrl || !$defaultKey || strlen($defaultKey) < 32) return null;
        $schoolId = 'school-' . $school->id;
        // A school ID taken by a manual connection elsewhere is never shared.
        if (RoboquestConnection::where('school_id', $schoolId)->where('school_user_id', '<>', $school->id)->exists()) return null;
        return RoboquestConnection::firstOrCreate(
            ['school_user_id' => $school->id],
            ['school_id' => $schoolId, 'base_url' => rtrim($defaultUrl, '/'), 'shared_key' => $defaultKey]
        );
    }
    /** School ID of the personal space on the shared RoboQuest server (people who are not in a school). */
    public static function personalSchoolId(): string { return (string) env('PERSONAL_ROBOQUEST_SCHOOL_ID', 'personal'); }
    public static function isPersonal(RoboquestConnection $connection): bool { return $connection->school_id === self::personalSchoolId(); }
    /**
     * The personal space's connection. It belongs to a hidden owner account (inactive, cannot sign in); its school
     * purchases are always empty. Uses the shared server (DEFAULT_ROBOQUEST_URL/KEY), like new schools.
     */
    public static function personalConnection(): ?RoboquestConnection {
        $url = env('DEFAULT_ROBOQUEST_URL'); $key = env('DEFAULT_ROBOQUEST_KEY');
        if (!$url || !$key || strlen($key) < 32) return null;
        $existing = RoboquestConnection::where('school_id', self::personalSchoolId())->first();
        if ($existing) return $existing;
        $owner = User::withoutEvents(fn () => User::firstOrCreate(['email' => 'personal-space@roboquest.invalid'],
            ['name' => 'RoboQuest personal space', 'role' => 'school', 'status' => 'inactive', 'is_banned' => 'yes', 'password' => \Illuminate\Support\Facades\Hash::make(Str::random(64))]));
        return RoboquestConnection::firstOrCreate(['school_user_id' => $owner->id], ['school_id' => self::personalSchoolId(), 'base_url' => rtrim($url, '/'), 'shared_key' => $key]);
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
        // The school travels with the link so one DigiLab server can host every school. RoboQuest trusts it only
        // after this code is redeemed, and a code redeems only for the school it was issued to.
        return rtrim($connection->base_url, '/') . '/launch?code=' . rawurlencode($code) . '&school=' . rawurlencode($connection->school_id);
    }
    public function claims(User $user, RoboquestConnection $connection): array {
        abort_unless($user->status === 'active' && !in_array($user->is_banned, [1, '1', 'yes'], true), 403, 'Account is inactive.');
        $clip = fn($value, $max) => $value === null || $value === '' ? null : mb_substr(trim((string) $value), 0, $max);
        if (self::isPersonal($connection)) {
            abort_unless(in_array($user->role, ['teacher', 'student', 'instructor'], true), 403, 'The personal space is for teachers and students.');
            abort_if(SchoolMember::where('user_id', $user->id)->where('status', 'active')->exists(), 403, 'You belong to a school now. Open RoboQuest from your school.');
            $role = in_array($user->role, ['teacher', 'instructor']) ? 'Teacher' : 'Student';
            $profile = ['phone' => $clip($user->phone, 20)];
        } elseif ($user->id == $connection->school_user_id || in_array($user->role, ['school', 'admin'])) {
            $role = 'School Admin';
            // The school's signup details become the School Admin's profile in RoboQuest.
            $profile = ['phone' => $clip($user->phone, 20), 'schoolName' => $clip($user->school_name, 255), 'registrationNumber' => $clip($user->registration_number, 100),
                'contactPerson' => $clip($user->contact_person, 255), 'jobTitle' => $clip($user->job_title, 255)];
        } else {
            $member = SchoolMember::where('school_id', $connection->school_user_id)->where('user_id', $user->id)->where('status', 'active')->first();
            abort_unless($member && in_array($user->role, ['teacher', 'student', 'instructor']), 403, 'Active school membership required.');
            $role = in_array($user->role, ['teacher', 'instructor']) ? 'Teacher' : 'Student';
            // Membership details the school entered on Skillvation.
            $profile = ['phone' => $clip($user->phone, 20), 'idNumber' => $clip($member->id_number, 100), 'grade' => $clip($member->grade, 50),
                'section' => $clip($member->section, 20), 'academicYear' => $clip($member->academic_year, 20), 'board' => $clip($member->board, 50)];
        }
        return ['externalId'=>(string)$user->id, 'email'=>$user->email, 'displayName'=>$user->name, 'role'=>$role, 'profile'=>array_filter($profile, fn($v) => $v !== null)];
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
        $memberUsers = self::isPersonal($connection)
            // Personal space: everyone who has opened it and still has no school (joining a school ends their access here).
            ? User::whereIn('id', DB::table('roboquest_launch_codes')->where('connection_id', $connection->id)->whereNotNull('used_at')->select('user_id'))
                ->whereNotIn('id', SchoolMember::where('status', 'active')->select('user_id'))->get()
            : SchoolMember::where('school_id', $connection->school_user_id)->where('status', 'active')->with('user')->get()->map(fn($m) => $m->user)->filter();
        $members = $memberUsers
            ->filter(fn($u) => $u->status === 'active' && in_array($u->role, ['teacher','student']) && !in_array($u->is_banned, [1,'1','yes'], true))
            ->map(fn($u) => ['externalId'=>(string)$u->id,'email'=>$u->email,'displayName'=>$u->name,'role'=>$u->role === 'teacher' ? 'Teacher' : 'Student'])->values();
        // A person's own purchases get their own IDs, so they never replace the school's record of the same course.
        $suffix = (int)$buyerId === (int)$connection->school_user_id ? '' : '-u'.$buyerId;
        $plain = fn($text) => mb_substr(trim(strip_tags(preg_replace('/<\/(p|div|li|h[1-6])>|<br\s*\/?\s*>/i', "\n", (string)$text))), 0, 12000);
        $video = function($lesson) {
            $value=$lesson->video_url;
            if (!$value && in_array($lesson->storage,['youtube','vimeo','external_link'],true) && $lesson->file_type==='video') $value=$lesson->file_path;
            $url=parse_url((string)$value);
            return filter_var($value,FILTER_VALIDATE_URL) && ($url['scheme']??'')==='https' && !isset($url['user']) && !isset($url['pass']) ? $value : null;
        };
        $list = $courses->map(function($course) use ($plain,$video,$buyerId,$suffix) {
            return ['id'=>(string)$course->id.$suffix, 'externalCourseId'=>$course->api_course_id, 'buyerUserId'=>(string)$buyerId, 'title'=>$course->title,
                'summary'=>$plain($course->description), 'capacity'=>$course->capacity > 0 ? (int)$course->capacity : null,
                'chapters'=>$course->chapters->where('status', 'active')->sortBy('order')->values()->map(fn($chapter)=>[
                    'id'=>(string)$chapter->id,'title'=>$chapter->title,
                    'lessons'=>$chapter->chapterItems->filter(fn($item)=>$item->lesson)->map(fn($item)=>[
                        'id'=>(string)$item->lesson->id,'title'=>$item->lesson->title,'text'=>$plain($item->lesson->description),
                        'materials'=>$plain($item->lesson->material_required),
                        'videoUrl'=>$video($item->lesson),
                    ])->values(),
                ])];
        })->values()->all();
        // Courses included with this buyer's paid Club Shop lab products (Mapper API). A direct course purchase takes precedence.
        // Lesson content for these courses comes from the RoboQuest platform Content Studio.
        $owned = $courses->pluck('api_course_id')->filter()->map(fn($id) => (string)$id)->all();
        // RoboQuest stores a teacher's snapshot courses by ID next to the school's, so a teacher's copy gets its own ID
        // and never replaces the school's record of the same lab course.
        foreach ($this->labCourses([(int)$buyerId]) as $courseId => $mapped) {
            if (in_array((string)$courseId, $owned, true)) continue;
            $list[] = ['id'=>'lab-course-'.$courseId.$suffix, 'externalCourseId'=>$courseId, 'buyerUserId'=>(string)$buyerId, 'title'=>$mapped['title'],
                'summary'=>'Included with '.implode(', ', array_column($mapped['labProducts'], 'title')),
                'capacity'=>null, 'chapters'=>[], 'source'=>'lab-product', 'labProducts'=>$mapped['labProducts']];
        }
        $snapshot = ['schoolId'=>$connection->school_id, 'buyerId'=>(string)$buyerId, 'members'=>$members, 'courses'=>$list, 'updatedAt'=>now()->toIso8601String()];
        // Robot kits and Skill Lab sections from paid Club Shop products (Mapper API): the school's own purchases (for
        // everyone in the school) and, for a teacher or student, their personal purchases. Left out when the mapper or
        // Club Shop is unreachable, so RoboQuest keeps what it had instead of hiding paid content.
        try {
            $snapshot['unlocks'] = [
                'school' => self::isPersonal($connection) ? ['kits' => [], 'labs' => []] : $this->productCourses->unlocksForBuyers([(int) $connection->school_user_id]),
                'personal' => (int) $buyerId === (int) $connection->school_user_id ? ['kits' => [], 'labs' => []] : $this->productCourses->unlocksForBuyers([(int) $buyerId]),
            ];
        } catch (\Throwable $error) {
            report($error);
        }
        return $snapshot;
    }
    /**
     * Provider course ID => mapped course, for the paid Club Shop lab products of these portal users.
     * Fails closed (503) so a mapper or Club Shop outage never removes or grants access.
     */
    public function labCourses(array $portalUserIds): array {
        try { return $this->productCourses->coursesForBuyers($portalUserIds); }
        catch (\Throwable $error) {
            report($error);
            abort(503, 'Lab course access is temporarily unavailable. Existing access is unchanged; try again shortly.');
        }
    }
    /** Lab-mapped course IDs for the school and its active teachers, for the master's content check. */
    public function schoolLabCourseIds(RoboquestConnection $connection): array {
        return array_keys($this->labCourses($this->productCourses->schoolBuyerIds($connection)));
    }
}
