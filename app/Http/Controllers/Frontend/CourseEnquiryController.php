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
        'skillvation-lab-stem-advance' => [
            'slug'     => 'skillvation-lab-advance',
            'label'    => 'Skillvation Lab Advance',
            'page_url' => '/club-shop/skillvation-lab-advance',
            'cart_url' => '/club-shop/advance-cart',
        ],
        'skillvation-lab-stem-premium' => [
            'slug'     => 'skillvation-lab-premium',
            'label'    => 'Skillvation Lab Premium',
            'page_url' => '/club-shop/skillvation-lab-premium',
            'cart_url' => '/club-shop/premium-cart',
        ],
        'skillvation-lab'              => [
            'slug'     => 'skillvation-lab',
            'label'    => 'Skillvation Lab',
            'page_url' => '/club-shop/skillvation-lab',
            'cart_url' => '/club-shop/skillvation-cart',
        ],
        'skillvation-lab-advance'      => [
            'slug'     => 'skillvation-lab-advance',
            'label'    => 'Skillvation Lab Advance',
            'page_url' => '/club-shop/skillvation-lab-advance',
            'cart_url' => '/club-shop/advance-cart',
        ],
        'skillvation-lab-premium'      => [
            'slug'     => 'skillvation-lab-premium',
            'label'    => 'Skillvation Lab Premium',
            'page_url' => '/club-shop/skillvation-lab-premium',
            'cart_url' => '/club-shop/premium-cart',
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
            'skillvation-lab-premium' => [
                'slug'     => 'skillvation-lab-premium',
                'label'    => 'Skillvation STEM Premium Package',
                'page_url' => '/club-shop/skillvation-lab-premium',
                'cart_url' => '/club-shop/premium-cart',
            ],
            'skillvation-lab-advance' => [
                'slug'     => 'skillvation-lab-advance',
                'label'    => 'Skillvation STEM Advance Package',
                'page_url' => '/club-shop/skillvation-lab-advance',
                'cart_url' => '/club-shop/advance-cart',
            ],
            'skillvation-lab' => [
                'slug'     => 'skillvation-lab',
                'label'    => 'Skillvation STEM Basic Package',
                'page_url' => '/club-shop/skillvation-lab',
                'cart_url' => '/club-shop/basic-cart',
            ],
            'cbse-composite-skill-lab-premium-73' => [
                'slug'     => 'cbse-composite-skill-lab-premium-73',
                'label'    => 'Composite Skill Lab Premium Package',
                'page_url' => '/club-shop/cbse-composite-skill-lab-premium-73',
                'cart_url' => '/club-shop/premium-cart',
            ],
            'cbse-composite-skill-lab-advanced-72' => [
                'slug'     => 'cbse-composite-skill-lab-advanced-72',
                'label'    => 'Composite Skill Lab Advance Package',
                'page_url' => '/club-shop/cbse-composite-skill-lab-advanced-72',
                'cart_url' => '/club-shop/advance-cart',
            ],
            default => [
                'slug'     => 'cbse-composite-skill-lab-basic-68',
                'label'    => 'Composite Skill Lab Basic Package',
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

        // 1. Skillvation Lab / STEM pages
        if (str_contains($sourceLower, 'skillvation')
            || str_contains($sourceLower, 'stem')
            || str_contains($titleLower, 'skillvation')
            || str_contains($titleLower, 'stem')) {
            if (str_contains($sourceLower, 'premium') || str_contains($titleLower, 'premium') || str_contains($tierLower, 'premium') || str_contains($tierLower, 'elite')) {
                return 'skillvation-lab-premium';
            }
            if (str_contains($sourceLower, 'advance') || str_contains($titleLower, 'advance') || str_contains($tierLower, 'advance') || str_contains($tierLower, 'growth')) {
                return 'skillvation-lab-advance';
            }
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

            $pkg   = trim((string)($c['package'] ?? ($c['package_title'] ?? '')));
            $cat   = $c['category'] ?? ($c['category_name'] ?? ($c['cat'] ?? 'General'));
            $qty   = (int) ($c['qty'] ?? ($c['required_quantity'] ?? ($c['quantity'] ?? 1)));
            if ($qty <= 0) $qty = 1;

            $price = (float) ($c['unit_price'] ?? ($c['price'] ?? 0));
            $total = (float) ($c['subtotal'] ?? ($c['total'] ?? ($c['total_price'] ?? ($price * $qty))));

            $grandTotal += $total;
            $items[] = [
                'package'=> $pkg,
                'cat'   => $cat,
                'item'  => $title,
                'qty'   => $qty,
                'price' => $price,
                'total' => $total,
            ];
            $grouped[$cat] = ($grouped[$cat] ?? 0) + $total;
        }

        if (empty($items)) return null;

        $computedTotal = array_sum(array_column($items, 'total'));
        $rawTotal = isset($raw['raw_total']) && (float)$raw['raw_total'] > 0
            ? (float)$raw['raw_total']
            : $computedTotal;

        $subtotal = isset($raw['subtotal']) && (float)$raw['subtotal'] > 0
            ? (float)$raw['subtotal']
            : $computedTotal;

        $discount   = (float)($raw['discount'] ?? ($raw['discount_amount'] ?? 0));
        $taxPercent = (float)($raw['tax_percent'] ?? 0);
        $taxAmount  = (float)($raw['tax_amount'] ?? 0);
        $shipping   = (float)($raw['shipping'] ?? 0);

        if (isset($raw['grand_total']) && (float)$raw['grand_total'] > 0) {
            $grandTotal = (float)$raw['grand_total'];
            if ($discount <= 0 && $taxAmount <= 0 && $shipping <= 0 && $subtotal > $grandTotal) {
                $discount = round($subtotal - $grandTotal, 2);
            }
        } else {
            $grandTotal = max(0, round($subtotal - $discount + $taxAmount + $shipping, 2));
        }

        return [
            'items'       => $items,
            'grouped'     => $grouped,
            'packages'    => $raw['packages'] ?? [],
            'raw_total'   => $rawTotal,
            'subtotal'    => $subtotal,
            'discount'    => $discount,
            'tax_percent' => $taxPercent,
            'tax_amount'  => $taxAmount,
            'shipping'    => $shipping,
            'grand_total' => $grandTotal,
        ];
    }

    /**
     * Send a proposal straight from a club-shop plan cart.
     * Auth-required (SSO session); reuses the full store() pipeline
     * (PDF + email to enquirer & admin + WhatsApp + LeadDemo).
     * The cart items become the quotation so the proposal reflects the
     * user's actual (possibly edited) cart, not just the default bundle.
     */
    public function cartProposal(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Please sign in to request a proposal.'], 401);
        }

        // Map the cart's planKey → enquiry source + human package label.
        $planKey = (string) $request->input('plan_key', '');
        $planMap = [
            // Skillvation (STEM) carts
            'skillvation'         => ['source' => 'skillvation-lab-stem',         'label' => 'Skillvation Lab'],
            'skillvation-advance' => ['source' => 'skillvation-lab-stem-advance', 'label' => 'Skillvation Lab Advance'],
            'skillvation-premium' => ['source' => 'skillvation-lab-stem-premium', 'label' => 'Skillvation Lab Premium'],
            // Composite Skill Lab carts
            'basic'               => ['source' => 'composite-skill-lab-basic',    'label' => 'Basic Composite Skill Lab'],
            'advance'             => ['source' => 'composite-skill-lab-advance',  'label' => 'Advance Composite Skill Lab'],
            'premium'             => ['source' => 'composite-skill-lab-premium',  'label' => 'Premium Composite Skill Lab'],
        ];
        $plan = $planMap[$planKey] ?? $planMap['skillvation'];

        // Build (and sanitize) the quotation from the posted cart items.
        // Prices/items come from the request but are re-normalized here; the
        // proposal is informational (no payment), so this is acceptable.
        $rawItems = $request->input('items', []);
        if (is_string($rawItems)) {
            $decoded = json_decode($rawItems, true);
            $rawItems = is_array($decoded) ? $decoded : [];
        }

        $rawPackages = $request->input('packages', []);
        if (is_string($rawPackages)) {
            $decodedPkg = json_decode($rawPackages, true);
            $rawPackages = is_array($decodedPkg) ? $decodedPkg : [];
        }

        $items = [];
        $computedRawTotal = 0.0;
        foreach ((array) $rawItems as $it) {
            $title = trim((string) ($it['item'] ?? $it['title'] ?? $it['name'] ?? ''));
            if ($title === '') continue;
            $qty   = max(0, (int) ($it['qty'] ?? $it['quantity'] ?? 0));
            $price = max(0, (float) ($it['unit_price'] ?? $it['price'] ?? 0));
            $total = isset($it['total']) ? (float) $it['total'] : ($qty * $price);
            $pkg   = trim((string) ($it['package'] ?? ''));
            $items[] = [
                'package'    => $pkg,
                'category'   => trim((string) ($it['category'] ?? 'General')) ?: 'General',
                'item'       => mb_substr($title, 0, 255),
                'qty'        => $qty,
                'unit_price' => $price,
                'total'      => $total,
            ];
            $computedRawTotal += $total;
        }

        // Determine packages and course title
        $packageTitles = [];
        if (!empty($rawPackages) && is_array($rawPackages)) {
            foreach ($rawPackages as $p) {
                if (!empty($p['title']) && !in_array($p['title'], $packageTitles, true)) {
                    $packageTitles[] = $p['title'];
                }
            }
        }
        if (empty($packageTitles)) {
            foreach ($items as $it) {
                if (!empty($it['package']) && !in_array($it['package'], $packageTitles, true)) {
                    $packageTitles[] = $it['package'];
                }
            }
        }
        $courseTitle = !empty($packageTitles) ? implode(' + ', $packageTitles) : $plan['label'];

        $rawTotal   = $request->has('raw_total') ? (float)$request->input('raw_total') : $computedRawTotal;
        $subtotal   = $request->has('subtotal') ? (float)$request->input('subtotal') : $computedRawTotal;
        $discount   = max(0, (float)$request->input('discount', $request->input('discount_amount', 0)));
        $taxAmount  = (float)$request->input('tax_amount', 0);
        $taxPercent = (float)$request->input('tax_percent', 0);
        $grandTotal = $request->has('grand_total')
            ? (float)$request->input('grand_total')
            : max(0, round($subtotal - $discount + $taxAmount, 2));

        $quotation = !empty($items)
            ? json_encode([
                'items'        => $items,
                'packages'     => $rawPackages,
                'raw_total'    => $rawTotal,
                'subtotal'     => $subtotal,
                'discount'     => $discount,
                'tax_percent'  => $taxPercent,
                'tax_amount'   => $taxAmount,
                'grand_total'  => $grandTotal,
            ])
            : null;

        // Forge the inputs store() expects, then delegate to the shared pipeline.
        $request->merge([
            'source'       => $plan['source'],
            'course_title' => $courseTitle,
            'message'      => 'Request Detailed Proposal',
            'quotation'    => $quotation,
            // name/email/school/etc. are auto-filled from the user inside store().
        ]);

        return $this->store($request);
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
            'board'         => ['nullable', 'string', 'max:100'],
            'quotation'     => ['nullable', 'string'],
            'course_id'     => ['nullable'],
            'api_course_id' => ['nullable'],
            'course_title'  => ['nullable', 'string', 'max:255'],
            'source'        => ['nullable', 'string', 'max:100'],
        ]);

        // `board` (e.g. the School Board on the labs enquiry forms) has no column
        // of its own — fold it into the notes/address so it is still captured.
        if (!empty($validated['board'])) {
            $boardLine = 'School Board: ' . $validated['board'];
            $validated['address'] = trim($boardLine . (empty($validated['address']) ? '' : "\n" . $validated['address']));
        }
        // Not a column on course_enquiries — drop before persisting.
        unset($validated['board']);

        $enquiry = CourseEnquiry::create($validated);

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
            $packageName = preg_replace('/\s*[-–—]\s*Enquiry\s*$/iu', '', trim($packageName));

            // Determine if this is a Skillvation Lab or Composite Skill Lab
            $isSkillvationLab = str_contains(strtolower($source), 'skillvation')
                             || str_contains(strtolower($source), 'stem')
                             || str_contains(strtolower($packageName), 'skillvation')
                             || str_contains(strtolower($packageName), 'stem');

            // ── Build quotation data (supports custom configurator JSON or club shop bundle)
            $quotationData = $this->parseQuotationData($validated['quotation'] ?? null);

            if (!$quotationData) {
                // ── Fetch bundle items server-side from Club Shop API / DB ─
                $bundleFetch   = $this->fetchBundleComponents($targetSlug);
                $components    = $bundleFetch['components'] ?? (is_array($bundleFetch) && isset($bundleFetch[0]) ? $bundleFetch : []);
                $bundleInfo    = $bundleFetch['bundle'] ?? null;
                $quotationData = $this->buildQuotationData($components, $bundleInfo);
            } elseif (!empty($targetSlug) && (empty($quotationData['discount']) || (float)$quotationData['discount'] <= 0)) {
                // If quotation was passed without discount, check if package has flat discount in clubshop
                try {
                    $bundleRow = DB::connection('clubshop')->table('products')->where('slug', $targetSlug)->where('is_deleted', 0)->first();
                    if ($bundleRow && (float)($bundleRow->discount_amount ?? 0) > 0) {
                        $quotationData['discount'] = (float)$bundleRow->discount_amount;
                        $quotationData['grand_total'] = max(0, round($quotationData['subtotal'] - $quotationData['discount'], 2));
                    }
                } catch (\Throwable $e) {}
            }

            // ── Push to LeadDemo CRM ──────────────────────────────────
            // The request type decides which endpoint is hit (see sendToLeadDemo):
            //   "Schedule a Demo with Academic Team" / "Book a Skill Lab Visit" => /api/demos/create
            //   "Request Detailed Proposal"                                     => /api/leads/create-with-demo
            $this->sendToLeadDemo($validated, $quotationData);

            // ── Build the HTML table (used in email body for admin)
            $quotationHtml = $this->renderQuotationTable($quotationData);

            // ── Product page links ────────────────────────────────
            $baseUrl     = url('/');
            $productLink = $planInfo ? $baseUrl . $planInfo['page_url'] : '';
            $cartLink    = $planInfo ? $baseUrl . $planInfo['cart_url']  : '';

            // ── Variable map ──────────────────────────────────────
            $vars = [
                '{{name}}'            => htmlspecialchars($validated['name']),
                '{{designation}}'     => !empty($validated['designation']) ? ', ' . htmlspecialchars($validated['designation']) : '',
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
                    $effectiveCartUrl = $cartLink ?: ($productLink ?: url('/club-shop'));
                    $waPayload = [
                        'name'               => $validated['name'],
                        'school'             => $validated['school'] ?? 'School',
                        'package'            => $packageName,
                        'request_type'       => $validated['message'] ?? 'Detailed Proposal',
                        'total'              => $quotationData['grand_total'] ?? 0,
                        'pdf_url'            => $pdfPublicUrl,
                        'product_url'        => $productLink ?: url('/labs'),
                        'cart_url'           => $effectiveCartUrl,
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
            'package'      => $packageName,
            'school'       => $validated['school'] ?? ($enquiry->school ?? ''),
            'total'        => isset($quotationData['grand_total']) ? '₹' . number_format((float) $quotationData['grand_total'], 2) : '',
            'pdf_url'      => $pdfPublicUrl ?? null,
            'whatsapp_url' => $whatsappUrl ?? null,
            'cart_url'     => $cartLink ?: ($productLink ?: url('/club-shop')),
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // Fetch bundle components from Club Shop DB / API (server-side)
    // ──────────────────────────────────────────────────────────────
    private function fetchBundleComponents(?string $slug): array
    {
        if (!$slug) return [];

        // 1. Try local or configured API first (uses full BundleModel with exact markup, optional item handling & discounts)
        try {
            $localUrl = url('/club-shop/api/products/slug/' . $slug);
            $response = Http::timeout(6)->get($localUrl);

            if ($response->successful() && !empty($response->json('data.bundle_components'))) {
                $data = $response->json('data');
                return [
                    'bundle' => [
                        'id'               => $data['id'] ?? null,
                        'title'            => $data['title'] ?? '',
                        'price'            => (float)($data['price'] ?? 0),
                        'discount_amount'  => (float)($data['discount_amount'] ?? 0),
                        'discount_rate'    => (int)($data['discount_rate'] ?? 0),
                        'price_discounted' => (float)($data['price_discounted'] ?? ($data['price'] ?? 0)),
                    ],
                    'components' => $data['bundle_components'],
                ];
            }
        } catch (\Throwable $e) {
            Log::warning('[CourseEnquiry] Bundle API fetch failed: ' . $e->getMessage());
        }

        // 2. Direct query against clubshop DB
        try {
            $bundle = DB::connection('clubshop')->table('products')->where('slug', $slug)->where('is_deleted', 0)->first();
            if (!$bundle && is_numeric($slug)) {
                $bundle = DB::connection('clubshop')->table('products')->where('id', (int)$slug)->where('is_deleted', 0)->first();
            }
            if ($bundle) {
                // Check store markup setting
                $markupRate = 0.0;
                $paySetting = DB::connection('clubshop')->table('payment_settings')->where('id', 1)->first();
                if ($paySetting && (int)($paySetting->seller_markup_status ?? 0) === 1) {
                    $markupRate = (float)($paySetting->seller_markup_rate ?? 0);
                }

                $rows = DB::connection('clubshop')->table('product_bundles as pb')
                    ->join('products as p', 'p.id', '=', 'pb.component_product_id')
                    ->leftJoin('product_details as pd', function($j) {
                        $j->on('pd.product_id', '=', 'p.id')->where('pd.lang_id', '=', 1);
                    })
                    ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
                    ->leftJoin('category_lang as cl', function($j) {
                        $j->on('cl.category_id', '=', 'c.id')->where('cl.lang_id', '=', 1);
                    })
                    ->where('pb.bundle_product_id', $bundle->id)
                    ->where('p.is_deleted', 0)
                    ->where(function($q) {
                        $q->where('pb.quantity', '>', 0)->orWhere('pb.is_optional', 0);
                    })
                    ->orderBy('pb.sort_order')
                    ->select(
                        'p.id',
                        'p.price as raw_price',
                        'p.is_markup_set',
                        'p.markup_rate as product_markup_rate',
                        'pd.title',
                        'cl.name as category_name',
                        'pb.quantity as required_quantity',
                        'pb.price_override'
                    )
                    ->get();

                if ($rows->isNotEmpty()) {
                    $components = [];
                    foreach ($rows as $r) {
                        $qty = (int)($r->required_quantity ?? 1);
                        if ($qty <= 0) continue;
                        $baseVal = !empty($r->price_override) && (float)$r->price_override > 0
                            ? (float)$r->price_override
                            : (float)$r->raw_price;

                        $compMarkup = $markupRate;
                        if (!empty($r->is_markup_set) && (int)$r->is_markup_set === 1) {
                            $compMarkup = (float)$r->product_markup_rate;
                        }

                        $unitPrice = $compMarkup > 0 ? round($baseVal * (1 + $compMarkup / 100), 2) : $baseVal;

                        $components[] = [
                            'id'                => $r->id,
                            'title'             => $r->title,
                            'category_name'     => $r->category_name ?? 'General',
                            'required_quantity' => $qty,
                            'unit_price'        => $unitPrice,
                        ];
                    }

                    return [
                        'bundle' => [
                            'id'               => $bundle->id,
                            'title'            => $bundle->slug,
                            'price'            => (float)$bundle->price,
                            'discount_amount'  => (float)($bundle->discount_amount ?? 0),
                            'discount_rate'    => (int)($bundle->discount_rate ?? 0),
                            'price_discounted' => (float)($bundle->price_discounted ?? $bundle->price),
                        ],
                        'components' => $components,
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('[CourseEnquiry] clubshop DB fetch failed: ' . $e->getMessage());
        }

        return [];
    }

    // ──────────────────────────────────────────────────────────────
    // Build structured quotation data from bundle components
    // ──────────────────────────────────────────────────────────────
    private function buildQuotationData(array $components, ?array $bundleInfo = null): array
    {
        $grouped       = [];
        $computedTotal = 0.0;
        $items         = [];

        foreach ($components as $c) {
            $title = $c['title'] ?? ($c['item'] ?? ($c['name'] ?? null));
            if (empty($title)) continue;
            $qty   = (int) ($c['required_quantity'] ?? ($c['qty'] ?? 1));
            $price = (float) ($c['unit_price'] ?? ($c['price'] ?? 0));
            if ($qty <= 0) continue;

            $cat   = $c['category_name'] ?? ($c['category'] ?? ($c['cat'] ?? 'General'));
            $total = (float) ($c['total_price'] ?? ($c['total'] ?? ($price * $qty)));
            $computedTotal += $total;

            $items[]       = compact('cat', 'qty', 'price', 'total') + ['item' => $title];
            $grouped[$cat] = ($grouped[$cat] ?? 0) + $total;
        }

        $subtotal = $computedTotal;
        $discount = 0.0;
        $grandTotal = $computedTotal;

        if (!empty($bundleInfo)) {
            if (isset($bundleInfo['price']) && (float)$bundleInfo['price'] > 0) {
                $subtotal = (float)$bundleInfo['price'];
            }
            if (isset($bundleInfo['discount_amount']) && (float)$bundleInfo['discount_amount'] > 0) {
                $discount = (float)$bundleInfo['discount_amount'];
            } elseif (isset($bundleInfo['price_discounted']) && (float)$bundleInfo['price_discounted'] > 0 && (float)$bundleInfo['price_discounted'] < $subtotal) {
                $discount = round($subtotal - (float)$bundleInfo['price_discounted'], 2);
            }
            $grandTotal = max(0, round($subtotal - $discount, 2));
        }

        return [
            'items'       => $items,
            'grouped'     => $grouped,
            'subtotal'    => $subtotal,
            'discount'    => $discount,
            'tax_percent' => 0,
            'tax_amount'  => 0,
            'shipping'    => 0,
            'grand_total' => $grandTotal,
        ];
    }

    // ──────────────────────────────────────────────────────────────
    // Render HTML table from quotation data
    // ──────────────────────────────────────────────────────────────
    private function renderQuotationTable(array $data): string
    {
        $items    = $data['items']       ?? [];
        $subtotal = (float) ($data['subtotal'] ?? 0);
        $discount = (float) ($data['discount'] ?? 0);
        $taxAmt   = (float) ($data['tax_amount'] ?? 0);
        $shipping = (float) ($data['shipping'] ?? 0);
        $grand    = (float) ($data['grand_total'] ?? 0);

        if (empty($items)) {
            return '<p style="color:#94a3b8;font-size:13px;">No bundle items found.</p>';
        }

        if ($subtotal <= 0) {
            $subtotal = array_sum(array_column($items, 'total'));
        }
        if ($grand <= 0) {
            $grand = max(0, $subtotal - $discount + $taxAmt + $shipping);
        }

        // Group rows by package if multiple packages exist, else by category
        $distinctPkgs = [];
        foreach ($items as $row) {
            $p = trim((string)($row['package'] ?? ''));
            if ($p !== '') {
                $distinctPkgs[$p] = true;
            }
        }
        $hasPackages = count($distinctPkgs) > 1;

        $rows = '';
        $sno  = 0;

        if ($hasPackages) {
            $pkgGrouped = [];
            foreach ($items as $row) {
                $pkgKey = trim((string)($row['package'] ?? '')) ?: 'Package';
                $pkgGrouped[$pkgKey][$row['cat']][] = $row;
            }

            foreach ($pkgGrouped as $pkgName => $categories) {
                $pkgTotal = 0;
                $rows .= '<tr style="background:#0f172a;color:#ffffff;">'
                    . '<td colspan="6" style="padding:9px 10px;font-weight:bold;font-size:13px;color:#ffffff;border:1px solid #0f172a;">'
                    . 'Package: ' . htmlspecialchars($pkgName)
                    . '</td></tr>';

                foreach ($categories as $cat => $catItems) {
                    $catTotal = array_sum(array_column($catItems, 'total'));
                    $pkgTotal += $catTotal;
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
                $rows .= '<tr style="background:#e2e8f0;">'
                    . '<td colspan="5" style="padding:8px 10px;border:1px solid #cbd5e1;text-align:right;font-weight:bold;color:#334155;">'
                    . htmlspecialchars($pkgName) . ' Total</td>'
                    . '<td style="padding:8px 10px;border:1px solid #cbd5e1;text-align:right;font-weight:800;color:#0f172a;">&#8377;' . number_format($pkgTotal, 2) . '</td>'
                    . '</tr>';
            }
        } else {
            $grouped = [];
            foreach ($items as $row) {
                $grouped[$row['cat']][] = $row;
            }

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
        }

        $tfoot = '';
        $rawTotal = (float)($data['raw_total'] ?? array_sum(array_column($items, 'total')));
        if ($rawTotal > $subtotal || $discount > 0) {
            $tfoot .= '<tr style="background:#f8fafc;">'
                . '<td colspan="5" style="padding:8px 10px;text-align:right;font-weight:700;color:#64748b;border:1px solid #e2e8f0;">Component List Total (MRP)</td>'
                . '<td style="padding:8px 10px;text-align:right;font-weight:700;color:#64748b;border:1px solid #e2e8f0;">&#8377;' . number_format($rawTotal, 2) . '</td>'
                . '</tr>';
        }
        if ($discount > 0) {
            $tfoot .= '<tr style="background:#f0fdf4;">'
                . '<td colspan="5" style="padding:8px 10px;text-align:right;font-weight:700;color:#15803d;border:1px solid #e2e8f0;">Package Discount / Savings</td>'
                . '<td style="padding:8px 10px;text-align:right;font-weight:800;color:#15803d;border:1px solid #e2e8f0;">-&#8377;' . number_format($discount, 2) . '</td>'
                . '</tr>';
        }
        $tfoot .= '<tr style="background:#f8fafc;">'
            . '<td colspan="5" style="padding:8px 10px;text-align:right;font-weight:700;color:#1e293b;border:1px solid #e2e8f0;">Offered Subtotal</td>'
            . '<td style="padding:8px 10px;text-align:right;font-weight:700;color:#1e293b;border:1px solid #e2e8f0;">&#8377;' . number_format($subtotal, 2) . '</td>'
            . '</tr>';
        if ($taxAmt > 0) {
            $taxLabel = !empty($data['tax_percent']) ? 'GST (' . $data['tax_percent'] . '%)' : 'GST';
            $tfoot .= '<tr style="background:#f8fafc;">'
                . '<td colspan="5" style="padding:8px 10px;text-align:right;font-weight:700;color:#475569;border:1px solid #e2e8f0;">' . $taxLabel . '</td>'
                . '<td style="padding:8px 10px;text-align:right;font-weight:700;color:#1e293b;border:1px solid #e2e8f0;">&#8377;' . number_format($taxAmt, 2) . '</td>'
                . '</tr>';
        }
        if ($shipping > 0) {
            $tfoot .= '<tr style="background:#f8fafc;">'
                . '<td colspan="5" style="padding:8px 10px;text-align:right;font-weight:700;color:#475569;border:1px solid #e2e8f0;">Shipping</td>'
                . '<td style="padding:8px 10px;text-align:right;font-weight:700;color:#1e293b;border:1px solid #e2e8f0;">&#8377;' . number_format($shipping, 2) . '</td>'
                . '</tr>';
        }

        $tfoot .= '<tr style="background:#1e1b4b;">'
            . '<td colspan="5" style="padding:10px;text-align:right;font-weight:800;color:#fff;font-size:14px;">Grand Total</td>'
            . '<td style="padding:10px;text-align:right;font-weight:800;color:#fbbf24;font-size:15px;">&#8377;' . number_format($grand, 2) . '</td>'
            . '</tr>';

        $packages = $data['packages'] ?? [];
        if (empty($packages) && !empty($items)) {
            $distinctPackages = [];
            foreach ($items as $it) {
                $pkgName = trim((string)($it['package'] ?? ''));
                if ($pkgName !== '') {
                    if (!isset($distinctPackages[$pkgName])) {
                        $distinctPackages[$pkgName] = [
                            'title'          => $pkgName,
                            'quantity'       => 1,
                            'unit_price'     => 0,
                            'original_price' => 0,
                            'total_price'    => 0,
                        ];
                    }
                    $distinctPackages[$pkgName]['total_price'] += (float)($it['total'] ?? 0);
                    $distinctPackages[$pkgName]['unit_price'] = $distinctPackages[$pkgName]['total_price'];
                    $distinctPackages[$pkgName]['original_price'] = $distinctPackages[$pkgName]['total_price'];
                }
            }
            if (count($distinctPackages) > 1) {
                $packages = array_values($distinctPackages);
            }
        }

        $packagesHtml = '';
        if (!empty($packages) && is_array($packages)) {
            $pRows = '';
            $pSno = 0;
            foreach ($packages as $pkg) {
                $pSno++;
                $pTitle = htmlspecialchars($pkg['title'] ?? 'Package');
                $pQty = (int)($pkg['quantity'] ?? 1);
                $pOrigPrice = (float)($pkg['original_price'] ?? ($pkg['unit_price'] ?? 0));
                $pUnitPrice = (float)($pkg['unit_price'] ?? 0);
                $pTotal = (float)($pkg['total_price'] ?? ($pUnitPrice * $pQty));

                $bg = ($pSno % 2 === 0) ? 'background:#f8fafc;' : '';
                $pRows .= '<tr style="' . $bg . '">'
                    . '<td style="padding:7px 10px;border:1px solid #e2e8f0;text-align:center;">' . $pSno . '</td>'
                    . '<td style="padding:7px 10px;border:1px solid #e2e8f0;font-weight:700;color:#0f172a;">' . $pTitle . '</td>'
                    . '<td style="padding:7px 10px;border:1px solid #e2e8f0;text-align:center;font-weight:700;">' . $pQty . '</td>'
                    . '<td style="padding:7px 10px;border:1px solid #e2e8f0;text-align:right;color:#64748b;">&#8377;' . number_format($pOrigPrice, 2) . '</td>'
                    . '<td style="padding:7px 10px;border:1px solid #e2e8f0;text-align:right;font-weight:700;color:#15803d;">&#8377;' . number_format($pUnitPrice, 2) . '</td>'
                    . '<td style="padding:7px 10px;border:1px solid #e2e8f0;text-align:right;font-weight:700;color:#0f172a;">&#8377;' . number_format($pTotal, 2) . '</td>'
                    . '</tr>';
            }

            $packagesHtml = '<div style="margin-bottom:20px;">'
                . '<div style="font-weight:800;font-size:14px;color:#1e293b;margin-bottom:8px;">Ordered Products / Packages</div>'
                . '<table style="width:100%;border-collapse:collapse;font-size:13px;margin-bottom:12px;">'
                . '<thead><tr style="background:#1e1b4b;color:#ffffff;">'
                . '<th style="padding:8px 10px;color:#fff;text-align:center;width:35px;">#</th>'
                . '<th style="padding:8px 10px;color:#fff;text-align:left;">Product / Package Description</th>'
                . '<th style="padding:8px 10px;color:#fff;text-align:center;width:55px;">Qty</th>'
                . '<th style="padding:8px 10px;color:#fff;text-align:right;width:110px;">Unit Price (MRP)</th>'
                . '<th style="padding:8px 10px;color:#fff;text-align:right;width:110px;">Offered Price</th>'
                . '<th style="padding:8px 10px;color:#fff;text-align:right;width:120px;">Total</th>'
                . '</tr></thead>'
                . '<tbody>' . $pRows . '</tbody>'
                . '<tfoot>' . $tfoot . '</tfoot>'
                . '</table></div>';
        }

        if ($packagesHtml !== '') {
            return $packagesHtml
                . '<div style="margin-top:20px;margin-bottom:8px;font-weight:800;font-size:14px;color:#1e293b;">Detailed Bill of Materials (Component Breakdown)</div>'
                . '<table style="width:100%;border-collapse:collapse;font-size:13px;margin-top:4px;">'
                . '<thead><tr style="background:#334155;color:#ffffff;">'
                . '<th style="padding:8px 10px;color:#fff;text-align:center;width:35px;">#</th>'
                . '<th style="padding:8px 10px;color:#fff;text-align:left;">Category</th>'
                . '<th style="padding:8px 10px;color:#fff;text-align:left;">Item / Description</th>'
                . '<th style="padding:8px 10px;color:#fff;text-align:center;width:50px;">Qty</th>'
                . '<th style="padding:8px 10px;color:#fff;text-align:right;width:100px;">Unit Price</th>'
                . '<th style="padding:8px 10px;color:#fff;text-align:right;width:110px;">Total</th>'
                . '</tr></thead>'
                . '<tbody>' . $rows . '</tbody>'
                . '</table>';
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
            . '<tfoot>' . $tfoot . '</tfoot>'
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
            $bundleFetch   = $this->fetchBundleComponents($targetSlug);
            $components    = $bundleFetch['components'] ?? (is_array($bundleFetch) && isset($bundleFetch[0]) ? $bundleFetch : []);
            $bundleInfo    = $bundleFetch['bundle'] ?? null;
            $quotationData = $this->buildQuotationData($components, $bundleInfo);
        } elseif (!empty($targetSlug) && (empty($quotationData['discount']) || (float)$quotationData['discount'] <= 0)) {
            try {
                $bundleRow = DB::connection('clubshop')->table('products')->where('slug', $targetSlug)->where('is_deleted', 0)->first();
                if ($bundleRow && (float)($bundleRow->discount_amount ?? 0) > 0) {
                    $quotationData['discount'] = (float)$bundleRow->discount_amount;
                    $quotationData['grand_total'] = max(0, round($quotationData['subtotal'] - $quotationData['discount'], 2));
                }
            } catch (\Throwable $e) {}
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
                $effectiveCartUrl = $cartLink ?: ($productLink ?: url('/club-shop'));
                $waPayload = [
                    'name'               => $data['name'] ?? '',
                    'school'             => $data['school'] ?? 'School',
                    'package'            => $packageName,
                    'total'              => $quotationData['grand_total'] ?? 0,
                    'pdf_url'            => $pdfPublicUrl,
                    'product_url'        => $productLink ?: url('/labs'),
                    'cart_url'           => $effectiveCartUrl,
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
        $packageName = preg_replace('/\s*[-–—]\s*Enquiry\s*$/iu', '', trim($packageName));
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
        $distinctPkgs = [];
        foreach (($quotationData['items'] ?? []) as $row) {
            $p = trim((string)($row['package'] ?? ''));
            if ($p !== '') {
                $distinctPkgs[$p] = true;
            }
        }
        $hasPackages = count($distinctPkgs) > 1;

        $qRows = '';
        $sno   = 0;

        if ($hasPackages) {
            $pkgGrouped = [];
            foreach (($quotationData['items'] ?? []) as $row) {
                $pkgKey = trim((string)($row['package'] ?? '')) ?: 'Package';
                $pkgGrouped[$pkgKey][$row['cat']][] = $row;
            }

            foreach ($pkgGrouped as $pkgName => $categories) {
                $pkgTotal = 0;
                $qRows .= '<tr style="background:#0f172a;color:#ffffff;">'
                    . '<td colspan="6" style="padding:6px 8px;font-weight:bold;font-size:9.5pt;color:#ffffff;border:1px solid #0f172a;">'
                    . 'Package: ' . htmlspecialchars($pkgName)
                    . '</td></tr>';

                foreach ($categories as $cat => $catItems) {
                    $catTotal = array_sum(array_column($catItems, 'total'));
                    $pkgTotal += $catTotal;
                    foreach ($catItems as $row) {
                        $sno++;
                        $bg = ($sno % 2 === 0) ? 'background:#f8fafc;' : 'background:#ffffff;';
                        $qRows .= '<tr style="' . $bg . '">'
                            . '<td style="padding:4px 6px;border:1px solid #e2e8f0;text-align:center;">' . $sno . '</td>'
                            . '<td style="padding:4px 6px;border:1px solid #e2e8f0;">' . htmlspecialchars($cat) . '</td>'
                            . '<td style="padding:4px 6px;border:1px solid #e2e8f0;font-weight:bold;">' . htmlspecialchars($row['item']) . '</td>'
                            . '<td style="padding:4px 6px;border:1px solid #e2e8f0;text-align:center;">' . $row['qty'] . '</td>'
                            . '<td style="padding:4px 6px;border:1px solid #e2e8f0;text-align:right;">Rs. ' . number_format($row['price'], 2) . '</td>'
                            . '<td style="padding:4px 6px;border:1px solid #e2e8f0;text-align:right;font-weight:bold;">Rs. ' . number_format($row['total'], 2) . '</td>'
                            . '</tr>';
                    }
                    $qRows .= '<tr style="background:#f1f5f9;">'
                        . '<td colspan="5" style="padding:4px 6px;border:1px solid #e2e8f0;text-align:right;font-weight:bold;color:#475569;">'
                        . htmlspecialchars($cat) . ' Subtotal</td>'
                        . '<td style="padding:4px 6px;border:1px solid #e2e8f0;text-align:right;font-weight:bold;color:#1d6fa4;">Rs. ' . number_format($catTotal, 2) . '</td>'
                        . '</tr>';
                }
                $qRows .= '<tr style="background:#e2e8f0;">'
                    . '<td colspan="5" style="padding:5px 6px;border:1px solid #cbd5e1;text-align:right;font-weight:bold;color:#334155;">'
                    . htmlspecialchars($pkgName) . ' Total</td>'
                    . '<td style="padding:5px 6px;border:1px solid #cbd5e1;text-align:right;font-weight:bold;color:#0f172a;">Rs. ' . number_format($pkgTotal, 2) . '</td>'
                    . '</tr>';
            }
        } else {
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
                        . '<td style="padding:4px 6px;border:1px solid #e2e8f0;text-align:center;">' . $sno . '</td>'
                        . '<td style="padding:4px 6px;border:1px solid #e2e8f0;">' . htmlspecialchars($cat) . '</td>'
                        . '<td style="padding:4px 6px;border:1px solid #e2e8f0;font-weight:bold;">' . htmlspecialchars($row['item']) . '</td>'
                        . '<td style="padding:4px 6px;border:1px solid #e2e8f0;text-align:center;">' . $row['qty'] . '</td>'
                        . '<td style="padding:4px 6px;border:1px solid #e2e8f0;text-align:right;">Rs. ' . number_format($row['price'], 2) . '</td>'
                        . '<td style="padding:4px 6px;border:1px solid #e2e8f0;text-align:right;font-weight:bold;">Rs. ' . number_format($row['total'], 2) . '</td>'
                        . '</tr>';
                }
                $qRows .= '<tr style="background:#f1f5f9;">'
                    . '<td colspan="5" style="padding:4px 6px;border:1px solid #e2e8f0;text-align:right;font-weight:bold;color:#475569;">'
                    . htmlspecialchars($cat) . ' Subtotal</td>'
                    . '<td style="padding:4px 6px;border:1px solid #e2e8f0;text-align:right;font-weight:bold;color:#1d6fa4;">Rs. ' . number_format($catTotal, 2) . '</td>'
                    . '</tr>';
            }
        }

        $rawTotal = (float)($quotationData['raw_total'] ?? array_sum(array_column($quotationData['items'] ?? [], 'total')));
        $subtotal = (float) ($quotationData['subtotal'] ?? 0);
        $discount = (float) ($quotationData['discount'] ?? 0);
        $taxAmt   = (float) ($quotationData['tax_amount'] ?? 0);
        $grand    = (float) ($quotationData['grand_total'] ?? 0);

        if ($subtotal <= 0 && !empty($quotationData['items'])) {
            $subtotal = array_sum(array_column($quotationData['items'], 'total'));
        }
        if ($grand <= 0) {
            $grand = max(0, $subtotal - $discount + $taxAmt);
        }

        $pdfFoot = '';
        if ($rawTotal > $subtotal || $discount > 0) {
            $pdfFoot .= '<tr style="background:#f8fafc;">'
                . '<td colspan="5" style="padding:4px 6px;border:1px solid #e2e8f0;text-align:right;font-weight:bold;color:#64748b;">Component List Total (MRP)</td>'
                . '<td style="padding:4px 6px;border:1px solid #e2e8f0;text-align:right;font-weight:bold;color:#64748b;">Rs. ' . number_format($rawTotal, 2) . '</td>'
                . '</tr>';
        }
        if ($discount > 0) {
            $pdfFoot .= '<tr style="background:#f0fdf4;">'
                . '<td colspan="5" style="padding:4px 6px;border:1px solid #e2e8f0;text-align:right;font-weight:bold;color:#15803d;">Package Discount / Savings</td>'
                . '<td style="padding:4px 6px;border:1px solid #e2e8f0;text-align:right;font-weight:bold;color:#15803d;">-Rs. ' . number_format($discount, 2) . '</td>'
                . '</tr>';
        }
        $pdfFoot .= '<tr style="background:#f8fafc;">'
            . '<td colspan="5" style="padding:4px 6px;border:1px solid #e2e8f0;text-align:right;font-weight:bold;color:#1e293b;">Offered Subtotal</td>'
            . '<td style="padding:4px 6px;border:1px solid #e2e8f0;text-align:right;font-weight:bold;color:#1e293b;">Rs. ' . number_format($subtotal, 2) . '</td>'
            . '</tr>';
        if ($taxAmt > 0) {
            $taxLabel = !empty($quotationData['tax_percent']) ? 'GST (' . $quotationData['tax_percent'] . '%)' : 'GST';
            $pdfFoot .= '<tr style="background:#f8fafc;">'
                . '<td colspan="5" style="padding:4px 6px;border:1px solid #e2e8f0;text-align:right;font-weight:bold;color:#475569;">' . $taxLabel . '</td>'
                . '<td style="padding:4px 6px;border:1px solid #e2e8f0;text-align:right;font-weight:bold;color:#1e293b;">Rs. ' . number_format($taxAmt, 2) . '</td>'
                . '</tr>';
        }

        $pdfFoot .= '<tr style="background:#1e1b4b;">'
            . '<td colspan="5" style="padding:6px 8px;text-align:right;font-weight:bold;color:#fff;font-size:9.5pt;">Grand Total (Inclusive of all taxes)</td>'
            . '<td style="padding:6px 8px;text-align:right;font-weight:bold;font-size:10.5pt;color:#fbbf24;">Rs. ' . number_format($grand, 2) . '</td>'
            . '</tr>';

        $packages = $quotationData['packages'] ?? [];
        if (empty($packages) && !empty($quotationData['items'])) {
            $distinctPackages = [];
            foreach ($quotationData['items'] as $it) {
                $pkgName = trim((string)($it['package'] ?? ''));
                if ($pkgName !== '') {
                    if (!isset($distinctPackages[$pkgName])) {
                        $distinctPackages[$pkgName] = [
                            'title'          => $pkgName,
                            'quantity'       => 1,
                            'unit_price'     => 0,
                            'original_price' => 0,
                            'total_price'    => 0,
                        ];
                    }
                    $distinctPackages[$pkgName]['total_price'] += (float)($it['total'] ?? 0);
                    $distinctPackages[$pkgName]['unit_price'] = $distinctPackages[$pkgName]['total_price'];
                    $distinctPackages[$pkgName]['original_price'] = $distinctPackages[$pkgName]['total_price'];
                }
            }
            if (count($distinctPackages) > 1) {
                $packages = array_values($distinctPackages);
            }
        }

        $packagesHtml = '';
        if (!empty($packages) && is_array($packages)) {
            $pRows = '';
            $pSno = 0;
            foreach ($packages as $pkgItem) {
                $pSno++;
                $pTitle = htmlspecialchars($pkgItem['title'] ?? 'Package');
                $pQty = (int)($pkgItem['quantity'] ?? 1);
                $pOrigPrice = (float)($pkgItem['original_price'] ?? ($pkgItem['unit_price'] ?? 0));
                $pUnitPrice = (float)($pkgItem['unit_price'] ?? 0);
                $pTotal = (float)($pkgItem['total_price'] ?? ($pUnitPrice * $pQty));

                $bg = ($pSno % 2 === 0) ? 'background:#f8fafc;' : 'background:#ffffff;';
                $pRows .= '<tr style="' . $bg . '">'
                    . '<td style="padding:5px 6px;border:1px solid #e2e8f0;text-align:center;">' . $pSno . '</td>'
                    . '<td style="padding:5px 6px;border:1px solid #e2e8f0;font-weight:bold;color:#0f172a;">' . $pTitle . '</td>'
                    . '<td style="padding:5px 6px;border:1px solid #e2e8f0;text-align:center;font-weight:bold;">' . $pQty . '</td>'
                    . '<td style="padding:5px 6px;border:1px solid #e2e8f0;text-align:right;color:#64748b;">Rs. ' . number_format($pOrigPrice, 2) . '</td>'
                    . '<td style="padding:5px 6px;border:1px solid #e2e8f0;text-align:right;font-weight:bold;color:#15803d;">Rs. ' . number_format($pUnitPrice, 2) . '</td>'
                    . '<td style="padding:5px 6px;border:1px solid #e2e8f0;text-align:right;font-weight:bold;color:#0f172a;">Rs. ' . number_format($pTotal, 2) . '</td>'
                    . '</tr>';
            }

            $packagesHtml = '<table class="dt" style="width:100%;border-collapse:collapse;font-size:8.5pt;margin-bottom:10px;">'
                . '<thead><tr style="background:#1e1b4b;color:#ffffff;">'
                . '<th style="padding:5px 6px;text-align:center;width:25px;color:#fff;">#</th>'
                . '<th style="padding:5px 6px;text-align:left;color:#fff;">Product / Package Description</th>'
                . '<th style="padding:5px 6px;text-align:center;width:40px;color:#fff;">Qty</th>'
                . '<th style="padding:5px 6px;text-align:right;width:85px;color:#fff;">Unit Price (MRP)</th>'
                . '<th style="padding:5px 6px;text-align:right;width:85px;color:#fff;">Offered Price</th>'
                . '<th style="padding:5px 6px;text-align:right;width:95px;color:#fff;">Total Amount</th>'
                . '</tr></thead><tbody>' . $pRows . '</tbody>'
                . '<tfoot>' . $pdfFoot . '</tfoot></table>';
        }

        $quotTable = $qRows
            ? '<table style="width:100%;border-collapse:collapse;font-size:8.5pt;">'
              . '<thead><tr style="background:#1e1b4b;">'
              . '<th style="padding:5px 6px;color:#fff;text-align:center;width:25px;">#</th>'
              . '<th style="padding:5px 6px;color:#fff;text-align:left;">Category</th>'
              . '<th style="padding:5px 6px;color:#fff;text-align:left;">Item / Description</th>'
              . '<th style="padding:5px 6px;color:#fff;text-align:center;width:35px;">Qty</th>'
              . '<th style="padding:5px 6px;color:#fff;text-align:right;width:80px;">Unit Price</th>'
              . '<th style="padding:5px 6px;color:#fff;text-align:right;width:90px;">Total</th>'
              . '</tr></thead><tbody>' . $qRows . '</tbody>'
              . '<tfoot>' . ($packagesHtml ? '' : $pdfFoot) . '</tfoot></table>'
            : '<p style="color:#94a3b8;">No bundle items available.</p>';

        $productLinkHtml = $productLink
            ? '<p style="margin:4px 0;font-size:9pt;"><strong>View Product &amp; Curriculum:</strong> <span style="color:#1d6fa4;">' . $productLink . '</span></p>'
              . '<p style="margin:4px 0;font-size:9pt;"><strong>Direct Procurement Link:</strong> <span style="color:#1d6fa4;">' . $cartLink . '</span></p>'
            : '';

        // Dynamic content based on lab type & package
        if ($isSkillvationLab) {
            $headerTitle = 'SKILLVATION LAB PROPOSAL';
            if (str_contains($pkgLower, '+') || str_contains($pkgLower, ' & ')) {
                $headerSub1 = 'Establishment of ' . $pkg;
                $pkgSubject = $pkg;
            } elseif (str_contains($pkgLower, 'premium')) {
                $headerSub1 = 'Establishment of Skillvation STEM & AI Lab (Premium Package)';
                $pkgSubject = 'Premium Package';
            } elseif (str_contains($pkgLower, 'advance')) {
                $headerSub1 = 'Establishment of Skillvation STEM & Robotics Lab (Advance Package)';
                $pkgSubject = 'Advance Package';
            } elseif (str_contains($pkgLower, 'custom')) {
                $headerSub1 = 'Establishment of Skillvation Future-Ready Lab (Custom Scope)';
                $pkgSubject = 'Custom Scope';
            } else {
                $headerSub1 = 'Establishment of Skillvation STEM & Tinkering Lab (Basic Package)';
                $pkgSubject = 'Basic Package';
            }
            $headerSub2  = 'Hands-On Experiential Learning, Coding, IoT &amp; Maker Space for Schools (Grades 1–10/12)';
            $subjectText = 'Proposal to establish a Future-Ready Skillvation Lab (' . $pkgSubject . ') for Grades 1–10/12';

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
            
            if (str_contains($pkgLower, '+') || str_contains($pkgLower, ' & ')) {
                $headerSub1 = 'Establishment of ' . $pkg;
                $headerSub2 = 'CBSE Composite Skill Lab aligned with CBSE Circular Skill-75/2024 &amp; NEP 2020';
                $pkgSubject = $pkg;
            } elseif (str_contains($pkgLower, 'premium')) {
                $headerSub1 = 'Establishment of CBSE Composite Skill Lab (Premium Package)';
                $headerSub2 = 'Center of Excellence &amp; Advanced AI/Robotics Hub aligned with CBSE Circular Skill-75/2024 &amp; NEP 2020';
                $pkgSubject = 'Premium Package';
            } elseif (str_contains($pkgLower, 'advance')) {
                $headerSub1 = 'Establishment of CBSE Composite Skill Lab (Advance Package)';
                $headerSub2 = 'Enhanced Multi-Domain Practical &amp; Digital Prototyping Hub aligned with CBSE Circular Skill-75/2024';
                $pkgSubject = 'Advance Package';
            } elseif (str_contains($pkgLower, 'custom')) {
                $headerSub1 = 'Establishment of CBSE Composite Skill Lab (Custom Tailored Scope)';
                $headerSub2 = 'Custom Multi-Domain Practical Skill Hub aligned with CBSE Circular Skill-75/2024 &amp; NEP 2020';
                $pkgSubject = 'Custom Scope';
            } else {
                $headerSub1 = 'Establishment of CBSE Standard Composite Skill Lab (Basic Package)';
                $headerSub2 = 'Turnkey 3 Forms of Work Setup aligned with CBSE Circular Skill-75/2024 &amp; NEP 2020';
                $pkgSubject = 'Basic Package';
            }

            $subjectText = 'Proposal to establish a CBSE Composite Skill Lab (' . $pkgSubject . ') for Grades 6–10/12';

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

        if ($packagesHtml !== '') {
            $commercialAndBomSections = '
  <h2>8. Commercial Quotation — ' . $pkg . '</h2>
  ' . $packagesHtml . '
  <div class="note">
    A detailed, itemized budget and implementation schedule will be finalized upon domain priority sign-off.
    Phased procurement can be scheduled across budget cycles to meet institutional milestones.
  </div>

  <h2>9. Detailed Bill of Materials (Component Breakdown)</h2>
  ' . $quotTable . '

  <h2>10. Conclusion</h2>
  <p>' . $conclusionText . '</p>';
        } else {
            $commercialAndBomSections = '
  <h2>8. Quotation &amp; Bill of Materials — ' . $pkg . '</h2>
  ' . $quotTable . '
  <div class="note">
    A detailed, itemized budget and implementation schedule will be finalized upon domain priority sign-off.
    Phased procurement can be scheduled across budget cycles to meet institutional milestones.
  </div>

  <h2>9. Conclusion</h2>
  <p>' . $conclusionText . '</p>';
        }

        $html = '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 10pt; color: #1a1a2e; margin:0; padding:0; }
  .page { padding: 22px 32px; }
  .hdr  { border-bottom: 3px solid #1e1b4b; padding-bottom: 10px; margin-bottom: 14px; }
  .hdr-title { font-size:16pt; font-weight:bold; color:#1e1b4b; margin:0 0 3px; }
  .hdr-sub   { font-size:9pt; color:#475569; margin:2px 0; }
  .hdr-badge { display:inline-block; background:#1e1b4b; color:#fff; font-size:8pt;
               font-weight:bold; padding:2px 10px; border-radius:3px; margin-top:6px; }
  .meta { width:100%; border-collapse:collapse; margin-bottom:14px; font-size:9pt; }
  .meta td { padding:3px 6px; vertical-align:top; }
  .ml { color:#64748b; width:85px; }
  .mv { font-weight:bold; color:#1e1b4b; }
  h2  { font-size:11pt; font-weight:bold; color:#1e1b4b; margin:14px 0 4px;
        border-left:4px solid #f97316; padding-left:8px; page-break-after: avoid; }
  p   { font-size:9pt; line-height:1.55; color:#334155; margin:0 0 6px; }
  ul  { font-size:9pt; line-height:1.65; color:#334155; padding-left:18px; margin:4px 0 8px; }
  li  { margin-bottom:2px; }
  .dt { width:100%; border-collapse:collapse; margin-bottom:10px; font-size:8.5pt; }
  .dt th { background:#1e1b4b; color:#fff; padding:5px 7px; text-align:left; }
  .dt td { padding:4px 6px; border:1px solid #e2e8f0; vertical-align:top; }
  .dt tr { page-break-inside: avoid; }
  .link-box { background:#f0f9ff; border:1px solid #bae6fd; border-radius:5px;
              padding:8px 12px; margin:8px 0 12px; font-size:8.5pt; }
  .note { background:#fffbeb; border:1px solid #fde68a; border-radius:4px;
          padding:6px 10px; font-size:8pt; color:#78350f; margin-top:6px; page-break-inside: avoid; }
  .footer { margin-top:16px; padding-top:8px; border-top:2px solid #e2e8f0;
            font-size:8pt; color:#64748b; page-break-inside: avoid; }
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

  ' . $commercialAndBomSections . '

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
    // private function sendToLeadDemo(array $data): void
    private function sendToLeadDemo(array $data, ?array $quotationData = null): void
    {
        $api = env('LEADDEMO_API_URL');

        if (empty($api)) return;

        $apiUrl  = rtrim($api, '/') . '/api/demos/create';            // demo / visit requests
        $apiUrl2 = rtrim($api, '/') . '/api/leads/create-with-demo';  // proposal requests

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

            // The selected request type decides the endpoint, the demo_type,
            // and the payload shape expected by LeadDemo:
            //   "Schedule a Demo with Academic Team" => walkthrough   -> /api/demos/create        (demo-centric)
            //   "Book a Skill Lab Visit"             => branch visit  -> /api/demos/create        (demo-centric)
            //   "Request Detailed Proposal"          => (no demo)     -> /api/leads/create-with-demo (lead-only)
            $requestType = $data['message'] ?? '';
            $isDemoRequest = in_array($requestType, [
                'Schedule a Demo with Academic Team',
                'Book a Skill Lab Visit',
            ], true);

            $assignedTo = (int) env('LEADDEMO_ASSIGNED_TO', 1);

            if ($isDemoRequest) {
                // ── Options 2 & 3: demo / visit → /api/demos/create ──────────
                $demoType = $requestType === 'Book a Skill Lab Visit'
                    ? 'branch visit'
                    : 'walkthrough';

                $targetUrl = $apiUrl;
                $payload = [
                    'first_name'    => $data['name'],
                    'phone_number'  => $data['phone'] ?? '',
                    'user'          => env('LEADDEMO_USER', ''),
                    'language'      => 'English',
                    'remarks'       => $remarks ?: ('Interested in ' . $packageName),
                    'demo_type'     => $demoType,
                    'demo_date'     => now()->addHour()->format('Y-m-d H:i'),
                    'notes'         => $notes ?: $packageName,
                    'incharge_name' => $data['school'] ?? 'website',
                    'campaign'      => $packageName,
                    'designation'   => $data['designation'] ?? '',
                    'source'        => $source,
                    'assigned_to'   => $assignedTo,
                    'pdf_json'      => $quotationData ?? [],
                ];
            } else {
                // ── Option 1: proposal → /api/leads/create-with-demo (lead-only)
                // Omitting `demo_schedule` makes the endpoint create a lead with
                // NO demo. Field names follow that endpoint's contract
                // (lead_name / phone / email), not the demos/create shape.
                $targetUrl = $apiUrl2;
                $payload = [
                    'lead_name'   => $data['name'],
                    'phone'       => $data['phone'] ?? '',
                    'email'       => $data['email'] ?? '',
                    'status'      => 'NEW',
                    'source'      => 'Website',          // allowed enum on this endpoint
                    'language'    => 'English',
                    'remarks'     => $remarks ?: ('Interested in ' . $packageName),
                    'notes'       => $notes ?: $packageName,
                    'assigned_to' => $assignedTo,
                    'pdf_json'    => $quotationData ?? [],
                    // NOTE: no 'demo_schedule' key → lead only, no demo created.
                ];
            }

            // Sends the request with the given bearer token.
            $doPost = function (string $token) use ($targetUrl, $payload) {
                return Http::timeout(8)
                    ->withoutVerifying()
                    ->withToken($token)   // Authorization: Bearer <token>
                    ->post($targetUrl, $payload);
            };

            $token    = (string) env('LEADDEMO_TOKEN', '');
            $response = $doPost($token);

            // A 401 means the token is missing/expired/revoked. Mint a fresh one
            // from the configured credentials and retry once.
            if ($response->status() === 401) {
                Log::warning('[LeadDemo] Token rejected (401) — attempting re-mint & retry', [
                    'endpoint' => $targetUrl,
                ]);

                $newToken = $this->mintLeadDemoToken($api);
                if ($newToken) {
                    $response = $doPost($newToken);
                }
            }

            if ($response->failed()) {
                // 4xx/5xx — the lead did NOT reach the CRM. Log loudly so it is noticed.
                Log::error('[LeadDemo] Push FAILED', [
                    'status'   => $response->status(),
                    'endpoint' => $targetUrl,
                    'phone'    => $data['phone'] ?? '',
                    'source'   => $source,
                    'body'     => $response->body(),
                ]);
            } else {
                Log::info('[LeadDemo] Pushed lead', [
                    'status'   => $response->status(),
                    'endpoint' => $targetUrl,
                    'phone'    => $data['phone'] ?? '',
                    'source'   => $source,
                    'response' => $response->json(),
                ]);
            }

        } catch (\Throwable $e) {
            Log::warning('[LeadDemo] Push failed: ' . $e->getMessage(), [
                'phone' => $data['phone'] ?? '',
            ]);
            // Never block the main enquiry flow
        }
    }

    // ──────────────────────────────────────────────────────────────
    // Mint a fresh LeadDemo bearer token via POST /api/auth/token
    // using the credentials in LEADDEMO_EMAIL / LEADDEMO_PASSWORD.
    // On success the token is persisted back to .env (LEADDEMO_TOKEN)
    // so subsequent requests reuse it. Returns the raw token or null.
    // ──────────────────────────────────────────────────────────────
    private function mintLeadDemoToken(string $api): ?string
    {
        $email    = (string) env('LEADDEMO_EMAIL', '');
        $password = (string) env('LEADDEMO_PASSWORD', '');

        if ($email === '' || $password === '') {
            Log::error('[LeadDemo] Cannot mint token — LEADDEMO_EMAIL / LEADDEMO_PASSWORD not set.');
            return null;
        }

        try {
            $resp = Http::timeout(8)
                ->withoutVerifying()
                ->post(rtrim($api, '/') . '/api/auth/token', [
                    'email'    => $email,
                    'password' => $password,
                    'label'    => 'SkillShop Website',
                ]);

            $newToken = $resp->json('token');
            if ($resp->successful() && is_string($newToken) && $newToken !== '') {
                $this->persistLeadDemoToken($newToken);
                Log::info('[LeadDemo] Minted a fresh token.', ['status' => $resp->status()]);
                return $newToken;
            }

            Log::error('[LeadDemo] Token mint failed', [
                'status' => $resp->status(),
                'body'   => $resp->body(),
            ]);
        } catch (\Throwable $e) {
            Log::error('[LeadDemo] Token mint threw: ' . $e->getMessage());
        }

        return null;
    }

    // Writes the new token into .env (replacing or appending LEADDEMO_TOKEN)
    // and updates the runtime env so the current request uses it too.
    private function persistLeadDemoToken(string $token): void
    {
        try {
            $path = base_path('.env');
            if (!is_writable($path)) return;

            $contents = file_get_contents($path);
            $line     = 'LEADDEMO_TOKEN=' . $token;

            if (preg_match('/^LEADDEMO_TOKEN=.*$/m', $contents)) {
                $contents = preg_replace('/^LEADDEMO_TOKEN=.*$/m', $line, $contents);
            } else {
                $contents .= PHP_EOL . $line . PHP_EOL;
            }

            file_put_contents($path, $contents);
            putenv($line);
            $_ENV['LEADDEMO_TOKEN'] = $token;
        } catch (\Throwable $e) {
            Log::warning('[LeadDemo] Could not persist token to .env: ' . $e->getMessage());
        }
    }
}
