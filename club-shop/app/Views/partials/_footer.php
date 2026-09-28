<?php
$newsletterSettings = getSettingsUnserialized('newsletter');
echo view("partials/_modals", ['newsletterSettings' => $newsletterSettings]);

// Fetch dynamic footer data from Laravel DB
$footer_menu_one = getPortalMenuBySlug('footer-col-one');
$footer_menu_two = getPortalMenuBySlug('footer-col-two');
$footer_menu_three = getPortalMenuBySlug('footer-col-three');
$footer_settings = getPortalFooterSettings();
$social_links = getPortalSocialLinks();

// Fallbacks if menus are empty in DB
if (empty($footer_menu_one)) {
    $footer_menu_one = [
        ['label' => 'Home', 'link' => '/'],
        ['label' => 'Courses', 'link' => '/courses'],
        ['label' => 'AI & Robotics Lab', 'link' => '/labs/ai-robotics'],
        ['label' => 'STEM Lab', 'link' => '/labs/stem'],
        ['label' => 'ECEC Lab', 'link' => '/labs/ecec'],
        ['label' => 'Composite Skill Lab', 'link' => '/labs/composite-skill'],
    ];
}

if (empty($footer_menu_two)) {
    $footer_menu_two = [
        ['label' => 'Skill 2 Skool', 'link' => '/skill2school'],
        ['label' => 'TTT', 'link' => '/ttt'],
        ['label' => 'Upskill 4 Teacher', 'link' => '/upskill4teacher'],
        ['label' => 'Shop', 'link' => '/club-shop'],
        ['label' => 'Blog', 'link' => '/blog'],
        ['label' => 'Contact', 'link' => '/contact'],
    ];
}

if (empty($footer_menu_three)) {
    $footer_menu_three = [
        ['label' => 'Terms & Conditions', 'link' => '/terms-and-conditions'],
        ['label' => 'Privacy Policy', 'link' => '/privacy-policy'],
    ];
}
?>

<footer class="footer__area mt-0" style="margin-top: 0 !important;">
    <div class="footer__top">
        <div class="container">
            <div class="row">
                <!-- Col 1: Brand & Contact Info -->
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="footer__widget">
                        <div class="logo mb-35">
                            <a href="<?= mainPortalUrl('/'); ?>" class="d-inline-block px-3 py-2 rounded-3 bg-white shadow-sm" style="max-width: 220px;">
                                <img src="<?= mainPortalUrl('designs/img/logo.png'); ?>" alt="Skillvation" onerror="this.onerror=null;this.src='<?= getLogo(); ?>';" style="max-height: 40px; width: auto; display: block;">
                            </a>
                        </div>
                        <div class="footer__content">
                            <p><?= !empty($footer_settings?->footer_text) ? esc($footer_settings->footer_text) : (!empty($baseSettings->about_footer) ? $baseSettings->about_footer : "Skillvation is an educational ecosystem dedicated to empowering learners and schools through hands-on skills, experiential labs, and future-ready curriculums."); ?></p>
                            <ul class="list-wrap">
                                <li><?= !empty($footer_settings?->address) ? esc($footer_settings->address) : (!empty($baseSettings->contact_address) ? esc($baseSettings->contact_address) : 'India'); ?></li>
                                <?php if (!empty($footer_settings?->phone) || !empty($baseSettings->contact_phone)): ?>
                                    <li><?= esc($footer_settings?->phone ?? $baseSettings->contact_phone); ?></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Col 2: Useful Links (Dynamic Menu One) -->
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                    <div class="footer__widget">
                        <h4 class="footer__widget-title"><?= trans("useful_links") ?? "Useful Links"; ?></h4>
                        <div class="footer__link">
                            <ul class="list-wrap">
                                <?php foreach ($footer_menu_one as $mOne):
                                    $linkLower = strtolower(trim($mOne['link'] ?? ''));
                                    $labelLower = strtolower(trim($mOne['label'] ?? ''));
                                    $isShop = ($linkLower === 'shop' || $linkLower === '/shop' || $linkLower === 'club-shop' || $linkLower === '/club-shop' || $labelLower === 'shop');
                                    $url = $isShop ? langBaseUrl() : mainPortalUrl($mOne['link']);
                                ?>
                                    <li><a href="<?= $url; ?>"><?= esc($mOne['label']); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Col 3: Our Company (Dynamic Menu Two) -->
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                    <div class="footer__widget">
                        <h4 class="footer__widget-title"><?= trans("our_company") ?? "Our Company"; ?></h4>
                        <div class="footer__link">
                            <ul class="list-wrap">
                                <?php foreach ($footer_menu_two as $mTwo):
                                    $linkLower = strtolower(trim($mTwo['link'] ?? ''));
                                    $labelLower = strtolower(trim($mTwo['label'] ?? ''));
                                    $isShop = ($linkLower === 'shop' || $linkLower === '/shop' || $linkLower === 'club-shop' || $linkLower === '/club-shop' || $labelLower === 'shop');
                                    $url = $isShop ? langBaseUrl() : mainPortalUrl($mTwo['link']);
                                ?>
                                    <li><a href="<?= $url; ?>"><?= esc($mTwo['label']); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Col 4: Get In Touch -->
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="footer__widget">
                        <h4 class="footer__widget-title"><?= trans("get_in_touch") ?? "Get In Touch"; ?></h4>
                        <div class="footer__contact-content">
                            <p><?= !empty($footer_settings?->get_in_touch_text) ? esc($footer_settings->get_in_touch_text) : "Connect with us for partnerships, lab setups, school implementations, and educator training."; ?></p>
                            <ul class="list-wrap footer__social">
                                <?php if (!empty($social_links)):
                                    foreach ($social_links as $sLink): ?>
                                        <li>
                                            <a href="<?= esc($sLink['link']); ?>" target="_blank">
                                                <img src="<?= mainPortalUrl($sLink['icon']); ?>" alt="Social">
                                            </a>
                                        </li>
                                    <?php endforeach;
                                else: ?>
                                    <li><a href="https://facebook.com" target="_blank"><img src="<?= mainPortalUrl('frontend/img/icons/facebook.svg'); ?>" alt="Facebook"></a></li>
                                    <li><a href="https://twitter.com" target="_blank"><img src="<?= mainPortalUrl('frontend/img/icons/twitter.svg'); ?>" alt="Twitter"></a></li>
                                    <li><a href="https://instagram.com" target="_blank"><img src="<?= mainPortalUrl('frontend/img/icons/instagram.svg'); ?>" alt="Instagram"></a></li>
                                    <li><a href="https://youtube.com" target="_blank"><img src="<?= mainPortalUrl('frontend/img/icons/youtube.svg'); ?>" alt="YouTube"></a></li>
                                    <li><a href="https://whatsapp.com" target="_blank"><img src="<?= mainPortalUrl('frontend/img/icons/whatsapp.svg'); ?>" alt="WhatsApp"></a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Bottom -->
    <div class="footer__bottom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <div class="copy-right-text">
                        <p>© <?= date('Y'); ?> <?= esc($generalSettings->application_name ?? 'Skillvation'); ?>. All rights reserved.</p>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="footer__bottom-menu">
                        <ul class="list-wrap">
                            <?php foreach ($footer_menu_three as $mThree): ?>
                                <li><a href="<?= mainPortalUrl($mThree['link']); ?>"><?= esc($mThree['label']); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<?php if (empty(helperGetCookie('cks_warning')) && $baseSettings->cookies_warning): ?>
