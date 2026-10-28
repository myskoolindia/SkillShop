<link rel="stylesheet" href="<?php echo e(asset('frontend/css/bootstrap.min.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('frontend/css/animate.min.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('frontend/css/magnific-popup.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('frontend/css/fontawesome-all.min.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('frontend/css/flaticon-skillgro.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('frontend/css/swiper-bundle.min.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('frontend/css/default-icons.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('frontend/css/select2.min.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('frontend/css/odometer.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('frontend/css/aos.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('frontend/css/plyr.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('frontend/css/spacing.css')); ?>">
<?php if($setting?->cursor_dot_status == 'active'): ?>
    <link rel="stylesheet" href="<?php echo e(asset('frontend/css/tg-cursor.css')); ?>">
<?php endif; ?>
<link rel="stylesheet" href="<?php echo e(asset('frontend/css/bootstrap-datepicker.min.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('global/toastr/toastr.min.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('global/nice-select/nice-select.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('frontend/css/main.css')); ?>?v=<?php echo e($setting?->version); ?>">
<link rel="stylesheet" href="<?php echo e(asset('frontend/css/frontend.css')); ?>?v=<?php echo e($setting?->version); ?>">

<?php if(Session::has('text_direction') && Session::get('text_direction') == 'rtl'): ?>
    <!-- RTL CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('frontend/css/rtl.css')); ?>?v=<?php echo e($setting?->version); ?>">
<?php endif; ?>


<style>
    :root {
        --tg-theme-primary: <?php echo e($setting->primary_color); ?>;
        --tg-theme-secondary: <?php echo e($setting->secondary_color); ?>;
        --tg-common-color-blue: <?php echo e($setting->common_color_one); ?>;
        --tg-common-color-blue-2: <?php echo e($setting->common_color_two); ?>;
        --tg-common-color-dark: <?php echo e($setting->common_color_three); ?>;
        --tg-common-color-black: <?php echo e($setting->common_color_four); ?>;
        --tg-common-color-dark-2: <?php echo e($setting->common_color_five); ?>;
    }
</style>
<?php /**PATH /var/www/html/pro/resources/views/frontend/layouts/styles.blade.php ENDPATH**/ ?>