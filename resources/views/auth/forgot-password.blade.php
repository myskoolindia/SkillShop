@extends('frontend.layouts.master')
@section('meta_title', 'Forget Password'. ' || ' . $setting->app_name)

@push('styles')
<style>
  .breadcrumb__bg {
    padding: 20px 0 15px 0 !important;
  }
  .breadcrumb__content .title {
    font-size: 26px !important;
    margin-bottom: 2px !important;
  }
  .breadcrumb__content .breadcrumb {
    font-size: 13px !important;
    gap: 6px !important;
  }
  .breadcrumb__shape-wrap {
    display: none !important;
  }
  .singUp-area.section-py-120 {
    padding-top: 15px !important;
    padding-bottom: 35px !important;
  }
  .singUp-wrap {
    padding: 24px 32px 24px !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
  }
  .singUp-wrap .title {
    font-size: 26px !important;
    margin-bottom: 4px !important;
  }
  @media (max-width: 767.98px) {
    .breadcrumb__bg {
      padding: 16px 0 10px 0 !important;
    }
    .singUp-area.section-py-120 {
      padding-top: 12px !important;
      padding-bottom: 30px !important;
    }
    .singUp-wrap {
      padding: 18px 14px !important;
      border-radius: 12px;
    }
    .singUp-wrap .title {
      font-size: 22px !important;
    }
  }
</style>
@endpush

@section('contents')
    <!-- breadcrumb-area -->
    <x-frontend.breadcrumb
        :title="__('Forgot Password')"
        :links="[
            ['url' => route('home'), 'text' => __('Home')],
            ['url' => route('password.request'), 'text' => __('Forgot Password')],
        ]"
    />
    <!-- breadcrumb-area-end -->

    <!-- singUp-area -->
    <section class="singUp-area section-py-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-8">
                    <div class="singUp-wrap">
                        <h2 class="title">{{ __('Forgot your password?') }}</h2>
                        <p>{{ __('Enter your email address and we will send you a link to reset your password') }}
                        </p>

                        <form method="POST" action="{{ route('forget-password') }}" class="account__form">
                            @csrf
                            <div class="form-grp">
                                <label for="email">{{ __('Email') }} <code>*</code></label>
                                <input id="email" type="text" placeholder="email" name="email">
                                <x-frontend.validation-error name="email" />
                            </div>

                            <!-- g-recaptcha -->
                            @if (Cache::get('setting')->recaptcha_status === 'active')
                            <div class="form-grp mt-3">
                                <div class="g-recaptcha" data-sitekey="{{ Cache::get('setting')->recaptcha_site_key }}"></div>
                                <x-frontend.validation-error name="g-recaptcha-response" />
                            </div>
                            @endif

                            <button type="submit" class="btn btn-two arrow-btn">{{ __('Send Reset Link') }}<img
                                    src="{{ asset('frontend/img/icons/right_arrow.svg') }}" alt="img"
                                    class="injectable"></button>
                        </form>
                        <div class="account__switch">
                            <p>{{ __('Already have an account?') }}<a href="{{ route('login') }}">{{ __('Sign in') }}</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- singUp-area-end -->
@endsection
