@php
    $nav_menu = Cache::rememberForever('nav_menu', function () {
        return menuGetBySlug('nav-menu');
    });

    // Ensure Labs menu item has child items populated
    if ($nav_menu) {
        foreach ($nav_menu as &$m) {
            $labelLower = strtolower(trim($m['label'] ?? ''));
            $linkTrim   = trim($m['link'] ?? '', '/');
            if ($labelLower === 'labs' || $linkTrim === 'labs') {
                if (empty($m['child'])) {
                    $m['child'] = [
                        ['label' => 'AI & Robotics Lab',   'link' => '/labs/ai-robotics'],
                        ['label' => 'STEM Lab',            'link' => '/labs/stem'],
                        ['label' => 'ECEC Lab',            'link' => '/labs/ecec'],
                        ['label' => 'Composite Skill Lab', 'link' => '/labs/composite-skill'],
                    ];
                }
            }
        }
        unset($m);
    }

    // Navigation follows the same verified audience rules as the backend.
    $shopRole = auth()->user()?->role;
    $allowedShopLink = function ($link) use ($shopRole) {
        if (!$shopRole) return true;
        $path = strtolower('/' . ltrim(parse_url($link, PHP_URL_PATH) ?: '', '/'));
        if (in_array($path, ['/shop','/skillbox'])) return $shopRole === 'student';
        return true;
    };
    $nav_menu = array_values(array_filter(array_map(function ($menu) use ($allowedShopLink) {
        if (!empty($menu['child'])) $menu['child'] = array_values(array_filter($menu['child'], fn($child) => $allowedShopLink($child['link'] ?? '')));
        return $menu;
    }, is_array($nav_menu) ? $nav_menu : ($nav_menu?->toArray() ?? [])), fn($menu) => $allowedShopLink($menu['link'] ?? '')));

    try {
        $categories = Cache::remember('header_course_categories', 3600, function () {
            return \Modules\Course\app\Models\CourseCategory::with('translation')
                ->where('status', 1)
                ->whereNull('parent_id')
                ->get();
        });
    } catch (\Throwable $e) {
        $categories = collect();
    }

    // Image map for known child menu links
    $childImageMap = [
        '/'                     => ['image' => asset('designs/img/logo.png'),              'desc' => 'Back to homepage'],
        '/skill2school'         => ['image' => asset('designs/img/skill2school-2.jpeg'),   'desc' => 'School-wide skill curriculum'],
        '/upskill4teacher'      => ['image' => asset('designs/img/TTT-1.png'),             'desc' => 'Professional educator development'],
        '/ttt'                  => ['image' => asset('designs/img/TTT-1.png'),             'desc' => 'Master trainer & bootcamp'],
        '/shop'                 => ['image' => asset('frontend/img/skillbox/skillbox_banner.png'), 'desc' => 'Interactive kits & learning boxes'],
        '/SkillBox'             => ['image' => asset('frontend/img/skillbox/skillbox_banner.png'), 'desc' => 'Interactive kits & learning boxes'],
        '/skillbox'             => ['image' => asset('frontend/img/skillbox/skillbox_banner.png'), 'desc' => 'Interactive kits & learning boxes'],
        '/labs'                       => ['image' => 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=600&q=80', 'desc' => 'Hands-on innovation labs'],
        '/labs/ai-robotics'           => ['image' => 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=600&q=80', 'desc' => 'AI & Robotics Lab'],
        '/labs/stem'                  => ['image' => 'https://images.unsplash.com/photo-1516339901601-2e1b62dc0c45?auto=format&fit=crop&w=600&q=80', 'desc' => 'Skillvation Lab'],
        '/labs/ecec'                  => ['image' => 'https://images.unsplash.com/photo-1581092335397-9583fe92d232?auto=format&fit=crop&w=600&q=80', 'desc' => 'ECEC Lab'],
        '/labs/composite-skill'       => ['image' => 'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=600&q=80', 'desc' => 'Composite Skill Lab'],
        '/labs/composite-skill-basic' => ['image' => 'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=600&q=80', 'desc' => 'Composite Skill Lab'],
        '/courses'              => ['image' => asset('designs/img/skill2school-3.png'),    'desc' => 'Browse all courses'],
        '/blog'                 => ['image' => asset('designs/img/skill2school-4.jpeg'),   'desc' => 'Articles & insights'],
        '/contact'              => ['image' => asset('designs/img/skill2school-5.jpeg'),   'desc' => 'Get in touch'],
    ];

    $childLabelMap = [
        'ai & robotics'       => 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=600&q=80',
        'ai & robotics lab'   => 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=600&q=80',
        'stem lab'            => 'https://images.unsplash.com/photo-1516339901601-2e1b62dc0c45?auto=format&fit=crop&w=600&q=80',
        'skillvation lab'     => 'https://images.unsplash.com/photo-1516339901601-2e1b62dc0c45?auto=format&fit=crop&w=600&q=80',
        'ecec lab'            => 'https://images.unsplash.com/photo-1581092335397-9583fe92d232?auto=format&fit=crop&w=600&q=80',
        'composite skill lab' => 'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=600&q=80',
        'composite lab'       => 'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=600&q=80',
    ];

    $headerLogo = null;
    if (!empty($setting?->logo) && file_exists(public_path($setting->logo))) {
        $headerLogo = asset($setting->logo);
    } elseif (!empty(Cache::get('setting')?->logo) && file_exists(public_path(Cache::get('setting')->logo))) {
        $headerLogo = asset(Cache::get('setting')->logo);
    } else {
        $headerLogo = asset('designs/img/logo.png');
    }
@endphp
<!-- header-area -->
<header>
    {{-- =========================================================================
         SPECIAL SCHOOL LAB FESTIVAL OFFER — PROMOTION TOP BAR (SCOPED CSS)
         ========================================================================= --}}
    @php
      $labOfferActive = false;
      try {
          $activeOffer = \App\Models\LabOffer::where('is_active', 1)->first();
          $labOfferActive = !empty($activeOffer) && (bool)$activeOffer->is_active;
      } catch (\Throwable $e) {}

      $currentPath = strtolower(trim(request()->path(), '/'));
      $userRole = auth()->check() ? strtolower(auth()->user()->role ?? '') : '';

      $isDashboardOrAdmin = !empty($hideFlashOffer)
        || request()->is('*school*', '*student*', '*instructor*', '*admin*', '*login*', '*register*')
        || request()->routeIs('school.*', 'student.*', 'instructor.*', 'admin.*', 'login', 'register')
        || str_contains($currentPath, 'school')
        || str_contains($currentPath, 'student')
        || str_contains($currentPath, 'instructor')
        || str_contains($currentPath, 'admin')
        || str_contains($currentPath, 'dashboard')
        || str_contains($currentPath, 'login')
        || str_contains($currentPath, 'register')
        || in_array($userRole, ['school', 'student', 'instructor', 'admin', 'teacher']);

      $showLabOffer = $labOfferActive && !$isDashboardOrAdmin;
    @endphp
    @if($showLabOffer)
    <style>
      .lab-flash-bar-wrap {
        background: linear-gradient(90deg, #020617 0%, #0f172a 50%, #020617 100%) !important;
        color: #ffffff !important;
        border-bottom: 1px solid rgba(240, 95, 67, 0.4) !important;
        font-size: 13px !important;
        padding: 8px 16px !important;
        position: relative !important;
        z-index: 9999 !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2) !important;
        font-family: inherit !important;
      }
      .lab-flash-container {
        max-width: 1280px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
      }
      .lab-flash-left {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
        min-width: 260px;
      }
      .lab-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 10px;
        border-radius: 9999px;
        background: linear-gradient(135deg, #f05f43, #f59e0b);
        color: #ffffff !important;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        white-space: nowrap;
        box-shadow: 0 2px 6px rgba(240,95,67,0.3);
      }
      .lab-badge-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #ffffff;
        display: inline-block;
        animation: labPulse 1.5s infinite;
      }
      @keyframes labPulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(0.85); }
      }
      .lab-event-desc {
        color: #e2e8f0 !important;
        font-size: 13px;
        font-weight: 500;
        margin: 0 !important;
      }
      .lab-flash-right {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
      }
      .lab-btn-claim {
        padding: 5px 14px;
        border-radius: 9999px;
        background: #f05f43 !important;
        color: #ffffff !important;
        font-weight: 800;
        font-size: 12px;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 2px 8px rgba(240,95,67,0.35);
        transition: transform 0.2s, background 0.2s;
      }
      .lab-btn-claim:hover {
        background: #e24e31 !important;
        color: #ffffff !important;
        transform: translateY(-1px);
      }
      .lab-btn-wa {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #10b981 !important;
        color: #ffffff !important;
        font-size: 15px;
        text-decoration: none !important;
        transition: transform 0.2s, background 0.2s;
        flex-shrink: 0;
      }
      .lab-btn-wa:hover {
        background: #059669 !important;
        color: #ffffff !important;
        transform: scale(1.08);
      }
      .lab-close-btn {
        color: #94a3b8 !important;
        background: transparent !important;
        border: none !important;
        font-size: 20px !important;
        line-height: 1 !important;
        padding: 2px 6px !important;
        cursor: pointer !important;
        transition: color 0.2s;
      }
      .lab-close-btn:hover {
        color: #ffffff !important;
      }
      @media (max-width: 768px) {
        .lab-event-desc {
          display: none;
        }
      }
    </style>

    <div id="labTopFlashBar" class="lab-flash-bar-wrap">
      <div class="lab-flash-container">
        
        <div class="lab-flash-left">
          <span class="lab-badge-pill">
            <span class="lab-badge-dot"></span>
            SPECIAL SCHOOL OFFER
          </span>
          <p class="lab-event-desc">
            Pre-book STEM, Robotics &amp; CBSE Composite Skill Labs with &#8377;25,000 advance token or save extra 10% on full payment!
          </p>
        </div>

        <div class="lab-flash-right">
          <a href="{{ route('lab.offer') }}" class="lab-btn-claim">
            <span>Claim Lab Offer &amp; 10% Discount</span>
            <i class="fa-solid fa-arrow-right text-[10px]"></i>
          </a>

          <a href="https://wa.me/919945612226?text=Hi%20Skillvation%20Team,%20I%20am%20enquiring%20about%20the%20Special%20School%20Lab%20Offer" target="_blank" class="lab-btn-wa" title="WhatsApp Enquiry">
            <i class="fa-brands fa-whatsapp"></i>
          </a>

          <button type="button" onclick="document.getElementById('labTopFlashBar').style.display='none'" class="lab-close-btn" title="Dismiss announcement">
            &times;
          </button>
        </div>

      </div>
    </div>
    @endif

    {{-- Floating Event Offer Badge on Site Pages (Scoped CSS, Hidden on Dashboards and when Offer Inactive) --}}
    @if($showLabOffer && !request()->routeIs('lab.offer'))
    <style>
      .lab-floating-pill {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 9990;
        display: flex;
        align-items: center;
        gap: 10px;
        font-family: inherit;
      }
      .lab-floating-card {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 18px 8px 14px;
        border-radius: 9999px;
        background: rgba(15, 23, 42, 0.95);
        color: #ffffff !important;
        box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        border: 1px solid rgba(240, 95, 67, 0.6);
        text-decoration: none !important;
        backdrop-filter: blur(8px);
        transition: transform 0.2s, box-shadow 0.2s;
      }
      .lab-floating-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(240, 95, 67, 0.25);
        color: #ffffff !important;
      }
      .lab-floating-wa {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #10b981;
        color: #ffffff !important;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4);
        text-decoration: none !important;
        transition: transform 0.2s, background 0.2s;
      }
      .lab-floating-wa:hover {
        background: #059669;
        transform: scale(1.08);
        color: #ffffff !important;
      }
      @media (max-width: 640px) {
        .lab-floating-card { display: none; }
      }
    </style>
    <div class="lab-floating-pill print:hidden">
      <a href="{{ route('lab.offer') }}" class="lab-floating-card">
        <span style="position: relative; display: flex; width: 10px; height: 10px;">
          <span style="position: absolute; width: 100%; height: 100%; border-radius: 50%; background: #f97316; opacity: 0.75; animation: labPulse 1.5s infinite;"></span>
          <span style="position: relative; width: 10px; height: 10px; border-radius: 50%; background: #f05f43;"></span>
        </span>
        <div style="text-align: left; line-height: 1.2;">
          <div style="font-size: 10px; font-weight: 900; color: #fde047; text-transform: uppercase; letter-spacing: 0.5px;">
            🔥 SPECIAL SCHOOL LAB OFFER
          </div>
          <div style="font-weight: 800; color: #ffffff; font-size: 12px;">
            10% Extra Discount &amp; Pre-Booking <span style="color: #f05f43;">&rarr;</span>
          </div>
        </div>
      </a>

      <a href="https://wa.me/919945612226?text=Hi%20Skillvation%20Team,%20I%20am%20enquiring%20about%20the%20Special%20School%20Lab%20Offer" target="_blank" class="lab-floating-wa" title="WhatsApp Enquiry: +91 99456 12226">
        <i class="fa-brands fa-whatsapp"></i>
      </a>
    </div>
    @endif

    @if ($setting?->header_topbar_status == 'active')
        <div class="tg-header__top">
            <div class="container custom-container xl_container">
                <div class="row">
                    <div class="col-lg-6">
                        <ul class="tg-header__top-info list-wrap">
                            @if ($setting?->site_address)
                                <li><img src="{{ asset('frontend/img/icons/map_marker.svg') }}" alt="Icon">
                                    <span>{{ $setting?->site_address }}</span>
                                </li>
                            @endif
                            @if ($setting?->site_email)
                                <li><img src="{{ asset('frontend/img/icons/envelope.svg') }}" alt="Icon"> <a
                                        href="mailto:{{ $setting?->site_email }}">{{ $setting?->site_email }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <div class="col-lg-6">
                        <div class="tg-header__top-right">
                            @if ($setting?->header_social_status == 'active')
                                <ul class="tg-header__top-social list-wrap">
                                    <li>{{ __('Follow Us On') }} :</li>
                                    @foreach (getSocialLinks() as $socialLink)
                                        <li class="header-social">
                                            <a href="{{ $socialLink->link }}" target="_blank">
                                                <img src="{{ asset($socialLink->icon) }}" alt="img">
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            <div class="header_language_area d-flex flex-wrap d-none d-xl-flex">

                                <ul>
                                    @if (count(allLanguages()?->where('status', 1)) > 1)
                                        <form action="{{ route('set-language') }}" id="setLanguageHeader">
                                            <select name="code" class="select_js">
                                                @forelse (allLanguages()?->where('status', 1) as $language)
                                                    <option value="{{ $language->code }}"
                                                        {{ getSessionLanguage() == $language->code ? 'selected' : '' }}>
                                                        {{ $language->name }}
                                                    </option>
                                                @empty
                                                    <option value="en"
                                                        {{ getSessionLanguage() == 'en' ? 'selected' : '' }}>
                                                        {{ __('English') }}
                                                    </option>
                                                @endforelse
                                            </select>
                                        </form>
                                    @endif
                                    @if (count(allCurrencies()?->where('status', 'active')) > 1)
                                        <form action="{{ route('set-currency') }}" class="set-currency-header"
                                            method="GET">
                                            <select name="currency" class="change-currency select_js">
                                                @forelse (allCurrencies()?->where('status', 'active') as $currency)
                                                    <option value="{{ $currency->currency_code }}"
                                                        {{ getSessionCurrency() == $currency->currency_code ? 'selected' : '' }}>
                                                        {{ $currency->currency_name }}
                                                    </option>
                                                @empty
                                                    <option value="USD"
                                                        {{ getSessionCurrency() == 'USD' ? 'selected' : '' }}>
                                                        {{ __('USD') }}
                                                    </option>
                                                @endforelse
                                            </select>
                                        </form>
                                    @endif
                                </ul>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div id="header-fixed-height"></div>
    <div id="sticky-header" class="tg-header__area">
        <div class="container custom-container">
            <div class="row">
                <div class="col-12">
                    <div class="tgmenu__wrap">
                        <nav class="tgmenu__nav">
                            <div class="logo">
                                <a href="{{ route('home') }}"><img src="{{ $headerLogo }}"
                                        alt="{{ config('app.name', 'Skillvation') }}" onerror="this.onerror=null;this.src='{{ asset('designs/img/logo.png') }}';" style="max-height: 48px; width: auto; display: block;"></a>
                            </div>
                            <div class="tgmenu__navbar-wrap tgmenu__main-menu d-none d-xl-flex">
                                @if ($nav_menu)
                                    <ul class="navigation">
                                        @foreach ($nav_menu as $menu)
                                            @if ($menu['link'] == '/' && $setting?->show_all_homepage == 1)
                                                <li class="menu-item-has-children">
                                                    <a href="{{ url('/') }}"
                                                        title="">{{ __('Home') }}</a>
                                                    <ul class="sub-menu">
                                                        <li class=""><a
                                                                href="{{ route('change-theme', 'theme-one') }}"
                                                                title="">{{ __('Home One') }}</a></li>
                                                        <li class=""><a
                                                                href="{{ route('change-theme', 'theme-two') }}"
                                                                title="">{{ __('Home Two') }}</a></li>
                                                        <li class=""><a
                                                                href="{{ route('change-theme', 'theme-three') }}"
                                                                title="">{{ __('Home Three') }}</a></li>
                                                        <li class=""><a
                                                                href="{{ route('change-theme', 'theme-four') }}"
                                                                title="">{{ __('Home Four') }}</a></li>
                                                    </ul><!-- /.sub-menu -->
                                                </li>
                                            @else
                                                <li class="{{ !empty($menu['child']) ? 'menu-item-has-children mega-menu-parent' : '' }}">
                                                    <a href="{{ !empty($menu['child']) ? 'javascript:;' : url($menu['link']) }}"
                                                        title="">
                                                        <span>{{ $menu['label'] }}</span>
                                                    </a>
                                                    @if (!empty($menu['child']))
                                                        @php
                                                            $childCount = count($menu['child']);
                                                            $panelW     = $childCount <= 2 ? '460px' : ($childCount === 3 ? '680px' : '900px');
                                                            $colW       = $childCount <= 2 ? 'calc(50% - 8px)' : ($childCount === 3 ? 'calc(33.333% - 11px)' : 'calc(25% - 12px)');
                                                        @endphp

                                                        {{-- Desktop Mega image-card panel (Desktop only) --}}
                                                        <div class="mega-dropdown-panel" style="width:{{ $panelW }};">
                                                            <div class="mega-dropdown-header">
                                                                <span class="mega-dropdown-title">{{ $menu['label'] }}</span>
                                                                <span class="mega-dropdown-count">{{ $childCount }}</span>
                                                            </div>
                                                            <div class="mega-dropdown-grid">
                                                                @foreach ($menu['child'] as $child)
                                                                    @php
                                                                        $childPath  = '/' . ltrim($child['link'], '/');
                                                                        $labelKey   = strtolower(trim($child['label']));
                                                                        $childMeta  = $childImageMap[$childPath] ?? null;
                                                                        $childImg   = $childMeta['image'] ?? ($childLabelMap[$labelKey] ?? 'https://images.unsplash.com/photo-1516339901601-2e1b62dc0c45?auto=format&fit=crop&w=600&q=80');
                                                                        $isActive   = request()->is(ltrim($child['link'], '/'));
                                                                    @endphp
                                                                    <a href="{{ url($child['link']) }}"
                                                                       class="mega-card {{ $isActive ? 'mega-card--active' : '' }}"
                                                                       style="width:{{ $colW }};">
                                                                        <p class="mega-card__title">{{ $child['label'] }}</p>
                                                                        <div class="mega-card__img">
                                                                            <img src="{{ $childImg }}"
                                                                                 alt="{{ $child['label'] }}"
                                                                                 onerror="this.onerror=null;this.style.opacity='.3';" />
                                                                        </div>
                                                                    </a>
                                                                @endforeach
                                                            </div>
                                                        </div>

                                                        {{-- Mobile Text-Based Sub-Menu (for mobile slide drawer only) --}}
                                                        <ul class="sub-menu">
                                                            @foreach ($menu['child'] as $child)
                                                                <li><a href="{{ url($child['link']) }}">{{ $child['label'] }}</a></li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul><!-- /.menu -->
                                @endif

                            </div>
                            <div class="tgmenu__action">
                                <ul class="list-wrap">
                                    <li class="mini-cart-icon">
                                        <a href="{{ route('cart') }}" class="cart-count">
                                            <img src="{{ asset('frontend/img/icons/cart.svg') }}" class="injectable"
                                                alt="img">
                                            <span class="mini-cart-count">{{ Cart::content()->count() }}</span>
                                        </a>
                                    </li>
                                    <li class="mini-cart-icon user_icon">
                                        <a href="javascript:;" class="cart-count">
                                            <img src="{{ asset('frontend/img/icons/menu_user.svg') }}"
                                                alt="img">
                                        </a>
                                        <ul class="menu_user_list">
                                            @guest
                                                <li><a href="{{ route('login') }}">{{ __('Sign in') }}</a></li>
                                                <li><a href="{{ route('register') }}">{{ __('Sign Up') }}</a></li>
                                            @else
                                                @if (Auth::guard('web')->user())
                                                    @if (userAuth()->role == 'instructor')
                                                        <li><a
                                                                href="{{ route('instructor.dashboard') }}">{{ __('Instructor Dashboard') }}</a>
                                                        </li>
                                                    @elseif (userAuth()->role == 'school')
                                                        <li><a
                                                                href="{{ route('school.dashboard') }}">{{ __('School Dashboard') }}</a>
                                                        </li>
                                                    @else
                                                        <li><a
                                                                href="{{ route('student.dashboard') }}">{{ __('Student Dashboard') }}</a>
                                                        </li>
                                                    @endif
                                                    <li><a href="{{ route('sso.shop', ['target' => '/orders']) }}" target="_blank" style="color:#d97706; font-weight:600;"><i class="fas fa-shopping-bag mr-1"></i> {{ __('Club Shop & Orders') }}</a>
                                                    </li>
                                                    <li><a
                                                            href="{{ userAuth()->role == 'instructor' ? route('instructor.setting.index') : (userAuth()->role == 'school' ? route('school.profile.index') : route('student.setting.index')) }}">{{ __('Profile') }}</a>
                                                    </li>
                                                    <li><a
                                                            href="{{ userAuth()->role == 'instructor' ? route('instructor.courses.index') : (userAuth()->role == 'school' ? route('school.courses.index') : route('student.enrolled-courses')) }}">{{ __('Courses') }}</a>
                                                    </li>
                                                    <li><a href="javascript:void(0)"
                                                            class="text-danger logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('Logout') }}</a>
                                                    </li>
                                                @endif
                                            @endguest
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <div class="mobile-nav-toggler"><i class="tg-flaticon-menu-1"></i></div>
                        </nav>
                    </div>

                    <!-- Mobile Menu  -->
                    <div class="tgmobile__menu">
                        <nav class="tgmobile__menu-box">
                            <div class="close-btn"><i class="tg-flaticon-close-1"></i></div>
                            <div class="nav-logo">
                                <a href="{{ route('home') }}"><img src="{{ $headerLogo }}"
                                        alt="{{ config('app.name', 'Skillvation') }}" onerror="this.onerror=null;this.src='{{ asset('designs/img/logo.png') }}';" style="max-height: 40px; width: auto; display: block;"></a>
                            </div>

                            <div class="header_language_area d-flex flex-wrap">

                                <ul>
                                    @if (count(allLanguages()?->where('status', 1)) > 1)
                                        <form action="{{ route('set-language') }}"
                                            class="change-language-header-mobile" method="GET">
                                            <select name="code" class="select_js set-language-header-mobile">
                                                @forelse (allLanguages()?->where('status', 1) as $language)
                                                    <option value="{{ $language->code }}"
                                                        {{ getSessionLanguage() == $language->code ? 'selected' : '' }}>
                                                        {{ $language->name }}
                                                    </option>
                                                @empty
                                                    <option value="en"
                                                        {{ getSessionLanguage() == 'en' ? 'selected' : '' }}>
                                                        {{ __('English') }}
                                                    </option>
                                                @endforelse
                                            </select>
                                        </form>
                                    @endif
                                    @if (count(allCurrencies()?->where('status', 'active')) > 1)
                                        <form action="{{ route('set-currency') }}"
                                            class="change-currency-header-mobile" method="GET">
                                            <select name="currency" class="set-currency-header-mobile select_js">
                                                @forelse (allCurrencies()?->where('status', 'active') as $currency)
                                                    <option value="{{ $currency->currency_code }}"
                                                        {{ getSessionCurrency() == $currency->currency_code ? 'selected' : '' }}>
                                                        {{ $currency->currency_name }}
                                                    </option>
                                                @empty
                                                    <option value="USD"
                                                        {{ getSessionCurrency() == 'USD' ? 'selected' : '' }}>
                                                        {{ __('USD') }}
                                                    </option>
                                                @endforelse
                                            </select>
                                        </form>
                                    @endif
                                </ul>
                            </div>
                            @guest
                                <ul class="mobile_menu_login d-flex flex-wrap">
                                    <li><a href="{{ route('login') }}">{{ __('login') }}</a></li>
                                    <li><a href="{{ route('register') }}">{{ __('register') }}</a></li>
                                </ul>
                            @endguest
                            @auth
                                @if (Auth::guard('web')->user()->role == 'vendor')
                                    <ul class="mobile_menu_login d-flex flex-wrap">
                                        <li><a href="{{ route('sso.shop') }}?target=dashboard">{{ __('Vendor Dashboard') }}</a></li>
                                        <li><a href="javascript:void(0)" class="text-danger logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('Logout') }}</a></li>
                                    </ul>
                                @elseif (Auth::guard('web')->user()->role == 'instructor')
                                    <ul class="mobile_menu_login d-flex flex-wrap">
                                        <li><a href="{{ route('instructor.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                        <li><a href="{{ route('instructor.courses.index') }}">{{ __('Courses') }}</a></li>
                                        <li><a href="javascript:void(0)" class="text-danger logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('Logout') }}</a></li>
                                    </ul>
                                @elseif (Auth::guard('web')->user()->role == 'school')
                                    <ul class="mobile_menu_login d-flex flex-wrap">
                                        <li><a href="{{ route('school.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                        <li><a href="{{ route('school.courses.index') }}">{{ __('Courses') }}</a></li>
                                        <li><a href="javascript:void(0)" class="text-danger logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('Logout') }}</a></li>
                                    </ul>
                                @else
                                    <ul class="mobile_menu_login d-flex flex-wrap">
                                        <li><a href="{{ route('student.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                        <li><a href="{{ route('student.enrolled-courses') }}">{{ __('Courses') }}</a></li>
                                        <li><a href="javascript:void(0)" class="text-danger logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('Logout') }}</a></li>
                                    </ul>
                                @endif
                            @endauth

                            <!-- <div class="tgmobile__search">
                                <form action="{{ route('courses') }}">
                                    <select class="form-select w_150px" aria-label="Default select example"
                                        name="main_category">
                                        <option selected disabled>{{ __('Categories') }}</option>
                                        @foreach ($categories as $category)
                                            <option @selected(request('main_category') == $category->slug) value="{{ $category->slug }}">
                                                {{ $category->translation->name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" placeholder="{{ __('Search here') }}..." name="search">
                                    <button><i class="fas fa-search"></i></button>
                                </form>
                            </div> -->
                            <div class="tgmobile__menu-outer">
                                <!--Here Menu Will Come Automatically Via Javascript / Same Menu as in Header-->
                            </div>
                            <div class="social-links">
                                @if (count(getSocialLinks()) > 0)
                                    <ul class="list-wrap">
                                        @foreach (getSocialLinks() as $socialLink)
                                            <li>
                                                <a href="{{ $socialLink->link }}" target="_blank">
                                                    <img src="{{ asset($socialLink->icon) }}" alt="img">
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </nav>
                    </div>
                    <div class="tgmobile__menu-backdrop"></div>
                    <!-- End Mobile Menu -->

                    {{-- start admin logout form --}}
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                    {{-- end admin logout form --}}
                </div>
            </div>
        </div>
    </div>
</header>
<!-- header-area-end -->
<style>
/* ── Navigation bar alignment ── */
.tgmenu__nav {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    width: 100%;
}

@media (min-width: 1200px) {
    .tgmenu__navbar-wrap {
        display: flex !important;
        flex-grow: 1 !important;
        justify-content: center !important;
    }
    .tgmenu__navbar-wrap ul,
    .tgmenu__navbar-wrap ul.navigation {
        margin: 0 auto !important;
        justify-content: center !important;
    }
    .mega-menu-parent .sub-menu {
        display: none !important;
    }
    .tgmenu__main-menu li.menu-item-has-children > a::after {
        transition: transform 0.25s ease;
    }
    .tgmenu__main-menu li.menu-item-has-children:hover > a::after {
        transform: rotate(270deg) !important;
    }
}

@media (max-width: 1199.98px) {
    .tgmenu__navbar-wrap {
        display: none !important;
    }
    .mobile-nav-toggler {
        display: flex !important;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        color: var(--tg-theme-primary, #1976d2);
        cursor: pointer;
        padding: 6px;
        margin-left: 8px;
    }
    .tgmenu__action {
        display: none !important;
        align-items: center;
        margin: 0 0 0 auto !important;
    }
    .tgmenu__action > ul {
        display: flex !important;
        align-items: center;
        gap: 12px;
        margin: 0;
        padding: 0;
    }
    /* Strictly hide any image card panels on mobile */
    .mega-dropdown-panel,
    .tgmobile__menu .mega-dropdown-panel {
        display: none !important;
        visibility: hidden !important;
        height: 0 !important;
        max-height: 0 !important;
        overflow: hidden !important;
        opacity: 0 !important;
        pointer-events: none !important;
    }
}

/* ── Mega dropdown panel (Desktop) ──────── */
.mega-menu-parent {
    position: relative;
}

.mega-menu-parent > a {
    display: inline-flex !important;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
}

.mega-dropdown-panel {
    display: none;
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: #ffffff;
    border-radius: 24px;
    box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.18), 0 0 0 1px rgba(0, 0, 0, 0.04);
    border: 1px solid #f1f5f9;
    padding: 24px 28px 28px;
    z-index: 999;
    box-sizing: border-box;
    margin-top: 6px;
}

/* Hover bridge so mouse movement into dropdown is seamless */
.mega-dropdown-panel::after {
    content: '';
    position: absolute;
    top: -14px;
    left: 0;
    width: 100%;
    height: 14px;
    background: transparent;
}

/* Upward pointer arrow */
.mega-dropdown-panel::before {
    content: '';
    position: absolute;
    top: -8px;
    left: 50%;
    transform: translateX(-50%) rotate(45deg);
    width: 16px;
    height: 16px;
    background: #ffffff;
    border-left: 1px solid #e2e8f0;
    border-top: 1px solid #e2e8f0;
    border-radius: 3px 0 0 0;
    z-index: 10;
}

.mega-menu-parent:hover .mega-dropdown-panel,
.mega-menu-parent:focus-within .mega-dropdown-panel {
    display: block !important;
    animation: megaDropdownFade 0.2s ease forwards;
}

@keyframes megaDropdownFade {
    from {
        opacity: 0;
        transform: translate(-50%, 6px);
    }
    to {
        opacity: 1;
        transform: translate(-50%, 0);
    }
}

.mega-dropdown-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 18px;
    padding-bottom: 0;
    border-bottom: none;
}

.mega-dropdown-title {
    font-size: 18px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.01em;
}

.mega-dropdown-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 24px;
    height: 24px;
    padding: 0 8px;
    background: #ede9fe;
    color: #6366f1;
    border-radius: 9999px;
    font-size: 13px;
    font-weight: 700;
}

.mega-dropdown-grid {
    display: flex;
    flex-wrap: nowrap;
    gap: 16px;
}

/* ── Individual Card (Title on TOP, Photo on BOTTOM) ─────── */
.mega-card {
    display: flex;
    flex-direction: column;
    padding: 14px 14px 16px;
    border-radius: 18px;
    border: 1px solid #e2e8f0;
    text-decoration: none !important;
    background: #ffffff;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    box-sizing: border-box;
    flex: 1 1 0;
    min-width: 0;
}

.mega-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.1);
    transform: translateY(-3px);
}

