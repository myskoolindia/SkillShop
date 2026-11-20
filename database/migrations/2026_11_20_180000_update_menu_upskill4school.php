<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        DB::table('menu_items')
            ->where('link', '/upskill4teacher')
            ->orWhere('label', 'Upskill 4 Teacher')
            ->update([
                'label' => 'Upskill 4 School',
                'link'  => '/upskill4school',
            ]);

        DB::table('menu_item_translations')
            ->where('label', 'Upskill 4 Teacher')
            ->update([
                'label' => 'Upskill 4 School',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        DB::table('menu_items')
            ->where('link', '/upskill4school')
            ->orWhere('label', 'Upskill 4 School')
            ->update([
                'label' => 'Upskill 4 Teacher',
                'link'  => '/upskill4teacher',
            ]);

        DB::table('menu_item_translations')
            ->where('label', 'Upskill 4 School')
            ->update([
                'label' => 'Upskill 4 Teacher',
            ]);
    }
};
