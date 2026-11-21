<?php
namespace Tests\Feature;
use Tests\TestCase;
use App\Models\{User,Course,RoboquestConnection,SchoolMember};
use App\Services\RoboquestService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\{DB,Hash,Http,Schema};
use Modules\Order\app\Models\{Order,Enrollment};
use Symfony\Component\HttpKernel\Exception\HttpException;

class ProductCourseMapTest extends TestCase {
    use RoboquestTestSchema;
    private const MAP_URL = 'https://collab.test/api/product-course-map';
    protected function setUp(): void {
        parent::setUp();
        $this->createRoboquestSchema();
        if(!Schema::hasTable('school_course_assignments'))Schema::create('school_course_assignments',function(Blueprint $t){$t->id();$t->foreignId('school_id');$t->foreignId('course_id');$t->foreignId('user_id');$t->string('role_in_school')->default('student');$t->string('status')->default('active');$t->timestamps();});
        config(['services.collab.lab_course_access'=>true,'services.collab.product_course_map_url'=>self::MAP_URL,'services.roboquest.platform_key'=>str_repeat('p',40),
            'database.connections.clubshop'=>['driver'=>'sqlite','database'=>':memory:','prefix'=>'']]);
        DB::purge('clubshop');
        $shop=Schema::connection('clubshop');
        $shop->create('users',function(Blueprint $t){$t->increments('id');$t->string('email');$t->tinyInteger('email_status')->default(0);$t->tinyInteger('banned')->default(0);});
        $shop->create('orders',function(Blueprint $t){$t->increments('id');$t->integer('buyer_id');$t->string('payment_status')->nullable();});
        $shop->create('order_items',function(Blueprint $t){$t->increments('id');$t->bigInteger('order_id');$t->integer('product_id')->nullable();$t->string('product_sku')->nullable();$t->string('order_status')->nullable();});
    }
    private int $mapperStatus = 200;
    private bool $mapperFaked = false;
    /** Laravel keeps the first matching fake, so register once and switch the provider status through a property. */
    private function mapper(int $status=200): void {
        $this->mapperStatus=$status;if($this->mapperFaked)return;$this->mapperFaked=true;
        // Two pages: course 98 is included with labs 85 and 86; course 120 with lab 90.
        $pages=[1=>['status'=>'success','data'=>[['course_id'=>98,'course_title'=>'Application of satellites','course_type'=>8,'course_type_name'=>'Pro','products'=>[['mapping_id'=>1,'product_id'=>85,'product_title'=>'ECE – Basic Lab']]]],'pagination'=>['last_page'=>2]],
            2=>['status'=>'success','data'=>[['course_id'=>98,'course_title'=>'Application of satellites','course_type'=>8,'products'=>[['mapping_id'=>2,'product_id'=>86,'product_title'=>'ECE – Premium Lab']]],['course_id'=>120,'course_title'=>'Trainer course','course_type'=>10,'products'=>[['mapping_id'=>3,'product_id'=>90,'product_title'=>'Robotics Lab']]]],'pagination'=>['last_page'=>2]]];
        Http::fake(['collab.test/*'=>function(HttpRequest $request) use ($pages){$status=$this->mapperStatus;parse_str((string)parse_url($request->url(),PHP_URL_QUERY),$query);return Http::response($status===200?$pages[(int)($query['page']??1)]:[],$status);}]);
    }
    private function user(string $email,string $role='school'): User {
        return User::withoutEvents(fn()=>User::create(['name'=>$role,'email'=>$email,'role'=>$role,'password'=>Hash::make('Test-Only-Password-2026!'),'status'=>'active','is_banned'=>'no','email_verified_at'=>now()]));
    }
    private function connection(User $school): RoboquestConnection {
        return RoboquestConnection::create(['school_user_id'=>$school->id,'school_id'=>'lab-school','base_url'=>'http://localhost:8080','shared_key'=>str_repeat('x',40)]);
    }
    private function labOrder(string $email,int $productId,string $payment='payment_received',?string $itemStatus='processing',int $verified=1): void {
        $shop=DB::connection('clubshop');
        $buyer=$shop->table('users')->where('email',$email)->value('id')??$shop->table('users')->insertGetId(['email'=>$email,'email_status'=>$verified]);
        $order=$shop->table('orders')->insertGetId(['buyer_id'=>$buyer,'payment_status'=>$payment]);
        $shop->table('order_items')->insert(['order_id'=>$order,'product_id'=>$productId,'order_status'=>$itemStatus]);
    }
    /** Launch snapshot: the school's own purchases, or a teacher's own purchases when $buyer is that teacher. */
    private function snapshot(RoboquestConnection $connection,?User $buyer=null): array { return app(RoboquestService::class)->snapshot($connection,$buyer); }
    private function entitlements() {
        $raw=json_encode(['schoolId'=>'lab-school','timestamp'=>time(),'nonce'=>bin2hex(random_bytes(20))]);
        return $this->call('POST','/api/roboquest/platform-entitlements',[],[],[],['CONTENT_TYPE'=>'application/json','HTTP_ACCEPT'=>'application/json','HTTP_X_ROBOQUEST_SIGNATURE'=>hash_hmac('sha256',$raw,str_repeat('p',40))],$raw);
    }

