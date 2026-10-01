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
                    </div>

                </div>
            </div>
        </div>
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
