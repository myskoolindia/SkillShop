<?php
namespace Tests\Feature;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB,Schema};

/** Minimal portal schema for RoboQuest bridge tests (in-memory SQLite). */
trait RoboquestTestSchema {
    protected function createRoboquestSchema(): void {
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
}
