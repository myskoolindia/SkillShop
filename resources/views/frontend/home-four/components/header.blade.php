@php
    $nav_menu = Cache::rememberForever('nav_menu', function () {
        return menuGetBySlug('nav-menu');
    });
    $setting = Cache::get('setting');
    // Image map for known child menu links — keyed by the link path
    $childImageMap = [
        '/'                 => ['image' => asset('designs/img/logo.png'),              'desc' => 'Back to homepage'],
        '/skill2school'     => ['image' => asset('designs/img/skill2school-2.jpeg'),   'desc' => 'School-wide skill curriculum'],
        '/upskill4school'   => ['image' => asset('designs/img/TTT-1.png'),             'desc' => 'Institutional & school development'],
        '/ttt'              => ['image' => asset('designs/img/TTT-1.png'),             'desc' => 'Master trainer & bootcamp program'],
        '/shop'             => ['image' => asset('frontend/img/skillbox/skillbox_banner.png'), 'desc' => 'Interactive kits & learning boxes'],
        '/SkillBox'         => ['image' => asset('frontend/img/skillbox/skillbox_banner.png'), 'desc' => 'Interactive kits & learning boxes'],
        '/labs'                       => ['image' => 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=400&q=80', 'desc' => 'Hands-on innovation labs'],
        '/labs/composite-skill'       => ['image' => 'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=400&q=80', 'desc' => '3D design, making & future-ready skills'],
        '/labs/composite-skill-basic' => ['image' => 'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=400&q=80', 'desc' => 'Composite Skill Lab'],
        '/labs/stem'                  => ['image' => 'https://images.unsplash.com/photo-1516339901601-2e1b62dc0c45?auto=format&fit=crop&w=400&q=80', 'desc' => 'Skillvation Lab'],
        '/labs/ai-robotics'     => ['image' => 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=400&q=80', 'desc' => 'Hands-on robotics, coding & AI experiments'],
        '/labs/ecec'            => ['image' => 'https://images.unsplash.com/photo-1581092335397-9583fe92d232?auto=format&fit=crop&w=400&q=80', 'desc' => 'Early childhood exploration & creativity'],
        '/courses'              => ['image' => asset('designs/img/skill2school-3.png'),    'desc' => 'Browse all available courses'],
        '/blog'                 => ['image' => asset('designs/img/skill2school-4.jpeg'),   'desc' => 'Articles & insights'],
        '/contact'              => ['image' => asset('designs/img/skill2school-5.jpeg'),   'desc' => 'Get in touch with us'],
    ];

    $labLinks = [
        [
            'label' => 'AI & Robotics Lab',
            'route' => route('labs.ai-robotics'),
            'image' => 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=400&q=80',
            'desc'  => 'Hands-on robotics, coding & AI experiments',
        ],
        [
            'label' => 'STEM Lab',
            'route' => route('labs.stem'),
            'image' => 'https://images.unsplash.com/photo-1516339901601-2e1b62dc0c45?auto=format&fit=crop&w=400&q=80',
            'desc'  => 'Electronics, IoT & project-based learning',
        ],
        [
            'label' => 'ECEC Lab',
            'route' => route('labs.ecec'),
            'image' => 'https://images.unsplash.com/photo-1581092335397-9583fe92d232?auto=format&fit=crop&w=400&q=80',
            'desc'  => 'Early childhood exploration & creativity',
        ],
        [
            'label' => 'Composite Skill Lab',
            'route' => route('labs.composite-skill'),
            'image' => 'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=400&q=80',
            'desc'  => '3D design, making & future-ready skills',
        ],
    ];
@endphp

<!-- BEGIN: TopNavBar (Common CMS-Managed Header) -->
<header
  class="fixed top-0 w-full z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-sm transition-all duration-300"
  id="navbar">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex justify-between items-center">

    <!-- Logo -->
    <a class="flex items-center gap-2 transition-transform hover:scale-105" href="{{ route('home') }}">
      <img alt="{{ config('app.name', 'Skillvation') }}" class="h-8 sm:h-9 w-auto object-contain" src="{{ asset('designs/img/logo.png') }}" onerror="this.onerror=null;this.src='{{ asset($setting?->logo ?? 'frontend/img/logo/logo.svg') }}';" />
    </a>

    <!-- Desktop Navigation (CMS Dynamic Menu) -->
    <nav class="hidden lg:flex gap-1 xl:gap-2 items-center mx-auto justify-center" id="main-nav">
      @if ($nav_menu)
        @foreach ($nav_menu as $menu)
          @php
            $linkUrl = $menu['link'] === '#' ? 'javascript:void(0);' : url($menu['link']);
            $menuPath = trim($menu['link'] ?? '', '/');
            $isActive = $menu['link'] !== '#' && (
              request()->is($menuPath) || 
              ($menuPath === '' && request()->path() === '/') ||
              request()->fullUrlIs($linkUrl) ||
              ($menuPath !== '' && request()->is($menuPath . '/*'))
            );
            $hasActiveChild = false;
            if (!empty($menu['child'])) {
              foreach ($menu['child'] as $c) {
                $cPath = trim($c['link'] ?? '', '/');
                if (request()->is($cPath) || request()->fullUrlIs(url($c['link'])) || ($cPath !== '' && request()->is($cPath . '/*'))) {
                  $hasActiveChild = true;
                  break;
                }
              }
            }
            $isCurrent = $isActive || $hasActiveChild;
          @endphp

          @if (!empty($menu['child']) && count($menu['child']) > 0)
            <!-- Mega Dropdown — image card grid (same style as Labs) -->
            @php
              $childCount = count($menu['child']);
              // pick panel width based on child count
              $panelWidth = $childCount <= 2 ? 'w-[480px]' : ($childCount === 3 ? 'w-[660px]' : 'w-[880px]');
              $gridCols   = $childCount <= 2 ? 'grid-cols-2' : ($childCount === 3 ? 'grid-cols-3' : 'grid-cols-4');
            @endphp
            <div class="relative group">
              <button type="button" class="nav-link inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm transition-colors duration-200 {{ $isCurrent ? 'text-primary bg-blue-50/70 font-extrabold' : 'text-slate-600 hover:text-primary hover:bg-slate-50 font-semibold' }}" style="{{ $isCurrent ? 'font-weight: 800 !important; color: #1976d2 !important;' : '' }}">
                <span style="{{ $isCurrent ? 'font-weight: 800 !important;' : '' }}">{{ $menu['label'] }}</span>
                <i class="fa-solid fa-chevron-down text-[10px] opacity-70 group-hover:rotate-180 transition-transform duration-200"></i>
              </button>

              <div class="absolute left-1/2 -translate-x-1/2 top-full pt-3 {{ $panelWidth }} opacity-0 invisible
                          group-hover:opacity-100 group-hover:visible transition-all duration-200
                          translate-y-1 group-hover:translate-y-0 z-50">
                <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 p-6">

                  {{-- Panel header --}}
                  <div class="flex items-center justify-between mb-5">
                    <div>
                      <span class="text-base font-extrabold text-slate-900">{{ $menu['label'] }}</span>
                      <span class="ml-2 inline-flex items-center justify-center w-5 h-5 rounded-full bg-slate-100 text-slate-500 text-xs font-bold">{{ $childCount }}</span>
                    </div>
                    @if($menu['link'] !== '#')
                      <a href="{{ url($menu['link']) }}" class="text-xs font-bold text-primary hover:underline">View all →</a>
                    @endif
                  </div>

                  {{-- Image card grid --}}
                  <div class="grid {{ $gridCols }} gap-4">
                    @foreach ($menu['child'] as $child)
                      @php
                        $childPath   = '/' . ltrim($child['link'], '/');
                        $cPathTrim   = trim($child['link'] ?? '', '/');
                        $childMeta   = $childImageMap[$childPath] ?? null;
                        $childImage  = $childMeta['image'] ?? 'https://images.unsplash.com/photo-1516339901601-2e1b62dc0c45?auto=format&fit=crop&w=400&q=80';
                        $childDesc   = $childMeta['desc'] ?? '';
                        $childActive = request()->is($cPathTrim) || request()->fullUrlIs(url($child['link'])) || ($cPathTrim !== '' && request()->is($cPathTrim . '/*'));
                      @endphp
                      <a href="{{ url($child['link']) }}"
                         class="group/item flex flex-col gap-3 p-3 rounded-xl border transition-all duration-200
                                {{ $childActive ? 'border-primary ring-2 ring-primary/20 shadow-md bg-blue-50/30' : 'border-slate-200 hover:border-primary hover:shadow-md bg-white' }}">
                        <div class="w-full aspect-[4/3] rounded-lg overflow-hidden bg-slate-100">
                          <img src="{{ $childImage }}"
                               alt="{{ $child['label'] }}"
                               class="w-full h-full object-cover group-hover/item:scale-105 transition-transform duration-300"
                               onerror="this.onerror=null;this.style.display='none';" />
                        </div>
                        <div>
                          <p class="text-sm transition-colors leading-tight {{ $childActive ? 'font-extrabold text-primary' : 'font-bold text-slate-900 group-hover/item:text-primary' }}" style="{{ $childActive ? 'font-weight: 800 !important; color: #1976d2 !important;' : '' }}">
                            {{ $child['label'] }}
                          </p>
                          @if($childDesc)
                            <p class="text-xs text-slate-400 mt-0.5 leading-tight">{{ $childDesc }}</p>
                          @endif
                        </div>
                      </a>
                    @endforeach
                  </div>

                </div>
              </div>
            </div>
          @else
            <!-- Standard Menu Item -->
            <a class="nav-link px-3 py-2 rounded-lg text-sm transition-colors duration-200 {{ $isCurrent ? 'text-primary bg-blue-50/70 font-extrabold' : 'text-slate-600 hover:text-primary hover:bg-slate-50 font-semibold' }}"
              style="{{ $isCurrent ? 'font-weight: 800 !important; color: #1976d2 !important;' : '' }}"
              href="{{ $linkUrl }}">
              {{ $menu['label'] }}
            </a>
          @endif
        @endforeach
      @else
        <!-- Fallback if menu is empty -->
        @php
          $fallbackItems = [
            ['Home', route('home'), ''],
            ['Skill 2 Skool', route('skill2school'), 'skill2school*'],
            ['Upskill 4 School', route('upskill4school'), 'upskill4school*'],
            ['TTT', route('ttt'), 'ttt*'],
            ['Shop', url('/shop'), 'shop*'],
            ['Composite Skill Lab', url('/labs/composite-skill-basic'), 'labs/composite-skill-basic*'],
            ['Skillvation Lab', url('/labs/stem'), 'labs/stem*'],
            ['Courses', route('courses'), 'courses*'],
          ];
        @endphp
        @foreach ($fallbackItems as [$label, $href, $pattern])
          @php
            $fbActive = request()->fullUrlIs($href) || ($pattern && request()->is($pattern)) || ($href === route('home') && request()->path() === '/');
          @endphp
          <a class="nav-link px-3 py-2 rounded-lg text-sm transition-colors duration-200 {{ $fbActive ? 'text-primary bg-blue-50/70 font-extrabold' : 'text-slate-600 hover:text-primary hover:bg-slate-50 font-semibold' }}" style="{{ $fbActive ? 'font-weight: 800 !important; color: #1976d2 !important;' : '' }}" href="{{ $href }}">{{ $label }}</a>
        @endforeach
      @endif
    </nav>

    <!-- Auth Buttons & Action Area -->
    <div class="flex items-center gap-2 sm:gap-3">
      @auth
        @php
          $authUser = auth()->user();
          $hasPurchased = $authUser->hasPurchasedCourses();
        @endphp

        {{-- Cart Button --}}
        <a href="{{ route('cart') }}" class="relative p-2 text-slate-600 hover:text-primary rounded-lg hover:bg-slate-50 transition-colors inline-flex items-center justify-center" title="Cart">
          <i class="fa-solid fa-cart-shopping text-sm sm:text-base"></i>
          @if(\Cart::count() > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center">{{ \Cart::count() }}</span>
          @endif
        </a>

        {{-- Dashboard / Portal Button (shown ONLY if courses purchased, or admin/vendor/instructor) --}}
        @if ($authUser->role === 'vendor')
          <a href="{{ url('/club-shop/dashboard') }}" class="bg-primary hover:bg-primary-dark text-white px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-bold transition-all shadow-sm hover:shadow inline-flex items-center justify-center gap-1.5">
            <i class="fa-solid fa-store text-xs"></i>
            <span>Vendor Dashboard</span>
          </a>
        @elseif ($authUser->role === 'instructor')
          <a href="{{ route('instructor.dashboard') }}" class="bg-primary hover:bg-primary-dark text-white px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-bold transition-all shadow-sm hover:shadow inline-flex items-center justify-center gap-1.5">
            <i class="fa-solid fa-chalkboard-user text-xs"></i>
            <span>Instructor Portal</span>
          </a>
        @elseif ($authUser->role === 'school')
          @if ($hasPurchased)
            <a href="{{ route('roboquest.launch') }}" class="bg-primary hover:bg-primary-dark text-white px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-bold transition-all shadow-sm hover:shadow inline-flex items-center justify-center gap-1.5">
              <i class="fa-solid fa-gauge text-xs"></i>
              <span>Dashboard</span>
            </a>
          @endif
        @else
          {{-- Student / Teacher --}}
          @if ($hasPurchased)
            <a href="{{ route('roboquest.launch') }}" class="bg-primary hover:bg-primary-dark text-white px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-bold transition-all shadow-sm hover:shadow inline-flex items-center justify-center gap-1.5">
              <i class="fa-solid fa-gauge text-xs"></i>
              <span>Dashboard</span>
            </a>
          @endif
        @endif
        
        <form method="POST" action="{{ route('logout') }}" class="inline">
          @csrf
          <button type="submit" class="p-2 text-slate-400 hover:text-red-500 rounded-lg hover:bg-red-50 text-sm font-medium transition-colors" title="Logout">
            <i class="fa-solid fa-right-from-bracket"></i>
          </button>
        </form>
      @else
        <a href="{{ route('register') }}" class="hidden sm:inline-block text-primary font-bold hover:text-primary-dark transition-colors text-xs sm:text-sm px-2">
          Sign Up
        </a>
        <a href="{{ route('login') }}" style="background-color: #1976d2; color: #ffffff;" class="bg-primary hover:bg-primary-dark text-white px-4 sm:px-5 py-2 rounded-full text-xs sm:text-sm font-bold transition-all shadow-sm hover:shadow inline-flex items-center justify-center">
          Login
        </a>
      @endauth

      <!-- Mobile Hamburger Toggle -->
      <button
        class="lg:hidden flex flex-col justify-center gap-1.5 p-2 rounded-lg text-slate-700 hover:bg-slate-100 focus:outline-none w-9 h-9"
        aria-label="Toggle navigation menu"
        id="mobile-menu-btn"
        onclick="
          const menu = document.getElementById('mobile-menu');
          const open = menu.classList.toggle('hidden') === false;
          this.querySelector('#bar1').style.transform = open ? 'translateY(6px) rotate(45deg)' : '';
          this.querySelector('#bar2').style.opacity  = open ? '0' : '1';
          this.querySelector('#bar3').style.transform = open ? 'translateY(-6px) rotate(-45deg)' : '';
        ">
        <span id="bar1" class="block w-5 h-0.5 bg-slate-700 transition-all duration-300 origin-center"></span>
        <span id="bar2" class="block w-5 h-0.5 bg-slate-700 transition-all duration-300"></span>
        <span id="bar3" class="block w-5 h-0.5 bg-slate-700 transition-all duration-300 origin-center"></span>
      </button>
    </div>
  </div>

  <!-- Mobile Dropdown Menu -->
  <div class="lg:hidden hidden bg-white border-t border-slate-100 px-4 py-3 shadow-lg max-h-[80vh] overflow-y-auto" id="mobile-menu">

    <div class="space-y-0.5">

      {{-- CMS menu items --}}
      @if ($nav_menu)
        @foreach ($nav_menu as $menu)
          @php
            $linkUrl = $menu['link'] === '#' ? 'javascript:void(0);' : url($menu['link']);
            $menuPath = trim($menu['link'] ?? '', '/');
            $isActive = $menu['link'] !== '#' && (
              request()->is($menuPath) ||
              ($menuPath === '' && request()->path() === '/') ||
              request()->fullUrlIs($linkUrl) ||
              ($menuPath !== '' && request()->is($menuPath . '/*'))
            );
            $hasActiveChild = false;
            if (!empty($menu['child'])) {
              foreach ($menu['child'] as $c) {
                $cPath = trim($c['link'] ?? '', '/');
                if (request()->is($cPath) || request()->fullUrlIs(url($c['link'])) || ($cPath !== '' && request()->is($cPath . '/*'))) {
                  $hasActiveChild = true;
                  break;
                }
              }
            }
            $isCurrent = $isActive || $hasActiveChild;
          @endphp

          @if (!empty($menu['child']) && count($menu['child']) > 0)
            {{-- Accordion parent --}}
            <details class="group/mob" {{ $hasActiveChild ? 'open' : '' }}>
              <summary class="flex items-center justify-between px-3 py-2.5 rounded-xl cursor-pointer select-none list-none text-sm transition-colors {{ $isCurrent ? 'text-primary bg-blue-50/70 font-extrabold' : 'font-semibold text-slate-700 hover:text-primary hover:bg-slate-50' }}" style="{{ $isCurrent ? 'font-weight: 800 !important; color: #1976d2 !important;' : '' }}">
                <span style="{{ $isCurrent ? 'font-weight: 800 !important;' : '' }}">{{ $menu['label'] }}</span>
                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 group-open/mob:rotate-180 transition-transform duration-200"></i>
              </summary>
              <div class="ml-3 pl-3 border-l-2 border-slate-100 mt-0.5 mb-1 space-y-0.5">
                @foreach ($menu['child'] as $child)
                  @php
                    $cPathTrim   = trim($child['link'] ?? '', '/');
                    $childActive = request()->is($cPathTrim) || request()->fullUrlIs(url($child['link'])) || ($cPathTrim !== '' && request()->is($cPathTrim . '/*'));
                  @endphp
                  <a href="{{ url($child['link']) }}"
                     class="block px-3 py-2 rounded-lg text-sm transition-colors {{ $childActive ? 'text-primary bg-blue-50/80 font-extrabold' : 'font-semibold text-slate-600 hover:text-primary hover:bg-blue-50/60' }}"
                     style="{{ $childActive ? 'font-weight: 800 !important; color: #1976d2 !important;' : '' }}">
                    {{ $child['label'] }}
                  </a>
                @endforeach
              </div>
            </details>
          @else
            <a class="block px-3 py-2.5 rounded-xl text-sm transition-colors {{ $isCurrent ? 'text-primary bg-blue-50 font-extrabold' : 'text-slate-700 hover:text-primary hover:bg-slate-50 font-semibold' }}"
               style="{{ $isCurrent ? 'font-weight: 800 !important; color: #1976d2 !important;' : '' }}"
               href="{{ $linkUrl }}">
              {{ $menu['label'] }}
            </a>
          @endif
        @endforeach

      @else
        {{-- Fallback links when no CMS menu --}}
        @foreach([
          ['Home',                  route('home'), ''],
          ['Skill 2 Skool',         route('skill2school'), 'skill2school*'],
          ['Upskill 4 School',     route('upskill4school'), 'upskill4school*'],
          ['TTT',                   route('ttt'), 'ttt*'],
          ['Shop',                  url('/shop'), 'shop*'],
          ['Composite Skill Lab',   url('/labs/composite-skill-basic'), 'labs/composite-skill-basic*'],
          ['Skillvation Lab',       url('/labs/stem'), 'labs/stem*'],
          ['Courses',               route('courses'), 'courses*'],
        ] as [$label, $href, $pattern])
          @php
            $fbActive = request()->fullUrlIs($href) || ($pattern && request()->is($pattern)) || ($href === route('home') && request()->path() === '/');
          @endphp
          <a href="{{ $href }}"
             class="block px-3 py-2.5 rounded-xl text-sm transition-colors text-slate-700 hover:text-primary hover:bg-slate-50 font-semibold {{ $fbActive ? 'text-primary bg-blue-50 font-extrabold' : '' }}"
             style="{{ $fbActive ? 'font-weight: 800 !important; color: #1976d2 !important;' : '' }}">
            {{ $label }}
          </a>
        @endforeach
      @endif
    </div>

    {{-- Auth actions --}}
    <div class="mt-3 pt-3 border-t border-slate-100 space-y-2">
      @auth
        @php
          $mUser = auth()->user();
          $mHasPurchased = $mUser->hasPurchasedCourses();
        @endphp

        @if (!in_array($mUser->role, ['school', 'teacher', 'student'], true) || $mHasPurchased)
          <a href="{{ $mUser->role === 'vendor'
                ? url('/club-shop/dashboard')
                : ($mUser->role === 'instructor'
                    ? route('instructor.dashboard')
                    : route('roboquest.launch')) }}"
             class="flex items-center gap-2 px-3 py-2.5 rounded-xl bg-primary/10 text-primary text-sm font-bold hover:bg-primary/20 transition-colors">
            <i class="fa-solid fa-gauge text-xs"></i> {{ $mUser->role === 'vendor' ? 'Vendor Dashboard' : ($mUser->role === 'instructor' ? 'Instructor Portal' : 'Dashboard') }}
          </a>
        @endif

        <a href="{{ route('cart') }}"
           class="flex items-center justify-between px-3 py-2.5 rounded-xl text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-colors">
          <span class="flex items-center gap-2"><i class="fa-solid fa-cart-shopping text-xs"></i> {{ __('Cart') }}</span>
          @if(\Cart::count() > 0)
            <span class="bg-primary text-white text-xs px-2 py-0.5 rounded-full font-bold">{{ \Cart::count() }}</span>
          @endif
        </a>

        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit"
                  class="w-full flex items-center gap-2 px-3 py-2.5 rounded-xl text-red-500 text-sm font-bold hover:bg-red-50 transition-colors text-left">
            <i class="fa-solid fa-right-from-bracket text-xs"></i> {{ __('Logout') }}
          </button>
        </form>
      @else
        <a href="{{ route('register') }}"
           class="block px-3 py-2.5 rounded-xl text-sm font-bold text-primary hover:bg-blue-50 transition-colors">
          Sign Up
        </a>
        <a href="{{ route('login') }}"
           style="background-color:#1976d2;"
           class="flex items-center justify-center gap-2 w-full px-4 py-3 rounded-xl text-white text-sm font-bold shadow-sm hover:opacity-90 transition-opacity">
          <i class="fa-solid fa-right-to-bracket text-xs"></i> Login
        </a>
      @endauth
    </div>

  </div>
</header>
<!-- END: TopNavBar -->
