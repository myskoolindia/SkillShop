<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\DefaultMail;
use App\Models\CourseEnquiry;
use App\Traits\MailSenderTrait;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Attachment;
use Modules\GlobalSetting\app\Models\EmailTemplate;
use Modules\GlobalSetting\app\Models\Setting;

class CourseEnquiryController extends Controller
{
    use MailSenderTrait;

    /**
     * Map enquiry source → club-shop product slug + product page URL
     */
    private const PLAN_MAP = [
        'composite-skill-lab-basic'    => [
            'slug'     => 'cbse-composite-skill-lab-basic-68',
            'label'    => 'Basic Composite Skill Lab',
            'page_url' => '/club-shop/cbse-composite-skill-lab-basic-68',
            'cart_url' => '/club-shop/basic-cart',
        ],
        'composite-skill-lab-advance'  => [
            'slug'     => 'cbse-composite-skill-lab-advanced-72',
            'label'    => 'Advance Composite Skill Lab',
            'page_url' => '/club-shop/cbse-composite-skill-lab-advanced-72',
            'cart_url' => '/club-shop/advance-cart',
        ],
        'composite-skill-lab-premium'  => [
            'slug'     => 'cbse-composite-skill-lab-premium-73',
            'label'    => 'Premium Composite Skill Lab',
            'page_url' => '/club-shop/cbse-composite-skill-lab-premium-73',
            'cart_url' => '/club-shop/premium-cart',
        ],
        // composite-skill main page forms
        'composite-skill-lab'          => [
            'slug'     => 'cbse-composite-skill-lab-basic-68',
            'label'    => 'Composite Skill Lab',
            'page_url' => '/labs/composite-skill',
            'cart_url' => '/club-shop/basic-cart',
        ],
        'composite-skill-lab-callback' => [
            'slug'     => 'cbse-composite-skill-lab-basic-68',
            'label'    => 'Composite Skill Lab — Callback',
            'page_url' => '/labs/composite-skill',
            'cart_url' => '/club-shop/basic-cart',
        ],
        'composite-skill-custom-package' => [
            'slug'     => 'cbse-composite-skill-lab-basic-68',
            'label'    => 'Custom Composite Skill Lab',
            'page_url' => '/labs/composite-skill',
            'cart_url' => '/labs/composite-skill',
        ],
        // Skillvation Lab (STEM page)
        'skillvation-lab-stem'         => [
            'slug'     => 'skillvation-lab',
            'label'    => 'Skillvation Lab',
            'page_url' => '/club-shop/skillvation-lab',
            'cart_url' => '/club-shop/skillvation-cart',
        ],
        'skillvation-lab'              => [
            'slug'     => 'skillvation-lab',
            'label'    => 'Skillvation Lab',
            'page_url' => '/club-shop/skillvation-lab',
            'cart_url' => '/club-shop/skillvation-cart',
        ],
        // Course detail quote form
        'course-detail-quote'          => [
            'slug'     => null,
            'label'    => 'Course Enquiry',
            'page_url' => '/courses',
            'cart_url' => '/courses',
        ],
    ];

    /**
     * Resolve default bundle metadata for a slug
     */
    private function getBundleMetaBySlug(string $slug): array
    {
        return match ($slug) {
            'cbse-composite-skill-lab-premium-73' => [
                'slug'     => 'cbse-composite-skill-lab-premium-73',
                'label'    => 'Premium Composite Skill Lab',
                'page_url' => '/club-shop/cbse-composite-skill-lab-premium-73',
                'cart_url' => '/club-shop/premium-cart',
            ],
            'cbse-composite-skill-lab-advanced-72' => [
                'slug'     => 'cbse-composite-skill-lab-advanced-72',
                'label'    => 'Advance Composite Skill Lab',
                'page_url' => '/club-shop/cbse-composite-skill-lab-advanced-72',
                'cart_url' => '/club-shop/advance-cart',
            ],
            'skillvation-lab' => [
                'slug'     => 'skillvation-lab',
                'label'    => 'Skillvation Lab',
                'page_url' => '/club-shop/skillvation-lab',
                'cart_url' => '/club-shop/skillvation-cart',
            ],
            default => [
                'slug'     => 'cbse-composite-skill-lab-basic-68',
                'label'    => 'Basic Composite Skill Lab',
                'page_url' => '/club-shop/cbse-composite-skill-lab-basic-68',
                'cart_url' => '/club-shop/basic-cart',
            ],
        };
    }

    /**
     * Dynamically resolve bundle product slug based on source, title, and configurator data
     */
    private function resolvePackageSlug(string $source, ?string $courseTitle, ?array $rawQ): string
    {
        $sourceLower = strtolower($source);
        $titleLower  = strtolower($courseTitle ?? '');
        $tierLower   = strtolower(($rawQ['recommended_tier'] ?? '') . ' ' . ($rawQ['budget_category'] ?? ''));

        // 1. Skillvation Lab / STEM pages have top precedence when requested from STEM routes
        if (str_contains($sourceLower, 'skillvation')
            || str_contains($sourceLower, 'stem')
            || str_contains($titleLower, 'skillvation')
            || str_contains($titleLower, 'stem')) {
            return 'skillvation-lab';
        }

        // 2. Check for Premium tier
        if (str_contains($sourceLower, 'premium')
            || str_contains($titleLower, 'premium')
            || str_contains($tierLower, 'premium')
            || str_contains($tierLower, 'elite')) {
            return 'cbse-composite-skill-lab-premium-73';
        }

        // 3. Check for Advance / Growth tier
        if (str_contains($sourceLower, 'advance')
            || str_contains($titleLower, 'advance')
            || str_contains($tierLower, 'advance')
            || str_contains($tierLower, 'growth')) {
            return 'cbse-composite-skill-lab-advanced-72';
        }

        // 4. Check for Basic / Standard / Essential tier
        if (str_contains($sourceLower, 'basic')
            || str_contains($titleLower, 'basic')
            || str_contains($titleLower, 'standard')
            || str_contains($tierLower, 'basic')
            || str_contains($tierLower, 'essential')) {
            return 'cbse-composite-skill-lab-basic-68';
        }

        // 5. Use mapped slug if present
        if (!empty(self::PLAN_MAP[$source]['slug'])) {
            return self::PLAN_MAP[$source]['slug'];
        }

        // 6. Default fallback for composite skill lab
        return 'cbse-composite-skill-lab-basic-68';
    }

