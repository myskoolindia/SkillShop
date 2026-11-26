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
        $shop->create('order_items',function(Blueprint $t){$t->increments('id');$t->bigInteger('order_id');$t->integer('product_id')->nullable();$t->string('product_sku')->nullable();$t->string('order_status')->nullable();$t->text('bundle_items')->nullable();});
        $shop->create('products',function(Blueprint $t){$t->increments('id');$t->integer('category_id')->nullable();});
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
    public function test_skill2skool_9_10_classes_and_domains_reach_roboquest(): void {
        config(['services.collab.product_course_map_url'=>'https://feed.test/api/course-product-map']);
        Http::fake(['feed.test/*'=>Http::response(['status'=>'success','data'=>[
            ['product_id'=>'bundle_cat_90_9','product_sku'=>'S2S-9','product_title'=>'Skill2Skool Class 9','courses'=>[['course_id'=>'s2s-grade:9'],['course_id'=>'s2s-grade:11']]],
            ['product_id'=>'404','product_sku'=>'S2S-FIN-10','product_title'=>'Finance – Class 10','courses'=>[['course_id'=>'s2s-domain:finance:10'],['course_id'=>'s2s-domain:Bad:10']]],
        ],'pagination'=>['last_page'=>1]])]);
        $school=$this->user('school@lab.test');$connection=$this->connection($school);
        $shop=DB::connection('clubshop');$buyer=$shop->table('users')->insertGetId(['email'=>'school@lab.test','email_status'=>1]);
        $order=$shop->table('orders')->insertGetId(['buyer_id'=>$buyer,'payment_status'=>'payment_received']);
        $shop->table('order_items')->insert([['order_id'=>$order,'product_id'=>900,'product_sku'=>'S2S-9','order_status'=>'processing'],['order_id'=>$order,'product_id'=>404,'product_sku'=>'S2S-FIN-10','order_status'=>'processing']]);
        $unlocks=$this->snapshot($connection)['unlocks']['school'];
        $this->assertSame([9],$unlocks['s2sGrades']);$this->assertSame(['finance:10'],$unlocks['s2sDomains']);
        $this->assertTrue($school->fresh()->hasPurchasedCourses(),'a Skill2Skool purchase opens RoboQuest at login');
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
        $this->assertSame(['kits'=>['G7-K02','G7-K03'],'labs'=>[],'lms'=>[],'lmsCategories'=>[],'s2sGrades'=>[],'s2sDomains'=>[]],$mine['unlocks']['school']);
        $this->assertSame([],array_column($mine['courses'],'id'));
        $teacherView=$this->snapshot($connection,$teacher);
        $this->assertSame(['kits'=>[],'labs'=>['ar-vr:basic'],'lms'=>[],'lmsCategories'=>[],'s2sGrades'=>[],'s2sDomains'=>[]],$teacherView['unlocks']['personal'],'a teacher\'s own purchase is theirs');
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

    public function test_a_paid_lab_bundle_unlocks_the_mapped_categories_it_has_components_of(): void {
        // Live mapper rows (2026-11-25): bundle_cat_<bundle>_<Club Shop category>.
        config(['services.collab.product_course_map_url'=>'https://feed.test/api/course-product-map']);
        Http::fake(['feed.test/*'=>Http::response(['status'=>'success','data'=>[
            ['product_id'=>'bundle_cat_72_1','product_sku'=>'CAT-1','product_title'=>'Agriculture and Gardening','courses'=>[['course_id'=>'lms:food'],['course_id'=>'lms:herbal-heritage']]],
            ['product_id'=>'bundle_cat_72_6','product_sku'=>'CAT-6','product_title'=>'ECE','courses'=>[['course_id'=>'lms:ece']]],
            ['product_id'=>'bundle_cat_72_5','product_sku'=>'CAT-5','product_title'=>'Healthcare','courses'=>[['course_id'=>'lms:life-cycle-of-medicine']]],
            ['product_id'=>'bundle_cat_68_7','product_sku'=>'CAT-7','product_title'=>'Design Thinking','courses'=>[['course_id'=>'lms:design-thinking']]],
            ['product_id'=>'bundle_cat_114_19','product_sku'=>'STEM','product_title'=>'STEM','courses'=>[['course_id'=>'lms-category:skillvation-stem']]],
        ],'pagination'=>['last_page'=>1]])]);
        $school=$this->user('school@lab.test');$connection=$this->connection($school);
        $shop=DB::connection('clubshop');
        // Components: 201/202 Agriculture (1), 206 ECE (6), 205 Healthcare (5, not chosen: quantity 0), 207 Design Thinking (7), 219 STEM (19).
        foreach ([201=>1,202=>1,206=>6,205=>5,207=>7,219=>19,218=>18] as $id=>$category) $shop->table('products')->insert(['id'=>$id,'category_id'=>$category]);
        $buyer=$shop->table('users')->insertGetId(['email'=>'school@lab.test','email_status'=>1]);
        $order=$shop->table('orders')->insertGetId(['buyer_id'=>$buyer,'payment_status'=>'payment_received']);
        $shop->table('order_items')->insert([
            ['order_id'=>$order,'product_id'=>72,'product_sku'=>'H2KN25H23-BUNDLE','order_status'=>'processing','bundle_items'=>json_encode([
                ['comp_id'=>1,'product_id'=>201,'qty'=>2],['comp_id'=>2,'product_id'=>202,'qty'=>1],['comp_id'=>3,'product_id'=>206,'qty'=>1],['comp_id'=>4,'product_id'=>205,'qty'=>0],['comp_id'=>5,'product_id'=>218,'qty'=>3]])],
            ['order_id'=>$order,'product_id'=>114,'product_sku'=>'SVLAB-BUNDLE','order_status'=>'processing','bundle_items'=>json_encode([['product_id'=>219,'qty'=>1,'unit_price'=>10,'is_main'=>0]])],
        ]);
        // Design Thinking of the OTHER bundle (68) was not bought: category keys belong to their own bundle.
        $unlocks=$this->snapshot($connection)['unlocks']['school'];
        $this->assertSame(['ece','food','herbal-heritage'],$unlocks['lms']);
        $this->assertSame(['skillvation-stem'],$unlocks['lmsCategories']);
        // A cancelled bundle line gives nothing.
        $shop->table('order_items')->update(['order_status'=>'cancelled']);
        $this->assertSame([],$this->snapshot($connection)['unlocks']['school']['lms']);
    }

    public function test_the_master_check_includes_kits_skill_labs_and_skill2skool_for_offline_content_packs(): void {
        config(['services.collab.product_course_map_url'=>'https://feed.test/api/course-product-map','services.roboquest.platform_key'=>str_repeat('p',40)]);
        Http::fake(['feed.test/*'=>Http::response(['status'=>'success','data'=>[
            ['product_id'=>'203','product_sku'=>'KIT-G6-1','product_title'=>'Timed Traffic Signal Kit','courses'=>[['course_id'=>'kit:G6-K01']]],
            ['product_id'=>'91','product_sku'=>'ARVR-1','product_title'=>'AR/VR','courses'=>[['course_id'=>'lab:ar-vr:basic']]],
            ['product_id'=>'404','product_sku'=>'S2S-FIN-9','product_title'=>'Finance – Class 9','courses'=>[['course_id'=>'s2s-domain:finance:9']]],
        ],'pagination'=>['last_page'=>1]])]);
        $school=$this->user('school@lab.test');$this->connection($school);
        $teacher=$this->user('teacher@lab.test','teacher');
        \App\Models\SchoolMember::create(['school_id'=>$school->id,'user_id'=>$teacher->id,'role_in_school'=>'teacher','status'=>'active']);
        $this->labOrder('school@lab.test',203);$this->labOrder('school@lab.test',404);$this->labOrder('teacher@lab.test',91);
        $this->entitlements()->assertOk()->assertJsonPath('kits',['G6-K01'])->assertJsonPath('labs',['ar-vr:basic'])
            ->assertJsonPath('s2sDomains',['finance:9'])->assertJsonPath('s2sGrades',[]);
    }

    /** Storefront orders (product pages): shop API prices, Razorpay payments, and the order tables. */
    private function storefront(bool $verifiedColumn=true): void {
        if(!Schema::hasTable('shop_orders'))Schema::create('shop_orders',function(Blueprint $t)use($verifiedColumn){$t->id();$t->string('order_number')->unique();$t->unsignedBigInteger('user_id')->nullable();$t->string('customer_name');$t->string('customer_email')->nullable();$t->string('customer_phone',20);$t->string('pincode',10);$t->string('city');$t->string('state');$t->string('country')->default('India');$t->text('address');$t->string('address_type')->default('Home');$t->string('payment_method')->default('razorpay');$t->string('payment_id')->nullable();$t->string('payment_status')->default('pending');if($verifiedColumn)$t->timestamp('payment_verified_at')->nullable();$t->string('order_status')->default('pending');$t->double('subtotal')->default(0);$t->double('shipping_cost')->default(0);$t->double('discount')->default(0);$t->double('total_amount')->default(0);$t->string('currency')->default('INR');$t->string('tracking_number')->nullable();$t->string('courier_partner')->nullable();$t->text('admin_notes')->nullable();$t->timestamps();});
        if(!Schema::hasTable('shop_order_items'))Schema::create('shop_order_items',function(Blueprint $t){$t->id();$t->unsignedBigInteger('shop_order_id');$t->unsignedBigInteger('product_id')->nullable();$t->string('product_title');$t->string('product_image')->nullable();$t->double('unit_price')->default(0);$t->integer('quantity')->default(1);$t->double('total_price')->default(0);$t->timestamps();});
        if(!Schema::hasTable('payment_gateways'))Schema::create('payment_gateways',function(Blueprint $t){$t->id();$t->string('key');$t->text('value')->nullable();$t->timestamps();});
        DB::table('payment_gateways')->insert([['key'=>'razorpay_key','value'=>'rzp_test_key'],['key'=>'razorpay_secret','value'=>'test-secret']]);
        putenv('SHOP_API_BASE_URL=https://shop.test/api/');$_ENV['SHOP_API_BASE_URL']=$_SERVER['SHOP_API_BASE_URL']='https://shop.test/api/';
        config(['app.app_mode'=>'LIVE']);
        \Illuminate\Support\Facades\Mail::fake();
        Http::fake([
            'shop.test/api/products/203'=>Http::response(['status'=>'success','data'=>['id'=>203,'title'=>'Timed Traffic Signal Kit (Grade 6)','price'=>12,'price_discounted'=>12]]),
            'api.razorpay.com/v1/payments/pay_GOOD123'=>Http::response(['id'=>'pay_GOOD123','status'=>'captured','amount'=>1200,'currency'=>'INR']),
            'api.razorpay.com/v1/payments/pay_LOW1234'=>Http::response(['id'=>'pay_LOW1234','status'=>'captured','amount'=>100,'currency'=>'INR']),
            'api.razorpay.com/v1/payments/pay_AUTH123/capture'=>Http::response(['id'=>'pay_AUTH123','status'=>'captured','amount'=>1200,'currency'=>'INR']),
            'api.razorpay.com/v1/payments/pay_AUTH123'=>Http::response(['id'=>'pay_AUTH123','status'=>'authorized','amount'=>1200,'currency'=>'INR']),
            'api.razorpay.com/*'=>Http::response(['error'=>['description'=>'not found']],404),
            'feed.test/*'=>Http::response(['status'=>'success','data'=>[['product_id'=>'203','product_sku'=>'H2NI2H2D1I0','product_title'=>'Timed Traffic Signal Kit (Grade 6)','courses'=>[['course_id'=>'kit:G6-K01']]]],'pagination'=>['last_page'=>1]]),
            '*'=>Http::response([],200),
        ]);
        config(['services.collab.product_course_map_url'=>'https://feed.test/api/course-product-map']);
    }
    private function storefrontOrder(User $buyer,string $paymentId,float $claimedPrice=12,string $status='paid') {
        return $this->actingAs($buyer)->postJson('/api/shop-orders/create',['customer_phone'=>'9999999999','payment_id'=>$paymentId,'payment_status'=>$status,
            'items'=>[['product_id'=>203,'title'=>'Timed Traffic Signal Kit (Grade 6)','unit_price'=>$claimedPrice,'quantity'=>1]],'total_amount'=>$claimedPrice]);
    }

    public function test_a_storefront_order_counts_only_when_razorpay_confirms_the_real_price_was_paid(): void {
        $this->storefront();
        $school=$this->user('school@lab.test');$connection=$this->connection($school);
        // Claiming "paid" with a ₹1 price and no real payment gets nothing.
        $fake=$this->storefrontOrder($school,'pay_MADEUP99',1)->assertOk()->json('data');
        $this->assertSame('pending',$fake['payment_status']);
        $this->assertNull($fake['payment_verified_at']);
        $this->assertSame([],$this->snapshot($connection)['unlocks']['school']['kits']);
        $this->assertStringContainsString('not verified',\App\Models\ShopOrder::find($fake['id'])->admin_notes);
        // Paying less than the shop price is caught.
        $this->assertSame('pending',$this->storefrontOrder($school,'pay_LOW1234',1)->json('data.payment_status'));
        // The real payment: verified, priced from the shop, and the kit unlocks.
        $paid=$this->storefrontOrder($school,'pay_GOOD123',1)->assertOk()->json('data');
        $this->assertSame('paid',$paid['payment_status']);
        $this->assertNotNull($paid['payment_verified_at']);
        $this->assertEquals(12,$paid['items'][0]['unit_price'],'the price comes from the shop, not the page');
        $this->assertSame(['G6-K01'],$this->snapshot($connection)['unlocks']['school']['kits']);
        // The same payment cannot pay for a second order; an authorised payment is captured.
        $this->assertSame('pending',$this->storefrontOrder($school,'pay_GOOD123')->json('data.payment_status'));
        $this->assertSame('paid',$this->storefrontOrder($school,'pay_AUTH123')->json('data.payment_status'));
    }

    public function test_orders_placed_before_verification_are_checked_at_sync_and_by_the_command_without_changing_others(): void {
        $this->storefront();
        $school=$this->user('school@lab.test');$connection=$this->connection($school);
        $make=function($paymentId)use($school){$o=\App\Models\ShopOrder::create(['order_number'=>'SKILLBOX-'.$paymentId,'user_id'=>$school->id,'customer_name'=>'School','customer_email'=>'school@lab.test','customer_phone'=>'9999999999','pincode'=>'560001','city'=>'B','state'=>'K','address'=>'x','payment_method'=>'razorpay','payment_id'=>$paymentId,'payment_status'=>'paid','total_amount'=>12]);
            \App\Models\ShopOrderItem::create(['shop_order_id'=>$o->id,'product_id'=>203,'product_title'=>'Kit','unit_price'=>12,'quantity'=>1,'total_price'=>12]);return $o;};
        $good=$make('pay_GOOD123');$bad=$make('pay_LOW1234');
        // RoboQuest's next sync checks both with Razorpay: the real payment unlocks, the underpaid one does not.
        $this->assertSame(['G6-K01'],$this->snapshot($connection)['unlocks']['school']['kits']);
        $this->artisan('shop-orders:verify')->assertSuccessful();
        $this->assertNotNull($good->fresh()->payment_verified_at);
        $this->assertNull($bad->fresh()->payment_verified_at);
        $this->assertSame('paid',$bad->fresh()->payment_status,'existing orders keep their status');
        $this->assertSame(['G6-K01'],$this->snapshot($connection)['unlocks']['school']['kits']);
    }

    public function test_without_commands_an_earlier_order_is_verified_when_roboquest_next_asks_and_the_column_is_added(): void {
        // Hostinger: no migrations, no artisan, no cron. The order was saved as "paid" by the old page.
        $this->storefront(false);
        $this->assertFalse(Schema::hasColumn('shop_orders','payment_verified_at'));
        $school=$this->user('school@lab.test');$connection=$this->connection($school);
        $order=\App\Models\ShopOrder::create(['order_number'=>'SKILLBOX-OLD','user_id'=>$school->id,'customer_name'=>'School','customer_email'=>'school@lab.test','customer_phone'=>'9999999999','pincode'=>'560001','city'=>'B','state'=>'K','address'=>'x','payment_method'=>'razorpay','payment_id'=>'pay_GOOD123','payment_status'=>'paid','total_amount'=>12]);
        \App\Models\ShopOrderItem::create(['shop_order_id'=>$order->id,'product_id'=>203,'product_title'=>'Kit','unit_price'=>12,'quantity'=>1,'total_price'=>12]);
        // The next sync (RoboQuest's snapshot) checks it with Razorpay and the kit unlocks.
        $this->assertSame(['G6-K01'],$this->snapshot($connection)['unlocks']['school']['kits']);
        $this->assertTrue(Schema::hasColumn('shop_orders','payment_verified_at'));
        $this->assertNotNull($order->fresh()->payment_verified_at);
    }

    public function test_a_storefront_problem_never_blocks_the_purchase_check(): void {
        $this->storefront();
        Schema::drop('shop_order_items');
        $school=$this->user('school@lab.test');$connection=$this->connection($school);
        $this->labOrder('school@lab.test',203);
        $this->assertSame(['G6-K01'],$this->snapshot($connection)['unlocks']['school']['kits'],'the Club Shop purchase still counts');
    }
}
