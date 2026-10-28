<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\Course;
use Modules\Order\app\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Cache,DB,Log};
class RoboquestSalesMappingController extends Controller {
    public function handle(Request $request) {
        $input=$request->validate(['action'=>'required|in:list,map','timestamp'=>'required|integer','nonce'=>'required|string|min:16|max:60','localCourseId'=>'required_if:action,map|integer|min:1','apiCourseId'=>'required_if:action,map|integer|min:1']);
        $key=(string)config('services.roboquest.platform_key');
        abort_unless(strlen($key)>=32&&abs(time()-$input['timestamp'])<=120&&hash_equals(hash_hmac('sha256',$request->getContent(),$key),(string)$request->header('X-RoboQuest-Signature')),401,'Invalid platform credentials.');
        abort_unless(Cache::add('roboquest-platform-mapping:'.$input['nonce'],true,now()->addMinutes(3)),409,'Request already used.');
        if($input['action']==='map'){
            Cache::lock('roboquest-platform-sales-mapping',10)->block(5,function()use($input){DB::transaction(function()use($input){
                $course=Course::whereKey($input['localCourseId'])->lockForUpdate()->firstOrFail();
                abort_if(Course::where('api_course_id',$input['apiCourseId'])->where('id','<>',$course->id)->exists(),409,'This API product is already mapped to another local course.');
                $paid=Enrollment::where('course_id',$course->id)->whereIn('order_id',DB::table('orders')->where('status','completed')->where('payment_status','paid')->select('id'))->exists();
                abort_if($paid&&$course->api_course_id!==null&&(string)$course->api_course_id!==(string)$input['apiCourseId'],409,'A sold course cannot be remapped to a different API product. Create a separate sales course.');
                $old=$course->api_course_id;$course->api_course_id=$input['apiCourseId'];$course->save();Log::info('Platform updated course API mapping',['localCourseId'=>$course->id,'oldApiId'=>$old,'apiCourseId'=>$input['apiCourseId']]);
            });});
        }
        return response()->json(['courses'=>Course::orderBy('id')->get(['id','title','api_course_id','status']),'createUrl'=>url('/admin/courses/create')])->header('Cache-Control','no-store');
    }
}
