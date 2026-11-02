<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PhoneVerification;
use App\Models\RoboquestConnection;
use App\Models\SchoolMember;
use App\Models\User;
use App\Rules\CustomRecaptcha;
use App\Services\MailSenderService;
use App\Services\WabaOtpService;
use App\Traits\GetGlobalInformationTrait;
use Cache;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Str;

class RegisteredUserController extends Controller
{
    use GetGlobalInformationTrait;

    protected WabaOtpService $wabaService;

    public function __construct(WabaOtpService $wabaService)
    {
        $this->wabaService = $wabaService;
    }

    public function create(Request $request): View
    {
        if ($request->filled('redirect')) {
            session()->put('url.intended', $request->input('redirect'));
        }

        $intended = $request->input('redirect')
            ?: session()->get('url.intended')
            ?: $request->headers->get('referer')
            ?: '';

        $isLabContext = $request->input('scope') === 'labs'
            || in_array($request->input('role'), ['school', 'teacher'], true)
            || \Str::contains(strtolower($intended), ['/labs', 'composite-skill', 'skillvation-lab', 'lab-stem', 'stem', 'ecec', 'ai-robotics']);

        $schools = User::where('role', 'school')
            ->where('status', 'active')
            ->select('id', 'name', 'school_name')
            ->orderBy('name')
            ->get();

        return view('auth.register', compact('isLabContext', 'intended', 'schools'));
    }

