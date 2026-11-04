@extends('frontend.school-dashboard.layouts.master')

@section('dashboard-contents')
    <div class="dashboard__content-wrap">
        <div class="dashboard__content-title">
            <h4 class="title">{{ __('Add Teacher') }}</h4>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('school.teachers.store') }}" method="POST" class="account__form">
                    @csrf
                    <div class="row gutter-20">
                        <div class="col-md-6">
                            <div class="form-grp">
                                <label for="name">{{ __('Full Name') }} <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="{{ __('Teacher full name') }}" required>
                                <x-frontend.validation-error name="name" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-grp">
                                <label for="email">{{ __('Email') }} <span class="text-danger">*</span></label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="{{ __('teacher@example.com') }}" required>
                                <x-frontend.validation-error name="email" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-grp">
                                <label for="password">{{ __('Password') }} <small class="text-muted">({{ __('optional') }})</small></label>
                                <input type="text" id="password" name="password" value="" minlength="8" maxlength="128" autocomplete="new-password" placeholder="{{ __('At least 8 characters, or leave blank to generate one') }}">
                                <x-frontend.validation-error name="password" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-grp">
                                <label for="id_number">{{ __('Employee ID / ID Number') }}</label>
                                <input type="text" id="id_number" name="id_number" value="{{ old('id_number') }}" placeholder="{{ __('Optional') }}">
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">{{ __('Add Teacher') }}</button>
                        <a href="{{ route('school.teachers.index') }}" class="btn btn-outline-secondary ms-2">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
