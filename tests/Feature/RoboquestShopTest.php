<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{User, RoboquestConnection, SchoolMember};
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Auth, DB, Hash, Http, Schema};

class RoboquestShopTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'shop_testing', 'database.connections.shop_testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true],
            'services.club_shop.api_url' => 'https://shop.test/api']);
        DB::purge('shop_testing');
        Schema::create('users', function (Blueprint $t) { $t->id(); $t->string('name'); $t->string('email')->unique(); $t->string('password'); $t->string('role'); $t->string('status')->default('active'); $t->string('is_banned')->default('no'); $t->string('phone')->nullable(); $t->timestamp('email_verified_at')->nullable(); $t->rememberToken(); $t->timestamps(); });
        Schema::create('school_members', function (Blueprint $t) { $t->id(); $t->foreignId('school_id'); $t->foreignId('user_id'); $t->string('role_in_school'); $t->string('status'); $t->timestamps(); });
        (require database_path('migrations/2026_10_27_000001_create_roboquest_connections.php'))->up();
        (require database_path('migrations/2026_11_18_000001_create_roboquest_shop_codes.php'))->up();
    }

    private function user(string $email, string $role): User
    {
        return User::withoutEvents(fn () => User::create(['name' => $role, 'email' => $email, 'role' => $role, 'password' => Hash::make('Test-Only-Password-2026!'), 'status' => 'active', 'is_banned' => 'no', 'email_verified_at' => now()]));
    }

    private function send(string $path, array $body)
    {
        $body += ['schoolId' => 'school-one', 'timestamp' => time(), 'nonce' => bin2hex(random_bytes(20))]; $raw = json_encode($body);
        return $this->call('POST', $path, [], [], [], ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json', 'HTTP_X_ROBOQUEST_SIGNATURE' => hash_hmac('sha256', $raw, str_repeat('x', 40))], $raw);
    }

    private function school(): User
    {
        $school = $this->user('school@one.test', 'school');
        RoboquestConnection::create(['school_user_id' => $school->id, 'school_id' => 'school-one', 'base_url' => 'http://localhost:8080', 'shared_key' => str_repeat('x', 40)]);
        return $school;
    }

    public function test_products_come_from_the_shop_catalogue_in_a_simple_shape(): void
    {
        $this->school();
        Http::fake([
            'shop.test/api/categories' => Http::response(['status' => 'success', 'data' => [['id' => 6, 'name' => 'ECE (Electronics & Communication)'], ['id' => 8, 'name' => 'Robotics']]]),
            'shop.test/api/categories/6/products*' => Http::response(['status' => 'success', 'data' => [['id' => 85, 'title' => 'ECE – Basic Lab', 'slug' => 'ece-basic-lab-85', 'sku' => 'H2KF', 'price' => 230, 'price_discounted' => 230, 'currency' => 'INR', 'category_id' => 6, 'stock' => 5, 'image' => 'https://shop.test/ece.jpg']], 'pagination' => ['page' => 1, 'pages' => 1, 'total' => 1]]),
        ]);
        $this->send('/api/roboquest/shop/products', ['audience' => 'teacher', 'categoryId' => 6])->assertOk()
            ->assertJsonPath('categories.1.name', 'Robotics')->assertJsonPath('products.0.title', 'ECE – Basic Lab')->assertJsonPath('products.0.slug', 'ece-basic-lab-85')
            ->assertJsonPath('products.0.price', 230)->assertJsonPath('products.0.inStock', true)->assertJsonPath('total', 1);
        $this->send('/api/roboquest/shop/products', ['audience' => 'parent'])->assertStatus(422);
    }

    public function test_a_one_time_link_signs_the_member_in_and_continues_to_the_shop_page(): void
    {
        $school = $this->school();
        $student = $this->user('kid@one.test', 'student');
        SchoolMember::create(['school_id' => $school->id, 'user_id' => $student->id, 'role_in_school' => 'student', 'status' => 'active']);
        $url = $this->send('/api/roboquest/shop/link', ['externalId' => (string) $student->id, 'target' => '/ece-basic-lab-85'])->assertOk()->json('url');
        $this->assertStringContainsString('/roboquest/shop-open?code=', $url);
        $path = parse_url($url, PHP_URL_PATH) . '?' . parse_url($url, PHP_URL_QUERY);
        $this->get($path)->assertRedirect(route('sso.shop', ['target' => '/ece-basic-lab-85']));
        $this->assertSame($student->id, Auth::guard('web')->id(), 'the member is signed in for checkout');
        Auth::guard('web')->logout();
        $this->get($path)->assertStatus(410);
        $outsider = $this->user('other@two.test', 'student');
        $this->send('/api/roboquest/shop/link', ['externalId' => (string) $outsider->id, 'target' => '/cart'])->assertNotFound();
        $this->send('/api/roboquest/shop/link', ['externalId' => (string) $student->id, 'target' => 'https://evil.test/'])->assertStatus(422);
        $this->send('/api/roboquest/shop/link', ['externalId' => (string) $student->id, 'target' => '/../admin'])->assertStatus(422);
        $this->get('/roboquest/shop-open?code=' . str_repeat('a', 64))->assertStatus(410);
    }

    public function test_my_orders_lists_only_that_persons_club_shop_orders(): void
    {
        $school = $this->school();
        $teacher = $this->user('Teacher@One.test', 'teacher');
        $outsider = $this->user('out@two.test', 'teacher');
        SchoolMember::create(['school_id' => $school->id, 'user_id' => $teacher->id, 'role_in_school' => 'teacher', 'status' => 'active']);
        // The Club Shop database (its own connection in production).
        config(['database.connections.clubshop' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']]); DB::purge('clubshop');
        $shop = Schema::connection('clubshop');
        $shop->create('users', function (Blueprint $t) { $t->id(); $t->string('email'); });
        $shop->create('orders', function (Blueprint $t) { $t->id(); $t->string('order_number'); $t->integer('buyer_id'); $t->integer('status'); $t->string('payment_status'); $t->string('payment_method'); $t->decimal('price_total'); $t->string('price_currency'); $t->timestamp('created_at')->nullable(); });
        $shop->create('order_items', function (Blueprint $t) { $t->id(); $t->integer('order_id'); $t->string('product_title'); $t->integer('product_quantity'); $t->decimal('product_total_price'); });
        $c = DB::connection('clubshop');
        $c->table('users')->insert([['id' => 7, 'email' => 'teacher@one.test'], ['id' => 8, 'email' => 'someone@else.test']]);
        $c->table('orders')->insert([
            ['id' => 1, 'order_number' => '10001', 'buyer_id' => 7, 'status' => 1, 'payment_status' => 'payment_received', 'payment_method' => 'razorpay', 'price_total' => 16, 'price_currency' => 'INR', 'created_at' => '2026-11-18 10:00:00'],
            ['id' => 2, 'order_number' => '10002', 'buyer_id' => 7, 'status' => 0, 'payment_status' => 'pending_payment', 'payment_method' => 'razorpay', 'price_total' => 230, 'price_currency' => 'INR', 'created_at' => '2026-11-18 11:00:00'],
            ['id' => 3, 'order_number' => '10003', 'buyer_id' => 8, 'status' => 1, 'payment_status' => 'payment_received', 'payment_method' => 'razorpay', 'price_total' => 99, 'price_currency' => 'INR', 'created_at' => '2026-11-18 12:00:00'],
        ]);
        $c->table('order_items')->insert([['order_id' => 1, 'product_title' => 'Jumper wires', 'product_quantity' => 2, 'product_total_price' => 16], ['order_id' => 3, 'product_title' => 'Not yours', 'product_quantity' => 1, 'product_total_price' => 99]]);
        $this->send('/api/roboquest/shop/orders', ['externalId' => (string) $teacher->id])->assertOk()
            ->assertJsonCount(2, 'orders')
            ->assertJsonPath('orders.0.number', '10002')->assertJsonPath('orders.0.status', 'pending_payment')
            ->assertJsonPath('orders.1.number', '10001')->assertJsonPath('orders.1.status', 'completed')->assertJsonPath('orders.1.total', 16)
            ->assertJsonPath('orders.1.items.0.title', 'Jumper wires')->assertJsonPath('orders.1.items.0.quantity', 2);
        $this->send('/api/roboquest/shop/orders', ['externalId' => (string) $outsider->id])->assertNotFound();
    }
}
