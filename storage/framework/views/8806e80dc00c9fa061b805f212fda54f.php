<?php if($setting->google_tagmanager_status == 'active'): ?>
    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', '<?php echo e($setting->google_tagmanager_id); ?>');
        // Initialize Data Layer
        window.dataLayer = window.dataLayer || [];
    </script>
    <!-- End Google Tag Manager -->
    
<?php endif; ?>

<!-- Facebook Pixel -->
<?php if($setting->pixel_status == 'active'): ?>
    <script>
        ! function(f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function() {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '<?php echo e($setting->pixel_app_id); ?>');
        fbq('track', 'PageView');
    </script>

    <noscript><img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id=<?php echo e($setting->pixel_app_id); ?>&ev=PageView&noscript=1" /></noscript>
<?php endif; ?>

<?php if($setting->google_analytic_status == 'active'): ?>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo e($setting->google_analytic_id); ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', '<?php echo e($setting->google_analytic_id); ?>');
    </script>
<?php endif; ?>
<?php if(customCode()?->header_javascript): ?>
    <script>
        "use strict";
        <?php echo customCode()->header_javascript; ?>

    </script>
<?php endif; ?>
<?php /**PATH /var/www/html/pro/resources/views/frontend/layouts/header-scripts.blade.php ENDPATH**/ ?>