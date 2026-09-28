<div class="top-bar">
<div class="container">
<div class="d-flex justify-content-between align-items-center">
<div class="col-left d-flex align-items-center">
<ul class="navbar-nav clearfix flex-row align-items-center top-info-links">
    <li class="nav-item">
        <a href="<?= mainPortalUrl('/'); ?>" class="nav-link top-portal-badge" title="Skillvation Learning Portal">
            <i class="icon-book-open"></i> <span>Main LMS Portal</span>
        </a>
    </li>
    <?php if (!empty($baseSettings->contact_email)): ?>
    <li class="nav-item d-none d-md-inline-block">
        <a href="mailto:<?= esc($baseSettings->contact_email); ?>" class="nav-link">
            <i class="icon-mail"></i> <span><?= esc($baseSettings->contact_email); ?></span>
        </a>
    </li>
    <?php endif; ?>
    <?php if (!empty($baseSettings->contact_phone)): ?>
    <li class="nav-item d-none d-lg-inline-block">
        <a href="tel:<?= esc($baseSettings->contact_phone); ?>" class="nav-link">
            <i class="icon-phone"></i> <span><?= esc($baseSettings->contact_phone); ?></span>
        </a>
    </li>
    <?php endif; ?>
    <?php if (!empty($menuLinks)):
    foreach ($menuLinks as $menuLink):
    if ($menuLink->location == 'top_menu'):
    $itemLink = generateMenuItemUrl($menuLink);
    if (!empty($menuLink->page_default_name)):
    $itemLink = generateUrl($menuLink->page_default_name);
    endif; ?>
    <li class="nav-item d-none d-xl-inline-block"><a href="<?= $itemLink; ?>" class="nav-link"><?= esc($menuLink->title); ?></a></li>
    <?php endif;
    endforeach;
    endif; ?>
</ul>
</div>
<div class="col-right">
<ul class="navbar-nav clearfix flex-row align-items-center">
<?php if ($generalSettings->location_search_header == 1 && countItems($activeCountries) > 0): ?>
<li class="nav-item d-none d-md-inline-block">
<button type="button" data-toggle="modal" data-target="#locationModal" class="nav-link btn-modal-location button-link btn-modal-location-header display-flex align-items-center" aria-label="location-modal">
<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16 16" fill="currentColor">
<path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
</svg>&nbsp;<?= !empty($baseVars->defaultLocationInput) ? $baseVars->defaultLocationInput : trans("location"); ?>
</button>
<?php if (!empty($baseVars->defaultLocationInput)): ?>
<form action="<?= base_url('Home/setDefaultLocationPost'); ?>" method="post" class="display-inline-block">
<?= csrf_field(); ?>
&nbsp;&nbsp;
<button type="submit" name="submit" value="reset" class="btn-reset-location"><?= trans("reset"); ?></button>
</form>
<?php endif; ?>
</li>
<?php endif; ?>
<?php if ($paymentSettings->currency_converter == 1 && countItems($currencies) > 1): ?>
<li class="nav-item dropdown top-menu-dropdown">
<button type="button" class="nav-link dropdown-toggle button-link" data-toggle="dropdown" aria-label="select-currency">
<?= getSelectedCurrency()->code; ?>&nbsp;(<?= getSelectedCurrency()->symbol; ?>)&nbsp;<i class="icon-arrow-down"></i>
</button>
<form action="<?= base_url('set-selected-currency-post'); ?>" method="post">
<?= csrf_field(); ?>
<ul class="dropdown-menu">
<?php foreach ($currencies as $currency):
if ($currency->status == 1):?>
<li>
<button type="submit" name="currency" value="<?= $currency->code; ?>"><?= $currency->code; ?>&nbsp;(<?= $currency->symbol; ?>)</button>
</li>
<?php endif;
endforeach; ?>
</ul>
</form>
</li>
<?php endif; ?>
<?php if ($generalSettings->multilingual_system == 1 && countItems($activeLanguages) > 1): ?>
<li class="nav-item dropdown top-menu-dropdown">
<button type="button" class="nav-link dropdown-toggle button-link" data-toggle="dropdown" aria-label="select-flag">
<img src="<?= base_url($activeLang->flag_path); ?>" class="flag" style="width: 16px; height: auto;" alt="<?= esc($activeLang->name) . " " . trans("active"); ?>"><?= esc($activeLang->name); ?>&nbsp;<i class="icon-arrow-down"></i>
</button>
<ul class="dropdown-menu dropdown-menu-lang">
<?php foreach ($activeLanguages as $language): ?>
<li>
<a href="<?= convertUrlByLanguage($language); ?>" class="dropdown-item <?= $language->id == $activeLang->id ? 'selected' : ''; ?>">
<img src="<?= base_url($language->flag_path); ?>" class="flag" style="width: 16px; height: auto;" alt="<?= esc($language->name); ?>"><?= esc($language->name); ?>
</a>
</li>
<?php endforeach; ?>
</ul>
</li>
<?php endif;
if (authCheck()): ?>
<li class="nav-item dropdown profile-dropdown p-r-0">
<button type="button" class="nav-link dropdown-toggle a-profile button-link" data-toggle="dropdown" aria-expanded="false" aria-label="select-user">
<img src="<?= getUserAvatar(user()->avatar, user()->storage_avatar); ?>" alt="<?= esc(getUsername(user())); ?>" width="24" height="24" class="rounded-circle">
<span><?= characterLimiter(esc(getUsername(user())), 12, '..'); ?></span>
<i class="icon-arrow-down"></i>
<?php if ($baseVars->unreadMessageCount > 0): ?>
<span class="message-notification"><?= $baseVars->unreadMessageCount; ?></span>
<?php endif; ?>
</button>
<ul class="dropdown-menu">
<?= view("nav/_profile_dropdown.php", ['profileMenuAdmin' => true, 'profileMenuDash' => true]); ?>
</ul>
</li>
<?php else: ?>
<li class="nav-item auth-links">
<button type="button" data-toggle="modal" data-target="#loginModal" class="nav-link button-link" aria-label="login"><i class="icon-user"></i> <?= trans("login"); ?></button>
<span class="auth-sep">/</span>
<a href="<?= generateUrl('register'); ?>" class="nav-link"><?= trans("register"); ?></a>
</li>
<?php endif; ?>
</ul>
</div>
</div>
</div>
</div>