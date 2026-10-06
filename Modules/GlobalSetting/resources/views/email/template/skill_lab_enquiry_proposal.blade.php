@extends('admin.master_layout')
@section('title')
    <title>{{ __('Email Template') }}</title>
@endsection
@section('admin-content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>{{ __('Email Template') }}</h1>
        </div>
        <div class="section-body">
            <a href="{{ route('admin.email-configuration') }}" class="btn btn-primary">
                <i class="fas fa-list"></i> {{ __('Email Templates') }}
            </a>

            <div class="row mt-4">
                <div class="col">
                    <div class="card">
                        <div class="card-header"><h4>{{ __('Available Variables') }}</h4></div>
                        <div class="card-body">
                            <table class="table table-bordered table-sm">
                                <thead class="thead-light">
                                    <tr><th>{{ __('Variable') }}</th><th>{{ __('Description') }}</th></tr>
                                </thead>
                                <tbody>
                                    @php
                                    $vars = [
                                        '{{name}}'            => 'Contact person name',
                                        '{{designation}}'     => 'Designation (e.g. Principal)',
                                        '{{school}}'          => 'School name',
                                        '{{city}}'            => 'City',
                                        '{{package}}'         => 'Package / lab type (Basic / Advance / Premium)',
                                        '{{date}}'            => 'Date of enquiry',
                                        '{{quotation_table}}' => 'Full formatted quotation table (HTML)',
                                    ];
                                    @endphp
                                    @foreach($vars as $var => $desc)
                                    <tr>
                                        <td><code>{{ $var }}</code></td>
                                        <td>{{ $desc }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>{{ __('Formal Proposal Email — Sent with Quotation') }}</h4>
                            <small class="text-muted">Full proposal document with implementation timeline and quotation, sent to the school.</small>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.update-email-template', $template->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label>{{ __('Subject') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="subject" value="{{ $template->subject }}">
                                </div>
                                <div class="form-group">
                                    <label>{{ __('Message') }} <span class="text-danger">*</span></label>
                                    <textarea name="message" cols="30" rows="16" class="form-control summernote">{{ $template->message }}</textarea>
                                </div>
                                <button class="btn btn-success" type="submit">{{ __('Update') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
