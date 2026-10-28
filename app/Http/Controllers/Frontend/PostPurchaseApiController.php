<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB,Hash};
use Modules\Order\app\Models\{Order,Enrollment};
class PostPurchaseApiController extends Controller {
    public function login(Request $request) {
        $data=$request->validate(['login'=>'required|string|max:200','password'=>'required|string|max:128']);
        $user=User::where('email',$data['login'])->orWhere('phone',$data['login'])->first();
        abort_unless($user && Hash::check($data['password'],$user->password) && $user->status==='active' && !in_array($user->is_banned,[1,'1','yes'],true),401,'Invalid credentials.');
        return response()->json(['status'=>'success','token'=>$user->createToken('post-purchase',['purchases:read'],now()->addHours(2))->plainTextToken,
            'user'=>['id'=>$user->id,'name'=>$user->name,'email'=>$user->email,'role'=>$user->role]])->header('Cache-Control','no-store');
    }
    private function orders(Request $request) {
        abort_unless($request->user()->tokenCan('purchases:read'),403);
        return Order::where('buyer_id',$request->user()->id)->where('status','completed')->where('payment_status','paid');
    }
    public function myOrders(Request $request) {
        $orders=$this->orders($request)->with('orderItems')->latest()->paginate(20);
        // Gateway payloads and payment credentials are not part of this read API.
        $orders->getCollection()->each(fn($order)=>$order->makeHidden(['payment_details']));
        return response()->json(['status'=>'success','data'=>$orders]);
    }
    public function order(Request $request,string $invoice) {
        $order=$this->orders($request)->where('invoice_id',$invoice)->with('orderItems')->firstOrFail()->makeHidden(['payment_details']);
        return response()->json(['status'=>'success','data'=>$order]);
    }
    public function enrollments(Request $request) {
        $orders=$this->orders($request)->select('id');
        $rows=Enrollment::where('user_id',$request->user()->id)->where('has_access',1)->whereIn('order_id',$orders)->with('course:id,title,slug,type,api_course_id')->paginate(20);
        return response()->json(['status'=>'success','data'=>$rows]);
    }
    public function summary(Request $request) {
        $orders=$this->orders($request);
        return response()->json(['status'=>'success','data'=>['orders_count'=>(clone $orders)->count(),
            'total_spent_by_currency'=>(clone $orders)->select('payable_currency',DB::raw('SUM(paid_amount) as total'))->groupBy('payable_currency')->get(),
            'enrolled_courses'=>Enrollment::where('user_id',$request->user()->id)->where('has_access',1)->whereIn('order_id',(clone $orders)->select('id'))->distinct()->count('course_id')]]);
    }
}
