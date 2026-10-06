@extends('frontend.layouts.master')
@section('meta_title', 'Register | ' . ($setting->app_name ?? 'Skillvation'))

@push('styles')
<style>
  /* Responsive Container & Spacing */
  @media (max-width: 767.98px) {
    .singUp-area.section-py-120 {
      padding-top: 40px !important;
      padding-bottom: 50px !important;
    }
    .singUp-wrap {
      padding: 24px 16px !important;
      border-radius: 12px;
    }
    .singUp-wrap .title {
      font-size: 24px !important;
    }
  }

  /* Role Selection Cards */
  .reg-role-card {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 10px;
    cursor: pointer;
    transition: all 0.25s ease;
    background: #ffffff;
    text-align: center;
    position: relative;
    user-select: none;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
  }
  .reg-role-card:hover {
    border-color: #1976d2;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(25, 118, 210, 0.12);
  }
  .reg-role-card.active {
    border-color: #1976d2;
    background: #f0f7ff;
    box-shadow: 0 4px 14px rgba(25, 118, 210, 0.18);
  }
  .reg-role-card .role-icon {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    margin-bottom: 8px;
    transition: all 0.25s ease;
  }
  .reg-role-card.active .role-icon {
    background-color: #1976d2 !important;
    color: #ffffff !important;
  }
  .reg-role-card .role-title {
    font-size: 14px;
    font-weight: 700;
    color: #1e293b;
    line-height: 1.2;
  }
  .reg-role-card .role-subtitle {
    font-size: 11px;
    color: #64748b;
    margin-top: 3px;
  }

  @media (max-width: 575.98px) {
    .reg-role-card {
      padding: 10px 4px;
      border-radius: 10px;
    }
    .reg-role-card .role-icon {
      width: 36px;
      height: 36px;
      font-size: 15px;
      margin-bottom: 6px;
    }
    .reg-role-card .role-title {
      font-size: 12px;
    }
    .reg-role-card .role-subtitle {
      display: none !important;
    }
  }

  .reg-step-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #1976d2;
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    margin-right: 6px;
    vertical-align: middle;
  }

  /* OTP Inputs Mobile Responsive */
  .otp-inputs-wrapper {
    display: flex;
    justify-content: center;
    gap: 8px;
    max-width: 320px;
    margin: 0 auto 16px auto;
  }
  .otp-digit-input {
    flex: 1;
    min-width: 34px;
    max-width: 44px;
    height: 48px;
    text-align: center;
    font-size: 20px;
    font-weight: 700;
    border: 2px solid #cbd5e1;
    border-radius: 8px;
    transition: all 0.2s ease;
    padding: 0;
  }
  .otp-digit-input:focus {
    border-color: #25d366;
    outline: none;
    box-shadow: 0 0 0 3px rgba(37, 211, 102, 0.2);
  }

  @media (max-width: 380px) {
    .otp-inputs-wrapper {
      gap: 4px;
    }
    .otp-digit-input {
      min-width: 30px;
      max-width: 38px;
      height: 42px;
      font-size: 17px;
    }
  }

  .waba-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #e8f8ee;
    color: #128c7e;
    font-size: 12px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 20px;
  }
  .verified-phone-box {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 10px;
    padding: 12px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 8px;
  }
</style>
@endpush

