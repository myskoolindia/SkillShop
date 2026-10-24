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
     * Start a "Design My Lab" session.
     * POST {base}/api/v1/sessions  { "reference": "<ref>" }
     * Returns the sessionId (+ any session URL) and persists a lab_designs row.
     */
    public function start(Request $request)
    {
        $reference = $request->input('reference')
            ?: ('lab-' . Str::lower(Str::random(8)));
        $source = $request->input('source', 'design-my-lab');

        if (empty($this->baseUrl()) || empty($this->apiKey())) {
            return response()->json(['result' => 0, 'message' => 'Design service is not configured.'], 422);
        }

        try {
            $response = Http::withToken($this->apiKey())
                ->acceptJson()
                ->asJson()
                ->connectTimeout(5)
                ->timeout(20)
                ->post($this->baseUrl() . '/api/v1/sessions', [
                    'reference' => $reference,
                ]);

            if (!$response->successful()) {
                Log::error('[LabDesign] create session failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return response()->json([
                    'result'  => 0,
                    'message' => 'Could not start the design session. Please try again.',
                ], 502);
            }

            $data = $response->json() ?? [];

            // Kalpana session API fields (with flexible fallbacks).
            $sessionId = $data['sessionId'] ?? $data['session_id'] ?? ($data['id'] ?? null);
            $launchUrl = $data['launchUrl'] ?? ($data['launch_url'] ?? ($data['url'] ?? null));

            $design = LabDesign::create([
                'session_id'  => $sessionId,
                'reference'   => $reference,
                'source'      => $source,
                'status'      => 'pending',
                'session_url' => $launchUrl,
                'result'      => null,
                'name'        => $request->input('name'),
                'school'      => $request->input('school'),
                'email'       => $request->input('email'),
                'phone'       => $request->input('phone'),
            ]);

            return response()->json([
                'result'      => 1,
                'id'          => $design->id,
                'session_id'  => $sessionId,
                'launch_url'  => $launchUrl,
                'raw'         => $data,
            ]);
        } catch (\Throwable $e) {
            Log::error('[LabDesign] create session exception: ' . $e->getMessage());
            return response()->json([
                'result'  => 0,
                'message' => 'The design service is unavailable right now.',
            ], 502);
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
            $response = Http::withToken($this->apiKey())
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
