<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\SchoolMember;
use App\Models\SchoolCourseAssignment;
use App\Models\Course;
use App\Models\CourseChapter;
use App\Models\CourseChapterItem;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizQuestionAnswer;
use App\Models\QuizResult;
use App\Models\RoboquestConnection;
use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderItem;
use Modules\Order\app\Models\Enrollment;

class MyskoolInternationalDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // 1. School: Myskool International
            $schoolEmail = 'myskool.international@myskool.club';
            $school = User::where('email', $schoolEmail)->first();
            if (!$school) {
                $school = User::create([
                    'name'               => 'Myskool International',
                    'email'              => $schoolEmail,
                    'role'               => 'school',
                    'password'           => Hash::make('Password123!'),
                    'status'             => 'active',
                    'is_banned'          => 'no',
                    'phone'              => '+919876543210',
                    'school_name'        => 'Myskool International',
                    'contact_person'     => 'Principal Dr. V. Sharma',
                    'registration_number'=> 'CBSE-AFF-2026-9871',
                    'address'            => 'Knowledge Boulevard, Sector 62, New Delhi, India',
                    'city'               => 'New Delhi',
                    'state'              => 'Delhi',
                    'country_id'         => 1,
                    'email_verified_at'  => now(),
                ]);
            } else {
                $school->update([
                    'name'              => 'Myskool International',
                    'role'              => 'school',
                    'status'            => 'active',
                    'password'          => Hash::make('Password123!'),
                    'school_name'       => 'Myskool International',
                    'email_verified_at' => now(),
                ]);
            }

            // School RoboQuest Connection
            $schoolIdString = 'school-' . $school->id;
            RoboquestConnection::updateOrCreate(
                ['school_user_id' => $school->id],
                [
                    'school_id'  => $schoolIdString,
                    'base_url'   => env('DEFAULT_ROBOQUEST_URL', 'http://localhost:8080'),
                    'shared_key' => env('DEFAULT_ROBOQUEST_KEY', 'default-key-skillvation-collab-shared-secret-32'),
                ]
            );

            // 2. Teacher: Saraswathi
            $teacherEmail = 'saraswathi@myskool.club';
            $teacher = User::where('email', $teacherEmail)->first();
            if (!$teacher) {
                $teacher = User::create([
                    'name'              => 'Saraswathi',
                    'email'             => $teacherEmail,
                    'role'              => 'teacher',
                    'password'          => Hash::make('Password123!'),
                    'status'            => 'active',
                    'is_banned'         => 'no',
                    'phone'             => '+919876543211',
                    'job_title'         => 'Senior STEM & Skill Educator',
                    'address'           => 'Faculty Quarters, Myskool International',
                    'city'              => 'New Delhi',
                    'state'             => 'Delhi',
                    'country_id'        => 1,
                    'email_verified_at' => now(),
                ]);
            } else {
                $teacher->update([
                    'name'              => 'Saraswathi',
                    'role'              => 'teacher',
                    'status'            => 'active',
                    'password'          => Hash::make('Password123!'),
                    'email_verified_at' => now(),
                ]);
            }

            SchoolMember::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'user_id'   => $teacher->id,
                ],
                [
                    'role_in_school' => 'teacher',
                    'id_number'      => 'TCH-SARASWATHI-01',
                    'grade'          => 'All Grades (Grades 1-8)',
                    'section'        => 'A',
                    'academic_year'  => '2026-2027',
                    'board'          => 'CBSE',
                    'status'         => 'active',
                ]
            );

            // 3. Student: Rahul (Grade 6)
            $studentEmail = 'rahul@myskool.club';
            $student = User::where('email', $studentEmail)->first();
            if (!$student) {
                $student = User::create([
                    'name'              => 'Rahul',
                    'email'             => $studentEmail,
                    'role'              => 'student',
                    'password'          => Hash::make('Password123!'),
                    'status'            => 'active',
                    'is_banned'         => 'no',
                    'phone'             => '+919876543212',
                    'address'           => 'Student Hostel Block B, Myskool International',
                    'city'              => 'New Delhi',
                    'state'             => 'Delhi',
                    'country_id'        => 1,
                    'email_verified_at' => now(),
                ]);
            } else {
                $student->update([
                    'name'              => 'Rahul',
                    'role'              => 'student',
                    'status'            => 'active',
                    'password'          => Hash::make('Password123!'),
                    'email_verified_at' => now(),
                ]);
            }

            SchoolMember::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'user_id'   => $student->id,
                ],
                [
                    'role_in_school' => 'student',
                    'id_number'      => 'STU-RAHUL-G6',
                    'grade'          => 'Grade 6',
                    'section'        => 'A',
                    'academic_year'  => '2026-2027',
                    'board'          => 'NCERT / CBSE',
                    'status'         => 'active',
                ]
            );

            // Sample 1 student in each class (Grades 1 to 5)
            $sampleStudents = [
                1 => ['name' => 'Aarav (Sample Grade 1)', 'email' => 'student.grade1@myskool.club', 'id_num' => 'STU-G1-001'],
                2 => ['name' => 'Ananya (Sample Grade 2)', 'email' => 'student.grade2@myskool.club', 'id_num' => 'STU-G2-001'],
                3 => ['name' => 'Vihaan (Sample Grade 3)', 'email' => 'student.grade3@myskool.club', 'id_num' => 'STU-G3-001'],
                4 => ['name' => 'Ishaan (Sample Grade 4)', 'email' => 'student.grade4@myskool.club', 'id_num' => 'STU-G4-001'],
                5 => ['name' => 'Riya (Sample Grade 5)',   'email' => 'student.grade5@myskool.club', 'id_num' => 'STU-G5-001'],
            ];

            foreach ($sampleStudents as $grNum => $sInfo) {
                $uSample = User::firstOrCreate(
                    ['email' => $sInfo['email']],
                    [
                        'name'              => $sInfo['name'],
                        'role'              => 'student',
                        'password'          => Hash::make('Password123!'),
                        'status'            => 'active',
                        'is_banned'         => 'no',
                        'phone'             => '+91987654321' . $grNum,
                        'email_verified_at' => now(),
                    ]
                );
                SchoolMember::updateOrCreate(
                    [
                        'school_id' => $school->id,
                        'user_id'   => $uSample->id,
                    ],
                    [
                        'role_in_school' => 'student',
                        'id_number'      => $sInfo['id_num'],
                        'grade'          => "Grade {$grNum}",
                        'section'        => 'A',
                        'academic_year'  => '2026-2027',
                        'board'          => 'CBSE',
                        'status'         => 'active',
                    ]
                );
            }

            // 4. Products / Courses
            $svLabCourse = Course::updateOrCreate(
                ['slug' => 'sv-lab-with-infra'],
                [
                    'title'          => 'SV Lab with Infrastructure (Skillvation Lab)',
                    'course_code'    => 'SV-LAB-INFRA',
                    'instructor_id'  => 1,
                    'category_id'    => 1,
                    'type'           => 'course',
                    'price'          => 150000.00,
                    'discount'       => 0,
                    'thumbnail'      => 'uploads/courses/lab-sv-infra.png',
                    'description'    => '<p>Complete turnkey Skillvation Lab with cutting-edge equipment, full physical infrastructure, robotics workbenches, and hands-on skill kits.</p>',
                    'seo_description'=> 'Complete turnkey Skillvation Lab with infrastructure and curriculum.',
                    'status'         => 'active',
                    'is_approved'    => 'approved',
                ]
            );

            $campsBundleCourse = Course::updateOrCreate(
                ['slug' => 'camps-complete-programme-grades-1-to-5'],
                [
                    'title'          => 'CAMPS Complete Programme (Grades 1 to 5)',
                    'course_code'    => 'CAMPS-ALL-GRADES',
                    'instructor_id'  => 1,
                    'category_id'    => 1,
                    'type'           => 'course',
                    'price'          => 25000.00,
                    'discount'       => 0,
                    'thumbnail'      => 'uploads/courses/camps/bundle.png',
                    'description'    => '<p>All five grades included together in the single complete CAMPS school package. Equipped with LATAA-OS AI teacher assistant, worksheets, and competency assessments.</p>',
                    'seo_description'=> 'CAMPS Complete Programme Grades 1 to 5.',
                    'status'         => 'active',
                    'is_approved'    => 'approved',
                ]
            );

            $u4tCourse = Course::updateOrCreate(
                ['slug' => 'upskill-4-teacher-u4t'],
                [
                    'title'          => 'Upskill 4 Teacher (U4T) - Professional Development Programme',
                    'course_code'    => 'U4T-PRO-DEV',
                    'instructor_id'  => 1,
                    'category_id'    => 1,
                    'type'           => 'course',
                    'price'          => 9999.00,
                    'discount'       => 0,
                    'thumbnail'      => 'designs/img/Upskill4Teacher-1.jpg',
                    'description'    => '<p>Comprehensive professional development programme for school educators aligned with CBSE Skill Education and NEP 2020 pedagogy.</p>',
                    'seo_description'=> 'Upskill 4 Teacher (U4T) professional development.',
                    'status'         => 'active',
                    'is_approved'    => 'approved',
                ]
            );

            $s2sCourse = Course::updateOrCreate(
                ['slug' => 'skill-2-skool-s2s'],
                [
                    'title'          => 'Skill 2 Skool (S2S) - Institutional Skill Framework',
                    'course_code'    => 'S2S-COMPLETE',
                    'instructor_id'  => 1,
                    'category_id'    => 1,
                    'type'           => 'course',
                    'price'          => 25000.00,
                    'discount'       => 0,
                    'thumbnail'      => 'designs/img/skill2school-2.jpeg',
                    'description'    => '<p>School-wide skill curriculum integrating experiential learning, LATAA-OS AI, and vocational preparedness across all stages.</p>',
                    'seo_description'=> 'Skill 2 Skool (S2S) Institutional Skill Framework.',
                    'status'         => 'active',
                    'is_approved'    => 'approved',
                ]
            );

            $integrateCreditsCourse = Course::updateOrCreate(
                ['slug' => 'integrate-my-curriculum-1000-credits-elif'],
                [
                    'title'          => 'Integrate My Curriculum - 1000 Credits (Publisher: Elif)',
                    'course_code'    => 'CURR-INTEGRATE-ELIF-1000',
                    'instructor_id'  => 1,
                    'category_id'    => 1,
                    'type'           => 'course',
                    'price'          => 1000.00,
                    'discount'       => 0,
                    'thumbnail'      => 'designs/img/credit-1000.png',
                    'description'    => '<p>1000 LATAA-OS Credits to integrate school curriculum with publisher <strong>Elif</strong>. Automated mapping to NEP 2020 and competency rubrics.</p>',
                    'seo_description'=> 'Integrate My Curriculum - 1000 Credits (Publisher: Elif).',
                    'status'         => 'active',
                    'is_approved'    => 'approved',
                ]
            );

            $createCreditsCourse = Course::updateOrCreate(
                ['slug' => 'create-my-curriculum-1000-credits-ncert-g6'],
                [
                    'title'          => 'Create My Curriculum - 1000 Credits (Grade 6, NCERT)',
                    'course_code'    => 'CURR-CREATE-NCERT-G6-1000',
                    'instructor_id'  => 1,
                    'category_id'    => 1,
                    'type'           => 'course',
                    'price'          => 1000.00,
                    'discount'       => 0,
                    'thumbnail'      => 'designs/img/credit-1000.png',
                    'description'    => '<p>1000 LATAA-OS Credits to author and generate dynamic curriculum for <strong>Grade 6 (NCERT)</strong>, including lesson plans, worksheets, and assessments.</p>',
                    'seo_description'=> 'Create My Curriculum - 1000 Credits (Grade 6, NCERT).',
                    'status'         => 'active',
                    'is_approved'    => 'approved',
                ]
            );

            // 5. School Orders & Enrollments
            $purchasedCourses = [
                $svLabCourse,
                $campsBundleCourse,
                Course::find(1),
                Course::find(10),
                Course::find(5),
                Course::find(13),
                Course::find(14),
                $u4tCourse,
                $s2sCourse,
                $integrateCreditsCourse,
                $createCreditsCourse,
            ];

            $totalPrice = collect($purchasedCourses)->filter()->sum('price');

            $schoolOrder = Order::updateOrCreate(
                ['invoice_id' => 'INV-MYSKOOL-2026-001'],
                [
                    'buyer_id'            => $school->id,
                    'seller_id'           => 1,
                    'status'              => 'completed',
                    'has_coupon'          => 0,
                    'payment_method'      => 'razorpay',
                    'payment_status'      => 'paid',
                    'payable_amount'      => $totalPrice,
                    'payable_with_charge' => $totalPrice,
                    'paid_amount'         => $totalPrice,
                    'conversion_rate'     => 1,
                    'payable_currency'    => 'INR',
                    'transaction_id'      => 'txn_myskool_live_' . Str::random(10),
                    'commission_rate'     => 0,
                    'payment_details'     => json_encode(['note' => 'Institutional purchase for Myskool International: SV Lab, CAMPS, U4T, S2S & Curriculum Credits']),
                ]
            );

            foreach ($purchasedCourses as $pCourse) {
                if (!$pCourse) continue;
                OrderItem::firstOrCreate(
                    [
                        'order_id'  => $schoolOrder->id,
                        'course_id' => $pCourse->id,
                    ],
                    [
                        'qty'             => 1,
                        'price'           => $pCourse->price,
                        'item_type'       => 'course',
                        'commission_rate' => 0,
                    ]
                );

                Enrollment::updateOrCreate(
                    [
                        'order_id'  => $schoolOrder->id,
                        'user_id'   => $school->id,
                        'course_id' => $pCourse->id,
                    ],
                    [
                        'has_access' => 1,
                    ]
                );
            }

            // 6. Assign All to Teacher Saraswathi
            foreach ($purchasedCourses as $pCourse) {
                if (!$pCourse) continue;
                SchoolCourseAssignment::updateOrCreate(
                    [
                        'school_id' => $school->id,
                        'course_id' => $pCourse->id,
                        'user_id'   => $teacher->id,
                    ],
                    [
                        'role_in_school' => 'teacher',
                        'order_id'       => $schoolOrder->id,
                        'status'         => 'active',
                        'assigned_at'    => now(),
                    ]
                );
            }

            // 7. Assign STEM Courses to Students & Saraswathi
            $stemCourseIds = [7, 12, 33, 34, 35, 36, 37, 38, 43, 44, $createCreditsCourse->id];
            foreach ($stemCourseIds as $sCourseId) {
                $c = Course::find($sCourseId);
                if (!$c) continue;

                Enrollment::firstOrCreate(
                    [
                        'order_id'  => $schoolOrder->id,
                        'user_id'   => $school->id,
                        'course_id' => $c->id,
                    ],
                    ['has_access' => 1]
                );

                SchoolCourseAssignment::updateOrCreate(
                    [
                        'school_id' => $school->id,
                        'course_id' => $c->id,
                        'user_id'   => $student->id,
                    ],
                    [
                        'role_in_school' => 'student',
                        'order_id'       => $schoolOrder->id,
                        'status'         => 'active',
                        'assigned_at'    => now(),
                    ]
                );

                SchoolCourseAssignment::updateOrCreate(
                    [
                        'school_id' => $school->id,
                        'course_id' => $c->id,
                        'user_id'   => $teacher->id,
                    ],
                    [
                        'role_in_school' => 'teacher',
                        'order_id'       => $schoolOrder->id,
                        'status'         => 'active',
                        'assigned_at'    => now(),
                    ]
                );
            }

            // 8. Assessment Created by Teacher Saraswathi
            $chapter = CourseChapter::where('course_id', 7)->first();
            if (!$chapter) {
                $chapter = CourseChapter::create([
                    'course_id'     => 7,
                    'instructor_id' => $teacher->id,
                    'title'         => 'STEM & Kaushal Bodh Practical Projects',
                    'status'        => 'active',
                    'order'         => 1,
                ]);
            }

            $chapterItem = CourseChapterItem::firstOrCreate(
                [
                    'chapter_id' => $chapter->id,
                    'type'       => 'quiz',
                ],
                [
                    'instructor_id' => $teacher->id,
                    'order'         => 99,
                ]
            );

            $teacherQuiz = Quiz::updateOrCreate(
                [
                    'chapter_item_id' => $chapterItem->id,
                    'course_id'       => 7,
                ],
                [
                    'instructor_id' => $teacher->id,
                    'chapter_id'    => $chapter->id,
                    'title'         => 'Teacher Assessment: STEM & Kaushal Bodh Competency (Created by Saraswathi)',
                    'time'          => '30 mins',
                    'attempt'       => '3',
                    'pass_mark'     => '60',
                    'total_mark'    => '100',
                    'status'        => 'active',
                ]
            );

            $q1 = QuizQuestion::updateOrCreate(
                [
                    'quiz_id' => $teacherQuiz->id,
                    'title'   => 'What is the primary role of sensors in an automated STEM IoT irrigation system?',
                ],
                ['type' => 'multiple', 'grade' => 25]
            );
            QuizQuestionAnswer::updateOrCreate(
                ['question_id' => $q1->id, 'title' => 'Real-time soil moisture and environment data acquisition'],
                ['correct' => 1]
            );
            QuizQuestionAnswer::updateOrCreate(
                ['question_id' => $q1->id, 'title' => 'Supplying physical water pressure'],
                ['correct' => 0]
            );
            QuizQuestionAnswer::updateOrCreate(
                ['question_id' => $q1->id, 'title' => 'Decorative landscaping display'],
                ['correct' => 0]
            );

            $q2 = QuizQuestion::updateOrCreate(
                [
                    'quiz_id' => $teacherQuiz->id,
                    'title'   => 'Which control structure in robotics programming allows repeated sensor checking?',
                ],
                ['type' => 'multiple', 'grade' => 25]
            );
            QuizQuestionAnswer::updateOrCreate(
                ['question_id' => $q2->id, 'title' => 'While / For Loop iterations'],
                ['correct' => 1]
            );
            QuizQuestionAnswer::updateOrCreate(
                ['question_id' => $q2->id, 'title' => 'Single Variable Declaration'],
                ['correct' => 0]
            );

            QuizQuestion::updateOrCreate(
                [
                    'quiz_id' => $teacherQuiz->id,
                    'title'   => 'How does organic composting in the school kitchen garden reinforce ecological nutrient cycles?',
                ],
                ['type' => 'descriptive', 'grade' => 25]
            );

            QuizQuestion::updateOrCreate(
                [
                    'quiz_id' => $teacherQuiz->id,
                    'title'   => 'Explain the three essential laboratory safety procedures before testing high-torque DC motors.',
                ],
                ['type' => 'descriptive', 'grade' => 25]
            );

            // 9. Student Self-Assessment & Shop Purchase
            $selfAssessmentChapterItem = CourseChapterItem::firstOrCreate(
                [
                    'chapter_id' => $chapter->id,
                    'type'       => 'quiz',
                    'order'      => 100,
                ],
                ['instructor_id' => 1]
            );

            $studentSelfQuiz = Quiz::updateOrCreate(
                [
                    'chapter_item_id' => $selfAssessmentChapterItem->id,
                    'title'           => 'Student Self-Assessment: Grade 6 STEM & Kaushal Bodh Practical Reflection',
                ],
                [
                    'instructor_id' => 1,
                    'chapter_id'    => $chapter->id,
                    'course_id'     => 7,
                    'time'          => '20 mins',
                    'attempt'       => '5',
                    'pass_mark'     => '50',
                    'total_mark'    => '100',
                    'status'        => 'active',
                ]
            );

            QuizResult::updateOrCreate(
                [
                    'user_id' => $student->id,
                    'quiz_id' => $studentSelfQuiz->id,
                ],
                [
                    'user_grade' => 88,
                    'status'     => 'pass',
                    'result'     => json_encode([
                        'student_name'      => 'Rahul',
                        'school'            => 'Myskool International',
                        'grade'             => 'Grade 6',
                        'reflection_score'  => 88,
                        'competency_status' => 'Proficient',
                        'answers'           => [
                            'q1_reflection' => 'Successfully connected moisture sensor to micro-controller and calibrated dry/wet thresholds.',
                            'q2_hands_on'   => 'Implemented loop algorithm to trigger alert buzzer when moisture dropped below 20%.',
                            'q3_safety'     => 'Followed anti-static grounding and polarity guidelines before powering on circuit.',
                        ],
                        'completed_at'      => now()->toIso8601String(),
                    ]),
                ]
            );

            $shopOrder = ShopOrder::updateOrCreate(
                ['order_number' => 'ORD-SHOP-RAHUL-G6'],
                [
                    'user_id'         => $student->id,
                    'customer_name'   => 'Rahul',
                    'customer_email'  => 'rahul@myskool.club',
                    'customer_phone'  => '+919876543212',
                    'pincode'         => '110001',
                    'city'            => 'New Delhi',
                    'state'           => 'Delhi',
                    'country'         => 'India',
                    'address'         => 'Student Hostel Block B, Myskool International, Sector 62',
                    'address_type'    => 'School/Hostel',
                    'payment_method'  => 'razorpay',
                    'payment_id'      => 'pay_shop_rahul_' . Str::random(8),
                    'payment_status'  => 'paid',
                    'order_status'    => 'processing',
                    'subtotal'        => 2499.00,
                    'shipping_cost'   => 0.00,
                    'discount'        => 0.00,
                    'total_amount'    => 2499.00,
                    'currency'        => 'INR',
                    'courier_partner' => 'BlueDart Express',
                    'tracking_number' => 'BD-MYK-872910',
                    'admin_notes'     => 'Student STEM kit ordered by Rahul for Grade 6 hands-on robotics and Kaushal Bodh curriculum.',
                ]
            );

            ShopOrderItem::updateOrCreate(
                [
                    'shop_order_id' => $shopOrder->id,
                    'product_title' => 'SkillBox STEM Robotic Starter Kit - Grade 6',
                ],
                [
                    'product_id'    => 1,
                    'product_image' => 'designs/img/shop-item-1.png',
                    'unit_price'    => 2499.00,
                    'quantity'      => 1,
                    'total_price'   => 2499.00,
                ]
            );
        });
    }
}
