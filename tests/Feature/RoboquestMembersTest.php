<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{User, RoboquestConnection, SchoolMember};
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema, Hash};

class RoboquestMembersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'members_testing', 'database.connections.members_testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true],
            'services.waba.driver' => 'mock']);
        DB::purge('members_testing');
        Schema::create('users', function (Blueprint $t) { $t->id(); $t->string('name'); $t->string('email')->unique(); $t->string('password'); $t->string('role'); $t->string('status')->default('active'); $t->string('is_banned')->default('no'); $t->string('phone')->nullable(); $t->timestamp('phone_verified_at')->nullable(); $t->string('school_name')->nullable(); $t->timestamp('email_verified_at')->nullable(); $t->timestamps(); });
        Schema::create('school_members', function (Blueprint $t) { $t->id(); $t->foreignId('school_id'); $t->foreignId('user_id'); $t->string('role_in_school'); $t->string('id_number')->nullable(); $t->string('grade')->nullable(); $t->string('section')->nullable(); $t->string('academic_year')->nullable(); $t->string('board')->nullable(); $t->string('status'); $t->timestamps(); });
        $migration = require database_path('migrations/2026_10_27_000001_create_roboquest_connections.php'); $migration->up();
    }

    private function school(string $email, string $schoolId): User
    {
        $school = User::withoutEvents(fn () => User::create(['name' => 'School', 'email' => $email, 'role' => 'school', 'password' => Hash::make('Test-Only-Password-2026!'), 'status' => 'active', 'is_banned' => 'no', 'school_name' => 'Sunrise Public School']));
        RoboquestConnection::create(['school_user_id' => $school->id, 'school_id' => $schoolId, 'base_url' => 'http://localhost:8080', 'shared_key' => str_repeat($schoolId === 'school-one' ? 'x' : 'y', 40)]);
        return $school;
    }

    private function send(array $body, string $schoolId = 'school-one', ?string $key = null)
    {
        $body += ['schoolId' => $schoolId, 'timestamp' => time(), 'nonce' => bin2hex(random_bytes(20))]; $raw = json_encode($body);
        return $this->call('POST', '/api/roboquest/members', [], [], [], ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json', 'HTTP_X_ROBOQUEST_SIGNATURE' => hash_hmac('sha256', $raw, $key ?? str_repeat($schoolId === 'school-one' ? 'x' : 'y', 40))], $raw);
    }

    private function member(array $extra = []): array
    {
        return ['action' => 'create', 'notify' => true, 'member' => $extra + ['name' => 'Asha Rao', 'email' => 'Asha@School.in', 'phone' => '98765 43210', 'role' => 'student', 'password' => 'abcd-efgh-2345', 'grade' => '6', 'section' => 'A', 'idNumber' => '17']];
    }

    public function test_a_member_created_in_roboquest_can_sign_in_on_skillvation_and_gets_whatsapp(): void
    {
        $school = $this->school('school@one.test', 'school-one');
        $created = $this->send($this->member())->assertCreated()->assertJsonPath('whatsapp', 'sent');
        $user = User::findOrFail((int) $created->json('externalId'));
        $this->assertSame(['asha@school.in', '919876543210', 'student'], [$user->email, $user->phone, $user->role]);
        $this->assertTrue(Hash::check('abcd-efgh-2345', $user->password), 'the same password works on Skillvation');
        $this->assertNotNull($user->phone_verified_at);
        $membership = SchoolMember::where('school_id', $school->id)->where('user_id', $user->id)->firstOrFail();
        $this->assertSame(['student', 'active', '6', 'A', '17'], [$membership->role_in_school, $membership->status, $membership->grade, $membership->section, $membership->id_number]);
        $this->send($this->member())->assertStatus(409);
        $this->send($this->member(['email' => 'other@school.in']))->assertStatus(409)->assertJsonFragment(['message' => 'This mobile number is already used by another Skillvation account. Each person needs their own number.']);
        $this->send(['action' => 'create', 'member' => ['name' => 'X', 'email' => 'bad']])->assertStatus(422);
    }

    public function test_updates_passwords_and_removal_only_touch_this_schools_teachers_and_students(): void
    {
        $school = $this->school('school@one.test', 'school-one'); $other = $this->school('school@two.test', 'school-two');
        $id = $this->send($this->member(['role' => 'teacher', 'email' => 'ravi@school.in', 'phone' => '9876500011']))->assertCreated()->json('externalId');
        $this->send(['action' => 'update', 'externalId' => $id, 'member' => ['name' => 'Ravi Kumar', 'phone' => '9876500022', 'grade' => '7']])->assertOk();
        $user = User::findOrFail((int) $id);
        $this->assertSame(['Ravi Kumar', '919876500022'], [$user->name, $user->phone]);
        $this->send(['action' => 'password', 'externalId' => $id, 'password' => 'new-pass-word-2026', 'notify' => true])->assertOk()->assertJsonPath('whatsapp', 'sent');
        $this->assertTrue(Hash::check('new-pass-word-2026', $user->fresh()->password));
        $this->send(['action' => 'update', 'externalId' => $id, 'member' => ['name' => 'Hijack']], 'school-two')->assertNotFound();
        $this->send(['action' => 'password', 'externalId' => (string) $school->id, 'password' => 'new-pass-word-2026'])->assertNotFound();
        $this->send(['action' => 'remove', 'externalId' => $id])->assertOk();
        $this->assertSame('inactive', SchoolMember::where('user_id', (int) $id)->value('status'));
        $this->send($this->member(), 'school-one', str_repeat('z', 40))->assertUnauthorized();
    }
}
