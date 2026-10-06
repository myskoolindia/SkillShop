@extends('frontend.layouts.master')
@section('meta_title', 'Login | ' . ($setting->app_name ?? 'Skillvation'))

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

  .auth-tabs {
    display: flex;
    background: #f1f5f9;
    border-radius: 10px;
    padding: 4px;
    margin-bottom: 24px;
    gap: 4px;
  }
  .auth-tab-btn {
    flex: 1;
    padding: 10px 12px;
    border-radius: 8px;
    border: none;
    background: transparent;
    font-size: 14px;
    font-weight: 700;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s ease;
    text-align: center;
    white-space: nowrap;
  }
  .auth-tab-btn.active {
    background: #ffffff;
    color: #1976d2;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  }

  @media (max-width: 480px) {
    .auth-tab-btn {
      font-size: 12px;
      padding: 8px 6px;
    }
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
</style>
@endpush

@section('contents')
    <!-- breadcrumb-area -->
    <x-frontend.breadcrumb
        :title="__('Login')"
        :links="[
            ['url' => route('home'), 'text' => __('Home')],
            ['url' => route('login'), 'text' => __('Login')],
        ]"
    />
    <!-- breadcrumb-area-end -->

    <!-- singUp-area -->
    <section class="singUp-area section-py-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-8">
                    <div class="singUp-wrap">
                        <div class="text-center mb-4">
                            <h2 class="title mb-2">{{ __('Welcome back!') }}</h2>
                            <p class="text-muted">{{ __('Log in to your School, Teacher, or Student account') }}</p>
                        </div>

                        {{-- Dual Login Tabs --}}
                        <div class="auth-tabs">
                            <button type="button" class="auth-tab-btn active" id="tab-password-btn" onclick="switchLoginTab('password')">
                                <i class="fas fa-key mr-1"></i> {{ __('Password Login') }}
                            </button>
                            <button type="button" class="auth-tab-btn" id="tab-otp-btn" onclick="switchLoginTab('otp')">
                                <i class="fab fa-whatsapp mr-1"></i> {{ __('WhatsApp OTP Login') }}
                            </button>
                        </div>

                        @if($setting && $setting->google_login_status == 'active')
                        <div class="account__social">
                            <a href="{{ route('auth.social', 'google') }}" class="account__social-btn">
                                <img src="{{ asset('frontend/img/icons/google.svg') }}" alt="img">
                                {{ __('Continue with google') }}
                            </a>
                        </div>
                        <div class="account__divider">
                            <span>{{ __('or') }}</span>
                        </div>
                        @endif

                        {{-- Tab 1: Password Login Form (Email OR Phone + Password) --}}
                        <div id="tab-password-content">
                            <form method="POST" action="{{ route('user-login') }}" class="account__form">
                                @csrf
                                <div class="form-grp">
                                    <label for="login-input">{{ __('Email Address or Mobile Number') }} <code>*</code></label>
                                    <input id="login-input" type="text" placeholder="{{ __('Enter email or WhatsApp mobile number') }}" value="{{ old('login') }}" name="login" required>
                                    <x-frontend.validation-error name="login" />
                                </div>
                                <div class="form-grp">
                                    <label for="password">{{ __('Password') }} <code>*</code></label>
                                    <input id="password" type="password" placeholder="{{ __('Enter your password') }}" name="password" required>
                                    <x-frontend.validation-error name="password" />
                                </div>
                                <div class="account__check">
                                    <div class="account__check-remember">
                                        <input type="checkbox" class="form-check-input" name="remember" value="1" id="terms-check">
                                        <label for="terms-check" class="form-check-label">{{ __('Remember me') }}</label>
                                    </div>
                                    <div class="account__check-forgot">
                                        <a href="{{ route('password.request') }}">{{ __('Forgot Password?') }}</a>
                                    </div>
                                </div>

                                @if ($setting && $setting->recaptcha_status === 'active')
                                <div class="form-grp mt-3">
                                    <div class="g-recaptcha" data-sitekey="{{ $setting->recaptcha_site_key }}"></div>
                                    <x-frontend.validation-error name="g-recaptcha-response" />
                                </div>
                                @endif

                                <button type="submit" class="btn btn-two arrow-btn w-100 py-3 mt-2">
                                    {{ __('Sign In') }}
                                    <img src="{{ asset('frontend/img/icons/right_arrow.svg') }}" alt="img" class="injectable">
                                </button>
                            </form>
                        </div>

                        {{-- Tab 2: WhatsApp OTP Login Form --}}
                        <div id="tab-otp-content" style="display:none;">
                            {{-- Step 1: Input Phone --}}
                            <div id="login-phone-box">
                                <div class="form-grp mb-3">
                                    <label for="login-otp-phone" class="font-weight-bold text-dark font-semibold">
                                        {{ __('Registered WhatsApp Mobile Number') }}
                                    </label>
                                    <div class="input-group mt-1">
                                        <span class="input-group-text bg-light border font-weight-bold text-dark px-3" style="border-radius:8px 0 0 8px;">
                                            +91
                                        </span>
                                        <input type="tel" id="login-otp-phone" class="form-control" placeholder="{{ __('Enter 10-digit registered number') }}" maxlength="10" style="border-radius:0 8px 8px 0; height:50px; font-size:16px;">
                                    </div>
                                    <div id="login-phone-error" class="text-danger small mt-1" style="display:none;"></div>
                                </div>

                                <button type="button" id="btn-login-send-otp" class="btn btn-two arrow-btn w-100 py-3" onclick="handleLoginSendOtp()">
                                    <i class="fab fa-whatsapp mr-2"></i> {{ __('Send WhatsApp Login Code') }}
                                </button>
                            </div>

                            {{-- Step 2: Enter OTP --}}
                            <div id="login-otp-box" class="p-3 p-sm-4 border rounded-3 bg-light" style="display:none;">
                                <div class="text-center mb-3">
                                    <div class="waba-badge mb-2">
                                        <i class="fab fa-whatsapp"></i> {{ __('WhatsApp Login Code') }}
                                    </div>
                                    <h5 class="font-weight-bold text-dark mb-1">{{ __('Enter 6-Digit Code') }}</h5>
                                    <p class="text-muted small mb-0">
                                        {{ __('Code sent to') }} <b id="login-display-phone" class="text-dark"></b>
                                        <a href="javascript:void(0)" onclick="resetLoginOtp()" class="text-primary ml-2 font-weight-bold" style="font-size:12px;">{{ __('Change') }}</a>
                                    </p>
                                </div>

                                <div class="otp-inputs-wrapper">
                                    <input type="text" maxlength="1" class="otp-digit-input" id="l-otp-1" oninput="lOtpMove(this, 'l-otp-2', '')" onkeydown="lOtpBack(this, event, '')">
                                    <input type="text" maxlength="1" class="otp-digit-input" id="l-otp-2" oninput="lOtpMove(this, 'l-otp-3', 'l-otp-1')" onkeydown="lOtpBack(this, event, 'l-otp-1')">
                                    <input type="text" maxlength="1" class="otp-digit-input" id="l-otp-3" oninput="lOtpMove(this, 'l-otp-4', 'l-otp-2')" onkeydown="lOtpBack(this, event, 'l-otp-2')">
                                    <input type="text" maxlength="1" class="otp-digit-input" id="l-otp-4" oninput="lOtpMove(this, 'l-otp-5', 'l-otp-3')" onkeydown="lOtpBack(this, event, 'l-otp-3')">
                                    <input type="text" maxlength="1" class="otp-digit-input" id="l-otp-5" oninput="lOtpMove(this, 'l-otp-6', 'l-otp-4')" onkeydown="lOtpBack(this, event, 'l-otp-4')">
                                    <input type="text" maxlength="1" class="otp-digit-input" id="l-otp-6" oninput="lOtpMove(this, '', 'l-otp-5')" onkeydown="lOtpBack(this, event, 'l-otp-5')">
                                </div>

                                <div id="l-otp-error" class="text-danger small text-center mb-2" style="display:none;"></div>

                                <button type="button" id="btn-login-verify-otp" class="btn btn-two arrow-btn w-100 py-3 mb-2" onclick="handleLoginVerifyOtp()">
                                    {{ __('Verify & Log In') }}
                                </button>

                                <div class="text-center mt-2">
                                    <span id="login-resend-timer" class="text-muted small">
                                        {{ __('Resend code in') }} <span id="login-countdown">45</span>s
                                    </span>
                                    <a href="javascript:void(0)" id="btn-login-resend-otp" onclick="handleLoginSendOtp()" class="text-primary font-weight-bold small" style="display:none;">
                                        {{ __('Resend Code') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="account__switch text-center mt-4">
                            <p class="mb-2">{{ __('Don\'t have an account?') }} <a href="{{ route('register') }}" class="font-weight-bold text-primary">{{ __('Sign Up here') }}</a></p>
                            <div class="d-flex justify-content-center gap-2 mt-2 flex-wrap">
                                <a href="{{ route('register', ['role' => 'school']) }}" class="badge rounded-pill bg-light text-dark border text-decoration-none px-3 py-2" style="font-size:12px;">
                                    <i class="fas fa-school text-purple me-1" style="color:#9333ea;"></i> {{ __('Register School') }}
                                </a>
                                <a href="{{ route('register', ['role' => 'teacher']) }}" class="badge rounded-pill bg-light text-dark border text-decoration-none px-3 py-2" style="font-size:12px;">
                                    <i class="fas fa-chalkboard-teacher text-warning me-1" style="color:#d97706;"></i> {{ __('Register Teacher') }}
                                </a>
                                <a href="{{ route('register', ['role' => 'student']) }}" class="badge rounded-pill bg-light text-dark border text-decoration-none px-3 py-2" style="font-size:12px;">
                                    <i class="fas fa-user-graduate text-info me-1" style="color:#0284c7;"></i> {{ __('Register Student') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@push('scripts')
<script>
    let loginCountdownInterval = null;

    function switchLoginTab(tab) {
        document.getElementById('tab-password-btn').classList.toggle('active', tab === 'password');
        document.getElementById('tab-otp-btn').classList.toggle('active', tab === 'otp');

        document.getElementById('tab-password-content').style.display = tab === 'password' ? 'block' : 'none';
        document.getElementById('tab-otp-content').style.display = tab === 'otp' ? 'block' : 'none';
    }

    function handleLoginSendOtp() {
        const phone = document.getElementById('login-otp-phone').value.trim();
        const errEl = document.getElementById('login-phone-error');
        const btnSend = document.getElementById('btn-login-send-otp');

        errEl.style.display = 'none';

        if (!phone || phone.length < 10) {
            errEl.textContent = '{{ __("Please enter your registered 10-digit mobile number.") }}';
            errEl.style.display = 'block';
            return;
        }

        btnSend.disabled = true;
        btnSend.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> {{ __("Sending Code...") }}';

        fetch('{{ route("auth.send-otp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                phone: phone,
                type: 'login'
            })
        })
        .then(res => res.json())
        .then(data => {
            btnSend.disabled = false;
            btnSend.innerHTML = '<i class="fab fa-whatsapp mr-2"></i> {{ __("Send WhatsApp Login Code") }}';

            if (data.status === 'success') {
                document.getElementById('login-display-phone').textContent = '+91 ' + phone;
                document.getElementById('login-phone-box').style.display = 'none';
                document.getElementById('login-otp-box').style.display = 'block';
                document.getElementById('l-otp-1').focus();

                startLoginTimer();

                if (data.mock_otp) {
                    console.log('Development Mock OTP:', data.mock_otp);
                }
            } else {
                errEl.textContent = data.message || '{{ __("Failed to send OTP.") }}';
                errEl.style.display = 'block';
            }
        })
        .catch(err => {
            btnSend.disabled = false;
            btnSend.innerHTML = '<i class="fab fa-whatsapp mr-2"></i> {{ __("Send WhatsApp Login Code") }}';
            errEl.textContent = '{{ __("Network error. Please try again.") }}';
            errEl.style.display = 'block';
        });
    }

    function handleLoginVerifyOtp() {
        const phone = document.getElementById('login-otp-phone').value.trim();
        const otpErr = document.getElementById('l-otp-error');
        const btnVerify = document.getElementById('btn-login-verify-otp');

        let otp = '';
        for (let i = 1; i <= 6; i++) {
            otp += document.getElementById('l-otp-' + i).value.trim();
        }

        otpErr.style.display = 'none';

        if (otp.length !== 6) {
            otpErr.textContent = '{{ __("Please enter all 6 digits.") }}';
            otpErr.style.display = 'block';
            return;
        }

        btnVerify.disabled = true;
        btnVerify.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> {{ __("Logging in...") }}';

        fetch('{{ route("auth.login-otp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                phone: phone,
                otp: otp
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                window.location.href = data.redirect_url || '{{ route("home") }}';
            } else {
                btnVerify.disabled = false;
                btnVerify.innerHTML = '{{ __("Verify & Log In") }}';
                otpErr.textContent = data.message || '{{ __("Invalid OTP.") }}';
                otpErr.style.display = 'block';
            }
        })
        .catch(() => {
            btnVerify.disabled = false;
            btnVerify.innerHTML = '{{ __("Verify & Log In") }}';
            otpErr.textContent = '{{ __("Verification error. Please try again.") }}';
            otpErr.style.display = 'block';
        });
    }

    function resetLoginOtp() {
        clearInterval(loginCountdownInterval);
        document.getElementById('login-otp-box').style.display = 'none';
        document.getElementById('login-phone-box').style.display = 'block';
        for (let i = 1; i <= 6; i++) {
            document.getElementById('l-otp-' + i).value = '';
        }
    }

    function startLoginTimer() {
        let count = 45;
        const countdownEl = document.getElementById('login-countdown');
        const timerTextEl = document.getElementById('login-resend-timer');
        const resendBtn = document.getElementById('btn-login-resend-otp');

        timerTextEl.style.display = 'inline';
        resendBtn.style.display = 'none';
        countdownEl.textContent = count;

        clearInterval(loginCountdownInterval);
        loginCountdownInterval = setInterval(() => {
            count--;
            countdownEl.textContent = count;
            if (count <= 0) {
                clearInterval(loginCountdownInterval);
                timerTextEl.style.display = 'none';
                resendBtn.style.display = 'inline';
            }
        }, 1000);
    }

    function lOtpMove(current, nextId, prevId) {
        if (current.value.length >= 1 && nextId) {
            document.getElementById(nextId).focus();
        }
    }

    function lOtpBack(current, event, prevId) {
        if (event.key === 'Backspace' && !current.value && prevId) {
            document.getElementById(prevId).focus();
        }
    }

    // Support pasting 6-digit OTP code directly in login OTP inputs
    document.addEventListener('DOMContentLoaded', () => {
        const otpInputs = [1, 2, 3, 4, 5, 6].map(i => document.getElementById('l-otp-' + i)).filter(Boolean);
        otpInputs.forEach((input, index) => {
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pastedData = (e.clipboardData || window.clipboardData).getData('text').trim().replace(/[^0-9]/g, '');
                if (pastedData) {
                    for (let i = 0; i < 6; i++) {
                        const target = document.getElementById('l-otp-' + (i + 1));
                        if (target && pastedData[i]) {
                            target.value = pastedData[i];
                        }
                    }
                    const lastFilledIndex = Math.min(pastedData.length, 6);
                    const focusTarget = document.getElementById('l-otp-' + lastFilledIndex);
                    if (focusTarget) focusTarget.focus();

                    if (pastedData.length >= 6) {
                        handleLoginVerifyOtp();
                    }
                }
            });

            input.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    handleLoginVerifyOtp();
                }
            });
        });

        const phoneInput = document.getElementById('login-otp-phone');
        if (phoneInput) {
            phoneInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    handleLoginSendOtp();
                }
            });
        }
    });
</script>
@endpush
@endsection
