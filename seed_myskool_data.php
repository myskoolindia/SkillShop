<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

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

echo "========================================================\n";
echo " SEEDING MYSKOOL INTERNATIONAL & COMPLETE SCENARIO DATA\n";
echo "========================================================\n\n";

DB::beginTransaction();

try {
    // ---------------------------------------------------------
    // 1. CREATE / RETRIEVE SCHOOL: Myskool International
    // ---------------------------------------------------------
    $schoolEmail = 'myskool.international@myskool.club';
    $school = User::where('email', $schoolEmail)->first();
    if (!$school) {
        $school = User::create([
            'name'              => 'Myskool International',
            'email'             => $schoolEmail,
            'role'              => 'school',
            'password'          => Hash::make('Password123!'),
            'status'            => 'active',
            'is_banned'         => 'no',
            'phone'             => '+919876543210',
            'school_name'       => 'Myskool International',
            'contact_person'    => 'Principal Dr. V. Sharma',
            'registration_number'=> 'CBSE-AFF-2026-9871',
            'address'           => 'Knowledge Boulevard, Sector 62, New Delhi, India',
            'city'              => 'New Delhi',
            'state'             => 'Delhi',
            'country_id'        => 1,
            'email_verified_at' => now(),
        ]);
        echo "[+] Created School: Myskool International ({$schoolEmail})\n";
    } else {
        $school->update([
            'name'              => 'Myskool International',
            'role'              => 'school',
            'status'            => 'active',
            'password'          => Hash::make('Password123!'),
            'school_name'       => 'Myskool International',
            'email_verified_at' => now(),
        ]);
        echo "[*] Updated existing School: Myskool International ({$schoolEmail})\n";
    }

    // Ensure RoboQuest Connection for School
    $schoolIdString = 'school-' . $school->id;
    $rqConnection = RoboquestConnection::updateOrCreate(
        ['school_user_id' => $school->id],
        [
            'school_id'  => $schoolIdString,
            'base_url'   => env('DEFAULT_ROBOQUEST_URL', 'http://localhost:8080'),
            'shared_key' => env('DEFAULT_ROBOQUEST_KEY', 'default-key-skillvation-collab-shared-secret-32'),
        ]
    );
    echo "[+] School RoboQuest Connection configured: {$schoolIdString}\n";

    // ---------------------------------------------------------
    // 2. CREATE / RETRIEVE TEACHER: Saraswathi
    // ---------------------------------------------------------
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
        echo "[+] Created Teacher: Saraswathi ({$teacherEmail})\n";
    } else {
        $teacher->update([
            'name'              => 'Saraswathi',
            'role'              => 'teacher',
            'status'            => 'active',
            'password'          => Hash::make('Password123!'),
            'email_verified_at' => now(),
        ]);
        echo "[*] Updated Teacher: Saraswathi ({$teacherEmail})\n";
    }

    // Link Teacher to School in school_members
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
    echo "[+] Linked Teacher Saraswathi to Myskool International\n";

    // ---------------------------------------------------------
    // 3. CREATE / RETRIEVE STUDENT: Rahul (and 1 sample in each class)
    // ---------------------------------------------------------
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
        echo "[+] Created Student: Rahul ({$studentEmail})\n";
    } else {
        $student->update([
            'name'              => 'Rahul',
            'role'              => 'student',
            'status'            => 'active',
            'password'          => Hash::make('Password123!'),
            'email_verified_at' => now(),
        ]);
        echo "[*] Updated Student: Rahul ({$studentEmail})\n";
    }

    // Link Rahul as Student in Grade 6 (CBSE)
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
    echo "[+] Linked Student Rahul to Myskool International (Grade 6)\n";

    // Also add sample students for each primary grade (Grade 1 to 5) as specified: "Sample 1 students in each class"
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
    echo "[+] Created/linked sample students for Grades 1 to 5\n";

    // ---------------------------------------------------------
    // 4. CREATE / ENSURE ALL COURSES MENTIONED IN NOTE EXIST
    // ---------------------------------------------------------
    // ① SV Lab with infra
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
    echo "[+] Course Ready: {$svLabCourse->title} (ID: {$svLabCourse->id})\n";

    // ② CAMPS Grades 1 to 5
    // IDs 1, 10, 5, 13, 14
    $campsGradeIds = [1, 10, 5, 13, 14];
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
    echo "[+] Course Ready: {$campsBundleCourse->title} (ID: {$campsBundleCourse->id})\n";

    // ③ U4T (Upskill 4 Teacher / Upskill 4 School)
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
    echo "[+] Course Ready: {$u4tCourse->title} (ID: {$u4tCourse->id})\n";

    // ⑤ S2S (Skill 2 Skool)
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
    echo "[+] Course Ready: {$s2sCourse->title} (ID: {$s2sCourse->id})\n";

    // ⑥ Integrate my curriculum 1000 credits (Publisher: Elif)
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
    echo "[+] Course Ready: {$integrateCreditsCourse->title} (ID: {$integrateCreditsCourse->id})\n";

    // ⑦ Create my curriculum 1000 credits (Grade 6, NCERT)
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
    echo "[+] Course Ready: {$createCreditsCourse->title} (ID: {$createCreditsCourse->id})\n";

    // ---------------------------------------------------------
    // 5. SCHOOL PURCHASES & ORDER (All items from note)
    // ---------------------------------------------------------
    // Items to purchase:
    // 1. SV lab with infra ($svLabCourse)
    // 2. CAMPS Grade 1-5 ($campsBundleCourse + Grade 1-5 individual courses)
    // 3. U4T ($u4tCourse)
    // 5. S2S ($s2sCourse)
    // 6. Integrate my curriculum 1000 credits ($integrateCreditsCourse)
    // 7. Create my curriculum 1000 credits ($createCreditsCourse)

    $purchasedCourses = [
        $svLabCourse,
        $campsBundleCourse,
        Course::find(1),  // CAMPS Grade 1
        Course::find(10), // CAMPS Grade 2
        Course::find(5),  // CAMPS Grade 3
        Course::find(13), // CAMPS Grade 4
        Course::find(14), // CAMPS Grade 5
        $u4tCourse,
        $s2sCourse,
        $integrateCreditsCourse,
        $createCreditsCourse,
    ];

    $totalPrice = collect($purchasedCourses)->sum('price');

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
    echo "[+] Created/Updated School Order: {$schoolOrder->invoice_id} (Total: ₹{$totalPrice})\n";

    // Order items and enrollments for school
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
    echo "[+] Order Items and School Enrollments verified for all purchased items\n";

    // ---------------------------------------------------------
    // 6. ASSIGN ALL TO TEACHER SARASWATHI
    // ---------------------------------------------------------
    // "Assign all to Teacher Saraswathi"
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
    echo "[+] Assigned ALL purchased courses/curricula to Teacher Saraswathi\n";

    // ---------------------------------------------------------
    // 7. ASSIGN STEM TO STUDENTS [all subjects]
    // ---------------------------------------------------------
    // "Assign STEM to Students [all subjects.]"
    // STEM Course IDs in system:
    // 7: Grade 6: Kaushal Bodh / Maker & Vocational
    // 12: Application of satellites
    // 33: Artificial Intelligence (AI)
    // 34: Design Thinking
    // 35: Information Technology
    // 36: Coding
    // 37: Data Science
    // 38: Ar/VR
    // 43: Rockets
    // 44: Satellites
    // Plus Create My Curriculum Grade 6 NCERT
    $stemCourseIds = [7, 12, 33, 34, 35, 36, 37, 38, 43, 44, $createCreditsCourse->id];

    // Ensure school is also enrolled in these STEM courses so it can assign them
    foreach ($stemCourseIds as $sCourseId) {
        $c = Course::find($sCourseId);
        if (!$c) continue;
        Enrollment::firstOrCreate(
            [
                'order_id'  => $schoolOrder->id,
                'user_id'   => $school->id,
                'course_id' => $c->id,
            ],
            [
                'has_access' => 1,
            ]
        );

        // Assign to Student Rahul
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

        // Also assign to Teacher Saraswathi so she can teach all STEM subjects
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
    echo "[+] Assigned all STEM subjects (AI, Coding, Robotics, Data Science, Space, Kaushal Bodh) to Student Rahul & Teacher Saraswathi\n";

    // ---------------------------------------------------------
    // 8. CREATE ASSESSMENT BY TEACHER LOGIN
    // ---------------------------------------------------------
    // "Create assessment by teacher login"
    // Use Grade 6 Vocational Education (Course 7) & Chapter 11
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

    // Questions for Teacher Assessment
    $q1 = QuizQuestion::updateOrCreate(
        [
            'quiz_id' => $teacherQuiz->id,
            'title'   => 'What is the primary role of sensors in an automated STEM IoT irrigation system?',
        ],
        [
            'type'  => 'multiple',
            'grade' => 25,
        ]
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
        [
            'type'  => 'multiple',
            'grade' => 25,
        ]
    );
    QuizQuestionAnswer::updateOrCreate(
        ['question_id' => $q2->id, 'title' => 'While / For Loop iterations'],
        ['correct' => 1]
    );
    QuizQuestionAnswer::updateOrCreate(
        ['question_id' => $q2->id, 'title' => 'Single Variable Declaration'],
        ['correct' => 0]
    );
    QuizQuestionAnswer::updateOrCreate(
        ['question_id' => $q2->id, 'title' => 'Static Comment Line'],
        ['correct' => 0]
    );

    $q3 = QuizQuestion::updateOrCreate(
        [
            'quiz_id' => $teacherQuiz->id,
            'title'   => 'How does organic composting in the school kitchen garden reinforce ecological nutrient cycles?',
        ],
        [
            'type'  => 'descriptive',
            'grade' => 25,
        ]
    );

    $q4 = QuizQuestion::updateOrCreate(
        [
            'quiz_id' => $teacherQuiz->id,
            'title'   => 'Explain the three essential laboratory safety procedures before testing high-torque DC motors.',
        ],
        [
            'type'  => 'descriptive',
            'grade' => 25,
        ]
    );
    echo "[+] Teacher Assessment created by Saraswathi with 4 graded questions (ID: {$teacherQuiz->id})\n";

    // ---------------------------------------------------------
    // 9. STUDENT SELF-ASSESSMENT & SHOP PURCHASE
    // ---------------------------------------------------------
    // "Create self assessment by student & purchase products from shop."
    $selfAssessmentChapterItem = CourseChapterItem::firstOrCreate(
        [
            'chapter_id' => $chapter->id,
            'type'       => 'quiz',
            'order'      => 100,
        ],
        [
            'instructor_id' => 1,
        ]
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

    // Record Rahul's completed self-assessment in quiz_results
    $selfAssessmentResult = QuizResult::updateOrCreate(
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
    echo "[+] Student Self-Assessment recorded for Rahul (Score: 88%, Status: PASS)\n";

    // Student Shop Purchase in shop_orders & shop_order_items
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
            'tracking_number' => 'BD-MYK-' . rand(100000, 999999),
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
    echo "[+] Student Shop Order placed: {$shopOrder->order_number} (Item: SkillBox STEM Robotic Starter Kit - Grade 6, Total: ₹2,499)\n";

    DB::commit();

    echo "\n========================================================\n";
    echo " ALL DATA SEEDED AND LINKED SUCCESSFULLY!\n";
    echo "========================================================\n";

} catch (\Throwable $e) {
    DB::rollBack();
    echo "\n[ERROR] Exception during seeding: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