    public function test_paid_lab_product_gives_the_school_its_mapped_courses_and_platform_entitlements(): void {
        $this->mapper();$school=$this->user('School@Lab.test');$connection=$this->connection($school);
        $this->labOrder('school@lab.test',85);$this->labOrder('school@lab.test',86);
        $courses=collect($this->snapshot($connection)['courses']);
        $this->assertSame(['lab-course-98'],$courses->pluck('id')->all(),'two labs including the same course give one course');
        $course=$courses->first();
        $this->assertSame(98,$course['externalCourseId']);$this->assertNull($course['capacity']);$this->assertSame([],$course['chapters']);$this->assertSame('lab-product',$course['source']);
        $this->assertSame([85,86],array_column($course['labProducts'],'productId'));$this->assertSame('Included with ECE – Basic Lab, ECE – Premium Lab',$course['summary']);
        $this->entitlements()->assertOk()->assertJsonPath('courseIds',['98']);
    }
    public function test_unpaid_cancelled_refunded_unverified_and_unrelated_lab_orders_grant_nothing(): void {
        $this->mapper();$school=$this->user('school@lab.test');$connection=$this->connection($school);
        $this->labOrder('school@lab.test',85,'pending_payment');
        $this->labOrder('school@lab.test',86,'payment_received','cancelled');
        $this->labOrder('school@lab.test',90,'payment_received','refund_approved');
        $this->labOrder('someone@else.test',85);
        $this->labOrder('student@lab.test',90);$student=$this->user('student@lab.test','student');SchoolMember::create(['school_id'=>$school->id,'user_id'=>$student->id,'role_in_school'=>'student','status'=>'active']);
        $this->assertSame([],$this->snapshot($connection)['courses']);
        $this->entitlements()->assertOk()->assertJsonPath('courseIds',[]);
        // A same-email Club Shop account that never verified its email is not trusted as the school.
        $other=$this->user('unverified@lab.test');$otherConnection=RoboquestConnection::create(['school_user_id'=>$other->id,'school_id'=>'other-school','base_url'=>'http://localhost:8081','shared_key'=>str_repeat('y',40)]);
        $this->labOrder('unverified@lab.test',85,'payment_received','processing',0);
        $this->assertSame([],$this->snapshot($otherConnection)['courses']);
    }
    public function test_a_teachers_lab_shows_in_their_own_snapshot_and_counts_for_the_school_content_check(): void {
        $this->mapper();$school=$this->user('school@lab.test');$connection=$this->connection($school);
        $teacher=$this->user('teacher@lab.test','teacher');SchoolMember::create(['school_id'=>$school->id,'user_id'=>$teacher->id,'role_in_school'=>'teacher','status'=>'active']);
        $former=$this->user('former@lab.test','teacher');SchoolMember::create(['school_id'=>$school->id,'user_id'=>$former->id,'role_in_school'=>'teacher','status'=>'inactive']);
        $this->labOrder('teacher@lab.test',90);$this->labOrder('former@lab.test',85);
        // Purchases are per buyer: the school's snapshot does not include a teacher's lab, the teacher's does.
        $this->assertSame([],$this->snapshot($connection)['courses']);
        $own=$this->snapshot($connection,$teacher)['courses'];
        $this->assertSame(['lab-course-120-u'.$teacher->id],array_column($own,'id'),'a teacher copy never shares the school record ID');$this->assertSame((string)$teacher->id,$own[0]['buyerUserId']);
        // The master's content check covers the school and its active teachers, not former teachers.
        $this->entitlements()->assertOk()->assertJsonPath('courseIds',['120']);
    }
    public function test_a_direct_course_purchase_takes_precedence_over_the_lab_mapped_copy(): void {
        $this->mapper();$school=$this->user('school@lab.test');$connection=$this->connection($school);$this->labOrder('school@lab.test',85);
        $course=Course::withoutEvents(fn()=>Course::create(['title'=>'Satellites (sold course)','slug'=>'satellites','type'=>'course','api_course_id'=>98,'capacity'=>30]));
        $order=Order::create(['buyer_id'=>$school->id,'invoice_id'=>'inv-1','status'=>'completed','payment_status'=>'paid']);Enrollment::create(['order_id'=>$order->id,'user_id'=>$school->id,'course_id'=>$course->id,'has_access'=>1]);
        $courses=$this->snapshot($connection)['courses'];
        $this->assertSame([(string)$course->id],array_column($courses,'id'));$this->assertSame(30,$courses[0]['capacity']);
        $this->entitlements()->assertOk()->assertJsonPath('courseIds',['98']);
    }
    public function test_the_mapper_of_2026_11_21_courses_sold_on_skillvation_never_match_club_shop_orders(): void {
        config(['services.collab.product_course_map_url'=>'https://feed.test/api/course-product-map']);
        Http::fake(['feed.test/*'=>Http::response(json_decode(file_get_contents(base_path('tests/Fixtures/course-product-map-2026-11-21.json')),true))]);
        $school=$this->user('school@lab.test');$connection=$this->connection($school);
        $catalog=app(\App\Services\ProductCourseMapService::class)->catalog();
        $this->assertArrayNotHasKey('2',$catalog['products'],'Grade 2 (sold as a course, category s2s) is not a Club Shop product');
        $this->assertArrayNotHasKey('27',$catalog['products']);$this->assertArrayNotHasKey('grade-2-68b96',$catalog['skus']);
        $this->assertSame(['G6-K01'],$catalog['products']['203']['kits']);
        // An unrelated Club Shop product #2 unlocks nothing; the kit and a lab bundle do.
        $this->labOrder('school@lab.test',2);
        $this->assertSame([],$this->snapshot($connection)['unlocks']['school']['kits']);
        $this->assertSame([],array_filter($this->snapshot($connection)['courses'],fn($c)=>($c['externalCourseId']??null)==2));
        $this->labOrder('school@lab.test',203);
        $shop=DB::connection('clubshop');$buyer=$shop->table('users')->where('email','school@lab.test')->value('id');
        $order=$shop->table('orders')->insertGetId(['buyer_id'=>$buyer,'payment_status'=>'payment_received']);
        $shop->table('order_items')->insert(['order_id'=>$order,'product_id'=>700,'product_sku'=>'CAT-1','order_status'=>'processing']);
        $unlocks=$this->snapshot($connection)['unlocks']['school'];
        $this->assertSame(['G6-K01'],$unlocks['kits']);$this->assertSame(['food','herbal-heritage'],$unlocks['lms']);
    }
    public function test_credit_packs_are_not_sent_to_roboquest_as_courses(): void {
        $school=$this->user('school@credits.test');$connection=$this->connection($school);
        $grade=Course::withoutEvents(fn()=>Course::create(['title'=>'Grade 1','slug'=>'grade-1','type'=>'course','api_course_id'=>27,'course_code'=>'grade-1-b3a464']));
        $credits=Course::withoutEvents(fn()=>Course::create(['title'=>'Product 1 - 10 Credits (Free Trial)','slug'=>'skill2school-product-10-credits','type'=>'course','course_code'=>'S2S-CREDIT-10']));
        $order=Order::create(['buyer_id'=>$school->id,'invoice_id'=>'inv-c','status'=>'completed','payment_status'=>'paid']);
        foreach([$grade,$credits] as $c) Enrollment::create(['order_id'=>$order->id,'user_id'=>$school->id,'course_id'=>$c->id,'has_access'=>1]);
        $this->assertSame([(string)$grade->id],array_column($this->snapshot($connection)['courses'],'id'),'only the real course reaches RoboQuest');
        $this->assertTrue($school->fresh()->hasPurchasedCourses(),'a credit pack still counts as a purchase for the Dashboard button');
    }
    public function test_mapper_outage_uses_the_last_good_list_fails_closed_without_one_and_is_not_called_without_lab_orders(): void {
        $school=$this->user('school@lab.test');$connection=$this->connection($school);
        $this->mapper();$this->assertSame([],$this->snapshot($connection)['courses']);Http::assertNothingSent();
        $this->labOrder('school@lab.test',85);
        $this->mapper(503);
        try{$this->snapshot($connection);$this->fail('Expected the snapshot to fail closed');}catch(HttpException $error){$this->assertSame(503,$error->getStatusCode());}
        $this->entitlements()->assertStatus(503);
        $this->mapper();$this->assertSame(['lab-course-98'],array_column($this->snapshot($connection)['courses'],'id'));
        $this->travel(20)->minutes();$this->mapper(503);
        $this->assertSame(['lab-course-98'],array_column($this->snapshot($connection)['courses'],'id'));
    }
    public function test_disabled_lab_course_access_ignores_lab_orders(): void {
        config(['services.collab.lab_course_access'=>false]);Http::fake();
        $school=$this->user('school@lab.test');$connection=$this->connection($school);$this->labOrder('school@lab.test',85);
        $this->assertSame([],$this->snapshot($connection)['courses']);Http::assertNothingSent();
    }
    public function test_the_product_first_feed_unlocks_kits_skill_labs_and_courses_by_product_id_or_bundle_sku(): void {
        config(['services.collab.product_course_map_url'=>'https://feed.test/api/course-product-map']);
        Http::fake(['feed.test/*'=>Http::response(['status'=>'success','data'=>[
            ['product_id'=>'bundle_cat_72_1','product_sku'=>'CAT-1','product_title'=>'Agriculture and Gardening','category_name'=>'Composite Skill Lab','courses'=>[
                ['course_id'=>'kit:G7-K03','course_name'=>'Line-Following Delivery Robot Kit'],['course_id'=>'kit:G7-K02','course_name'=>'Rover'],['course_id'=>'video:9','course_name'=>'Unknown kinds are ignored']]],
            ['product_id'=>'91','product_sku'=>'ARVR-1','product_title'=>'Ar/VR','courses'=>[['course_id'=>'lab:ar-vr:basic','course_name'=>'AR & VR – Basic']]],
            ['product_id'=>'27','product_sku'=>'grade-1','product_title'=>'Grade 1','courses'=>[['course_id'=>'course:27','course_name'=>'Grade 1','course_category_name'=>'Skill2School']]],
        ],'pagination'=>['last_page'=>1]])]);
        $school=$this->user('school@lab.test');$connection=$this->connection($school);
        $teacher=$this->user('teacher@lab.test','teacher');
        \App\Models\SchoolMember::create(['school_id'=>$school->id,'user_id'=>$teacher->id,'role_in_school'=>'teacher','status'=>'active']);
        // The school bought the bundle (order line has its own product ID, matched by SKU). A row that only maps a course to
        // itself (Grade 1 → course:27) is a course sold on Skillvation, not a Club Shop product: Club Shop line 27 gets nothing.
        $shop=DB::connection('clubshop');$buyer=$shop->table('users')->insertGetId(['email'=>'school@lab.test','email_status'=>1]);
        $order=$shop->table('orders')->insertGetId(['buyer_id'=>$buyer,'payment_status'=>'payment_received']);
        $shop->table('order_items')->insert([['order_id'=>$order,'product_id'=>500,'product_sku'=>'cat-1','order_status'=>'processing'],['order_id'=>$order,'product_id'=>27,'product_sku'=>'grade-1','order_status'=>'processing']]);
        $this->labOrder('teacher@lab.test',91);
        $mine=$this->snapshot($connection);
        $this->assertSame(['kits'=>['G7-K02','G7-K03'],'labs'=>[],'lms'=>[],'lmsCategories'=>[]],$mine['unlocks']['school']);
        $this->assertSame([],array_column($mine['courses'],'id'));
        $teacherView=$this->snapshot($connection,$teacher);
        $this->assertSame(['kits'=>[],'labs'=>['ar-vr:basic'],'lms'=>[],'lmsCategories'=>[]],$teacherView['unlocks']['personal'],'a teacher\'s own purchase is theirs');
        $this->assertSame(['G7-K02','G7-K03'],$teacherView['unlocks']['school']['kits'],'the school\'s purchase reaches its teachers');
    }
    public function test_lab_lms_categories_and_single_lms_reach_roboquest_and_the_master_check(): void {
        config(['services.collab.product_course_map_url'=>'https://feed.test/api/course-product-map','services.roboquest.platform_key'=>str_repeat('p',40)]);
        Http::fake(['feed.test/*'=>Http::response(['status'=>'success','data'=>[
            ['product_id'=>'bundle_cat_72_1','product_sku'=>'CAT-1','product_title'=>'Agriculture and Gardening','courses'=>[['course_id'=>'lms-category:composite-agriculture-gardening']]],
            ['product_id'=>'95','product_sku'=>'LMS-BAKING','product_title'=>'Baking (LMS)','courses'=>[['course_id'=>'lms:baking'],['course_id'=>'lms:Bad ID']]],
        ],'pagination'=>['last_page'=>1]])]);
        $school=$this->user('school@lab.test');$connection=$this->connection($school);
        $teacher=$this->user('teacher@lab.test','teacher');
        \App\Models\SchoolMember::create(['school_id'=>$school->id,'user_id'=>$teacher->id,'role_in_school'=>'teacher','status'=>'active']);
        $shop=DB::connection('clubshop');$buyer=$shop->table('users')->insertGetId(['email'=>'school@lab.test','email_status'=>1]);
        $order=$shop->table('orders')->insertGetId(['buyer_id'=>$buyer,'payment_status'=>'payment_received']);
        $shop->table('order_items')->insert(['order_id'=>$order,'product_id'=>700,'product_sku'=>'CAT-1','order_status'=>'processing']);
        $this->labOrder('teacher@lab.test',95);
        $this->assertSame(['composite-agriculture-gardening'],$this->snapshot($connection)['unlocks']['school']['lmsCategories']);
        $this->assertSame(['baking'],$this->snapshot($connection,$teacher)['unlocks']['personal']['lms'],'a teacher can buy a single LMS');
        // The master's purchase check: the school and its teachers.
        $this->entitlements()->assertOk()->assertJsonPath('lmsCategories',['composite-agriculture-gardening'])->assertJsonPath('lms',['baking']);
        // Lab products alone (no course) count as a purchase: login and the school dashboard open RoboQuest.
        $this->assertTrue($school->fresh()->hasPurchasedCourses(),'a school that bought only a lab category');
        $this->assertTrue($teacher->fresh()->hasPurchasedCourses(),'a teacher who bought one LMS, in a school that bought a category');
        $student=$this->user('student@lab.test','student');
        \App\Models\SchoolMember::create(['school_id'=>$school->id,'user_id'=>$student->id,'role_in_school'=>'student','status'=>'active']);
        $this->assertTrue($student->fresh()->hasPurchasedCourses(),'a student of a school that bought a lab category');
        $this->assertFalse($this->user('other@lab.test','student')->hasPurchasedCourses(),'nobody else');
    }
}
