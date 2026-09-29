<?php
$nav_menu = getPortalMenuBySlug('nav-menu');
if (empty($nav_menu)) {
    // Fallback menu if DB table is empty
    $nav_menu = [
        ['label' => 'Home', 'link' => '/'],
        ['label' => 'Skill 2 Skool', 'link' => '/skill2school'],
        ['label' => 'TTT', 'link' => '/ttt'],
        ['label' => 'Upskill 4 Teacher', 'link' => '/upskill4teacher'],
        ['label' => 'Shop', 'link' => '/club-shop'],
        [
            'label' => 'Labs',
            'link' => '/labs',
            'child' => [
                ['label' => 'AI & Robotics Lab',   'link' => '/labs/ai-robotics'],
                ['label' => 'STEM Lab',            'link' => '/labs/stem'],
                ['label' => 'ECEC Lab',            'link' => '/labs/ecec'],
                ['label' => 'Composite Skill Lab', 'link' => '/labs/composite-skill'],
            ]
        ]
    ];
}

$childImageMap = [
    '/'                     => ['image' => mainPortalUrl('designs/img/logo.png'),              'desc' => 'Back to homepage'],
    '/skill2school'         => ['image' => mainPortalUrl('designs/img/skill2school-2.jpeg'),   'desc' => 'School-wide skill curriculum'],
    '/upskill4teacher'      => ['image' => mainPortalUrl('designs/img/TTT-1.png'),             'desc' => 'Professional educator development'],
    '/ttt'                  => ['image' => mainPortalUrl('designs/img/TTT-1.png'),             'desc' => 'Master trainer & bootcamp'],
    '/shop'                 => ['image' => mainPortalUrl('frontend/img/skillbox/skillbox_banner.png'), 'desc' => 'Interactive kits & learning boxes'],
    '/club-shop'            => ['image' => mainPortalUrl('frontend/img/skillbox/skillbox_banner.png'), 'desc' => 'Interactive kits & learning boxes'],
    '/labs'                 => ['image' => 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=600&q=80', 'desc' => 'Hands-on innovation labs'],
    '/labs/ai-robotics'     => ['image' => 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=600&q=80', 'desc' => 'AI & Robotics Lab'],
    '/labs/stem'            => ['image' => 'https://images.unsplash.com/photo-1516339901601-2e1b62dc0c45?auto=format&fit=crop&w=600&q=80', 'desc' => 'STEM Lab'],
    '/labs/ecec'            => ['image' => 'https://images.unsplash.com/photo-1581092335397-9583fe92d232?auto=format&fit=crop&w=600&q=80', 'desc' => 'ECEC Lab'],
    '/labs/composite-skill' => ['image' => 'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=600&q=80', 'desc' => 'Composite Skill Lab'],
    '/courses'              => ['image' => mainPortalUrl('designs/img/skill2school-3.png'),    'desc' => 'Browse all courses'],
    '/blog'                 => ['image' => mainPortalUrl('designs/img/skill2school-4.jpeg'),   'desc' => 'Articles & insights'],
    '/contact'              => ['image' => mainPortalUrl('designs/img/skill2school-5.jpeg'),   'desc' => 'Get in touch'],
];

$childLabelMap = [
    'ai & robotics'       => 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=600&q=80',
    'ai & robotics lab'   => 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=600&q=80',
    'stem lab'            => 'https://images.unsplash.com/photo-1516339901601-2e1b62dc0c45?auto=format&fit=crop&w=600&q=80',
    'ecec lab'            => 'https://images.unsplash.com/photo-1581092335397-9583fe92d232?auto=format&fit=crop&w=600&q=80',
    'composite skill lab' => 'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=600&q=80',
    'composite lab'       => 'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=600&q=80',
];
?>
<!DOCTYPE html>
<html lang="<?= esc($activeLang->short_form); ?>" <?= $baseVars->rtl ? 'dir="rtl"' : ''; ?>>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title><?= escMeta($title); ?> - <?= escMeta($baseSettings->site_title); ?></title>
<meta name="description" content="<?= escMeta($description); ?>"/>
<meta name="keywords" content="<?= escMeta($keywords); ?>"/>
<meta name="author" content="<?= escMeta($generalSettings->application_name); ?>"/>
<?= seoRobotsTag(!isset($products) || !empty($products)); ?>
<link rel="shortcut icon" type="image/png" href="<?= getFavicon(); ?>"/>
<meta property="og:locale" content="<?= escMeta($activeLang->language_code); ?>"/>
<meta property="og:site_name" content="<?= escMeta($generalSettings->application_name); ?>"/>
<?= csrf_meta(); ?>

<?php if (isset($showOgTags)): ?>
<meta property="og:type" content="<?= !empty($ogType) ? escMeta($ogType) : 'website'; ?>"/>
<meta property="og:title" content="<?= !empty($ogTitle) ? escMeta($ogTitle) : 'index'; ?>"/>
<meta property="og:description" content="<?= escMeta($ogDescription); ?>"/>
<meta property="og:url" content="<?= cleanSeoUrl((string)$ogUrl); ?>"/>
<meta property="og:image" content="<?= escMeta($ogImage); ?>"/>
<meta property="og:image:width" content="<?= !empty($ogWidth) ? $ogWidth : 250; ?>"/>
<meta property="og:image:height" content="<?= !empty($ogHeight) ? $ogHeight : 250; ?>"/>
<meta property="article:author" content="<?= !empty($ogAuthor) ? escMeta($ogAuthor) : ''; ?>"/>
<meta property="fb:app_id" content="<?= escMeta($generalSettings->facebook_app_id); ?>"/>
<?php if (!empty($ogTags)):foreach ($ogTags as $tag): ?>
<meta property="article:tag" content="<?= escMeta($tag->tag); ?>"/>
<?php endforeach; endif; ?>
<meta property="article:published_time" content="<?= !empty($ogPublishedTime) ? $ogPublishedTime : ''; ?>"/>
<meta property="article:modified_time" content="<?= !empty($ogModifiedTime) ? $ogModifiedTime : ''; ?>"/>
<meta name="twitter:card" content="summary_large_image"/>
<meta name="twitter:site" content="@<?= escMeta($generalSettings->application_name); ?>"/>
<meta name="twitter:creator" content="@<?= escMeta($ogCreator); ?>"/>
<meta name="twitter:title" content="<?= escMeta($ogTitle); ?>"/>
<meta name="twitter:description" content="<?= escMeta($ogDescription); ?>"/>
<meta name="twitter:image" content="<?= escMeta($ogImage); ?>"/>
<?php else: ?>
<meta property="og:image" content="<?= getLogo(); ?>"/>
<meta property="og:image:width" content="<?= $baseVars->logoWidth; ?>"/>
<meta property="og:image:height" content="<?= $baseVars->logoHeight; ?>"/>
<meta property="og:type" content="website"/>
<meta property="og:title" content="<?= escMeta($title); ?> - <?= escMeta($baseSettings->site_title); ?>"/>
<meta property="og:description" content="<?= escMeta($description); ?>"/>
<meta property="og:url" content="<?= cleanSeoUrl((string)current_url(true)); ?>"/>
<meta property="fb:app_id" content="<?= escMeta($generalSettings->facebook_app_id); ?>"/>
<meta name="twitter:card" content="summary_large_image"/>
<meta name="twitter:site" content="@<?= escMeta($generalSettings->application_name); ?>"/>
<meta name="twitter:title" content="<?= escMeta($title); ?> - <?= escMeta($baseSettings->site_title); ?>"/>
<meta name="twitter:description" content="<?= escMeta($description); ?>"/>
<?php endif;
if ($generalSettings->pwa_status == 1): ?>
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="apple-mobile-web-app-title" content="<?= escMeta($generalSettings->application_name); ?>">
<meta name="msapplication-TileImage" content="<?= base_url(getPwaLogo($generalSettings, 'sm')); ?>">
<meta name="msapplication-TileColor" content="#2F3BA2">
<link rel="manifest" href="<?= base_url('manifest.json'); ?>">
<link rel="apple-touch-icon" href="<?= base_url(getPwaLogo($generalSettings, 'sm')); ?>">
<?php endif; ?>
<?= seoCanonicalTag(); ?>

<?= seoHreflangTags($isTranslatable ?? false); ?>

<?= view('partials/_fonts'); ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
<link rel="preload" href="<?= base_url("assets/css/icon-font/mds-icons.woff2"); ?>" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="<?= base_url('assets/css/plugins-2.6.css'); ?>"/>
<link rel="stylesheet" href="<?= base_url('assets/css/style-2.6.min.css'); ?>"/>
<link rel="stylesheet" href="<?= mainPortalUrl('frontend/css/flaticon-skillgro.css'); ?>"/>
<?= view('partials/_css_js_header'); ?>
<?php if (!empty($jsonLdScript)):
echo $jsonLdScript;
endif; ?>

<?= $generalSettings->google_adsense_code; ?>
<?= $generalSettings->custom_header_codes; ?>
</head>
<body>
<!-- header-area -->
<header>
    <div id="header-fixed-height"></div>
    <div id="sticky-header" class="tg-header__area">
        <div class="container custom-container xl_container">
            <div class="row">
                <div class="col-12">
                    <div class="tgmenu__wrap">
                        <nav class="tgmenu__nav">
                            <div class="logo">
                                <a href="<?= mainPortalUrl('/'); ?>">
                                    <img src="<?= mainPortalUrl('designs/img/logo.png'); ?>" alt="Skillvation" onerror="this.onerror=null;this.src='<?= getLogo(); ?>';" style="max-height: 48px; width: auto; display: block;">
                                </a>
                            </div>
                            <div class="tgmenu__navbar-wrap tgmenu__main-menu d-none d-xl-flex">
                                <ul class="navigation">
                                    <?php foreach ($nav_menu as $menu):
                                        $hasChild = !empty($menu['child']) && count($menu['child']) > 0;
                                        $linkLower = strtolower(trim($menu['link'] ?? ''));
                                        $labelLower = strtolower(trim($menu['label'] ?? ''));
                                        $isShopLink = ($linkLower === 'shop' || $linkLower === '/shop' || $linkLower === 'club-shop' || $linkLower === '/club-shop' || $labelLower === 'shop');
                                        $targetUrl = $isShopLink ? langBaseUrl() : mainPortalUrl($menu['link']);
                                    ?>
                                        <li class="<?= $hasChild ? 'menu-item-has-children mega-menu-parent' : ''; ?> <?= $isShopLink ? 'active' : ''; ?>">
                                            <a href="<?= $hasChild ? 'javascript:;' : $targetUrl; ?>">
                                                <span><?= esc($menu['label']); ?></span>
                                            </a>
                                            <?php if ($hasChild):
                                                $childCount = count($menu['child']);
                                                $panelW = $childCount <= 2 ? '460px' : ($childCount === 3 ? '680px' : '900px');
                                                $colW = $childCount <= 2 ? 'calc(50% - 8px)' : ($childCount === 3 ? 'calc(33.333% - 11px)' : 'calc(25% - 12px)');
                                            ?>
                                                <div class="mega-dropdown-panel" style="width:<?= $panelW; ?>;">
                                                    <div class="mega-dropdown-header">
                                                        <span class="mega-dropdown-title"><?= esc($menu['label']); ?></span>
                                                        <span class="mega-dropdown-count"><?= $childCount; ?></span>
                                                    </div>
                                                    <div class="mega-dropdown-grid">
                                                        <?php foreach ($menu['child'] as $child):
                                                            $childPath = '/' . ltrim($child['link'], '/');
                                                            $labelKey = strtolower(trim($child['label']));
                                                            $childMeta = $childImageMap[$childPath] ?? null;
                                                            $childImg = $childMeta['image'] ?? ($childLabelMap[$labelKey] ?? 'https://images.unsplash.com/photo-1516339901601-2e1b62dc0c45?auto=format&fit=crop&w=600&q=80');
                                                        ?>
                                                            <a href="<?= mainPortalUrl($child['link']); ?>" class="mega-card" style="width:<?= $colW; ?>;">
                                                                <p class="mega-card__title"><?= esc($child['label']); ?></p>
                                                                <div class="mega-card__img">
                                                                    <img src="<?= $childImg; ?>" alt="<?= esc($child['label']); ?>" onerror="this.onerror=null;this.style.opacity='.3';" />
                                                                </div>
                                                            </a>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div class="tgmenu__action">
                                <ul class="list-wrap">
                                    <li class="mini-cart-icon">
                                        <a href="<?= generateUrl('cart'); ?>" class="cart-count">
                                            <img src="<?= mainPortalUrl('frontend/img/icons/cart.svg'); ?>" alt="cart">
                                            <span class="mini-cart-count"><?= esc($cartItemCount); ?></span>
                                        </a>
                                    </li>
                                    <li class="mini-cart-icon user_icon">
                                        <a href="javascript:;" class="cart-count" <?= !authCheck() ? 'data-toggle="modal" data-target="#loginModal"' : ''; ?>>
                                            <img src="<?= mainPortalUrl('frontend/img/icons/menu_user.svg'); ?>" alt="user">
                                        </a>
                                        <ul class="menu_user_list">
                                            <?php if (!authCheck()): ?>
                                                <li><a href="javascript:;" data-toggle="modal" data-target="#loginModal"><?= trans("login") ?? "Sign in"; ?></a></li>
                                                <li><a href="<?= generateUrl('register'); ?>"><?= trans("register") ?? "Sign Up"; ?></a></li>
                                            <?php else: ?>
                                                <li><a href="<?= dashboardUrl(); ?>"><?= trans("dashboard") ?? "Dashboard"; ?></a></li>
                                                <li><a href="<?= dashboardUrl('orders'); ?>"><?= trans("orders") ?? "My Orders"; ?></a></li>
                                                <li><a href="<?= generateUrl('wishlist'); ?>"><?= trans("wishlist") ?? "Wishlist"; ?></a></li>
                                                <li><a href="<?= generateUrl('profile_edit'); ?>"><?= trans("profile") ?? "Profile Settings"; ?></a></li>
                                                <li><a href="<?= mainPortalUrl('student/dashboard'); ?>" target="_blank" style="color:#d97706; font-weight:600;"><i class="icon-book-open mr-1"></i> LMS Portal</a></li>
                                                <li><a href="<?= base_url('logout'); ?>" class="text-danger"><?= trans("logout") ?? "Logout"; ?></a></li>
                                            <?php endif; ?>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <div class="mobile-nav-toggler"><i class="tg-flaticon-menu-1"></i></div>
                        </nav>
                    </div>

                    <!-- Mobile Menu  -->
                    <div class="tgmobile__menu">
                        <nav class="tgmobile__menu-box">
                            <div class="close-btn"><i class="tg-flaticon-close-1"></i></div>
                            <div class="nav-logo mb-4">
                                <a href="<?= mainPortalUrl('/'); ?>"><img src="<?= mainPortalUrl('designs/img/logo.png'); ?>" alt="Skillvation" style="max-height: 40px; width: auto;"></a>
                            </div>

                            <ul class="tgmobile__menu-nav list-unstyled">
                                <?php foreach ($nav_menu as $menu):
                                    $hasChild = !empty($menu['child']) && count($menu['child']) > 0;
                                    $linkLower = strtolower(trim($menu['link'] ?? ''));
                                    $labelLower = strtolower(trim($menu['label'] ?? ''));
                                    $isShopLink = ($linkLower === 'shop' || $linkLower === '/shop' || $linkLower === 'club-shop' || $linkLower === '/club-shop' || $labelLower === 'shop');
                                    $targetUrl = $isShopLink ? langBaseUrl() : mainPortalUrl($menu['link']);
                                ?>
                                    <li class="<?= $hasChild ? 'menu-item-has-children' : ''; ?>">
                                        <a href="<?= $hasChild ? 'javascript:;' : $targetUrl; ?>" class="<?= $isShopLink ? 'text-primary font-weight-bold' : ''; ?>">
                                            <?= esc($menu['label']); ?>
                                            <?php if ($hasChild): ?><i class="icon-arrow-down float-right mt-1"></i><?php endif; ?>
                                        </a>
                                        <?php if ($hasChild): ?>
                                            <ul class="sub-menu list-unstyled pl-3">
                                                <?php foreach ($menu['child'] as $child): ?>
                                                    <li><a href="<?= mainPortalUrl($child['link']); ?>"><?= esc($child['label']); ?></a></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                                <li><a href="<?= generateUrl('cart'); ?>">Cart (<?= esc($cartItemCount); ?>)</a></li>
                                <li><a href="<?= generateUrl('wishlist'); ?>">Wishlist</a></li>
                            </ul>

                            <div class="mobile_menu_login mt-4">
                                <?php if (!authCheck()): ?>
                                    <a href="javascript:;" data-toggle="modal" data-target="#loginModal" class="btn btn-primary btn-sm mr-2"><?= trans("login"); ?></a>
                                    <a href="<?= generateUrl('register'); ?>" class="btn btn-outline-primary btn-sm"><?= trans("register"); ?></a>
                                <?php else: ?>
                                    <a href="<?= dashboardUrl(); ?>" class="btn btn-primary btn-sm"><?= trans("dashboard"); ?></a>
                                    <a href="<?= base_url('logout'); ?>" class="btn btn-danger btn-sm ml-2"><?= trans("logout"); ?></a>
                                <?php endif; ?>
                            </div>
                        </nav>
                    </div>
                    <div class="tgmobile__menu-backdrop"></div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- header-area-end -->

<div id="overlay_bg" class="overlay-bg"></div>