@extends('admin.master_layout')
@section('title')
    <title>{{ __('Course Enquiries') }}</title>
@endsection
@section('admin-content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>{{ __('Course Enquiries') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active">
                    <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                </div>
                <div class="breadcrumb-item">{{ __('Course Enquiries') }}</div>
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
                                            <th>{{ __('Name') }}</th>
                                            <th>{{ __('Phone') }}</th>
                                            <th>{{ __('Email') }}</th>
                                            <th>{{ __('City') }}</th>
                                            <th>{{ __('Source') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Date') }}</th>
                                            <th class="text-center">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($enquiries as $enq)
                                            <tr>
                                                <td>{{ $loop->iteration + ($enquiries->currentPage() - 1) * $enquiries->perPage() }}</td>

                                                <td>{{ $enq->name }}</td>

                                                <td>{{ $enq->phone ?? '—' }}</td>

                                                <td>{{ $enq->email }}</td>

                                                <td>{{ $enq->city ?? '—' }}</td>

                                                <td>
                                                    @if($enq->source)
                                                        
                                                    {{ ucwords(str_replace(['-','_'], ' ', $enq->source)) }}
                                                        
                                                    @else
                                                        —
                                                    @endif
                                                </td>

                                                <td>
                                                    @if(!$enq->status || $enq->status === 'new')
                                                        {{ __('New') }}
                                                    @elseif($enq->status === 'read')
                                                        {{ __('Read') }}
                                                    @elseif($enq->status === 'contacted')
                                                        {{ __('Contacted') }}
                                                    @elseif($enq->status === 'closed')
                                                        {{ __('Closed') }}
                                                    @endif
                                                </td>

                                                <td>{{ $enq->created_at }}</td>

                                                <td class="text-center min-200">
                                                    <a href="{{ route('admin.course-enquiry.show', $enq->id) }}"
                                                       class="m-1 text-white btn btn-sm btn-primary" title="{{ __('View') }}">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="javascript:;" data-toggle="modal"
                                                       data-target="#deleteModal"
                                                       class="btn btn-danger btn-sm"
                                                       onclick="deleteData({{ $enq->id }})">
                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                    </a>
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
</script>
@endpush

@push('css')
<style>
    .max-h-400 { min-height: 400px; }
</style>
@endpush
