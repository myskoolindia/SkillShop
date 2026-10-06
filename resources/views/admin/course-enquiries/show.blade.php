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
                    <a href="{{ route('admin.course-enquiries') }}">{{ __('Course Enquiries') }}</a>
                </div>
                <div class="breadcrumb-item">#{{ $enquiry->id }}</div>
            </div>
        </div>

        <div class="section-body">
            <div class="mt-4 row justify-content-center">
                <div class="col-12 col-lg-9">

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
                                <select id="statusDropdown"
                                        class="form-control form-control-sm"
                                        style="width:140px;"
                                        data-id="{{ $enquiry->id }}">
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
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/','',$enquiry->phone) }}"
                                                   target="_blank"
                                                   class="btn btn-success btn-sm py-0 px-2"
                                                   style="font-size:12px;">
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
                                            <td>{{ $enquiry->school ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">{{ __('City') }}</td>
                                            <td>{{ $enquiry->city ?? '—' }}</td>
                                        </tr>
                                        @if($enquiry->address)
                                        <tr>
                                            <td class="text-muted">{{ __('Address') }}</td>
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
                                            <td>{{ $enquiry->course_title ?? '—' }}</td>
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

                            <div class="mt-3 pt-3 border-top">
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/','',$enquiry->phone) }}"
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

                    {{-- ── Quotation / Proposal Table ──────────────────── --}}
                    @if($enquiry->quotation)
                    @php
                        $quot = json_decode($enquiry->quotation, true);
                        $items = $quot['items'] ?? [];
                        $grandTotal = $quot['grand_total'] ?? 0;
                        // Group items by category
                        $grouped = [];
                        foreach($items as $row) {
                            $cat = $row['category'] ?? 'General';
                            $grouped[$cat][] = $row;
                        }
                    @endphp
                    @if(!empty($items))
                    <div class="card mt-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">
                                <i class="fas fa-file-invoice text-warning mr-2"></i>
                                {{ __('Quotation — Lab Bundle Items') }}
                            </h4>
                            <span class="badge badge-warning" style="font-size:13px;">
                                {{ count($items) }} {{ __('items') }}
                            </span>
                        </div>
                        <div class="card-body p-0">

                            {{-- Proposal Header --}}
                            <div class="px-4 py-3 border-bottom bg-light">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1 font-weight-bold text-dark" style="font-size:14px;">
                                            {{ __('Establishment of a Composite Skill Lab — Skillvation') }}
                                        </p>
                                        <p class="mb-0 text-muted" style="font-size:12px;">
                                            {{ __('A multi-domain, hands-on skill education space for Grades 6–12') }}
                                        </p>
                                    </div>
                                    <div class="col-md-6 text-md-right">
                                        <p class="mb-1 text-muted" style="font-size:12px;">{{ __('To') }}: <strong>{{ $enquiry->name }}</strong>{{ $enquiry->designation ? ', '.$enquiry->designation : '' }}</p>
                                        <p class="mb-0 text-muted" style="font-size:12px;">{{ $enquiry->school ?? '' }}{{ $enquiry->city ? ' · '.$enquiry->city : '' }}</p>
                                        <p class="mb-0 text-muted" style="font-size:12px;">{{ $enquiry->created_at?->format('d M Y') }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Items table grouped by category --}}
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0" style="font-size:13px;">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th style="width:30px;">#</th>
                                            <th>{{ __('Category') }}</th>
                                            <th>{{ __('Item / Description') }}</th>
                                            <th class="text-center" style="width:80px;">{{ __('Qty') }}</th>
                                            <th class="text-right" style="width:110px;">{{ __('Unit Price') }}</th>
                                            <th class="text-right" style="width:120px;">{{ __('Total') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $sno = 0; @endphp
                                        @foreach($grouped as $category => $catItems)
                                            @php
                                                $catTotal = array_sum(array_column($catItems, 'total'));
                                            @endphp
                                            @foreach($catItems as $row)
                                                @php $sno++; @endphp
                                                <tr>
                                                    <td class="text-muted">{{ $sno }}</td>
                                                    <td>
                                                        @if($loop->first)
                                                            <span class="badge badge-secondary">{{ $category }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="font-weight-bold">{{ $row['item'] ?? '—' }}</td>
                                                    <td class="text-center">{{ $row['qty'] ?? 0 }}</td>
                                                    <td class="text-right">
                                                        ₹{{ number_format($row['unit_price'] ?? 0, 2) }}
                                                    </td>
                                                    <td class="text-right font-weight-bold">
                                                        ₹{{ number_format($row['total'] ?? 0, 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            {{-- Category subtotal --}}
                                            <tr class="table-light">
                                                <td colspan="5" class="text-right text-muted font-weight-bold" style="font-size:12px;">
                                                    {{ $category }} {{ __('Subtotal') }}
                                                </td>
                                                <td class="text-right font-weight-bold text-primary">
                                                    ₹{{ number_format($catTotal, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-dark">
                                            <td colspan="5" class="text-right font-weight-bold" style="font-size:14px;">
                                                <i class="fas fa-rupee-sign mr-1"></i> {{ __('Grand Total') }}
                                            </td>
                                            <td class="text-right font-weight-bold" style="font-size:15px; color:#ffc107;">
                                                ₹{{ number_format($grandTotal, 2) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            {{-- Footer note --}}
                            <div class="px-4 py-3 border-top bg-light">
                                <p class="mb-1 text-muted" style="font-size:12px;">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    {{ __('This quotation is based on the bundle items configured in the Skillvation lab package selected by the school. A detailed itemized budget will be prepared once priorities and vendor quotations are finalised.') }}
                                </p>
                                <p class="mb-0 text-muted" style="font-size:12px;">
                                    {{ __('Phased procurement (core IT/electronics first, other domains in subsequent phases) can be considered to spread costs across budget cycles.') }}
                                </p>
                            </div>

                        </div>
                    </div>
                    @endif
                    @endif

                </div>{{-- /.col --}}
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
                data: {
                    _token: '{{ csrf_token() }}',
                    status: status,
                },
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
