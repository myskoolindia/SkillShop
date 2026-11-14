<?php
namespace App\Services;

use App\Models\{SchoolMember, User};
use Illuminate\Support\Facades\{DB, Hash};
use Illuminate\Support\Str;

/**
 * Adds teachers and students to a school, for both the single-add form and CSV import.
 *
 * Rules:
 * - A new account gets the role it is added as (teacher or student).
 * - An existing account can only be linked with its own role, and its password is never changed.
 * - A person can be active in only one school at a time. RoboQuest needs this to know which school to open.
 * - A password is at least 8 characters. When none is given, a random one is generated and shown once.
 */
class SchoolMemberProvisioner
{
    public const MIN_PASSWORD = 8;
    public const ROLES = ['teacher', 'student'];

    /**
     * @param array $fields Extra school_members columns (id_number, grade, section, academic_year, board).
     * @return array{added: bool, created: bool, error: ?string, user: ?User, password: ?string}
     *         created: a new account was made (otherwise an existing one was linked).
     *         password: set only when it was generated here; this is the only time it can be shown.
     */
    public function add(int $schoolId, string $role, string $name, string $email, ?string $password, array $fields = []): array
    {
        abort_unless(in_array($role, self::ROLES, true), 500, 'Unsupported school role.');
        $name = trim($name);
        $email = mb_strtolower(trim($email));
        $password = $password === null || trim($password) === '' ? null : $password;
        if ($name === '' || mb_strlen($name) > 255) return $this->fail(__('Enter a name of up to 255 characters.'));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 255) return $this->fail(__('Enter a valid email address.'));
        if ($password !== null && (mb_strlen($password) < self::MIN_PASSWORD || mb_strlen($password) > 128)) {
            return $this->fail(__('Passwords must be :min to 128 characters.', ['min' => self::MIN_PASSWORD]));
        }

        return DB::transaction(function () use ($schoolId, $role, $name, $email, $password, $fields) {
            // Lock the account so two schools cannot add the same person at the same moment.
            $user = User::whereRaw('LOWER(email) = ?', [$email])->lockForUpdate()->first();
            $generated = null;
            $created = !$user;
            if ($user) {
                if ($user->role !== $role) return $this->fail(__('This email belongs to an existing :role account and cannot be added as a :wanted.', ['role' => $user->role, 'wanted' => $role]));
                if ($user->status !== 'active' || in_array($user->is_banned, [1, '1', 'yes'], true)) return $this->fail(__('This account is inactive. Contact support to restore it.'));
                if (SchoolMember::where('school_id', $schoolId)->where('user_id', $user->id)->exists()) return $this->fail(__('This person is already a member of your school.'));
                if ($this->activeInAnotherSchool($user->id, $schoolId)) return $this->fail(__('This :role is already active in another school.', ['role' => $role]));
            } else {
                $generated = $password === null ? $this->generatePassword() : null;
                $user = User::create([
                    'name' => $name, 'email' => $email, 'role' => $role, 'password' => Hash::make($password ?? $generated),
                    'status' => 'active', 'is_banned' => 'no', 'email_verified_at' => now(), 'verification_token' => null,
                ]);
            }
            SchoolMember::create(array_merge(array_map(fn($value) => is_string($value) && trim($value) === '' ? null : $value, $fields), [
                'school_id' => $schoolId, 'user_id' => $user->id, 'role_in_school' => $role, 'status' => 'active',
            ]));
            return ['added' => true, 'created' => $created, 'error' => null, 'user' => $user, 'password' => $generated];
        });
    }

    /** Re-activating a member is allowed only while they are not active in another school. */
    public function canActivate(SchoolMember $member): bool
    {
        return !$this->activeInAnotherSchool($member->user_id, $member->school_id);
    }

    public function activeInAnotherSchool(int $userId, int $schoolId): bool
    {
        return SchoolMember::where('user_id', $userId)->where('school_id', '!=', $schoolId)->where('status', 'active')->exists();
    }

    public function generatePassword(): string
    {
        // 12 letters and digits (no symbols), so it is easy to read out or type.
        return Str::password(12, true, true, false);
    }

    private function fail(string $error): array
    {
        return ['added' => false, 'created' => false, 'error' => $error, 'user' => null, 'password' => null];
    }
}
