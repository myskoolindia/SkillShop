<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\RoboquestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * SchoolProvisionController
 * -------------------------
 * Authenticated REST API for CRRM automated school account provisioning upon payment realization.
 *
 * Authentication:
 *   - Bearer Token: Authorization: Bearer <CRRM_API_SECRET>
 *   - Or Custom Header: X-API-KEY: <CRRM_API_SECRET>
 *   - Or Payload param: api_secret (for environments where headers are stripped)
 */
class SchoolProvisionController extends Controller
{
    /**
     * Authenticate incoming request against configured CRRM_API_SECRET.
     */
    protected function authenticate(Request $request): bool
    {
        $configuredSecret = (string) config('services.crrm.api_secret', env('CRRM_API_SECRET', 'sk_live_myskool_skillvation_2026_sec'));
        if (empty($configuredSecret)) {
            Log::error('[SchoolProvision] CRRM_API_SECRET is not configured in services/env.');
            return false;
        }

        // 1. Check Bearer Token
        $bearer = $request->bearerToken();
        if ($bearer && hash_equals($configuredSecret, $bearer)) {
            return true;
        }

        // 2. Check X-API-KEY header
        $headerKey = $request->header('X-API-KEY') ?? $request->header('x-api-key');
        if ($headerKey && hash_equals($configuredSecret, (string) $headerKey)) {
            return true;
        }

        // 3. Fallback to body param if proxies strip custom headers
        $bodySecret = $request->input('api_secret');
        if ($bodySecret && hash_equals($configuredSecret, (string) $bodySecret)) {
            return true;
        }

        return false;
    }

    /**
     * Clean and normalize phone numbers into a consistent format.
     */
    protected function normalizePhone(?string $phone): string
    {
        $clean = preg_replace('/[^0-9]/', '', (string) $phone);

        // Strip leading 0
        if (str_starts_with($clean, '0') && strlen($clean) === 11) {
            $clean = substr($clean, 1);
        }

        // Strip leading 91 for Indian 10-digit mobile
        if (str_starts_with($clean, '91') && strlen($clean) === 12) {
            $clean = substr($clean, 2);
        }

        return $clean;
    }

