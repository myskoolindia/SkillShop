<?php

use Illuminate\Database\Migrations\Migration;
use Database\Seeders\MyskoolInternationalDataSeeder;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $seeder = new MyskoolInternationalDataSeeder();
        $seeder->run();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Non-destructive rollback
    }
};