    /**
     * Extract itemized quotation data from any payload shape:
     * - selected_components
     * - items
     * - selected_items
     * - sequential list
     */
    private function parseQuotationData($quotation): ?array
    {
        if (empty($quotation)) return null;

        $raw = is_string($quotation) ? json_decode($quotation, true) : $quotation;
        if (!is_array($raw)) return null;

        $list = null;
        if (!empty($raw['selected_components']) && is_array($raw['selected_components'])) {
            $list = $raw['selected_components'];
        } elseif (!empty($raw['items']) && is_array($raw['items'])) {
            $list = $raw['items'];
        } elseif (!empty($raw['selected_items']) && is_array($raw['selected_items'])) {
            $list = $raw['selected_items'];
        } elseif (array_is_list($raw) && !empty($raw) && is_array($raw[0])) {
            $list = $raw;
        }

        if (empty($list)) return null;

        $items      = [];
        $grouped    = [];
        $grandTotal = 0;

        foreach ($list as $c) {
            if (!is_array($c)) continue;
            $title = $c['title'] ?? ($c['item'] ?? ($c['name'] ?? null));
            if (empty($title)) continue;

            $cat   = $c['category'] ?? ($c['category_name'] ?? ($c['cat'] ?? 'General'));
            $qty   = (int) ($c['qty'] ?? ($c['required_quantity'] ?? ($c['quantity'] ?? 1)));
            if ($qty <= 0) $qty = 1;

            $price = (float) ($c['unit_price'] ?? ($c['price'] ?? 0));
            $total = (float) ($c['subtotal'] ?? ($c['total'] ?? ($c['total_price'] ?? ($price * $qty))));

            $grandTotal += $total;
            $items[] = [
                'cat'   => $cat,
                'item'  => $title,
                'qty'   => $qty,
                'price' => $price,
                'total' => $total,
            ];
            $grouped[$cat] = ($grouped[$cat] ?? 0) + $total;
        }

        if (empty($items)) return null;

        return [
            'items'       => $items,
            'grouped'     => $grouped,
            'grand_total' => $grandTotal,
        ];
    }

