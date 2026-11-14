<?php
namespace Tests\Feature;
use Tests\TestCase;
use App\Models\{User,SchoolMember};
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\{Hash,Schema};

/**
 * School teacher and student management:
 * - new accounts get the role they are added as, with a password of at least 8 characters (generated if blank, shown once);
 * - an existing account is linked only with its own role and keeps its password;
 * - a person is active in only one school at a time.
 */
class SchoolTeacherMembershipTest extends TestCase {
    use RoboquestTestSchema;
    protected function setUp(): void {
        parent::setUp();
        $this->createRoboquestSchema();
        Schema::table('users',fn($t)=>$t->string('verification_token')->nullable());
        Schema::table('school_members',function($t){foreach(['id_number','grade','section','academic_year','board'] as $c)$t->string($c)->nullable();});
        // Real routing (session, CSRF exemption in tests, model binding, auth, role); skip site-wide settings middleware.
        $this->withoutMiddleware([\App\Http\Middleware\DemoModeMiddleware::class,\App\Http\Middleware\SetLocaleMiddleware::class,\App\Http\Middleware\ShopAudience::class]);
    }
    private function user(string $email,string $role,string $password='Existing-Pass-2026'): User {
        return User::withoutEvents(fn()=>User::create(['name'=>ucfirst($role),'email'=>$email,'role'=>$role,'password'=>Hash::make($password),'status'=>'active','is_banned'=>'no','email_verified_at'=>now()]));
    }
    private function add(User $school,string $role,string $email,array $extra=[]) {
        return $this->actingAs($school)->post("/school/{$role}s",['name'=>'New '.$role,'email'=>$email]+$extra);
    }
    /** The notice panel shown at the top of the teacher/student list (the full page layout needs site settings tables). */
    private function notices(string $role): string { return view('frontend.school-dashboard.partials.member-notices',['role'=>$role])->render(); }
    private function csv(string $body): UploadedFile { return UploadedFile::fake()->createWithContent('members.csv',$body); }