    public function store(Request $request): RedirectResponse
    {
        $setting = Cache::get('setting');

        $request->validate([
            'account_type'        => ['required', 'in:student,teacher,school'],
            'phone'               => ['required', 'string', 'max:20'],
            'phone_token'         => ['required', 'string'],
            'name'                => ['required', 'string', 'max:255'],
            'email'               => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password'            => ['required', 'confirmed', 'min:4', 'max:100'],
            // School specific
            'school_name'         => ['required_if:account_type,school', 'nullable', 'string', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:100'],
            'contact_person'      => ['nullable', 'string', 'max:255'],
            // Teacher specific
            'job_title'           => ['nullable', 'string', 'max:255'],
            'short_bio'           => ['nullable', 'string', 'max:1000'],
            'school_id'           => ['nullable', 'exists:users,id'],
            // Student specific
            'age'                 => ['nullable', 'string', 'max:10'],
            'gender'              => ['nullable', 'in:male,female,other'],
            'g-recaptcha-response'=> ($setting && $setting->recaptcha_status == 'active') ? ['required', new CustomRecaptcha()] : 'nullable',
        ], [
            'name.required'           => __('Name is required'),
            'email.required'          => __('Email is required'),
            'email.unique'            => __('Email already exists'),
            'password.required'       => __('Password is required'),
            'password.confirmed'      => __('Confirm password does not match'),
            'password.min'            => __('You have to provide minimum 4 character password'),
            'school_name.required_if' => __('School name is required for school accounts'),
            'phone.required'          => __('WhatsApp mobile number is required'),
            'phone_token.required'    => __('Please complete WhatsApp OTP verification first'),
        ]);

        $cleanPhone = $this->wabaService->sanitizePhone($request->phone);

        // Verify phone_token in PhoneVerification table
        $verification = PhoneVerification::where('phone', $cleanPhone)
            ->where('token', $request->phone_token)
            ->whereNotNull('verified_at')
            ->first();

        if (!$verification) {
            throw ValidationException::withMessages([
                'phone' => __('Mobile number verification is invalid or expired. Please verify with WhatsApp OTP again.'),
            ]);
        }

        // Check if phone already registered by another user
        if (User::where('phone', $cleanPhone)->exists()) {
            throw ValidationException::withMessages([
                'phone' => __('This mobile number is already registered. Please log in.'),
            ]);
        }

        $role = $request->input('account_type', 'student');

        $userData = [
            'role'              => $role,
            'name'              => $request->name,
            'email'             => $request->email,
            'phone'             => $cleanPhone,
            'phone_verified_at' => Carbon::now(),
            'status'            => 'active',
            'is_banned'         => 'no',
            'password'          => Hash::make($request->password),
            'email_verified_at' => Carbon::now(), // Mark active upon verified WhatsApp OTP
            'verification_token'=> null,
        ];

        // Add role-specific fields
        if ($role === 'school') {
            $userData['school_name']         = $request->school_name;
            $userData['registration_number'] = $request->registration_number;
            $userData['contact_person']      = $request->contact_person;
        } elseif ($role === 'teacher') {
            $userData['job_title']           = $request->job_title;
            $userData['short_bio']           = $request->short_bio;
        } elseif ($role === 'student') {
            $userData['age']                 = $request->age;
            $userData['gender']              = $request->gender;
        }

        $user = User::create($userData);

        // Auto-provision DigiLab RoboQuest connection or membership
        try {
            if ($role === 'school') {
                $schoolId = 'school-' . $user->id;
                $defaultUrl = env('DEFAULT_ROBOQUEST_URL', 'http://localhost:8080');
                $defaultKey = env('DEFAULT_ROBOQUEST_KEY', '6fc6bcf57baf76cbc26e4e36743a91e6b08b9268e8aff9aceeca2680d2df891a');

                RoboquestConnection::firstOrCreate(
                    ['school_user_id' => $user->id],
                    [
                        'school_id'  => $schoolId,
                        'base_url'   => rtrim($defaultUrl, '/'),
                        'shared_key' => $defaultKey,
                    ]
                );
            } elseif ($role === 'teacher') {
                $targetSchoolId = $request->input('school_id');
                if (!$targetSchoolId) {
                    // Fall back to first active school if not explicitly selected
                    $firstSchool = User::where('role', 'school')->where('status', 'active')->first();
                    $targetSchoolId = $firstSchool?->id;
                }

                if ($targetSchoolId) {
                    SchoolMember::firstOrCreate(
                        ['school_id' => $targetSchoolId, 'user_id' => $user->id],
                        ['role_in_school' => 'teacher', 'status' => 'active']
                    );
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Auto RoboQuest provisioning warning: ' . $e->getMessage());
        }

        // Delete or invalidate used verification token
        $verification->delete();

        $settings = cache()->get('setting');
        $marketingSettings = cache()->get('marketing_setting');
        if ($user && $settings && $settings->google_tagmanager_status == 'active' && $marketingSettings?->register) {
            $register_user = [
                'name'  => $user->name,
                'email' => $user->email,
            ];
            session()->put('registerUser', $register_user);
        }

        // Log the user in directly
        Auth::guard('web')->login($user, true);

        $notification = __('Account created successfully! Welcome to Skillvation.');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        $intendedUrl = null;

        if ($request->filled('redirect')) {
            $intendedUrl = $request->input('redirect');
            session()->forget('url.intended');
        } elseif ($request->headers->get('referer')) {
            $referer = $request->headers->get('referer');
            $parsedRef = parse_url($referer);
            if (!empty($parsedRef['query'])) {
                parse_str($parsedRef['query'], $queryParams);
                if (!empty($queryParams['redirect'])) {
                    $intendedUrl = $queryParams['redirect'];
                    session()->forget('url.intended');
                }
            }
        }

        if (!$intendedUrl) {
            $intendedUrl = session()->pull('url.intended');
        }

        if ($intendedUrl) {
            $parsed = parse_url($intendedUrl);
            $host = $parsed['host'] ?? null;
            $currentHost = $request->getHost();
            $isSameHost = empty($host) || $host === $currentHost || in_array($host, ['localhost', '127.0.0.1'], true);
            $isSafePath = !\Str::contains($intendedUrl, ['/admin', '/login', '/register', '/user-login', '/user-register']);

            if ($isSameHost && $isSafePath) {
                return redirect()->to($intendedUrl)->with($notification);
            }
        }

        $defaultRoute = match ($user->role) {
            'school'     => route('school.dashboard'),
            'teacher'    => route('student.dashboard'),
            'instructor' => route('instructor.dashboard'),
            default      => route('student.dashboard'),
        };

        return redirect()->to($defaultRoute)->with($notification);
    }

    public function custom_user_verification($token)
    {
        $user = User::where('verification_token', $token)->first();
        if ($user) {
            if ($user->email_verified_at != null) {
                $notification = __('Email already verified');
                $notification = ['messege' => $notification, 'alert-type' => 'error'];

                return redirect()->route('login')->with($notification);
            }

            $user->email_verified_at = date('Y-m-d H:i:s');
            $user->verification_token = null;
            $user->save();

            $notification = __('Verification successful please try to login now');
            $notification = ['messege' => $notification, 'alert-type' => 'success'];
            return redirect()->route('login')->with($notification);
        } else {
            $notification = __('Invalid token');
            $notification = ['messege' => $notification, 'alert-type' => 'error'];

            return redirect()->route('register')->with($notification);
        }
    }
}