.mega-card--active {
    border-color: #6366f1;
    box-shadow: 0 8px 20px -4px rgba(99, 102, 241, 0.15);
}

.mega-card__title {
    font-size: 14.5px;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 12px 2px;
    line-height: 1.25;
    transition: color 0.2s;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.mega-card:hover .mega-card__title {
    color: var(--tg-theme-primary, #1976d2);
}

.mega-card__img {
    width: 100%;
    aspect-ratio: 1 / 0.95;
    border-radius: 12px;
    overflow: hidden;
    background: #f1f5f9;
}

.mega-card__img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.35s ease;
    display: block;
}

.mega-card:hover .mega-card__img img {
    transform: scale(1.06);
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Clean up any cloned mega-dropdown panels from the mobile drawer
    function purgeMobileMegaPanels() {
        $('.tgmobile__menu .mega-dropdown-panel').remove();
    }
    purgeMobileMegaPanels();
    $(document).on('click', '.mobile-nav-toggler', function () {
        purgeMobileMegaPanels();
    });

    // Allow clicking parent link in mobile drawer to open submenu
    $(document).on('click', '.tgmobile__menu li.menu-item-has-children > a', function (e) {
        var href = $(this).attr('href');
        if (href === 'javascript:;' || href === 'javascript:void(0);' || href === '#' || href === '') {
            e.preventDefault();
            var $btn = $(this).siblings('.dropdown-btn');
            if ($btn.length) {
                $btn.trigger('click');
            } else {
                $(this).siblings('ul').slideToggle(300);
            }
        }
    });
});
</script>