<div class="cookies-warning">
<button type="button" aria-label="close" class="close" onclick="hideCookiesWarning();"><i class="icon-close"></i></button>
<div class="text">
<?= $baseSettings->cookies_warning_text; ?>
</div>
<button type="button" class="btn btn-md btn-block" aria-label="close" onclick="hideCookiesWarning();"><?= trans("accept_cookies"); ?></button>
</div>
<?php endif; ?>

<button type="button" class="scrollup" aria-label="scroll-up"><i class="icon-arrow-up"></i></button>
<script src="<?= base_url('assets/js/jquery-3.5.1.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/plugins-2.6.js'); ?>"></script>
<script src="<?= base_url('assets/common/js/utils.min.js'); ?>"></script>
<?= view("cart/_payment_js.php"); ?>
<script src="<?= base_url('assets/js/script-2.6.2.min.js'); ?>"></script>
<script>$('<input>').attr({type: 'hidden', name: 'sysLangId', value: '<?=selectedLangId(); ?>'}).appendTo('form[method="post"]');</script>
<?php if ($generalSettings->pwa_status == 1): ?>
<script>if ('serviceWorker' in navigator) {window.addEventListener('load', function () {navigator.serviceWorker.register('<?= base_url('pwa-sw.js');?>').then(function (registration) {}, function (err) {console.log('ServiceWorker registration failed: ', err);}).catch(function (err) {console.log(err);});});} else {console.log('service worker is not supported');}</script>
<?php endif; ?>
<?php if (!empty($video) || !empty($audio)): ?>
<script src="<?= base_url('assets/vendor/plyr/plyr.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendor/plyr/plyr.polyfilled.min.js'); ?>"></script>
<script>
$(document).bind('ready ajaxComplete', function () {
const player = new Plyr('#player');
const audio_player = new Plyr('#audio_player');
});
$(document).ready(function () {
setTimeout(function () {
$(".product-video-preview").css("opacity", "1");
}, 300);
setTimeout(function () {
$(".product-audio-preview").css("opacity", "1");
}, 300);
});</script>
<?php endif;
if (!empty($loadSupportEditor)):
echo view('support/_editor');
endif; ?>
<?php if (checkNewsletterModal($newsletterSettings)): ?>
<script>$(window).on('load', function () {
$('#modal_newsletter').modal('show');
});</script>
<?php endif; ?>
<?= $generalSettings->google_analytics; ?>
<?= $generalSettings->custom_footer_codes; ?>
</body>
</html>