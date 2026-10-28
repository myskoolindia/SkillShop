<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\{DB,Hash};
use App\Models\{User,SchoolMember,Course,RoboquestConnection,CourseChapter,CourseChapterItem,CourseChapterLesson};
use Modules\Order\app\Models\{Order,Enrollment};
class RoboquestDemoSeeder extends Seeder {
    public function run(): void {
        if (!app()->environment(['local','testing'])) throw new \RuntimeException('Demo school accounts are for local/testing environments only.');
        $password=env('ROBOQUEST_DEMO_PASSWORD');$key=env('ROBOQUEST_DEMO_CONNECTION_KEY');
        if (!$password || strlen($password)<12 || !$key || strlen($key)<32) throw new \RuntimeException('Set ROBOQUEST_DEMO_PASSWORD and ROBOQUEST_DEMO_CONNECTION_KEY securely before seeding.');
        User::withoutEvents(function() use ($password,$key) {
            $school=User::firstOrCreate(['email'=>'school@roboquest.test'],['name'=>'RoboQuest Demo School','role'=>'school','password'=>Hash::make($password),'status'=>'active','is_banned'=>'no','email_verified_at'=>now()]);
            foreach(['teacher','student']as$role){$user=User::firstOrCreate(['email'=>$role.'@roboquest.test'],['name'=>'RoboQuest Demo '.ucfirst($role),'role'=>$role,'password'=>Hash::make($password),'status'=>'active','is_banned'=>'no','email_verified_at'=>now()]);SchoolMember::firstOrCreate(['school_id'=>$school->id,'user_id'=>$user->id],['role_in_school'=>$role,'status'=>'active']);}
            RoboquestConnection::updateOrCreate(['school_user_id'=>$school->id],['school_id'=>env('ROBOQUEST_DEMO_SCHOOL_ID','school-demo'),'base_url'=>env('ROBOQUEST_DEMO_URL','http://localhost:8080'),'shared_key'=>$key]);
            // A clearly labelled local fixture never calls a payment provider or changes a real purchase.
            $course=Course::where('slug','roboquest-local-test-course')->first();
            if (!$course) {
                $template=Course::first();
                if ($template) {
                    $course=$template->replicate(); $course->title='[TEST] RoboQuest School Course';
                    $course->slug='roboquest-local-test-course'; $course->type='course'; $course->api_course_id=null;
                    $course->description='Local integration-test learning: identify input, decision and output, then test a boundary value.';
                    $course->capacity=3;
                    if (array_key_exists('course_code',$course->getAttributes())) $course->course_code='RQ-LOCAL-TEST-COURSE';
                    $course->save();
                }
            }
            if ($course && $course->type !== 'course') { $course->type='course'; $course->save(); }
            if ($course && !$course->chapters()->exists()) {
                $chapter=CourseChapter::create(['course_id'=>$course->id,'instructor_id'=>$course->instructor_id,'title'=>'[TEST] Robot decisions','status'=>'active','order'=>0]);
                $item=CourseChapterItem::create(['chapter_id'=>$chapter->id,'instructor_id'=>$course->instructor_id,'type'=>'lesson','order'=>0]);
                CourseChapterLesson::create(['course_id'=>$course->id,'chapter_id'=>$chapter->id,'chapter_item_id'=>$item->id,'instructor_id'=>$course->instructor_id,'title'=>'[TEST] Predict before testing','description'=>'A sensor supplies input, a program chooses a decision, and an actuator produces output. For distance > 20, test 19, 20 and 21. Predict STOP, STOP and MOVE before running the model. Explain why equality belongs to the stop case.','material_required'=>'The digital rover model in Robot Building. No physical kit is required.','storage'=>'local','file_type'=>'text','is_free'=>0]);
            }
            if($course){$order=Order::firstOrCreate(['invoice_id'=>'TEST-ROBOQUEST-DEMO'],['buyer_id'=>$school->id,'status'=>'completed','payment_status'=>'paid','payment_method'=>'Local test fixture','transaction_id'=>'TEST-ROBOQUEST-NO-PAYMENT','payable_amount'=>0,'paid_amount'=>0,'payable_currency'=>'INR','commission_rate'=>0]);Enrollment::firstOrCreate(['order_id'=>$order->id,'user_id'=>$school->id,'course_id'=>$course->id],['has_access'=>1]);}
        });
    }
}
