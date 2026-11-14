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
    public function settings(Request $request, RoboquestService $service) {
        abort_unless($request->user()->role === 'school', 403);
        $connection = $service->connection($request->user());
        return view('frontend.school-dashboard.roboquest', compact('connection'));
    }
    public function save(Request $request) {
        abort_unless($request->user()->role === 'school', 403);
        $data = $request->validate([
            'school_id'  => 'required|regex:/^[A-Za-z0-9_-]{1,80}$/',
            'base_url'   => 'required|url|max:500',
            'shared_key' => 'nullable|string|min:32|max:200',
        ]);
        $url = parse_url($data['base_url']);
        $host = $url['host'] ?? '';
        $scheme = $url['scheme'] ?? '';

        if (str_contains($data['base_url'], 'skillvation.comphp') || str_contains($data['base_url'], '/school/roboquest')) {
            return back()->withInput()->withErrors(['base_url' => __('You entered the Skillvation website URL. The RoboQuest URL must be the RoboQuest server origin (e.g. http://localhost:8080 or https://digilab.skillvation.com).')]);
        }

        $port = isset($url['port']) ? ':' . $url['port'] : '';
        $normalizedBaseUrl = $scheme . '://' . $host . $port;

        $isLocal = ($scheme === 'http' && in_array($host, ['localhost', '127.0.0.1', '[::1]']));
        if ($scheme !== 'https' && !$isLocal) {
            return back()->withInput()->withErrors(['base_url' => __('Use HTTPS (e.g. https://digilab.skillvation.com), or HTTP on localhost for testing.')]);
        }
        if (RoboquestConnection::where('school_id', $data['school_id'])->where('school_user_id', '<>', $request->user()->id)->exists()) {
            return back()->withInput()->withErrors(['school_id' => __('School ID is already connected by another school.')]);
        }
        $connection = RoboquestConnection::firstOrNew(['school_user_id' => $request->user()->id]);
        $defaultKey = env('DEFAULT_ROBOQUEST_KEY');
        if (!$connection->exists && !$request->filled('shared_key')) {
            if (!$defaultKey) return back()->withInput()->withErrors(['shared_key' => __('Enter the connection key from the RoboQuest server.')]);
            $connection->shared_key = $defaultKey;
        } elseif ($request->filled('shared_key')) {
            $connection->shared_key = $data['shared_key'];
        }
        $connection->fill([
            'school_id' => $data['school_id'],
            'base_url'  => rtrim($normalizedBaseUrl, '/'),
        ])->save();

        return back()->with('roboquest_saved', true);
    }
    public function launch(Request $request, RoboquestService $service) {
        $user = $request->user('web') ?? $request->user('admin') ?? auth('admin')->user() ?? auth('web')->user();
        if (!$user) {
            return redirect()->route('login');
        }
        $url = $service->launch($user);
        if (!$url) {
            if ($user->role === 'school') return redirect()->route('roboquest.settings')->withErrors(['connection'=>'Connect this school’s RoboQuest first.']);
            return redirect()->route('roboquest.signin', ['role'=>$user->role ?? 'student'])->withErrors(['login'=>'Your school must connect RoboQuest and activate your membership before you can enter.']);
        }
        return redirect()->away($url)->withHeaders(['Referrer-Policy'=>'no-referrer','Cache-Control'=>'no-store']);
    }
    private function authenticate(Request $request): RoboquestConnection {
        $input = $request->validate(['schoolId'=>'required|string|max:80','timestamp'=>'required|integer','nonce'=>'required|string|min:16|max:80']);
        abort_unless(abs(time() - $input['timestamp']) <= 120, 401, 'Expired request.');
        // The request is signed for one school and checked with that school's own connection key.
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
            $row = DB::table('roboquest_launch_codes')->where('code_hash',hash('sha256',$code))->lockForUpdate()->first();
            abort_unless($row && !$row->used_at && now()->lt($row->expires_at),401,'Launch link expired or already used.');
            // A code opens only the school it was issued for, even when schools share one DigiLab server and key.
            abort_unless((int)$row->connection_id === (int)$connection->id,401,'Launch link belongs to another school.');
            $user = User::findOrFail($row->user_id);
            $claims = $service->claims($user,$connection);
            DB::table('roboquest_launch_codes')->where('id',$row->id)->update(['used_at'=>now()]);

            $snapshot = $service->snapshot($connection, $user);
            $snapshot['schoolId'] = $connection->school_id;

            return response()->json(['user'=>$claims,'snapshot'=>$snapshot])->header('Cache-Control','no-store');
        });
    }
    public function snapshot(Request $request, RoboquestService $service) {
        $connection = $this->authenticate($request);
        $snapshot = $service->snapshot($connection);
        $snapshot['schoolId'] = $connection->school_id;
        return response()->json($snapshot)->header('Cache-Control','no-store');
    }
}
