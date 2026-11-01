<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\LabDesign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LabDesignController extends Controller
{
    private function baseUrl(): string
    {
        return rtrim((string) config('services.labdesign.base_url'), '/');
    }

    private function apiKey(): string
    {
        return (string) config('services.labdesign.api_key');
    }

    /**
     * Rewrite a Kalpana launch URL so its scheme+host match our configured public base.
     * Kalpana composes the URL from its own PUBLIC_URL, which can point at an internal/
     * unreachable host (e.g. http://localhost:8083/kalpana/). We keep the path, query and
     * fragment (session id + launch token) and only swap the origin to LABDESIGN_API_BASE.
     */
    private function normalizeLaunchUrl(?string $launchUrl): ?string
    {
        if (empty($launchUrl)) {
            return $launchUrl;
        }

        $base = $this->baseUrl(); // e.g. https://kalpana.skillvation.com
        if (empty($base)) {
            return $launchUrl;
        }

        $b = parse_url($base);
        $u = parse_url($launchUrl);
        if (!$u || empty($b['host'])) {
            return $launchUrl;
        }

        // If the launch URL already points at the configured host, leave it alone.
        if (($u['host'] ?? null) === $b['host']) {
            return $launchUrl;
        }

        $scheme = $b['scheme'] ?? 'https';
        $host   = $b['host'];
        $port   = isset($b['port']) ? ':' . $b['port'] : '';
        $path   = $u['path'] ?? '/';
        $query  = isset($u['query']) ? '?' . $u['query'] : '';
        $frag   = isset($u['fragment']) ? '#' . $u['fragment'] : '';

        // Kalpana now lives on its own domain, serving the planner at the root.
        // Drop a legacy "/kalpana" path prefix from the old folder-under-path setup,
        // unless the configured base itself includes that path.
        $basePath = rtrim($b['path'] ?? '', '/');
        if ($basePath === '' && preg_match('#^/kalpana(/|$)#', $path)) {
            $path = preg_replace('#^/kalpana#', '', $path);
            if ($path === '') {
                $path = '/';
            }
        }

        return $scheme . '://' . $host . $port . $path . $query . $frag;
    }

    /**
     * Start a "Design My Lab" session.
     * POST {base}/api/v1/sessions  { "reference": "<ref>" }
     * Returns the sessionId (+ any session URL) and persists a lab_designs row.
     */
    public function start(Request $request)
    {
        $reference = $request->input('reference')
            ?: ('lab-' . Str::lower(Str::random(8)));
        $source = $request->input('source', 'design-my-lab');

        if (empty($this->baseUrl())) {
            return response()->json(['result' => 0, 'message' => 'Design service is not configured.'], 422);
        }

        try {
            $sessionId = null;
            $launchUrl = null;
            $data = [];

            if (!empty($this->apiKey())) {
                $response = Http::withoutVerifying()
                    ->withToken($this->apiKey())
                    ->acceptJson()
                    ->asJson()
                    ->connectTimeout(5)
                    ->timeout(20)
                    ->post($this->baseUrl() . '/api/v1/sessions', [
                        'reference' => $reference,
                    ]);

                if ($response->successful()) {
                    $data = $response->json() ?? [];
                    $sessionId = $data['sessionId'] ?? $data['session_id'] ?? ($data['id'] ?? null);
                    $launchUrl = $data['launchUrl'] ?? ($data['launch_url'] ?? ($data['url'] ?? null));
                    $launchUrl = $this->normalizeLaunchUrl($launchUrl);
                } else {
                    Log::warning('[LabDesign] Kalpana API session creation returned ' . $response->status() . '; falling back to direct planner URL.', [
                        'body' => $response->body(),
                    ]);
                }
            }

            // Fallback to direct planner if session API was not configured or returned error
            if (empty($sessionId) || empty($launchUrl)) {
                $sessionId = 'direct-' . Str::lower(Str::random(12));
                $launchUrl = $this->baseUrl() . '/lab-planner?scope=' . urlencode($sessionId) . '&ref=' . urlencode($reference);
            }

            $design = LabDesign::create([
                'session_id'  => $sessionId,
                'reference'   => $reference,
                'source'      => $source,
                'status'      => 'pending',
                'session_url' => $launchUrl,
                'result'      => null,
                'name'        => $request->input('name') ?: auth()->user()?->name,
                'school'      => $request->input('school') ?: (auth()->user()?->school_name ?? auth()->user()?->school_id),
                'email'       => $request->input('email') ?: auth()->user()?->email,
                'phone'       => $request->input('phone') ?: auth()->user()?->phone,
            ]);

            return response()->json([
                'result'      => 1,
                'id'          => $design->id,
                'session_id'  => $sessionId,
                'launch_url'  => $launchUrl,
                'raw'         => $data,
            ]);
        } catch (\Throwable $e) {
            Log::error('[LabDesign] create session exception: ' . $e->getMessage() . '; falling back to direct planner URL.');
            $sessionId = 'direct-' . Str::lower(Str::random(12));
            $launchUrl = $this->baseUrl() . '/lab-planner?scope=' . urlencode($sessionId) . '&ref=' . urlencode($reference);

            $design = LabDesign::create([
                'session_id'  => $sessionId,
                'reference'   => $reference,
                'source'      => $source,
                'status'      => 'pending',
                'session_url' => $launchUrl,
                'result'      => null,
                'name'        => auth()->user()?->name,
                'school'      => auth()->user()?->school_name ?? auth()->user()?->school_id,
                'email'       => auth()->user()?->email,
                'phone'       => auth()->user()?->phone,
            ]);

            return response()->json([
                'result'      => 1,
                'id'          => $design->id,
                'session_id'  => $sessionId,
                'launch_url'  => $launchUrl,
            ]);
        }
    }

    /**
     * Fetch the result of a design session, show it, and save to DB.
     * GET {base}/api/v1/sessions/{sessionId}/result
     */
    public function result(string $sessionId)
    {
        if (empty($this->baseUrl()) || empty($this->apiKey())) {
            return response()->json(['result' => 0, 'message' => 'Design service is not configured.'], 422);
        }

        try {
            $response = Http::withoutVerifying()
                ->withToken($this->apiKey())
                ->acceptJson()
                ->connectTimeout(5)
                ->timeout(20)
                ->get($this->baseUrl() . '/api/v1/sessions/' . rawurlencode($sessionId) . '/result');

            if (!$response->successful()) {
                Log::error('[LabDesign] result fetch failed', [
                    'session' => $sessionId,
                    'status'  => $response->status(),
                    'body'    => $response->body(),
                ]);
                return response()->json(['result' => 0, 'message' => 'Could not fetch the design result.'], 502);
            }

            $data = $response->json() ?? [];
            $apiStatus = strtolower((string) ($data['status'] ?? ''));

            // Only "ready" means the quotation is complete. Everything else
            // (pending / processing / in_progress) means keep polling.
            if ($apiStatus !== 'ready' && $apiStatus !== 'completed') {
                // Keep the latest snapshot on the row but leave status as pending.
                $design = LabDesign::where('session_id', $sessionId)->latest('id')->first();
                if ($design) {
                    $design->update(['status' => $apiStatus ?: 'pending', 'result' => $data]);
                }
                return response()->json([
                    'result'  => 0,
                    'pending' => 1,
                    'status'  => $apiStatus ?: 'pending',
                    'message' => 'Design still in progress.',
                ]);
            }

            // Ready — persist the full result and return it for display.
            $design = LabDesign::where('session_id', $sessionId)->latest('id')->first();
            if ($design) {
                $design->update(['status' => 'ready', 'result' => $data]);
            } else {
                $design = LabDesign::create([
                    'session_id' => $sessionId,
                    'status'     => 'ready',
                    'result'     => $data,
                ]);
            }

            return response()->json([
                'result'     => 1,
                'id'         => $design->id,
                'session_id' => $sessionId,
                'status'     => 'ready',
                'data'       => $data,
            ]);
        } catch (\Throwable $e) {
            Log::error('[LabDesign] result exception: ' . $e->getMessage());
            return response()->json(['result' => 0, 'message' => 'The design service is unavailable right now.'], 502);
        }
    }
}
