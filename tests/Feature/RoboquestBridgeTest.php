<?php
namespace Tests\Feature;
use Tests\TestCase;
use App\Models\{User,Course,RoboquestConnection,SchoolMember};
use App\Services\RoboquestService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB,Schema,Hash};
use Modules\Order\app\Models\{Order,Enrollment};

class RoboquestBridgeTest extends TestCase {
    protected function setUp(): void {
        parent::setUp();
        config(['database.default'=>'roboquest_testing','database.connections.roboquest_testing'=>['driver'=>'sqlite','database'=>':memory:','prefix'=>'','foreign_key_constraints'=>true]]);
        DB::purge('roboquest_testing');
        Schema::create('users',function(Blueprint $t){$t->id();$t->string('name');$t->string('email')->unique();$t->string('password');$t->string('role');$t->string('status')->default('active');$t->string('is_banned')->default('no');$t->decimal('wallet_balance',12,2)->default(0);$t->string('phone')->nullable();$t->timestamp('email_verified_at')->nullable();$t->timestamps();});
        Schema::create('courses',function(Blueprint $t){$t->id();$t->string('title');$t->string('slug');$t->string('type');$t->string('status')->default('active');$t->integer('api_course_id')->nullable();$t->text('description')->nullable();$t->integer('capacity')->default(1);$t->foreignId('instructor_id')->nullable();$t->softDeletes();$t->timestamps();});
        Schema::create('orders',function(Blueprint $t){$t->id();$t->foreignId('buyer_id');$t->string('invoice_id');$t->string('status');$t->string('payment_status');$t->integer('has_coupon')->default(0);$t->string('coupon_code')->nullable();$t->decimal('coupon_discount_percent')->nullable();$t->decimal('coupon_discount_amount')->nullable();$t->string('payment_method')->nullable();$t->decimal('payable_amount')->nullable();$t->decimal('gateway_charge')->nullable();$t->decimal('payable_with_charge')->nullable();$t->decimal('paid_amount')->nullable();$t->string('payable_currency')->nullable();$t->decimal('conversion_rate')->nullable();$t->text('payment_details')->nullable();$t->string('transaction_id')->nullable();$t->decimal('commission_rate')->nullable();$t->timestamps();});
        Schema::create('order_items',function(Blueprint $t){$t->id();$t->foreignId('order_id');$t->foreignId('course_id');$t->decimal('price');$t->decimal('commission_rate');$t->timestamps();});
        Schema::create('enrollments',function(Blueprint $t){$t->id();$t->foreignId('order_id');$t->foreignId('user_id');$t->foreignId('course_id');$t->integer('has_access');$t->timestamps();});
        Schema::create('school_members',function(Blueprint $t){$t->id();$t->foreignId('school_id');$t->foreignId('user_id');$t->string('role_in_school');$t->string('status');$t->timestamps();});
        Schema::create('course_chapters',function(Blueprint $t){$t->id();$t->foreignId('course_id');$t->string('title');$t->string('status')->default('active');$t->integer('order')->default(0);});
        Schema::create('course_chapter_items',function(Blueprint $t){$t->id();$t->foreignId('chapter_id');$t->integer('order')->default(0);});
        Schema::create('course_chapter_lessons',function(Blueprint $t){$t->id();$t->foreignId('chapter_item_id');$t->string('title');$t->text('description')->nullable();$t->text('material_required')->nullable();$t->text('video_url')->nullable();});
        $migration=require database_path('migrations/2026_10_27_000001_create_roboquest_connections.php');$migration->up();
    }
    private function user(string $email,string $role='school'): User {
        return User::withoutEvents(fn()=>User::create(['name'=>$role,'email'=>$email,'role'=>$role,'password'=>Hash::make('Test-Only-Password-2026!'),'status'=>'active','is_banned'=>'no','email_verified_at'=>now()]));
    }
    private function connection(User $school,string $id='school-one'): RoboquestConnection {
        return RoboquestConnection::create(['school_user_id'=>$school->id,'school_id'=>$id,'base_url'=>'http://localhost:8080','shared_key'=>str_repeat('x',40)]);
    }
    private function paidCourse(User $school,string $status='paid'): Course {
        $course=Course::withoutEvents(fn()=>Course::create(['title'=>'School robotics','slug'=>'school-robotics-'.random_int(1,99999),'type'=>'course','description'=>'<b>Original learning</b>','capacity'=>4]));
        $order=Order::create(['buyer_id'=>$school->id,'invoice_id'=>bin2hex(random_bytes(8)),'status'=>'completed','payment_status'=>$status]);
        Enrollment::create(['order_id'=>$order->id,'user_id'=>$school->id,'course_id'=>$course->id,'has_access'=>1]);return $course;
    }
    private function signed(string $path,array $body,string $key=null) {
        $body+=['schoolId'=>'school-one','timestamp'=>time(),'nonce'=>bin2hex(random_bytes(20))];$raw=json_encode($body);
        return $this->call('POST',$path,[],[],[],['CONTENT_TYPE'=>'application/json','HTTP_ACCEPT'=>'application/json','HTTP_X_ROBOQUEST_SIGNATURE'=>hash_hmac('sha256',$raw,$key??str_repeat('x',40))],$raw);
    }
    public function test_launch_code_is_scoped_single_use_and_only_paid_courses_are_returned(): void {
        $school=$this->user('school@one.test');$this->connection($school);$paid=$this->paidCourse($school);$this->paidCourse($school,'pending');$this->paidCourse($school,'refunded');
        $launch=app(RoboquestService::class)->launch($school);parse_str(parse_url($launch,PHP_URL_QUERY),$query);
        $result=$this->signed('/api/roboquest/launch/redeem',['code'=>$query['code']]);$result->assertOk()->assertJsonPath('user.role','School Admin')->assertJsonCount(1,'snapshot.courses')->assertJsonPath('snapshot.courses.0.id',(string)$paid->id);
        $this->signed('/api/roboquest/launch/redeem',['code'=>$query['code']])->assertStatus(401);
    }
    public function test_school_dashboard_automatically_hands_off_to_roboquest(): void {
        $school=$this->user('school@dashboard.test');$this->connection($school);$this->actingAs($school);
        $response=app(\App\Http\Controllers\Frontend\SchoolDashboardController::class)->index();
        $this->assertSame(route('roboquest.launch'),$response->getTargetUrl());
        $request=\Illuminate\Http\Request::create('/roboquest/open');$request->setUserResolver(fn()=>$school);
        $launch=app(\App\Http\Controllers\Frontend\RoboquestController::class)->launch($request,app(RoboquestService::class));
        $this->assertStringStartsWith('http://localhost:8080/launch?code=',$launch->getTargetUrl());
        $this->assertTrue($launch->headers->hasCacheControlDirective('no-store'));
    }
    public function test_unconnected_school_is_sent_to_connection_setup(): void {
        $school=$this->user('school@setup.test');$this->actingAs($school);
        $request=\Illuminate\Http\Request::create('/roboquest/open');$request->setUserResolver(fn()=>$school);
        $response=app(\App\Http\Controllers\Frontend\RoboquestController::class)->launch($request,app(RoboquestService::class));
        $this->assertSame(route('roboquest.settings'),$response->getTargetUrl());
        $this->assertSame(0,DB::table('roboquest_launch_codes')->count());
    }
    public function test_role_sign_in_requires_matching_credentials_and_replaces_current_account(): void {
        $school=$this->user('school@login.test');$teacher=$this->user('teacher@login.test','teacher');$student=$this->user('student@login.test','student');
        $this->actingAs($school);
        foreach ([$teacher,$student] as $account) {
            $request=\Illuminate\Http\Request::create('/roboquest/sign-in','POST',['login'=>$account->email,'password'=>'Test-Only-Password-2026!','account_role'=>$account->role]);
            $request->setLaravelSession(session()->driver());
            $route=(new \Illuminate\Routing\Route('POST','roboquest/sign-in',[]))->name('roboquest.signin.store');$route->bind($request);
            $request->setRouteResolver(fn()=>$route);
            $response=app(\App\Http\Controllers\Auth\AuthenticatedSessionController::class)->store($request);
            $this->assertSame(route('roboquest.launch'),$response->getTargetUrl());$this->assertAuthenticatedAs($account);
        }
        $request->merge(['account_role'=>'school']);
        try {app(\App\Http\Controllers\Auth\AuthenticatedSessionController::class)->store($request);$this->fail('A selected role must not grant another role');}
        catch (\Illuminate\Validation\ValidationException $e) {$this->assertArrayHasKey('login',$e->errors());}
        $this->assertAuthenticatedAs($student);
        $request->merge(['account_role'=>'teacher','login'=>$teacher->email,'password'=>'wrong-password']);
        try {app(\App\Http\Controllers\Auth\AuthenticatedSessionController::class)->store($request);$this->fail('Credentials are required to switch accounts');}
        catch (\Illuminate\Validation\ValidationException $e) {$this->assertArrayHasKey('login',$e->errors());}
        $this->assertAuthenticatedAs($student);
    }
    public function test_signatures_nonce_replay_and_school_isolation(): void {
        $school=$this->user('school@two.test');$this->connection($school);$other=$this->user('other@two.test');$this->connection($other,'school-two');$this->paidCourse($other);
        $this->signed('/api/roboquest/school-snapshot',[],str_repeat('y',40))->assertStatus(401);
        $request=['nonce'=>str_repeat('a',40)];$this->signed('/api/roboquest/school-snapshot',$request)->assertOk()->assertJsonCount(0,'courses');$this->signed('/api/roboquest/school-snapshot',$request)->assertStatus(409);
        $this->signed('/api/roboquest/school-snapshot',['timestamp'=>time()-300])->assertStatus(401);
    }
    public function test_teacher_and_student_members_receive_correct_roles_and_revoked_members_do_not_launch(): void {
        $school=$this->user('school@three.test');$this->connection($school);
        foreach(['teacher','student']as$role){$member=$this->user($role.'@three.test',$role);SchoolMember::create(['school_id'=>$school->id,'user_id'=>$member->id,'role_in_school'=>$role,'status'=>'active']);$launch=app(RoboquestService::class)->launch($member);parse_str(parse_url($launch,PHP_URL_QUERY),$query);$this->signed('/api/roboquest/launch/redeem',['code'=>$query['code']])->assertOk()->assertJsonPath('user.role',$role==='teacher'?'Teacher':'Student');SchoolMember::where('user_id',$member->id)->update(['status'=>'inactive']);$this->assertNull(app(RoboquestService::class)->launch($member));}
    }
    public function test_role_restrictions_are_server_side(): void {
        $school=$this->user('school@shops.test');$student=$this->user('student@shops.test','student');$teacher=$this->user('teacher@shops.test','teacher');
        $middleware=new \App\Http\Middleware\ShopAudience();
        foreach([['/shop',$school],['/api/shop-products',$teacher],['/labs/stem',$student],['/lab-design/start',$student]] as [$path,$user]){
            $request=\Illuminate\Http\Request::create($path);$request->setUserResolver(fn()=>$user);
            try{$middleware->handle($request,fn()=>response('Allowed'));$this->fail('Wrong role was allowed');}catch(\Symfony\Component\HttpKernel\Exception\HttpException $e){$this->assertSame(403,$e->getStatusCode());}
        }
    }
    public function test_confirmed_payment_persists_enrollment_before_school_handoff(): void {
        $school=$this->user('school@payment.test');$this->connection($school);
        $instructor=$this->user('author@payment.test','instructor');
        $course=$this->paidCourse($school,'pending');$course->instructor_id=$instructor->id;$course->save();
        $this->actingAs($school);
        \Illuminate\Support\Facades\Cache::put('setting',(object)['commission_rate'=>0,'google_tagmanager_status'=>'inactive']);
        \Illuminate\Support\Facades\Cache::put('marketing_setting',(object)['order_success'=>false]);
        \Gloudemans\Shoppingcart\Facades\Cart::shouldReceive('content')->andReturn(collect([(object)['id'=>$course->id,'price'=>100,'name'=>$course->title,'options'=>(object)['slug'=>$course->slug]]]));
        session()->put(['currency_code'=>'INR','currency_rate'=>1,'currency_position'=>'left','currency_icon'=>'₹','payable_amount'=>100,'payable_with_charge'=>100,'payable_currency'=>'INR','after_success_gateway'=>'PayPal','after_success_transaction'=>'VERIFIED-LOCAL-TEST','paid_amount'=>100,'payment_details'=>['status'=>'COMPLETED'],'gateway_charge_in_usd'=>0]);
        $response=app(\App\Http\Controllers\Frontend\PaymentController::class)->payment_addon_success();
        $this->assertStringStartsWith('http://localhost:8080/launch?code=',$response->getTargetUrl());
        $order=Order::where('transaction_id','VERIFIED-LOCAL-TEST')->firstOrFail();
        $this->assertSame('paid',$order->payment_status);
        $this->assertTrue(Enrollment::where('order_id',$order->id)->where('course_id',$course->id)->where('user_id',$school->id)->exists());
        $this->assertFalse(session()->has('after_success_transaction'));
        session()->put(['after_success_gateway'=>'PayPal','after_success_transaction'=>'VERIFIED-LOCAL-TEST','payable_amount'=>100]);
        $replay=app(\App\Http\Controllers\Frontend\PaymentController::class)->payment_addon_success();
        $this->assertStringStartsWith('http://localhost:8080/launch?code=',$replay->getTargetUrl());$this->assertSame(1,Order::where('transaction_id','VERIFIED-LOCAL-TEST')->count());$this->assertSame(1,Enrollment::where('order_id',$order->id)->count());
    }
    public function test_content_import_preserves_ids_previews_and_rolls_back_invalid_content(): void {
        Schema::table('course_chapters',function(Blueprint $t){$t->foreignId('instructor_id')->nullable();$t->timestamps();});
        Schema::table('course_chapter_items',function(Blueprint $t){$t->foreignId('instructor_id')->nullable();$t->string('type')->nullable();$t->timestamps();});
        Schema::table('course_chapter_lessons',function(Blueprint $t){$t->foreignId('course_id')->nullable();$t->foreignId('chapter_id')->nullable();$t->foreignId('instructor_id')->nullable();$t->string('storage')->nullable();$t->string('file_type')->nullable();$t->text('file_path')->nullable();$t->boolean('is_free')->default(false);$t->timestamps();});
        $school=$this->user('school@content.test');$connection=$this->connection($school);$course=$this->paidCourse($school);$course->instructor_id=$school->id;$course->api_course_id=123;$course->save();
        $lesson=['title'=>'Observe a sensor','text'=>"First paragraph.\nSecond paragraph.",'materials'=>'Notebook','video_url'=>null];
        $bundle=['version'=>1,'courses'=>[['course_id'=>$course->id,'external_course_id'=>123,'chapters'=>[['title'=>'Chapter one','status'=>'inactive','lessons'=>[$lesson]]]]]];
        $service=app(\App\Services\RoboquestContentImport::class);$before=Enrollment::count();
        $service->run($bundle);$this->assertSame(0,DB::table('course_chapters')->count());
        $receipt=$service->run($bundle,true);$this->assertNotNull($receipt['courses'][0]['chapters'][0]['lessons'][0]['id']);
        $this->assertCount(0,app(RoboquestService::class)->snapshot($connection)['courses'][0]['chapters']);
        $receipt['courses'][0]['chapters'][0]['status']='active';$service->run($receipt,true);$this->assertSame(1,DB::table('course_chapters')->count());$this->assertSame(1,DB::table('course_chapter_lessons')->count());
        $snapshot=app(RoboquestService::class)->snapshot($connection);$this->assertSame($lesson['text'],$snapshot['courses'][0]['chapters'][0]['lessons'][0]['text']);
        $invalid=$receipt;$invalid['courses'][0]['chapters'][0]['title']='Must roll back';$invalid['courses'][0]['chapters'][0]['lessons'][]=['title'=>'Empty','text'=>'','materials'=>'Notebook','video_url'=>null];
        try{$service->run($invalid,true);$this->fail('Empty content accepted');}catch(\Illuminate\Validation\ValidationException $e){$this->assertNotEmpty($e->errors());}
        $this->assertSame('Chapter one',DB::table('course_chapters')->value('title'));$this->assertSame(1,DB::table('course_chapter_lessons')->count());
        foreach (['mapping','url','scope','duplicate'] as $case) {
            $bad=$receipt;
            if($case==='mapping')$bad['courses'][0]['external_course_id']=999;
            if($case==='url')$bad['courses'][0]['chapters'][0]['lessons'][0]['video_url']='javascript:alert(1)';
            if($case==='scope')$bad['courses'][0]['chapters'][0]['lessons'][0]['id']=999;
            if($case==='duplicate')unset($bad['courses'][0]['chapters'][0]['id']);
            try{$service->run($bad,true);$this->fail('Invalid '.$case.' accepted');}catch(\Illuminate\Validation\ValidationException $e){$this->assertNotEmpty($e->errors());}
        }
        $this->assertSame($before,Enrollment::count());$this->assertSame(1,Order::count());
    }
    public function test_platform_authority_returns_only_paid_school_course_ids_and_rejects_school_keys_and_replay(): void {
        $school=$this->user('school@authority.test');$this->connection($school);$paid=$this->paidCourse($school);$paid->api_course_id=27;$paid->save();$pending=$this->paidCourse($school,'pending');$pending->api_course_id=999;$pending->save();
        config(['services.roboquest.platform_key'=>str_repeat('p',40)]);
        $payload=['schoolId'=>'school-one','timestamp'=>time(),'nonce'=>bin2hex(random_bytes(20))];$raw=json_encode($payload);
        $send=fn($key)=>$this->call('POST','/api/roboquest/platform-entitlements',[],[],[],['CONTENT_TYPE'=>'application/json','HTTP_ACCEPT'=>'application/json','HTTP_X_ROBOQUEST_SIGNATURE'=>hash_hmac('sha256',$raw,$key)],$raw);
        $send(str_repeat('x',40))->assertStatus(401);
        $send(str_repeat('p',40))->assertOk()->assertJsonPath('courseIds',['27'])->assertJsonMissingPath('members')->assertJsonMissingPath('courses');
        $send(str_repeat('p',40))->assertStatus(409);
    }
    public function test_platform_sales_mapping_is_explicit_and_cannot_change_an_already_sold_product(): void {
        $school=$this->user('school@mapping.test');$paid=$this->paidCourse($school);$paid->api_course_id=27;$paid->save();$other=$this->paidCourse($school,'pending');config(['services.roboquest.platform_key'=>str_repeat('p',40)]);
        $send=function($fields){$raw=json_encode($fields+['timestamp'=>time(),'nonce'=>bin2hex(random_bytes(20))]);return $this->call('POST','/api/roboquest/platform-sales-mappings',[],[],[],['CONTENT_TYPE'=>'application/json','HTTP_ACCEPT'=>'application/json','HTTP_X_ROBOQUEST_SIGNATURE'=>hash_hmac('sha256',$raw,str_repeat('p',40))],$raw);};
        $send(['action'=>'list'])->assertOk()->assertJsonCount(2,'courses');
        $send(['action'=>'map','localCourseId'=>$other->id,'apiCourseId'=>27])->assertStatus(409);
        $send(['action'=>'map','localCourseId'=>$paid->id,'apiCourseId'=>99])->assertStatus(409);
        $send(['action'=>'map','localCourseId'=>$other->id,'apiCourseId'=>52])->assertOk();$this->assertSame(52,(int)$other->fresh()->api_course_id);
        $request=\Illuminate\Http\Request::create('/add-to-cart-by-api-id/'.$other->id,'POST');
        $response=app(\App\Http\Controllers\Frontend\CartController::class)->addToCartByApiId($request,(string)$other->id);
        $this->assertSame('error',$response->getOriginalContent()['status']);$this->assertSame(2,Order::count());
    }
    public function test_lab_payment_return_requires_paid_order_owner_and_bound_staff_session(): void {
        require_once base_path('club-shop/app/Libraries/RoboquestPaymentReturn.php');
        $order=(object)['buyer_id'=>42,'payment_status'=>'payment_received'];
        $session=['auth_user_id'=>42,'roboquest_shop_user_id'=>42,'roboquest_shop_role'=>'school','roboquest_shop_role_expires'=>time()+3600];
        $url='http://localhost:8083/roboquest/open';$resolve=fn($o,$s)=>\App\Libraries\RoboquestPaymentReturn::destination($o,$s,$url);
        $this->assertSame($url,$resolve($order,$session));
        foreach(['pending_payment','failed','refunded'] as $status)$this->assertNull($resolve((object)['buyer_id'=>42,'payment_status'=>$status],$session));
        $this->assertNull($resolve($order,[...$session,'auth_user_id'=>99]));$this->assertNull($resolve($order,[...$session,'roboquest_shop_user_id'=>99]));$this->assertNull($resolve($order,[...$session,'roboquest_shop_role'=>'student']));$this->assertNull($resolve($order,[...$session,'roboquest_shop_role_expires'=>time()-1]));
    }
    public function test_visiting_success_route_without_verified_gateway_state_cannot_create_a_paid_order(): void {
        $school=$this->user('school@no-payment.test');$this->actingAs($school);$before=Order::count();
        $response=app(\App\Http\Controllers\Frontend\PaymentController::class)->payment_addon_success();
        $this->assertSame(route('order-fail'),$response->getTargetUrl());$this->assertSame($before,Order::count());
    }

}
