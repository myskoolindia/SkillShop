<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{User, Course, RoboquestConnection, SchoolMember, SchoolCourseAssignment};
use App\Http\Controllers\Frontend\{SchoolDashboardController, StudentDashboardController};
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Schema, Hash, Auth};
use Modules\Order\app\Models\{Order, OrderItem, Enrollment};

class CoursePurchaseAccessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'sqlite_testing',
            'database.connections.sqlite_testing' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
                'foreign_key_constraints' => true,
            ],
            'services.collab.lab_course_access' => false,
        ]);
        DB::purge('sqlite_testing');

        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('email')->unique();
            $t->string('password');
            $t->string('role');
            $t->string('status')->default('active');
            $t->string('is_banned')->default('no');
            $t->decimal('wallet_balance', 12, 2)->default(0);
            $t->string('phone')->nullable();
            $t->string('school_name')->nullable();
            $t->timestamp('email_verified_at')->nullable();
            $t->timestamps();
        });

        Schema::create('courses', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->string('slug');
            $t->string('type')->default('course');
            $t->string('status')->default('active');
            $t->integer('api_course_id')->nullable();
            $t->text('description')->nullable();
            $t->integer('capacity')->default(1);
            $t->foreignId('instructor_id')->nullable();
            $t->softDeletes();
            $t->timestamps();
        });

        Schema::create('orders', function (Blueprint $t) {
            $t->id();
            $t->foreignId('buyer_id');
            $t->string('invoice_id');
            $t->string('status');
            $t->string('payment_status');
            $t->timestamps();
        });

        Schema::create('order_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_id');
            $t->foreignId('course_id');
            $t->decimal('price', 10, 2)->default(0);
            $t->decimal('commission_rate', 5, 2)->default(0);
            $t->timestamps();
        });

        Schema::create('enrollments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_id');
            $t->foreignId('user_id');
            $t->foreignId('course_id');
            $t->integer('has_access')->default(1);
            $t->timestamps();
        });

        Schema::create('school_members', function (Blueprint $t) {
            $t->id();
            $t->foreignId('school_id');
            $t->foreignId('user_id');
            $t->string('role_in_school');
            $t->string('status')->default('active');
            $t->timestamps();
        });

        Schema::create('school_course_assignments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('school_id');
            $t->foreignId('course_id');
            $t->foreignId('user_id');
            $t->string('role_in_school')->default('student');
            $t->string('status')->default('active');
            $t->timestamps();
        });

        $migration = require database_path('migrations/2026_10_27_000001_create_roboquest_connections.php');
        $migration->up();
    }

    private function createUser(string $email, string $role): User
    {
        return User::withoutEvents(fn () => User::create([
            'name' => ucfirst($role) . ' User',
            'email' => $email,
            'role' => $role,
            'password' => Hash::make('password123'),
            'status' => 'active',
            'is_banned' => 'no',
            'email_verified_at' => now(),
        ]));
    }

    public function test_user_without_purchased_courses_has_flag_false(): void
    {
        $school = $this->createUser('school_none@test.com', 'school');
        $student = $this->createUser('student_none@test.com', 'student');
        $teacher = $this->createUser('teacher_none@test.com', 'teacher');

        $this->assertFalse($school->hasPurchasedCourses());
        $this->assertFalse($student->hasPurchasedCourses());
        $this->assertFalse($teacher->hasPurchasedCourses());
    }

    public function test_user_with_paid_enrollment_has_flag_true(): void
    {
        $student = $this->createUser('student_paid@test.com', 'student');
        $course = Course::withoutEvents(fn () => Course::create(['title' => 'Robotics 101', 'slug' => 'robotics-101', 'type' => 'course']));
        $order = Order::create(['buyer_id' => $student->id, 'invoice_id' => 'INV-001', 'status' => 'completed', 'payment_status' => 'paid']);
        Enrollment::create(['order_id' => $order->id, 'user_id' => $student->id, 'course_id' => $course->id, 'has_access' => 1]);

        $this->assertTrue($student->hasPurchasedCourses());
    }

    public function test_student_with_school_course_assignment_has_flag_true(): void
    {
        $school = $this->createUser('school_assign@test.com', 'school');
        $student = $this->createUser('student_assign@test.com', 'student');
        $course = Course::withoutEvents(fn () => Course::create(['title' => 'AI Basics', 'slug' => 'ai-basics', 'type' => 'course']));

        SchoolCourseAssignment::create([
            'school_id' => $school->id,
            'course_id' => $course->id,
            'user_id' => $student->id,
            'status' => 'active',
        ]);

        $this->assertTrue($student->hasPurchasedCourses());
    }

    public function test_login_without_purchased_courses_redirects_to_home_not_dashboard(): void
    {
        $student = $this->createUser('student_login_none@test.com', 'student');

        $loginRequest = LoginRequest::create('/user-login', 'POST', [
            'login' => $student->email,
            'password' => 'password123',
        ]);
        $loginRequest->setLaravelSession(session()->driver());
        $loginRequest->setContainer(app());

        $controller = app(\App\Http\Controllers\Auth\AuthenticatedSessionController::class);
        $response = $controller->store($loginRequest);

        $this->assertSame(route('home'), $response->getTargetUrl());
    }

    public function test_login_with_purchased_courses_redirects_to_roboquest(): void
    {
        $student = $this->createUser('student_login_paid@test.com', 'student');
        $course = Course::withoutEvents(fn () => Course::create(['title' => 'Coding 101', 'slug' => 'coding-101', 'type' => 'course']));
        $order = Order::create(['buyer_id' => $student->id, 'invoice_id' => 'INV-002', 'status' => 'completed', 'payment_status' => 'paid']);
        Enrollment::create(['order_id' => $order->id, 'user_id' => $student->id, 'course_id' => $course->id, 'has_access' => 1]);

        $loginRequest = LoginRequest::create('/user-login', 'POST', [
            'login' => $student->email,
            'password' => 'password123',
        ]);
        $loginRequest->setLaravelSession(session()->driver());
        $loginRequest->setContainer(app());

        $controller = app(\App\Http\Controllers\Auth\AuthenticatedSessionController::class);
        $response = $controller->store($loginRequest);

        $this->assertSame(route('roboquest.launch'), $response->getTargetUrl());
    }

    public function test_school_without_purchased_courses_visiting_dashboard_redirects_to_profile(): void
    {
        $school = $this->createUser('school_dash_none@test.com', 'school');
        $this->actingAs($school);

        $controller = app(SchoolDashboardController::class);
        $response = $controller->index();

        $this->assertSame(route('school.profile.index'), $response->getTargetUrl());
    }

    public function test_student_without_purchased_courses_visiting_dashboard_redirects_to_profile(): void
    {
        $student = $this->createUser('student_dash_none@test.com', 'student');
        $this->actingAs($student);

        $controller = app(StudentDashboardController::class);
        $response = $controller->index();

        $this->assertSame(route('student.setting.index'), $response->getTargetUrl());
    }
}
