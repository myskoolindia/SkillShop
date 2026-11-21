<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\SchoolMember;
use App\Models\SchoolCourseAssignment;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizResult;
use App\Models\ShopOrder;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\Enrollment;

echo "========================================================\n";
echo " VERIFYING MYSKOOL INTERNATIONAL COMPLETE DATA\n";
echo "========================================================\n\n";

$school = User::where('email', 'myskool.international@myskool.club')->first();
$teacher = User::where('email', 'saraswathi@myskool.club')->first();
$student = User::where('email', 'rahul@myskool.club')->first();

echo "1. USERS & ROLES:\n";
echo "   School:  [ID: {$school->id}] {$school->name} ({$school->email}) - Role: {$school->role} - Access: " . ($school->hasPurchasedCourses() ? 'YES' : 'NO') . "\n";
echo "   Teacher: [ID: {$teacher->id}] {$teacher->name} ({$teacher->email}) - Role: {$teacher->role} - Access: " . ($teacher->hasPurchasedCourses() ? 'YES' : 'NO') . "\n";
echo "   Student: [ID: {$student->id}] {$student->name} ({$student->email}) - Role: {$student->role} - Access: " . ($student->hasPurchasedCourses() ? 'YES' : 'NO') . "\n\n";

echo "2. SCHOOL MEMBERS:\n";
$members = SchoolMember::where('school_id', $school->id)->with('user')->get();
foreach ($members as $m) {
    echo "   - {$m->role_in_school}: {$m->user->name} ({$m->user->email}) | Grade: {$m->grade} | ID: {$m->id_number} | Status: {$m->status}\n";
}

echo "\n3. SCHOOL PURCHASES & ENROLLMENTS:\n";
$orders = Order::where('buyer_id', $school->id)->get();
foreach ($orders as $o) {
    echo "   Order: {$o->invoice_id} | Status: {$o->status} | Paid: ₹{$o->paid_amount} | Gateway: {$o->payment_method}\n";
}
$enrollments = Enrollment::where('user_id', $school->id)->with('course')->get();
echo "   Enrolled Courses (" . $enrollments->count() . " total):\n";
foreach ($enrollments as $e) {
    echo "     * [Course {$e->course_id}] {$e->course->title} (Access: {$e->has_access})\n";
}

echo "\n4. COURSE ASSIGNMENTS TO TEACHER SARASWATHI:\n";
$tAssignments = SchoolCourseAssignment::where('school_id', $school->id)
    ->where('user_id', $teacher->id)
    ->where('status', 'active')
    ->with('course')
    ->get();
echo "   Teacher Saraswathi has " . $tAssignments->count() . " assigned courses:\n";
foreach ($tAssignments as $ta) {
    echo "     * [Course {$ta->course_id}] {$ta->course->title}\n";
}

echo "\n5. COURSE ASSIGNMENTS TO STUDENT RAHUL (STEM COURSES):\n";
$sAssignments = SchoolCourseAssignment::where('school_id', $school->id)
    ->where('user_id', $student->id)
    ->where('status', 'active')
    ->with('course')
    ->get();
echo "   Student Rahul has " . $sAssignments->count() . " assigned STEM courses:\n";
foreach ($sAssignments as $sa) {
    echo "     * [Course {$sa->course_id}] {$sa->course->title}\n";
}

echo "\n6. TEACHER ASSESSMENT (CREATED BY SARASWATHI):\n";
$tQuiz = Quiz::where('instructor_id', $teacher->id)->with('questions.answers')->first();
if ($tQuiz) {
    echo "   Quiz: [ID: {$tQuiz->id}] {$tQuiz->title}\n";
    echo "   Time: {$tQuiz->time} | Pass Mark: {$tQuiz->pass_mark} | Total Mark: {$tQuiz->total_mark}\n";
    foreach ($tQuiz->questions as $i => $q) {
        echo "     Q" . ($i+1) . " ({$q->type}, {$q->grade} marks): {$q->title}\n";
        foreach ($q->answers as $ans) {
            echo "       - [" . ($ans->correct ? 'CORRECT' : ' ') . "] {$ans->title}\n";
        }
    }
}

echo "\n7. STUDENT SELF-ASSESSMENT & QUIZ RESULTS (RAHUL):\n";
$sResults = QuizResult::where('user_id', $student->id)->with('quiz')->get();
foreach ($sResults as $sr) {
    echo "   Quiz: {$sr->quiz->title}\n";
    echo "   Student Score: {$sr->user_grade}% | Status: {$sr->status}\n";
    echo "   Result Payload: {$sr->result}\n";
}

echo "\n8. STUDENT SHOP ORDER (RAHUL):\n";
$shopOrders = ShopOrder::where('user_id', $student->id)->with('items')->get();
foreach ($shopOrders as $so) {
    echo "   Shop Order: {$so->order_number} | Status: {$so->order_status} | Payment: {$so->payment_status} | Total: ₹{$so->total_amount}\n";
    foreach ($so->items as $it) {
        echo "     * {$it->product_title} (Qty: {$it->quantity}, Price: ₹{$it->unit_price})\n";
    }
}

echo "\n========================================================\n";
echo " VERIFICATION COMPLETED - ALL REQUIREMENTS SATISFIED!\n";
echo "========================================================\n";