    /**
     * Provision or verify a School Account.
     *
     * POST /api/school/provision
     * POST /api/school/signup
     */
    public function provision(Request $request): JsonResponse
    {
        if (!$this->authenticate($request)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized: Invalid or missing API authentication credentials.',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'school_name'         => 'required|string|max:255',
            'email'               => 'required|email|max:255',
            'phone'               => 'required|string|max:30',
            'contact_person'      => 'nullable|string|max:255',
            'name'                => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:255',
            'address'             => 'nullable|string|max:500',
            'city'                => 'nullable|string|max:100',
            'state'               => 'nullable|string|max:100',
            'password'            => 'nullable|string|min:6|max:64',
            'invoice_no'          => 'nullable|string|max:100',
            'receipt_no'          => 'nullable|string|max:100',
            'amount_paid'         => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $email         = strtolower(trim($request->input('email')));
        $cleanPhone    = $this->normalizePhone($request->input('phone'));
        $schoolName    = trim($request->input('school_name'));
        $contactPerson = trim($request->input('contact_person') ?: ($request->input('name') ?: 'School Admin'));
        $userName      = trim($request->input('name') ?: $contactPerson);
        $regNumber     = $request->input('registration_number');
        $address       = $request->input('address');
        $city          = $request->input('city');
        $state         = $request->input('state');

        // Check if user already exists by email or phone
        $user = User::where('email', $email)
            ->orWhere('phone', $cleanPhone)
            ->first();

        $appBaseUrl = rtrim(config('app.url', 'https://devpro.skillvation.com'), '/');
        $loginUrl   = $appBaseUrl . '/login';
        $dashUrl    = $appBaseUrl . '/school/dashboard';

        if ($user) {
            // User exists: Update profile details if currently unset and ensure school privileges
            $updated = false;

            if ($user->role !== 'school') {
                $user->role = 'school';
                $updated = true;
            }

            if (empty($user->school_name) && !empty($schoolName)) {
                $user->school_name = $schoolName;
                $updated = true;
            }

            if (empty($user->contact_person) && !empty($contactPerson)) {
                $user->contact_person = $contactPerson;
                $updated = true;
            }

            if (empty($user->phone) && !empty($cleanPhone)) {
                $user->phone = $cleanPhone;
                $updated = true;
            }

            if (empty($user->city) && !empty($city)) {
                $user->city = $city;
                $updated = true;
            }

            if (empty($user->state) && !empty($state)) {
                $user->state = $state;
                $updated = true;
            }

            if (empty($user->address) && !empty($address)) {
                $user->address = $address;
                $updated = true;
            }

            if (empty($user->registration_number) && !empty($regNumber)) {
                $user->registration_number = $regNumber;
                $updated = true;
            }

            if ($user->status !== 'active') {
                $user->status = 'active';
                $updated = true;
            }

            if ($user->is_banned !== 'no') {
                $user->is_banned = 'no';
                $updated = true;
            }

            if (!$user->email_verified_at) {
                $user->email_verified_at = Carbon::now();
                $updated = true;
            }

            if (!$user->phone_verified_at) {
                $user->phone_verified_at = Carbon::now();
                $updated = true;
            }

            if ($updated) {
                $user->save();
            }

            // Auto-provision or verify DigiLab RoboQuest connection
            try {
                RoboquestService::defaultConnection($user);
            } catch (\Throwable $e) {
                Log::warning('[SchoolProvision] Existing user RoboQuest default connection notice: ' . $e->getMessage());
            }

            Log::info("[SchoolProvision] Existing school account refreshed: ID={$user->id}, Email={$user->email}, Phone={$user->phone}");

            return response()->json([
                'status'        => 'success',
                'created_new'   => false,
                'message'       => 'School account already exists and is active.',
                'user'          => [
                    'id'             => $user->id,
                    'name'           => $user->name,
                    'school_name'    => $user->school_name,
                    'contact_person' => $user->contact_person,
                    'email'          => $user->email,
                    'phone'          => $user->phone,
                    'role'           => $user->role,
                    'status'         => $user->status,
                ],
                'login_url'     => $loginUrl,
                'dashboard_url' => $dashUrl,
            ], 200);
        }

        // New User Creation
        $plainPassword = $request->filled('password')
            ? (string) $request->input('password')
            : ('School@' . mt_rand(1000, 9999));

        $userData = [
            'role'                => 'school',
            'name'                => $userName,
            'school_name'         => $schoolName,
            'contact_person'      => $contactPerson,
            'email'               => $email,
            'phone'               => $cleanPhone,
            'phone_verified_at'   => Carbon::now(),
            'email_verified_at'   => Carbon::now(),
            'status'              => 'active',
            'is_banned'           => 'no',
            'password'            => Hash::make($plainPassword),
            'registration_number' => $regNumber,
            'address'             => $address,
            'city'                => $city,
            'state'               => $state,
            'country_id'          => 99, // India default
        ];

        $newUser = User::create($userData);

        // Auto-provision DigiLab RoboQuest connection
        try {
            RoboquestService::defaultConnection($newUser);
        } catch (\Throwable $e) {
            Log::warning('[SchoolProvision] New school RoboQuest provisioning notice: ' . $e->getMessage());
        }

        Log::info("[SchoolProvision] New school account provisioned: ID={$newUser->id}, Email={$newUser->email}, School={$newUser->school_name}");

        return response()->json([
            'status'             => 'success',
            'created_new'        => true,
            'message'            => 'School account provisioned successfully.',
            'user'               => [
                'id'             => $newUser->id,
                'name'           => $newUser->name,
                'school_name'    => $newUser->school_name,
                'contact_person' => $newUser->contact_person,
                'email'          => $newUser->email,
                'phone'          => $newUser->phone,
                'role'           => $newUser->role,
                'status'         => $newUser->status,
            ],
            'temporary_password' => $plainPassword,
            'login_url'          => $loginUrl,
            'dashboard_url'      => $dashUrl,
        ], 201);
    }

    /**
     * Check if a school account exists by email or phone without creating.
     *
     * GET/POST /api/school/check
     */
    public function check(Request $request): JsonResponse
    {
        if (!$this->authenticate($request)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized: Invalid or missing API credentials.',
            ], 401);
        }

        $email = $request->input('email') ? strtolower(trim($request->input('email'))) : null;
        $phone = $request->input('phone') ? $this->normalizePhone($request->input('phone')) : null;

        if (!$email && !$phone) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Provide email or phone to check.',
            ], 422);
        }

        $query = User::query();
        if ($email && $phone) {
            $query->where(function ($q) use ($email, $phone) {
                $q->where('email', $email)->orWhere('phone', $phone);
            });
        } elseif ($email) {
            $query->where('email', $email);
        } else {
            $query->where('phone', $phone);
        }

        $user = $query->first();

        if ($user) {
            return response()->json([
                'status' => 'success',
                'exists' => true,
                'user'   => [
                    'id'          => $user->id,
                    'name'        => $user->name,
                    'school_name' => $user->school_name,
                    'email'       => $user->email,
                    'phone'       => $user->phone,
                    'role'        => $user->role,
                    'status'      => $user->status,
                ],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'exists' => false,
            'message' => 'No school account found matching the criteria.',
        ], 200);
    }
}
