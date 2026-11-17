<?php
namespace App\Http\Controllers\Frontend;

use App\Models\{SchoolMember, User};
use App\Services\WabaOtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Hash};

/**
 * Teachers and students created by a School Admin in RoboQuest get the same account on Skillvation: one login (email or
 * mobile number + password) for both. RoboQuest calls this school-signed, replay-protected endpoint:
 *
 *   action=create    member{name,email,phone,role,password,grade?,section?,idNumber?}  → {externalId, whatsapp}
 *   action=update    externalId, member{name?,phone?,grade?,section?,idNumber?}
 *   action=password  externalId, password                                          → {whatsapp}
 *   action=remove    externalId  (the school membership is ended; the account stays)
 *
 * With notify=true the sign-in details are sent by WhatsApp to the member's own number (one person per number).
 * Only members of the signing school can be changed, never School Admins.
 */
class RoboquestMembersController extends RoboquestController
{
    public function handle(Request $request, WabaOtpService $waba)
    {
        $connection = $this->authenticate($request);
        $school = User::findOrFail($connection->school_user_id);
        $input = $request->validate([
            'action' => 'required|in:create,update,password,remove',
            'externalId' => 'required_unless:action,create|nullable|string|max:20',
            'notify' => 'nullable|boolean',
            'loginUrl' => 'nullable|url|max:300',
            'password' => 'required_if:action,password|nullable|string|min:12|max:128',
            'member' => 'nullable|array',
            'member.name' => 'required_if:action,create|nullable|string|min:2|max:100',
            'member.email' => 'required_if:action,create|nullable|email|max:200',
            'member.phone' => 'required_if:action,create|nullable|string|max:20',
            'member.role' => 'required_if:action,create|nullable|in:teacher,student',
            'member.password' => 'required_if:action,create|nullable|string|min:12|max:128',
            'member.grade' => 'nullable|string|max:50',
            'member.section' => 'nullable|string|max:20',
            'member.idNumber' => 'nullable|string|max:100',
        ]);
        $member = $input['member'] ?? [];
        $phone = isset($member['phone']) && $member['phone'] !== '' ? $waba->sanitizePhone($member['phone']) : null;
        abort_if($phone !== null && !preg_match('/^\d{10,15}$/', $phone), 422, 'Invalid mobile number.');
        $phoneTaken = fn (?int $except) => $phone && User::where(fn ($q) => $q->where('phone', $phone)->orWhere('phone', 'like', '%' . substr($phone, -10)))->when($except, fn ($q) => $q->where('id', '<>', $except))->exists();
        $notify = function (User $user, string $password, bool $reset) use ($input, $waba, $school) {
            if (empty($input['notify']) || empty($user->phone)) return 'skipped';
            $result = $waba->sendLoginDetails($user->phone, ['name' => $user->name, 'login' => $user->email, 'password' => $password, 'url' => $input['loginUrl'] ?? url('/login'), 'school' => $school->school_name ?: $school->name, 'reset' => $reset]);
            return $result['success'] ? 'sent' : 'failed';
        };

        if ($input['action'] === 'create') {
            $email = mb_strtolower(trim($member['email']));
            abort_if(User::where('email', $email)->exists(), 409, 'An account with this email already exists on Skillvation.');
            abort_if($phoneTaken(null), 409, 'This mobile number is already used by another Skillvation account. Each person needs their own number.');
            $user = DB::transaction(function () use ($member, $email, $phone, $school) {
                $user = User::create(['name' => trim($member['name']), 'email' => $email, 'phone' => $phone, 'password' => Hash::make($member['password']), 'role' => $member['role'],
                    'status' => 'active', 'is_banned' => 'no']);
                $user->forceFill(['email_verified_at' => now(), 'phone_verified_at' => now()])->save();
                SchoolMember::create(['school_id' => $school->id, 'user_id' => $user->id, 'role_in_school' => $member['role'], 'status' => 'active']
                    + array_filter(['id_number' => $member['idNumber'] ?? null, 'grade' => $member['grade'] ?? null, 'section' => $member['section'] ?? null], fn ($v) => $v !== null && $v !== ''));
                return $user;
            });
            return response()->json(['externalId' => (string) $user->id, 'whatsapp' => $notify($user, $member['password'], false)], 201)->header('Cache-Control', 'no-store');
        }

        // The other actions change an existing member of this school only.
        $membership = SchoolMember::where('school_id', $school->id)->where('user_id', (int) $input['externalId'])->first();
        abort_unless($membership, 404, 'This person is not a member of the school on Skillvation.');
        $user = User::findOrFail($membership->user_id);
        abort_unless(in_array($user->role, ['teacher', 'student', 'instructor'], true), 403, 'Only teachers and students can be changed.');

        if ($input['action'] === 'update') {
            abort_if($phoneTaken($user->id), 409, 'This mobile number is already used by another Skillvation account. Each person needs their own number.');
            DB::transaction(function () use ($user, $membership, $member, $phone) {
                $user->forceFill(array_filter(['name' => isset($member['name']) ? trim($member['name']) : null, 'phone' => $phone], fn ($v) => $v !== null))->save();
                $fields = [];
                foreach (['idNumber' => 'id_number', 'grade' => 'grade', 'section' => 'section'] as $from => $to) if (array_key_exists($from, $member)) $fields[$to] = $member[$from] === '' ? null : $member[$from];
                if ($fields) $membership->forceFill($fields)->save();
            });
            return response()->json(['ok' => true])->header('Cache-Control', 'no-store');
        }
        if ($input['action'] === 'password') {
            $user->forceFill(['password' => Hash::make($input['password'])])->save();
            return response()->json(['ok' => true, 'whatsapp' => $notify($user, $input['password'], true)])->header('Cache-Control', 'no-store');
        }
        $membership->forceFill(['status' => 'inactive'])->save();
        return response()->json(['ok' => true])->header('Cache-Control', 'no-store');
    }
}
