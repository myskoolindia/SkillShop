<?php
namespace App\Console\Commands;

use App\Models\{SchoolMember, User};
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Finds school membership records created before the teacher/student rules were enforced:
 * 1. Teachers whose account role is not "teacher" (older versions created teachers as students),
 *    so they sign in to RoboQuest as Students.
 * 2. People active in more than one school, so RoboQuest cannot tell which school to open.
 *
 * Read-only by default. --fix-teacher-roles changes only accounts that are clearly affected by the old bug:
 * role "student", at least one teacher membership and no student membership anywhere.
 * Multi-school records are only reported: which school keeps the person is a decision for the schools.
 */
class SchoolMembersAudit extends Command
{
    protected $signature = 'school-members:audit {--fix-teacher-roles : Set role "teacher" on accounts that only have teacher memberships} {--force : Apply the fix without asking for confirmation}';
    protected $description = 'Report (and optionally repair) teacher roles and people active in more than one school';

    public function handle(): int
    {
        $mismatched = User::whereIn('id', SchoolMember::where('role_in_school', 'teacher')->select('user_id'))
            ->where('role', '!=', 'teacher')->orderBy('id')->get(['id', 'email', 'role']);
        $studentMembers = SchoolMember::where('role_in_school', 'student')->pluck('user_id')->flip();
        $fixable = $mismatched->filter(fn($user) => $user->role === 'student' && !$studentMembers->has($user->id));

        $this->info('Teacher memberships whose account role is not "teacher": ' . $mismatched->count());
        if ($mismatched->isNotEmpty()) {
            $this->table(['User ID', 'Email', 'Account role', 'Safe to fix'], $mismatched->map(fn($user) => [$user->id, $user->email, $user->role, $fixable->contains('id', $user->id) ? 'yes' : 'no - check manually'])->all());
        }

        $multi = SchoolMember::where('status', 'active')->select('user_id', DB::raw('COUNT(*) AS schools'))->groupBy('user_id')->havingRaw('COUNT(*) > 1')->get();
        $this->info('People active in more than one school: ' . $multi->count());
        if ($multi->isNotEmpty()) {
            $rows = SchoolMember::with('user:id,email')->whereIn('user_id', $multi->pluck('user_id'))->where('status', 'active')->orderBy('user_id')->get();
            $this->table(['User ID', 'Email', 'School ID', 'Role in school', 'Member since'], $rows->map(fn($m) => [$m->user_id, $m->user?->email, $m->school_id, $m->role_in_school, (string) $m->created_at])->all());
            $this->line('Ask the school that should no longer have each person to deactivate them on its Teachers or Students page.');
        }

        if ($this->option('fix-teacher-roles')) {
            if ($fixable->isEmpty()) { $this->info('No accounts to fix.'); return self::SUCCESS; }
            if (!$this->option('force') && !$this->confirm("Set role \"teacher\" on {$fixable->count()} account(s)?")) return self::FAILURE;
            $updated = User::whereIn('id', $fixable->pluck('id'))->where('role', 'student')->update(['role' => 'teacher', 'updated_at' => now()]);
            $this->info("Updated {$updated} account(s). They will sign in to RoboQuest as Teachers from their next launch.");
        }
        return self::SUCCESS;
    }
}
