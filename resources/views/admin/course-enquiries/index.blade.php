@extends('admin.master_layout')
@section('title')
    <title>{{ __('Manage Enquiries') }}</title>
@endsection
@section('admin-content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>{{ __('Manage Enquiries') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active">
                    <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                </div>
                <div class="breadcrumb-item">{{ __('Manage Enquiries') }}</div>
            </div>
        </div>

        <div class="section-body">
            <div class="mt-4 row">

                {{-- Filters --}}
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('admin.course-enquiries') }}" method="GET"
                                  onchange="$(this).trigger('submit')" class="form_padding">
                                <div class="row">

                                    <div class="col-md-3 form-group">
                                        <input type="text" name="keyword"
                                               value="{{ request('keyword') }}"
                                               class="form-control"
                                               placeholder="{{ __('Search name, phone, email, school…') }}">
                                    </div>

                                    <div class="col-md-3 form-group">
                                        <select name="source" class="form-control">
                                            <option value="">{{ __('All Sources') }}</option>
                                            @foreach($sources as $src)
                                                <option value="{{ $src }}" {{ request('source') == $src ? 'selected' : '' }}>
                                                    {{ ucwords(str_replace(['-','_'], ' ', $src)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3 form-group">
                                        <select name="status" class="form-control">
                                            <option value="">{{ __('Select Status') }}</option>
                                            <option value="new"       {{ request('status') == 'new'       ? 'selected' : '' }}>{{ __('New') }}</option>
                                            <option value="read"      {{ request('status') == 'read'      ? 'selected' : '' }}>{{ __('Read') }}</option>
                                            <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>{{ __('Contacted') }}</option>
                                            <option value="closed"    {{ request('status') == 'closed'    ? 'selected' : '' }}>{{ __('Closed') }}</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 form-group">
                                        <select name="par-page" class="form-control">
                                            <option value="">{{ __('Per Page') }}</option>
                                            <option value="10"  {{ request('par-page') == '10'  ? 'selected' : '' }}>{{ __('10') }}</option>
                                            <option value="25"  {{ request('par-page') == '25'  ? 'selected' : '' }}>{{ __('25') }}</option>
                                            <option value="50"  {{ request('par-page') == '50'  ? 'selected' : '' }}>{{ __('50') }}</option>
                                            <option value="100" {{ request('par-page') == '100' ? 'selected' : '' }}>{{ __('100') }}</option>
                                        </select>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Table --}}
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>{{ __('Enquiry List') }}
                                <span class="badge badge-primary ml-1">{{ $enquiries->total() }}</span>
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive max-h-400">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>{{ __('SN') }}</th>
                                            <th>{{ __('Name / School') }}</th>
                                            <th>{{ __('Phone') }}</th>
                                            <th>{{ __('Email') }}</th>
                                            <th>{{ __('City') }}</th>
                                            <th>{{ __('Source') }}</th>
                                            <th style="min-width: 165px;">{{ __('Action') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Date') }}</th>
                                            <th class="text-center">{{ __('Manage') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($enquiries as $enq)
                                            @php
                                                $qData = $enq->quotation_data;
                                                $pData = $enq->proforma_invoice_data;
                                                $iData = $enq->invoice_data;
                                                $paySummary = $enq->invoice_payment_summary;
                                            @endphp
                                            <tr>
                                                <td>{{ $loop->iteration + ($enquiries->currentPage() - 1) * $enquiries->perPage() }}</td>

                                                <td>
                                                    <strong>{{ $enq->name }}</strong>
                                                    @if($enq->school)
                                                        <br><small class="text-muted"><i class="fas fa-university mr-1"></i>{{ $enq->school }}</small>
                                                    @endif
                                                </td>

                                                <td>{{ $enq->phone ?? '—' }}</td>

                                                <td>{{ $enq->email }}</td>

                                                <td>{{ $enq->city ?? '—' }}</td>

                                                <td>
                                                    @if($enq->source)
                                                        <span class="badge badge-light border">
                                                            {{ ucwords(str_replace(['-','_'], ' ', $enq->source)) }}
                                                        </span>
                                                    @else
                                                        —
                                                    @endif
                                                </td>

                                                {{-- Action (Documents & Payment) Column --}}
                                                <td>
                                                    <div class="d-flex flex-column" style="gap: 4px; min-width: 155px;">
                                                        {{-- 1. Quotation badge --}}
                                                        @if(!empty($qData['items']))
                                                            <a href="{{ route('admin.course-enquiry.quotation.view', $enq->id) }}"
                                                               target="_blank"
                                                               class="badge badge-primary text-white py-1 px-2 d-flex justify-content-between align-items-center"
                                                               title="{{ __('View Quotation') }}: {{ $qData['quotation_number'] ?? '' }}">
                                                                <span><i class="fas fa-file-invoice mr-1"></i> {{ __('Quote') }}</span>
                                                                @if(!empty($qData['grand_total']))
                                                                    <span class="font-weight-normal ml-1">₹{{ number_format($qData['grand_total']) }}</span>
                                                                @endif
                                                            </a>
                                                        @else
                                                            <a href="{{ route('admin.course-enquiry.quotation', $enq->id) }}"
                                                               class="badge badge-light border text-muted py-1 px-2 text-left"
                                                               title="{{ __('Create Quotation') }}">
                                                                <i class="fas fa-plus mr-1"></i> {{ __('Quote') }}
                                                            </a>
                                                        @endif

                                                        {{-- 2. Proforma badge --}}
                                                        @if(!empty($pData['items']) && $enq->hasProformaInvoice())
                                                            <a href="{{ route('admin.course-enquiry.proforma.view', $enq->id) }}"
                                                               target="_blank"
                                                               class="badge badge-success text-white py-1 px-2 d-flex justify-content-between align-items-center"
                                                               title="{{ __('View Proforma Invoice') }}: {{ $pData['invoice_number'] ?? '' }} (Rev {{ $enq->proforma_version }})">
                                                                <span><i class="fas fa-file-invoice-dollar mr-1"></i> {{ __('Proforma') }}</span>
                                                                @if(!empty($pData['grand_total']))
                                                                    <span class="font-weight-normal ml-1">₹{{ number_format($pData['grand_total']) }}</span>
                                                                @endif
                                                            </a>
                                                        @else
                                                            <a href="{{ route('admin.course-enquiry.proforma', $enq->id) }}"
                                                               class="badge badge-light border text-muted py-1 px-2 text-left"
                                                               title="{{ __('Create Proforma Invoice') }}">
                                                                <i class="fas fa-plus mr-1"></i> {{ __('Proforma') }}
                                                            </a>
                                                        @endif

                                                        {{-- 3. Tax Invoice badge --}}
                                                        @if(!empty($iData['items']) && $enq->hasInvoice())
                                                            <a href="{{ route('admin.course-enquiry.invoice.view', $enq->id) }}"
                                                               target="_blank"
                                                               class="badge badge-info text-white py-1 px-2 d-flex justify-content-between align-items-center"
                                                               title="{{ __('View Tax Invoice') }}: {{ $iData['invoice_number'] ?? '' }}">
                                                                <span><i class="fas fa-receipt mr-1"></i> {{ __('Invoice') }}</span>
                                                                @if(!empty($iData['grand_total']))
                                                                    <span class="font-weight-normal ml-1">₹{{ number_format($iData['grand_total']) }}</span>
                                                                @endif
                                                            </a>
                                                        @else
                                                            <a href="{{ route('admin.course-enquiry.invoice', $enq->id) }}"
                                                               class="badge badge-light border text-muted py-1 px-2 text-left"
                                                               title="{{ __('Create Tax Invoice') }}">
                                                                <i class="fas fa-plus mr-1"></i> {{ __('Invoice') }}
                                                            </a>
                                                        @endif

                                                        {{-- 4. Payment badge --}}
                                                        @if($enq->hasInvoice() && ($paySummary['total_paid'] ?? 0) > 0)
                                                            @if(($paySummary['payment_status'] ?? '') === 'paid')
                                                                <a href="{{ route('admin.course-enquiry.show', $enq->id) }}#payment-card"
                                                                   class="badge badge-success text-white py-1 px-2 d-flex justify-content-between align-items-center"
                                                                   title="{{ __('Paid in Full: ₹') }}{{ number_format($paySummary['total_paid'], 2) }}">
                                                                    <span><i class="fas fa-check-circle mr-1"></i> {{ __('Paid') }}</span>
                                                                    <span class="font-weight-normal ml-1">₹{{ number_format($paySummary['total_paid']) }}</span>
                                                                </a>
                                                            @else
                                                                <a href="{{ route('admin.course-enquiry.show', $enq->id) }}#payment-card"
                                                                   class="badge badge-warning text-dark py-1 px-2 d-flex justify-content-between align-items-center"
                                                                   title="{{ __('Advance: ₹') }}{{ number_format($paySummary['advance_paid'] > 0 ? $paySummary['advance_paid'] : $paySummary['total_paid']) }} | Due: ₹{{ number_format($paySummary['balance_due']) }}">
                                                                    <span><i class="fas fa-adjust mr-1"></i> {{ __('Advance') }}</span>
                                                                    <span class="font-weight-bold ml-1">₹{{ number_format($paySummary['advance_paid'] > 0 ? $paySummary['advance_paid'] : $paySummary['total_paid']) }}</span>
                                                                </a>
                                                            @endif
                                                        @elseif($enq->hasInvoice())
                                                            <a href="{{ route('admin.course-enquiry.show', $enq->id) }}#payment-card"
                                                               class="badge badge-danger text-white py-1 px-2 d-flex justify-content-between align-items-center"
                                                               title="{{ __('Payment Pending - Due: ₹') }}{{ number_format($paySummary['grand_total'], 2) }}">
                                                                <span><i class="fas fa-clock mr-1"></i> {{ __('Payment') }}</span>
                                                                <span class="font-weight-normal ml-1">{{ __('Pending') }}</span>
                                                            </a>
                                                        @else
                                                            <a href="{{ route('admin.course-enquiry.show', $enq->id) }}#payment-card"
                                                               class="badge badge-light border text-muted py-1 px-2 text-left"
                                                               title="{{ __('Collect Payment (Tax Invoice needed first)') }}">
                                                                <i class="fas fa-hand-holding-usd mr-1"></i> {{ __('Payment') }}
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>

                                                <td>
                                                    @if(!$enq->status || $enq->status === 'new')
                                                        <span class="badge badge-primary">{{ __('New') }}</span>
                                                    @elseif($enq->status === 'read')
                                                        <span class="badge badge-secondary">{{ __('Read') }}</span>
                                                    @elseif($enq->status === 'contacted')
                                                        <span class="badge badge-warning">{{ __('Contacted') }}</span>
                                                    @elseif($enq->status === 'closed')
                                                        <span class="badge badge-success">{{ __('Closed') }}</span>
                                                    @endif
                                                </td>

                                                <td>{{ $enq->created_at?->format('d M Y') ?? '—' }}</td>

                                                <td class="text-center" style="white-space: nowrap;">
                                                    {{-- View Details --}}
                                                    <a href="{{ route('admin.course-enquiry.show', $enq->id) }}"
                                                       class="btn btn-sm btn-info text-white" title="{{ __('View Details') }}">
                                                        <i class="fa fa-eye"></i>
                                                    </a>

                                                    {{-- Quick Tax Invoice shortcut --}}
                                                    @if($enq->hasInvoice())
                                                        <a href="{{ route('admin.course-enquiry.invoice.view', $enq->id) }}"
                                                           target="_blank"
                                                           class="btn btn-sm btn-primary text-white" title="{{ __('View Tax Invoice') }}">
                                                            <i class="fas fa-receipt"></i>
                                                        </a>
                                                    @else
                                                        <a href="{{ route('admin.course-enquiry.invoice', $enq->id) }}"
                                                           class="btn btn-sm btn-outline-primary" title="{{ __('Create Tax Invoice') }}">
                                                            <i class="fas fa-receipt"></i>
                                                        </a>
                                                    @endif

                                                    {{-- Delete --}}
                                                    <a href="javascript:;" data-toggle="modal"
                                                       data-target="#deleteModal"
                                                       class="btn btn-danger btn-sm"
                                                       onclick="deleteData({{ $enq->id }})" title="{{ __('Delete') }}">
                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <x-empty-table :name="__('Enquiries')" route="" create="no"
                                                :message="__('No enquiries found!')" colspan="10">
                                            </x-empty-table>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="float-right">
                                {{ $enquiries->links() }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

<x-admin.delete-modal />
@endsection

@push('js')
<script>
    'use strict';
    function deleteData(id) {
        var form = document.getElementById('deleteForm');
        form.setAttribute('action', '{{ url("admin/course-enquiry") }}/' + id);
        var mi = form.querySelector('input[name="_method"]');
        if (!mi) {
            mi = document.createElement('input');
            mi.type = 'hidden'; mi.name = '_method';
            form.appendChild(mi);
        }
        mi.value = 'DELETE';
    }
</script>
@endpush

@push('css')
<style>
    .max-h-400 { min-height: 400px; }
</style>
@endpush
