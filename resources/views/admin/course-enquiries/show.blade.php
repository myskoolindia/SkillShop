@extends('admin.master_layout')
@section('title')
    <title>{{ __('Enquiry Details') }}</title>
@endsection
@section('admin-content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>{{ __('Enquiry Details') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active">
                    <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                </div>
                <div class="breadcrumb-item">
                    <a href="{{ route('admin.course-enquiries') }}">{{ __('Manage Enquiries') }}</a>
                </div>
                <div class="breadcrumb-item">#{{ $enquiry->id }}</div>
            </div>
        </div>

        <div class="section-body">
            <div class="mt-4 row justify-content-center">
                <div class="col-12 col-lg-10">

                    {{-- ── Contact + Enquiry Card ─────────────────── --}}
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">
                                <i class="fas fa-user-circle text-primary mr-2"></i>
                                {{ $enquiry->name }}
                                @if($enquiry->designation)
                                    <small class="text-muted font-weight-normal ml-1">— {{ $enquiry->designation }}</small>
                                @endif
                            </h4>
                            <div class="d-flex align-items-center">
                                <label class="mb-0 mr-2 text-muted font-weight-bold" style="font-size:13px;">{{ __('Status') }}:</label>
                                <select id="statusDropdown" class="form-control form-control-sm" style="width:140px;" data-id="{{ $enquiry->id }}">
                                    <option value="new"       {{ $enquiry->status == 'new'       ? 'selected' : '' }}>{{ __('New') }}</option>
                                    <option value="read"      {{ $enquiry->status == 'read'      ? 'selected' : '' }}>{{ __('Read') }}</option>
                                    <option value="contacted" {{ $enquiry->status == 'contacted' ? 'selected' : '' }}>{{ __('Contacted') }}</option>
                                    <option value="closed"    {{ $enquiry->status == 'closed'    ? 'selected' : '' }}>{{ __('Closed') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">

                                {{-- Contact details --}}
                                <div class="col-md-6">
                                    <h6 class="section-label mb-3">{{ __('Contact Details') }}</h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td class="text-muted" style="width:130px;">{{ __('Phone') }}</td>
                                            <td>
                                                <a href="tel:{{ $enquiry->phone }}" class="font-weight-bold">{{ $enquiry->phone }}</a>
                                                &nbsp;
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/','', (string) ($enquiry->phone ?? '')) }}"
                                                   target="_blank" class="btn btn-success btn-sm py-0 px-2" style="font-size:12px;">
                                                    <i class="fab fa-whatsapp"></i> WhatsApp
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">{{ __('Email') }}</td>
                                            <td><a href="mailto:{{ $enquiry->email }}">{{ $enquiry->email }}</a></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">{{ __('School') }}</td>
                                            <td class="font-weight-bold">{{ $enquiry->school ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">{{ __('City') }}</td>
                                            <td>{{ $enquiry->city ?? '—' }}</td>
                                        </tr>
                                        @if($enquiry->address)
                                        <tr>
                                            <td class="text-muted">{{ __('Address / Space') }}</td>
                                            <td>{{ $enquiry->address }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>

                                {{-- Enquiry metadata --}}
                                <div class="col-md-6">
                                    <h6 class="section-label mb-3">{{ __('Enquiry Details') }}</h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td class="text-muted" style="width:130px;">{{ __('Source') }}</td>
                                            <td>
                                                @if($enquiry->source)
                                                    <span class="badge badge-info">
                                                        {{ ucwords(str_replace(['-','_'], ' ', $enquiry->source)) }}
                                                    </span>
                                                @else —
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">{{ __('Package') }}</td>
                                            <td class="font-weight-bold">{{ $enquiry->course_title ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">{{ __('Request Type') }}</td>
                                            <td>{{ $enquiry->message ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">{{ __('Status') }}</td>
                                            <td>
                                                @if(!$enquiry->status || $enquiry->status === 'new')
                                                    <span class="badge badge-primary">{{ __('New') }}</span>
                                                @elseif($enquiry->status === 'read')
                                                    <span class="badge badge-secondary">{{ __('Read') }}</span>
                                                @elseif($enquiry->status === 'contacted')
                                                    <span class="badge badge-warning">{{ __('Contacted') }}</span>
                                                @elseif($enquiry->status === 'closed')
                                                    <span class="badge badge-success">{{ __('Closed') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">{{ __('Received') }}</td>
                                            <td>
                                                {{ $enquiry->created_at?->format('d M Y, h:i A') ?? '—' }}
                                                <br>
                                                <small class="text-muted">{{ $enquiry->created_at?->diffForHumans() }}</small>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                            </div>

                            {{-- Action buttons --}}
                            <div class="mt-3 pt-3 border-top">
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/','', (string) ($enquiry->phone ?? '')) }}"
                                   target="_blank" class="btn btn-success mr-1">
                                    <i class="fab fa-whatsapp"></i> {{ __('WhatsApp') }}
                                </a>
                                <a href="tel:{{ $enquiry->phone }}" class="btn btn-primary mr-1">
                                    <i class="fas fa-phone"></i> {{ __('Call') }}
                                </a>
                                <a href="mailto:{{ $enquiry->email }}" class="btn btn-secondary mr-1">
                                    <i class="fas fa-envelope"></i> {{ __('Email') }}
                                </a>
                                <a href="{{ route('admin.course-enquiries') }}" class="btn btn-light float-right">
                                    <i class="fas fa-arrow-left"></i> {{ __('Back to List') }}
                                </a>
                            </div>
                        </div>
                    </div>{{-- /.card --}}

                    {{-- ── Quotation Management Card ──────────── --}}
                    <div class="card mt-4 border-left-primary">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>
                                <i class="fas fa-file-invoice text-primary mr-2"></i>
                                {{ __('Quotation') }}
                                @if(!empty($quotation['items']))
                                    <span class="badge badge-success ml-2">
                                        <i class="fas fa-check mr-1"></i>{{ __('Created') }}: {{ $quotation['quotation_number'] }}
                                    </span>
                                @else
                                    <span class="badge badge-light border ml-2 text-muted">{{ __('Not Created Yet') }}</span>
                                @endif
                                @if($enquiry->isQuotationLocked())
                                    <span class="badge badge-warning ml-1" title="{{ __('Quotation is locked because a Proforma Invoice has been issued.') }}">
                                        <i class="fas fa-lock mr-1"></i>{{ __('Locked') }}
                                    </span>
                                @endif
                            </h4>
                            <div>
                                @if(!empty($quotation['items']))
                                    <a href="{{ route('admin.course-enquiry.quotation.view', $enquiry->id) }}" target="_blank" class="btn btn-sm btn-primary mr-1">
                                        <i class="fas fa-eye mr-1"></i> {{ __('View / Print') }}
                                    </a>
                                    @if($enquiry->isQuotationLocked())
                                        <span class="btn btn-sm btn-light border text-muted mr-1" title="{{ __('Quotation cannot be edited because Proforma Invoice is issued.') }}">
                                            <i class="fas fa-lock mr-1"></i> {{ __('Locked') }}
                                        </span>
                                    @else
                                        <a href="{{ route('admin.course-enquiry.quotation', $enquiry->id) }}" class="btn btn-sm btn-outline-primary mr-1">
                                            <i class="fas fa-edit mr-1"></i> {{ __('Edit Quotation') }}
                                        </a>
                                    @endif
                                    @if(!$enquiry->hasProformaInvoice())
                                        <a href="{{ route('admin.course-enquiry.proforma', $enquiry->id) }}" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-file-invoice-dollar mr-1"></i> {{ __('Convert to Proforma') }}
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ route('admin.course-enquiry.quotation', $enquiry->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus mr-1"></i> {{ __('Create Quotation') }}
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="card-body">
                            @if(!empty($quotation['items']))
                                <div class="row mb-3 pb-3 border-bottom">
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">{{ __('Quote Number') }}</small>
                                        <strong class="text-primary">{{ $quotation['quotation_number'] }}</strong>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">{{ __('Date') }}</small>
                                        <strong>{{ \Carbon\Carbon::parse($quotation['date'])->format('d M Y') }}</strong>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">{{ __('Valid Until') }}</small>
                                        <strong>{{ !empty($quotation['valid_until']) ? \Carbon\Carbon::parse($quotation['valid_until'])->format('d M Y') : '—' }}</strong>
                                    </div>
                                    <div class="col-md-3 text-md-right">
                                        <small class="text-muted d-block">{{ __('Grand Total') }}</small>
                                        <strong class="text-success" style="font-size: 18px;">₹{{ number_format($quotation['grand_total'] ?? 0, 2) }}</strong>
                                    </div>
                                </div>

                                {{-- Line items preview table --}}
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 40px;">#</th>
                                                <th>{{ __('Item Description') }}</th>
                                                <th>{{ __('Category') }}</th>
                                                <th class="text-center" style="width: 80px;">{{ __('Qty') }}</th>
                                                <th class="text-right" style="width: 120px;">{{ __('Unit Price') }}</th>
                                                <th class="text-right" style="width: 130px;">{{ __('Total') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($quotation['items'] as $item)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <span class="font-weight-bold">{{ $item['item'] }}</span>
                                                        @if(!empty($item['sku']))
                                                            <span class="badge badge-light border ml-1 text-muted small"><i class="fas fa-barcode mr-1"></i>{{ $item['sku'] }}</span>
                                                        @endif
                                                    </td>
                                                    <td><span class="badge badge-light border">{{ $item['category'] ?? 'General' }}</span></td>
                                                    <td class="text-center">{{ $item['qty'] }}</td>
                                                    <td class="text-right">₹{{ number_format($item['unit_price'], 2) }}</td>
                                                    <td class="text-right font-weight-bold">₹{{ number_format($item['total'], 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="5" class="text-right text-muted font-weight-bold">{{ __('Subtotal:') }}</td>
                                                <td class="text-right font-weight-bold">₹{{ number_format($quotation['subtotal'] ?? 0, 2) }}</td>
                                            </tr>
                                            @if(!empty($quotation['discount']) && $quotation['discount'] > 0)
                                            <tr>
                                                <td colspan="5" class="text-right text-danger font-weight-bold">{{ __('Discount:') }}</td>
                                                <td class="text-right text-danger font-weight-bold">-₹{{ number_format($quotation['discount'], 2) }}</td>
                                            </tr>
                                            @endif
                                            @if(!empty($quotation['tax_amount']) && $quotation['tax_amount'] > 0)
                                            <tr>
                                                <td colspan="5" class="text-right text-muted font-weight-bold">{{ __('GST (:rate%):', ['rate' => $quotation['tax_percent'] ?? 18]) }}</td>
                                                <td class="text-right font-weight-bold">₹{{ number_format($quotation['tax_amount'], 2) }}</td>
                                            </tr>
                                            @endif
                                            @if(!empty($quotation['shipping']) && $quotation['shipping'] > 0)
                                            <tr>
                                                <td colspan="5" class="text-right text-muted font-weight-bold">{{ __('Shipping / Setup:') }}</td>
                                                <td class="text-right font-weight-bold">₹{{ number_format($quotation['shipping'], 2) }}</td>
                                            </tr>
                                            @endif
                                            <tr class="table-primary">
                                                <td colspan="5" class="text-right font-weight-bold text-dark" style="font-size:15px;">{{ __('Grand Total:') }}</td>
                                                <td class="text-right font-weight-bold text-primary" style="font-size:16px;">₹{{ number_format($quotation['grand_total'] ?? 0, 2) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <div class="text-muted mb-3">
                                        <i class="fas fa-file-invoice fa-3x text-light mb-2 d-block"></i>
                                        {{ __('No quotation created yet for this enquiry. You can generate a custom quotation with package items, pricing, and GST.') }}
                                    </div>
                                    <a href="{{ route('admin.course-enquiry.quotation', $enquiry->id) }}" class="btn btn-primary">
                                        <i class="fas fa-plus mr-1"></i> {{ __('Create Quotation Now') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>{{-- /.card quotation --}}

                    {{-- ── Proforma Invoice Management Card ──────── --}}
                    <div class="card mt-4 border-left-success">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>
                                <i class="fas fa-file-invoice-dollar text-success mr-2"></i>
                                {{ __('Proforma Invoice') }}
                                @if(!empty($proforma['items']) && $enquiry->hasProformaInvoice())
                                    <span class="badge badge-success ml-2">
                                        <i class="fas fa-check mr-1"></i>{{ __('Created') }}: {{ $proforma['invoice_number'] }}
                                    </span>
                                    <span class="badge badge-info ml-1" title="Current revision">
                                        <i class="fas fa-code-branch mr-1"></i>Rev {{ $enquiry->proforma_version }}
                                    </span>
                                    @if(count($enquiry->proforma_versions_list) > 0)
                                        <span class="badge badge-light border ml-1 text-muted" title="{{ count($enquiry->proforma_versions_list) }} previous version(s) saved">
                                            <i class="fas fa-history mr-1"></i>{{ count($enquiry->proforma_versions_list) }} {{ __('Prior Rev(s)') }}
                                        </span>
                                    @endif
                                @else
                                    <span class="badge badge-light border ml-2 text-muted">{{ __('Not Created Yet') }}</span>
                                @endif
                                @if($enquiry->isProformaLocked())
                                    <span class="badge badge-warning ml-1" title="{{ __('Locked because Final Tax Invoice has been generated.') }}">
                                        <i class="fas fa-lock mr-1"></i>{{ __('Locked') }}
                                    </span>
                                @endif
                            </h4>
                            <div>
                                @if(!empty($proforma['items']) && $enquiry->hasProformaInvoice())
                                    <a href="{{ route('admin.course-enquiry.proforma.view', $enquiry->id) }}" target="_blank" class="btn btn-sm btn-success mr-1">
                                        <i class="fas fa-eye mr-1"></i> {{ __('View / Print') }}
                                    </a>
                                    @if($enquiry->isProformaLocked())
                                        <span class="btn btn-sm btn-light border text-muted mr-1" title="{{ __('Proforma invoice is locked because Final Tax Invoice is issued.') }}">
                                            <i class="fas fa-lock mr-1"></i> {{ __('Locked') }}
                                        </span>
                                    @else
                                        <a href="{{ route('admin.course-enquiry.proforma', $enquiry->id) }}" class="btn btn-sm btn-outline-success mr-1">
                                            <i class="fas fa-edit mr-1"></i> {{ __('Edit Proforma') }} (Rev {{ $enquiry->proforma_version }})
                                        </a>
                                    @endif
                                    @if(!$enquiry->hasInvoice())
                                        <a href="{{ route('admin.course-enquiry.invoice', $enquiry->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-receipt mr-1"></i> {{ __('Convert to Tax Invoice') }}
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ route('admin.course-enquiry.proforma', $enquiry->id) }}" class="btn btn-sm btn-success">
                                        <i class="fas fa-plus mr-1"></i> {{ __('Create Proforma Invoice') }}
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="card-body">
                            @if(!empty($proforma['items']) && $enquiry->hasProformaInvoice())
                                <div class="row mb-3 pb-3 border-bottom">
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">{{ __('Invoice Number') }}</small>
                                        <strong class="text-success">{{ $proforma['invoice_number'] }}</strong>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">{{ __('Invoice Date') }}</small>
                                        <strong>{{ \Carbon\Carbon::parse($proforma['date'])->format('d M Y') }}</strong>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">{{ __('Due Date') }}</small>
                                        <strong>{{ !empty($proforma['due_date']) ? \Carbon\Carbon::parse($proforma['due_date'])->format('d M Y') : '—' }}</strong>
                                    </div>
                                    <div class="col-md-3 text-md-right">
                                        <small class="text-muted d-block">{{ __('Total Payable') }}</small>
                                        <strong class="text-success" style="font-size: 18px;">₹{{ number_format($proforma['grand_total'] ?? 0, 2) }}</strong>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h6 class="text-muted font-weight-bold" style="font-size:12px; text-transform:uppercase;">{{ __('Bill To') }}</h6>
                                        <div><strong>{{ $proforma['school'] ?: $proforma['client_name'] }}</strong></div>
                                        @if(!empty($proforma['gstin']))
                                            <div class="text-muted font-weight-bold" style="font-size:12px;">GSTIN: {{ $proforma['gstin'] }}</div>
                                        @endif
                                        <div class="text-muted" style="font-size:13px;">{{ $proforma['email'] }} | {{ $proforma['phone'] }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted font-weight-bold" style="font-size:12px; text-transform:uppercase;">{{ __('Bank Account') }}</h6>
                                        <div style="font-size:13px;"><strong>{{ $proforma['bank_name'] ?? 'HDFC Bank' }}</strong> — A/C: <code>{{ $proforma['account_number'] ?? '50200012345678' }}</code></div>
                                        <div class="text-muted" style="font-size:12px;">IFSC: {{ $proforma['ifsc_code'] ?? 'HDFC0001234' }} | UPI: {{ $proforma['upi_id'] ?? 'skillvation@hdfcbank' }}</div>
                                    </div>
                                </div>

                                {{-- Proforma line items preview table --}}
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 40px;">#</th>
                                                <th>{{ __('Item Description') }}</th>
                                                <th>{{ __('Category') }}</th>
                                                <th class="text-center" style="width: 80px;">{{ __('Qty') }}</th>
                                                <th class="text-right" style="width: 120px;">{{ __('Unit Price') }}</th>
                                                <th class="text-right" style="width: 130px;">{{ __('Total') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($proforma['items'] as $item)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <span class="font-weight-bold">{{ $item['item'] }}</span>
                                                        @if(!empty($item['sku']))
                                                            <span class="badge badge-light border ml-1 text-muted small"><i class="fas fa-barcode mr-1"></i>{{ $item['sku'] }}</span>
                                                        @endif
                                                    </td>
                                                    <td><span class="badge badge-light border">{{ $item['category'] ?? 'General' }}</span></td>
                                                    <td class="text-center">{{ $item['qty'] }}</td>
                                                    <td class="text-right">₹{{ number_format($item['unit_price'], 2) }}</td>
                                                    <td class="text-right font-weight-bold">₹{{ number_format($item['total'], 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="5" class="text-right text-muted font-weight-bold">{{ __('Subtotal:') }}</td>
                                                <td class="text-right font-weight-bold">₹{{ number_format($proforma['subtotal'] ?? 0, 2) }}</td>
                                            </tr>
                                            @if(!empty($proforma['discount']) && $proforma['discount'] > 0)
                                            <tr>
                                                <td colspan="5" class="text-right text-danger font-weight-bold">{{ __('Discount:') }}</td>
                                                <td class="text-right text-danger font-weight-bold">-₹{{ number_format($proforma['discount'], 2) }}</td>
                                            </tr>
                                            @endif
                                            @if(!empty($proforma['tax_amount']) && $proforma['tax_amount'] > 0)
                                            <tr>
                                                <td colspan="5" class="text-right text-muted font-weight-bold">{{ __('GST (:rate%):', ['rate' => $proforma['tax_percent'] ?? 18]) }}</td>
                                                <td class="text-right font-weight-bold">₹{{ number_format($proforma['tax_amount'], 2) }}</td>
                                            </tr>
                                            @endif
                                            @if(!empty($proforma['shipping']) && $proforma['shipping'] > 0)
                                            <tr>
                                                <td colspan="5" class="text-right text-muted font-weight-bold">{{ __('Shipping / Freight:') }}</td>
                                                <td class="text-right font-weight-bold">₹{{ number_format($proforma['shipping'], 2) }}</td>
                                            </tr>
                                            @endif
                                            <tr class="table-success">
                                                <td colspan="5" class="text-right font-weight-bold" style="font-size:15px;">{{ __('Grand Total Payable:') }}</td>
                                                <td class="text-right font-weight-bold text-success" style="font-size:16px;">₹{{ number_format($proforma['grand_total'] ?? 0, 2) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <div class="text-muted mb-3">
                                        <i class="fas fa-file-invoice-dollar fa-3x text-light mb-2 d-block"></i>
                                        {{ __('No Proforma Invoice created yet. You can create one from scratch or convert existing quotation data into a commercial proforma invoice.') }}
                                    </div>
                                    <a href="{{ route('admin.course-enquiry.proforma', $enquiry->id) }}" class="btn btn-success">
                                        <i class="fas fa-plus mr-1"></i> {{ __('Create Proforma Invoice') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>{{-- /.card proforma --}}

                    {{-- ── Final Tax Invoice & Payment Collection Card ──────── --}}
                    <div class="card mt-4 border-left-info" id="payment-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>
                                <i class="fas fa-receipt text-primary mr-2"></i>
                                {{ __('Final Tax Invoice & Payment Collection') }}
                                @if($enquiry->hasInvoice())
                                    <span class="badge badge-primary ml-2">
                                        <i class="fas fa-check mr-1"></i>{{ __('Created') }}: {{ $invoice['invoice_number'] }}
                                    </span>
                                    @if(($paymentSummary['payment_status'] ?? '') === 'paid')
                                        <span class="badge badge-success ml-1"><i class="fas fa-check-circle mr-1"></i>{{ __('PAID IN FULL') }}</span>
                                    @elseif(($paymentSummary['payment_status'] ?? '') === 'partially_paid')
                                        <span class="badge badge-warning ml-1"><i class="fas fa-adjust mr-1"></i>{{ __('PARTIALLY PAID (ADVANCE)') }}</span>
                                    @else
                                        <span class="badge badge-danger ml-1"><i class="fas fa-clock mr-1"></i>{{ __('PAYMENT PENDING') }}</span>
                                    @endif
                                @else
                                    <span class="badge badge-light border ml-2 text-muted">{{ __('Not Created Yet') }}</span>
                                @endif
                            </h4>
                            <div>
                                @if($enquiry->hasInvoice())
                                    <button type="button" class="btn btn-sm btn-success mr-1" data-toggle="modal" data-target="#recordPaymentModal">
                                        <i class="fas fa-hand-holding-usd mr-1"></i> {{ __('Record Payment') }}
                                    </button>
                                    <a href="{{ route('admin.course-enquiry.invoice.view', $enquiry->id) }}" target="_blank" class="btn btn-sm btn-primary mr-1">
                                        <i class="fas fa-eye mr-1"></i> {{ __('View / Print') }}
                                    </a>
                                    <a href="{{ route('admin.course-enquiry.invoice', $enquiry->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit mr-1"></i> {{ __('Edit Invoice') }}
                                    </a>
                                @else
                                    <a href="{{ route('admin.course-enquiry.invoice', $enquiry->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus mr-1"></i> {{ __('Create Final Tax Invoice') }}
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="card-body">
                            @if($enquiry->hasInvoice())
                                {{-- Payment Metrics Strip --}}
                                <div class="row mb-4">
                                    <div class="col-md-3 col-sm-6 mb-2">
                                        <div class="p-3 bg-light rounded border text-center">
                                            <small class="text-muted text-uppercase d-block font-weight-bold">{{ __('Invoice Total') }}</small>
                                            <span class="h4 font-weight-bold text-dark mb-0">₹{{ number_format($paymentSummary['grand_total'], 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-2">
                                        <div class="p-3 bg-light rounded border text-center">
                                            <small class="text-muted text-uppercase d-block font-weight-bold">{{ __('Total Paid') }}</small>
                                            <span class="h4 font-weight-bold text-success mb-0">₹{{ number_format($paymentSummary['total_paid'], 2) }}</span>
                                            @if($paymentSummary['advance_paid'] > 0)
                                                <small class="text-muted d-block">{{ __('(Advance: ₹') }}{{ number_format($paymentSummary['advance_paid'], 2) }})</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-2">
                                        <div class="p-3 bg-light rounded border text-center">
                                            <small class="text-muted text-uppercase d-block font-weight-bold">{{ __('Balance Due') }}</small>
                                            <span class="h4 font-weight-bold {{ $paymentSummary['balance_due'] > 0 ? 'text-danger' : 'text-success' }} mb-0">
                                                ₹{{ number_format($paymentSummary['balance_due'], 2) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-2">
                                        <div class="p-3 bg-light rounded border text-center d-flex flex-column justify-content-center">
                                            <small class="text-muted text-uppercase d-block font-weight-bold mb-1">{{ __('Payment Status') }}</small>
                                            <div>
                                                @if($paymentSummary['payment_status'] === 'paid')
                                                    <span class="badge badge-success px-3 py-2 font-weight-bold"><i class="fas fa-check-circle mr-1"></i> PAID IN FULL</span>
                                                @elseif($paymentSummary['payment_status'] === 'partially_paid')
                                                    <span class="badge badge-warning px-3 py-2 font-weight-bold"><i class="fas fa-adjust mr-1"></i> PARTIALLY PAID</span>
                                                @else
                                                    <span class="badge badge-danger px-3 py-2 font-weight-bold"><i class="fas fa-clock mr-1"></i> UNPAID</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Payment Receipts Ledger --}}
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="font-weight-bold mb-0 text-dark">
                                            <i class="fas fa-money-check-alt text-success mr-2"></i>{{ __('Payment Receipts Ledger') }}
                                            <span class="badge badge-light border ml-1">{{ count($paymentSummary['payments']) }}</span>
                                        </h6>
                                        <button type="button" class="btn btn-sm btn-outline-success font-weight-bold" data-toggle="modal" data-target="#recordPaymentModal">
                                            <i class="fas fa-plus-circle mr-1"></i> {{ __('+ Collect Payment (Advance / Full)') }}
                                        </button>
                                    </div>

                                    @if(!empty($paymentSummary['payments']) && count($paymentSummary['payments']) > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm table-hover mb-0">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th style="width: 40px;" class="text-center">#</th>
                                                        <th>{{ __('Receipt #') }}</th>
                                                        <th>{{ __('Date') }}</th>
                                                        <th>{{ __('Type') }}</th>
                                                        <th>{{ __('Method') }}</th>
                                                        <th>{{ __('UTR / Ref #') }}</th>
                                                        <th class="text-right">{{ __('Amount (₹)') }}</th>
                                                        <th>{{ __('Recorded By') }}</th>
                                                        <th style="width: 70px;" class="text-center">{{ __('Action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($paymentSummary['payments'] as $p)
                                                        <tr>
                                                            <td class="text-center align-middle font-weight-bold">{{ $loop->iteration }}</td>
                                                            <td class="align-middle">
                                                                <code class="font-weight-bold text-dark">{{ $p['receipt_number'] ?? '-' }}</code>
                                                            </td>
                                                            <td class="align-middle">{{ \Carbon\Carbon::parse($p['date'])->format('d M Y') }}</td>
                                                            <td class="align-middle">
                                                                @if($p['type'] === 'advance')
                                                                    <span class="badge badge-warning">{{ __('Advance Payment') }}</span>
                                                                @elseif($p['type'] === 'balance')
                                                                    <span class="badge badge-info">{{ __('Balance Payment') }}</span>
                                                                @else
                                                                    <span class="badge badge-success">{{ __('Full Payment') }}</span>
                                                                @endif
                                                            </td>
                                                            <td class="align-middle text-uppercase font-weight-bold small">{{ $p['method'] ?? 'Bank Transfer' }}</td>
                                                            <td class="align-middle font-family-monospace small">{{ $p['transaction_id'] ?? '—' }}</td>
                                                            <td class="text-right align-middle font-weight-bold text-success" style="font-size: 15px;">
                                                                ₹{{ number_format($p['amount'], 2) }}
                                                            </td>
                                                            <td class="align-middle small text-muted">{{ $p['recorded_by'] ?? 'Admin' }}</td>
                                                            <td class="text-center align-middle">
                                                                <form action="{{ route('admin.course-enquiry.payment.delete', [$enquiry->id, $p['payment_id'] ?? '']) }}"
                                                                      method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this payment record?') }}');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0" title="{{ __('Delete Receipt') }}">
                                                                        <i class="fas fa-trash-alt"></i>
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-light border text-center py-3 mb-0">
                                            <i class="fas fa-receipt text-muted fa-2x mb-2 d-block"></i>
                                            <span class="text-muted">{{ __('No payments recorded yet for this invoice. Click the button below to collect an Advance Payment or Full Settlement.') }}</span>
                                            <div class="mt-2">
                                                <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#recordPaymentModal">
                                                    <i class="fas fa-plus mr-1"></i> {{ __('Record Advance / Full Payment') }}
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Tax Invoice line items preview table --}}
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 40px;">#</th>
                                                <th>{{ __('Item Description') }}</th>
                                                <th>{{ __('Category') }}</th>
                                                <th class="text-center" style="width: 80px;">{{ __('Qty') }}</th>
                                                <th class="text-right" style="width: 120px;">{{ __('Unit Price') }}</th>
                                                <th class="text-right" style="width: 130px;">{{ __('Total') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($invoice['items'] ?? [] as $item)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <span class="font-weight-bold">{{ $item['item'] }}</span>
                                                        @if(!empty($item['sku']))
                                                            <span class="badge badge-light border ml-1 text-muted small"><i class="fas fa-barcode mr-1"></i>{{ $item['sku'] }}</span>
                                                        @endif
                                                    </td>
                                                    <td><span class="badge badge-light border">{{ $item['category'] ?? 'Equipment' }}</span></td>
                                                    <td class="text-center">{{ $item['qty'] }}</td>
                                                    <td class="text-right">₹{{ number_format($item['unit_price'], 2) }}</td>
                                                    <td class="text-right font-weight-bold">₹{{ number_format($item['total'] ?? ($item['qty'] * $item['unit_price']), 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="5" class="text-right text-muted font-weight-bold">{{ __('Subtotal:') }}</td>
                                                <td class="text-right font-weight-bold">₹{{ number_format($invoice['subtotal'] ?? 0, 2) }}</td>
                                            </tr>
                                            @if(!empty($invoice['discount']) && $invoice['discount'] > 0)
                                            <tr>
                                                <td colspan="5" class="text-right text-danger font-weight-bold">{{ __('Discount:') }}</td>
                                                <td class="text-right text-danger font-weight-bold">-₹{{ number_format($invoice['discount'], 2) }}</td>
                                            </tr>
                                            @endif
                                            @if(!empty($invoice['tax_amount']) && $invoice['tax_amount'] > 0)
                                            <tr>
                                                <td colspan="5" class="text-right text-muted font-weight-bold">{{ __('GST (:rate%):', ['rate' => $invoice['tax_percent'] ?? 18]) }}</td>
                                                <td class="text-right font-weight-bold">₹{{ number_format($invoice['tax_amount'], 2) }}</td>
                                            </tr>
                                            @endif
                                            @if(!empty($invoice['shipping']) && $invoice['shipping'] > 0)
                                            <tr>
                                                <td colspan="5" class="text-right text-muted font-weight-bold">{{ __('Freight & Setup:') }}</td>
                                                <td class="text-right font-weight-bold">₹{{ number_format($invoice['shipping'], 2) }}</td>
                                            </tr>
                                            @endif
                                            <tr class="table-primary">
                                                <td colspan="5" class="text-right font-weight-bold text-dark" style="font-size:15px;">{{ __('Grand Total:') }}</td>
                                                <td class="text-right font-weight-bold text-primary" style="font-size:16px;">₹{{ number_format($invoice['grand_total'] ?? 0, 2) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <div class="text-muted mb-3">
                                        <i class="fas fa-receipt fa-3x text-light mb-2 d-block"></i>
                                        {{ __('No Final Tax Invoice created yet. Once created, previous proforma revisions are permanently locked and you can record Advance or Full payment collections.') }}
                                    </div>
                                    <a href="{{ route('admin.course-enquiry.invoice', $enquiry->id) }}" class="btn btn-primary">
                                        <i class="fas fa-plus mr-1"></i> {{ __('Create Final Tax Invoice') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>{{-- /.card tax invoice --}}

                    {{-- ── Record Payment Modal ── --}}
                    @if($enquiry->hasInvoice())
                        <div class="modal fade" id="recordPaymentModal" tabindex="-1" role="dialog" aria-labelledby="recordPaymentModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content shadow-lg border-0">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title font-weight-bold" id="recordPaymentModalLabel">
                                            <i class="fas fa-hand-holding-usd mr-2"></i>{{ __('Record Payment Collection') }}
                                        </h5>
                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('admin.course-enquiry.payment.record', $enquiry->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-body p-4">
                                            <div class="alert alert-light border mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="text-muted">{{ __('Invoice Grand Total:') }}</span>
                                                    <strong>₹{{ number_format($paymentSummary['grand_total'], 2) }}</strong>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="text-muted">{{ __('Already Collected:') }}</span>
                                                    <strong class="text-success">₹{{ number_format($paymentSummary['total_paid'], 2) }}</strong>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center border-top pt-1">
                                                    <span class="font-weight-bold text-dark">{{ __('Current Balance Due:') }}</span>
                                                    <strong class="text-danger font-weight-bold" style="font-size: 16px;">₹{{ number_format($paymentSummary['balance_due'], 2) }}</strong>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="font-weight-bold">{{ __('Payment Type') }} <span class="text-danger">*</span></label>
                                                <select name="type" class="form-control" required id="paymentTypeSelect">
                                                    @if($paymentSummary['total_paid'] <= 0)
                                                        <option value="advance" selected>{{ __('Advance Payment (Partial)') }}</option>
                                                        <option value="full">{{ __('Full Payment (100% Settlement)') }}</option>
                                                    @else
                                                        <option value="balance" selected>{{ __('Balance / Settlement Payment') }}</option>
                                                        <option value="advance">{{ __('Additional Advance Payment') }}</option>
                                                        <option value="full">{{ __('Full Payment') }}</option>
                                                    @endif
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label class="font-weight-bold">{{ __('Amount Collected (₹)') }} <span class="text-danger">*</span></label>
                                                <input type="number" step="0.01" min="1" name="amount" class="form-control font-weight-bold text-success" style="font-size: 17px;"
                                                       value="{{ $paymentSummary['balance_due'] > 0 ? $paymentSummary['balance_due'] : $paymentSummary['grand_total'] }}" required id="paymentAmountInput">
                                                <small class="text-muted">{{ __('Enter the amount received from the client.') }}</small>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label class="font-weight-bold">{{ __('Payment Method') }} <span class="text-danger">*</span></label>
                                                    <select name="method" class="form-control" required>
                                                        <option value="Bank Transfer" selected>{{ __('Bank Transfer (NEFT/RTGS)') }}</option>
                                                        <option value="UPI / QR">{{ __('UPI / QR / VPA') }}</option>
                                                        <option value="Cheque / DD">{{ __('Cheque / DD') }}</option>
                                                        <option value="Net Banking">{{ __('Net Banking') }}</option>
                                                        <option value="Cash">{{ __('Cash') }}</option>
                                                        <option value="Other">{{ __('Other') }}</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label class="font-weight-bold">{{ __('Date Received') }} <span class="text-danger">*</span></label>
                                                    <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label>{{ __('Transaction ID / UTR / Cheque Ref #') }}</label>
                                                <input type="text" name="transaction_id" class="form-control font-weight-bold text-uppercase"
                                                       placeholder="e.g. UTR123456789 or CHQ-99882">
                                            </div>

                                            <div class="form-group mb-0">
                                                <label>{{ __('Remarks / Notes') }}</label>
                                                <textarea name="notes" rows="2" class="form-control" placeholder="e.g. 50% advance received via HDFC corporate transfer"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                                            <button type="submit" class="btn btn-success font-weight-bold">
                                                <i class="fas fa-check-circle mr-1"></i> {{ __('Save & Record Payment') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>{{-- /.col --}}
            </div>{{-- /.row --}}
        </div>{{-- /.section-body --}}
    </section>
</div>
@endsection

@push('js')
<script>
    'use strict';
    var statusDropdown = document.getElementById('statusDropdown');
    if (statusDropdown) {
        statusDropdown.addEventListener('change', function () {
            var id     = this.dataset.id;
            var status = this.value;
            $.ajax({
                type: 'POST',
                url: '/admin/course-enquiry/' + id + '/status',
                data: { _token: '{{ csrf_token() }}', status: status },
                success: function (response) {
                    if (response.success) {
                        toastr.success('{{ __("Status updated successfully") }}');
                    } else {
                        toastr.warning('{{ __("Could not update status") }}');
                    }
                },
                error: function () {
                    toastr.error('{{ __("An error occurred") }}');
                }
            });
        });
    }
</script>
@endpush
