<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $grade1Desc = '<p>The Grade 1 curriculum integrates five key learning areas — Personality Development, Mathematics, Art & Craft, STEM, and Communication Skills — to foster overall growth. Children build confidence, teamwork, and empathy through role-play and group activities; strengthen number sense, measurement, and problem-solving in maths; explore creativity via art, craft, and model-making; discover scientific concepts through experiments and observation; and enhance language abilities with reading, speaking, and writing exercises. The program focuses on practical engagement, creativity, and skill-building to prepare students for higher learning and everyday life.</p>';
        $grade1ShortDesc = 'A balanced program developing emotional, social, academic, creative, and scientific skills through interactive learning, hands-on activities, and real-life applications.';

        $grade2Desc = '<p>The Grade 2 curriculum blends Personality Development, Mathematics, Art & Craft, STEM, and Communication Skills to promote holistic growth. Students enhance empathy, teamwork, and leadership through group projects and role-play; master number sense, operations, fractions, and problem-solving in maths; develop artistic skills using various mediums and techniques; explore science through experiments, technology, and real-world applications; and advance their reading, writing, speaking, and listening abilities. Learning is activity-based, fostering curiosity, responsibility, and self-expression while preparing children for higher academic challenges.</p>';
        $grade2ShortDesc = 'An integrated learning program that strengthens emotional intelligence, academic skills, creativity, scientific understanding, and communication through engaging, hands-on experiences.';

        $grade3Desc = '<p>The Grade 3 curriculum integrates Personality Development, Mathematics, Art & Craft, STEM, and Communication Skills to promote balanced growth. Students build leadership, teamwork, and problem-solving skills through real-life scenarios; master multiplication, division, fractions, geometry, and financial literacy; express creativity through art, craft, and design projects; explore science concepts via experiments, engineering challenges, and technology-based learning; and enhance communication abilities with reading, writing, debating, and public speaking activities. The curriculum focuses on critical thinking, collaboration, and practical application of knowledge.</p>';
        $grade3ShortDesc = 'A well-rounded program designed to strengthen academic proficiency, creativity, scientific inquiry, communication skills, and emotional intelligence through interactive, hands-on learning.';

        $grade4Desc = '<p>Curriculum integrates five key learning areas — Personality Development, Mathematics, Art & Craft, STEM, and Communication Skills — to foster overall growth. Children build confidence, teamwork, and empathy through role-play and group activities; strengthen number sense, measurement, and problem-solving in maths; explore creativity via art, craft, and model-making; discover scientific concepts through experiments and observation; and enhance language abilities with reading, speaking, and writing exercises. The program focuses on practical engagement, creativity, and skill-building to prepare students for higher learning and everyday life.</p>';
        $grade4ShortDesc = 'A balanced program developing emotional, social, academic, creative, and scientific skills through interactive learning, hands-on activities, and real-life applications.';

        $grade5Desc = '<p>CAMPS prepares students with advanced communication, logical thinking, leadership, and innovation. It integrates all skills to develop confidence, responsibility, and problem-solving for higher grades.</p>';
        $grade5ShortDesc = 'Prepare for higher learning with innovation.';

        // Align Course ID 1 -> Grade 1 (api_course_id: 27)
        if (DB::table('courses')->where('id', 1)->exists()) {
            DB::table('courses')->where('id', 1)->update([
                'title'           => 'Grade 1',
                'slug'            => 'grade-1',
                'api_course_id'   => 27,
                'course_code'     => 'grade-1-b3a464',
                'price'           => 5000,
                'description'     => $grade1Desc,
                'seo_description' => $grade1ShortDesc,
                'thumbnail'       => 'uploads/courses/camps/27.png',
                'status'          => 'active',
                'is_approved'     => 'approved',
                'updated_at'      => now(),
            ]);
        }

        // Align Course ID 10 -> Grade 2 (api_course_id: 2)
        if (DB::table('courses')->where('id', 10)->exists()) {
            DB::table('courses')->where('id', 10)->update([
                'title'           => 'Grade 2',
                'slug'            => 'grade-2',
                'api_course_id'   => 2,
                'course_code'     => 'grade-2-68b96',
                'price'           => 5000,
                'description'     => $grade2Desc,
                'seo_description' => $grade2ShortDesc,
                'thumbnail'       => 'uploads/courses/camps/2.png',
                'status'          => 'active',
                'is_approved'     => 'approved',
                'updated_at'      => now(),
            ]);
        }

        // Align Course ID 5 -> Grade 3 (api_course_id: 52)
        if (DB::table('courses')->where('id', 5)->exists()) {
            DB::table('courses')->where('id', 5)->update([
                'title'           => 'Grade 3',
                'slug'            => 'grade-3',
                'api_course_id'   => 52,
                'course_code'     => 'GRADE3-696B8E36D0678',
                'price'           => 5000,
                'description'     => $grade3Desc,
                'seo_description' => $grade3ShortDesc,
                'thumbnail'       => 'uploads/courses/camps/52.png',
                'status'          => 'active',
                'is_approved'     => 'approved',
                'updated_at'      => now(),
            ]);
        }

        // Align Course ID 13 -> Grade 4 (api_course_id: 53)
        if (DB::table('courses')->where('id', 13)->exists()) {
            DB::table('courses')->where('id', 13)->update([
                'title'           => 'Grade 4',
                'slug'            => 'grade-4',
                'api_course_id'   => 53,
                'course_code'     => 'GRADE4-696DEA3745EE3',
                'price'           => 5000,
                'description'     => $grade4Desc,
                'seo_description' => $grade4ShortDesc,
                'thumbnail'       => 'uploads/courses/camps/53.png',
                'status'          => 'active',
                'is_approved'     => 'approved',
                'updated_at'      => now(),
            ]);
        }

        // Align Course ID 14 -> Grade 5 (api_course_id: 54)
        if (DB::table('courses')->where('id', 14)->exists()) {
            DB::table('courses')->where('id', 14)->update([
                'title'           => 'Grade 5',
                'slug'            => 'grade-5',
                'api_course_id'   => 54,
                'course_code'     => 'GRADE5-69708BB7C8FD0',
                'price'           => 5000,
                'description'     => $grade5Desc,
                'seo_description' => $grade5ShortDesc,
                'thumbnail'       => 'uploads/courses/camps/54.png',
                'status'          => 'active',
                'is_approved'     => 'approved',
                'updated_at'      => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe no-op
    }
};
