@extends('admin.master_layout')

@section('title')
    <title>{{ $title }}</title>
@endsection

@section('admin-content')
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex justify-content-between align-items-center">
            <h1><i class="fas fa-tags text-warning mr-2"></i> {{ __('Lab Offer & Promotional Campaign') }}</h1>
            <div class="section-header-breadcrumb d-flex align-items-center">
                <a href="{{ url('/lab-offer') }}" target="_blank" class="btn btn-outline-primary mr-2">
                    <i class="fas fa-external-link-alt mr-1"></i> {{ __('View Landing Page') }}
                </a>
                <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></div>
                <div class="breadcrumb-item active">{{ __('Lab Offers') }}</div>
            </div>
        </div>

        <div class="section-body">
            @if(session('messege'))
                <div class="alert alert-{{ session('alert-type') == 'success' ? 'success' : 'info' }} alert-dismissible show fade">
                    <div class="alert-body">
                        <button class="close" data-dismiss="alert"><span>&times;</span></button>
                        {{ session('messege') }}
                    </div>
                </div>
            @endif

            <form action="{{ route('admin.lab-offers.save') }}" method="POST" id="labOfferForm">
                @csrf

                {{-- Campaign Status & Overview Card --}}
                <div class="card mb-4 border-left-{{ $offer->is_active ? 'success' : 'danger' }}" style="border-left-width: 5px;">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="mb-1 d-flex align-items-center">
                                    <span class="badge badge-{{ $offer->is_active ? 'success' : 'danger' }} mr-2 px-3 py-2 text-uppercase" id="statusBadge">
                                        {{ $offer->is_active ? __('Campaign Active (Enabled)') : __('Campaign Inactive (Disabled)') }}
                                    </span>
                                    <span>{{ $offer->title }}</span>
                                </h5>
                                <p class="text-muted mb-0 small">
                                    {{ __('When enabled, visitors can access the promotional landing page at') }}
                                    <a href="{{ url('/lab-offer') }}" target="_blank" class="font-weight-bold text-primary">{{ url('/lab-offer') }}</a>
                                    {{ __('and proceed directly through payment or proposal flow.') }}
                                </p>
                            </div>
                            <div class="col-md-4 text-md-right mt-3 mt-md-0">
                                <div class="custom-control custom-switch custom-control-lg d-inline-block text-left mr-3">
                                    <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="isActiveSwitch" {{ $offer->is_active ? 'checked' : '' }}>
                                    <label class="custom-control-label font-weight-bold cursor-pointer" for="isActiveSwitch">
                                        <span id="switchLabel">{{ $offer->is_active ? __('Enabled') : __('Disabled') }}</span>
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-primary px-4 font-weight-bold shadow-sm">
                                    <i class="fas fa-save mr-1"></i> {{ __('Save All Changes') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Campaign Details & Configuration --}}
                <div class="row">
                    <div class="col-lg-7">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom">
                                <h4 class="text-dark font-weight-bold"><i class="fas fa-bullhorn text-primary mr-2"></i> {{ __('1. Campaign Content & Messaging') }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="font-weight-bold">{{ __('Campaign Title') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" value="{{ old('title', $offer->title) }}" required placeholder="e.g. Special Skill Lab Festival Offer">
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">{{ __('Promotional Top Badge') }}</label>
                                    <input type="text" name="badge_text" class="form-control" value="{{ old('badge_text', $offer->badge_text) }}" placeholder="e.g. 🔥 EXCLUSIVE SCHOOL PROMOTION — SAVE UP TO 35%">
                                    <small class="form-text text-muted">{{ __('Displayed as an eye-catching pill above the main headline.') }}</small>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">{{ __('Headline Subtitle / Value Pitch') }}</label>
                                    <textarea name="subtitle" class="form-control" rows="3" placeholder="Explain the key institutional offer value">{{ old('subtitle', $offer->subtitle) }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">{{ __('Offer Key Highlights (1 per line)') }}</label>
                                    <textarea name="highlights" class="form-control" rows="5" placeholder="100% CBSE & NEP 2020 Compliant&#10;Turnkey Hardware & Equipment&#10;Free Teacher Masterclass Training">{{ is_array($offer->highlights) ? implode("\n", $offer->highlights) : $offer->highlights }}</textarea>
                                    <small class="form-text text-muted">{{ __('Each line appears as a green checkmark feature on the hero section.') }}</small>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">{{ __('Terms & Conditions / Disclaimer') }}</label>
                                    <textarea name="terms" class="form-control" rows="3">{{ old('terms', $offer->terms) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom">
                                <h4 class="text-dark font-weight-bold"><i class="fas fa-clock text-warning mr-2"></i> {{ __('2. Countdown & Support Contacts') }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="font-weight-bold">{{ __('Offer Expiration Date & Time') }}</label>
                                    <input type="datetime-local" name="countdown_end" class="form-control" value="{{ $offer->countdown_end ? $offer->countdown_end->format('Y-m-d\TH:i') : '' }}">
                                    <small class="form-text text-muted">{{ __('Drives the live countdown clock (Days, Hours, Minutes, Seconds) on the landing page.') }}</small>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold"><i class="fab fa-whatsapp text-success mr-1"></i> {{ __('WhatsApp Enquiry Number') }}</label>
                                    <input type="text" name="whatsapp_number" class="form-control" value="{{ old('whatsapp_number', $offer->whatsapp_number) }}" placeholder="+919876543210">
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold"><i class="fas fa-phone-alt text-primary mr-1"></i> {{ __('Support Phone') }}</label>
                                    <input type="text" name="support_phone" class="form-control" value="{{ old('support_phone', $offer->support_phone) }}" placeholder="+91 80 4718 1234">
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold"><i class="fas fa-envelope text-info mr-1"></i> {{ __('Support Email') }}</label>
                                    <input type="email" name="support_email" class="form-control" value="{{ old('support_email', $offer->support_email) }}" placeholder="admissions@skillvation.com">
                                </div>

                                <div class="p-3 bg-light rounded border mt-3">
                                    <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-info-circle text-primary mr-1"></i> {{ __('Checkout & Payment Flow') }}</h6>
                                    <p class="small text-muted mb-0">
                                        {{ __('On the landing page, clicking "Buy Now" triggers an immediate checkout modal powered by the active Razorpay gateway and creates an authenticated order in Shop Orders. Alternatively, schools can request a customized proposal or customize components in the Club Shop cart.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pre-Booking & Advance Payment Configuration --}}
                <div class="card shadow-sm mb-4 border-left border-warning" style="border-left-width: 5px !important;">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="text-dark font-weight-bold mb-0">
                                <i class="fas fa-hand-holding-usd text-warning mr-2"></i> {{ __('3. Pre-Booking & Advance Payment Configuration') }}
                            </h4>
                            <small class="text-muted">{{ __('Allow customers to lock-in the promotional offer with a token advance payment (e.g., ₹25,000) OR get an extra discount (e.g., 10%) on full upfront payment.') }}</small>
                        </div>
                        <div class="custom-control custom-switch custom-control-lg">
                            <input type="checkbox" name="booking_enabled" value="1" class="custom-control-input" id="bookingEnabledSwitch" {{ ($offer->booking_enabled ?? true) ? 'checked' : '' }}>
                            <label class="custom-control-label font-weight-bold cursor-pointer" for="bookingEnabledSwitch">
                                {{ __('Enable Booking Feature') }}
                            </label>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">{{ __('Advance Booking Token (₹)') }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text">₹</span></div>
                                        <input type="number" step="100" min="0" name="booking_token_amount" class="form-control font-weight-bold text-success" value="{{ old('booking_token_amount', $offer->booking_token_amount ?? 25000) }}" placeholder="25000">
                                    </div>
                                    <small class="form-text text-muted">{{ __('Amount charged via payment gateway to lock in promo price.') }}</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">{{ __('Extra Full-Payment Discount (%)') }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" step="0.5" min="0" max="100" name="booking_extra_discount_percent" class="form-control font-weight-bold text-danger" value="{{ old('booking_extra_discount_percent', $offer->booking_extra_discount_percent ?? 10) }}" placeholder="10">
                                        <div class="input-group-append"><span class="input-group-text">% OFF</span></div>
                                    </div>
                                    <small class="form-text text-muted">{{ __('Instant discount if school pays in full online.') }}</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">{{ __('Booking Offer Heading') }}</label>
                                    <input type="text" name="booking_title" class="form-control" value="{{ old('booking_title', $offer->booking_title ?? 'Lock In Promotional Offer — Pre-Book or Pay Full') }}" placeholder="e.g. Special Booking & Advance Payment Offer">
                                    <small class="form-text text-muted">{{ __('Heading shown above the payment options in checkout.') }}</small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold">{{ __('Booking Subtitle / Value Pitch') }}</label>
                                    <input type="text" name="booking_subtitle" class="form-control" value="{{ old('booking_subtitle', $offer->booking_subtitle ?? 'Pre-book your turnkey school lab with ₹25,000 advance token to lock promo price, or pay in full today for an extra 10% instant discount!') }}" placeholder="Subtitle / callout description">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Promotional Products Section --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="text-dark font-weight-bold mb-0">
                                <i class="fas fa-boxes text-success mr-2"></i> {{ __('4. Products & Lab Bundles in Promotion') }}
                            </h4>
                            <small class="text-muted">{{ __('Configure the promotional lab packages displayed on the offer page with special discount pricing.') }}</small>
                        </div>
                        <button type="button" class="btn btn-success font-weight-bold" onclick="addPromoProduct()">
                            <i class="fas fa-plus mr-1"></i> {{ __('Add Product') }}
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="promoProductsContainer">
                            @php
                                $promoProducts = is_array($offer->products) ? $offer->products : [];
                            @endphp

                            @forelse($promoProducts as $idx => $prod)
                                <div class="card promo-card mb-4 border border-secondary shadow-none" id="product_card_{{ $idx }}" style="background:#fcfdfd;">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3">
                                        <div class="font-weight-bold text-dark">
                                            <span class="badge badge-dark mr-2">#{{ $idx + 1 }}</span>
                                            <span class="product-title-display">{{ $prod['title'] ?? 'Promotional Product' }}</span>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removePromoProduct({{ $idx }})">
                                                <i class="fas fa-trash-alt mr-1"></i> {{ __('Remove') }}
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <input type="hidden" name="products[{{ $idx }}][id]" value="{{ $prod['id'] ?? 'promo_' . $idx }}">
                                        <input type="hidden" name="products[{{ $idx }}][package_slug]" class="prod-slug-input" value="{{ $prod['package_slug'] ?? $prod['slug'] ?? '' }}">
                                        <input type="hidden" name="products[{{ $idx }}][cart_url]" class="prod-cart-url-input" value="{{ $prod['cart_url'] ?? '' }}">

                                        {{-- Catalog Auto-Fill Dropdown --}}
                                        <div class="row mb-3 bg-white p-2 rounded border mx-0 align-items-center">
                                            <div class="col-md-3">
                                                <small class="font-weight-bold text-muted text-uppercase">{{ __('Quick Fill from Catalog:') }}</small>
                                            </div>
                                            <div class="col-md-9">
                                                <select class="form-control form-control-sm select-catalog-prod" onchange="fillFromCatalog(this, {{ $idx }})">
                                                    <option value="">-- {{ __('Select a ClubShop Product / Bundle to Auto-Fill') }} --</option>
                                                    @foreach($clubShopProducts as $cp)
                                                        @php
                                                            $discPrice = (!empty($cp->price_discounted) && (float)$cp->price_discounted > 0) ? (float)$cp->price_discounted : (float)$cp->price;
                                                            $hasDiscount = $discPrice > 0 && $discPrice < (float)$cp->price;
                                                        @endphp
                                                        <option value="{{ $cp->id }}"
                                                                data-title="{{ $cp->title }}"
                                                                data-price="{{ $cp->price }}"
                                                                data-offer-price="{{ $discPrice }}"
                                                                data-slug="{{ $cp->slug }}"
                                                                {{ (!empty($prod['product_id']) && $prod['product_id'] == $cp->id) ? 'selected' : '' }}>
                                                            {{ $cp->is_bundle ? '📦 [BUNDLE] ' : '🔧 [PRODUCT] ' }}{{ $cp->title }} — ₹{{ number_format($cp->price) }}{{ $hasDiscount ? ' (Offer: ₹' . number_format($discPrice) . ')' : '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-7">
                                                <div class="form-group mb-2">
                                                    <label class="font-weight-bold small">{{ __('Package / Product Title') }} <span class="text-danger">*</span></label>
                                                    <input type="text" name="products[{{ $idx }}][title]" class="form-control form-control-sm prod-title-input" value="{{ $prod['title'] ?? '' }}" required placeholder="e.g. Standard Skillvation Lab (With Lab Infra)">
                                                </div>

                                                <div class="form-group mb-2">
                                                    <label class="font-weight-bold small">{{ __('Subtitle / Target Segment') }}</label>
                                                    <input type="text" name="products[{{ $idx }}][subtitle]" class="form-control form-control-sm" value="{{ $prod['subtitle'] ?? '' }}" placeholder="e.g. Complete Turnkey Experiential Hub with Modular Workstations">
                                                </div>

                                                <div class="form-group mb-2">
                                                    <label class="font-weight-bold small">{{ __('Promotional Badge Text') }}</label>
                                                    <input type="text" name="products[{{ $idx }}][badge]" class="form-control form-control-sm" value="{{ $prod['badge'] ?? '' }}" placeholder="e.g. MOST POPULAR — SAVE ₹1,08,438">
                                                </div>

                                                <div class="form-group mb-2">
                                                    <label class="font-weight-bold small">{{ __('Key Inclusions / Features (1 per line)') }}</label>
                                                    <textarea name="products[{{ $idx }}][features]" class="form-control form-control-sm" rows="4" placeholder="18 Turnkey Skill Domains&#10;Complete Lab Infrastructure&#10;20 Student Activity Kits">{{ is_array($prod['features'] ?? null) ? implode("\n", $prod['features']) : ($prod['features'] ?? '') }}</textarea>
                                                </div>
                                            </div>

                                            <div class="col-md-5">
                                                <div class="p-3 bg-white rounded border mb-2">
                                                    <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">{{ __('Pricing & Discount') }}</h6>
                                                    
                                                    <div class="form-group mb-2">
                                                        <label class="font-weight-bold small">{{ __('Regular Price / MRP (₹)') }}</label>
                                                        <input type="number" step="any" name="products[{{ $idx }}][mrp]" class="form-control form-control-sm prod-mrp-input" value="{{ $prod['mrp'] ?? 0 }}" oninput="updateCardSavings({{ $idx }})">
                                                    </div>

                                                    <div class="form-group mb-2">
                                                        <label class="font-weight-bold small text-success">{{ __('Special Offer Price (₹)') }} <span class="text-danger">*</span></label>
                                                        <input type="number" step="any" name="products[{{ $idx }}][offer_price]" class="form-control form-control-sm prod-offer-input font-weight-bold text-success" value="{{ $prod['offer_price'] ?? 0 }}" required oninput="updateCardSavings({{ $idx }})">
                                                    </div>

                                                    <div class="alert alert-success py-1 px-2 mb-0 small" id="savings_badge_{{ $idx }}">
                                                        @php
                                                            $m = (float)($prod['mrp'] ?? 0);
                                                            $o = (float)($prod['offer_price'] ?? 0);
                                                            $sav = $m > $o ? ($m - $o) : 0;
                                                            $pct = $m > 0 && $sav > 0 ? round(($sav / $m) * 100) : 0;
                                                        @endphp
                                                        <strong>{{ __('Discount:') }}</strong> <span class="savings-amount">₹{{ number_format($sav) }}</span> (<span class="savings-percent">{{ $pct }}%</span> off)
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="form-group mb-2">
                                                            <label class="font-weight-bold small">{{ __('Catalog Product ID') }}</label>
                                                            <input type="number" name="products[{{ $idx }}][product_id]" class="form-control form-control-sm prod-id-input" value="{{ $prod['product_id'] ?? '' }}" placeholder="e.g. 74">
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group mb-2">
                                                            <label class="font-weight-bold small">{{ __('Stock / Slot Status') }}</label>
                                                            <select name="products[{{ $idx }}][stock_status]" class="form-control form-control-sm">
                                                                <option value="in_stock" {{ ($prod['stock_status'] ?? '') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                                                                <option value="limited_slots" {{ ($prod['stock_status'] ?? '') == 'limited_slots' ? 'selected' : '' }}>Limited Slots</option>
                                                                <option value="almost_full" {{ ($prod['stock_status'] ?? '') == 'almost_full' ? 'selected' : '' }}>Almost Sold Out</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="form-group mb-2">
                                                            <label class="font-weight-bold small">{{ __('Sort Order') }}</label>
                                                            <input type="number" name="products[{{ $idx }}][sort_order]" class="form-control form-control-sm" value="{{ $prod['sort_order'] ?? ($idx + 1) }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group mb-2 pt-4">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="products[{{ $idx }}][is_active]" value="1" class="custom-control-input" id="prodActive_{{ $idx }}" {{ !empty($prod['is_active']) ? 'checked' : '' }}>
                                                                <label class="custom-control-label font-weight-bold small" for="prodActive_{{ $idx }}">{{ __('Visible in Offer') }}</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted" id="noProductsNotice">
                                    <i class="fas fa-boxes fa-3x mb-2 text-secondary"></i>
                                    <p>{{ __('No promotional products configured yet. Click "Add Product" to add your first offer package.') }}</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    <div class="card-footer bg-light border-top d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-outline-success font-weight-bold" onclick="addPromoProduct()">
                            <i class="fas fa-plus mr-1"></i> {{ __('Add Another Product') }}
                        </button>
                        <button type="submit" class="btn btn-primary px-4 font-weight-bold shadow-sm">
                            <i class="fas fa-save mr-1"></i> {{ __('Save All Changes') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

<script>
var productIdx = {{ count($promoProducts) }};
var catalogList = @json($clubShopProducts);

function addPromoProduct() {
    var notice = document.getElementById('noProductsNotice');
    if (notice) notice.style.display = 'none';

    var container = document.getElementById('promoProductsContainer');
    var i = productIdx++;

    var html = `
    <div class="card promo-card mb-4 border border-secondary shadow-none" id="product_card_${i}" style="background:#fcfdfd;">
        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3">
            <div class="font-weight-bold text-dark">
                <span class="badge badge-dark mr-2">#${i + 1}</span>
                <span class="product-title-display">New Promotional Product</span>
            </div>
            <div>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removePromoProduct(${i})">
                    <i class="fas fa-trash-alt mr-1"></i> Remove
                </button>
            </div>
        </div>
        <div class="card-body">
            <input type="hidden" name="products[${i}][id]" value="promo_${Date.now()}">
            <input type="hidden" name="products[${i}][package_slug]" class="prod-slug-input" value="">
            <input type="hidden" name="products[${i}][cart_url]" class="prod-cart-url-input" value="">

            <div class="row mb-3 bg-white p-2 rounded border mx-0 align-items-center">
                <div class="col-md-3">
                    <small class="font-weight-bold text-muted text-uppercase">Quick Fill from Catalog:</small>
                </div>
                <div class="col-md-9">
                    <select class="form-control form-control-sm select-catalog-prod" onchange="fillFromCatalog(this, ${i})">
                        <option value="">-- Select a ClubShop Product / Bundle to Auto-Fill --</option>
                        ${catalogList.map(c => `<option value="${c.id}" data-title="${c.title}" data-price="${c.price}" data-offer-price="${c.price_discounted && Number(c.price_discounted) > 0 ? c.price_discounted : c.price}" data-slug="${c.slug}">${c.is_bundle ? '📦 [BUNDLE] ' : '🔧 [PRODUCT] '}${c.title} — ₹${Number(c.price).toLocaleString('en-IN')}${c.price_discounted && Number(c.price_discounted) > 0 && Number(c.price_discounted) < Number(c.price) ? ' (Offer: ₹' + Number(c.price_discounted).toLocaleString('en-IN') + ')' : ''}</option>`).join('')}
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-7">
                    <div class="form-group mb-2">
                        <label class="font-weight-bold small">Package / Product Title <span class="text-danger">*</span></label>
                        <input type="text" name="products[${i}][title]" class="form-control form-control-sm prod-title-input" required placeholder="e.g. Standard Skillvation Lab (With Lab Infra)">
                    </div>

                    <div class="form-group mb-2">
                        <label class="font-weight-bold small">Subtitle / Target Segment</label>
                        <input type="text" name="products[${i}][subtitle]" class="form-control form-control-sm" placeholder="e.g. Complete Turnkey Experiential Hub with Modular Workstations">
                    </div>

                    <div class="form-group mb-2">
                        <label class="font-weight-bold small">Promotional Badge Text</label>
                        <input type="text" name="products[${i}][badge]" class="form-control form-control-sm" placeholder="e.g. LIMITED OFFER — SAVE ₹50,000">
                    </div>

                    <div class="form-group mb-2">
                        <label class="font-weight-bold small">Key Inclusions / Features (1 per line)</label>
                        <textarea name="products[${i}][features]" class="form-control form-control-sm" rows="4" placeholder="Feature item 1&#10;Feature item 2&#10;Feature item 3"></textarea>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="p-3 bg-white rounded border mb-2">
                        <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">Pricing & Discount</h6>
                        
                        <div class="form-group mb-2">
                            <label class="font-weight-bold small">Regular Price / MRP (₹)</label>
                            <input type="number" step="any" name="products[${i}][mrp]" class="form-control form-control-sm prod-mrp-input" value="0" oninput="updateCardSavings(${i})">
                        </div>

                        <div class="form-group mb-2">
                            <label class="font-weight-bold small text-success">Special Offer Price (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="any" name="products[${i}][offer_price]" class="form-control form-control-sm prod-offer-input font-weight-bold text-success" value="0" required oninput="updateCardSavings(${i})">
                        </div>

                        <div class="alert alert-success py-1 px-2 mb-0 small" id="savings_badge_${i}">
                            <strong>Discount:</strong> <span class="savings-amount">₹0</span> (<span class="savings-percent">0%</span> off)
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-2">
                                <label class="font-weight-bold small">Catalog Product ID</label>
                                <input type="number" name="products[${i}][product_id]" class="form-control form-control-sm prod-id-input" placeholder="e.g. 74">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-2">
                                <label class="font-weight-bold small">Stock / Slot Status</label>
                                <select name="products[${i}][stock_status]" class="form-control form-control-sm">
                                    <option value="in_stock" selected>In Stock</option>
                                    <option value="limited_slots">Limited Slots</option>
                                    <option value="almost_full">Almost Sold Out</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-2">
                                <label class="font-weight-bold small">Sort Order</label>
                                <input type="number" name="products[${i}][sort_order]" class="form-control form-control-sm" value="${i + 1}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-2 pt-4">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="products[${i}][is_active]" value="1" class="custom-control-input" id="prodActive_${i}" checked>
                                    <label class="custom-control-label font-weight-bold small" for="prodActive_${i}">Visible in Offer</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>`;

    container.insertAdjacentHTML('beforeend', html);
}

function removePromoProduct(idx) {
    if (confirm("Are you sure you want to remove this promotional product from the offer?")) {
        var card = document.getElementById('product_card_' + idx);
        if (card) card.remove();
    }
}

function fillFromCatalog(sel, idx) {
    var opt = sel.options[sel.selectedIndex];
    if (!opt || !opt.value) return;

    var card = document.getElementById('product_card_' + idx);
    if (!card) return;

    var title = opt.getAttribute('data-title') || '';
    var price = parseFloat(opt.getAttribute('data-price') || 0);
    var offerPrice = parseFloat(opt.getAttribute('data-offer-price') || 0);
    var pid = opt.value;
    var slug = opt.getAttribute('data-slug') || '';

    var titleInp = card.querySelector('.prod-title-input');
    var mrpInp = card.querySelector('.prod-mrp-input');
    var offerInp = card.querySelector('.prod-offer-input');
    var idInp = card.querySelector('.prod-id-input');
    var slugInp = card.querySelector('.prod-slug-input');
    var titleDisplay = card.querySelector('.product-title-display');

    if (titleInp) titleInp.value = title;
    if (titleDisplay) titleDisplay.textContent = title;
    if (mrpInp) mrpInp.value = price;
    if (offerInp) offerInp.value = (offerPrice > 0) ? offerPrice : price;
    if (idInp) idInp.value = pid;
    if (slugInp) slugInp.value = slug;

    updateCardSavings(idx);
}

function updateCardSavings(idx) {
    var card = document.getElementById('product_card_' + idx);
    if (!card) return;

    var mrp = parseFloat(card.querySelector('.prod-mrp-input')?.value || 0);
    var offer = parseFloat(card.querySelector('.prod-offer-input')?.value || 0);
    var badge = document.getElementById('savings_badge_' + idx);

    if (badge) {
        var sav = mrp > offer ? (mrp - offer) : 0;
        var pct = mrp > 0 && sav > 0 ? Math.round((sav / mrp) * 100) : 0;
        badge.querySelector('.savings-amount').textContent = '₹' + Math.round(sav).toLocaleString('en-IN');
        badge.querySelector('.savings-percent').textContent = pct + '%';
    }
}

// Live switch label toggle
document.getElementById('isActiveSwitch')?.addEventListener('change', function() {
    var label = document.getElementById('switchLabel');
    var badge = document.getElementById('statusBadge');
    if (this.checked) {
        if (label) label.textContent = 'Enabled';
        if (badge) {
            badge.className = 'badge badge-success mr-2 px-3 py-2 text-uppercase';
            badge.textContent = 'Campaign Active (Enabled)';
        }
    } else {
        if (label) label.textContent = 'Disabled';
        if (badge) {
            badge.className = 'badge badge-danger mr-2 px-3 py-2 text-uppercase';
            badge.textContent = 'Campaign Inactive (Disabled)';
        }
    }
});
</script>
@endsection

