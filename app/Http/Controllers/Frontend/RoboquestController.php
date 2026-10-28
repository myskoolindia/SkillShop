<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\{RoboquestConnection, User};
use App\Services\RoboquestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoboquestController extends Controller {
    public function signIn(Request $request) {
        $role = in_array($request->query('role'), ['teacher','student','school'], true) ? $request->query('role') : 'student';
        return response()->view('auth.roboquest-signin', compact('role'))->header('Cache-Control','no-store');
    }
    public function settings(Request $request) {
        abort_unless($request->user()->role === 'school', 403);
        $connection = RoboquestConnection::where('school_user_id', $request->user()->id)->first();
        return view('frontend.school-dashboard.roboquest', compact('connection'));
    }
    public function save(Request $request) {
        abort_unless($request->user()->role === 'school', 403);
        $data = $request->validate(['school_id'=>'required|regex:/^[A-Za-z0-9_-]{1,80}$/','base_url'=>'required|url|max:500','shared_key'=>'nullable|string|min:32|max:200']);
        $url = parse_url($data['base_url']);
        abort_unless(!isset($url['user']) && !isset($url['pass']) && !isset($url['query']) && !isset($url['fragment']) && empty(trim($url['path'] ?? '', '/')), 422, 'Use the school server origin without a path or credentials.');
        abort_unless(($url['scheme'] ?? '') === 'https' || (($url['scheme'] ?? '') === 'http' && in_array($url['host'] ?? '', ['localhost','127.0.0.1','[::1]'])), 422, 'Use HTTPS, or HTTP on localhost for testing.');
        $connection = RoboquestConnection::firstOrNew(['school_user_id'=>$request->user()->id]);
        abort_if(RoboquestConnection::where('school_id',$data['school_id'])->where('school_user_id','<>',$request->user()->id)->exists(), 422, 'School ID already connected.');
        abort_unless($connection->exists || $request->filled('shared_key'),422,'Set the matching connection key for this school server.');
        if (!$connection->exists || $request->filled('shared_key')) $connection->shared_key = $data['shared_key'] ?? bin2hex(random_bytes(32));
        $connection->fill(['school_id'=>$data['school_id'],'base_url'=>rtrim($data['base_url'],'/')])->save();
        return back()->with('roboquest_saved', true);
    }
    public function launch(Request $request, RoboquestService $service) {
        $url = $service->launch($request->user());
        if (!$url) {
            if ($request->user()->role === 'school') return redirect()->route('roboquest.settings')->withErrors(['connection'=>'Connect this school’s RoboQuest first.']);
            return redirect()->route('roboquest.signin', ['role'=>$request->user()->role])->withErrors(['login'=>'Your school must connect RoboQuest and activate your membership before you can enter.']);
        }
        return redirect()->away($url)->withHeaders(['Referrer-Policy'=>'no-referrer','Cache-Control'=>'no-store']);
    }
    private function authenticate(Request $request): RoboquestConnection {
        $input = $request->validate(['schoolId'=>'required|string|max:80','timestamp'=>'required|integer','nonce'=>'required|string|min:16|max:80']);
        abort_unless(abs(time() - $input['timestamp']) <= 120, 401, 'Expired request.');
        $connection = RoboquestConnection::where('school_id',$input['schoolId'])->first();
        abort_unless($connection && hash_equals(hash_hmac('sha256',$request->getContent(),$connection->shared_key), (string)$request->header('X-RoboQuest-Signature')), 401, 'Invalid school credentials.');
        DB::table('roboquest_request_nonces')->where('expires_at','<',now())->delete();
        $inserted = DB::table('roboquest_request_nonces')->insertOrIgnore(['connection_id'=>$connection->id,'nonce'=>$input['nonce'],'expires_at'=>now()->addMinutes(3)]);
        abort_unless($inserted,409,'Request already used.');
        return $connection;
    }
    public function redeem(Request $request, RoboquestService $service) {
        $connection = $this->authenticate($request);
        $code = $request->validate(['code'=>'required|string|size:64'])['code'];
        return DB::transaction(function() use ($connection,$code,$service) {
            $row = DB::table('roboquest_launch_codes')->where('connection_id',$connection->id)->where('code_hash',hash('sha256',$code))->lockForUpdate()->first();
            abort_unless($row && !$row->used_at && now()->lt($row->expires_at),401,'Launch link expired or already used.');
            $user = User::findOrFail($row->user_id);
            $claims = $service->claims($user,$connection);
            DB::table('roboquest_launch_codes')->where('id',$row->id)->update(['used_at'=>now()]);
            return response()->json(['user'=>$claims,'snapshot'=>$service->snapshot($connection)])->header('Cache-Control','no-store');
        });
    }
    public function snapshot(Request $request, RoboquestService $service) {
        return response()->json($service->snapshot($this->authenticate($request)))->header('Cache-Control','no-store');
    }
}
