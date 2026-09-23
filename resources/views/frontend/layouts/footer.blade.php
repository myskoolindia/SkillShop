@php
    $footerSetting = \Modules\FooterSetting\app\Models\FooterSetting::first();
    $setting = Cache::get('setting');
    $footer_menu_one = Cache::rememberForever('footer_menu_one', function () {
        return menuGetBySlug('footer-col-one');
    });
    $footer_menu_two = Cache::rememberForever('footer_menu_two', function () {
        return menuGetBySlug('footer-col-two-1PiTN');
    });
    $footer_menu_three = Cache::rememberForever('footer_menu_three', function () {
        return menuGetBySlug('footer-col-three');
    });

    $footerLogo = null;
    if (!empty($footerSetting?->logo) && file_exists(public_path($footerSetting->logo))) {
        $footerLogo = asset($footerSetting->logo);
    } elseif (!empty($setting?->footer_logo) && file_exists(public_path($setting->footer_logo))) {
        $footerLogo = asset($setting->footer_logo);
    } elseif (!empty($setting?->logo) && file_exists(public_path($setting->logo))) {
        $footerLogo = asset($setting->logo);
    } else {
        $footerLogo = asset('designs/img/logo.png');
    }
@endphp

<footer
    class="footer__area mt-0" style="margin-top: 0 !important;">
    <style>
        .footer__area .footer__top,
        .footer__area-two .footer__top,
        .footer__area.footer__area-two .footer__top {
            padding: 48px 0 32px !important;
        }
        .footer__area .footer__widget .logo {
            margin-bottom: 22px !important;
        }
        .footer__area .footer__bottom {
            padding: 20px 0 !important;
        }
        @media (max-width: 767.98px) {
            .footer__area .footer__top,
            .footer__area-two .footer__top,
            .footer__area.footer__area-two .footer__top {
                padding: 36px 0 24px !important;
            }
        }
    </style>

    <div class="footer__top">
        <div class="container">
            <div class="row">
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="footer__widget">
                        <div class="logo mb-35">
                            <a href="{{ route('home') }}" class="d-inline-block px-3 py-2 rounded-3 bg-white shadow-sm" style="max-width: 220px;">
                                <img src="{{ $footerLogo }}" alt="{{ config('app.name', 'Skillvation') }}" onerror="this.onerror=null;this.src='{{ asset('designs/img/logo.png') }}';" style="max-height: 40px; width: auto; display: block;">
                            </a>
                        </div>
                        <div class="footer__content">
                            <p>{{ $footerSetting?->footer_text }}</p>
                            <ul class="list-wrap">
                                <li>{{ $footerSetting?->address }}</li>
                                <li>{{ $footerSetting?->phone }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                    @if(count($footer_menu_one) > 0)
                    <div class="footer__widget">
                        <h4 class="footer__widget-title">{{ __('Useful Links') }}</h4>
                        <div class="footer__link">
                            <ul class="list-wrap">
                                @foreach ($footer_menu_one as $footerMenuOne)
                                    <li><a href="{{ url($footerMenuOne['link']) }}">{{ $footerMenuOne['label'] }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                    @if(count($footer_menu_two) > 0)
                    <div class="footer__widget">
                        <h4 class="footer__widget-title">{{ __('Our Company') }}</h4>
                        <div class="footer__link">
                            <ul class="list-wrap">
                                @foreach ($footer_menu_two as $footerMenuTwo)
                                    <li><a href="{{ url($footerMenuTwo['link']) }}">{{ $footerMenuTwo['label'] }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="footer__widget">
                        <h4 class="footer__widget-title">{{ __('Get In Touch') }}</h4>
                        <div class="footer__contact-content">
                            <p>{{ $footerSetting?->get_in_touch_text }}</p>
                            <ul class="list-wrap footer__social">
                                @foreach (getSocialLinks() as $socialLink)
                                    <li>
                                        <a href="{{ $socialLink->link }}" target="_blank">
                                            <img src="{{ asset($socialLink->icon) }}" alt="img">
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="app-download">
                            @if ($footerSetting?->google_play_link)
                                <a href="{{ $footerSetting->google_play_link }}"><img
                                        src="{{ asset('frontend/img/others/google-play.svg') }}" alt="img"></a>
                            @endif
                            @if ($footerSetting?->apple_store_link)
                                <a href="{{ $footerSetting->apple_store_link }}"><img
                                        src="{{ asset('frontend/img/others/apple-store.svg') }}" alt="img"></a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer__bottom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <div class="copy-right-text">
                        @if(Cache::get('setting')->copyright_text)
                        <p>© {{ Cache::get('setting')->copyright_text }}</p>
                        @endif
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="footer__bottom-menu">
                        <ul class="list-wrap">
                            @foreach ($footer_menu_three as $footerMenuThree)
                                <li><a href="{{ url($footerMenuThree['link']) }}">{{ $footerMenuThree['label'] }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
