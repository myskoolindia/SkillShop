@extends('admin.master_layout')
@section('title')
    <title>{{ $title }}</title>
@endsection

@section('admin-content')
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex justify-content-between align-items-center">
            <h1><i class="fas fa-file-invoice-dollar mr-2 text-success"></i>{{ $title }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.course-enquiries') }}">{{ __('Manage Enquiries') }}</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.course-enquiry.show', $enquiry->id) }}">#{{ $enquiry->id }}</a></div>
                <div class="breadcrumb-item active">{{ __('Proforma Invoice') }}</div>
            </div>
        </div>

        <div class="section-body">
            {{-- Context Alert --}}
            <div class="alert alert-light border shadow-sm mb-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <div>
                        <strong><i class="fas fa-university text-success mr-1"></i> {{ $enquiry->school ?? $enquiry->name }}</strong>
                        @if($enquiry->name && $enquiry->school) <span class="text-muted">({{ $enquiry->name }})</span> @endif
                        @if($enquiry->course_title) <span class="badge badge-light border ml-2">{{ $enquiry->course_title }}</span> @endif
                        <span class="badge badge-info ml-2">
                            <i class="fas fa-code-branch mr-1"></i>{{ __('Version / Rev') }}: {{ $proforma['version'] ?? 1 }}
                        </span>
                        @if(!empty($proforma['versions']) && count($proforma['versions']) > 0)
                            <span class="badge badge-light border ml-1 text-muted" title="{{ count($proforma['versions']) }} previous revision(s) saved">
                                <i class="fas fa-history mr-1"></i>{{ count($proforma['versions']) }} {{ __('Prior Rev(s)') }}
                            </span>
                        @endif
                    </div>
                    <div>
                        <a href="{{ route('admin.course-enquiry.show', $enquiry->id) }}" class="btn btn-sm btn-outline-secondary mr-1">
                            <i class="fas fa-arrow-left mr-1"></i>{{ __('Back to Enquiry') }}
                        </a>
                        @if($enquiry->hasProformaInvoice())
                            <a href="{{ route('admin.course-enquiry.proforma.view', $enquiry->id) }}" target="_blank" class="btn btn-sm btn-outline-success mr-1">
                                <i class="fas fa-eye mr-1"></i>{{ __('Preview Invoice') }}
                            </a>
                        @endif
                        @if($enquiry->hasInvoice())
                            <a href="{{ route('admin.course-enquiry.invoice', $enquiry->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-receipt mr-1"></i>{{ __('Tax Invoice') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            @if(!empty($isLocked))
                <div class="alert alert-warning border shadow-sm mb-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-lock fa-2x mr-3 text-warning"></i>
                        <div>
                            <h6 class="alert-heading mb-1 font-weight-bold text-dark">{{ __('Proforma Invoice Locked (Read-Only)') }}</h6>
                            <p class="mb-0 small text-dark">
                                {{ __('The Final Tax Invoice has already been issued for this enquiry. Once the Tax Invoice is created, previous proforma revisions are permanently locked.') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('admin.course-enquiry.proforma.save', $enquiry->id) }}" method="POST" id="proformaForm">
                @csrf
                <fieldset {{ !empty($isLocked) ? 'disabled' : '' }}>

                <div class="row">
                    {{-- ── Left: Invoice & Buyer Information ── --}}
                    <div class="col-12 col-lg-8">
                        <div class="card card-success">
                            <div class="card-header">
                                <h4><i class="fas fa-file-invoice mr-2 text-success"></i>{{ __('Proforma Invoice Details') }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 form-group">
                                        <label>{{ __('Proforma Invoice #') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="invoice_number" class="form-control font-weight-bold"
                                               value="{{ old('invoice_number', $proforma['invoice_number']) }}" required>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label>{{ __('Invoice Date') }} <span class="text-danger">*</span></label>
                                        <input type="date" name="date" class="form-control"
                                               value="{{ old('date', $proforma['date']) }}" required>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label>{{ __('Payment Due Date') }}</label>
                                        <input type="date" name="due_date" class="form-control"
                                               value="{{ old('due_date', $proforma['due_date']) }}">
                                    </div>

                                    <div class="col-md-12 form-group">
                                        <label>{{ __('Lab / Course Package') }}</label>
                                        <input type="text" name="course_title" class="form-control"
                                               value="{{ old('course_title', $proforma['course_title'] ?? $enquiry->course_title) }}"
                                               placeholder="e.g. Skillvation Composite Skill Lab – Basic Setup">
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label>{{ __('Bill To (Institution / School)') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="school" class="form-control font-weight-bold"
                                               value="{{ old('school', $proforma['school']) }}" placeholder="School or Organization Name" required>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label>{{ __('Contact Person / Representative') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="client_name" class="form-control"
                                               value="{{ old('client_name', $proforma['client_name']) }}" required>
                                    </div>

                                    <div class="col-md-4 form-group">
                                        <label>{{ __('Designation') }}</label>
                                        <input type="text" name="designation" class="form-control"
                                               value="{{ old('designation', $proforma['designation']) }}"
                                               placeholder="e.g. Principal / Secretary">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label>{{ __('GSTIN / Tax ID') }}</label>
                                        <input type="text" name="gstin" class="form-control text-uppercase font-weight-bold"
                                               value="{{ old('gstin', $proforma['gstin'] ?? '') }}"
                                               placeholder="GSTIN of Buyer (optional)">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label>{{ __('City') }}</label>
                                        <input type="text" name="city" class="form-control"
                                               value="{{ old('city', $proforma['city']) }}">
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label>{{ __('Email') }}</label>
                                        <input type="email" name="email" class="form-control"
                                               value="{{ old('email', $proforma['email']) }}">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label>{{ __('Phone') }}</label>
                                        <input type="text" name="phone" class="form-control"
                                               value="{{ old('phone', $proforma['phone']) }}">
                                    </div>

                                    <div class="col-md-12 form-group mb-0">
                                        <label>{{ __('Billing Address') }}</label>
                                        <textarea name="address" rows="2" class="form-control">{{ old('address', $proforma['address']) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Right: Calculations & Actions ── --}}
                    <div class="col-12 col-lg-4">
                        <div class="card card-dark">
                            <div class="card-header bg-dark text-white d-flex justify-content-between">
                                <h4 class="text-white mb-0"><i class="fas fa-calculator mr-2"></i>{{ __('Billing Summary') }}</h4>
                                <span class="badge badge-success">{{ __('INR (₹)') }}</span>
                            </div>
                            <div class="card-body p-3">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td class="text-muted">{{ __('Subtotal:') }}</td>
                                        <td class="text-right font-weight-bold" id="displaySubtotal">₹0.00</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">{{ __('Discount (₹):') }}</td>
                                        <td class="text-right" style="width: 130px;">
                                            <input type="number" step="0.01" min="0" name="discount" id="inputDiscount"
                                                   class="form-control form-control-sm text-right calc-trigger"
                                                   value="{{ old('discount', $proforma['discount'] ?? 0) }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">{{ __('GST Rate (%):') }}</td>
                                        <td class="text-right" style="width: 130px;">
                                            <div class="input-group input-group-sm">
                                                <input type="number" step="0.5" min="0" max="100" name="tax_percent" id="inputTaxPercent"
                                                       class="form-control form-control-sm text-right calc-trigger"
                                                       value="{{ old('tax_percent', $proforma['tax_percent'] ?? 18) }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">{{ __('GST Amount:') }}</td>
                                        <td class="text-right font-weight-bold" id="displayTaxAmount">₹0.00</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">{{ __('Freight / Setup (₹):') }}</td>
                                        <td class="text-right" style="width: 130px;">
                                            <input type="number" step="0.01" min="0" name="shipping" id="inputShipping"
                                                   class="form-control form-control-sm text-right calc-trigger"
                                                   value="{{ old('shipping', $proforma['shipping'] ?? 0) }}">
                                        </td>
                                    </tr>
                                    <tr class="border-top">
                                        <td class="font-weight-bold text-dark pt-2" style="font-size:16px;">{{ __('Grand Total:') }}</td>
                                        <td class="text-right font-weight-bold text-success pt-2" style="font-size:18px;" id="displayGrandTotal">₹0.00</td>
                                    </tr>
                                </table>

                                <div class="mt-4 pt-3 border-top">
                                    @if(!empty($isLocked))
                                        <div class="alert alert-warning p-2 text-center small mb-2">
                                            <i class="fas fa-lock mr-1"></i>{{ __('Proforma invoice locked because Final Tax Invoice has been issued.') }}
                                        </div>
                                        <a href="{{ route('admin.course-enquiry.proforma.view', $enquiry->id) }}" target="_blank" class="btn btn-success btn-block mb-2 font-weight-bold">
                                            <i class="fas fa-eye mr-1"></i> {{ __('View / Print Proforma') }}
                                        </a>
                                        <a href="{{ route('admin.course-enquiry.invoice', $enquiry->id) }}" class="btn btn-primary btn-block mb-2 font-weight-bold">
                                            <i class="fas fa-receipt mr-1"></i> {{ __('Go to Final Tax Invoice') }}
                                        </a>
                                    @else
                                        <button type="submit" name="submit_action" value="save" class="btn btn-success btn-block mb-2 font-weight-bold">
                                            <i class="fas fa-save mr-1"></i> {{ __('Save Proforma Invoice') }}
                                            @if(!empty($proforma['version']))
                                                <small>(Rev {{ $proforma['version'] + 1 }})</small>
                                            @endif
                                        </button>
                                        <button type="submit" name="submit_action" value="save_and_view" class="btn btn-primary btn-block mb-2 font-weight-bold">
                                            <i class="fas fa-eye mr-1"></i> {{ __('Save & View Invoice') }}
                                        </button>
                                        @if($enquiry->hasProformaInvoice())
                                            <a href="{{ route('admin.course-enquiry.invoice', $enquiry->id) }}" class="btn btn-outline-primary btn-block mb-2 font-weight-bold">
                                                <i class="fas fa-receipt mr-1"></i> {{ __('Generate Final Tax Invoice') }}
                                            </a>
                                        @endif
                                    @endif
                                    <a href="{{ route('admin.course-enquiry.show', $enquiry->id) }}" class="btn btn-outline-secondary btn-block">
                                        {{ __('Back to Enquiry') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Full Width: Line Items ── --}}
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4><i class="fas fa-list-ol text-success mr-2"></i>{{ __('Invoice Line Items') }}</h4>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary mr-2" id="btnSortByCategory" title="{{ __('Sort and group line items by Category') }}">
                                        <i class="fas fa-sort-alpha-down mr-1"></i>{{ __('Sort by Category') }}
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-success mr-2" id="btnBrowseClubShop" data-toggle="modal" data-target="#clubShopModal">
                                        <i class="fas fa-store mr-1"></i>{{ __('ClubShop Catalog') }}
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-info mr-2" id="btnReloadDefaultItems">
                                        <i class="fas fa-sync-alt mr-1"></i>{{ __('Load Package Items') }}
                                    </button>
                                    <button type="button" class="btn btn-sm btn-success" id="btnAddRow">
                                        <i class="fas fa-plus mr-1"></i>{{ __('Add Item') }}
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm mb-0" id="itemsTable">
                                        <thead class="bg-light">
                                            <tr>
                                                <th style="width: 45px;" class="text-center">#</th>
                                                <th style="width: 170px;">{{ __('Category') }}</th>
                                                <th>{{ __('Item Description') }} <span class="text-danger">*</span></th>
                                                <th style="width: 110px;">{{ __('HSN/SAC') }}</th>
                                                <th style="width: 90px;" class="text-center">{{ __('Qty') }}</th>
                                                <th style="width: 140px;" class="text-right">{{ __('Unit Price (₹)') }}</th>
                                                <th style="width: 150px;" class="text-right">{{ __('Total (₹)') }}</th>
                                                <th style="width: 50px;" class="text-center"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemsBody">
                                            @php
                                                $rawItems = $proforma['items'] ?? [];
                                                $items = collect($rawItems)->sortBy(function($it) {
                                                    return [strtoupper($it['category'] ?? 'General'), strtoupper($it['item'] ?? '')];
                                                })->values()->all();
                                            @endphp
                                            @forelse($items as $idx => $row)
                                                <tr class="item-row">
                                                    <td class="text-center row-sno align-middle">{{ $loop->iteration }}</td>
                                                    <td>
                                                        <input type="text" name="items[{{ $idx }}][category]" class="form-control form-control-sm item-category"
                                                               value="{{ $row['category'] ?? 'General' }}" placeholder="Category">
                                                    </td>
                                                    <td>
                                                        <input type="hidden" name="items[{{ $idx }}][product_id]" class="item-product-id" value="{{ $row['product_id'] ?? '' }}">
                                                        <input type="hidden" name="items[{{ $idx }}][sku]" class="item-sku" value="{{ $row['sku'] ?? '' }}">
                                                        <div class="position-relative">
                                                            <div class="input-group input-group-sm">
                                                                <input type="text" name="items[{{ $idx }}][item]" class="form-control form-control-sm font-weight-bold item-name-input"
                                                                       value="{{ $row['item'] ?? '' }}" placeholder="Search ClubShop product or enter description" autocomplete="off" required>
                                                                <div class="input-group-append">
                                                                    <button type="button" class="btn btn-outline-secondary btn-row-search-clubshop" title="{{ __('Pick from ClubShop Catalog') }}">
                                                                        <i class="fas fa-search"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="clubshop-dropdown list-group shadow position-absolute w-100" style="display:none; z-index:1060; max-height:220px; overflow-y:auto; font-size:12px;"></div>
                                                            <div class="item-sku-display text-muted small mt-1" style="{{ empty($row['sku']) ? 'display:none;' : '' }}">
                                                                <i class="fas fa-barcode text-primary mr-1"></i>ClubShop SKU: <span class="sku-value font-weight-bold text-dark">{{ $row['sku'] ?? '' }}</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="items[{{ $idx }}][hsn]" class="form-control form-control-sm item-hsn"
                                                               value="{{ $row['hsn'] ?? '9023' }}" placeholder="HSN/SAC">
                                                    </td>
                                                    <td>
                                                        <input type="number" min="1" step="1" name="items[{{ $idx }}][qty]"
                                                               class="form-control form-control-sm text-center item-qty calc-trigger"
                                                               value="{{ $row['qty'] ?? 1 }}" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" min="0" step="0.01" name="items[{{ $idx }}][unit_price]"
                                                               class="form-control form-control-sm text-right item-price calc-trigger"
                                                               value="{{ $row['unit_price'] ?? 0 }}" required>
                                                    </td>
                                                    <td class="text-right align-middle font-weight-bold item-row-total">
                                                        ₹{{ number_format(($row['qty'] ?? 1) * ($row['unit_price'] ?? 0), 2) }}
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <button type="button" class="btn btn-sm btn-link text-danger p-0 btn-remove-row" title="{{ __('Remove') }}">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr class="item-row">
                                                    <td class="text-center row-sno align-middle">1</td>
                                                    <td>
                                                        <input type="text" name="items[0][category]" class="form-control form-control-sm item-category"
                                                               value="Equipment" placeholder="Category">
                                                    </td>
                                                    <td>
                                                        <input type="hidden" name="items[0][product_id]" class="item-product-id" value="">
                                                        <input type="hidden" name="items[0][sku]" class="item-sku" value="">
                                                        <div class="position-relative">
                                                            <div class="input-group input-group-sm">
                                                                <input type="text" name="items[0][item]" class="form-control form-control-sm font-weight-bold item-name-input"
                                                                       value="{{ $enquiry->course_title ?? 'Lab Equipment Kit' }}" placeholder="Search ClubShop product or enter description" autocomplete="off" required>
                                                                <div class="input-group-append">
                                                                    <button type="button" class="btn btn-outline-secondary btn-row-search-clubshop" title="{{ __('Pick from ClubShop Catalog') }}">
                                                                        <i class="fas fa-search"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="clubshop-dropdown list-group shadow position-absolute w-100" style="display:none; z-index:1060; max-height:220px; overflow-y:auto; font-size:12px;"></div>
                                                            <div class="item-sku-display text-muted small mt-1" style="display:none;">
                                                                <i class="fas fa-barcode text-primary mr-1"></i>ClubShop SKU: <span class="sku-value font-weight-bold text-dark"></span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="items[0][hsn]" class="form-control form-control-sm item-hsn"
                                                               value="9023" placeholder="HSN/SAC">
                                                    </td>
                                                    <td>
                                                        <input type="number" min="1" step="1" name="items[0][qty]"
                                                               class="form-control form-control-sm text-center item-qty calc-trigger"
                                                               value="1" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" min="0" step="0.01" name="items[0][unit_price]"
                                                               class="form-control form-control-sm text-right item-price calc-trigger"
                                                               value="0" required>
                                                    </td>
                                                    <td class="text-right align-middle font-weight-bold item-row-total">
                                                        ₹0.00
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <button type="button" class="btn btn-sm btn-link text-danger p-0 btn-remove-row" title="{{ __('Remove') }}">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Bank Details for Remittance ── --}}
                    <div class="col-12">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h4><i class="fas fa-university text-primary mr-2"></i>{{ __('Bank Remittance / Payment Details') }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 form-group">
                                        <label>{{ __('Beneficiary / Account Name') }}</label>
                                        <input type="text" name="account_name" class="form-control"
                                               value="{{ old('account_name', $proforma['account_name'] ?? 'Skillvation EdTech Pvt Ltd') }}">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label>{{ __('Bank Name') }}</label>
                                        <input type="text" name="bank_name" class="form-control"
                                               value="{{ old('bank_name', $proforma['bank_name'] ?? 'HDFC Bank') }}">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label>{{ __('Account Number') }}</label>
                                        <input type="text" name="account_number" class="form-control font-weight-bold"
                                               value="{{ old('account_number', $proforma['account_number'] ?? '50200012345678') }}">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label>{{ __('IFSC Code') }}</label>
                                        <input type="text" name="ifsc_code" class="form-control text-uppercase font-weight-bold"
                                               value="{{ old('ifsc_code', $proforma['ifsc_code'] ?? 'HDFC0001234') }}">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label>{{ __('Branch') }}</label>
                                        <input type="text" name="branch" class="form-control"
                                               value="{{ old('branch', $proforma['branch'] ?? 'Bengaluru Main Branch') }}">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label>{{ __('UPI ID / VPA') }}</label>
                                        <input type="text" name="upi_id" class="form-control"
                                               value="{{ old('upi_id', $proforma['upi_id'] ?? 'skillvation@hdfcbank') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Payment Terms & Notes ── --}}
                    <div class="col-12 col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4><i class="fas fa-file-contract text-success mr-2"></i>{{ __('Payment Terms & Instructions') }}</h4>
                            </div>
                            <div class="card-body">
                                <textarea name="payment_terms" rows="4" class="form-control" style="font-size:13px;">{{ old('payment_terms', $proforma['payment_terms'] ?? '') }}</textarea>
                                <small class="text-muted">{{ __('Payment schedule and instructions for buyer finance department.') }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4><i class="fas fa-sticky-note text-success mr-2"></i>{{ __('Legal Notes & Declaration') }}</h4>
                            </div>
                            <div class="card-body">
                                <textarea name="notes" rows="4" class="form-control" style="font-size:13px;">{{ old('notes', $proforma['notes'] ?? '') }}</textarea>
                                <small class="text-muted">{{ __('Jurisdiction, dispatch conditions, or declaration.') }}</small>
                            </div>
                        </div>
                    </div>

                </div>{{-- /.row --}}
                </fieldset>
            </form>
        </div>
    </section>

    {{-- ── ClubShop Catalog Modal ── --}}
    <div class="modal fade" id="clubShopModal" tabindex="-1" role="dialog" aria-labelledby="clubShopModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title font-weight-bold" id="clubShopModalLabel">
                        <i class="fas fa-store mr-2"></i>{{ __('ClubShop Database Product Catalog') }}
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-3">
                    <div class="row mb-3">
                        <div class="col-md-5">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                </div>
                                <input type="text" id="modalClubShopSearch" class="form-control" placeholder="Search by product name or SKU...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select id="modalClubShopCategory" class="form-control">
                                <option value="">{{ __('All Categories') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3 text-right">
                            <button type="button" class="btn btn-outline-secondary" id="btnModalResetFilters">
                                <i class="fas fa-undo mr-1"></i>{{ __('Reset') }}
                            </button>
                            <button type="button" class="btn btn-success" id="btnModalAddSelected">
                                <i class="fas fa-plus-circle mr-1"></i>{{ __('Add Selected') }} (<span id="selectedCount">0</span>)
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive" style="max-height: 480px;">
                        <table class="table table-hover table-sm table-striped align-middle" id="modalProductsTable">
                            <thead class="thead-light sticky-top">
                                <tr>
                                    <th style="width: 40px;" class="text-center">
                                        <input type="checkbox" id="checkAllModalProducts">
                                    </th>
                                    <th style="width: 140px;">{{ __('SKU') }}</th>
                                    <th>{{ __('Product Title') }}</th>
                                    <th style="width: 180px;">{{ __('Category') }}</th>
                                    <th style="width: 140px;" class="text-right">{{ __('Price (₹)') }}</th>
                                    <th style="width: 100px;" class="text-center">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody id="modalProductsBody">
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fas fa-spinner fa-spin mr-1"></i> {{ __('Loading ClubShop catalog...') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light d-flex justify-content-between">
                    <div class="text-muted small">
                        <i class="fas fa-database text-success mr-1"></i> Connected to database: <code>club-shop</code>
                    </div>
                    <div>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('js')
<script>
    'use strict';
    $(document).ready(function () {
        var itemsBody = $('#itemsBody');
        var targetRowForModal = null;
        var clubShopCatalog = [];
        var clubShopCategories = [];
        var catalogLoaded = false;

        function createRowHtml(idx, itemData) {
            itemData = itemData || {};
            var cat = itemData.category_name || itemData.category || 'General';
            var name = itemData.title || itemData.item || '';
            var sku = itemData.sku || '';
            var prodId = itemData.product_id || itemData.id || '';
            var hsn = itemData.hsn || '9023';
            var qty = itemData.qty || 1;
            var price = itemData.unit_price !== undefined ? itemData.unit_price : 0;
            var total = qty * price;

            return `
                <tr class="item-row">
                    <td class="text-center row-sno align-middle">${idx + 1}</td>
                    <td>
                        <input type="text" name="items[${idx}][category]" class="form-control form-control-sm item-category" value="${cat}" placeholder="Category">
                    </td>
                    <td>
                        <input type="hidden" name="items[${idx}][product_id]" class="item-product-id" value="${prodId}">
                        <input type="hidden" name="items[${idx}][sku]" class="item-sku" value="${sku}">
                        <div class="position-relative">
                            <div class="input-group input-group-sm">
                                <input type="text" name="items[${idx}][item]" class="form-control form-control-sm font-weight-bold item-name-input"
                                       value="${name}" placeholder="Search ClubShop product or enter description" autocomplete="off" required>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary btn-row-search-clubshop" title="Pick from ClubShop Catalog">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="clubshop-dropdown list-group shadow position-absolute w-100" style="display:none; z-index:1060; max-height:220px; overflow-y:auto; font-size:12px;"></div>
                            <div class="item-sku-display text-muted small mt-1" style="${sku ? '' : 'display:none;'}">
                                <i class="fas fa-barcode text-primary mr-1"></i>ClubShop SKU: <span class="sku-value font-weight-bold text-dark">${sku}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <input type="text" name="items[${idx}][hsn]" class="form-control form-control-sm item-hsn" value="${hsn}" placeholder="HSN/SAC">
                    </td>
                    <td>
                        <input type="number" min="1" step="1" name="items[${idx}][qty]" class="form-control form-control-sm text-center item-qty calc-trigger" value="${qty}" required>
                    </td>
                    <td>
                        <input type="number" min="0" step="0.01" name="items[${idx}][unit_price]" class="form-control form-control-sm text-right item-price calc-trigger" value="${price}" required>
                    </td>
                    <td class="text-right align-middle font-weight-bold item-row-total">₹${total.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                    <td class="text-center align-middle">
                        <button type="button" class="btn btn-sm btn-link text-danger p-0 btn-remove-row" title="{{ __('Remove') }}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            `;
        }

        function reindexAndRecalculate() {
            var subtotal = 0;
            var rows = itemsBody.find('tr.item-row');

            rows.each(function (idx) {
                var row = $(this);
                row.find('.row-sno').text(idx + 1);

                row.find('input').each(function () {
                    var name = $(this).attr('name');
                    if (name) {
                        $(this).attr('name', name.replace(/items\[\d+\]/, 'items[' + idx + ']'));
                    }
                });

                var qty = parseFloat(row.find('.item-qty').val()) || 0;
                var price = parseFloat(row.find('.item-price').val()) || 0;
                var rowTotal = qty * price;
                subtotal += rowTotal;

                row.find('.item-row-total').text('₹' + rowTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            });

            var discount = parseFloat($('#inputDiscount').val()) || 0;
            var taxPercent = parseFloat($('#inputTaxPercent').val()) || 0;
            var shipping = parseFloat($('#inputShipping').val()) || 0;

            var netSubtotal = Math.max(0, subtotal - discount);
            var taxAmount = netSubtotal * (taxPercent / 100);
            var grandTotal = netSubtotal + taxAmount + shipping;

            $('#displaySubtotal').text('₹' + subtotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $('#displayTaxAmount').text('₹' + taxAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $('#displayGrandTotal').text('₹' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        }

        function populateRowWithProduct(row, product) {
            row.find('.item-product-id').val(product.product_id || product.id || '');
            row.find('.item-sku').val(product.sku || '');
            row.find('.item-name-input').val(product.title || product.item || '');
            row.find('.item-category').val(product.category_name || product.category || 'Equipment');
            row.find('.item-price').val(product.unit_price !== undefined ? product.unit_price : (product.price || 0));
            if (product.hsn) {
                row.find('.item-hsn').val(product.hsn);
            }

            if (product.sku) {
                row.find('.sku-value').text(product.sku);
                row.find('.item-sku-display').show();
            } else {
                row.find('.item-sku-display').hide();
            }

            row.find('.clubshop-dropdown').hide().empty();
            reindexAndRecalculate();
        }

        // Calculation trigger
        $(document).on('input change', '.calc-trigger', function () {
            reindexAndRecalculate();
        });

        // Add Row
        $('#btnAddRow').on('click', function () {
            var idx = itemsBody.find('tr.item-row').length;
            itemsBody.append(createRowHtml(idx));
            reindexAndRecalculate();
        });

        // Remove Row
        $(document).on('click', '.btn-remove-row', function () {
            if (itemsBody.find('tr.item-row').length > 1) {
                $(this).closest('tr.item-row').remove();
                reindexAndRecalculate();
            } else {
                toastr.warning('{{ __("At least one item row is required.") }}');
            }
        });

        // Autocomplete on item name input
        var searchTimeout = null;
        $(document).on('input', '.item-name-input', function () {
            var input = $(this);
            var query = input.val().trim();
            var dropdown = input.closest('td').find('.clubshop-dropdown');

            clearTimeout(searchTimeout);
            if (query.length < 2) {
                dropdown.hide().empty();
                return;
            }

            searchTimeout = setTimeout(function () {
                $.ajax({
                    url: '{{ route("admin.clubshop.products.search") }}',
                    type: 'GET',
                    data: { q: query },
                    success: function (res) {
                        if (res.success && res.data && res.data.length) {
                            var html = '';
                            res.data.forEach(function (prod) {
                                html += `
                                    <a href="javascript:void(0)" class="list-group-item list-group-item-action p-2 clubshop-item-pick"
                                       data-product='${JSON.stringify(prod).replace(/'/g, "&#39;")}'>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <strong>${prod.title}</strong>
                                            <span class="badge badge-success">₹${(prod.unit_price || 0).toLocaleString('en-IN')}</span>
                                        </div>
                                        <div class="small text-muted mt-1">
                                            <span class="badge badge-light border mr-1">${prod.category_name || 'General'}</span>
                                            ${prod.sku ? '<i class="fas fa-barcode ml-1 mr-1"></i>SKU: ' + prod.sku : ''}
                                        </div>
                                    </a>
                                `;
                            });
                            dropdown.html(html).show();
                        } else {
                            dropdown.html('<div class="p-2 text-muted small text-center">{{ __("No matching ClubShop products") }}</div>').show();
                        }
                    }
                });
            }, 250);
        });

        // Click on autocomplete item
        $(document).on('click', '.clubshop-item-pick', function (e) {
            e.preventDefault();
            var prodData = $(this).data('product');
            var row = $(this).closest('tr.item-row');
            populateRowWithProduct(row, prodData);
        });

        // Close dropdown when clicking outside
        $(document).on('click', function (e) {
            if (!$(e.target).closest('.item-name-input, .clubshop-dropdown').length) {
                $('.clubshop-dropdown').hide();
            }
        });

        // Row search icon clicked
        $(document).on('click', '.btn-row-search-clubshop', function () {
            targetRowForModal = $(this).closest('tr.item-row');
            $('#clubShopModal').modal('show');
        });

        $('#btnBrowseClubShop').on('click', function () {
            targetRowForModal = null; // Adding new items mode
        });

        // Load ClubShop catalog into modal
        function loadClubShopCatalog() {
            var tbody = $('#modalProductsBody');
            tbody.html('<tr><td colspan="6" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-1"></i> {{ __("Loading ClubShop catalog...") }}</td></tr>');

            $.ajax({
                url: '{{ route("admin.clubshop.products.catalog") }}',
                type: 'GET',
                success: function (res) {
                    if (res.success) {
                        clubShopCatalog = res.products || [];
                        clubShopCategories = res.categories || [];
                        catalogLoaded = true;

                        // Populate category dropdown
                        var catSelect = $('#modalClubShopCategory');
                        catSelect.empty().append('<option value="">{{ __("All Categories") }}</option>');
                        clubShopCategories.forEach(function (c) {
                            catSelect.append(`<option value="${c.id}">${c.name}</option>`);
                        });

                        renderModalProducts();
                    } else {
                        tbody.html('<tr><td colspan="6" class="text-center py-4 text-danger">{{ __("Failed to load catalog.") }}</td></tr>');
                    }
                },
                error: function () {
                    tbody.html('<tr><td colspan="6" class="text-center py-4 text-danger">{{ __("Unable to connect to clubshop database.") }}</td></tr>');
                }
            });
        }

        function renderModalProducts() {
            var search = ($('#modalClubShopSearch').val() || '').toLowerCase().trim();
            var catId = $('#modalClubShopCategory').val();
            var tbody = $('#modalProductsBody');

            var filtered = clubShopCatalog.filter(function (p) {
                var matchCat = !catId || p.category_id == catId;
                var matchSearch = !search ||
                    (p.title && p.title.toLowerCase().indexOf(search) !== -1) ||
                    (p.sku && p.sku.toLowerCase().indexOf(search) !== -1) ||
                    (p.category_name && p.category_name.toLowerCase().indexOf(search) !== -1);
                return matchCat && matchSearch;
            });

            // Sort filtered catalog items grouped by category, then title
            filtered.sort(function (a, b) {
                var catA = (a.category_name || 'General').toLowerCase();
                var catB = (b.category_name || 'General').toLowerCase();
                var cmp = catA.localeCompare(catB);
                if (cmp !== 0) return cmp;
                return (a.title || '').localeCompare(b.title || '');
            });

            if (!filtered.length) {
                tbody.html('<tr><td colspan="6" class="text-center py-4 text-muted">{{ __("No products found matching filters.") }}</td></tr>');
                updateSelectedCount();
                return;
            }

            var html = '';
            var currentModalCat = null;
            filtered.forEach(function (prod) {
                var catName = prod.category_name || 'General';
                if (catName !== currentModalCat) {
                    currentModalCat = catName;
                    html += `
                        <tr class="table-light" style="background:#f1f5f9; border-top:2px solid #cbd5e1; border-bottom:1px solid #cbd5e1;">
                            <td colspan="6" class="py-2 px-3 font-weight-bold text-success" style="font-size:12px; text-transform:uppercase; letter-spacing:0.5px;">
                                <i class="fas fa-folder-open mr-2"></i> ${currentModalCat}
                            </td>
                        </tr>
                    `;
                }
                var jsonStr = JSON.stringify(prod).replace(/'/g, "&#39;");
                html += `
                    <tr>
                        <td class="text-center align-middle">
                            <input type="checkbox" class="modal-prod-check" data-product='${jsonStr}'>
                        </td>
                        <td class="align-middle">
                            <code class="text-dark font-weight-bold">${prod.sku || '-'}</code>
                        </td>
                        <td class="align-middle font-weight-bold">
                            ${prod.title}
                        </td>
                        <td class="align-middle">
                            <span class="badge badge-light border">${catName}</span>
                        </td>
                        <td class="text-right align-middle font-weight-bold text-success">
                            ₹${(prod.unit_price || 0).toLocaleString('en-IN', {minimumFractionDigits: 2})}
                        </td>
                        <td class="text-center align-middle">
                            <button type="button" class="btn btn-sm btn-success btn-modal-add-single" data-product='${jsonStr}'>
                                <i class="fas fa-plus mr-1"></i>{{ __('Add') }}
                            </button>
                        </td>
                    </tr>
                `;
            });

            tbody.html(html);
            $('#checkAllModalProducts').prop('checked', false);
            updateSelectedCount();
        }

        function updateSelectedCount() {
            var count = $('#modalProductsBody .modal-prod-check:checked').length;
            $('#selectedCount').text(count);
        }

        $('#clubShopModal').on('show.bs.modal', function () {
            if (!catalogLoaded) {
                loadClubShopCatalog();
            } else {
                renderModalProducts();
            }
        });

        $('#modalClubShopSearch, #modalClubShopCategory').on('input change', function () {
            renderModalProducts();
        });

        $('#btnModalResetFilters').on('click', function () {
            $('#modalClubShopSearch').val('');
            $('#modalClubShopCategory').val('');
            renderModalProducts();
        });

        $('#checkAllModalProducts').on('change', function () {
            var checked = $(this).is(':checked');
            $('#modalProductsBody .modal-prod-check').prop('checked', checked);
            updateSelectedCount();
        });

        $(document).on('change', '.modal-prod-check', function () {
            updateSelectedCount();
        });

        // Add single product from modal
        $(document).on('click', '.btn-modal-add-single', function () {
            var prod = $(this).data('product');
            if (targetRowForModal && targetRowForModal.length) {
                populateRowWithProduct(targetRowForModal, prod);
                toastr.success(`Selected "${prod.title}" for row.`);
            } else {
                var idx = itemsBody.find('tr.item-row').length;
                var firstRow = itemsBody.find('tr.item-row:first');
                if (itemsBody.find('tr.item-row').length === 1 && !firstRow.find('.item-name-input').val().trim()) {
                    populateRowWithProduct(firstRow, prod);
                } else {
                    itemsBody.append(createRowHtml(idx, prod));
                    reindexAndRecalculate();
                }
                toastr.success(`Added "${prod.title}" to invoice.`);
            }
            $('#clubShopModal').modal('hide');
        });

        // Add selected products from modal
        $('#btnModalAddSelected').on('click', function () {
            var checked = $('#modalProductsBody .modal-prod-check:checked');
            if (!checked.length) {
                toastr.warning('{{ __("Please select at least one product.") }}');
                return;
            }

            var firstRow = itemsBody.find('tr.item-row:first');
            var firstRowEmpty = (itemsBody.find('tr.item-row').length === 1 && !firstRow.find('.item-name-input').val().trim());

            checked.each(function (i) {
                var prod = $(this).data('product');
                if (i === 0 && targetRowForModal && targetRowForModal.length) {
                    populateRowWithProduct(targetRowForModal, prod);
                } else if (i === 0 && firstRowEmpty) {
                    populateRowWithProduct(firstRow, prod);
                } else {
                    var idx = itemsBody.find('tr.item-row').length;
                    itemsBody.append(createRowHtml(idx, prod));
                }
            });

            sortItemsTableByCategory();
            toastr.success(`Added ${checked.length} product(s) from ClubShop.`);
            $('#clubShopModal').modal('hide');
        });

        // Function: Sort items table rows grouped by Category, then Item Name
        function sortItemsTableByCategory() {
            var rows = itemsBody.find('tr.item-row').get();
            if (!rows.length) return;

            rows.sort(function (a, b) {
                var catA = ($(a).find('.item-category').val() || 'General').toLowerCase().trim();
                var catB = ($(b).find('.item-category').val() || 'General').toLowerCase().trim();
                var cmp = catA.localeCompare(catB);
                if (cmp !== 0) return cmp;
                var itemA = ($(a).find('.item-name-input').val() || '').toLowerCase().trim();
                var itemB = ($(b).find('.item-name-input').val() || '').toLowerCase().trim();
                return itemA.localeCompare(itemB);
            });

            $.each(rows, function (idx, row) {
                itemsBody.append(row);
            });
            reindexAndRecalculate();
        }

        $('#btnSortByCategory').on('click', function () {
            sortItemsTableByCategory();
            toastr.info('{{ __("Line items grouped and sorted by Category.") }}');
        });

        // Fetch / Reload default items (from ClubShop bundles)
        $('#btnReloadDefaultItems').on('click', function () {
            var btn = $(this);
            var originalHtml = btn.html();
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Loading...');

            $.ajax({
                url: '{{ route("admin.course-enquiry.default-items", $enquiry->id) }}',
                type: 'GET',
                success: function (res) {
                    if (res.success && res.items && res.items.length) {
                        itemsBody.empty();
                        res.items.forEach(function (it, idx) {
                            itemsBody.append(createRowHtml(idx, it));
                        });
                        sortItemsTableByCategory();
                        toastr.success('{{ __("Loaded package components directly from ClubShop!") }}');
                    } else {
                        toastr.info(res.message || '{{ __("No package components found.") }}');
                    }
                },
                error: function () {
                    toastr.error('{{ __("Failed to fetch package items.") }}');
                },
                complete: function () {
                    btn.prop('disabled', false).html(originalHtml);
                }
            });
        });

        // Initial calculation
        reindexAndRecalculate();
    });
</script>
@endpush
