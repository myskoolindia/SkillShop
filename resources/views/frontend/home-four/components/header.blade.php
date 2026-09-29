@php
    $nav_menu = Cache::rememberForever('nav_menu', function () {
        return menuGetBySlug('nav-menu');
    });
    $setting = Cache::get('setting');

    // Image map for known child menu links — keyed by the link path
    $childImageMap = [
        '/'                 => ['image' => asset('designs/img/logo.png'),              'desc' => 'Back to homepage'],
        '/skill2school'     => ['image' => asset('designs/img/skill2school-2.jpeg'),   'desc' => 'School-wide skill curriculum'],
        '/upskill4teacher'  => ['image' => asset('designs/img/TTT-1.png'),             'desc' => 'Professional educator development'],
        '/ttt'              => ['image' => asset('designs/img/TTT-1.png'),             'desc' => 'Master trainer & bootcamp program'],
        '/shop'             => ['image' => asset('frontend/img/skillbox/skillbox_banner.png'), 'desc' => 'Interactive kits & learning boxes'],
        '/SkillBox'         => ['image' => asset('frontend/img/skillbox/skillbox_banner.png'), 'desc' => 'Interactive kits & learning boxes'],
        '/labs'                 => ['image' => 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=400&q=80', 'desc' => 'Hands-on innovation labs'],
        '/labs/composite-skill' => ['image' => 'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=400&q=80', 'desc' => '3D design, making & future-ready skills'],
        '/labs/stem'            => ['image' => 'https://images.unsplash.com/photo-1516339901601-2e1b62dc0c45?auto=format&fit=crop&w=400&q=80', 'desc' => 'Electronics, IoT & project-based learning'],
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
            $isActive = $menu['link'] !== '#' && (
              request()->is(ltrim($menu['link'], '/')) || 
              (request()->path() === '/' && $menu['link'] === '/') ||
              request()->fullUrlIs($linkUrl)
            );
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
              <button type="button" class="nav-link inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold transition-colors duration-200 {{ $isActive ? 'text-primary bg-blue-50/60 font-bold' : 'text-slate-600 hover:text-primary hover:bg-slate-50' }}">
                <span>{{ $menu['label'] }}</span>
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
                        $childMeta   = $childImageMap[$childPath] ?? null;
                        $childImage  = $childMeta['image'] ?? 'https://images.unsplash.com/photo-1516339901601-2e1b62dc0c45?auto=format&fit=crop&w=400&q=80';
                        $childDesc   = $childMeta['desc'] ?? '';
                        $childActive = request()->is(ltrim($child['link'], '/'));
                      @endphp
                      <a href="{{ url($child['link']) }}"
                         class="group/item flex flex-col gap-3 p-3 rounded-xl border transition-all duration-200
                                {{ $childActive ? 'border-primary shadow-md bg-blue-50/30' : 'border-slate-200 hover:border-primary hover:shadow-md bg-white' }}">
                        <div class="w-full aspect-[4/3] rounded-lg overflow-hidden bg-slate-100">
                          <img src="{{ $childImage }}"
                               alt="{{ $child['label'] }}"
                               class="w-full h-full object-cover group-hover/item:scale-105 transition-transform duration-300"
                               onerror="this.onerror=null;this.style.display='none';" />
                        </div>
                        <div>
                          <p class="text-sm font-bold text-slate-900 group-hover/item:text-primary transition-colors leading-tight">
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
            <a class="nav-link px-3 py-2 rounded-lg text-sm font-semibold transition-colors duration-200 {{ $isActive ? 'text-primary bg-blue-50/60 font-bold' : 'text-slate-600 hover:text-primary hover:bg-slate-50' }}"
              href="{{ $linkUrl }}">
              {{ $menu['label'] }}
            </a>
          @endif
        @endforeach
      @else
        <!-- Fallback if menu is empty -->
        <a class="nav-link px-3 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:text-primary" href="{{ route('home') }}">Home</a>
        <a class="nav-link px-3 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:text-primary" href="{{ route('skill2school') }}">Skill 2 Skool</a>
        <a class="nav-link px-3 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:text-primary" href="{{ route('upskill4teacher') }}">Upskill 4 Teacher</a>
        <a class="nav-link px-3 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:text-primary" href="{{ route('ttt') }}">TTT</a>
        <a class="nav-link px-3 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:text-primary" href="{{ route('labs') }}">LABS</a>
        <a class="nav-link px-3 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:text-primary" href="{{ route('courses') }}">Courses</a>
      @endif
    </nav>

    <!-- Auth Buttons & Action Area -->
    <div class="flex items-center gap-3 sm:gap-4">
      @auth
        @if (auth()->user()->role === 'school')
          <a href="{{ route('school.dashboard') }}" class="bg-primary hover:bg-primary-dark text-white px-4 sm:px-5 py-2 rounded-full text-xs sm:text-sm font-bold transition-all shadow-sm hover:shadow inline-flex items-center justify-center gap-1.5">
            <i class="fa-solid fa-graduation-cap text-xs"></i>
            <span>School Portal</span>
          </a>
        @elseif (auth()->user()->role === 'instructor')
          <a href="{{ route('instructor.dashboard') }}" class="bg-primary hover:bg-primary-dark text-white px-4 sm:px-5 py-2 rounded-full text-xs sm:text-sm font-bold transition-all shadow-sm hover:shadow inline-flex items-center justify-center gap-1.5">
            <i class="fa-solid fa-chalkboard-user text-xs"></i>
            <span>Instructor Portal</span>
          </a>
        @else
          <a href="{{ route('student.dashboard') }}" class="bg-primary hover:bg-primary-dark text-white px-4 sm:px-5 py-2 rounded-full text-xs sm:text-sm font-bold transition-all shadow-sm hover:shadow inline-flex items-center justify-center gap-1.5">
            <i class="fa-solid fa-user-graduate text-xs"></i>
            <span>My Learning</span>
          </a>
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
            $isActive = $menu['link'] !== '#' && (
              request()->is(ltrim($menu['link'], '/')) ||
              (request()->path() === '/' && $menu['link'] === '/') ||
              request()->fullUrlIs($linkUrl)
            );
          @endphp

          @if (!empty($menu['child']) && count($menu['child']) > 0)
            {{-- Accordion parent --}}
            <details class="group/mob">
              <summary class="flex items-center justify-between px-3 py-2.5 rounded-xl cursor-pointer select-none list-none text-sm font-semibold text-slate-700 hover:text-primary hover:bg-slate-50 transition-colors">
                <span>{{ $menu['label'] }}</span>
                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 group-open/mob:rotate-180 transition-transform duration-200"></i>
              </summary>
              <div class="ml-3 pl-3 border-l-2 border-slate-100 mt-0.5 mb-1 space-y-0.5">
                @foreach ($menu['child'] as $child)
                  <a href="{{ url($child['link']) }}"
                     class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:text-primary hover:bg-blue-50/60 transition-colors">
                    {{ $child['label'] }}
                  </a>
                @endforeach
              </div>
            </details>
          @else
            <a class="block px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors {{ $isActive ? 'text-primary bg-blue-50 font-bold' : 'text-slate-700 hover:text-primary hover:bg-slate-50' }}"
               href="{{ $linkUrl }}">
              {{ $menu['label'] }}
            </a>
          @endif
        @endforeach

      @else
        {{-- Fallback links when no CMS menu --}}
        @foreach([
          ['Home',             route('home')],
          ['Skill 2 Skool',    route('skill2school')],
          ['Upskill 4 Teacher',route('upskill4teacher')],
          ['TTT',              route('ttt')],
          ['Courses',          route('courses')],
        ] as [$label, $href])
          <a href="{{ $href }}"
             class="block px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-700 hover:text-primary hover:bg-slate-50 transition-colors {{ request()->fullUrlIs($href) ? 'text-primary bg-blue-50 font-bold' : '' }}">
            {{ $label }}
          </a>
        @endforeach
      @endif
    </div>

    {{-- Auth actions --}}
    <div class="mt-3 pt-3 border-t border-slate-100 space-y-2">
      @auth
        <a href="{{ auth()->user()->role === 'school'
              ? route('school.dashboard')
              : (auth()->user()->role === 'instructor'
                  ? route('instructor.dashboard')
                  : route('student.dashboard')) }}"
           class="flex items-center gap-2 px-3 py-2.5 rounded-xl bg-primary/10 text-primary text-sm font-bold hover:bg-primary/20 transition-colors">
          <i class="fa-solid fa-gauge text-xs"></i> Go to Dashboard
        </a>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit"
                  class="w-full flex items-center gap-2 px-3 py-2.5 rounded-xl text-red-500 text-sm font-bold hover:bg-red-50 transition-colors text-left">
            <i class="fa-solid fa-right-from-bracket text-xs"></i> Logout
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
