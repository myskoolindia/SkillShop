@extends('frontend.school-dashboard.layouts.master')

@section('dashboard-contents')
    <div class="dashboard__content-wrap">
        <div class="dashboard__content-title d-flex justify-content-between align-items-center mb-4">
            <h4 class="title mb-0">{{ __('Add Student') }}</h4>
            <a href="{{ route('school.students.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fa fa-arrow-left"></i> {{ __('Back to Students') }}
            </a>
        </div>

        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-body p-4">
                <form action="{{ route('school.students.store') }}" method="POST" class="account__form">
                    @csrf
                    <div class="row g-3">
                        {{-- Full Name --}}
                        <div class="col-md-6">
                            <div class="form-grp mb-3">
                                <label for="name" class="form-label fw-bold">{{ __('Full Name') }} <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control" placeholder="{{ __('Student full name') }}" required>
                                <x-frontend.validation-error name="name" />
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="col-md-6">
                            <div class="form-grp mb-3">
                                <label for="email" class="form-label fw-bold">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="{{ __('student@example.com') }}" required>
                                <x-frontend.validation-error name="email" />
                            </div>
                        </div>

                        {{-- Password --}}
                        <div class="col-md-6">
                            <div class="form-grp mb-3">
                                <label for="password" class="form-label fw-bold">{{ __('Password') }} <small class="text-muted fw-normal">({{ __('optional') }})</small></label>
                                <input type="text" id="password" name="password" value="" minlength="8" maxlength="128" autocomplete="new-password" class="form-control" placeholder="{{ __('At least 8 characters, or leave blank to generate one') }}">
                                <x-frontend.validation-error name="password" />
                            </div>
                        </div>

                        {{-- Roll Number / ID --}}
                        <div class="col-md-6">
                            <div class="form-grp mb-3">
                                <label for="id_number" class="form-label fw-bold">{{ __('Roll Number / Student ID') }}</label>
                                <input type="text" id="id_number" name="id_number" value="{{ old('id_number') }}" class="form-control" placeholder="{{ __('e.g. STD-2026-001') }}">
                                <x-frontend.validation-error name="id_number" />
                            </div>
                        </div>

                        {{-- Grade --}}
                        <div class="col-md-6">
                            <div class="form-grp mb-3">
                                <label for="grade" class="form-label fw-bold">{{ __('Grade / Class') }} <span class="text-danger">*</span></label>
                                <select id="grade" name="grade" class="form-select" required>
                                    <option value="" disabled {{ old('grade') ? '' : 'selected' }}>{{ __('Select Grade / Class') }}</option>
                                    @foreach($grades as $g)
                                        <option value="{{ $g }}" {{ old('grade') == $g ? 'selected' : '' }}>{{ $g }}</option>
                                    @endforeach
                                </select>
                                <x-frontend.validation-error name="grade" />
                            </div>
                        </div>

                        {{-- Section --}}
                        <div class="col-md-6">
                            <div class="form-grp mb-3">
                                <label for="section" class="form-label fw-bold">{{ __('Section') }}</label>
                                <select id="section" name="section" class="form-select">
                                    <option value="">{{ __('Select Section (Optional)') }}</option>
                                    @foreach($sections as $sec)
                                        <option value="{{ $sec }}" {{ old('section') == $sec ? 'selected' : '' }}>{{ __('Section') }} {{ $sec }}</option>
                                    @endforeach
                                </select>
                                <x-frontend.validation-error name="section" />
                            </div>
                        </div>

                        {{-- Academic Year --}}
                        <div class="col-md-6">
                            <div class="form-grp mb-3">
                                <label for="academic_year" class="form-label fw-bold">{{ __('Academic Year') }}</label>
                                <select id="academic_year" name="academic_year" class="form-select">
                                    <option value="">{{ __('Select Academic Year (Optional)') }}</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year }}" {{ (old('academic_year') == $year || (!old('academic_year') && $year == date('Y') . '-' . (date('Y') + 1))) ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-frontend.validation-error name="academic_year" />
                            </div>
                        </div>

                        {{-- Board --}}
                        <div class="col-md-6">
                            <div class="form-grp mb-3">
                                <label for="board" class="form-label fw-bold">{{ __('Education Board') }}</label>
                                <select id="board" name="board" class="form-select">
                                    <option value="">{{ __('Select Board (Optional)') }}</option>
                                    @foreach($boards as $b)
                                        <option value="{{ $b }}" {{ old('board') == $b ? 'selected' : ($b == 'CBSE' && !old('board') ? 'selected' : '') }}>
                                            {{ $b }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-frontend.validation-error name="board" />
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2 mt-4 pt-2">
                        <button type="submit" class="btn btn-primary px-4 py-2" style="width: auto !important; margin-top: 0 !important;">
                            <i class="fa fa-user-plus me-1"></i> {{ __('Add Student') }}
                        </button>
                        <a href="{{ route('school.students.index') }}" class="btn btn-outline-secondary px-4 py-2" style="width: auto !important; margin-top: 0 !important;">
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
