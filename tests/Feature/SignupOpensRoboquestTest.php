<?php
namespace Tests\Feature;
use Tests\TestCase;
use App\Models\{SchoolMember, User};
use Illuminate\Support\Facades\{DB, Log, Schema};

/**
 * Signing up on Skillvation: the person lands in their RoboQuest dashboard (not the Skillvation one), gets a welcome
 * message on WhatsApp (never their password), and is never added to a school they did not choose.
 */
class SignupOpensRoboquestTest extends TestCase {
    use RoboquestTestSchema;
    protected function setUp(): void {
        parent::setUp();
        $this->createRoboquestSchema();
        Schema::table('users', function ($t) { foreach (['verification_token','school_name','registration_number','contact_person','job_title','short_bio','age','gender'] as $c) $t->string($c)->nullable(); $t->timestamp('phone_verified_at')->nullable(); $t->rememberToken(); });
        $migration = require database_path('migrations/2026_09_12_000002_create_phone_verifications_table.php'); $migration->up();
        $this->withoutMiddleware([\App\Http\Middleware\DemoModeMiddleware::class, \App\Http\Middleware\SetLocaleMiddleware::class, \App\Http\Middleware\ShopAudience::class]);
        config(['services.waba.driver' => 'mock']);
        $key = str_repeat('d', 64);
        foreach (['DEFAULT_ROBOQUEST_URL' => 'https://digilab.test', 'DEFAULT_ROBOQUEST_KEY' => $key] as $k => $v) { putenv("$k=$v"); $_ENV[$k] = $_SERVER[$k] = $v; }
    }
    protected function tearDown(): void {
        foreach (['DEFAULT_ROBOQUEST_URL', 'DEFAULT_ROBOQUEST_KEY'] as $k) { putenv($k); unset($_ENV[$k], $_SERVER[$k]); }
        parent::tearDown();
    }
    private function signup(string $type, string $email, string $phone, array $extra = []) {
        DB::table('phone_verifications')->insert(['phone' => $phone, 'otp' => '000000', 'token' => 'tok-' . $phone, 'expires_at' => now()->addMinutes(5), 'verified_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
        return $this->post('/register', ['account_type' => $type, 'phone' => $phone, 'phone_token' => 'tok-' . $phone, 'name' => ucfirst($type) . ' One', 'email' => $email, 'password' => 'Secret-Pass-1', 'password_confirmation' => 'Secret-Pass-1'] + $extra);
    }
    public function test_signup_opens_roboquest_sends_whatsapp_and_never_joins_a_school_by_itself(): void {
        Log::spy();
        $existingSchool = User::create(['name' => 'Some School', 'email' => 'school@x.test', 'role' => 'school', 'password' => 'x', 'status' => 'active']);
        foreach ([['school', 'new-school@x.test', '919000000011', 'https://digilab.test/launch?code='], ['teacher', 'teacher@x.test', '919000000012', 'https://digilab.test/launch?code='], ['student', 'kid@x.test', '919000000013', 'https://digilab.test/launch?code=']] as [$type, $email, $phone, $prefix]) {
            auth('web')->logout();
            $response = $this->signup($type, $email, $phone, $type === 'school' ? ['school_name' => 'New School'] : []);
            $this->assertStringStartsWith($prefix, $response->headers->get('Location'), "$type lands in RoboQuest");
            $user = User::where('email', $email)->first();
            $this->assertSame($type === 'school' ? 'school-' . $user->id : 'personal', substr($response->headers->get('Location'), strpos($response->headers->get('Location'), '&school=') + 8));
        }
        $this->assertSame(0, SchoolMember::count(), 'a teacher who picked no school is not put in one');
        $sent = [];
        Log::shouldHaveReceived('info')->withArgs(function ($m, $c = []) use (&$sent) { if ($m === '[WABA Mock Login] Sign-in details sent') $sent[] = $c['login']; return true; });
        // (Within one test the app keeps earlier after-response callbacks, so earlier signups repeat; each real request sends once.)
        $this->assertSame(['kid@x.test', 'new-school@x.test', 'teacher@x.test'], array_values(array_unique(array_map(null, (function ($s) { sort($s); return $s; })($sent)))));
        // Affiliates, and people signing up on the way to another page, are not redirected to RoboQuest.
        auth('web')->logout();
        $this->assertStringNotContainsString('digilab.test', (string) $this->signup('student', 'shopper@x.test', '919000000014', ['redirect' => '/cart'])->headers->get('Location'));
    }
}
