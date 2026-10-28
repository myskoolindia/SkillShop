<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\{Course,RoboquestConnection};
use Modules\Order\app\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class RoboquestPlatformController extends Controller {
    public function entitlements(Request $request) {
        $input=$request->validate(['schoolId'=>'required|string|max:80','timestamp'=>'required|integer','nonce'=>'required|string|min:16|max:60']);
        $key=(string)config('services.roboquest.platform_key');
        abort_unless(strlen($key)>=32 && abs(time()-$input['timestamp'])<=120 && hash_equals(hash_hmac('sha256',$request->getContent(),$key),(string)$request->header('X-RoboQuest-Signature')),401,'Invalid platform credentials.');
        $connection=RoboquestConnection::where('school_id',$input['schoolId'])->first();abort_unless($connection,404,'School is not connected.');
        DB::table('roboquest_request_nonces')->where('expires_at','<',now())->delete();
        abort_unless(DB::table('roboquest_request_nonces')->insertOrIgnore(['connection_id'=>$connection->id,'nonce'=>'platform:'.$input['nonce'],'expires_at'=>now()->addMinutes(3)]),409,'Request already used.');
        $ids=Enrollment::where('user_id',$connection->school_user_id)->where('has_access',1)->whereIn('order_id',DB::table('orders')->where('buyer_id',$connection->school_user_id)->where('status','completed')->where('payment_status','paid')->select('id'))->pluck('course_id');
        $apiIds=Course::whereIn('id',$ids)->whereNotNull('api_course_id')->pluck('api_course_id')->unique()->map(fn($id)=>(string)$id)->values();
        return response()->json(['schoolId'=>$connection->school_id,'courseIds'=>$apiIds])->header('Cache-Control','no-store');
    }
}
