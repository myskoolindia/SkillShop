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
        'composite-skill-lab-basic'   => [
            'slug'     => 'basic-skill-1',
            'label'    => 'Basic Composite Skill Lab',
            'page_url' => '/club-shop/basic-skill-1',
            'cart_url' => '/club-shop/basic-cart',
        ],
        'composite-skill-lab-advance' => [
            'slug'     => 'advance-skill-1',
            'label'    => 'Advance Composite Skill Lab',
            'page_url' => '/club-shop/advance-skill-1',
            'cart_url' => '/club-shop/advance-cart',
        ],
        'composite-skill-lab-premium' => [
            'slug'     => 'premium-skill-1',
            'label'    => 'Premium Composite Skill Lab',
            'page_url' => '/club-shop/premium-skill-1',
            'cart_url' => '/club-shop/premium-cart',
        ],
    ];

    public function store(Request $request)
    {
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

        try {
            self::setMailConfig();

            $adminEmail  = Setting::where('key', 'contact_message_receiver_mail')->value('value')
                        ?? config('mail.from.address');
            $source      = $validated['source'] ?? '';
            $sourceLabel = ucwords(str_replace(['-', '_'], ' ', $source));
            $planInfo    = self::PLAN_MAP[$source] ?? null;
            $packageName = $validated['course_title'] ?? ($planInfo['label'] ?? $sourceLabel);

            // ── Fetch bundle items server-side from Club Shop API ─
            $bundleComponents = $this->fetchBundleComponents($planInfo['slug'] ?? null);

            // ── Build quotation data from server-fetched components
            $quotationData = $this->buildQuotationData($bundleComponents);

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
            if ($adminTpl) {
                $adminSubject = str_replace(array_keys($vars), array_values($vars), $adminTpl->subject);
                $adminBody    = str_replace(array_keys($vars), array_values($vars), $adminTpl->message);
                Mail::to($adminEmail)->send(new DefaultMail(['subject' => $adminSubject], $adminBody));
            }

            // ── 2. Confirmation + PDF proposal to enquirer ────────
            $confirmTpl = EmailTemplate::where('name', 'skill_lab_enquiry_confirmation')->first();
            if ($confirmTpl) {
                $confirmSubject = str_replace(array_keys($vars), array_values($vars), $confirmTpl->subject);
                $confirmBody    = str_replace(array_keys($vars), array_values($vars), $confirmTpl->message);

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
            }

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
    <div class="hdr-sub">Establishment of a Composite Skill Lab — Skillvation</div>
    <div class="hdr-sub">A multi-domain, hands-on skill education space for Grades 6–12</div>
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
      <td colspan="3">Proposal to establish a Composite Skill Lab ("Skillvation") for Grades 6–12</td>
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

  <h2>3. Proposed Name &amp; Identity</h2>
  <p>The lab is proposed to be branded <strong>"Skillvation"</strong> (Skill + Innovation), giving it a distinct identity for student engagement, showcases, and communication with parents and the community.</p>

  <h2>4. Scope: Grade Range</h2>
  <p>The lab will serve <strong>Grades 6 to 12</strong> as a single composite space, in keeping with CBSE\'s guideline that schools may establish one lab covering this grade band.</p>

  <h2>5. Skill Domains Covered</h2>
  <table class="dt">
    <thead><tr><th>Domain</th><th>Focus Areas</th><th>Core Equipment</th></tr></thead>
    <tbody>
      <tr><td><strong>IT / ITeS &amp; AI</strong></td><td>Computer hardware, coding, AI fundamentals, data handling</td><td>PCs/laptops, AI learning kits, coding boards</td></tr>
      <tr><td><strong>Robotics, Electronics &amp; 3D Printing</strong></td><td>Circuit building, robotics kits, CAD design, 3D printing</td><td>Robotics kits, electronics trainer kits, 3D printer</td></tr>
      <tr><td><strong>Healthcare</strong></td><td>First aid, hygiene, basic diagnostics, wellness</td><td>First-aid kits, BP/pulse demo kits, anatomy models</td></tr>
      <tr><td><strong>Agriculture</strong></td><td>Soil testing, hydroponics, plant biology, farming</td><td>Hydroponics kit, soil-testing kit, germination trays</td></tr>
      <tr><td><strong>Apparel &amp; Design</strong></td><td>Pattern making, textiles, basic tailoring</td><td>Sewing kits, fabric samples, design templates</td></tr>
      <tr><td><strong>Food Production &amp; Nutrition</strong></td><td>Food safety, nutrition basics, preservation</td><td>Hygiene kits, food-testing kits, display models</td></tr>
    </tbody>
  </table>

  <h2>6. Infrastructure Requirements</h2>
  <ul>
    <li>Dedicated space: <strong>600 sq. ft.</strong> (single composite lab) or <strong>two separate labs of 400 sq. ft. each</strong>, as per CBSE guidelines.</li>
    <li>Reliable power supply with safety-compliant wiring, adequate lighting and ventilation.</li>
  </ul>

  <h2>7. Curriculum Integration</h2>
  <ul>
    <li>Aligns with NCF-SE\'s three "forms of work" and mandatory skill-subject requirement from Grade 6 onward.</li>
    <li>Weekly rotation across domains, with project-based assessment.</li>
    <li>Grade 6–8: foundational exposure across all domains; Grade 9–12: specialisation in one or two domains.</li>
  </ul>

  <h2>8. Staffing &amp; Training</h2>
  <ul>
    <li>Existing computer/science faculty trained as lab coordinators, supplemented by domain resource persons.</li>
    <li>Orientation and safety training for all staff prior to roll out.</li>
  </ul>

  <h2>9. Expected Outcomes</h2>
  <ul>
    <li>Full compliance with CBSE\'s Composite Skill Lab mandate.</li>
    <li>Measurable improvement in students\' practical, vocational and problem-solving skills.</li>
    <li>A distinctive, branded skill-education identity for the school.</li>
    <li>A scalable model that can extend to further grades in a future phase.</li>
  </ul>

  <h2>10. Implementation Timeline (Indicative)</h2>
  <table class="dt">
    <thead><tr><th>Phase</th><th>Activities</th></tr></thead>
    <tbody>
      <tr><td><strong>Weeks 1–2</strong></td><td>Space planning, procurement approvals, vendor selection</td></tr>
      <tr><td><strong>Weeks 3–5</strong></td><td>Civil work, electrical fittings, furniture installation</td></tr>
      <tr><td><strong>Weeks 6–7</strong></td><td>Equipment installation and testing across all domains</td></tr>
      <tr><td><strong>Week 8</strong></td><td>Teacher orientation and safety training</td></tr>
      <tr><td><strong>Week 9+</strong></td><td>Phased roll out of modules; parent/community showcase</td></tr>
    </tbody>
  </table>

  <h2>11. Product Details</h2>
  ' . $productLinkHtml . '

  <h2>12. Quotation — ' . $pkg . '</h2>
  ' . $quotTable . '
  <div class="note">
    A detailed, itemized budget will be prepared once domain priorities and vendor quotations are finalised.
    Phased procurement (core IT/electronics first, other domains in subsequent phases) can be considered
    to spread costs across budget cycles.
  </div>

  <h2>13. Conclusion</h2>
  <p>Skillvation offers the school a structured, CBSE-aligned way to give students hands-on exposure across a broad range of skill domains. We request approval to proceed to the detailed planning and procurement stage.</p>

  <div class="footer">
    <strong>MYSKOOL LEARNING PRIVATE LIMITED</strong><br>
    +91 98450 26782 &nbsp;|&nbsp; infomyskoolonline@gmail.com &nbsp;|&nbsp; www.skillvation.com &nbsp;|&nbsp; www.pedaskills.com<br>
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
}
