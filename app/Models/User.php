<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\UserStatus;
use App\Models\JitsiSetting;
use Laravel\Sanctum\HasApiTokens;
use Modules\Order\app\Models\Order;
use Illuminate\Notifications\Notifiable;
use Modules\LiveChat\app\Models\Message;
use Modules\Location\app\Models\Country;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Modules\InstructorRequest\app\Models\InstructorRequest;

class User extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'role',
        'name',
        'email',
        'phone',
        'phone_verified_at',
        'password',
        'status',
        'is_banned',
        'verification_token',
        'forget_password_token',
        'school_name',
        'registration_number',
        'contact_person',
        'job_title',
        'bio',
        'short_bio',
        'gender',
        'age',
        'country_id',
        'state',
        'city',
        'board',
        'address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    public function messagesSent() {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function messagesReceived() {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function contactUsers() {
        return User::whereIn('id', $this->messagesSent()->pluck('receiver_id'))
            ->orWhereIn('id', $this->messagesReceived()->pluck('sender_id'))
            ->get();
    }

    public function contactUsersWithUnseenMessages() {
        $contactUsers = User::whereIn('id', $this->messagesSent()->pluck('receiver_id'))
            ->orWhereIn('id', $this->messagesReceived()->pluck('sender_id'))
            ->select('id', 'name', 'email', 'image')
            ->get();

        $contactUsersWithUnseenMessages = [];

        foreach ($contactUsers as $contactUser) {
            $unseenMessagesCount = Message::where('sender_id', $contactUser->id)
                ->where('receiver_id', $this->id)
                ->where('seen', 'no')
                ->count();

            $lastMessage = Message::where(function ($query) use ($contactUser) {
                $query->where('sender_id', $this->id)->where('receiver_id', $contactUser->id);
            })->orWhere(function ($query) use ($contactUser) {
                $query->where('sender_id', $contactUser->id)->where('receiver_id', $this->id);
            })->latest('created_at')->first();

            $contactUsersWithUnseenMessages[] = (object) [
                'id'           => $contactUser->id,
                'name'         => $contactUser->name,
                'email'        => $contactUser->email,
                'image'        => $contactUser->image,
                'new_message'  => $unseenMessagesCount,
                'last_message' => $lastMessage->created_at,
            ];
        }

        usort($contactUsersWithUnseenMessages, function ($a, $b) {
            return $b->last_message <=> $a->last_message;
        });

        return $contactUsersWithUnseenMessages;
    }

    public function scopeActive($query) {
        return $query->where('status', UserStatus::ACTIVE);
    }

    public function scopeInactive($query) {
        return $query->where('status', UserStatus::DEACTIVE);
    }

    public function scopeBanned($query) {
        return $query->where('is_banned', UserStatus::BANNED);
    }

    public function scopeUnbanned($query) {
        return $query->where('is_banned', UserStatus::UNBANNED);
    }

    public function socialite() {
        return $this->hasMany(SocialiteCredential::class, 'user_id');
    }

    function instructorInfo(): HasOne {
        return $this->hasOne(InstructorRequest::class, 'user_id', 'id');
    }

    public function courses() {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    function country(): BelongsTo {
        return $this->belongsTo(Country::class, 'country_id');
    }
    function orders(): HasMany {
        return $this->hasMany(Order::class, 'buyer_id', 'id');
    }
    function zoom_credential(): HasOne {
        return $this->hasOne(ZoomCredential::class, 'instructor_id', 'id');
    }
    function jitsi_credential(): HasOne {
        return $this->hasOne(JitsiSetting::class, 'instructor_id', 'id');
    }

    // ── Role helpers ────────────────────────────────────────────

    public function isSchool(): bool {
        return $this->role === 'school';
    }

    public function isTeacher(): bool {
        return $this->role === 'teacher';
    }

    public function isInstructor(): bool {
        return $this->role === 'instructor';
    }

    public function instructorStatus(): ?string {
        return $this->instructorInfo?->status;
    }

    public function isStudent(): bool {
        return $this->role === 'student';
    }

    // ── School relationships ────────────────────────────────────

    /**
     * All roster members of this school.
     */
    public function schoolMembers(): HasMany {
        return $this->hasMany(SchoolMember::class, 'school_id');
    }

    /**
     * Teachers belonging to this school.
     */
    public function schoolTeachers(): HasMany {
        return $this->hasMany(SchoolMember::class, 'school_id')->where('role_in_school', 'teacher');
    }

    /**
     * Students belonging to this school.
     */
    public function schoolStudents(): HasMany {
        return $this->hasMany(SchoolMember::class, 'school_id')->where('role_in_school', 'student');
    }

    /**
     * Course assignments made by this school.
     */
    public function schoolCourseAssignments(): HasMany {
        return $this->hasMany(SchoolCourseAssignment::class, 'school_id');
    }

    /**
     * Courses assigned to this user by a school.
     */
    public function assignedCourses(): HasMany {
        return $this->hasMany(SchoolCourseAssignment::class, 'user_id');
    }

    /**
     * Determine if this user has purchased or has active access to any courses.
     */
    public function hasPurchasedCourses(): bool {
        // Admin always has full access
        if ($this->role === 'admin') {
            return true;
        }

        // 1. Direct LMS paid enrollments with active access
        $hasPaidEnrollment = \Modules\Order\app\Models\Enrollment::where('user_id', $this->id)
            ->where('has_access', 1)
            ->whereIn('order_id', \Illuminate\Support\Facades\DB::table('orders')
                ->where('buyer_id', $this->id)
                ->where('status', 'completed')
                ->where('payment_status', 'paid')
                ->select('id'))
            ->exists();

        if ($hasPaidEnrollment) {
            return true;
        }

        // 2. For teachers/students: check active course assignments from school
        if (in_array($this->role, ['teacher', 'student'], true)) {
            $hasAssignment = \App\Models\SchoolCourseAssignment::where('user_id', $this->id)
                ->where('status', 'active')
                ->exists();
            if ($hasAssignment) {
                return true;
            }
        }

        // 3. Check paid Club Shop lab products: mapped courses, and also lab LMS categories, single LMS, kits and
        //    Skill Labs, which open RoboQuest content without any course.
        try {
            $mapService = app(\App\Services\ProductCourseMapService::class);
            if ($mapService->enabled()) {
                if (!empty($mapService->coursesForBuyers([$this->id]))) {
                    return true;
                }
                if (array_filter($mapService->unlocksForBuyers([$this->id]))) {
                    return true;
                }
            }
        } catch (\Throwable $e) {
            // Fail safe
        }

        // 4. For school: check if school has paid orders containing course items
        // 5. Teachers and students of a school that bought lab content (a category opens it for the whole school).
        if (in_array($this->role, ['teacher', 'student'], true)) {
            $schools = \App\Models\SchoolMember::where('user_id', $this->id)->active()->pluck('school_id')->all();
            try {
                $mapService = app(\App\Services\ProductCourseMapService::class);
                if ($schools && $mapService->enabled() && array_filter($mapService->unlocksForBuyers($schools))) {
                    return true;
                }
            } catch (\Throwable $e) {
                // Fail safe
            }
        }

        if ($this->role === 'school') {
            $hasCourseOrder = \Modules\Order\app\Models\OrderItem::whereIn('order_id', \Illuminate\Support\Facades\DB::table('orders')
                ->where('buyer_id', $this->id)
                ->where('status', 'completed')
                ->where('payment_status', 'paid')
                ->select('id'))
                ->whereNotNull('course_id')
                ->exists();
            if ($hasCourseOrder) {
                return true;
            }
        }

        return false;
    }

    /**
     * Boot the model.
     */
    protected static function boot() {
        parent::boot();

        static::deleting(function ($user) {
            // Delete related instructor request
            $user->instructorInfo()->delete();
        });
    }
}
