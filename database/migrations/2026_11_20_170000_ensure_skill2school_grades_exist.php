<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $grades = [
            [
                'title' => 'Grade 1',
                'slug' => 'grade-1',
                'api_course_id' => 27,
                'course_code' => 'grade-1-b3a464',
                'price' => 5000,
                'description' => '<p>The Grade 1 curriculum integrates five key learning areas — Personality Development, Mathematics, Art & Craft, STEM, and Communication Skills — to foster overall growth.</p>',
            ],
            [
                'title' => 'Grade 2',
                'slug' => 'grade-2',
                'api_course_id' => 2,
                'course_code' => 'grade-2-68b96',
                'price' => 5000,
                'description' => '<p>The Grade 2 curriculum blends Personality Development, Mathematics, Art & Craft, STEM, and Communication Skills to promote holistic growth.</p>',
            ],
            [
                'title' => 'Grade 3',
                'slug' => 'grade-3',
                'api_course_id' => 52,
                'course_code' => 'GRADE3-696B8E36D0678',
                'price' => 5000,
                'description' => '<p>The Grade 3 curriculum integrates Personality Development, Mathematics, Art & Craft, STEM, and Communication Skills to promote balanced growth.</p>',
            ],
            [
                'title' => 'Grade 4',
                'slug' => 'grade-4',
                'api_course_id' => 53,
                'course_code' => 'GRADE4-696DEA3745EE3',
                'price' => 5000,
                'description' => '<p>Curriculum integrates five key learning areas — Personality Development, Mathematics, Art & Craft, STEM, and Communication Skills — to foster overall growth.</p>',
            ],
            [
                'title' => 'Grade 5',
                'slug' => 'grade-5',
                'api_course_id' => 54,
                'course_code' => 'GRADE5-69708BB7C8FD0',
                'price' => 5000,
                'description' => '<p>CAMPS prepares students with advanced communication, logical thinking, leadership, and innovation. It integrates all skills to develop confidence, responsibility, and problem-solving for higher grades.</p>',
            ],
        ];

        foreach ($grades as $g) {
            $exists = DB::table('courses')->where('api_course_id', $g['api_course_id'])->exists();
            if (!$exists) {
                DB::table('courses')->insert([
                    'instructor_id' => 1,
                    'category_id'   => 2,
                    'type'          => 'course',
                    'title'         => $g['title'],
                    'slug'          => $g['slug'],
                    'api_course_id' => $g['api_course_id'],
                    'course_code'   => $g['course_code'],
                    'price'         => $g['price'],
                    'description'   => $g['description'],
                    'status'        => 'active',
                    'is_approved'   => 'approved',
                    'is_online'     => 0,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe no-op to prevent unintended course deletion
    }
};
