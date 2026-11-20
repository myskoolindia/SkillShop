<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>@yield('meta_title', $setting->app_name)</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Custom Meta -->
    @stack('custom_meta')
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset($setting->favicon) }}">
    <!-- CSS here -->
    @include('frontend.layouts.styles')
    <!-- Tailwind CSS + Brand Config (home-four pages) -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script>
      tailwind.config = {
        darkMode: 'class',
        theme: {
          extend: {
            fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
            colors: {
              primary: '#1976d2',
              'primary-light': '#4791db',
              'primary-dark': '#115293',
              surface: '#ffffff',
              'surface-container': '#f9fafb',
              'on-surface': '#1f2937',
              'on-surface-variant': '#4b5563',
              outline: '#e5e7eb',
              brand: {
                navy: '#28246f', darknavy: '#1d1956',
                orange: '#f05f43', orangehover: '#e24e31',
                peach: '#ffe8dc', peachlight: '#fff4ee',
                lavender: '#ecebff', lavenderlight: '#f5f4ff',
                blue: '#e1ebfd', bluelight: '#f0f5fe',
              },
            },
            spacing: {
              'margin-mobile': '1rem',
              gutter: '2rem',
              'section-gap': '4rem',
            },
            maxWidth: {
              'container-max': '1280px',
            },
          },
        },
      };
    </script>
    <style>
      body { font-family: 'Plus Jakarta Sans', sans-serif; }
      .scroll-smooth { scroll-behavior: smooth; }
    </style>
    <!-- CustomCSS here -->
    @stack('styles')
    @if (customCode()?->css)
        <style>
            {!! customCode()->css !!}
        </style>
    @endif

    {{-- dynamic header scripts --}}
    @include('frontend.layouts.header-scripts')

    @php
        setEnrollmentIdsInSession();
        setInstructorCourseIdsInSession();
    @endphp
</head>

<body>
    @if ($setting->google_tagmanager_status == 'active')
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $setting->google_tagmanager_id }}"
                height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
    @endif

    @if ($setting->preloader_status == 1)
        <!--Preloader-->
        <div id="preloader">
            <div id="loader" class="loader">
                <div class="loader-container">
                    <div class="loader-icon"><img src="{{ asset($setting->preloader) }}" alt="Preloader">
                    </div>
                </div>
            </div>
        </div>
        <!--Preloader-end -->
    @endif

    <!-- Scroll-top -->
    <button class="scroll__top scroll-to-target" data-target="html">
        <i class="tg-flaticon-arrowhead-up"></i>
    </button>
    <!-- Scroll-top-end-->

    <!-- header-area -->
    @include('frontend.layouts.header')
    <!-- header-area-end -->

  <!-- Main Content -->
  <main class="flex-grow">
    @yield('contents')
  </main>

  <!-- modal-area -->
    @include('frontend.partials.modal')
    @include('frontend.instructor-dashboard.course.partials.add-new-section-modal')
    <!-- modal-area -->

    <!-- footer-area -->
    @include('frontend.layouts.footer')
    <!-- footer-area-end -->


    <!-- JS here -->
    @include('frontend.layouts.scripts')

    <!-- Language Translation Variables -->
    @include('global.dynamic-js-variables')

    <!-- Page specific js -->
    @if (session('registerUser') && $setting->google_tagmanager_status == 'active' && $marketing_setting?->register)
        @php
            $registerUser = session('registerUser');
            session()->forget('registerUser');
        @endphp
        <script>
            $(function() {
                dataLayer.push({
                    'event': 'newStudent',
                    'student_info': @json($registerUser)
                });
            });
        </script>
    @endif
  <!-- Dynamic JS Global Config -->
  <script>
    window.APP_CONFIG = {
      API_BASE_URL: "{{ url('/') }}",
      COURSES_API_URL: "{{ url('/api/collab-courses') }}",
      APP_API_BASE: "{{ rtrim(env('APP_API_BASE', 'https://collab.myskool.club'), '/') }}",
      IMAGE_BASE_URL: "{{ rtrim(env('APP_API_BASE', 'https://collab.myskool.club'), '/') }}",
      IMAGE_FALLBACK: "{{ asset('designs/img/TTT-1.png') }}",
      APP_URL: "{{ url('/') }}"
    };
  </script>

  @stack('scripts')
  @if (customCode()?->javascript)
      <script>
          "use strict";
          {!! customCode()->javascript !!}
      </script>
  @endif
</body>

</html>