    public function test_new_accounts_get_their_role_and_a_generated_password_shown_once(): void {
        $school=$this->user('school@a.test','school');
        foreach(['teacher','student'] as $role){
            $response=$this->add($school,$role,"New.{$role}@Example.test")->assertRedirect(route("school.{$role}s.index"));
            $login=session('new_member_login');$user=User::where('email',"new.{$role}@example.test")->first();
            $this->assertSame($role,$user->role,'role matches the page it was added from; email is stored lower-case');
            $this->assertSame(12,strlen($login['password']));$this->assertTrue(Hash::check($login['password'],$user->password));
            $this->assertStringNotContainsString($login['password'],(string)session('messege'),'password is not put in the disappearing toast');
            $this->assertStringContainsString($login['password'],$this->notices($role));
            // Flashed for one page view only: after the next request it is gone.
            $this->actingAs($school)->get(route("school.{$role}s.login-details"));
            $this->assertNull(session('new_member_login'));$this->assertStringNotContainsString($login['password'],$this->notices($role));
        }
    }
    public function test_short_passwords_are_rejected_and_chosen_passwords_are_kept(): void {
        $school=$this->user('school@a.test','school');
        $this->add($school,'teacher','short@t.test',['password'=>'1234567'])->assertSessionHasErrors('password');
        $this->assertNull(User::where('email','short@t.test')->first());
        $this->add($school,'teacher','chosen@t.test',['password'=>'Chosen-Pass-1']);
        $this->assertTrue(Hash::check('Chosen-Pass-1',User::where('email','chosen@t.test')->value('password')));
    }
    public function test_existing_accounts_link_only_with_their_own_role_and_keep_their_password(): void {
        $school=$this->user('school@a.test','school');$this->user('other-school@b.test','school');$this->user('teacher@t.test','teacher');$this->user('Kid@s.test','student');
        $this->add($school,'student','other-school@b.test')->assertSessionHas('messege','This email belongs to an existing school account and cannot be added as a student.');
        $this->add($school,'student','teacher@t.test')->assertSessionHas('alert-type','error');
        $this->assertSame(0,SchoolMember::count());
        $this->add($school,'student','kid@s.test',['password'=>'Should-Be-Ignored-1'])->assertSessionHas('messege','Student added. They already had an account, so their current password is unchanged.');
        $this->assertTrue(Hash::check('Existing-Pass-2026',User::where('email','Kid@s.test')->value('password')));$this->assertNull(session('new_member_login'));
        $this->add($school,'student','kid@s.test')->assertSessionHas('messege','This person is already a member of your school.');
    }
    public function test_a_person_is_active_in_only_one_school(): void {
        $a=$this->user('school@a.test','school');$b=$this->user('school@b.test','school');
        foreach(['teacher','student'] as $role){
            $this->add($a,$role,"shared-{$role}@x.test");
            $this->add($b,$role,"shared-{$role}@x.test")->assertSessionHas('messege',"This {$role} is already active in another school.");
            $user=User::where('email',"shared-{$role}@x.test")->first();
            $this->assertSame([$a->id],SchoolMember::where('user_id',$user->id)->pluck('school_id')->all());
            // After School A deactivates them, School B can add them, and School A can no longer re-activate.
            $old=SchoolMember::where('user_id',$user->id)->first();
            $this->actingAs($a)->patch(route("school.{$role}s.toggle-status",$old->id));$this->assertSame('inactive',$old->fresh()->status);
            $this->add($b,$role,"shared-{$role}@x.test")->assertRedirect(route("school.{$role}s.index"));
            $this->actingAs($a)->patch(route("school.{$role}s.toggle-status",$old->id))->assertSessionHas('messege',"This {$role} is already active in another school.");
            $this->assertSame('inactive',$old->fresh()->status);
            $this->assertSame(1,SchoolMember::where('user_id',$user->id)->where('status','active')->count());
        }
    }
    public function test_another_school_cannot_change_a_members_status(): void {
        $a=$this->user('school@a.test','school');$b=$this->user('school@b.test','school');$this->add($a,'teacher','t@x.test');$member=SchoolMember::first();
        $this->actingAs($b)->patch(route('school.teachers.toggle-status',$member->id))->assertForbidden();
        $this->assertSame('active',$member->fresh()->status);
    }
    public function test_csv_import_reports_each_skipped_row_and_offers_new_passwords_once(): void {
        $a=$this->user('school@a.test','school');$b=$this->user('school@b.test','school');$this->user('admin-like@x.test','school');
        $this->add($a,'teacher','taken@x.test');
        $csv="Name,Email,ID_Number,Password\n=HYPERLINK(\"x\"),fresh@x.test,T1,\nChosen,chosen@x.test,T2,Chosen-Pass-1\nTaken,taken@x.test,T3,\nWrong role,admin-like@x.test,T4,\nShort,short@x.test,T5,abc\nBad,not-an-email,T6,\n\n";
        $this->actingAs($b)->post(route('school.teachers.import'),['csv_file'=>$this->csv($csv)])
            ->assertSessionHas('messege','Import complete: 2 added, 4 skipped.')->assertSessionHas('alert-type','warning');
        $problems=collect(session('import_problems'));
        $this->assertSame([4,5,6,7],$problems->pluck('line')->all());
        $this->assertSame('This teacher is already active in another school.',$problems[0]['reason']);
        $page=$this->notices('teacher');$this->assertStringContainsString('1 new account(s) need their login details',$page);$this->assertStringContainsString('not-an-email',$page);
        $this->assertStringNotContainsString('need their login details',$this->notices('student'));
        // Student page does not offer the teacher list.
        $this->get(route('school.students.login-details'))->assertRedirect(route('school.students.index'));
        $download=$this->get(route('school.teachers.login-details'));$download->assertOk();
        $rows=array_map('str_getcsv',array_filter(explode("\n",$download->streamedContent())));
        $this->assertSame(['Name','Email','Password'],$rows[0]);$this->assertCount(2,$rows,'chosen passwords are not repeated back');
        $this->assertSame("'=HYPERLINK(\"x\")",$rows[1][0],'spreadsheet formulas are neutralised');$this->assertSame('fresh@x.test',$rows[1][1]);
        $this->assertTrue(Hash::check($rows[1][2],User::where('email','fresh@x.test')->value('password')));
        $this->get(route('school.teachers.login-details'))->assertRedirect(route('school.teachers.index'))->assertSessionHas('messege','These login details were already downloaded.');
    }
    public function test_student_csv_full_format_keeps_class_details(): void {
        $a=$this->user('school@a.test','school');
        $csv="Name,Email,Roll_Number,Grade,Section,Academic_Year,Board,Password\nAsha,asha@s.test,R1,Grade 6,A,2026-2027,CBSE,Student-Pass-1\n";
        $this->actingAs($a)->post(route('school.students.import'),['csv_file'=>$this->csv($csv)])->assertSessionHas('messege','Import complete: 1 added, 0 skipped.');
        $member=SchoolMember::first();
        $this->assertSame(['R1','Grade 6','A','2026-2027','CBSE','student'],[$member->id_number,$member->grade,$member->section,$member->academic_year,$member->board,$member->user->role]);
        $this->assertNull(session(\App\Http\Controllers\Frontend\Concerns\ManagesSchoolMembers::credentialsKey('student')),'no generated passwords, nothing to download');
    }
    public function test_audit_command_reports_old_records_and_fixes_only_clear_teacher_role_mistakes(): void {
        $a=$this->user('school@a.test','school');$b=$this->user('school@b.test','school');
        $bugTeacher=$this->user('bug@t.test','student');$both=$this->user('both@t.test','student');$multi=$this->user('multi@t.test','teacher');
        SchoolMember::create(['school_id'=>$a->id,'user_id'=>$bugTeacher->id,'role_in_school'=>'teacher','status'=>'active']);
        SchoolMember::create(['school_id'=>$a->id,'user_id'=>$both->id,'role_in_school'=>'teacher','status'=>'active']);
        SchoolMember::create(['school_id'=>$b->id,'user_id'=>$both->id,'role_in_school'=>'student','status'=>'inactive']);
        SchoolMember::create(['school_id'=>$a->id,'user_id'=>$multi->id,'role_in_school'=>'teacher','status'=>'active']);
        SchoolMember::create(['school_id'=>$b->id,'user_id'=>$multi->id,'role_in_school'=>'teacher','status'=>'active']);
        $this->artisan('school-members:audit')->expectsOutputToContain('Teacher memberships whose account role is not "teacher": 2')
            ->expectsOutputToContain('People active in more than one school: 1')->assertSuccessful();
        $this->assertSame('student',$bugTeacher->fresh()->role,'read-only without the fix option');
        $this->artisan('school-members:audit --fix-teacher-roles --force')->expectsOutputToContain('Updated 1 account(s).')->assertSuccessful();
        $this->assertSame('teacher',$bugTeacher->fresh()->role);
        $this->assertSame('student',$both->fresh()->role,'an account that is also a student somewhere is left for a person to check');
        $this->assertSame(2,SchoolMember::where('user_id',$multi->id)->where('status','active')->count(),'multi-school records are only reported');
    }
}
