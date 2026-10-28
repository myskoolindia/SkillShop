<?php
    $nav_menu = Cache::rememberForever('nav_menu', function () {
        return menuGetBySlug('nav-menu');
    });

    // Ensure Labs menu item has child items populated
    if ($nav_menu) {
        foreach ($nav_menu as &$m) {
            $labelLower = strtolower(trim($m['label'] ?? ''));
            $linkTrim   = trim($m['link'] ?? '', '/');
            if ($labelLower === 'labs' || $linkTrim === 'labs') {
                if (empty($m['child'])) {
                    $m['child'] = [
                        ['label' => 'AI & Robotics Lab',   'link' => '/labs/ai-robotics'],
                        ['label' => 'STEM Lab',            'link' => '/labs/stem'],
                        ['label' => 'ECEC Lab',            'link' => '/labs/ecec'],
                        ['label' => 'Composite Skill Lab', 'link' => '/labs/composite-skill'],
                    ];
                }
            }
        }
        unset($m);
    }

    // Navigation follows the same verified audience rules as the backend.
    $shopRole = auth()->user()?->role;
    $allowedShopLink = function ($link) use ($shopRole) {
        if (!$shopRole) return true;
        $path = strtolower('/' . ltrim(parse_url($link, PHP_URL_PATH) ?: '', '/'));
        if (in_array($path, ['/shop','/skillbox'])) return $shopRole === 'student';
        if (preg_match('~^/labs(?:/|$)~', $path)) return in_array($shopRole, ['school','teacher']);
        return true;
    };
    $nav_menu = array_values(array_filter(array_map(function ($menu) use ($allowedShopLink) {
        if (!empty($menu['child'])) $menu['child'] = array_values(array_filter($menu['child'], fn($child) => $allowedShopLink($child['link'] ?? '')));
        return $menu;
    }, is_array($nav_menu) ? $nav_menu : ($nav_menu?->toArray() ?? [])), fn($menu) => $allowedShopLink($menu['link'] ?? '')));

    $categories = \Modules\Course\app\Models\CourseCategory::with('translation')
        ->where('status', 1)
        ->whereNull('parent_id')
        ->get();

    // Image map for known child menu links
    $childImageMap = [
        '/'                     => ['image' => asset('designs/img/logo.png'),              'desc' => 'Back to homepage'],
        '/skill2school'         => ['image' => asset('designs/img/skill2school-2.jpeg'),   'desc' => 'School-wide skill curriculum'],
        '/upskill4teacher'      => ['image' => asset('designs/img/TTT-1.png'),             'desc' => 'Professional educator development'],
        '/ttt'                  => ['image' => asset('designs/img/TTT-1.png'),             'desc' => 'Master trainer & bootcamp'],
        '/shop'                 => ['image' => asset('frontend/img/skillbox/skillbox_banner.png'), 'desc' => 'Interactive kits & learning boxes'],
        '/SkillBox'             => ['image' => asset('frontend/img/skillbox/skillbox_banner.png'), 'desc' => 'Interactive kits & learning boxes'],
        '/skillbox'             => ['image' => asset('frontend/img/skillbox/skillbox_banner.png'), 'desc' => 'Interactive kits & learning boxes'],
        '/labs'                 => ['image' => 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=600&q=80', 'desc' => 'Hands-on innovation labs'],
        '/labs/ai-robotics'     => ['image' => 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=600&q=80', 'desc' => 'AI & Robotics Lab'],
        '/labs/stem'            => ['image' => 'https://images.unsplash.com/photo-1516339901601-2e1b62dc0c45?auto=format&fit=crop&w=600&q=80', 'desc' => 'STEM Lab'],
        '/labs/ecec'            => ['image' => 'https://images.unsplash.com/photo-1581092335397-9583fe92d232?auto=format&fit=crop&w=600&q=80', 'desc' => 'ECEC Lab'],
        '/labs/composite-skill' => ['image' => 'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=600&q=80', 'desc' => 'Composite Skill Lab'],
        '/courses'              => ['image' => asset('designs/img/skill2school-3.png'),    'desc' => 'Browse all courses'],
        '/blog'                 => ['image' => asset('designs/img/skill2school-4.jpeg'),   'desc' => 'Articles & insights'],
        '/contact'              => ['image' => asset('designs/img/skill2school-5.jpeg'),   'desc' => 'Get in touch'],
    ];

    $childLabelMap = [
        'ai & robotics'       => 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=600&q=80',
        'ai & robotics lab'   => 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=600&q=80',
        'stem lab'            => 'https://images.unsplash.com/photo-1516339901601-2e1b62dc0c45?auto=format&fit=crop&w=600&q=80',
        'ecec lab'            => 'https://images.unsplash.com/photo-1581092335397-9583fe92d232?auto=format&fit=crop&w=600&q=80',
        'composite skill lab' => 'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=600&q=80',
        'composite lab'       => 'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=600&q=80',
    ];

    $headerLogo = null;
    if (!empty($setting?->logo) && file_exists(public_path($setting->logo))) {
        $headerLogo = asset($setting->logo);
    } elseif (!empty(Cache::get('setting')?->logo) && file_exists(public_path(Cache::get('setting')->logo))) {
        $headerLogo = asset(Cache::get('setting')->logo);
    } else {
        $headerLogo = asset('designs/img/logo.png');
    }
?>
<!-- header-area -->
<header>
    <?php if($setting?->header_topbar_status == 'active'): ?>
        <div class="tg-header__top">
            <div class="container custom-container xl_container">
                <div class="row">
                    <div class="col-lg-6">
                        <ul class="tg-header__top-info list-wrap">
                            <?php if($setting?->site_address): ?>
                                <li><img src="<?php echo e(asset('frontend/img/icons/map_marker.svg')); ?>" alt="Icon">
                                    <span><?php echo e($setting?->site_address); ?></span>
                                </li>
                            <?php endif; ?>
                            <?php if($setting?->site_email): ?>
                                <li><img src="<?php echo e(asset('frontend/img/icons/envelope.svg')); ?>" alt="Icon"> <a
                                        href="mailto:<?php echo e($setting?->site_email); ?>"><?php echo e($setting?->site_email); ?></a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="col-lg-6">
                        <div class="tg-header__top-right">
                            <?php if($setting?->header_social_status == 'active'): ?>
                                <ul class="tg-header__top-social list-wrap">
                                    <li><?php echo e(__('Follow Us On')); ?> :</li>
                                    <?php $__currentLoopData = getSocialLinks(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $socialLink): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="header-social">
                                            <a href="<?php echo e($socialLink->link); ?>" target="_blank">
                                                <img src="<?php echo e(asset($socialLink->icon)); ?>" alt="img">
                                            </a>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            <?php endif; ?>
                            <div class="header_language_area d-flex flex-wrap d-none d-xl-flex">

                                <ul>
                                    <?php if(count(allLanguages()?->where('status', 1)) > 1): ?>
                                        <form action="<?php echo e(route('set-language')); ?>" id="setLanguageHeader">
                                            <select name="code" class="select_js">
                                                <?php $__empty_1 = true; $__currentLoopData = allLanguages()?->where('status', 1); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                    <option value="<?php echo e($language->code); ?>"
                                                        <?php echo e(getSessionLanguage() == $language->code ? 'selected' : ''); ?>>
                                                        <?php echo e($language->name); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                    <option value="en"
                                                        <?php echo e(getSessionLanguage() == 'en' ? 'selected' : ''); ?>>
                                                        <?php echo e(__('English')); ?>

                                                    </option>
                                                <?php endif; ?>
                                            </select>
                                        </form>
                                    <?php endif; ?>
                                    <?php if(count(allCurrencies()?->where('status', 'active')) > 1): ?>
                                        <form action="<?php echo e(route('set-currency')); ?>" class="set-currency-header"
                                            method="GET">
                                            <select name="currency" class="change-currency select_js">
                                                <?php $__empty_1 = true; $__currentLoopData = allCurrencies()?->where('status', 'active'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                    <option value="<?php echo e($currency->currency_code); ?>"
                                                        <?php echo e(getSessionCurrency() == $currency->currency_code ? 'selected' : ''); ?>>
                                                        <?php echo e($currency->currency_name); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                    <option value="USD"
                                                        <?php echo e(getSessionCurrency() == 'USD' ? 'selected' : ''); ?>>
                                                        <?php echo e(__('USD')); ?>

                                                    </option>
                                                <?php endif; ?>
                                            </select>
                                        </form>
                                    <?php endif; ?>
                                </ul>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <div id="header-fixed-height"></div>
    <div id="sticky-header" class="tg-header__area">
        <div class="container custom-container">
            <div class="row">
                <div class="col-12">
                    <div class="tgmenu__wrap">
                        <nav class="tgmenu__nav">
                            <div class="logo">
                                <a href="<?php echo e(route('home')); ?>"><img src="<?php echo e($headerLogo); ?>"
                                        alt="<?php echo e(config('app.name', 'Skillvation')); ?>" onerror="this.onerror=null;this.src='<?php echo e(asset('designs/img/logo.png')); ?>';" style="max-height: 48px; width: auto; display: block;"></a>
                            </div>
                            <div class="tgmenu__navbar-wrap tgmenu__main-menu d-none d-xl-flex">
                                <?php if($nav_menu): ?>
                                    <ul class="navigation">
                                        <?php $__currentLoopData = $nav_menu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($menu['link'] == '/' && $setting?->show_all_homepage == 1): ?>
                                                <li class="menu-item-has-children">
                                                    <a href="<?php echo e(url('/')); ?>"
                                                        title=""><?php echo e(__('Home')); ?></a>
                                                    <ul class="sub-menu">
                                                        <li class=""><a
                                                                href="<?php echo e(route('change-theme', 'theme-one')); ?>"
                                                                title=""><?php echo e(__('Home One')); ?></a></li>
                                                        <li class=""><a
                                                                href="<?php echo e(route('change-theme', 'theme-two')); ?>"
                                                                title=""><?php echo e(__('Home Two')); ?></a></li>
                                                        <li class=""><a
                                                                href="<?php echo e(route('change-theme', 'theme-three')); ?>"
                                                                title=""><?php echo e(__('Home Three')); ?></a></li>
                                                        <li class=""><a
                                                                href="<?php echo e(route('change-theme', 'theme-four')); ?>"
                                                                title=""><?php echo e(__('Home Four')); ?></a></li>
                                                    </ul><!-- /.sub-menu -->
                                                </li>
                                            <?php else: ?>
                                                <li class="<?php echo e(!empty($menu['child']) ? 'menu-item-has-children mega-menu-parent' : ''); ?>">
                                                    <a href="<?php echo e(!empty($menu['child']) ? 'javascript:;' : url($menu['link'])); ?>"
                                                        title="">
                                                        <span><?php echo e($menu['label']); ?></span>
                                                    </a>
                                                    <?php if(!empty($menu['child'])): ?>
                                                        <?php
                                                            $childCount = count($menu['child']);
                                                            $panelW     = $childCount <= 2 ? '460px' : ($childCount === 3 ? '680px' : '900px');
                                                            $colW       = $childCount <= 2 ? 'calc(50% - 8px)' : ($childCount === 3 ? 'calc(33.333% - 11px)' : 'calc(25% - 12px)');
                                                        ?>

                                                        
                                                        <div class="mega-dropdown-panel" style="width:<?php echo e($panelW); ?>;">
                                                            <div class="mega-dropdown-header">
                                                                <span class="mega-dropdown-title"><?php echo e($menu['label']); ?></span>
                                                                <span class="mega-dropdown-count"><?php echo e($childCount); ?></span>
                                                            </div>
                                                            <div class="mega-dropdown-grid">
                                                                <?php $__currentLoopData = $menu['child']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <?php
                                                                        $childPath  = '/' . ltrim($child['link'], '/');
                                                                        $labelKey   = strtolower(trim($child['label']));
                                                                        $childMeta  = $childImageMap[$childPath] ?? null;
                                                                        $childImg   = $childMeta['image'] ?? ($childLabelMap[$labelKey] ?? 'https://images.unsplash.com/photo-1516339901601-2e1b62dc0c45?auto=format&fit=crop&w=600&q=80');
                                                                        $isActive   = request()->is(ltrim($child['link'], '/'));
                                                                    ?>
                                                                    <a href="<?php echo e(url($child['link'])); ?>"
                                                                       class="mega-card <?php echo e($isActive ? 'mega-card--active' : ''); ?>"
                                                                       style="width:<?php echo e($colW); ?>;">
                                                                        <p class="mega-card__title"><?php echo e($child['label']); ?></p>
                                                                        <div class="mega-card__img">
                                                                            <img src="<?php echo e($childImg); ?>"
                                                                                 alt="<?php echo e($child['label']); ?>"
                                                                                 onerror="this.onerror=null;this.style.opacity='.3';" />
                                                                        </div>
                                                                    </a>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </div>
                                                        </div>

                                                        
                                                        <ul class="sub-menu">
                                                            <?php $__currentLoopData = $menu['child']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <li><a href="<?php echo e(url($child['link'])); ?>"><?php echo e($child['label']); ?></a></li>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </ul>
                                                    <?php endif; ?>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul><!-- /.menu -->
                                <?php endif; ?>

                            </div>
                            <div class="tgmenu__action">
                                <ul class="list-wrap">
                                    <li class="mini-cart-icon">
                                        <a href="<?php echo e(route('cart')); ?>" class="cart-count">
                                            <img src="<?php echo e(asset('frontend/img/icons/cart.svg')); ?>" class="injectable"
                                                alt="img">
                                            <span class="mini-cart-count"><?php echo e(Cart::content()->count()); ?></span>
                                        </a>
                                    </li>
                                    <li class="mini-cart-icon user_icon">
                                        <a href="javascript:;" class="cart-count">
                                            <img src="<?php echo e(asset('frontend/img/icons/menu_user.svg')); ?>"
                                                alt="img">
                                        </a>
                                        <ul class="menu_user_list">
                                            <?php if(auth()->guard()->guest()): ?>
                                                <li><a href="<?php echo e(route('login')); ?>"><?php echo e(__('Sign in')); ?></a></li>
                                                <li><a href="<?php echo e(route('register')); ?>"><?php echo e(__('Sign Up')); ?></a></li>
                                            <?php else: ?>
                                                <?php if(Auth::guard('web')->user()): ?>
                                                    <?php if(userAuth()->role == 'instructor'): ?>
                                                        <li><a
                                                                href="<?php echo e(route('instructor.dashboard')); ?>"><?php echo e(__('Instructor Dashboard')); ?></a>
                                                        </li>
                                                    <?php elseif(userAuth()->role == 'school'): ?>
                                                        <li><a
                                                                href="<?php echo e(route('school.dashboard')); ?>"><?php echo e(__('School Dashboard')); ?></a>
                                                        </li>
                                                    <?php else: ?>
                                                        <li><a
                                                                href="<?php echo e(route('student.dashboard')); ?>"><?php echo e(__('Student Dashboard')); ?></a>
                                                        </li>
                                                    <?php endif; ?>
                                                    <li><a href="<?php echo e(route('sso.shop')); ?>" target="_blank" style="color:#d97706; font-weight:600;"><i class="fas fa-shopping-bag mr-1"></i> <?php echo e(__('Club Shop & Orders')); ?></a>
                                                    </li>
                                                    <li><a
                                                            href="<?php echo e(userAuth()->role == 'instructor' ? route('instructor.setting.index') : (userAuth()->role == 'school' ? route('school.profile.index') : route('student.setting.index'))); ?>"><?php echo e(__('Profile')); ?></a>
                                                    </li>
                                                    <li><a
                                                            href="<?php echo e(userAuth()->role == 'instructor' ? route('instructor.courses.index') : (userAuth()->role == 'school' ? route('school.courses.index') : route('student.enrolled-courses'))); ?>"><?php echo e(__('Courses')); ?></a>
                                                    </li>
                                                    <li><a href="javascript:void(0)"
                                                            class="text-danger logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><?php echo e(__('Logout')); ?></a>
                                                    </li>
                                                <?php endif; ?>
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
                            <div class="nav-logo">
                                <a href="<?php echo e(route('home')); ?>"><img src="<?php echo e($headerLogo); ?>"
                                        alt="<?php echo e(config('app.name', 'Skillvation')); ?>" onerror="this.onerror=null;this.src='<?php echo e(asset('designs/img/logo.png')); ?>';" style="max-height: 40px; width: auto; display: block;"></a>
                            </div>

                            <div class="header_language_area d-flex flex-wrap">

                                <ul>
                                    <?php if(count(allLanguages()?->where('status', 1)) > 1): ?>
                                        <form action="<?php echo e(route('set-language')); ?>"
                                            class="change-language-header-mobile" method="GET">
                                            <select name="code" class="select_js set-language-header-mobile">
                                                <?php $__empty_1 = true; $__currentLoopData = allLanguages()?->where('status', 1); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                    <option value="<?php echo e($language->code); ?>"
                                                        <?php echo e(getSessionLanguage() == $language->code ? 'selected' : ''); ?>>
                                                        <?php echo e($language->name); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                    <option value="en"
                                                        <?php echo e(getSessionLanguage() == 'en' ? 'selected' : ''); ?>>
                                                        <?php echo e(__('English')); ?>

                                                    </option>
                                                <?php endif; ?>
                                            </select>
                                        </form>
                                    <?php endif; ?>
                                    <?php if(count(allCurrencies()?->where('status', 'active')) > 1): ?>
                                        <form action="<?php echo e(route('set-currency')); ?>"
                                            class="change-currency-header-mobile" method="GET">
                                            <select name="currency" class="set-currency-header-mobile select_js">
                                                <?php $__empty_1 = true; $__currentLoopData = allCurrencies()?->where('status', 'active'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                    <option value="<?php echo e($currency->currency_code); ?>"
                                                        <?php echo e(getSessionCurrency() == $currency->currency_code ? 'selected' : ''); ?>>
                                                        <?php echo e($currency->currency_name); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                    <option value="USD"
                                                        <?php echo e(getSessionCurrency() == 'USD' ? 'selected' : ''); ?>>
                                                        <?php echo e(__('USD')); ?>

                                                    </option>
                                                <?php endif; ?>
                                            </select>
                                        </form>
                                    <?php endif; ?>
                                </ul>
                            </div>
                            <?php if(auth()->guard()->guest()): ?>
                                <ul class="mobile_menu_login d-flex flex-wrap">
                                    <li><a href="<?php echo e(route('login')); ?>"><?php echo e(__('login')); ?></a></li>
                                    <li><a href="<?php echo e(route('register')); ?>"><?php echo e(__('register')); ?></a></li>
                                </ul>
                            <?php endif; ?>
                            <?php if(auth()->guard()->check()): ?>
                                <?php if(Auth::guard('web')->user()->role == 'vendor'): ?>
                                    <ul class="mobile_menu_login d-flex flex-wrap">
                                        <li><a href="<?php echo e(route('sso.shop')); ?>?target=dashboard"><?php echo e(__('Vendor Dashboard')); ?></a></li>
                                        <li><a href="javascript:void(0)" class="text-danger logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><?php echo e(__('Logout')); ?></a></li>
                                    </ul>
                                <?php elseif(Auth::guard('web')->user()->role == 'instructor'): ?>
                                    <ul class="mobile_menu_login d-flex flex-wrap">
                                        <li><a href="<?php echo e(route('instructor.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                        <li><a href="<?php echo e(route('instructor.courses.index')); ?>"><?php echo e(__('Courses')); ?></a></li>
                                        <li><a href="javascript:void(0)" class="text-danger logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><?php echo e(__('Logout')); ?></a></li>
                                    </ul>
                                <?php elseif(Auth::guard('web')->user()->role == 'school'): ?>
                                    <ul class="mobile_menu_login d-flex flex-wrap">
                                        <li><a href="<?php echo e(route('school.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                        <li><a href="<?php echo e(route('school.courses.index')); ?>"><?php echo e(__('Courses')); ?></a></li>
                                        <li><a href="javascript:void(0)" class="text-danger logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><?php echo e(__('Logout')); ?></a></li>
                                    </ul>
                                <?php else: ?>
                                    <ul class="mobile_menu_login d-flex flex-wrap">
                                        <li><a href="<?php echo e(route('student.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                        <li><a href="<?php echo e(route('student.enrolled-courses')); ?>"><?php echo e(__('Courses')); ?></a></li>
                                        <li><a href="javascript:void(0)" class="text-danger logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><?php echo e(__('Logout')); ?></a></li>
                                    </ul>
                                <?php endif; ?>
                            <?php endif; ?>

                            <!-- <div class="tgmobile__search">
                                <form action="<?php echo e(route('courses')); ?>">
                                    <select class="form-select w_150px" aria-label="Default select example"
                                        name="main_category">
                                        <option selected disabled><?php echo e(__('Categories')); ?></option>
                                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option <?php if(request('main_category') == $category->slug): echo 'selected'; endif; ?> value="<?php echo e($category->slug); ?>">
                                                <?php echo e($category->translation->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <input type="text" placeholder="<?php echo e(__('Search here')); ?>..." name="search">
                                    <button><i class="fas fa-search"></i></button>
                                </form>
                            </div> -->
                            <div class="tgmobile__menu-outer">
                                <!--Here Menu Will Come Automatically Via Javascript / Same Menu as in Header-->
                            </div>
                            <div class="social-links">
                                <?php if(count(getSocialLinks()) > 0): ?>
                                    <ul class="list-wrap">
                                        <?php $__currentLoopData = getSocialLinks(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $socialLink): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li>
                                                <a href="<?php echo e($socialLink->link); ?>" target="_blank">
                                                    <img src="<?php echo e(asset($socialLink->icon)); ?>" alt="img">
                                                </a>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </nav>
                    </div>
                    <div class="tgmobile__menu-backdrop"></div>
                    <!-- End Mobile Menu -->

                    
                    <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
                        <?php echo csrf_field(); ?>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</header>
<!-- header-area-end -->
<style>
/* ── Navigation bar alignment ── */
.tgmenu__nav {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    width: 100%;
}

@media (min-width: 1200px) {
    .tgmenu__navbar-wrap {
        display: flex !important;
        flex-grow: 1 !important;
        justify-content: center !important;
    }
    .tgmenu__navbar-wrap ul,
    .tgmenu__navbar-wrap ul.navigation {
        margin: 0 auto !important;
        justify-content: center !important;
    }
    .mega-menu-parent .sub-menu {
        display: none !important;
    }
    .tgmenu__main-menu li.menu-item-has-children > a::after {
        transition: transform 0.25s ease;
    }
    .tgmenu__main-menu li.menu-item-has-children:hover > a::after {
        transform: rotate(270deg) !important;
    }
}

@media (max-width: 1199.98px) {
    .tgmenu__navbar-wrap {
        display: none !important;
    }
    .mobile-nav-toggler {
        display: flex !important;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        color: var(--tg-theme-primary, #1976d2);
        cursor: pointer;
        padding: 6px;
        margin-left: 8px;
    }
    .tgmenu__action {
        display: none !important;
        align-items: center;
        margin: 0 0 0 auto !important;
    }
    .tgmenu__action > ul {
        display: flex !important;
        align-items: center;
        gap: 12px;
        margin: 0;
        padding: 0;
    }
    /* Strictly hide any image card panels on mobile */
    .mega-dropdown-panel,
    .tgmobile__menu .mega-dropdown-panel {
        display: none !important;
        visibility: hidden !important;
        height: 0 !important;
        max-height: 0 !important;
        overflow: hidden !important;
        opacity: 0 !important;
        pointer-events: none !important;
    }
}

/* ── Mega dropdown panel (Desktop) ──────── */
.mega-menu-parent {
    position: relative;
}

.mega-menu-parent > a {
    display: inline-flex !important;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
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
    z-index: 999;
    box-sizing: border-box;
    margin-top: 6px;
}

/* Hover bridge so mouse movement into dropdown is seamless */
.mega-dropdown-panel::after {
    content: '';
    position: absolute;
    top: -14px;
    left: 0;
    width: 100%;
    height: 14px;
    background: transparent;
}

/* Upward pointer arrow */
.mega-dropdown-panel::before {
    content: '';
    position: absolute;
    top: -8px;
    left: 50%;
    transform: translateX(-50%) rotate(45deg);
    width: 16px;
    height: 16px;
    background: #ffffff;
    border-left: 1px solid #e2e8f0;
    border-top: 1px solid #e2e8f0;
    border-radius: 3px 0 0 0;
    z-index: 10;
}

.mega-menu-parent:hover .mega-dropdown-panel,
.mega-menu-parent:focus-within .mega-dropdown-panel {
    display: block !important;
    animation: megaDropdownFade 0.2s ease forwards;
}

@keyframes megaDropdownFade {
    from {
        opacity: 0;
        transform: translate(-50%, 6px);
    }
    to {
        opacity: 1;
        transform: translate(-50%, 0);
    }
}

.mega-dropdown-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 18px;
    padding-bottom: 0;
    border-bottom: none;
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

/* ── Individual Card (Title on TOP, Photo on BOTTOM) ─────── */
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

.mega-card--active {
    border-color: #6366f1;
    box-shadow: 0 8px 20px -4px rgba(99, 102, 241, 0.15);
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
    color: var(--tg-theme-primary, #1976d2);
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
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Clean up any cloned mega-dropdown panels from the mobile drawer
    function purgeMobileMegaPanels() {
        $('.tgmobile__menu .mega-dropdown-panel').remove();
    }
    purgeMobileMegaPanels();
    $(document).on('click', '.mobile-nav-toggler', function () {
        purgeMobileMegaPanels();
    });

    // Allow clicking parent link in mobile drawer to open submenu
    $(document).on('click', '.tgmobile__menu li.menu-item-has-children > a', function (e) {
        var href = $(this).attr('href');
        if (href === 'javascript:;' || href === 'javascript:void(0);' || href === '#' || href === '') {
            e.preventDefault();
            var $btn = $(this).siblings('.dropdown-btn');
            if ($btn.length) {
                $btn.trigger('click');
            } else {
                $(this).siblings('ul').slideToggle(300);
            }
        }
    });
});
</script>
<?php /**PATH /var/www/html/pro/resources/views/frontend/layouts/header.blade.php ENDPATH**/ ?>