@section('contents')
    <!-- breadcrumb-area -->
    <x-frontend.breadcrumb :title="__('Create an Account')" :links="[['url' => route('home'), 'text' => __('Home')], ['url' => route('register'), 'text' => __('Register')]]" />
    <!-- breadcrumb-area-end -->

    <section class="singUp-area section-py-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-7 col-lg-8">
                    <div class="singUp-wrap">
                        <div class="text-center mb-4">
                            <h2 class="title mb-2">{{ __('Join Skillvation') }}</h2>
                            <p class="text-muted">{{ __('Select your role and verify your WhatsApp number to get started') }}</p>
                        </div>

                        {{-- Step 1 & 2: Role Selection & WhatsApp Mobile Verification --}}
                        <div id="otp-verification-section">
                            
                            {{-- 1. Role Selection --}}
                            <div class="mb-4">
                                <label class="form-label font-weight-bold mb-2 d-block text-dark font-semibold">
                                    <span class="reg-step-badge">1</span> {{ __('Select Account Type') }}
                                </label>
                                <div class="row g-2 g-sm-3">
                                    <div class="col-4">
                                        <div class="reg-role-card active" data-role="school" onclick="selectRole('school')">
                                            <div class="role-icon" style="background:#f3e8ff; color:#9333ea;">
                                                <i class="fas fa-school"></i>
                                            </div>
                                            <div class="role-title">{{ __('School') }}</div>
                                            <div class="role-subtitle">{{ __('For institutions') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="reg-role-card" data-role="teacher" onclick="selectRole('teacher')">
                                            <div class="role-icon" style="background:#fef3c7; color:#d97706;">
                                                <i class="fas fa-chalkboard-teacher"></i>
                                            </div>
                                            <div class="role-title">{{ __('Teacher') }}</div>
                                            <div class="role-subtitle">{{ __('For educators') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="reg-role-card" data-role="student" onclick="selectRole('student')">
                                            <div class="role-icon" style="background:#e0f2fe; color:#0284c7;">
                                                <i class="fas fa-user-graduate"></i>
                                            </div>
                                            <div class="role-title">{{ __('Student') }}</div>
                                            <div class="role-subtitle">{{ __('For learners') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- 2. Phone Input Box --}}
                            <div class="form-grp mb-4" id="phone-input-container">
                                <label for="phone-input" class="form-label font-weight-bold text-dark font-semibold">
                                    <span class="reg-step-badge">2</span> {{ __('WhatsApp Mobile Number') }}
                                </label>
                                <div class="d-flex align-items-center mb-1">
                                    <span class="waba-badge">
                                        <i class="fab fa-whatsapp"></i> {{ __('OTP sent via WhatsApp') }}
                                    </span>
                                </div>
                                <div class="input-group mt-2">
                                    <span class="input-group-text bg-light border font-weight-bold text-dark px-3" style="border-radius:8px 0 0 8px;">
                                        +91
                                    </span>
                                    <input type="tel" id="phone-input" class="form-control" placeholder="{{ __('Enter 10-digit mobile number') }}" maxlength="10" style="border-radius:0 8px 8px 0; height:50px; font-size:16px;">
                                </div>
                                <div id="phone-error" class="text-danger small mt-1" style="display:none;"></div>
                                <div class="mt-3">
                                    <button type="button" id="btn-send-otp" class="btn btn-two arrow-btn w-100 py-3" onclick="handleSendOtp()">
                                        <i class="fab fa-whatsapp mr-2"></i> {{ __('Send WhatsApp Verification Code') }}
                                    </button>
                                </div>
                            </div>

                            {{-- 3. OTP Enter Box (Initially Hidden) --}}
                            <div id="otp-input-container" class="p-3 p-sm-4 border rounded-3 bg-light mb-4" style="display:none;">
                                <div class="text-center mb-3">
                                    <div class="waba-badge mb-2">
                                        <i class="fab fa-whatsapp"></i> {{ __('WhatsApp Verification') }}
                                    </div>
                                    <h5 class="font-weight-bold text-dark mb-1">{{ __('Enter 6-Digit Code') }}</h5>
                                    <p class="text-muted small mb-0">
                                        {{ __('Code sent to WhatsApp number') }} <b id="display-phone" class="text-dark"></b>
                                        <a href="javascript:void(0)" onclick="resetPhoneStep()" class="text-primary ml-2 font-weight-bold" style="font-size:12px;">{{ __('Change') }}</a>
                                    </p>
                                </div>

                                <div class="otp-inputs-wrapper">
                                    <input type="text" maxlength="1" class="otp-digit-input" id="otp-1" oninput="otpMove(this, 'otp-2', '')" onkeydown="otpBack(this, event, '')">
                                    <input type="text" maxlength="1" class="otp-digit-input" id="otp-2" oninput="otpMove(this, 'otp-3', 'otp-1')" onkeydown="otpBack(this, event, 'otp-1')">
                                    <input type="text" maxlength="1" class="otp-digit-input" id="otp-3" oninput="otpMove(this, 'otp-4', 'otp-2')" onkeydown="otpBack(this, event, 'otp-2')">
                                    <input type="text" maxlength="1" class="otp-digit-input" id="otp-4" oninput="otpMove(this, 'otp-5', 'otp-3')" onkeydown="otpBack(this, event, 'otp-3')">
                                    <input type="text" maxlength="1" class="otp-digit-input" id="otp-5" oninput="otpMove(this, 'otp-6', 'otp-4')" onkeydown="otpBack(this, event, 'otp-4')">
                                    <input type="text" maxlength="1" class="otp-digit-input" id="otp-6" oninput="otpMove(this, '', 'otp-5')" onkeydown="otpBack(this, event, 'otp-5')">
                                </div>

                                <div id="otp-error" class="text-danger small text-center mb-2" style="display:none;"></div>

                                <button type="button" id="btn-verify-otp" class="btn btn-two arrow-btn w-100 py-3 mb-2" onclick="handleVerifyOtp()">
                                    {{ __('Verify & Continue') }}
                                </button>

                                <div class="text-center mt-2">
                                    <span id="resend-timer-text" class="text-muted small">
                                        {{ __('Resend code in') }} <span id="countdown">45</span>s
                                    </span>
                                    <a href="javascript:void(0)" id="btn-resend-otp" onclick="handleSendOtp()" class="text-primary font-weight-bold small" style="display:none;">
                                        {{ __('Resend WhatsApp Code') }}
                                    </a>
                                </div>
                            </div>

                        </div>

                        {{-- Step 3: Registration Form (Shown after OTP verified) --}}
                        <div id="details-form-section" style="display:none;">
                            <div class="verified-phone-box">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-check-circle text-success fa-lg"></i>
                                    <div>
                                        <div class="font-weight-bold text-dark" style="font-size:14px;" id="badge-verified-phone"></div>
                                        <small class="text-success font-weight-bold" style="font-size:12px;">{{ __('Verified via WhatsApp') }}</small>
                                    </div>
                                </div>
                                <span class="badge badge-primary px-3 py-2 text-capitalize" id="badge-role-display" style="font-size:12px; background:#1976d2;"></span>
                            </div>

                            <form method="POST" action="{{ route('register') }}" class="account__form" id="final-register-form">
                                @csrf
                                <input type="hidden" name="account_type" id="form-account-type" value="school">
                                <input type="hidden" name="phone" id="form-phone" value="">
                                <input type="hidden" name="phone_token" id="form-phone-token" value="">

                                {{-- School Specific Fields --}}
                                <div id="school-details-fields" style="display:none;">
                                    <div class="form-grp">
                                        <label for="school_name">{{ __('School / Institution Name') }} <code>*</code></label>
                                        <input type="text" id="school_name" name="school_name" value="{{ old('school_name') }}" placeholder="{{ __('e.g. Springfield International School') }}">
                                        <x-frontend.validation-error name="school_name" />
                                    </div>
                                    <div class="row gutter-20">
                                        <div class="col-md-6">
                                            <div class="form-grp">
                                                <label for="registration_number">{{ __('Registration / Affiliation No.') }}</label>
                                                <input type="text" id="registration_number" name="registration_number" value="{{ old('registration_number') }}" placeholder="{{ __('e.g. CBSE/2026/09') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-grp">
                                                <label for="contact_person">{{ __('Contact Person / Principal') }}</label>
                                                <input type="text" id="contact_person" name="contact_person" value="{{ old('contact_person') }}" placeholder="{{ __('Principal / Admin name') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Teacher Specific Fields --}}
                                <div id="teacher-details-fields" style="display:none;">
                                    <div class="row gutter-20">
                                        <div class="col-md-12">
                                            <div class="form-grp">
                                                <label for="job_title">{{ __('Subject / Primary Teaching Domain') }}</label>
                                                <input type="text" id="job_title" name="job_title" value="{{ old('job_title') }}" placeholder="{{ __('e.g. Science, Mathematics, AI & Robotics, Arts') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-grp">
                                                <label for="short_bio">{{ __('Experience / Short Bio') }}</label>
                                                <textarea id="short_bio" name="short_bio" class="form-control" rows="2" placeholder="{{ __('Brief summary of your teaching experience') }}" style="border-radius:8px;">{{ old('short_bio') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Student Specific Fields --}}
                                <div id="student-details-fields" style="display:none;">
                                    <div class="row gutter-20">
                                        <div class="col-md-6">
                                            <div class="form-grp">
                                                <label for="age">{{ __('Grade / Class / Age') }}</label>
                                                <input type="text" id="age" name="age" value="{{ old('age') }}" placeholder="{{ __('e.g. Grade 8 or Age 14') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-grp">
                                                <label for="gender">{{ __('Gender') }}</label>
                                                <select id="gender" name="gender" class="form-control" style="border-radius:8px; height:50px;">
                                                    <option value="">{{ __('Select Gender (Optional)') }}</option>
                                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Common Fields --}}
                                <div class="form-grp">
                                    <label for="reg-name"><span id="name-label-text">{{ __('Full Name') }}</span> <code>*</code></label>
                                    <input type="text" id="reg-name" placeholder="{{ __('Enter full name') }}" name="name" value="{{ old('name') }}" required>
                                    <x-frontend.validation-error name="name" />
                                </div>

                                <div class="form-grp">
                                    <label for="reg-email">{{ __('Email Address') }} <code>*</code></label>
                                    <input type="email" id="reg-email" placeholder="{{ __('Enter email address') }}" name="email" value="{{ old('email') }}" required>
                                    <x-frontend.validation-error name="email" />
                                </div>

                                <div class="row gutter-20">
                                    <div class="col-md-6">
                                        <div class="form-grp">
                                            <label for="reg-password">{{ __('Password') }} <code>*</code></label>
                                            <input type="password" id="reg-password" placeholder="{{ __('Min 4 characters') }}" name="password" required>
                                            <x-frontend.validation-error name="password" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-grp">
                                            <label for="reg-confirm-password">{{ __('Confirm Password') }} <code>*</code></label>
                                            <input type="password" id="reg-confirm-password" placeholder="{{ __('Repeat password') }}" name="password_confirmation" required>
                                            <x-frontend.validation-error name="password_confirmation" />
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-two arrow-btn w-100 py-3 mt-3">
                                    {{ __('Complete Registration') }}
                                    <img src="{{ asset('frontend/img/icons/right_arrow.svg') }}" alt="img" class="injectable">
                                </button>
                            </form>
                        </div>

                        <div class="account__switch text-center mt-4">
                            <p>{{ __('Already have an account?') }} <a href="{{ route('login') }}" class="font-weight-bold text-primary">{{ __('Log in here') }}</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@push('scripts')
<script>
    let selectedRole = 'school';
    let verifiedPhone = '';
    let verifiedPhoneToken = '';
    let countdownInterval = null;

    function selectRole(role) {
        selectedRole = role;
        document.querySelectorAll('.reg-role-card').forEach(card => {
            card.classList.toggle('active', card.dataset.role === role);
        });
        document.getElementById('form-account-type').value = role;

        // Update name label text if school
        const nameLabel = document.getElementById('name-label-text');
        if (nameLabel) {
            nameLabel.textContent = role === 'school' ? '{{ __("Administrator Name") }}' : '{{ __("Full Name") }}';
        }

        // Switch role specific fields
        document.getElementById('school-details-fields').style.display = role === 'school' ? 'block' : 'none';
        document.getElementById('teacher-details-fields').style.display = role === 'teacher' ? 'block' : 'none';
        document.getElementById('student-details-fields').style.display = role === 'student' ? 'block' : 'none';
    }

    function handleSendOtp() {
        const phoneInput = document.getElementById('phone-input').value.trim();
        const phoneErr = document.getElementById('phone-error');
        const btnSend = document.getElementById('btn-send-otp');

        phoneErr.style.display = 'none';

        if (!phoneInput || phoneInput.length < 10) {
            phoneErr.textContent = '{{ __("Please enter a valid 10-digit mobile number.") }}';
            phoneErr.style.display = 'block';
            return;
        }

        btnSend.disabled = true;
        btnSend.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> {{ __("Sending WhatsApp OTP...") }}';

        fetch('{{ route("auth.send-otp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                phone: phoneInput,
                type: 'registration'
            })
        })
        .then(res => res.json())
        .then(data => {
            btnSend.disabled = false;
            btnSend.innerHTML = '<i class="fab fa-whatsapp mr-2"></i> {{ __("Send WhatsApp Verification Code") }}';

            if (data.status === 'success') {
                document.getElementById('display-phone').textContent = '+91 ' + phoneInput;
                document.getElementById('phone-input-container').style.display = 'none';
                document.getElementById('otp-input-container').style.display = 'block';
                document.getElementById('otp-1').focus();

                startResendTimer();

                if (data.mock_otp) {
                    console.log('Development Mock OTP:', data.mock_otp);
                }
            } else {
                phoneErr.textContent = data.message || '{{ __("Failed to send OTP.") }}';
                phoneErr.style.display = 'block';
            }
        })
        .catch(err => {
            btnSend.disabled = false;
            btnSend.innerHTML = '<i class="fab fa-whatsapp mr-2"></i> {{ __("Send WhatsApp Verification Code") }}';
            phoneErr.textContent = '{{ __("Network error. Please try again.") }}';
            phoneErr.style.display = 'block';
        });
    }

    function handleVerifyOtp() {
        const phoneInput = document.getElementById('phone-input').value.trim();
        const otpErr = document.getElementById('otp-error');
        const btnVerify = document.getElementById('btn-verify-otp');

        let otp = '';
        for (let i = 1; i <= 6; i++) {
            otp += document.getElementById('otp-' + i).value.trim();
        }

        otpErr.style.display = 'none';

        if (otp.length !== 6) {
            otpErr.textContent = '{{ __("Please enter all 6 digits of the OTP code.") }}';
            otpErr.style.display = 'block';
            return;
        }

        btnVerify.disabled = true;
        btnVerify.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> {{ __("Verifying...") }}';

        fetch('{{ route("auth.verify-otp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                phone: phoneInput,
                otp: otp
            })
        })
        .then(res => res.json())
        .then(data => {
            btnVerify.disabled = false;
            btnVerify.innerHTML = '{{ __("Verify & Continue") }}';

            if (data.status === 'success') {
                verifiedPhone = data.phone;
                verifiedPhoneToken = data.token;

                // Setup Final Form
                document.getElementById('form-phone').value = verifiedPhone;
                document.getElementById('form-phone-token').value = verifiedPhoneToken;
                document.getElementById('badge-verified-phone').textContent = '+91 ' + phoneInput;
                document.getElementById('badge-role-display').textContent = selectedRole.toUpperCase();

                // Switch to Details Form Step
                document.getElementById('otp-verification-section').style.display = 'none';
                document.getElementById('details-form-section').style.display = 'block';

                selectRole(selectedRole);
            } else {
                otpErr.textContent = data.message || '{{ __("Invalid OTP.") }}';
                otpErr.style.display = 'block';
            }
        })
        .catch(err => {
            btnVerify.disabled = false;
            btnVerify.innerHTML = '{{ __("Verify & Continue") }}';
            otpErr.textContent = '{{ __("Verification error. Please try again.") }}';
            otpErr.style.display = 'block';
        });
    }

    function resetPhoneStep() {
        clearInterval(countdownInterval);
        document.getElementById('otp-input-container').style.display = 'none';
        document.getElementById('phone-input-container').style.display = 'block';
        for (let i = 1; i <= 6; i++) {
            document.getElementById('otp-' + i).value = '';
        }
    }

    function startResendTimer() {
        let count = 45;
        const countdownEl = document.getElementById('countdown');
        const timerTextEl = document.getElementById('resend-timer-text');
        const resendBtn = document.getElementById('btn-resend-otp');

        timerTextEl.style.display = 'inline';
        resendBtn.style.display = 'none';
        countdownEl.textContent = count;

        clearInterval(countdownInterval);
        countdownInterval = setInterval(() => {
            count--;
            countdownEl.textContent = count;
            if (count <= 0) {
                clearInterval(countdownInterval);
                timerTextEl.style.display = 'none';
                resendBtn.style.display = 'inline';
            }
        }, 1000);
    }

    function otpMove(current, nextId, prevId) {
        if (current.value.length >= 1 && nextId) {
            document.getElementById(nextId).focus();
        }
    }

    function otpBack(current, event, prevId) {
        if (event.key === 'Backspace' && !current.value && prevId) {
            document.getElementById(prevId).focus();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const urlRole = urlParams.get('role');
        const oldRole = "{{ old('role', '') }}";
        const initialRole = (['school', 'teacher', 'student'].includes(urlRole)) 
            ? urlRole 
            : ((['school', 'teacher', 'student'].includes(oldRole)) ? oldRole : 'student');
        selectRole(initialRole);

        const otpInputs = [1, 2, 3, 4, 5, 6].map(i => document.getElementById('otp-' + i)).filter(Boolean);
        otpInputs.forEach((input) => {
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pastedData = (e.clipboardData || window.clipboardData).getData('text').trim().replace(/[^0-9]/g, '');
                if (pastedData) {
                    for (let i = 0; i < 6; i++) {
                        const target = document.getElementById('otp-' + (i + 1));
                        if (target && pastedData[i]) {
                            target.value = pastedData[i];
                        }
                    }
                    const lastFilledIndex = Math.min(pastedData.length, 6);
                    const focusTarget = document.getElementById('otp-' + lastFilledIndex);
                    if (focusTarget) focusTarget.focus();

                    if (pastedData.length >= 6) {
                        handleVerifyOtp();
                    }
                }
            });

            input.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    handleVerifyOtp();
                }
            });
        });

        const phoneInput = document.getElementById('phone-input');
        if (phoneInput) {
            phoneInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    handleSendOtp();
                }
            });
        }
    });
</script>
@endpush
@endsection
