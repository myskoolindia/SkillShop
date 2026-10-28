<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('roboquest_connections', function (Blueprint $table) {
            $table->id(); $table->foreignId('school_user_id')->unique()->constrained('users');
            $table->string('school_id', 80)->unique(); $table->string('base_url', 500);
            $table->text('shared_key'); $table->timestamps();
        });
        Schema::create('roboquest_launch_codes', function (Blueprint $table) {
            $table->id(); $table->foreignId('connection_id')->constrained('roboquest_connections')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users'); $table->string('code_hash', 64)->unique();
            $table->timestamp('expires_at'); $table->timestamp('used_at')->nullable(); $table->timestamps();
        });
        Schema::create('roboquest_request_nonces', function (Blueprint $table) {
            $table->id(); $table->foreignId('connection_id')->constrained('roboquest_connections')->cascadeOnDelete();
            $table->string('nonce', 80); $table->timestamp('expires_at'); $table->unique(['connection_id', 'nonce']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('roboquest_request_nonces'); Schema::dropIfExists('roboquest_launch_codes'); Schema::dropIfExists('roboquest_connections');
    }
};
