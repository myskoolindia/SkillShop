@php
    $setting = $setting ?? Cache::get('setting');
    $grandTotal = (float) ($quotation['grand_total'] ?? 0);
    $subtotal   = (float) ($quotation['subtotal'] ?? 0);
    $discount   = (float) ($quotation['discount'] ?? 0);
    $taxPercent = (float) ($quotation['tax_percent'] ?? 0);
    $taxAmount  = (float) ($quotation['tax_amount'] ?? 0);
    $shipping   = (float) ($quotation['shipping'] ?? 0);
    $items      = $quotation['items'] ?? [];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Quotation') }} - {{ $quotation['quotation_number'] ?? '#' . $enquiry->id }}</title>
    <link rel="icon" href="{{ asset($setting?->favicon ?? '') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            color: #1e293b;
            background: #f1f5f9;
            padding: 30px 15px;
            line-height: 1.5;
        }
        .container-box {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 40px;
            position: relative;
        }

        /* ── Action Toolbar (Hidden in print) ── */
        .toolbar {
            max-width: 900px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }
        .btn-back {
            background: #e2e8f0;
            color: #334155;
        }
        .btn-back:hover {
            background: #cbd5e1;
            color: #0f172a;
        }
        .btn-edit {
            background: #0284c7;
            color: #ffffff;
        }
        .btn-edit:hover {
            background: #0369a1;
        }
        .btn-proforma {
            background: #059669;
            color: #ffffff;
        }
        .btn-proforma:hover {
            background: #047857;
        }
        .btn-print {
            background: #2563eb;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
        .btn-print:hover {
            background: #1d4ed8;
        }

        /* ── Header ── */
        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 25px;
            border-bottom: 2px solid #e2e8f0;
        }
        .logo-wrap {
            max-width: 380px;
        }
        .company-logo {
            max-height: 55px;
            max-width: 230px;
            object-fit: contain;
            margin-bottom: 8px;
            display: block;
        }
        .brand-name {
            font-size: 26px;
            font-weight: 800;
            color: #1e3a8a;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .company-info {
            font-size: 13px;
            color: #64748b;
            line-height: 1.5;
        }
        .doc-title-wrap {
            text-align: right;
        }
        .doc-badge {
            display: inline-block;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 12px;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 6px;
            border: 1px solid #bfdbfe;
        }
        .doc-title {
            font-size: 32px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: 1px;
            margin: 0;
            line-height: 1.1;
        }
        .doc-number {
            font-size: 16px;
            font-weight: 700;
            color: #2563eb;
            margin-top: 6px;
        }
        .doc-meta {
            margin-top: 8px;
            font-size: 13px;
            color: #475569;
        }
        .doc-meta strong {
            color: #0f172a;
        }

        /* ── Client & Project Info ── */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            padding: 24px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .info-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px 20px;
        }
        .info-title {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #64748b;
            margin-bottom: 8px;
        }
        .client-name {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 2px;
        }
        .client-school {
            font-size: 15px;
            font-weight: 600;
            color: #2563eb;
            margin-bottom: 6px;
        }
        .client-contact {
            font-size: 13px;
            color: #475569;
            line-height: 1.5;
        }

        /* ── Package Highlight ── */
        .package-banner {
            background: linear-gradient(135deg, #eff6ff 0%, #e0e7ff 100%);
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 12px 20px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .package-title {
            font-size: 15px;
            font-weight: 700;
            color: #1e3a8a;
        }
        .package-sub {
            font-size: 12px;
            color: #475569;
        }

        /* ── Table ── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            font-size: 13.5px;
        }
        .items-table th {
            background: #0f172a;
            color: #ffffff;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11.5px;
            letter-spacing: 0.5px;
            padding: 10px 12px;
            text-align: left;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
            vertical-align: middle;
        }
        .items-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        .badge-cat {
            display: inline-block;
            background: #e0f2fe;
            color: #0369a1;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 4px;
        }

        /* ── Calculations & Bank Summary ── */
        .bottom-grid {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 25px;
            margin-bottom: 30px;
        }
        .bank-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px 20px;
            font-size: 13px;
        }
        .bank-card-title {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            color: #475569;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .bank-details td {
            padding: 3px 6px;
            font-size: 13px;
        }
        .calc-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }
        .calc-table td {
            padding: 6px 10px;
        }
        .calc-table .label-col {
            color: #64748b;
        }
        .calc-table .val-col {
            text-align: right;
            font-weight: 600;
            color: #1e293b;
        }
        .grand-total-row {
            background: #eff6ff;
            border-top: 2px solid #2563eb;
            border-bottom: 2px solid #2563eb;
        }
        .grand-total-row td {
            padding: 10px;
            font-size: 18px;
            font-weight: 900;
            color: #1e3a8a;
        }
        .words-box {
            background: #f8fafc;
            border-left: 3px solid #2563eb;
            padding: 8px 12px;
            margin-top: 10px;
            font-size: 12.5px;
            color: #334155;
            font-style: italic;
        }

        /* ── Terms & Signatures ── */
        .terms-block {
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
            margin-bottom: 30px;
        }
        .terms-title {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .terms-content {
            font-size: 12.5px;
            color: #475569;
            line-height: 1.6;
            white-space: pre-line;
        }
        .sig-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 40px;
            padding-top: 15px;
        }
        .sig-box {
            text-align: center;
            width: 220px;
        }
        .sig-line {
            height: 1px;
            background: #94a3b8;
            margin-bottom: 8px;
        }
        .sig-name {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
        }
        .sig-role {
            font-size: 11px;
            color: #64748b;
        }

        .footer-note {
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px dashed #cbd5e1;
        }

        /* ── Print Styling ── */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
                color: #000000;
            }
            .toolbar {
                display: none !important;
            }
            .container-box {
                box-shadow: none;
                border: none;
                padding: 20px 25px;
                max-width: 100%;
                border-radius: 0;
            }
            .items-table th {
                background: #1e293b !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .badge-cat, .package-banner, .grand-total-row, .words-box, .doc-badge {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    {{-- ── Action Toolbar ── --}}
    <div class="toolbar">
        <div>
            <a href="{{ route('admin.course-enquiry.show', $enquiry->id) }}" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> {{ __('Back to Enquiry') }}
            </a>
            <a href="{{ route('admin.course-enquiries') }}" class="btn btn-back">
                <i class="fas fa-list"></i> {{ __('Manage Enquiries') }}
            </a>
        </div>
        <div style="display: flex; gap: 8px;">
            @if(!$enquiry->isQuotationLocked())
                <a href="{{ route('admin.course-enquiry.quotation', $enquiry->id) }}" class="btn btn-edit">
                    <i class="fas fa-edit"></i> {{ __('Edit Quotation') }}
                </a>
            @else
                <span class="btn btn-back text-muted" title="{{ __('Quotation is locked because Proforma Invoice has been issued') }}" style="cursor: default;">
                    <i class="fas fa-lock"></i> {{ __('Quotation Locked') }}
                </span>
            @endif
            @if($enquiry->hasProformaInvoice())
                <a href="{{ route('admin.course-enquiry.proforma.view', $enquiry->id) }}" class="btn btn-proforma">
                    <i class="fas fa-file-invoice-dollar"></i> {{ __('View Proforma') }}
                </a>
            @else
                <a href="{{ route('admin.course-enquiry.proforma', $enquiry->id) }}" class="btn btn-proforma">
                    <i class="fas fa-file-invoice-dollar"></i> {{ __('Generate Proforma') }}
                </a>
            @endif
            @if($enquiry->hasInvoice())
                <a href="{{ route('admin.course-enquiry.invoice.view', $enquiry->id) }}" class="btn" style="background:#2563eb; color:#fff;">
                    <i class="fas fa-receipt"></i> {{ __('View Tax Invoice') }}
                </a>
            @endif
            <button onclick="window.print()" class="btn btn-print">
                <i class="fas fa-print"></i> {{ __('Print / Download PDF') }}
            </button>
        </div>
    </div>

    {{-- ── Main Printable Container ── --}}
    <div class="container-box">

        {{-- Header --}}
        <div class="header-row">
            <div class="logo-wrap">
                @if(!empty($setting?->logo))
                    <img src="{{ asset($setting->logo) }}" alt="{{ $setting?->app_name ?? 'Skillvation' }}" class="company-logo">
                @else
                    <div class="brand-name">{{ $setting?->app_name ?? 'Skillvation' }}</div>
                @endif
                <div class="company-info">
                    @if(!empty($setting?->site_address))
                        <div>{{ $setting->site_address }}</div>
                    @else
                        <div>Bengaluru, Karnataka, India</div>
                    @endif
                    @if(!empty($setting?->site_email))
                        <div><strong>Email:</strong> {{ $setting->site_email }}</div>
                    @endif
                    @if(!empty($setting?->site_phone))
                        <div><strong>Phone:</strong> {{ $setting->site_phone }}</div>
                    @endif
                    <div><strong>Web:</strong> {{ url('/') }}</div>
                </div>
            </div>

            <div class="doc-title-wrap">
                <div class="doc-badge">{{ __('Official Proposal') }}</div>
                <h1 class="doc-title">{{ __('QUOTATION') }}</h1>
                <div class="doc-number">{{ $quotation['quotation_number'] }}</div>
                <div class="doc-meta">
                    <div><strong>{{ __('Date:') }}</strong> {{ \Carbon\Carbon::parse($quotation['date'])->format('d M Y') }}</div>
                    @if(!empty($quotation['valid_until']))
                        <div><strong>{{ __('Valid Until:') }}</strong> {{ \Carbon\Carbon::parse($quotation['valid_until'])->format('d M Y') }}</div>
                    @endif
                    <div><strong>{{ __('Enquiry Ref:') }}</strong> #{{ $enquiry->id }}</div>
                </div>
            </div>
        </div>

        {{-- Client & Details Grid --}}
        <div class="info-grid">
            <div class="info-card">
                <div class="info-title"><i class="fas fa-user-circle mr-1"></i> {{ __('Quotation Prepared For') }}</div>
                <div class="client-name">{{ $quotation['client_name'] }}</div>
                @if(!empty($quotation['school']))
                    <div class="client-school"><i class="fas fa-university mr-1"></i> {{ $quotation['school'] }}</div>
                @endif
                <div class="client-contact">
                    @if(!empty($quotation['designation']))
                        <div><strong>Designation:</strong> {{ $quotation['designation'] }}</div>
                    @endif
                    @if(!empty($quotation['phone']))
                        <div><strong>Phone:</strong> {{ $quotation['phone'] }}</div>
                    @endif
                    @if(!empty($quotation['email']))
                        <div><strong>Email:</strong> {{ $quotation['email'] }}</div>
                    @endif
                    @if(!empty($quotation['address']) || !empty($quotation['city']))
                        <div><strong>Location:</strong> {{ implode(', ', array_filter([$quotation['address'] ?? '', $quotation['city'] ?? ''])) }}</div>
                    @endif
                </div>
            </div>

            <div class="info-card">
                <div class="info-title"><i class="fas fa-building mr-1"></i> {{ __('Issued By') }}</div>
                <div class="client-name">{{ $setting?->app_name ?? 'Skillvation' }} EdTech</div>
                <div class="client-school" style="color:#059669;"><i class="fas fa-certificate mr-1"></i> Skill Labs & Curriculum Partner</div>
                <div class="client-contact">
                    <div><strong>State/UT:</strong> Karnataka, India</div>
                    <div><strong>Jurisdiction:</strong> Bengaluru</div>
                    <div><strong>Support:</strong> {{ $setting?->site_email ?? 'contact@myskill.club' }}</div>
                    <div><strong>Payment Terms:</strong> Advance against Confirmed PO</div>
                </div>
            </div>
        </div>

        {{-- Package Reference Banner --}}
        @if(!empty($quotation['course_title']))
            <div class="package-banner" style="margin-top: 20px;">
                <div>
                    <div class="package-title"><i class="fas fa-layer-group mr-1"></i> {{ $quotation['course_title'] }}</div>
                    <div class="package-sub">{{ __('Comprehensive turnkey skill lab setup, interactive learning kits & trainer enablement.') }}</div>
                </div>
                <div>
                    <span class="badge-cat" style="background:#2563eb; color:#fff; padding:4px 10px; font-size:12px;">
                        {{ __('Turnkey Solution') }}
                    </span>
                </div>
            </div>
        @endif

        {{-- Items Table --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 45px; text-align: center;">#</th>
                    <th>{{ __('Item Description') }}</th>
                    <th style="width: 150px;">{{ __('Category') }}</th>
                    <th style="width: 90px; text-align: center;">{{ __('HSN/SAC') }}</th>
                    <th style="width: 70px; text-align: center;">{{ __('Qty') }}</th>
                    <th style="width: 120px; text-align: right;">{{ __('Unit Price (₹)') }}</th>
                    <th style="width: 130px; text-align: right;">{{ __('Total (₹)') }}</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $sortedItems = collect($items)->sortBy(function($it) {
                        return [strtoupper($it['category'] ?? 'General'), strtoupper($it['item'] ?? '')];
                    })->values();
                    $currentCat = null;
                @endphp
                @forelse($sortedItems as $idx => $it)
                    @php
                        $itemCat = !empty($it['category']) ? trim($it['category']) : 'General';
                    @endphp
                    @if($itemCat !== $currentCat)
                        @php $currentCat = $itemCat; @endphp
                        <tr class="category-header-row" style="background: #f1f5f9; border-top: 2px solid #cbd5e1; border-bottom: 1px solid #cbd5e1;">
                            <td colspan="7" style="padding: 7px 12px; font-weight: 800; color: #1e3a8a; font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.6px;">
                                <i class="fas fa-folder-open mr-2 text-primary"></i> {{ $currentCat }}
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td style="text-align: center; color: #94a3b8;">{{ $loop->iteration }}</td>
                        <td>
                            <strong style="color: #0f172a;">{{ $it['item'] }}</strong>
                            @if(!empty($it['sku']))
                                <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                    <i class="fas fa-barcode"></i> SKU: <span style="font-family: monospace; font-weight: 600; color: #334155;">{{ $it['sku'] }}</span>
                                </div>
                            @endif
                        </td>
                        <td>
                            <span class="badge-cat">{{ $it['category'] ?? 'General' }}</span>
                        </td>
                        <td style="text-align: center; font-size: 12px; color: #64748b;">
                            {{ $it['hsn'] ?? '9023' }}
                        </td>
                        <td style="text-align: center; font-weight: 700;">
                            {{ $it['qty'] }}
                        </td>
                        <td style="text-align: right;">
                            ₹{{ number_format($it['unit_price'], 2) }}
                        </td>
                        <td style="text-align: right; font-weight: 700; color: #0f172a;">
                            ₹{{ number_format($it['total'], 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #94a3b8; padding: 25px;">
                            {{ __('No items listed in this quotation.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Bottom Grid: Calculations & Remittance info --}}
        <div class="bottom-grid">
            <div class="bank-card">
                <div class="bank-card-title">
                    <i class="fas fa-university text-primary"></i> {{ __('Bank Remittance Information') }}
                </div>
                <table class="bank-details" style="width: 100%;">
                    <tr>
                        <td style="width: 120px; color:#64748b;">Account Name:</td>
                        <td style="font-weight: 700; color: #0f172a;">Skillvation EdTech Pvt Ltd</td>
                    </tr>
                    <tr>
                        <td style="color:#64748b;">Bank Name:</td>
                        <td style="font-weight: 600;">HDFC Bank</td>
                    </tr>
                    <tr>
                        <td style="color:#64748b;">Account No:</td>
                        <td style="font-weight: 700; font-family: monospace; letter-spacing: 0.5px;">50200012345678</td>
                    </tr>
                    <tr>
                        <td style="color:#64748b;">IFSC Code:</td>
                        <td style="font-weight: 700; font-family: monospace;">HDFC0001234</td>
                    </tr>
                    <tr>
                        <td style="color:#64748b;">Branch:</td>
                        <td>Bengaluru Main Branch</td>
                    </tr>
                    <tr>
                        <td style="color:#64748b;">UPI ID:</td>
                        <td style="font-weight: 600; color: #2563eb;">skillvation@hdfcbank</td>
                    </tr>
                </table>

                @if(!empty($quotation['notes']))
                    <div style="margin-top: 14px; padding-top: 10px; border-top: 1px dashed #e2e8f0; font-size: 12px; color: #475569;">
                        <strong>Note:</strong> {{ $quotation['notes'] }}
                    </div>
                @endif
            </div>

            <div>
                <table class="calc-table">
                    <tr>
                        <td class="label-col">{{ __('Subtotal:') }}</td>
                        <td class="val-col">₹{{ number_format($subtotal, 2) }}</td>
                    </tr>
                    @if($discount > 0)
                        <tr>
                            <td class="label-col" style="color: #dc2626;">{{ __('Discount:') }}</td>
                            <td class="val-col" style="color: #dc2626;">-₹{{ number_format($discount, 2) }}</td>
                        </tr>
                    @endif
                    @if($taxPercent > 0)
                        <tr>
                            <td class="label-col">{{ __('GST (:rate%):', ['rate' => $taxPercent]) }}</td>
                            <td class="val-col">₹{{ number_format($taxAmount, 2) }}</td>
                        </tr>
                    @endif
                    @if($shipping > 0)
                        <tr>
                            <td class="label-col">{{ __('Shipping / Installation:') }}</td>
                            <td class="val-col">₹{{ number_format($shipping, 2) }}</td>
                        </tr>
                    @endif
                    <tr class="grand-total-row">
                        <td>{{ __('Grand Total:') }}</td>
                        <td class="val-col" style="color:#1e3a8a;">₹{{ number_format($grandTotal, 2) }}</td>
                    </tr>
                </table>

                <div class="words-box">
                    <strong>Amount in words:</strong><br>
                    {{ $amountInWords }}
                </div>
            </div>
        </div>

        {{-- Terms & Conditions --}}
        @if(!empty($quotation['terms']))
            <div class="terms-block">
                <div class="terms-title"><i class="fas fa-file-contract mr-1"></i> {{ __('Terms & Conditions') }}</div>
                <div class="terms-content">{{ $quotation['terms'] }}</div>
            </div>
        @endif

        {{-- Signatures --}}
        <div class="sig-row">
            <div class="sig-box">
                <div class="sig-line"></div>
                <div class="sig-name">{{ $quotation['client_name'] }}</div>
                <div class="sig-role">Customer Acceptance / Seal</div>
            </div>
            <div class="sig-box">
                <div class="sig-line"></div>
                <div class="sig-name">{{ $setting?->app_name ?? 'Skillvation' }} EdTech</div>
                <div class="sig-role">Authorized Signatory</div>
            </div>
        </div>

        {{-- Footer Note --}}
        <div class="footer-note">
            <p>{{ __('This is a computer-generated quotation. For questions or purchase confirmation, contact support at :email.', ['email' => $setting?->site_email ?? 'contact@myskill.club']) }}</p>
        </div>

    </div>

</body>
</html>
