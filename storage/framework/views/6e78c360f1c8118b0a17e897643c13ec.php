<script src="<?php echo e(asset('global/js/jquery-3.7.1.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/js/proper.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/js/bootstrap.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/js/imagesloaded.pkgd.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/js/jquery.magnific-popup.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/js/jquery.odometer.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/js/jquery.appear.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/js/tween-max.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/js/select2.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/js/swiper-bundle.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/js/jquery.marquee.min.js')); ?>"></script>
<?php if($setting?->cursor_dot_status == 'active'): ?>
<script src="<?php echo e(asset('frontend/js/tg-cursor.min.js')); ?>"></script>
<?php endif; ?>
<script src="<?php echo e(asset('frontend/js/svg-inject.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/js/jquery.circleType.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/js/jquery.lettering.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/js/bootstrap-datepicker.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/js/plyr.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/js/wow.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/js/aos.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/js/vivus.min.js')); ?>"></script>
<script src="<?php echo e(asset('global/toastr/toastr.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/js/sweetalert.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/js/tinymce/js/tinymce/tinymce.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/js/default/frontend.js')); ?>?v=<?php echo e($setting?->version); ?>"></script>
<script src="<?php echo e(asset('frontend/js/default/cart.js')); ?>?v=<?php echo e($setting?->version); ?>"></script>
<script src="<?php echo e(asset('global/nice-select/jquery.nice-select.min.js')); ?>"></script>
<!-- File Manager js-->
<script src="<?php echo e(url('/vendor/laravel-filemanager/js/stand-alone-button.js')); ?>"></script>


<script src="<?php echo e(asset('frontend/js/main.js')); ?>?v=<?php echo e($setting?->version); ?>"></script>

<script>
    $('.file-manager').filemanager('file', {
        prefix: '<?php echo e(url('/frontend-filemanager')); ?>'
    });
    $('.file-manager-image').filemanager('image', {
        prefix: '<?php echo e(url('/frontend-filemanager')); ?>'
    });

    SVGInject(document.querySelectorAll("img.injectable"));
</script>

<!-- dynamic Toastr Notification -->
<script>
    "use strict";
    toastr.options.closeButton = true;
    toastr.options.progressBar = true;
    toastr.options.positionClass = 'toast-bottom-right';

    <?php $__sessionArgs = ['messege'];
if (session()->has($__sessionArgs[0])) :
if (isset($value)) { $__sessionPrevious[] = $value; }
$value = session()->get($__sessionArgs[0]); ?>
    var type = "<?php echo e(Session::get('alert-type', 'info')); ?>"
    switch (type) {
        case 'info':
            toastr.info("<?php echo e($value); ?>");
            break;
        case 'success':
            toastr.success("<?php echo e($value); ?>");
            break;
        case 'warning':
            toastr.warning("<?php echo e($value); ?>");
            break;
        case 'error':
            toastr.error("<?php echo e($value); ?>");
            break;
    }
    <?php unset($value);
if (isset($__sessionPrevious) && !empty($__sessionPrevious)) { $value = array_pop($__sessionPrevious); }
if (isset($__sessionPrevious) && empty($__sessionPrevious)) { unset($__sessionPrevious); }
endif;
unset($__sessionArgs); ?>

    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        orientation: "bottom auto"
    });
</script>


<!-- Toastr -->
<?php if($errors->any()): ?>
    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <script>
            toastr.error('<?php echo e($error); ?>', null, {
                timeOut: 10000
            });
        </script>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>


<!-- Google reCAPTCHA -->
<?php if(Cache::get('setting')->recaptcha_status === 'active'): ?>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif; ?>

<!-- tawk -->
<?php if($setting->tawk_status == 'active'): ?>
    <script type="text/javascript">
        "use strict";
        var Tawk_API = Tawk_API || {},
            Tawk_LoadStart = new Date();
        (function() {
            var s1 = document.createElement("script"),
                s0 = document.getElementsByTagName("script")[0];
            s1.async = true;
            s1.src = '<?php echo e($setting->tawk_chat_link); ?>';
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            s0.parentNode.insertBefore(s1, s0);
        })();
    </script>
<?php endif; ?>

<!-- Cookie Consent -->
<?php if($setting->cookie_status == 'active'): ?>
    <script src="<?php echo e(asset('frontend/js/cookieconsent.min.js')); ?>"></script>

    <script>
        "use strict";
        window.addEventListener("load", function() {
            window.wpcc.init({
                "border": "<?php echo e($setting->border); ?>",
                "corners": "<?php echo e($setting->corners); ?>",
                "colors": {
                    "popup": {
                        "background": "<?php echo e($setting->background_color); ?>",
                        "text": "<?php echo e($setting->text_color); ?> !important",
                        "border": "<?php echo e($setting->border_color); ?>"
                    },
                    "button": {
                        "background": "<?php echo e($setting->btn_bg_color); ?>",
                        "text": "<?php echo e($setting->btn_text_color); ?>"
                    }
                },
                "content": {
                    "href": "<?php echo e(url($setting->link)); ?>",
                    "message": "<?php echo e($setting->message); ?>",
                    "link": "<?php echo e($setting->link_text); ?>",
                    "button": "<?php echo e($setting->btn_text); ?>"
                }
            })
        });
    </script>
<?php endif; ?>

<script>
    if ($(".marquee_mode").length) {
        $('.marquee_mode').marquee({
            speed: 20,
            gap: 35,
            delayBeforeStart: 0,
            direction: "<?php echo e(Session::has('text_direction') && Session::get('text_direction') == 'rtl' ? 'right' : 'left'); ?>",
            duplicated: true,
            pauseOnHover: true,
            startVisible: true,
        });
    }
</script>
<?php /**PATH /var/www/html/pro/resources/views/frontend/layouts/scripts.blade.php ENDPATH**/ ?>