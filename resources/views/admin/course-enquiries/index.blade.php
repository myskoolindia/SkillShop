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
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Date') }}</th>
                                            <th class="text-center" style="min-width: 220px;">{{ __('Action') }}</th>
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

                                                {{-- Action (Workflow 2x2 Grid + Manage View/Delete) --}}
                                                <td class="text-center" style="white-space: nowrap;">
                                                    <div class="d-inline-flex align-items-center" style="gap: 5px;">
                                                        {{-- 2x2 Grid: Quote, PI, Inv, Pay --}}
                                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4px; min-width: 175px; max-width: 195px;">
                                                            {{-- Cell 1: Quote --}}
                                                            @if(!empty($qData['items']))
                                                                <a href="{{ route('admin.course-enquiry.quotation.view', $enq->id) }}"
                                                                   target="_blank"
                                                                   class="btn btn-sm btn-primary p-1 text-truncate text-left d-flex align-items-center"
                                                                   style="font-size: 11px; line-height: 1.2; height: 26px;"
                                                                   title="{{ __('Quotation') }}: {{ $qData['quotation_number'] ?? '' }} (₹{{ number_format($qData['grand_total'] ?? 0) }})">
                                                                    <i class="fas fa-file-invoice mr-1 flex-shrink-0"></i>
                                                                    <span class="text-truncate">Quote</span>
                                                                    @if(!empty($qData['grand_total']))
                                                                        <span class="ml-auto font-weight-bold" style="font-size: 10px;">₹{{ $qData['grand_total'] >= 1000 ? round($qData['grand_total']/1000, 1).'k' : number_format($qData['grand_total']) }}</span>
                                                                    @endif
                                                                </a>
                                                            @else
                                                                <a href="{{ route('admin.course-enquiry.quotation', $enq->id) }}"
                                                                   class="btn btn-sm btn-outline-secondary p-1 text-truncate text-left d-flex align-items-center text-muted"
                                                                   style="font-size: 11px; line-height: 1.2; height: 26px; border-style: dashed;"
                                                                   title="{{ __('Create Quotation') }}">
                                                                    <i class="fas fa-plus mr-1 flex-shrink-0"></i>
                                                                    <span>Quote</span>
                                                                </a>
                                                            @endif

                                                            {{-- Cell 2: Proforma (PI) --}}
                                                            @if(!empty($pData['items']) && $enq->hasProformaInvoice())
                                                                <a href="{{ route('admin.course-enquiry.proforma.view', $enq->id) }}"
                                                                   target="_blank"
                                                                   class="btn btn-sm btn-success p-1 text-truncate text-left d-flex align-items-center"
                                                                   style="font-size: 11px; line-height: 1.2; height: 26px;"
                                                                   title="{{ __('Proforma Invoice') }}: {{ $pData['invoice_number'] ?? '' }} (Rev {{ $enq->proforma_version }}) (₹{{ number_format($pData['grand_total'] ?? 0) }})">
                                                                    <i class="fas fa-file-invoice-dollar mr-1 flex-shrink-0"></i>
                                                                    <span class="text-truncate">PI</span>
                                                                    @if(!empty($pData['grand_total']))
                                                                        <span class="ml-auto font-weight-bold" style="font-size: 10px;">₹{{ $pData['grand_total'] >= 1000 ? round($pData['grand_total']/1000, 1).'k' : number_format($pData['grand_total']) }}</span>
                                                                    @endif
                                                                </a>
                                                            @else
                                                                <a href="{{ route('admin.course-enquiry.proforma', $enq->id) }}"
                                                                   class="btn btn-sm btn-outline-secondary p-1 text-truncate text-left d-flex align-items-center text-muted"
                                                                   style="font-size: 11px; line-height: 1.2; height: 26px; border-style: dashed;"
                                                                   title="{{ __('Create Proforma Invoice') }}">
                                                                    <i class="fas fa-plus mr-1 flex-shrink-0"></i>
                                                                    <span>PI</span>
                                                                </a>
                                                            @endif

                                                            {{-- Cell 3: Tax Invoice (Inv) --}}
                                                            @if(!empty($iData['items']) && $enq->hasInvoice())
                                                                <a href="{{ route('admin.course-enquiry.invoice.view', $enq->id) }}"
                                                                   target="_blank"
                                                                   class="btn btn-sm btn-info p-1 text-truncate text-left d-flex align-items-center text-white"
                                                                   style="font-size: 11px; line-height: 1.2; height: 26px;"
                                                                   title="{{ __('Tax Invoice') }}: {{ $iData['invoice_number'] ?? '' }} (₹{{ number_format($iData['grand_total'] ?? 0) }})">
                                                                    <i class="fas fa-receipt mr-1 flex-shrink-0"></i>
                                                                    <span class="text-truncate">Inv</span>
                                                                    @if(!empty($iData['grand_total']))
                                                                        <span class="ml-auto font-weight-bold" style="font-size: 10px;">₹{{ $iData['grand_total'] >= 1000 ? round($iData['grand_total']/1000, 1).'k' : number_format($iData['grand_total']) }}</span>
                                                                    @endif
                                                                </a>
                                                            @else
                                                                <a href="{{ route('admin.course-enquiry.invoice', $enq->id) }}"
                                                                   class="btn btn-sm btn-outline-secondary p-1 text-truncate text-left d-flex align-items-center text-muted"
                                                                   style="font-size: 11px; line-height: 1.2; height: 26px; border-style: dashed;"
                                                                   title="{{ __('Create Tax Invoice') }}">
                                                                    <i class="fas fa-plus mr-1 flex-shrink-0"></i>
                                                                    <span>Inv</span>
                                                                </a>
                                                            @endif

                                                            {{-- Cell 4: Payment (Pay) --}}
                                                            @if($enq->hasInvoice() && ($paySummary['total_paid'] ?? 0) > 0)
                                                                @if(($paySummary['payment_status'] ?? '') === 'paid')
                                                                    <a href="{{ route('admin.course-enquiry.show', $enq->id) }}#payment-card"
                                                                       class="btn btn-sm btn-success p-1 text-truncate text-left d-flex align-items-center"
                                                                       style="font-size: 11px; line-height: 1.2; height: 26px;"
                                                                       title="{{ __('Paid in Full: ₹') }}{{ number_format($paySummary['total_paid'], 2) }}">
                                                                        <i class="fas fa-check-circle mr-1 flex-shrink-0"></i>
                                                                        <span class="text-truncate">Paid</span>
                                                                        <span class="ml-auto font-weight-bold" style="font-size: 10px;">₹{{ $paySummary['total_paid'] >= 1000 ? round($paySummary['total_paid']/1000, 1).'k' : number_format($paySummary['total_paid']) }}</span>
                                                                    </a>
                                                                @else
                                                                    <a href="{{ route('admin.course-enquiry.show', $enq->id) }}#payment-card"
                                                                       class="btn btn-sm btn-warning p-1 text-truncate text-left d-flex align-items-center text-dark font-weight-bold"
                                                                       style="font-size: 11px; line-height: 1.2; height: 26px;"
                                                                       title="{{ __('Advance: ₹') }}{{ number_format($paySummary['advance_paid'] > 0 ? $paySummary['advance_paid'] : $paySummary['total_paid']) }} | Due: ₹{{ number_format($paySummary['balance_due']) }}">
                                                                        <i class="fas fa-adjust mr-1 flex-shrink-0"></i>
                                                                        <span class="text-truncate">Adv</span>
                                                                        @php $advAmt = $paySummary['advance_paid'] > 0 ? $paySummary['advance_paid'] : $paySummary['total_paid']; @endphp
                                                                        <span class="ml-auto font-weight-bold" style="font-size: 10px;">₹{{ $advAmt >= 1000 ? round($advAmt/1000, 1).'k' : number_format($advAmt) }}</span>
                                                                    </a>
                                                                @endif
                                                            @elseif($enq->hasInvoice())
                                                                <a href="{{ route('admin.course-enquiry.show', $enq->id) }}#payment-card"
                                                                   class="btn btn-sm btn-danger p-1 text-truncate text-left d-flex align-items-center"
                                                                   style="font-size: 11px; line-height: 1.2; height: 26px;"
                                                                   title="{{ __('Payment Pending - Due: ₹') }}{{ number_format($paySummary['grand_total'], 2) }}">
                                                                    <i class="fas fa-clock mr-1 flex-shrink-0"></i>
                                                                    <span class="text-truncate">Unpaid</span>
                                                                </a>
                                                            @else
                                                                <a href="{{ route('admin.course-enquiry.show', $enq->id) }}#payment-card"
                                                                   class="btn btn-sm btn-outline-secondary p-1 text-truncate text-left d-flex align-items-center text-muted"
                                                                   style="font-size: 11px; line-height: 1.2; height: 26px; border-style: dashed;"
                                                                   title="{{ __('Collect Payment (Tax Invoice needed first)') }}">
                                                                    <i class="fas fa-credit-card mr-1 flex-shrink-0"></i>
                                                                    <span>Pay</span>
                                                                </a>
                                                            @endif
                                                        </div>

                                                        {{-- Manage Controls (View & Delete) --}}
                                                        <div class="d-flex flex-column pl-1 border-left" style="gap: 4px;">
                                                            {{-- View Details --}}
                                                            <a href="{{ route('admin.course-enquiry.show', $enq->id) }}"
                                                               class="btn btn-sm btn-info text-white p-0 d-flex align-items-center justify-content-center"
                                                               style="width: 26px; height: 26px;"
                                                               title="{{ __('View Details') }}">
                                                                <i class="fa fa-eye" style="font-size: 11px;"></i>
                                                            </a>

                                                            {{-- Delete --}}
                                                            <a href="javascript:;" data-toggle="modal"
                                                               data-target="#deleteModal"
                                                               class="btn btn-sm btn-danger text-white p-0 d-flex align-items-center justify-content-center"
                                                               style="width: 26px; height: 26px;"
                                                               onclick="deleteData({{ $enq->id }})"
                                                               title="{{ __('Delete') }}">
                                                                <i class="fa fa-trash" style="font-size: 11px;"></i>
                                                            </a>

                                                            {{-- Demo Scheduled --}}
                                                            <a href="javascript:;"
                                                            class="btn btn-sm btn-warning text-dark p-0 d-flex align-items-center justify-content-center"
                                                            style="width: 26px; height: 26px;"
                                                            title="{{ __('Schedule Demo') }}"
                                                            onclick="scheduleDemo({{ $enq->id }}, this)">
                                                                <i class="fas fa-calendar-check" style="font-size: 11px;"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <x-empty-table :name="__('Enquiries')" route="" create="no"
                                                :message="__('No enquiries found!')" colspan="9">
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
    function scheduleDemo(id, button) {

        if (!confirm('Are you sure you want to schedule the demo?')) {
            return;
        }

        var originalHtml = button.innerHTML;

        button.innerHTML = '<i class="fas fa-spinner fa-spin" style="font-size: 11px;"></i>';
        button.style.pointerEvents = 'none';

        $.ajax({
            url: '{{ route("admin.course-enquiry.demo-scheduled") }}',
            type: 'POST',
            data: {
                enquiry_id: id,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {

                if (response.success) {
                    button.classList.remove('btn-warning', 'text-dark');
                    button.classList.add('btn-success', 'text-white');

                    button.innerHTML =
                        '<i class="fas fa-check" style="font-size: 11px;"></i>';

                    button.title = 'Demo Scheduled';

                    if (typeof toastr !== 'undefined') {
                        toastr.success(
                            response.message || 'Demo scheduled successfully.'
                        );
                    }
                } else {
                    button.innerHTML = originalHtml;
                    button.style.pointerEvents = '';

                    if (typeof toastr !== 'undefined') {
                        toastr.error(
                            response.message || 'Unable to schedule demo.'
                        );
                    } else {
                        alert(response.message || 'Unable to schedule demo.');
                    }
                }
            },
            error: function(xhr) {

                button.innerHTML = originalHtml;
                button.style.pointerEvents = '';

                var message = 'Unable to schedule demo.';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                if (typeof toastr !== 'undefined') {
                    toastr.error(message);
                } else {
                    alert(message);
                }
            }
        });
    }
</script>
@endpush

@push('css')
<style>
    .max-h-400 { min-height: 400px; }
</style>
@endpush
