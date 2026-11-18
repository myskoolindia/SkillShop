<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// One-time links that open the shop, signed in, inside RoboQuest (RoboquestShopController).
return new class extends Migration {
    public function up(): void
    {
        Schema::create('roboquest_shop_codes', function (Blueprint $table) {
            $table->id(); $table->foreignId('connection_id')->constrained('roboquest_connections')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users'); $table->string('code_hash', 64)->unique(); $table->string('target', 300);
            $table->timestamp('expires_at'); $table->timestamp('used_at')->nullable(); $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('roboquest_shop_codes'); }
};
