<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $__env->yieldContent('meta_title', $setting->app_name); ?></title>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Custom Meta -->
    <?php echo $__env->yieldPushContent('custom_meta'); ?>
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo e(asset($setting->favicon)); ?>">
    <!-- CSS here -->
    <?php echo $__env->make('frontend.layouts.styles', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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
    <?php echo $__env->yieldPushContent('styles'); ?>
    <?php if(customCode()?->css): ?>
        <style>
            <?php echo customCode()->css; ?>

        </style>
    <?php endif; ?>

    
    <?php echo $__env->make('frontend.layouts.header-scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php
        setEnrollmentIdsInSession();
        setInstructorCourseIdsInSession();
    ?>
</head>

<body>
    <?php if($setting->google_tagmanager_status == 'active'): ?>
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo e($setting->google_tagmanager_id); ?>"
                height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
    <?php endif; ?>

    <?php if($setting->preloader_status == 1): ?>
        <!--Preloader-->
        <div id="preloader">
            <div id="loader" class="loader">
                <div class="loader-container">
                    <div class="loader-icon"><img src="<?php echo e(asset($setting->preloader)); ?>" alt="Preloader">
                    </div>
                </div>
            </div>
        </div>
        <!--Preloader-end -->
    <?php endif; ?>

    <!-- Scroll-top -->
    <button class="scroll__top scroll-to-target" data-target="html">
        <i class="tg-flaticon-arrowhead-up"></i>
    </button>
    <!-- Scroll-top-end-->

    <!-- header-area -->
    <?php echo $__env->make('frontend.layouts.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <!-- header-area-end -->

  <!-- Main Content -->
  <main class="flex-grow">
    <?php echo $__env->yieldContent('contents'); ?>
  </main>

  <!-- modal-area -->
    <?php echo $__env->make('frontend.partials.modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('frontend.instructor-dashboard.course.partials.add-new-section-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <!-- modal-area -->

    <!-- footer-area -->
    <?php echo $__env->make('frontend.layouts.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <!-- footer-area-end -->


    <!-- JS here -->
    <?php echo $__env->make('frontend.layouts.scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!-- Language Translation Variables -->
    <?php echo $__env->make('global.dynamic-js-variables', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!-- Page specific js -->
    <?php if(session('registerUser') && $setting->google_tagmanager_status == 'active' && $marketing_setting?->register): ?>
        <?php
            $registerUser = session('registerUser');
            session()->forget('registerUser');
        ?>
        <script>
            $(function() {
                dataLayer.push({
                    'event': 'newStudent',
                    'student_info': <?php echo json_encode($registerUser, 15, 512) ?>
                });
            });
        </script>
    <?php endif; ?>
    <?php echo $__env->yieldPushContent('scripts'); ?>
    <?php if(customCode()?->javascript): ?>
        <script>
            "use strict";
            <?php echo customCode()->javascript; ?>

        </script>
    <?php endif; ?>

  <!-- Dynamic JS Global Config -->
  <script>
    window.APP_CONFIG = {
      API_BASE_URL: "<?php echo e(url('/')); ?>",
      COURSES_API_URL: "<?php echo e(url('/api/collab-courses')); ?>",
      IMAGE_BASE_URL: "",
      IMAGE_FALLBACK: "<?php echo e(asset('designs/img/TTT-1.png')); ?>",
      APP_URL: "<?php echo e(url('/')); ?>"
    };
  </script>

  <!-- <?php echo $__env->yieldPushContent('scripts'); ?> -->
</body>

</html>
<?php /**PATH /var/www/html/pro/resources/views/frontend/home-four/layouts/master.blade.php ENDPATH**/ ?>