    public function store(Request $request)
    {
        $source = (string) $request->input('source', '');

        if (auth()->check()) {
            $user = auth()->user();
            $request->merge([
                'name'        => $request->input('name') ?: ($user->name ?? 'Verified Account'),
                'email'       => $request->input('email') ?: $user->email,
                'phone'       => $request->input('phone') ?: ($user->phone ?? ''),
                'school'      => $request->input('school') ?: ($user->school_name ?? $user->name ?? ''),
                'designation' => $request->input('designation') ?: ($user->job_title ?? ''),
                'city'        => $request->input('city') ?: ($user->city ?? ''),
            ]);
        }

        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'max:255'],
            'phone'         => ['nullable', 'string', 'max:20'],
            'designation'   => ['nullable', 'string', 'max:255'],
            'school'        => ['nullable', 'string', 'max:255'],
            'city'          => ['nullable', 'string', 'max:255'],
            'address'       => ['nullable', 'string', 'max:1000'],
            'message'       => ['nullable', 'string', 'max:2000'],
            'quotation'     => ['nullable', 'string'],
            'course_id'     => ['nullable'],
            'api_course_id' => ['nullable'],
            'course_title'  => ['nullable', 'string', 'max:255'],
            'source'        => ['nullable', 'string', 'max:100'],
        ]);

        $enquiry = CourseEnquiry::create($validated);

        // ── Push to LeadDemo CRM (fire-and-forget) ────────────────
        // $this->sendToLeadDemo($validated);

        try {
            self::setMailConfig();

            $adminEmail  = Setting::where('key', 'contact_message_receiver_mail')->value('value')
                        ?? config('mail.from.address');
            $source      = $validated['source'] ?? '';
            $sourceLabel = ucwords(str_replace(['-', '_'], ' ', $source));
            $rawQ        = !empty($validated['quotation'])
                ? (is_string($validated['quotation']) ? json_decode($validated['quotation'], true) : $validated['quotation'])
                : null;

            // Resolve target package slug
            $targetSlug  = $this->resolvePackageSlug($source, $validated['course_title'] ?? null, is_array($rawQ) ? $rawQ : null);
            $bundleMeta  = $this->getBundleMetaBySlug($targetSlug);

            $planInfo    = array_merge($bundleMeta, self::PLAN_MAP[$source] ?? []);
            if (empty($planInfo['slug'])) {
                $planInfo['slug'] = $targetSlug;
            }
            if (empty($planInfo['page_url'])) {
                $planInfo['page_url'] = $bundleMeta['page_url'];
            }
            if (empty($planInfo['cart_url'])) {
                $planInfo['cart_url'] = $bundleMeta['cart_url'];
            }

            $packageName = !empty($validated['course_title'])
                ? $validated['course_title']
                : ($planInfo['label'] ?? $sourceLabel);

            // Determine if this is a Skillvation Lab or Composite Skill Lab
            $isSkillvationLab = str_contains(strtolower($source), 'skillvation')
                             || str_contains(strtolower($source), 'stem')
                             || str_contains(strtolower($packageName), 'skillvation')
                             || str_contains(strtolower($packageName), 'stem');

            // ── Build quotation data (supports custom configurator JSON or club shop bundle)
            $quotationData = $this->parseQuotationData($validated['quotation'] ?? null);

            if (!$quotationData) {
                // ── Fetch bundle items server-side from Club Shop API / DB ─
                $bundleComponents = $this->fetchBundleComponents($targetSlug);
                $quotationData    = $this->buildQuotationData($bundleComponents);
            }

            // ── Build the HTML table (used in email body for admin)
            $quotationHtml = $this->renderQuotationTable($quotationData);

            // ── Product page links ────────────────────────────────
            $baseUrl     = url('/');
            $productLink = $planInfo ? $baseUrl . $planInfo['page_url'] : '';
            $cartLink    = $planInfo ? $baseUrl . $planInfo['cart_url']  : '';

            // ── Variable map ──────────────────────────────────────
            $vars = [
                '{{name}}'            => htmlspecialchars($validated['name']),
                '{{designation}}'     => $validated['designation'] ? ', ' . htmlspecialchars($validated['designation']) : '',
                '{{school}}'          => htmlspecialchars($validated['school'] ?? '—'),
                '{{city}}'            => htmlspecialchars($validated['city'] ?? '—'),
                '{{phone}}'           => htmlspecialchars($validated['phone'] ?? '—'),
                '{{email}}'           => htmlspecialchars($validated['email']),
                '{{package}}'         => htmlspecialchars($packageName),
                '{{source}}'          => htmlspecialchars($sourceLabel),
                '{{request_type}}'    => htmlspecialchars($validated['message'] ?? '—'),
                '{{address}}'         => htmlspecialchars($validated['address'] ?? '—'),
                '{{date}}'            => now()->format('d M Y'),
                '{{quotation_table}}' => $quotationHtml,
                '{{admin_url}}'       => url('/admin/course-enquiry/' . $enquiry->id),
                '{{product_link}}'    => $productLink,
                '{{cart_link}}'       => $cartLink,
            ];

            // ── 1. Admin notification ─────────────────────────────
            $adminTpl = EmailTemplate::where('name', 'skill_lab_enquiry_admin')->first();
            $defaultAdminSubject = $isSkillvationLab
                ? "New Skillvation Lab Enquiry: {$packageName} – " . ($validated['school'] ?? 'School')
                : "New CBSE Composite Skill Lab Enquiry: {$packageName} – " . ($validated['school'] ?? 'School');
            $adminSubject = ($adminTpl && !empty($adminTpl->subject))
                ? str_replace(array_keys($vars), array_values($vars), $adminTpl->subject)
                : $defaultAdminSubject;
            $adminBody = ($adminTpl && !empty($adminTpl->message))
                ? str_replace(array_keys($vars), array_values($vars), $adminTpl->message)
                : "<p>New enquiry from <strong>{$validated['name']}</strong> ({$validated['school']})</p><p>Package: {$packageName}</p><p>Phone: {$validated['phone']}</p><p>Email: {$validated['email']}</p>{$quotationHtml}";

            Mail::to($adminEmail)->send(new DefaultMail(['subject' => $adminSubject], $adminBody));

            // ── 2. Confirmation + PDF proposal to enquirer ────────
            $confirmTpl = EmailTemplate::where('name', 'skill_lab_enquiry_confirmation')->first();
            $defaultConfirmSubject = $isSkillvationLab
                ? "Skillvation Lab Proposal – " . ($validated['school'] ?? 'School')
                : "Skillvation CBSE Composite Skill Lab Proposal ({$packageName}) – " . ($validated['school'] ?? 'School');
            $confirmSubject = ($confirmTpl && !empty($confirmTpl->subject))
                ? str_replace(array_keys($vars), array_values($vars), $confirmTpl->subject)
                : $defaultConfirmSubject;
            $confirmBody = ($confirmTpl && !empty($confirmTpl->message))
                ? str_replace(array_keys($vars), array_values($vars), $confirmTpl->message)
                : "<p>Dear {$validated['name']},</p><p>Thank you for your interest in Skillvation. Please find attached your customized Proposal PDF.</p>{$quotationHtml}";

            // Generate PDF with full proposal + dynamic bundle items
            $pdfBytes  = $this->generateProposalPdf($validated, $packageName, $quotationData, $planInfo);
            $cleanSchool = preg_replace('/[^A-Za-z0-9_]/', '_', $validated['school'] ?? 'School');
            $pdfPrefix = $isSkillvationLab ? 'Skillvation_Lab_Proposal_' : 'Composite_Skill_Lab_Proposal_';
            $pdfName   = $pdfPrefix . $cleanSchool . '_' . time() . '.pdf';

            // Save PDF to public uploads directory for WhatsApp & web access
            $proposalsDir = public_path('uploads/proposals');
            if (!file_exists($proposalsDir)) {
                @mkdir($proposalsDir, 0755, true);
            }
            @file_put_contents($proposalsDir . '/' . $pdfName, $pdfBytes);
            $pdfPublicUrl = url('uploads/proposals/' . rawurlencode($pdfName));

            Mail::to($validated['email'])
                ->send(new DefaultMail(
                    ['subject' => $confirmSubject],
                    $confirmBody,
                    $pdfBytes,
                    $pdfName
                ));

            // ── 3. WhatsApp notification (if phone available) ───
            $whatsappUrl = null;
            if (!empty($validated['phone'])) {
                try {
                    $wabaService = app(\App\Services\WabaOtpService::class);
                    $waPayload = [
                        'name'               => $validated['name'],
                        'school'             => $validated['school'] ?? 'School',
                        'package'            => $packageName,
                        'total'              => $quotationData['grand_total'] ?? 0,
                        'pdf_url'            => $pdfPublicUrl,
                        'product_url'        => $productLink ?: url('/labs'),
                        'is_skillvation_lab' => $isSkillvationLab,
                    ];
                    $whatsappUrl = $wabaService->generateWhatsAppShareUrl($validated['phone'], $waPayload);
                    $wabaService->sendProposalMessage($validated['phone'], $waPayload);
                } catch (\Throwable $we) {
                    Log::warning('[CourseEnquiry] WhatsApp dispatch failed: ' . $we->getMessage());
                }
            }

        } catch (\Throwable $e) {
            Log::error('[CourseEnquiry] Mail failed: ' . $e->getMessage(), [
                'enquiry_id' => $enquiry->id,
                'trace'      => $e->getTraceAsString(),
            ]);
        }

        return response()->json([
            'status'       => 'success',
            'message'      => 'Enquiry submitted successfully. Proposal has been sent to your email and WhatsApp.',
            'pdf_url'      => $pdfPublicUrl ?? null,
            'whatsapp_url' => $whatsappUrl ?? null,
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // Fetch bundle components from Club Shop DB / API (server-side)
    // ──────────────────────────────────────────────────────────────
    private function fetchBundleComponents(?string $slug): array
    {
        if (!$slug) return [];

        // 1. Direct query against clubshop DB
        try {
            $bundle = DB::connection('clubshop')->table('products')->where('slug', $slug)->first();
            if ($bundle) {
                $components = DB::connection('clubshop')->table('product_bundles as pb')
                    ->join('products as p', 'p.id', '=', 'pb.component_product_id')
                    ->leftJoin('product_details as pd', function($j) {
                        $j->on('pd.product_id', '=', 'p.id')->where('pd.lang_id', '=', 1);
                    })
                    ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
                    ->leftJoin('category_lang as cl', function($j) {
                        $j->on('cl.category_id', '=', 'c.id')->where('cl.lang_id', '=', 1);
                    })
                    ->where('pb.bundle_product_id', $bundle->id)
                    ->where('pb.is_optional', 0)
                    ->orderBy('pb.sort_order')
                    ->select(
                        'p.id',
                        'pd.title',
                        'cl.name as category_name',
                        'pb.quantity as required_quantity',
                        DB::raw('COALESCE(pb.price_override, p.price) as unit_price')
                    )
                    ->get();

                if ($components->isNotEmpty()) {
                    return $components->map(fn($item) => (array) $item)->toArray();
                }
            }
        } catch (\Throwable $e) {
            Log::warning('[CourseEnquiry] clubshop DB fetch failed: ' . $e->getMessage());
        }

        // 2. HTTP fallback
        try {
            $apiBase  = rtrim(env('SHOP_API_BASE_URL', 'https://test.myskill.club/api/'), '/');
            $localUrl = url('/club-shop/api/products/slug/' . $slug);

            $response = Http::timeout(5)->get($localUrl);

            if ($response->successful() && !empty($response->json('data.bundle_components'))) {
                return $response->json('data.bundle_components', []);
            }

            $response = Http::timeout(5)->get($apiBase . '/products/slug/' . $slug);
            return $response->successful() ? $response->json('data.bundle_components', []) : [];

        } catch (\Throwable $e) {
            Log::warning('[CourseEnquiry] Bundle HTTP fetch failed: ' . $e->getMessage());
            return [];
        }
    }

    // ──────────────────────────────────────────────────────────────
    // Build structured quotation data from bundle components
    // ──────────────────────────────────────────────────────────────
    private function buildQuotationData(array $components): array
    {
        $grouped    = [];
        $grandTotal = 0;
        $items      = [];

        foreach ($components as $c) {
            if (empty($c['title'])) continue;
            $qty   = (int)   ($c['required_quantity'] ?? 0);
            $price = (float) ($c['unit_price'] ?? 0);
            if ($qty <= 0) continue;

            $cat   = $c['category_name'] ?? 'General';
            $total = $price * $qty;
            $grandTotal += $total;

            $items[]       = compact('cat', 'qty', 'price', 'total') + ['item' => $c['title']];
            $grouped[$cat] = ($grouped[$cat] ?? 0) + $total;
        }

        return [
            'items'       => $items,
            'grouped'     => $grouped,
            'grand_total' => $grandTotal,
        ];
    }

    // ──────────────────────────────────────────────────────────────
    // Render HTML table from quotation data
    // ──────────────────────────────────────────────────────────────
    private function renderQuotationTable(array $data): string
    {
        $items = $data['items']       ?? [];
        $grand = $data['grand_total'] ?? 0;

        if (empty($items)) {
            return '<p style="color:#94a3b8;font-size:13px;">No bundle items found.</p>';
        }

        // Group rows by category
        $grouped = [];
        foreach ($items as $row) {
            $grouped[$row['cat']][] = $row;
        }

        $rows = '';
        $sno  = 0;
        foreach ($grouped as $cat => $catItems) {
            $catTotal = array_sum(array_column($catItems, 'total'));
            foreach ($catItems as $row) {
                $sno++;
                $bg = ($sno % 2 === 0) ? 'background:#f8fafc;' : '';
                $rows .= '<tr style="' . $bg . '">'
                    . '<td style="padding:7px 10px;border:1px solid #e2e8f0;text-align:center;">' . $sno . '</td>'
                    . '<td style="padding:7px 10px;border:1px solid #e2e8f0;">' . htmlspecialchars($cat) . '</td>'
                    . '<td style="padding:7px 10px;border:1px solid #e2e8f0;font-weight:600;">' . htmlspecialchars($row['item']) . '</td>'
                    . '<td style="padding:7px 10px;border:1px solid #e2e8f0;text-align:center;">' . $row['qty'] . '</td>'
                    . '<td style="padding:7px 10px;border:1px solid #e2e8f0;text-align:right;">&#8377;' . number_format($row['price'], 2) . '</td>'
                    . '<td style="padding:7px 10px;border:1px solid #e2e8f0;text-align:right;font-weight:700;">&#8377;' . number_format($row['total'], 2) . '</td>'
                    . '</tr>';
            }
            $rows .= '<tr style="background:#f1f5f9;">'
                . '<td colspan="5" style="padding:7px 10px;border:1px solid #e2e8f0;text-align:right;font-weight:700;color:#64748b;">'
                . htmlspecialchars($cat) . ' Subtotal</td>'
                . '<td style="padding:7px 10px;border:1px solid #e2e8f0;text-align:right;font-weight:800;color:#1d6fa4;">&#8377;' . number_format($catTotal, 2) . '</td>'
                . '</tr>';
        }

        return '<table style="width:100%;border-collapse:collapse;font-size:13px;margin-top:8px;">'
            . '<thead><tr style="background:#1e1b4b;">'
            . '<th style="padding:8px 10px;color:#fff;text-align:center;width:35px;">#</th>'
            . '<th style="padding:8px 10px;color:#fff;text-align:left;">Category</th>'
            . '<th style="padding:8px 10px;color:#fff;text-align:left;">Item / Description</th>'
            . '<th style="padding:8px 10px;color:#fff;text-align:center;width:50px;">Qty</th>'
            . '<th style="padding:8px 10px;color:#fff;text-align:right;width:100px;">Unit Price</th>'
            . '<th style="padding:8px 10px;color:#fff;text-align:right;width:110px;">Total</th>'
            . '</tr></thead>'
            . '<tbody>' . $rows . '</tbody>'
            . '<tfoot><tr style="background:#1e1b4b;">'
            . '<td colspan="5" style="padding:10px;text-align:right;font-weight:800;color:#fff;font-size:14px;">Grand Total</td>'
            . '<td style="padding:10px;text-align:right;font-weight:800;color:#fbbf24;font-size:15px;">&#8377;' . number_format($grand, 2) . '</td>'
            . '</tr></tfoot>'
            . '</table>';
    }

    // ──────────────────────────────────────────────────────────────
    // Public wrapper — resend emails for an existing enquiry
    // Called by Admin CourseEnquiryController::sendMail()
    // ──────────────────────────────────────────────────────────────
    public function resendMailForEnquiry(array $data): void
    {
        self::setMailConfig();

        $adminEmail  = Setting::where('key', 'contact_message_receiver_mail')->value('value')
                    ?? config('mail.from.address');
        $source      = $data['source'] ?? '';
        $sourceLabel = ucwords(str_replace(['-', '_'], ' ', $source));
        $rawQ        = !empty($data['quotation'])
            ? (is_string($data['quotation']) ? json_decode($data['quotation'], true) : $data['quotation'])
            : null;

        $targetSlug  = $this->resolvePackageSlug($source, $data['course_title'] ?? null, is_array($rawQ) ? $rawQ : null);
        $bundleMeta  = $this->getBundleMetaBySlug($targetSlug);

        $planInfo    = array_merge($bundleMeta, self::PLAN_MAP[$source] ?? []);
        if (empty($planInfo['slug'])) {
            $planInfo['slug'] = $targetSlug;
        }
        if (empty($planInfo['page_url'])) {
            $planInfo['page_url'] = $bundleMeta['page_url'];
        }
        if (empty($planInfo['cart_url'])) {
            $planInfo['cart_url'] = $bundleMeta['cart_url'];
        }

        $packageName = !empty($data['course_title'])
            ? $data['course_title']
            : ($planInfo['label'] ?? $sourceLabel);

        $isSkillvationLab = str_contains(strtolower($source), 'skillvation')
                         || str_contains(strtolower($source), 'stem')
                         || str_contains(strtolower($packageName), 'skillvation')
                         || str_contains(strtolower($packageName), 'stem');

        $quotationData = $this->parseQuotationData($data['quotation'] ?? null);

        if (!$quotationData && !empty($data['selected_components'])) {
            $quotationData = $this->parseQuotationData($data['selected_components']);
        }

        if (!$quotationData) {
            $bundleComponents = $this->fetchBundleComponents($targetSlug);
            $quotationData    = $this->buildQuotationData($bundleComponents);
        }

        $quotationHtml = $this->renderQuotationTable($quotationData);

        $baseUrl     = url('/');
        $productLink = $planInfo ? $baseUrl . $planInfo['page_url'] : '';
        $cartLink    = $planInfo ? $baseUrl . $planInfo['cart_url']  : '';

        $vars = [
            '{{name}}'            => htmlspecialchars($data['name'] ?? ''),
            '{{designation}}'     => (!empty($data['designation'])) ? ', ' . htmlspecialchars($data['designation']) : '',
            '{{school}}'          => htmlspecialchars($data['school'] ?? '—'),
            '{{city}}'            => htmlspecialchars($data['city'] ?? '—'),
            '{{phone}}'           => htmlspecialchars($data['phone'] ?? '—'),
            '{{email}}'           => htmlspecialchars($data['email'] ?? ''),
            '{{package}}'         => htmlspecialchars($packageName),
            '{{source}}'          => htmlspecialchars($sourceLabel),
            '{{request_type}}'    => htmlspecialchars($data['message'] ?? '—'),
            '{{address}}'         => htmlspecialchars($data['address'] ?? '—'),
            '{{date}}'            => now()->format('d M Y'),
            '{{quotation_table}}' => $quotationHtml,
            '{{admin_url}}'       => url('/admin/course-enquiries'),
            '{{product_link}}'    => $productLink,
            '{{cart_link}}'       => $cartLink,
        ];

        // Admin notification
        $adminTpl = EmailTemplate::where('name', 'skill_lab_enquiry_admin')->first();
        $defaultAdminSubject = $isSkillvationLab
            ? "Skill Lab Enquiry: {$packageName} – " . ($data['school'] ?? 'School')
            : "CBSE Composite Skill Lab Enquiry: {$packageName} – " . ($data['school'] ?? 'School');
        $adminSubject = ($adminTpl && !empty($adminTpl->subject))
            ? str_replace(array_keys($vars), array_values($vars), $adminTpl->subject)
            : $defaultAdminSubject;
        $adminBody = ($adminTpl && !empty($adminTpl->message))
            ? str_replace(array_keys($vars), array_values($vars), $adminTpl->message)
            : "<p>Enquiry for <strong>{$data['name']}</strong> ({$data['school']})</p><p>Package: {$packageName}</p>{$quotationHtml}";

        Mail::to($adminEmail)->send(new DefaultMail(['subject' => $adminSubject], $adminBody));

        // Confirmation + PDF to school
        $confirmTpl = EmailTemplate::where('name', 'skill_lab_enquiry_confirmation')->first();
        $defaultConfirmSubject = $isSkillvationLab
            ? "Skillvation Lab Proposal – " . ($data['school'] ?? 'School')
            : "Skillvation CBSE Composite Skill Lab Proposal ({$packageName}) – " . ($data['school'] ?? 'School');
        $confirmSubject = ($confirmTpl && !empty($confirmTpl->subject))
            ? str_replace(array_keys($vars), array_values($vars), $confirmTpl->subject)
            : $defaultConfirmSubject;
        $confirmBody = ($confirmTpl && !empty($confirmTpl->message))
            ? str_replace(array_keys($vars), array_values($vars), $confirmTpl->message)
            : "<p>Dear {$data['name']},</p><p>Please find attached your customized Proposal PDF.</p>{$quotationHtml}";

        $pdfBytes = $this->generateProposalPdf($data, $packageName, $quotationData, $planInfo);
        $cleanSchool = preg_replace('/[^A-Za-z0-9_]/', '_', $data['school'] ?? 'School');
        $pdfPrefix = $isSkillvationLab ? 'Skillvation_Lab_Proposal_' : 'Composite_Skill_Lab_Proposal_';
        $pdfName  = $pdfPrefix . $cleanSchool . '_' . time() . '.pdf';

        // Save PDF to public uploads directory for WhatsApp & web access
        $proposalsDir = public_path('uploads/proposals');
        if (!file_exists($proposalsDir)) {
            @mkdir($proposalsDir, 0755, true);
        }
        @file_put_contents($proposalsDir . '/' . $pdfName, $pdfBytes);
        $pdfPublicUrl = url('uploads/proposals/' . rawurlencode($pdfName));

        Mail::to($data['email'])
            ->send(new DefaultMail(['subject' => $confirmSubject], $confirmBody, $pdfBytes, $pdfName));

        // WhatsApp notification (if phone available)
        if (!empty($data['phone'])) {
            try {
                $wabaService = app(\App\Services\WabaOtpService::class);
                $waPayload = [
                    'name'               => $data['name'] ?? '',
                    'school'             => $data['school'] ?? 'School',
                    'package'            => $packageName,
                    'total'              => $quotationData['grand_total'] ?? 0,
                    'pdf_url'            => $pdfPublicUrl,
                    'product_url'        => $productLink ?: url('/labs'),
                    'is_skillvation_lab' => $isSkillvationLab,
                ];
                $wabaService->sendProposalMessage($data['phone'], $waPayload);
            } catch (\Throwable $we) {
                Log::warning('[CourseEnquiry] WhatsApp resend failed: ' . $we->getMessage());
            }
        }
    }

    // ──────────────────────────────────────────────────────────────
    // Generate full A4 proposal PDF (docx content + bundle items)
    // ──────────────────────────────────────────────────────────────
    private function generateProposalPdf(array $data, string $packageName, array $quotationData, ?array $planInfo): string
    {
        $name        = htmlspecialchars($data['name']);
        $designation = !empty($data['designation']) ? ', ' . htmlspecialchars($data['designation']) : '';
        $school      = htmlspecialchars($data['school']  ?? '');
        $city        = htmlspecialchars($data['city']    ?? '');
        $phone       = htmlspecialchars($data['phone']   ?? '');
        $email       = htmlspecialchars($data['email']);
        $address     = htmlspecialchars($data['address'] ?? '');
        $pkg         = htmlspecialchars($packageName);
        $date        = now()->format('d M Y');

        $source      = strtolower($data['source'] ?? '');
        $pkgLower    = strtolower($packageName);

        $isSkillvationLab = str_contains($source, 'skillvation')
                         || str_contains($source, 'stem')
                         || str_contains($pkgLower, 'skillvation')
                         || str_contains($pkgLower, 'stem');

        // Product page links
        $baseUrl     = url('/');
        $productLink = $planInfo ? $baseUrl . $planInfo['page_url'] : '';
        $cartLink    = $planInfo ? $baseUrl . $planInfo['cart_url']  : '';

        // Build PDF-specific quotation table
        $qRows = '';
        $sno   = 0;
        $grand = $quotationData['grand_total'] ?? 0;
        $grouped = [];
        foreach (($quotationData['items'] ?? []) as $row) {
            $grouped[$row['cat']][] = $row;
        }
        foreach ($grouped as $cat => $catItems) {
            $catTotal = array_sum(array_column($catItems, 'total'));
            foreach ($catItems as $row) {
                $sno++;
                $bg = ($sno % 2 === 0) ? 'background:#f8fafc;' : 'background:#ffffff;';
                $qRows .= '<tr style="' . $bg . '">'
                    . '<td style="padding:6px 8px;border:1px solid #e2e8f0;text-align:center;">' . $sno . '</td>'
                    . '<td style="padding:6px 8px;border:1px solid #e2e8f0;">' . htmlspecialchars($cat) . '</td>'
                    . '<td style="padding:6px 8px;border:1px solid #e2e8f0;font-weight:bold;">' . htmlspecialchars($row['item']) . '</td>'
                    . '<td style="padding:6px 8px;border:1px solid #e2e8f0;text-align:center;">' . $row['qty'] . '</td>'
                    . '<td style="padding:6px 8px;border:1px solid #e2e8f0;text-align:right;">Rs. ' . number_format($row['price'], 2) . '</td>'
                    . '<td style="padding:6px 8px;border:1px solid #e2e8f0;text-align:right;font-weight:bold;">Rs. ' . number_format($row['total'], 2) . '</td>'
                    . '</tr>';
            }
            $qRows .= '<tr style="background:#f1f5f9;">'
                . '<td colspan="5" style="padding:6px 8px;border:1px solid #e2e8f0;text-align:right;font-weight:bold;color:#475569;">'
                . htmlspecialchars($cat) . ' Subtotal</td>'
                . '<td style="padding:6px 8px;border:1px solid #e2e8f0;text-align:right;font-weight:bold;color:#1d6fa4;">Rs. ' . number_format($catTotal, 2) . '</td>'
                . '</tr>';
        }

        $quotTable = $qRows
            ? '<table style="width:100%;border-collapse:collapse;font-size:9pt;">'
              . '<thead><tr style="background:#1e1b4b;">'
              . '<th style="padding:7px 8px;color:#fff;text-align:center;width:30px;">#</th>'
              . '<th style="padding:7px 8px;color:#fff;text-align:left;">Category</th>'
              . '<th style="padding:7px 8px;color:#fff;text-align:left;">Item / Description</th>'
              . '<th style="padding:7px 8px;color:#fff;text-align:center;width:40px;">Qty</th>'
              . '<th style="padding:7px 8px;color:#fff;text-align:right;width:90px;">Unit Price</th>'
              . '<th style="padding:7px 8px;color:#fff;text-align:right;width:100px;">Total</th>'
              . '</tr></thead><tbody>' . $qRows . '</tbody>'
              . '<tfoot><tr style="background:#1e1b4b;">'
              . '<td colspan="5" style="padding:8px;text-align:right;font-weight:bold;color:#fff;font-size:10pt;">Grand Total</td>'
              . '<td style="padding:8px;text-align:right;font-weight:bold;font-size:11pt;color:#fbbf24;">Rs. ' . number_format($grand, 2) . '</td>'
              . '</tr></tfoot></table>'
            : '<p style="color:#94a3b8;">No bundle items available.</p>';

        $productLinkHtml = $productLink
            ? '<p style="margin:4px 0;font-size:9pt;"><strong>View Product &amp; Curriculum:</strong> <span style="color:#1d6fa4;">' . $productLink . '</span></p>'
              . '<p style="margin:4px 0;font-size:9pt;"><strong>Direct Procurement Link:</strong> <span style="color:#1d6fa4;">' . $cartLink . '</span></p>'
            : '';

        // Dynamic content based on lab type & package
        if ($isSkillvationLab) {
            $headerTitle = 'SKILLVATION LAB PROPOSAL';
            $headerSub1  = 'Turnkey Future-Ready STEM, Robotics, AI &amp; Innovation Lab';
            $headerSub2  = 'Hands-On Experiential Learning, Coding, IoT &amp; Maker Space for Schools (Grades 1–10/12)';
            $subjectText = 'Proposal to establish a Future-Ready Skillvation Lab for Grades 1–10/12';

            $bodySections = '
  <h2>1. Background &amp; Rationale</h2>
  <p>Modern schooling requires a decisive transition from passive textbook learning into hands-on innovation and applied technology. In alignment with national pedagogical reforms (NEP 2020) and global STEM frameworks, the <strong>Skillvation Lab</strong> is designed as a premier, future-ready experiential space. It equips students from primary through senior grades with critical 21st-century competencies: computational thinking, robotics, sensor electronics, Artificial Intelligence (AI), Internet of Things (IoT), and human-centered Design Thinking.</p>

  <h2>2. Objectives &amp; Vision</h2>
  <ul>
    <li>Provide every student structured, age-appropriate experiential exposure to STEM, Robotics, IoT, and Artificial Intelligence.</li>
    <li>Cultivate computational logic, creative problem solving, scientific inquiry, and collaborative engineering through design-build challenges.</li>
    <li>Establish a state-of-the-art innovation and maker hub inside the school campus with zero operational friction.</li>
    <li>Prepare school teams for national and international robotics leagues, hackathons, and ATL-aligned innovation showcases.</li>
  </ul>

  <h2>3. Lab Infrastructure &amp; Space Planning</h2>
  <ul>
    <li>Recommended space: <strong>600 to 1,000 sq. ft.</strong> dedicated innovation space with modular student workbenches and ergonomic seating.</li>
    <li>Infrastructure zones: Mechanical Fabrication &amp; Maker Bench, Electronics &amp; Soldering Workstations, Robotics Testing Arena &amp; Track, and Digital Computing/AI Stations.</li>
    <li>Electrical &amp; safety: Safe multi-point surge-protected power strips, component storage organizers, and lab safety apparatus.</li>
  </ul>

  <h2>4. Curriculum &amp; Graded Learning Tracks</h2>
  <ul>
    <li><strong>Grades 1–3 (Early Explorers):</strong> Foundational mechanics, paper circuits, tactile block construction, visual sequencing.</li>
    <li><strong>Grades 4–5 (Young Engineers):</strong> Introductory visual coding (Scratch/Blockly), mechanical linkages, motorized builds, basic sensors.</li>
    <li><strong>Grades 6–8 (Maker &amp; IoT Innovators):</strong> Microcontroller architectures (Arduino, ESP32), sensor interfacing, circuit prototyping, introductory IoT and 3D design.</li>
    <li><strong>Grades 9–12 (Applied AI &amp; Advanced Robotics):</strong> Embedded C/Python programming, machine vision, autonomous systems, edge AI applications, and capstone engineering challenges.</li>
  </ul>

  <h2>5. Faculty Enablement &amp; Ongoing Support</h2>
  <ul>
    <li>Comprehensive multi-day masterclass certification for nominated science, computer, and STEM teachers.</li>
    <li>Complete instructional package: grade-wise lesson manuals, student activity workbooks, project assessment rubrics, and video walkthroughs.</li>
    <li>Dedicated academic coordinator and periodic virtual mentor sessions to ensure sustained lab utilization.</li>
  </ul>

  <h2>6. Measurable Institutional Outcomes</h2>
  <ul>
    <li>Turnkey transformation into a premier, technologically distinguished institution in the region.</li>
    <li>Verifiable growth in student analytical reasoning, digital dexterity, and collaborative problem solving.</li>
    <li>Student-built portfolio of working models, hardware prototypes, and innovation projects for science showcases and competitions.</li>
  </ul>';

            $conclusionText = 'The Skillvation Lab represents a comprehensive, turnkey investment in your school\'s academic future, providing students with the technological tools and mindset needed to excel in an AI-driven world. We look forward to partnering with your institution to establish this flagship innovation center.';
        } else {
            // Composite Skill Lab
            $headerTitle = 'CBSE COMPOSITE SKILL LAB PROPOSAL';
            
            if (str_contains($pkgLower, 'premium')) {
                $headerSub1 = 'Establishment of CBSE Composite Skill Lab (Premium Package)';
                $headerSub2 = 'Center of Excellence &amp; Advanced AI/Robotics Hub aligned with CBSE Circular Skill-75/2024 &amp; NEP 2020';
            } elseif (str_contains($pkgLower, 'advance')) {
                $headerSub1 = 'Establishment of CBSE Composite Skill Lab (Advance Package)';
                $headerSub2 = 'Enhanced Multi-Domain Practical &amp; Digital Prototyping Hub aligned with CBSE Circular Skill-75/2024';
            } elseif (str_contains($pkgLower, 'custom')) {
                $headerSub1 = 'Establishment of CBSE Composite Skill Lab (Custom Tailored Scope)';
                $headerSub2 = 'Custom Multi-Domain Practical Skill Hub aligned with CBSE Circular Skill-75/2024 &amp; NEP 2020';
            } else {
                $headerSub1 = 'Establishment of CBSE Standard Composite Skill Lab (Basic Package)';
                $headerSub2 = 'Turnkey 3 Forms of Work Setup aligned with CBSE Circular Skill-75/2024 &amp; NEP 2020';
            }

            $subjectText = 'Proposal to establish a CBSE Composite Skill Lab (' . $pkg . ') for Grades 6–10/12';

            $bodySections = '
  <h2>1. Background &amp; Rationale</h2>
  <p>CBSE has directed all affiliated schools to establish Composite Skill Labs in line with <strong>NEP 2020</strong> and <strong>NCF-SE</strong>, ensuring students gain mandatory hands-on, multi-sector vocational exposure alongside formal academics. This turnkey proposal presents a dedicated, CBSE-compliant Composite Skill Lab branded <strong>"Skillvation"</strong>, providing multi-disciplinary practical skills across Grades 6–12.</p>

  <h2>2. Objectives &amp; Regulatory Alignment</h2>
  <ul>
    <li>Provide every student in Grades 6–12 structured, hands-on exposure across multiple skill sectors in one integrated space.</li>
    <li>Build foundational competencies across all 3 NCF-SE Forms of Work: Life Forms, Machines &amp; Materials, and Human Services.</li>
    <li>Ensure 100% compliance with CBSE Composite Skill Lab mandates (Circulars Skill-75/2024 &amp; Skill-01/2025) in an efficient turnkey format.</li>
    <li>Develop critical thinking, practical problem solving, and vocational readiness through authentic project-based assessments.</li>
  </ul>

  <h2>3. Infrastructure &amp; Spatial Specifications</h2>
  <ul>
    <li>Dedicated space: <strong>600 sq. ft.</strong> (single composite lab) or <strong>two separate labs of 400 sq. ft. each</strong>, as prescribed by CBSE guidelines.</li>
    <li>Workstations: Modular heavy-duty work tables, dedicated domain storage cabinets, and safety-compliant electrical wiring with fire safety gear.</li>
  </ul>

  <h2>4. Curriculum Integration &amp; 3 Forms of Work</h2>
  <ul>
    <li><strong>Form 1 — Work with Life Forms:</strong> Agriculture, hydroponics, health &amp; wellness, food preservation, and herbal gardening.</li>
    <li><strong>Form 2 — Work with Machines &amp; Materials:</strong> Electronics, robotics, carpentry, electrical tools, mechanical mechanisms, and 3D printing.</li>
    <li><strong>Form 3 — Work in Human Services:</strong> Retail, financial literacy, tourism, graphic communication, and digital media skills.</li>
    <li>Grade 6–8: foundational exploration across all domains; Grade 9–12: deep specialisation and board skill elective preparation.</li>
  </ul>

  <h2>5. Faculty Enablement &amp; Safety Protocols</h2>
  <ul>
    <li>Orientation and safety training for lab coordinators and science/vocational instructors prior to lab commissioning.</li>
    <li>Standard Operating Procedures (SOPs), safety signage, and structured domain project guides for every student batch.</li>
  </ul>

  <h2>6. Expected Institutional Outcomes</h2>
  <ul>
    <li>Full regulatory compliance for CBSE affiliation and inspections.</li>
    <li>Measurable improvement in student practical competence and creative problem-solving ability.</li>
    <li>A distinctive, future-focused institutional identity for skill education and holistic student development.</li>
  </ul>';

            $conclusionText = 'Skillvation offers the school a structured, CBSE-aligned way to give students hands-on exposure across a broad range of skill domains. We request approval to proceed to the detailed planning and procurement stage.';
        }

        $html = '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 10.5pt; color: #1a1a2e; margin:0; padding:0; }
  .page { padding: 30px 38px; }
  .hdr  { border-bottom: 3px solid #1e1b4b; padding-bottom: 12px; margin-bottom: 18px; }
  .hdr-title { font-size:17pt; font-weight:bold; color:#1e1b4b; margin:0 0 3px; }
  .hdr-sub   { font-size:9.5pt; color:#475569; margin:2px 0; }
  .hdr-badge { display:inline-block; background:#1e1b4b; color:#fff; font-size:8.5pt;
               font-weight:bold; padding:3px 11px; border-radius:3px; margin-top:7px; }
  .meta { width:100%; border-collapse:collapse; margin-bottom:16px; font-size:9.5pt; }
  .meta td { padding:4px 8px; vertical-align:top; }
  .ml { color:#64748b; width:90px; }
  .mv { font-weight:bold; color:#1e1b4b; }
  h2  { font-size:11.5pt; font-weight:bold; color:#1e1b4b; margin:18px 0 5px;
        border-left:4px solid #f97316; padding-left:9px; }
  p   { font-size:9.5pt; line-height:1.65; color:#334155; margin:0 0 8px; }
  ul  { font-size:9.5pt; line-height:1.75; color:#334155; padding-left:20px; margin:5px 0 10px; }
  li  { margin-bottom:2px; }
  .dt { width:100%; border-collapse:collapse; margin-bottom:12px; font-size:9pt; }
  .dt th { background:#1e1b4b; color:#fff; padding:6px 8px; text-align:left; }
  .dt td { padding:5px 8px; border:1px solid #e2e8f0; vertical-align:top; }
  .dt tr:nth-child(even) td { background:#f8fafc; }
  .link-box { background:#f0f9ff; border:1px solid #bae6fd; border-radius:5px;
              padding:10px 14px; margin:10px 0 16px; font-size:9pt; }
  .note { background:#fffbeb; border:1px solid #fde68a; border-radius:4px;
          padding:8px 12px; font-size:8.5pt; color:#78350f; margin-top:8px; }
  .footer { margin-top:24px; padding-top:12px; border-top:2px solid #e2e8f0;
            font-size:8.5pt; color:#64748b; }
</style>
</head>
<body>
<div class="page">
  <div class="hdr">
    <div class="hdr-title">' . $headerTitle . '</div>
    <div class="hdr-sub">' . $headerSub1 . '</div>
    <div class="hdr-sub">' . $headerSub2 . '</div>
    <div class="hdr-badge">MYSKOOL LEARNING PRIVATE LIMITED</div>
  </div>

  <table class="meta">
    <tr>
      <td class="ml">To:</td>       <td class="mv">' . $name . $designation . '</td>
      <td class="ml">Date:</td>     <td class="mv">' . $date . '</td>
    </tr>
    <tr>
      <td class="ml">School:</td>   <td class="mv">' . $school . ($city ? ', ' . $city : '') . '</td>
      <td class="ml">Phone:</td>    <td class="mv">' . $phone . '</td>
    </tr>
    <tr>
      <td class="ml">Package:</td>  <td class="mv">' . $pkg . '</td>
      <td class="ml">Email:</td>    <td class="mv">' . $email . '</td>
    </tr>
    <tr>
      <td class="ml">Subject:</td>
      <td colspan="3">' . $subjectText . '</td>
    </tr>
  </table>

  ' . $bodySections . '

  <h2>7. Product &amp; Curriculum Details</h2>
  ' . $productLinkHtml . '

  <h2>8. Quotation &amp; Bill of Materials — ' . $pkg . '</h2>
  ' . $quotTable . '
  <div class="note">
    A detailed, itemized budget and implementation schedule will be finalized upon domain priority sign-off.
    Phased procurement can be scheduled across budget cycles to meet institutional milestones.
  </div>

  <h2>9. Conclusion</h2>
  <p>' . $conclusionText . '</p>

  <div class="footer">
    <strong>MYSKOOL LEARNING PRIVATE LIMITED</strong><br>
    +91 98450 26782 &nbsp;|&nbsp; infomyskoolonline@gmail.com &nbsp;|&nbsp; www.skillvation.com <br>
    Bengaluru, Karnataka
  </div>
</div>
</body>
</html>';

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    // ──────────────────────────────────────────────────────────────
    // Push lead to LeadDemo CRM API
    // ──────────────────────────────────────────────────────────────
    private function sendToLeadDemo(array $data): void
    {
        $apiUrl = env('LEADDEMO_API_URL');

        if (empty($apiUrl)) return;

        try {
            $source      = 'SV_WEBSITE';
            $planInfo    = self::PLAN_MAP[$source] ?? null;
            $packageName = $data['course_title'] ?? ($planInfo['label'] ?? ucwords(str_replace(['-','_'],' ',$source)));

            // Remarks: package + request type
            $remarks = trim(implode(' — ', array_filter([
                $packageName,
                $data['message'] ?? '',
            ])));

            // Notes: school + city + address
            $notes = trim(implode(', ', array_filter([
                $data['school']  ?? '',
                $data['city']    ?? '',
                $data['address'] ?? '',
            ])));

            $payload = [
                'first_name'    => $data['name'],
                'phone_number'  => $data['phone'] ?? '',
                'user'          => '',
                'language'      => 'English',
                'remarks'       => $remarks ?: ('Interested in ' . $packageName),
                'demo_type'     => 'walkthrough',
                'demo_date'     => now()->addHour()->format('Y-m-d H:i'),
                'notes'         => $notes ?: $packageName,
                'incharge_name' => $data['school'] ?? 'website',
                'campaign'      => $packageName,
                'designation'   => $data['designation'] ?? '',
                'source'        => $source,
                'assigned_to'   => 116 ?? 1,
            ];

            $response = Http::timeout(8)
                ->withoutVerifying()
                ->post($apiUrl, $payload);

            Log::info('[LeadDemo] Pushed lead', [
                'status'   => $response->status(),
                'phone'    => $data['phone'] ?? '',
                'source'   => $source,
                'response' => $response->json(),
            ]);

        } catch (\Throwable $e) {
            Log::warning('[LeadDemo] Push failed: ' . $e->getMessage(), [
                'phone' => $data['phone'] ?? '',
            ]);
            // Never block the main enquiry flow
        }
    }
}
