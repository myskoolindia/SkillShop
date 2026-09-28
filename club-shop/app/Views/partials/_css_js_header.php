<?php
$cssOutput = '';
$cssOutput .= ':root{--mds-color-main:' . esc($generalSettings->site_color) . ';--mds-object-fit-mode:' . ($generalSettings->product_img_display_mode == 'full_image' ? 'contain' : 'cover') . ';--tg-theme-primary:#1363DF;--tg-theme-secondary:#FF8A00;--tg-heading-color:#1c1a4a;--tg-common-color-white:#ffffff;--tg-body-color:#555555;}';

if (!empty($indexBannersArray)) {
    foreach ($indexBannersArray as $bannerSet) {
        foreach ($bannerSet as $banner) {
            $cssOutput .= '.index_bn_' . $banner->id . '{-ms-flex:0 0 ' . $banner->banner_width . '%;flex:0 0 ' . $banner->banner_width . '%;max-width:' . $banner->banner_width . '%;}';
        }
    }
}

if (!empty($adSpaces)) {
    foreach ($adSpaces as $item) {
        if (!empty($item->desktop_width) && !empty($item->desktop_height)) {
            $cssOutput .= '.bn-ds-' . $item->id . '{width:' . $item->desktop_width . 'px;height:' . $item->desktop_height . 'px;}';
            $cssOutput .= '.bn-mb-' . $item->id . '{width:' . $item->mobile_width . 'px;height:' . $item->mobile_height . 'px;}';
        }
    }
}
$cssOutput .= '.product-card .price {white-space: nowrap;font-size: 0.938rem;}.product-card .discount-original-price {color: #868e96 !important;white-space: nowrap;font-size: 0.875rem !important;}.btn-cart-remove-mobile{display:none}@media (max-width:767px){.shopping-cart .item .list-item .product-title{line-height:24px}.shopping-cart .item{display:block!important;width:100%!important}.shopping-cart .item .cart-item-quantity{display:flex!important;width:100%!important;padding-left:80px;margin-top:10px;gap:15px}.product-image-box-md{height:70px;width:70px}.shopping-cart .number-spinner{height:40px;width:120px}.shopping-cart .number-spinner input{height:40px;padding:8px 4px!important}.shopping-cart .number-spinner button{height:40px;width:40px}.btn-cart-remove{display:none!important}.btn-cart-remove-mobile{padding:0;display:flex;align-items:center;justify-content:center;width:40px;height:40px;margin-top:0!important}.btn-cart-remove-mobile i{margin:0!important}.btn-cart-remove-mobile span{display:none}.product-delivery-est .item{margin-top:5px}[dir=rtl] .shopping-cart .item .cart-item-quantity{padding-left:0;padding-right:80px}}';
$cssOutput .= '.mega-menu .mega-menu-content,.mega-menu .dropdown-menu-large{max-height: 600px;overflow-y:auto !important;} .is-invalid-stars i {color: #EF4444 !important;} .btn-edit-product{display: inline-flex;align-items: center;justify-content: center;font-size: 10px;padding: 0;width: 20px;height: 20px;color: #666 !important;border-radius: 5px;position:relative;top:-1px;}.profile-actions-shipping a {padding: 8px 12px;}';

