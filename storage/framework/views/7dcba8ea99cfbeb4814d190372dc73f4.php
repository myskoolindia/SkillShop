<?php
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
?>

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
                            <a href="<?php echo e(route('home')); ?>" class="d-inline-block px-3 py-2 rounded-3 bg-white shadow-sm" style="max-width: 220px;">
                                <img src="<?php echo e($footerLogo); ?>" alt="<?php echo e(config('app.name', 'Skillvation')); ?>" onerror="this.onerror=null;this.src='<?php echo e(asset('designs/img/logo.png')); ?>';" style="max-height: 40px; width: auto; display: block;">
                            </a>
                        </div>
                        <div class="footer__content">
                            <p><?php echo e($footerSetting?->footer_text); ?></p>
                            <ul class="list-wrap">
                                <li><?php echo e($footerSetting?->address); ?></li>
                                <?php if($footerSetting?->address2): ?>
                                <li><?php echo e($footerSetting->address2); ?></li>
                                <?php endif; ?>
                                <li><?php echo e($footerSetting?->phone); ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                    <?php if(count($footer_menu_one) > 0): ?>
                    <div class="footer__widget">
                        <h4 class="footer__widget-title"><?php echo e(__('Useful Links')); ?></h4>
                        <div class="footer__link">
                            <ul class="list-wrap">
                                <?php $__currentLoopData = $footer_menu_one; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $footerMenuOne): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><a href="<?php echo e(url($footerMenuOne['link'])); ?>"><?php echo e($footerMenuOne['label']); ?></a></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                    <?php if(count($footer_menu_two) > 0): ?>
                    <div class="footer__widget">
                        <h4 class="footer__widget-title"><?php echo e(__('Our Company')); ?></h4>
                        <div class="footer__link">
                            <ul class="list-wrap">
                                <?php $__currentLoopData = $footer_menu_two; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $footerMenuTwo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><a href="<?php echo e(url($footerMenuTwo['link'])); ?>"><?php echo e($footerMenuTwo['label']); ?></a></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="footer__widget">
                        <h4 class="footer__widget-title"><?php echo e(__('Get In Touch')); ?></h4>
                        <div class="footer__contact-content">
                            <p><?php echo e($footerSetting?->get_in_touch_text); ?></p>
                            <ul class="list-wrap footer__social">
                                <?php $__currentLoopData = getSocialLinks(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $socialLink): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li>
                                        <a href="<?php echo e($socialLink->link); ?>" target="_blank">
                                            <img src="<?php echo e(asset($socialLink->icon)); ?>" alt="img">
                                        </a>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                        <div class="app-download">
                            <?php if($footerSetting?->google_play_link): ?>
                                <a href="<?php echo e($footerSetting->google_play_link); ?>"><img
                                        src="<?php echo e(asset('frontend/img/others/google-play.svg')); ?>" alt="img"></a>
                            <?php endif; ?>
                            <?php if($footerSetting?->apple_store_link): ?>
                                <a href="<?php echo e($footerSetting->apple_store_link); ?>"><img
                                        src="<?php echo e(asset('frontend/img/others/apple-store.svg')); ?>" alt="img"></a>
                            <?php endif; ?>
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
                        <?php if(Cache::get('setting')->copyright_text): ?>
                        <p>© <?php echo e(Cache::get('setting')->copyright_text); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="footer__bottom-menu">
                        <ul class="list-wrap">
                            <?php $__currentLoopData = $footer_menu_three; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $footerMenuThree): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><a href="<?php echo e(url($footerMenuThree['link'])); ?>"><?php echo e($footerMenuThree['label']); ?></a></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<?php /**PATH /var/www/html/pro/resources/views/frontend/layouts/footer.blade.php ENDPATH**/ ?>