@extends('frontend.school-dashboard.layouts.master')

@section('dashboard-contents')
    <div class="dashboard__content-wrap">
        <div class="dashboard__content-title d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
            <div>
                <h4 class="title mb-1">{{ __('RoboQuest Learning Lab') }}</h4>
                <p class="text-muted small mb-0">{{ __('Synchronize your school courses, teachers, and students with DigiLab RoboQuest.') }}</p>
            </div>
            @if($connection)
                <a class="btn btn-primary btn-sm d-inline-flex align-items-center gap-2" href="{{ route('roboquest.launch') }}" target="_blank">
                    <i class="fas fa-external-link-alt"></i> {{ __('Open RoboQuest Lab') }}
                </a>
            @endif
        </div>

        @if(session('roboquest_saved'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                <i class="fas fa-check-circle fa-lg"></i>
                <div>
                    <strong>{{ __('Connection Saved!') }}</strong> {{ __('Your school connection settings have been updated successfully.') }}
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                <i class="fas fa-exclamation-triangle fa-lg"></i>
                <div>
                    <strong>{{ __('Connection Error') }}:</strong> {{ $errors->first() }}
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Connection Status Banner --}}
        @if($connection)
            <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-left: 4px solid #16a34a !important;">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge bg-success px-2 py-1"><i class="fas fa-link me-1"></i> {{ __('Connected') }}</span>
                                <span class="text-muted small">{{ __('Active Integration') }}</span>
                            </div>
                            <h5 class="mb-1 text-dark">{{ __('DigiLab Learning Lab is connected') }}</h5>
                            <p class="mb-0 text-secondary small">
                                <strong>{{ __('School ID') }}:</strong> <code>{{ $connection->school_id }}</code> &nbsp;|&nbsp;
                                <strong>{{ __('RoboQuest URL') }}:</strong> <a href="{{ $connection->base_url }}" target="_blank" class="text-primary text-decoration-underline">{{ $connection->base_url }}</a>
                            </p>
                        </div>
                        <a class="btn btn-success d-inline-flex align-items-center gap-2" href="{{ route('roboquest.launch') }}" target="_blank">
                            <i class="fas fa-rocket"></i> {{ __('Launch RoboQuest') }}
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-left: 4px solid #2563eb !important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start gap-3">
                        <div class="text-primary mt-1">
                            <i class="fas fa-info-circle fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="mb-1 text-dark">{{ __('Connect DigiLab RoboQuest') }}</h5>
                            <p class="mb-0 text-secondary small">
                                {{ __('After confirmed course payment, your school opens its own RoboQuest environment. Paid courses and enrolled members are synced automatically.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Settings Form --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title mb-0 fs-6 fw-bold text-dark">
                    <i class="fas fa-sliders-h text-primary me-2"></i>{{ __('Connection Settings') }}
                </h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('roboquest.settings.save') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('School ID') }} <span class="text-danger">*</span></label>
                        <input class="form-control" name="school_id" required pattern="[A-Za-z0-9_-]{1,80}" 
                               placeholder="e.g. school-{{ auth()->id() }}" 
                               value="{{ old('school_id', $connection?->school_id ?? ('school-' . auth()->id())) }}">
                        <div class="form-text text-muted">
                            {{ __('A unique alphanumeric identifier (letters, numbers, dashes, underscores) identifying your school in RoboQuest.') }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('RoboQuest URL') }} <span class="text-danger">*</span></label>
                        <input class="form-control" name="base_url" type="url" required 
                               placeholder="https://digilab.skillvation.com" 
                               value="{{ old('base_url', $connection?->base_url ?? 'https://digilab.skillvation.com') }}">
                        <div class="form-text text-muted">
                            {{ __('The primary URL of the DigiLab RoboQuest server (e.g. https://digilab.skillvation.com). Must use HTTPS without trailing path.') }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold mb-0">
                                {{ __('School Connection Key') }} 
                                @if(!$connection)
                                    <span class="text-danger">*</span>
                                @endif
                            </label>
                            <button type="button" class="btn btn-link btn-sm text-decoration-none p-0" id="generate-key-btn">
                                <i class="fas fa-magic me-1"></i>{{ __('Generate 64-char key') }}
                            </button>
                        </div>
                        <div class="input-group">
                            <input class="form-control" id="shared-key-input" name="shared_key" type="password" 
                                   autocomplete="new-password" minlength="32" maxlength="200" 
                                   placeholder="{{ $connection ? __('Leave blank to keep existing shared key') : __('Enter or generate a 32+ character key') }}"
                                   {{ $connection ? '' : 'required' }}>
                            <button class="btn btn-outline-secondary" type="button" id="toggle-key-visibility" title="{{ __('Toggle visibility') }}">
                                <i class="fas fa-eye" id="toggle-key-icon"></i>
                            </button>
                        </div>
                        <div class="form-text text-muted">
                            {{ __('The HMAC-SHA256 secret key configured on the school server. Minimum 32 characters. Leave blank to keep existing key.') }}
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-1"></i> {{ __('Save Connection') }}
                        </button>
                        @if($connection)
                            <a class="btn btn-outline-primary" href="{{ route('roboquest.launch') }}" target="_blank">
                                <i class="fas fa-external-link-alt me-1"></i> {{ __('Open RoboQuest') }}
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Help / Guide Section --}}
        <div class="row mt-4">
            <div class="col-md-4 mb-3">
                <div class="p-3 border rounded bg-white h-100">
                    <div class="text-primary mb-2"><i class="fas fa-sync-alt fa-lg"></i></div>
                    <h6 class="fw-bold mb-1">{{ __('1. Automated Sync') }}</h6>
                    <p class="small text-muted mb-0">{{ __('Purchased and paid courses are automatically synced to your RoboQuest lab along with chapters, lessons, and video resources.') }}</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="p-3 border rounded bg-white h-100">
                    <div class="text-primary mb-2"><i class="fas fa-user-shield fa-lg"></i></div>
                    <h6 class="fw-bold mb-1">{{ __('2. Single Sign-On') }}</h6>
                    <p class="small text-muted mb-0">{{ __('Teachers and students can seamlessly enter RoboQuest using one-time signed cryptographic tokens with zero extra passwords.') }}</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="p-3 border rounded bg-white h-100">
                    <div class="text-primary mb-2"><i class="fas fa-users-cog fa-lg"></i></div>
                    <h6 class="fw-bold mb-1">{{ __('3. Roster Management') }}</h6>
                    <p class="small text-muted mb-0">{{ __('School members added in your School Dashboard are recognized by DigiLab for role-based course access and assignments.') }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const keyInput = document.getElementById('shared-key-input');
        const toggleBtn = document.getElementById('toggle-key-visibility');
        const toggleIcon = document.getElementById('toggle-key-icon');
        const generateBtn = document.getElementById('generate-key-btn');

        if (toggleBtn && keyInput && toggleIcon) {
            toggleBtn.addEventListener('click', function () {
                if (keyInput.type === 'password') {
                    keyInput.type = 'text';
                    toggleIcon.classList.remove('fa-eye');
                    toggleIcon.classList.add('fa-eye-slash');
                } else {
                    keyInput.type = 'password';
                    toggleIcon.classList.remove('fa-eye-slash');
                    toggleIcon.classList.add('fa-eye');
                }
            });
        }

        if (generateBtn && keyInput) {
            generateBtn.addEventListener('click', function () {
                const array = new Uint8Array(32);
                window.crypto.getRandomValues(array);
                const hexKey = Array.from(array, function (b) {
                    return b.toString(16).padStart(2, '0');
                }).join('');

                keyInput.value = hexKey;
                keyInput.type = 'text';
                if (toggleIcon) {
                    toggleIcon.classList.remove('fa-eye');
                    toggleIcon.classList.add('fa-eye-slash');
                }
            });
        }
    });
</script>
@endpush
