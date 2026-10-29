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
            'slug'     => null,
            'label'    => 'Composite Skill Lab',
            'page_url' => '/labs/composite-skill',
            'cart_url' => '/labs/composite-skill',
        ],
        'composite-skill-lab-callback' => [
            'slug'     => null,
            'label'    => 'Composite Skill Lab — Callback',
            'page_url' => '/labs/composite-skill',
            'cart_url' => '/labs/composite-skill',
        ],
        'composite-skill-custom-package' => [
            'slug'     => null,
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
        // Course detail quote form
        'course-detail-quote'          => [
            'slug'     => null,
            'label'    => 'Course Enquiry',
            'page_url' => '/courses',
            'cart_url' => '/courses',
        ],
    ];

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
            $planInfo    = self::PLAN_MAP[$source] ?? null;
            $packageName = $validated['course_title'] ?? ($planInfo['label'] ?? $sourceLabel);

            // ── Build quotation data (supports custom configurator JSON or club shop bundle)
            $quotationData = null;
            if (!empty($validated['quotation'])) {
                $rawQ = is_string($validated['quotation']) ? json_decode($validated['quotation'], true) : $validated['quotation'];
                if (is_array($rawQ) && !empty($rawQ['selected_components'])) {
                    $items = [];
                    $grouped = [];
                    $grandTotal = 0;
                    foreach ($rawQ['selected_components'] as $c) {
                        $itemTitle = $c['title'] ?? 'Component';
                        $cat       = $c['category'] ?? 'General';
                        $qty       = (int) ($c['qty'] ?? 1);
                        $price     = (float) ($c['unit_price'] ?? 0);
                        $total     = (float) ($c['subtotal'] ?? ($price * $qty));
                        $grandTotal += $total;
                        $items[] = compact('cat', 'qty', 'price', 'total') + ['item' => $itemTitle];
                        $grouped[$cat] = ($grouped[$cat] ?? 0) + $total;
                    }
                    $quotationData = [
                        'items'       => $items,
                        'grouped'     => $grouped,
                        'grand_total' => $grandTotal,
                    ];
                }
            }

            if (!$quotationData) {
                // ── Fetch bundle items server-side from Club Shop API ─
                $bundleComponents = $this->fetchBundleComponents($planInfo['slug'] ?? null);
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
            $adminSubject = ($adminTpl && !empty($adminTpl->subject))
                ? str_replace(array_keys($vars), array_values($vars), $adminTpl->subject)
                : "New Skill Lab Enquiry: {$packageName} – " . ($validated['school'] ?? 'School');
            $adminBody = ($adminTpl && !empty($adminTpl->message))
                ? str_replace(array_keys($vars), array_values($vars), $adminTpl->message)
                : "<p>New enquiry from <strong>{$validated['name']}</strong> ({$validated['school']})</p><p>Package: {$packageName}</p><p>Phone: {$validated['phone']}</p><p>Email: {$validated['email']}</p>{$quotationHtml}";

            Mail::to($adminEmail)->send(new DefaultMail(['subject' => $adminSubject], $adminBody));

            // ── 2. Confirmation + PDF proposal to enquirer ────────
            $confirmTpl = EmailTemplate::where('name', 'skill_lab_enquiry_confirmation')->first();
            $confirmSubject = ($confirmTpl && !empty($confirmTpl->subject))
                ? str_replace(array_keys($vars), array_values($vars), $confirmTpl->subject)
                : "Skillvation Composite Skill Lab Proposal – " . ($validated['school'] ?? 'School');
            $confirmBody = ($confirmTpl && !empty($confirmTpl->message))
                ? str_replace(array_keys($vars), array_values($vars), $confirmTpl->message)
                : "<p>Dear {$validated['name']},</p><p>Thank you for your interest in Skillvation. Please find attached your customized Proposal PDF.</p>{$quotationHtml}";

            // Generate PDF with full proposal + dynamic bundle items
            $pdfBytes  = $this->generateProposalPdf($validated, $packageName, $quotationData, $planInfo);
            $pdfName   = 'Skillvation_Proposal_'
                       . preg_replace('/[^A-Za-z0-9_]/', '_', $validated['school'] ?? 'School')
                       . '.pdf';

            Mail::to($validated['email'])
                ->send(new DefaultMail(
                    ['subject' => $confirmSubject],
                    $confirmBody,
                    $pdfBytes,
                    $pdfName
                ));

        } catch (\Throwable $e) {
            Log::error('[CourseEnquiry] Mail failed: ' . $e->getMessage(), [
                'enquiry_id' => $enquiry->id,
                'trace'      => $e->getTraceAsString(),
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Enquiry submitted successfully.',
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // Fetch bundle components from Club Shop API (server-side)
    // ──────────────────────────────────────────────────────────────
    private function fetchBundleComponents(?string $slug): array
    {
        if (!$slug) return [];

        try {
            // Use the local club-shop API (same server)
            $apiBase  = rtrim(env('SHOP_API_BASE_URL', 'https://test.myskill.club/api/'), '/');
            // Prefer local URL for server-side calls
            $localUrl = url('/club-shop/api/products/slug/' . $slug);

            $response = Http::timeout(10)->get($localUrl);

            if ($response->successful()) {
                return $response->json('data.bundle_components', []);
            }

            // Fallback to configured API base
            $response = Http::timeout(10)->get($apiBase . '/products/slug/' . $slug);
            return $response->successful() ? $response->json('data.bundle_components', []) : [];

        } catch (\Throwable $e) {
            Log::warning('[CourseEnquiry] Bundle fetch failed: ' . $e->getMessage());
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
    public function resendMailForEnquiry(array $data): void    {
        self::setMailConfig();

        $adminEmail  = Setting::where('key', 'contact_message_receiver_mail')->value('value')
                    ?? config('mail.from.address');
        $source      = $data['source'] ?? '';
        $sourceLabel = ucwords(str_replace(['-', '_'], ' ', $source));
        $planInfo    = self::PLAN_MAP[$source] ?? null;
        $packageName = $data['course_title'] ?? ($planInfo['label'] ?? $sourceLabel);

        $quotationData = null;
        if (!empty($data['quotation'])) {
            $q = is_string($data['quotation']) ? json_decode($data['quotation'], true) : $data['quotation'];
            if (!empty($q['items'])) {
                $quotationData = [
                    'items'       => $q['items'],
                    'grouped'     => $q['grouped'] ?? [],
                    'grand_total' => $q['grand_total'] ?? ($q['total'] ?? 0),
                ];
            } elseif (!empty($q['selected_components'])) {
                $items = [];
                $grouped = [];
                $grandTotal = 0;
                foreach ($q['selected_components'] as $c) {
                    $itemTitle = $c['title'] ?? 'Component';
                    $cat       = $c['category'] ?? ($c['category_name'] ?? 'General');
                    $qty       = (int) ($c['qty'] ?? ($c['required_quantity'] ?? 1));
                    $price     = (float) ($c['unit_price'] ?? 0);
                    $total     = (float) ($c['subtotal'] ?? ($price * $qty));
                    $grandTotal += $total;
                    $items[] = compact('cat', 'qty', 'price', 'total') + ['item' => $itemTitle];
                    $grouped[$cat] = ($grouped[$cat] ?? 0) + $total;
                }
                $quotationData = [
                    'items'       => $items,
                    'grouped'     => $grouped,
                    'grand_total' => $grandTotal,
                ];
            }
        }

        if (!$quotationData && !empty($data['selected_components'])) {
            $rawComponents = is_string($data['selected_components']) ? json_decode($data['selected_components'], true) : $data['selected_components'];
            if (is_array($rawComponents) && !empty($rawComponents)) {
                $items = [];
                $grouped = [];
                $grandTotal = 0;
                foreach ($rawComponents as $c) {
                    if (empty($c['title'])) continue;
                    $qty = (int) ($c['required_quantity'] ?? ($c['qty'] ?? 1));
                    $price = (float) ($c['unit_price'] ?? ($c['price'] ?? 0));
                    if ($qty <= 0) continue;
                    $cat = $c['category_name'] ?? ($c['cat'] ?? 'General');
                    $total = $price * $qty;
                    $grandTotal += $total;
                    $items[] = compact('cat', 'qty', 'price', 'total') + ['item' => $c['title']];
                    $grouped[$cat] = ($grouped[$cat] ?? 0) + $total;
                }
                $quotationData = [
                    'items'       => $items,
                    'grouped'     => $grouped,
                    'grand_total' => $grandTotal,
                ];
            }
        }

        if (!$quotationData) {
            $bundleComponents = $this->fetchBundleComponents($planInfo['slug'] ?? null);
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
        $adminSubject = ($adminTpl && !empty($adminTpl->subject))
            ? str_replace(array_keys($vars), array_values($vars), $adminTpl->subject)
            : "Skill Lab Enquiry: {$packageName} – " . ($data['school'] ?? 'School');
        $adminBody = ($adminTpl && !empty($adminTpl->message))
            ? str_replace(array_keys($vars), array_values($vars), $adminTpl->message)
            : "<p>Enquiry for <strong>{$data['name']}</strong> ({$data['school']})</p><p>Package: {$packageName}</p>{$quotationHtml}";

        Mail::to($adminEmail)->send(new DefaultMail(['subject' => $adminSubject], $adminBody));

        // Confirmation + PDF to school
        $confirmTpl = EmailTemplate::where('name', 'skill_lab_enquiry_confirmation')->first();
        $confirmSubject = ($confirmTpl && !empty($confirmTpl->subject))
            ? str_replace(array_keys($vars), array_values($vars), $confirmTpl->subject)
            : "Skillvation Composite Skill Lab Proposal – " . ($data['school'] ?? 'School');
        $confirmBody = ($confirmTpl && !empty($confirmTpl->message))
            ? str_replace(array_keys($vars), array_values($vars), $confirmTpl->message)
            : "<p>Dear {$data['name']},</p><p>Please find attached your customized Proposal PDF.</p>{$quotationHtml}";

        $pdfBytes = $this->generateProposalPdf($data, $packageName, $quotationData, $planInfo);
        $pdfName  = 'Skillvation_Proposal_'
                  . preg_replace('/[^A-Za-z0-9_]/', '_', $data['school'] ?? 'School')
                  . '.pdf';
        Mail::to($data['email'])
            ->send(new DefaultMail(['subject' => $confirmSubject], $confirmBody, $pdfBytes, $pdfName));
    }

    // ──────────────────────────────────────────────────────────────
    // Generate full A4 proposal PDF (docx content + bundle items)
    // ──────────────────────────────────────────────────────────────
    private function generateProposalPdf(array $data, string $packageName, array $quotationData, ?array $planInfo): string
    {
        $name        = htmlspecialchars($data['name']);
        $designation = $data['designation'] ? ', ' . htmlspecialchars($data['designation']) : '';
        $school      = htmlspecialchars($data['school']  ?? '');
        $city        = htmlspecialchars($data['city']    ?? '');
        $phone       = htmlspecialchars($data['phone']   ?? '');
        $email       = htmlspecialchars($data['email']);
        $address     = htmlspecialchars($data['address'] ?? '');
        $pkg         = htmlspecialchars($packageName);
        $date        = now()->format('d M Y');

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
            ? '<p style="margin:4px 0;font-size:9pt;"><strong>View Product:</strong> <span style="color:#1d6fa4;">' . $productLink . '</span></p>'
              . '<p style="margin:4px 0;font-size:9pt;"><strong>Add to Cart:</strong> <span style="color:#1d6fa4;">' . $cartLink . '</span></p>'
            : '';

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
    <div class="hdr-title">PROPOSAL</div>
    <div class="hdr-sub">Establishment of Standard Composite Skill Lab</div>
    <div class="hdr-sub">A multi-domain, hands-on skill education space for Grades 6–10</div>
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
      <td colspan="3">Proposal to establish a Standard Composite Skill Lab for Grades 6–10</td>
    </tr>
  </table>

  <h2>1. Background &amp; Rationale</h2>
  <p>CBSE has directed all affiliated schools to establish Composite Skill Labs in line with <strong>NEP 2020</strong> and <strong>NCF-SE</strong>, so that students gain hands-on, multi-sector skill exposure alongside academic learning. This proposal recommends a dedicated Composite Skill Lab — branded <strong>"Skillvation"</strong> — providing hands-on learning across a broad mix of skill domains for Grades 6–12.</p>

  <h2>2. Objectives</h2>
  <ul>
    <li>Provide every student in Grades 6–12 structured, hands-on exposure across multiple skill sectors in one integrated space.</li>
    <li>Build foundational competencies in computing, electronics, robotics and design alongside life-relevant domains such as healthcare, agriculture, apparel and food production.</li>
    <li>Meet CBSE\'s Composite Skill Lab mandate (Circular Skill-75/2024 &amp; Skill-01/2025) in a cost-effective, single-space model.</li>
    <li>Develop critical thinking, creativity, collaboration and problem-solving through project-based learning.</li>
  </ul>

  <h2>3. Infrastructure Requirements</h2>
  <ul>
    <li>Dedicated space: <strong>600 sq. ft.</strong> (single composite lab) or <strong>two separate labs of 400 sq. ft. each</strong>, as per CBSE guidelines.</li>
    <li>Reliable power supply with safety-compliant wiring, adequate lighting and ventilation.</li>
  </ul>

  <h2>4. Curriculum Integration</h2>
  <ul>
    <li>Aligns with NCF-SE\'s three "forms of work" and mandatory skill-subject requirement from Grade 6 onward.</li>
    <li>Weekly rotation across domains, with project-based assessment.</li>
    <li>Grade 6–8: foundational exposure across all domains; Grade 9–12: specialisation in one or two domains.</li>
  </ul>

  <h2>5. Staffing &amp; Training</h2>
  <ul>
    <li>Existing computer/science faculty trained as lab coordinators, supplemented by domain resource persons.</li>
    <li>Orientation and safety training for all staff prior to roll out.</li>
  </ul>

  <h2>6. Expected Outcomes</h2>
  <ul>
    <li>Full compliance with CBSE\'s Composite Skill Lab mandate.</li>
    <li>Measurable improvement in students\' practical, vocational and problem-solving skills.</li>
    <li>A distinctive, branded skill-education identity for the school.</li>
    <li>A scalable model that can extend to further grades in a future phase.</li>
  </ul>

  <h2>7. Product Details</h2>
  ' . $productLinkHtml . '

  <h2>8. Quotation — ' . $pkg . '</h2>
  ' . $quotTable . '
  <div class="note">
    A detailed, itemized budget will be prepared once domain priorities and vendor quotations are finalised.
    Phased procurement (core IT/electronics first, other domains in subsequent phases) can be considered
    to spread costs across budget cycles.
  </div>

  <h2>9. Conclusion</h2>
  <p>Skillvation offers the school a structured, CBSE-aligned way to give students hands-on exposure across a broad range of skill domains. We request approval to proceed to the detailed planning and procurement stage.</p>

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