// Exact Main Portal Navigation & Header CSS
$cssOutput .= '
body { font-family: "Plus Jakarta Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important; }

/* ── Main Portal Header Area ── */
.tg-header__area {
    background: #ffffff;
    padding: 0;
    position: relative;
    z-index: 999;
    border-bottom: 1px solid #f0f0f0;
}
.tgmenu__wrap {
    position: relative;
}
.tgmenu__nav {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    width: 100%;
    min-height: 80px;
}
.tgmenu__nav .logo {
    display: flex;
    align-items: center;
}
.tgmenu__nav .logo img {
    max-height: 48px;
    width: auto;
    display: block;
}

/* ── Navigation Menu Items ── */
.tgmenu__navbar-wrap {
    display: flex !important;
    flex-grow: 1 !important;
    justify-content: center !important;
}
.tgmenu__navbar-wrap ul.navigation {
    display: flex !important;
    align-items: center;
    list-style: none;
    margin: 0 auto !important;
    padding: 0;
    gap: 0;
}
.tgmenu__navbar-wrap ul.navigation > li {
    display: block;
    position: relative;
    list-style: none;
    padding: 0 4px;
}
.tgmenu__navbar-wrap ul.navigation > li > a {
    font-size: 15.5px;
    font-weight: 500;
    color: #1c1a4a !important;
    padding: 28px 14px;
    display: flex;
    align-items: center;
    line-height: 1;
    text-decoration: none !important;
    transition: color 0.2s ease;
}
.tgmenu__navbar-wrap ul.navigation > li:hover > a,
.tgmenu__navbar-wrap ul.navigation > li.active > a {
    color: #1363DF !important;
}

/* ── Labs Dropdown & Arrow ── */
.tgmenu__main-menu li.menu-item-has-children > a::after {
    content: " ⌵";
    font-size: 13px;
    font-weight: 700;
    margin-left: 6px;
    display: inline-block;
    transition: transform 0.25s ease;
}
.tgmenu__main-menu li.menu-item-has-children:hover > a::after {
    transform: rotate(180deg);
}

/* ── Mega Dropdown Panel ── */
.mega-menu-parent {
    position: relative;
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
    z-index: 1000;
    box-sizing: border-box;
    margin-top: 2px;
}
.mega-menu-parent:hover .mega-dropdown-panel {
    display: block !important;
    animation: megaDropdownFade 0.2s ease forwards;
}
@keyframes megaDropdownFade {
    from { opacity: 0; transform: translate(-50%, 6px); }
    to { opacity: 1; transform: translate(-50%, 0); }
}
.mega-dropdown-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 18px;
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
    color: #1363DF;
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

/* ── Cart and User Icon ── */
.tgmenu__action {
    display: flex;
    align-items: center;
    margin-left: auto;
}
.tgmenu__action > ul.list-wrap {
    display: flex;
    align-items: center;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 12px;
}
.tgmenu__action > ul li {
    position: relative;
    padding: 0;
}
.tgmenu__action > ul li .cart-count {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    border: 1px solid #7F7E97;
    color: #7F7E97;
    border-radius: 50%;
    text-decoration: none !important;
    position: relative;
    transition: all 0.2s ease;
}
.tgmenu__action > ul li .cart-count img {
    width: 22px;
    height: 22px;
    display: block;
}
.tgmenu__action > ul li .cart-count:hover {
    border-color: #1363DF;
    background: #1363DF;
}
.tgmenu__action > ul li .cart-count:hover img {
    filter: brightness(0) invert(1);
}
.tgmenu__action > ul li .cart-count .mini-cart-count {
    position: absolute;
    top: -8px;
    right: -4px;
    width: 22px;
    height: 22px;
    font-size: 12px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #FF8A00;
    color: #ffffff;
    border-radius: 50%;
    z-index: 1;
}

/* ── User Dropdown ── */
.user_icon {
    position: relative;
}
.user_icon .menu_user_list {
    padding: 15px 20px;
    position: absolute;
    width: 230px;
    top: 100%;
    right: 0;
    z-index: 99;
    margin-top: 12px;
    border-radius: 12px;
    background: #ffffff;
    box-shadow: 0px 20px 50px rgba(0, 0, 0, 0.12);
    border: 1px solid #eef2f6;
    list-style: none;
    opacity: 0;
    visibility: hidden;
    transform: translateY(10px);
    transition: all 0.25s ease;
}
.user_icon:hover .menu_user_list {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}
.user_icon .menu_user_list li a {
    line-height: 2.2;
    display: block;
    color: #1c1a4a !important;
    font-size: 14.5px;
    font-weight: 500;
    text-decoration: none !important;
    transition: color 0.2s;
}
.user_icon .menu_user_list li a:hover {
    color: #1363DF !important;
}

/* ── Mobile Hamburger & Drawer ── */
.mobile-nav-toggler {
    display: none;
    font-size: 26px;
    cursor: pointer;
    color: #1363DF;
    padding: 6px;
    margin-left: 12px;
}
@media (max-width: 1199.98px) {
    .tgmenu__navbar-wrap {
        display: none !important;
    }
    .mobile-nav-toggler {
        display: block !important;
    }
}
.tgmobile__menu {
    position: fixed;
    right: 0;
    top: 0;
    width: 300px;
    padding-right: 30px;
    max-width: 100%;
    height: 100%;
    z-index: 99999;
    opacity: 0;
    visibility: hidden;
    transform: translateX(101%);
    transition: all 0.35s ease;
}
.tgmobile__menu.active {
    opacity: 1;
    visibility: visible;
    transform: translateX(0);
}
.tgmobile__menu-box {
    position: absolute;
    left: 0px;
    top: 0px;
    width: 100%;
    height: 100%;
    max-height: 100%;
    overflow-y: auto;
    background: #ffffff;
    padding: 30px 24px;
    z-index: 5;
    box-shadow: -10px 0 30px rgba(0,0,0,0.1);
}
.tgmobile__menu-box .close-btn {
    position: absolute;
    right: 20px;
    top: 20px;
    line-height: 30px;
    width: 30px;
    text-align: center;
    font-size: 24px;
    color: #1c1a4a;
    cursor: pointer;
}
.tgmobile__menu-nav li a {
    display: block;
    padding: 10px 0;
    font-size: 15px;
    font-weight: 600;
    color: #1c1a4a;
    text-decoration: none;
    border-bottom: 1px solid #f1f5f9;
}
.tgmobile__menu-backdrop {
    position: fixed;
    right: 0;
    top: 0;
    width: 100%;
    height: 100%;
    z-index: 9999;
    background: rgba(0, 0, 0, 0.5);
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}
.tgmobile__menu-backdrop.active {
    opacity: 1;
    visibility: visible;
}

/* ── EXACT MAIN PORTAL FOOTER STYLES ── */
.footer__area {
    background: #061224 !important;
    color: #8c9ab4 !important;
    margin-top: 0 !important;
    position: relative;
    z-index: 1;
}
.footer__area .footer__top {
    padding: 60px 0 32px !important;
}
.footer__area .footer__widget {
    margin-bottom: 30px !important;
}
.footer__area .footer__widget .logo {
    margin-bottom: 22px !important;
}
.footer__area .footer__widget-title {
    color: #ffffff !important;
    font-size: 20px !important;
    font-weight: 600 !important;
    position: relative !important;
    padding-bottom: 15px !important;
    margin-bottom: 22px !important;
}
.footer__area .footer__widget-title::before {
    content: "";
    position: absolute;
    left: 0;
    bottom: 0;
    width: 30px;
    height: 3px;
    border-radius: 2px;
    background: #1363DF;
}
.footer__area .footer__content p {
    color: #8c9ab4 !important;
    font-size: 14.5px !important;
    line-height: 1.6 !important;
    margin-bottom: 15px !important;
}
.footer__area .footer__content .list-wrap {
    list-style: none !important;
    padding: 0 !important;
    margin: 0 !important;
}
.footer__area .footer__content .list-wrap li {
    color: #8c9ab4 !important;
    font-size: 14px !important;
    margin-bottom: 8px !important;
}
.footer__area .footer__link .list-wrap {
    list-style: none !important;
    padding: 0 !important;
    margin: 0 !important;
}
.footer__area .footer__link .list-wrap li {
    margin-bottom: 10px !important;
}
.footer__area .footer__link .list-wrap li a {
    color: #8c9ab4 !important;
    font-size: 15px !important;
    font-weight: 500 !important;
    text-decoration: none !important;
    transition: all 0.25s ease !important;
    display: inline-block !important;
}
.footer__area .footer__link .list-wrap li a:hover {
    color: #FF8A00 !important;
    transform: translateX(4px) !important;
}
.footer__area .footer__contact-content p {
    color: #8c9ab4 !important;
    font-size: 14.5px !important;
    margin-bottom: 15px !important;
}
.footer__area .footer__social {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    list-style: none !important;
    padding: 0 !important;
    margin: 0 0 20px 0 !important;
}
.footer__area .footer__social li a {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 40px !important;
    height: 40px !important;
    border-radius: 50% !important;
    background: rgba(255, 255, 255, 0.08) !important;
    transition: all 0.25s ease !important;
}
.footer__area .footer__social li a:hover {
    background: #1363DF !important;
    transform: translateY(-3px) !important;
}
.footer__area .footer__social li a img {
    width: 18px !important;
    height: 18px !important;
    filter: brightness(0) invert(1) !important;
}
.footer__area .footer__bottom {
    background: #040d1a !important;
    padding: 22px 0 !important;
    border-top: 1px solid rgba(255, 255, 255, 0.06) !important;
}
.footer__area .copy-right-text p {
    color: #8c9ab4 !important;
    margin: 0 !important;
    font-size: 14px !important;
}
.footer__area .footer__bottom-menu .list-wrap {
    display: flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
    gap: 24px !important;
    list-style: none !important;
    padding: 0 !important;
    margin: 0 !important;
}
@media (max-width: 767.98px) {
    .footer__area .footer__bottom-menu .list-wrap {
        justify-content: center !important;
        margin-top: 10px !important;
    }
    .footer__area .copy-right-text {
        text-align: center !important;
    }
}
.footer__area .footer__bottom-menu .list-wrap li a {
    color: #8c9ab4 !important;
    font-size: 14px !important;
    text-decoration: none !important;
    transition: color 0.2s ease !important;
}
.footer__area .footer__bottom-menu .list-wrap li a:hover {
    color: #ffffff !important;
}
';

echo '<style>' . $cssOutput . '</style>';

$jsConfig = [
    'baseUrl' => base_url(),
    'langBaseUrl' => langBaseUrl(),
    'isloggedIn' => authCheck() ? 1 : 0,
    'sysLangId' => $activeLang->id,
    'langShort' => $activeLang->short_form,
    'decimalSeparator' => $baseVars->decimalSeparator,
    'csrfTokenName' => csrf_token(),
    'chatUpdateTime' => (int)CHAT_UPDATE_TIME,
    'reviewsLoadLimit' => (int)REVIEWS_LOAD_LIMIT,
    'commentsLoadLimit' => (int)COMMENTS_LOAD_LIMIT,
    'cartRoute' => !empty($this->routes) && !empty($this->routes->cart) ? $this->routes->cart : '',
    'sliderFadeEffect' => $generalSettings->slider_effect == 'fade' ? 1 : 0,
    'indexProductsPerRow' => (int)$generalSettings->index_products_per_row,
    'isTurnstileEnabled' => !empty($generalSettings->turnstile_status),
    'rtl' => (bool)$baseVars->rtl,
    'text' => [
        'viewAll' => esc(trans("view_all")),
        'noResultsFound' => esc(trans("no_results_found")),
        'ok' => esc(trans("ok")),
        'cancel' => esc(trans("cancel")),
        'acceptTerms' => esc(trans("msg_accept_terms")),
        'addToCart' => esc(trans("add_to_cart")),
        'addedToCart' => esc(trans("added_to_cart")),
        'updateCart' => esc((function_exists('trans') && trans("update_cart") !== "update_cart") ? trans("update_cart") : "Update Cart"),
        'copyLink' => esc(trans("copy_link")),
        'copied' => esc(trans("copied")),
        'addToWishlist' => esc(trans("add_to_wishlist")),
        'removeFromWishlist' => esc(trans("remove_from_wishlist")),
        'processing' => esc(trans("processing")),
    ]
]; ?>

<script>window.MdsConfig = <?= json_encode($jsConfig, JSON_UNESCAPED_SLASHES); ?>;</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Mobile menu toggler
    $('.mobile-nav-toggler').on('click', function () {
        $('.tgmobile__menu').addClass('active');
        $('.tgmobile__menu-backdrop').addClass('active');
    });
    $('.tgmobile__menu .close-btn, .tgmobile__menu-backdrop').on('click', function () {
        $('.tgmobile__menu').removeClass('active');
        $('.tgmobile__menu-backdrop').removeClass('active');
    });
});
</script>