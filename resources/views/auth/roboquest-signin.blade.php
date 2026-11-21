@extends('frontend.layouts.master')
@section('meta_title', 'Sign in to RoboQuest')
@push('styles')
<style>
.rq-signin{max-width:640px;margin:48px auto 80px;padding:36px;background:#fff;border:1px solid #e3def2;border-radius:24px;box-shadow:0 18px 55px #36246610}.rq-signin>small{color:#7053c5;letter-spacing:.14em;font-weight:700}.rq-signin h1{font-size:34px;margin:14px 0}.rq-signin p{color:#746a86;line-height:1.7}.rq-login-options{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin:24px 0}.rq-login-option input{position:absolute;opacity:0}.rq-login-option span{display:block;padding:16px 8px;text-align:center;border:1px solid #e2ddee;border-radius:12px;color:#645873;cursor:pointer;font-weight:600}.rq-login-option input:checked+span{background:#efeaff;border-color:#6d4de3;color:#5735c4}.rq-login-option input:focus-visible+span{outline:3px solid #a693ec;outline-offset:3px}.rq-signin .form-grp{margin:20px 0}.rq-signin .form-grp label{display:block;margin-bottom:8px;font-weight:600}.rq-signin input[type=email],.rq-signin input[type=password]{width:100%;padding:14px;border:1px solid #dcd5ec;border-radius:10px}.rq-login-help{display:block;margin-top:18px;text-align:center}.rq-signin .alert{padding:14px;border-radius:10px;background:#fff1ef;color:#9c3434}@media(max-width:600px){.rq-signin{margin:24px 14px;padding:24px}.rq-signin h1{font-size:28px}.rq-login-option span{font-size:13px;padding:14px 5px}}
</style>
@endpush
@section('contents')
<main class="rq-signin">
    <small>ROBOQUEST LEARNING LAB</small>
    @if(!empty($signedIn))
    <h1>{{ __('You’re signed in') }}</h1>
    <p>{{ __('As') }} <strong>{{ $signedIn->name }}</strong> ({{ __(['school'=>'School Admin','teacher'=>'Teacher','student'=>'Student'][$signedIn->role] ?? 'Student') }}).</p>
    <a href="{{ route('roboquest.launch') }}" class="btn btn-two w-100" id="rq-back-to-roboquest">{{ __('Back to RoboQuest') }}</a>
    <a class="rq-login-help" href="{{ route('roboquest.signin', ['role' => $role, 'switch' => 1]) }}">{{ __('Use another account') }}</a>
    @else
    <h1>{{ __('Welcome to your learning space') }}</h1>
    <p>{{ __('Choose your account type and sign in to your school’s RoboQuest.') }}</p>
    @if($errors->any())<div class="alert" role="alert">{{ $errors->first() }}</div>@endif
    <form method="POST" action="{{ route('roboquest.signin.store') }}">
        @csrf
        <div class="rq-login-options" role="group" aria-label="{{ __('Sign-in options') }}">
            @foreach(['student'=>'Student', 'teacher'=>'Teacher', 'school'=>'School Admin'] as $value=>$label)
                <label class="rq-login-option"><input type="radio" name="account_role" value="{{ $value }}" @checked(old('account_role', $role)===$value) required><span>{{ __($label) }}</span></label>
            @endforeach
        </div>
        <div class="form-grp"><label for="rq-email">{{ __('Email address') }}</label><input id="rq-email" name="login" type="email" autocomplete="username" value="{{ old('login', request('email', request('login', ''))) }}" required></div>
        <div class="form-grp">
            <label for="rq-password">{{ __('Password') }}</label>
            <div style="position: relative;">
                <input id="rq-password" name="password" type="password" autocomplete="current-password" required style="padding-right: 48px;">
                <button type="button" id="toggleRqPasswordBtn" aria-label="{{ __('Show / Hide password') }}" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: transparent; border: none; padding: 6px 8px; cursor: pointer; color: #7053c5; font-size: 16px; display: inline-flex; align-items: center; justify-content: center; z-index: 5;">
                    <i class="far fa-eye" id="toggleRqPasswordIcon"></i>
                </button>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const btn = document.getElementById('toggleRqPasswordBtn');
                const inp = document.getElementById('rq-password');
                const ico = document.getElementById('toggleRqPasswordIcon');
                if (btn && inp && ico) {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        if (inp.type === 'password') {
                            inp.type = 'text';
                            ico.className = 'fas fa-eye-slash';
                            ico.style.color = '#5735c4';
                        } else {
                            inp.type = 'password';
                            ico.className = 'far fa-eye';
                            ico.style.color = '#7053c5';
                        }
                    });
                }
            });
        </script>
        @if($setting && $setting->recaptcha_status==='active')<div class="g-recaptcha" data-sitekey="{{ $setting->recaptcha_site_key }}"></div>@endif
        <button type="submit" class="btn btn-two w-100">{{ __('Sign in to RoboQuest') }}</button>
        <a class="rq-login-help" href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a>
    </form>
    @endif
    <p style="font-size:13px;margin-top:22px">{{ __('Use the account provided by your school. Your courses, assignments and permissions follow your verified account.') }}</p>
</main>
@endsection
