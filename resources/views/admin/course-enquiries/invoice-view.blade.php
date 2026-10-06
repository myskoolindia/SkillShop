@php
    $setting = $setting ?? Cache::get('setting');
    $grandTotal = (float) ($invoice['grand_total'] ?? 0);
    $subtotal   = (float) ($invoice['subtotal'] ?? 0);
    $discount   = (float) ($invoice['discount'] ?? 0);
    $taxPercent = (float) ($invoice['tax_percent'] ?? 0);
    $taxAmount  = (float) ($invoice['tax_amount'] ?? 0);
    $shipping   = (float) ($invoice['shipping'] ?? 0);
    $items      = $invoice['items'] ?? [];
    $payments   = $invoice['payments'] ?? [];
    $totalPaid  = array_sum(array_column($payments, 'amount'));
    $balanceDue = max(0, $grandTotal - $totalPaid);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Tax Invoice') }} - {{ $invoice['invoice_number'] ?? '#' . $enquiry->id }}</title>
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
            color: #0f172a;
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
            background: #2563eb;
            color: #ffffff;
        }
        .btn-edit:hover {
            background: #1d4ed8;
        }
        .btn-payment {
            background: #059669;
            color: #ffffff;
        }
        .btn-payment:hover {
            background: #047857;
        }
        .btn-proforma {
            background: #64748b;
            color: #ffffff;
        }
        .btn-proforma:hover {
            background: #475569;
        }
        .btn-print {
            background: #0f172a;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.3);
        }
        .btn-print:hover {
            background: #1e293b;
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
            background: #dbeafe;
            color: #1e40af;
            font-size: 11px;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
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
            font-size: 17px;
            font-weight: 800;
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

        /* ── Buyer & Seller Grid ── */
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
        .client-school {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 3px;
        }
        .client-name {
            font-size: 14px;
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
            background: linear-gradient(135deg, #eff6ff 0%, #f8fafc 100%);
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
            color: #1e40af;
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
            background: #dbeafe;
            color: #1e40af;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 4px;
        }

        /* ── Totals Grid ── */
        .totals-section {
            display: grid;
            grid-template-columns: 1.3fr 1fr;
            gap: 25px;
            margin-bottom: 25px;
            align-items: start;
        }
        .bank-box {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 16px 20px;
            font-size: 13px;
        }
        .bank-box-title {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #1e3a8a;
            margin-bottom: 10px;
        }
        .bank-details-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            color: #475569;
        }
        .bank-details-row strong {
            color: #0f172a;
        }

        .calc-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        .calc-table td {
            padding: 7px 10px;
        }
        .calc-table td:last-child {
            text-align: right;
            font-weight: 600;
            color: #0f172a;
        }
        .grand-total-row {
            background: #eff6ff;
            border-top: 2px solid #2563eb;
            border-bottom: 2px solid #2563eb;
        }
        .grand-total-row td {
            font-size: 18px !important;
            font-weight: 800 !important;
            color: #1e40af !important;
            padding: 12px 10px !important;
        }

        .words-box {
            background: #f1f5f9;
            border-left: 4px solid #2563eb;
            padding: 10px 16px;
            font-size: 13px;
            font-style: italic;
            color: #334155;
            margin-bottom: 25px;
            border-radius: 0 6px 6px 0;
        }

        /* ── Payment Receipts Ledger Box ── */
        .payments-box {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #fafafa;
            padding: 18px;
            margin-bottom: 25px;
        }
        .payments-box-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        .payments-box-title {
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #0f172a;
        }
        .payment-status-stamp {
            display: inline-block;
            font-weight: 900;
            font-size: 12px;
            letter-spacing: 1px;
            padding: 4px 14px;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .stamp-paid {
            background: #dcfce7;
            color: #15803d;
            border: 1.5px solid #22c55e;
        }
        .stamp-partial {
            background: #fef3c7;
            color: #b45309;
            border: 1.5px solid #f59e0b;
        }
        .stamp-unpaid {
            background: #fee2e2;
            color: #b91c1c;
            border: 1.5px solid #ef4444;
        }

        .payments-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-bottom: 10px;
        }
        .payments-table th {
            background: #f1f5f9;
            color: #475569;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 7px 10px;
            text-align: left;
        }
        .payments-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }

        .payment-summary-strip {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 14px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
        }

        /* ── Terms & Signatures ── */
        .notes-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 30px;
            font-size: 12.5px;
            color: #475569;
        }
        .notes-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 14px 16px;
            white-space: pre-line;
            line-height: 1.6;
        }
        .notes-card-title {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 40px;
            padding-top: 15px;
        }
        .sig-box {
            text-align: center;
            width: 230px;
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
                background: #0f172a !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .badge-cat, .package-banner, .grand-total-row, .words-box, .doc-badge, .stamp-paid, .stamp-partial, .stamp-unpaid {
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
            <a href="{{ route('admin.course-enquiry.show', $enquiry->id) }}#payment-card" class="btn btn-payment">
                <i class="fas fa-hand-holding-usd"></i> {{ __('Record Payment') }}
            </a>
            <a href="{{ route('admin.course-enquiry.invoice', $enquiry->id) }}" class="btn btn-edit">
                <i class="fas fa-edit"></i> {{ __('Edit Invoice') }}
            </a>
            @if($enquiry->hasProformaInvoice())
                <a href="{{ route('admin.course-enquiry.proforma.view', $enquiry->id) }}" class="btn btn-proforma">
                    <i class="fas fa-file-invoice-dollar"></i> {{ __('View Proforma') }}
                </a>
            @endif
            <button onclick="window.print()" class="btn btn-print">
                <i class="fas fa-print"></i> {{ __('Print / PDF') }}
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
                <div class="doc-badge">{{ __('Official Tax Invoice') }} &bull; {{ __('Original for Recipient') }}</div>
                <h1 class="doc-title">{{ __('TAX INVOICE') }}</h1>
                <div class="doc-number">{{ $invoice['invoice_number'] }}</div>
                <div class="doc-meta">
                    <div><strong>{{ __('Invoice Date:') }}</strong> {{ \Carbon\Carbon::parse($invoice['date'])->format('d M Y') }}</div>
                    @if(!empty($invoice['due_date']))
                        <div><strong>{{ __('Due Date:') }}</strong> {{ \Carbon\Carbon::parse($invoice['due_date'])->format('d M Y') }}</div>
                    @endif
                    <div><strong>{{ __('Enquiry Ref:') }}</strong> #{{ $enquiry->id }}</div>
                </div>
            </div>
        </div>

        {{-- Buyer & Seller Grid --}}
        <div class="info-grid">
            <div class="info-card">
                <div class="info-title"><i class="fas fa-university mr-1"></i> {{ __('Billed To / Recipient') }}</div>
                <div class="client-school">{{ $invoice['school'] ?: $invoice['client_name'] }}</div>
                @if(!empty($invoice['client_name']) && $invoice['client_name'] !== $invoice['school'])
                    <div class="client-name">Attn: {{ $invoice['client_name'] }} @if(!empty($invoice['designation'])) ({{ $invoice['designation'] }}) @endif</div>
                @endif
                <div class="client-contact">
                    @if(!empty($invoice['gstin']))
                        <div><strong>GSTIN / Tax ID:</strong> <span style="font-family: monospace; font-weight:700;">{{ $invoice['gstin'] }}</span></div>
                    @endif
                    @if(!empty($invoice['pan']))
                        <div><strong>PAN:</strong> <span style="font-family: monospace; font-weight:700;">{{ $invoice['pan'] }}</span></div>
                    @endif
                    @if(!empty($invoice['phone']))
                        <div><strong>Phone:</strong> {{ $invoice['phone'] }}</div>
                    @endif
                    @if(!empty($invoice['email']))
                        <div><strong>Email:</strong> {{ $invoice['email'] }}</div>
                    @endif
                    @if(!empty($invoice['address']))
                        <div style="margin-top: 4px;"><strong>Address:</strong> {{ $invoice['address'] }} @if(!empty($invoice['city'])), {{ $invoice['city'] }} @endif</div>
                    @endif
                </div>
            </div>

            <div class="info-card">
                <div class="info-title"><i class="fas fa-building mr-1"></i> {{ __('Supplier / Service Provider') }}</div>
                <div class="client-school">{{ $invoice['account_name'] ?? 'Skillvation EdTech Pvt Ltd' }}</div>
                <div class="client-contact">
                    <div><strong>GSTIN:</strong> <span style="font-family: monospace; font-weight:700;">29AAACS1234F1Z5</span></div>
                    <div><strong>PAN:</strong> <span style="font-family: monospace; font-weight:700;">AAACS1234F</span></div>
                    <div><strong>Place of Supply:</strong> {{ $invoice['city'] ?? 'Bengaluru' }}, Karnataka</div>
                    <div><strong>Reverse Charge:</strong> No</div>
                    <div><strong>Bank:</strong> {{ $invoice['bank_name'] ?? 'HDFC Bank' }} (A/C: {{ $invoice['account_number'] ?? '50200012345678' }})</div>
                </div>
            </div>
        </div>

        {{-- Package Banner --}}
        @if(!empty($invoice['course_title']))
            <div class="package-banner">
                <div>
                    <div class="package-title"><i class="fas fa-cube mr-2"></i>{{ $invoice['course_title'] }}</div>
                    <div class="package-sub">{{ __('Official Delivery & Implementation for Student Labs') }}</div>
                </div>
                <span class="badge-cat">{{ __('Composite Skill Lab') }}</span>
            </div>
        @endif

        {{-- Line Items Table --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 38px; text-align: center;">#</th>
                    <th style="width: 130px;">{{ __('Category') }}</th>
                    <th>{{ __('Item Description') }}</th>
                    <th style="width: 90px;">{{ __('HSN/SAC') }}</th>
                    <th style="width: 65px; text-align: center;">{{ __('Qty') }}</th>
                    <th style="width: 110px; text-align: right;">{{ __('Rate (₹)') }}</th>
                    <th style="width: 120px; text-align: right;">{{ __('Amount (₹)') }}</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $sortedItems = collect($items)->sortBy(function($it) {
                        return [strtoupper($it['category'] ?? 'Equipment'), strtoupper($it['item'] ?? '')];
                    })->values();
                    $currentCat = null;
                @endphp
                @forelse($sortedItems as $idx => $item)
                    @php
                        $itemCat = !empty($item['category']) ? trim($item['category']) : 'Equipment';
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
                        <td style="text-align: center; font-weight: 600;">{{ $loop->iteration }}</td>
                        <td>
                            <span class="badge-cat">{{ $itemCat }}</span>
                        </td>
                        <td>
                            <strong style="color: #0f172a;">{{ $item['item'] }}</strong>
                            @if(!empty($item['sku']))
                                <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                    <i class="fas fa-barcode"></i> SKU: <span style="font-family: monospace; font-weight:600;">{{ $item['sku'] }}</span>
                                </div>
                            @endif
                        </td>
                        <td style="font-family: monospace; font-size: 12px;">{{ $item['hsn'] ?? '9023' }}</td>
                        <td style="text-align: center; font-weight: 700;">{{ $item['qty'] }}</td>
                        <td style="text-align: right;">₹{{ number_format($item['unit_price'], 2) }}</td>
                        <td style="text-align: right; font-weight: 700; color: #0f172a;">₹{{ number_format($item['total'] ?? ($item['qty'] * $item['unit_price']), 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 25px; color: #94a3b8;">
                            {{ __('No line items defined.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Totals & Bank Section --}}
        <div class="totals-section">
            {{-- Bank Details --}}
            <div class="bank-box">
                <div class="bank-box-title"><i class="fas fa-university mr-1"></i> {{ __('Bank Remittance Information') }}</div>
                <div class="bank-details-row">
                    <span>Beneficiary Name:</span>
                    <strong>{{ $invoice['account_name'] ?? 'Skillvation EdTech Pvt Ltd' }}</strong>
                </div>
                <div class="bank-details-row">
                    <span>Bank Name:</span>
                    <strong>{{ $invoice['bank_name'] ?? 'HDFC Bank' }}</strong>
                </div>
                <div class="bank-details-row">
                    <span>Account Number:</span>
                    <strong style="font-family: monospace; letter-spacing:0.5px;">{{ $invoice['account_number'] ?? '50200012345678' }}</strong>
                </div>
                <div class="bank-details-row">
                    <span>IFSC Code:</span>
                    <strong style="font-family: monospace;">{{ $invoice['ifsc_code'] ?? 'HDFC0001234' }}</strong>
                </div>
                <div class="bank-details-row">
                    <span>Branch:</span>
                    <strong>{{ $invoice['branch'] ?? 'Bengaluru Main Branch' }}</strong>
                </div>
                @if(!empty($invoice['upi_id']))
                    <div class="bank-details-row" style="margin-top: 6px; padding-top: 6px; border-top: 1px dashed #e2e8f0;">
                        <span>UPI ID / VPA:</span>
                        <strong style="color: #2563eb;">{{ $invoice['upi_id'] }}</strong>
                    </div>
                @endif
            </div>

            {{-- Calculations --}}
            <table class="calc-table">
                <tr>
                    <td style="color: #64748b;">{{ __('Subtotal:') }}</td>
                    <td>₹{{ number_format($subtotal, 2) }}</td>
                </tr>
                @if($discount > 0)
                    <tr>
                        <td style="color: #15803d;">{{ __('Discount:') }}</td>
                        <td style="color: #15803d;">- ₹{{ number_format($discount, 2) }}</td>
                    </tr>
                @endif
                @if($taxPercent > 0)
                    <tr>
                        <td style="color: #64748b;">{{ __('GST') }} ({{ $taxPercent }}%):</td>
                        <td>₹{{ number_format($taxAmount, 2) }}</td>
                    </tr>
                @endif
                @if($shipping > 0)
                    <tr>
                        <td style="color: #64748b;">{{ __('Freight & Setup:') }}</td>
                        <td>₹{{ number_format($shipping, 2) }}</td>
                    </tr>
                @endif
                <tr class="grand-total-row">
                    <td>{{ __('Invoice Grand Total:') }}</td>
                    <td>₹{{ number_format($grandTotal, 2) }}</td>
                </tr>
            </table>
        </div>

        {{-- Amount in Words --}}
        <div class="words-box">
            <strong>{{ __('Amount in Words:') }}</strong> {{ $amountInWords ?? 'Rupees Only' }}
        </div>

        {{-- ── Payment Receipts Ledger & Settlement Box ── --}}
        <div class="payments-box">
            <div class="payments-box-header">
                <div class="payments-box-title">
                    <i class="fas fa-receipt mr-1 text-primary"></i> {{ __('Payment Collection & Settlement Record') }}
                </div>
                <div>
                    @if($balanceDue <= 0 && $totalPaid > 0)
                        <span class="payment-status-stamp stamp-paid"><i class="fas fa-check-circle mr-1"></i> PAID IN FULL</span>
                    @elseif($totalPaid > 0)
                        <span class="payment-status-stamp stamp-partial"><i class="fas fa-adjust mr-1"></i> PARTIALLY PAID (ADVANCE)</span>
                    @else
                        <span class="payment-status-stamp stamp-unpaid"><i class="fas fa-clock mr-1"></i> PAYMENT PENDING</span>
                    @endif
                </div>
            </div>

            @if(!empty($payments) && count($payments) > 0)
                <table class="payments-table">
                    <thead>
                        <tr>
                            <th>{{ __('Receipt #') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Payment Type') }}</th>
                            <th>{{ __('Method') }}</th>
                            <th>{{ __('UTR / Ref #') }}</th>
                            <th style="text-align: right;">{{ __('Amount Paid') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $p)
                            <tr>
                                <td style="font-family: monospace; font-weight: 700;">{{ $p['receipt_number'] ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($p['date'])->format('d M Y') }}</td>
                                <td>
                                    <span class="badge-cat">
                                        {{ $p['type'] === 'advance' ? __('Advance') : ($p['type'] === 'balance' ? __('Balance') : __('Full Payment')) }}
                                    </span>
                                </td>
                                <td>{{ strtoupper($p['method'] ?? 'Bank Transfer') }}</td>
                                <td style="font-family: monospace;">{{ $p['transaction_id'] ?? '-' }}</td>
                                <td style="text-align: right; font-weight: 700; color: #15803d;">₹{{ number_format($p['amount'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="font-size: 13px; color: #64748b; font-style: italic; margin-bottom: 8px;">
                    {{ __('No payment receipts recorded yet for this invoice.') }}
                </div>
            @endif

            <div class="payment-summary-strip">
                <div>
                    <span style="color: #64748b;">{{ __('Total Received:') }}</span>
                    <strong style="color: #15803d; margin-left: 5px;">₹{{ number_format($totalPaid, 2) }}</strong>
                </div>
                <div>
                    <span style="color: #64748b;">{{ __('Balance Due:') }}</span>
                    <strong style="color: {{ $balanceDue > 0 ? '#b91c1c' : '#15803d' }}; margin-left: 5px; font-size: 15px;">
                        ₹{{ number_format($balanceDue, 2) }}
                    </strong>
                </div>
            </div>
        </div>

        {{-- Terms & Conditions / Declaration --}}
        <div class="notes-grid">
            <div class="notes-card">
                <div class="notes-card-title"><i class="fas fa-file-contract mr-1"></i> {{ __('Terms & Conditions') }}</div>
                {{ $invoice['terms'] ?? "1. Payment terms as agreed.\n2. Warranty covers manufacturing defects for 1 year.\n3. Subject to Bengaluru jurisdiction." }}
            </div>
            <div class="notes-card">
                <div class="notes-card-title"><i class="fas fa-stamp mr-1"></i> {{ __('Declaration') }}</div>
                {{ $invoice['notes'] ?? "Certified that the particulars given above are true and correct, and the amount indicated represents the price actually charged and there is no flow of additional consideration." }}
            </div>
        </div>

        {{-- Signatures --}}
        <div class="signatures">
            <div class="sig-box">
                <div class="sig-line"></div>
                <div class="sig-name">{{ $invoice['school'] ?: $invoice['client_name'] }}</div>
                <div class="sig-role">{{ __('Authorized Signatory / Buyer Stamp') }}</div>
            </div>
            <div class="sig-box">
                <div class="sig-line"></div>
                <div class="sig-name">{{ $invoice['account_name'] ?? 'Skillvation EdTech Pvt Ltd' }}</div>
                <div class="sig-role">{{ __('Authorized Signatory') }}</div>
            </div>
        </div>

        {{-- Footer Note --}}
        <div class="footer-note">
            {{ __('This is a computer-generated tax invoice. Registered with GST under Central Goods and Services Tax Act, 2017.') }}
        </div>

    </div>

</body>
</html>
