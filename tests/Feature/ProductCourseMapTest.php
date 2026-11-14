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
        config(['services.collab.lab_course_access'=>true,'services.collab.product_course_map_url'=>self::MAP_URL,'services.roboquest.platform_key'=>str_repeat('p',40),
            'database.connections.clubshop'=>['driver'=>'sqlite','database'=>':memory:','prefix'=>'']]);
        DB::purge('clubshop');
        $shop=Schema::connection('clubshop');
        $shop->create('users',function(Blueprint $t){$t->increments('id');$t->string('email');$t->tinyInteger('email_status')->default(0);$t->tinyInteger('banned')->default(0);});
        $shop->create('orders',function(Blueprint $t){$t->increments('id');$t->integer('buyer_id');$t->string('payment_status')->nullable();});
        $shop->create('order_items',function(Blueprint $t){$t->increments('id');$t->bigInteger('order_id');$t->integer('product_id')->nullable();$t->string('order_status')->nullable();});
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
}
