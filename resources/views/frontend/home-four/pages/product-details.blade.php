@extends('frontend.home-four.layouts.master')

@section('meta_title', 'Product Details — ' . config('app.name', 'Skillvation'))
@section('meta_description', 'View hands-on activity kit specifications, contents, pricing, and materials.')

@push('styles')
<style>
  .product-details-wrap {
    max-width: 1480px;
    margin: 0 auto;
    padding-bottom: 4rem;
  }

  /* Breadcrumb & Action bar */
  .prod-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    padding: 1.25rem 0;
  }

  .prod-breadcrumb {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.88rem;
    color: #64748b;
  }
  .prod-breadcrumb a {
    color: #475569;
    transition: color 0.2s ease;
  }
  .prod-breadcrumb a:hover {
    color: #E2002B;
  }
  .prod-breadcrumb span {
    color: #0f172a;
    font-weight: 600;
  }

  /* Main Box */
  .prod-main-box {
    border: 1px solid #E7E7E7;
    border-radius: 16px;
    background: #ffffff;
    padding: 1.75rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
  }

  .prod-gallery-main {
    background-color: #f8fafc;
    border-radius: 14px;
    height: 380px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
    overflow: hidden;
    position: relative;
  }
  .prod-gallery-main img {
    max-width: 90%;
    max-height: 90%;
    object-fit: contain;
    transition: transform 0.3s ease;
  }
  .prod-gallery-main:hover img {
    transform: scale(1.05);
  }

  .prod-thumbnail {
    width: 68px;
    height: 68px;
    border-radius: 10px;
    background: #f8fafc;
    border: 2px solid transparent;
    cursor: pointer;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 4px;
    transition: all 0.2s ease;
  }
  .prod-thumbnail:hover, .prod-thumbnail.active {
    border-color: #E2002B;
    background: #ffffff;
    box-shadow: 0 2px 8px rgba(226, 0, 43, 0.15);
  }
  .prod-thumbnail img {
    max-width: 90%;
    max-height: 90%;
    object-fit: contain;
  }

  /* Quantity & CTA buttons */
  .qty-btn {
    width: 36px;
    height: 36px;
    background: #E2002B;
    color: #ffffff;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.2s ease;
  }
  .qty-btn:hover {
    background: #c50024;
  }

  .btn-purchase-now {
    flex: 1;
    min-width: 160px;
    padding: 0.85rem 1.5rem;
    border-radius: 10px;
    background: #E2002B;
    color: #ffffff;
    font-weight: 700;
    font-size: 0.95rem;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    text-align: center;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }
  .btn-purchase-now:hover {
    background: #c50024;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(226, 0, 43, 0.25);
  }

  .btn-add-cart {
    flex: 1;
    min-width: 160px;
    padding: 0.85rem 1.5rem;
    border-radius: 10px;
    border: 2px solid #E2002B;
    color: #E2002B;
    background: #ffffff;
    font-weight: 700;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.2s ease;
    text-align: center;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }
  .btn-add-cart:hover {
    background: #fff1f2;
    transform: translateY(-2px);
  }

  /* Tabs */
  .prod-tab-btn {
    padding: 0.6rem 1.25rem;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.2s ease;
  }
  .prod-tab-btn:hover {
    border-color: #E2002B;
    color: #E2002B;
  }
  .prod-tab-btn.active {
    background: #E2002B;
    border-color: #E2002B;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(226, 0, 43, 0.2);
  }

  /* Social Share */
  .social-share-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #f1f5f9;
    color: #334155;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    font-size: 0.9rem;
  }
  .social-share-btn:hover {
    background: #E2002B;
    color: #ffffff;
    transform: scale(1.1);
  }

  /* Related Product Card */
  .rel-card {
    border-radius: 16px;
    overflow: hidden;
    background: #ffffff;
    border: 1px solid #E7E7E7;
    transition: all 0.25s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
  }
  .rel-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 24px rgba(0,0,0,0.06);
  }
  .rel-card__img-box {
    background: #f8fafc;
    height: 180px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    cursor: pointer;
  }
  .rel-card__img {
    max-width: 80%;
    max-height: 80%;
    object-fit: contain;
    transition: transform 0.25s ease;
  }
  .rel-card:hover .rel-card__img {
    transform: scale(1.06);
  }

  /* Toast notification */
  .skillbox-toast {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    background: #1e293b;
    color: #ffffff;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    z-index: 9999;
    transform: translateY(100px);
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .skillbox-toast.show {
    transform: translateY(0);
    opacity: 1;
  }

  /* Modal Backdrop & Dialog */
  .modal-overlay {
    position: fixed;
    inset: 0;
    background-color: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10000;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.25s ease;
    padding: 1rem;
  }
  .modal-overlay.active {
    opacity: 1;
    pointer-events: auto;
  }
  .modal-container {
    background: #ffffff;
    border-radius: 20px;
    width: 100%;
    max-width: 680px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    transform: scale(0.95);
    transition: transform 0.25s ease;
  }
  .modal-overlay.active .modal-container {
    transform: scale(1);
  }

  .form-label-custom {
    display: block;
    font-size: 0.8rem;
    font-weight: 700;
    color: #334155;
    margin-bottom: 0.35rem;
    text-transform: uppercase;
    letter-spacing: 0.025em;
  }
  .form-input-custom {
    width: 100%;
    padding: 0.7rem 0.9rem;
    border-radius: 10px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    font-size: 0.9rem;
    color: #0f172a;
    transition: border-color 0.2s, box-shadow 0.2s;
    outline: none;
  }
  .form-input-custom:focus {
    border-color: #E2002B;
    box-shadow: 0 0 0 3px rgba(226, 0, 43, 0.12);
  }
  .form-input-custom.error {
    border-color: #ef4444;
    background: #fef2f2;
  }

  .addr-type-btn {
    flex: 1;
    padding: 0.55rem;
    text-align: center;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.82rem;
    font-weight: 600;
    color: #64748b;
    cursor: pointer;
    background: #f8fafc;
    transition: all 0.2s;
  }
  .addr-type-btn.active {
    background: #fff1f2;
    border-color: #E2002B;
    color: #E2002B;
    font-weight: 700;
  }

  /* Cart Drawer Item */
  .cart-item-row {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    padding: 0.75rem 0;
  }
  .cart-item-img {
    width: 58px;
    height: 58px;
    border-radius: 10px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 4px;
    flex-shrink: 0;
  }
  .cart-item-img img {
    max-width: 90%;
    max-height: 90%;
    object-fit: contain;
  }
</style>
@endpush

@section('contents')
<div class="bg-slate-50/60 min-h-screen pt-4 pb-16">
  <div class="product-details-wrap px-4 sm:px-6 lg:px-8">

    <!-- ── Top Breadcrumb & Action Icons ──────────────────────────── -->
    <div class="prod-topbar">
      <div class="prod-breadcrumb">
        <a href="{{ route('home') }}">Home</a>
        <i class="fa-solid fa-chevron-right text-xs text-slate-300"></i>
        <a href="{{ route('shop') }}">Shop</a>
        <i class="fa-solid fa-chevron-right text-xs text-slate-300"></i>
        <span id="breadcrumbTitle">Loading product...</span>
      </div>

      <div class="flex items-center gap-4">
        <a href="{{ route('shop') }}" class="text-sm font-semibold text-slate-600 hover:text-red-600 transition-colors flex items-center gap-1.5">
          <i class="fa-solid fa-arrow-left"></i> Back to Shop
        </a>
        
        <!-- Cart Trigger Button -->
        <button type="button" id="openCartBtn" class="relative inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-red-600 shadow-sm transition-all cursor-pointer" title="View Shopping Cart">
          <i class="fa-solid fa-cart-shopping"></i>
          <span id="headerCartBadge" class="absolute -top-1.5 -right-1.5 bg-red-600 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center">0</span>
        </button>
      </div>
    </div>

    <!-- ── Loading Placeholder ────────────────────────────────────── -->
    <div id="productLoading" class="prod-main-box animate-pulse">
      <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
        <div class="md:col-span-5 flex flex-col gap-4">
          <div class="bg-slate-200 h-96 rounded-xl w-full"></div>
          <div class="flex gap-3">
            <div class="bg-slate-200 h-16 w-16 rounded-lg"></div>
            <div class="bg-slate-200 h-16 w-16 rounded-lg"></div>
            <div class="bg-slate-200 h-16 w-16 rounded-lg"></div>
          </div>
        </div>
        <div class="md:col-span-7 flex flex-col gap-4">
          <div class="bg-slate-200 h-8 rounded w-3/4"></div>
          <div class="bg-slate-200 h-6 rounded w-1/4"></div>
          <div class="bg-slate-200 h-16 rounded w-full"></div>
          <div class="bg-slate-200 h-10 rounded w-1/3"></div>
          <div class="bg-slate-200 h-12 rounded w-1/2 mt-4"></div>
        </div>
      </div>
    </div>

    <!-- ── Main Product Display ───────────────────────────────────── -->
    <div id="productContent" class="hidden">
      
      <!-- Top Card Section -->
      <div class="prod-main-box">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
          
          <!-- Left: Gallery -->
          <div class="md:col-span-5">
            <div class="prod-gallery-main mb-4">
              <img id="mainProductImg" src="" alt="Product Image" />
            </div>
            <div id="thumbnailsStrip" class="flex gap-3 overflow-x-auto pb-1">
              <!-- Injected via JS -->
            </div>
          </div>

          <!-- Right: Details -->
          <div class="md:col-span-7 flex flex-col">
            
            <!-- Title & Price -->
            <div class="flex flex-wrap items-start justify-between gap-3 mb-2">
              <h1 id="productTitle" class="text-2xl sm:text-3xl font-bold text-slate-900 leading-snug">
                <!-- Title -->
              </h1>
              <div class="text-right">
                <div class="flex items-baseline gap-2 justify-end">
                  <span id="productOldPrice" class="text-sm text-slate-400 line-through hidden"></span>
                  <span id="productPrice" class="text-2xl sm:text-3xl font-extrabold text-slate-900">₹ 0</span>
                </div>
                <div class="text-[11px] text-slate-400 mt-0.5" id="unitPriceNote"></div>
              </div>
            </div>

            <!-- Short description -->
            <p id="productShortDesc" class="text-sm text-slate-600 leading-relaxed mb-4">
              <!-- Short desc -->
            </p>

            <!-- Stock & Shipping Note -->
            <div class="flex flex-wrap items-center gap-4 mb-4">
              <span id="stockStatusBadge" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <i class="fa-solid fa-circle-check"></i> <span id="stockUnitsText">In Stock</span>
              </span>
              <span class="text-xs text-slate-500">
                <i class="fa-solid fa-truck-fast mr-1 text-slate-400"></i> Free express delivery available
              </span>
            </div>

            <!-- Ratings -->
            <div class="flex items-center gap-2 mb-6 pb-6 border-b border-slate-100">
              <div class="flex text-amber-400 text-sm gap-0.5">
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
              </div>
              <span class="text-xs font-semibold text-slate-700">(4.9 out of 5)</span>
              <span class="text-xs text-slate-400">• Verified Activity Kit</span>
            </div>

            <!-- Quantity Selector -->
            <div class="flex items-center gap-4 mb-6">
              <span class="text-sm font-bold text-slate-700">Quantity:</span>
              <div class="inline-flex items-center border border-slate-200 rounded-lg overflow-hidden bg-white">
                <button id="qtyDecBtn" type="button" class="qty-btn" title="Decrease quantity"><i class="fa-solid fa-minus text-xs"></i></button>
                <span id="qtyVal" class="w-12 text-center text-sm font-bold text-slate-800">1</span>
                <button id="qtyIncBtn" type="button" class="qty-btn" title="Increase quantity"><i class="fa-solid fa-plus text-xs"></i></button>
              </div>
              <span class="text-xs text-slate-400">(Price updates dynamically)</span>
            </div>

            <!-- CTA Buttons -->
            <div class="flex flex-wrap gap-4 mb-6">
              <button id="purchaseNowBtn" type="button" class="btn-purchase-now">
                <i class="fa-solid fa-bolt mr-2"></i> Purchase Now
              </button>
              <button id="addToCartBtn" type="button" class="btn-add-cart">
                <i class="fa-solid fa-cart-plus mr-2"></i> Add To Cart
              </button>
            </div>

            <!-- Share Product -->
            <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
              <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Share this kit:</span>
              <div class="flex items-center gap-2">
                <a id="shareWa" href="#" target="_blank" class="social-share-btn" title="Share on WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                <a id="shareFb" href="#" target="_blank" class="social-share-btn" title="Share on Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                <a id="shareTw" href="#" target="_blank" class="social-share-btn" title="Share on X / Twitter"><i class="fa-brands fa-x-twitter"></i></a>
                <a id="shareIg" href="https://instagram.com" target="_blank" class="social-share-btn" title="Share on Instagram"><i class="fa-brands fa-instagram"></i></a>
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- Tabs Box: Description, Contents, Reviews, Comments -->
      <div class="prod-main-box mb-12">
        <div class="flex flex-wrap gap-2 mb-6 pb-4 border-b border-slate-100" id="tabsHeader">
          <button class="prod-tab-btn active" data-tab="tab-desc">Product Description</button>
          <button class="prod-tab-btn" data-tab="tab-contents">Contents In The Box</button>
          <button class="prod-tab-btn" data-tab="tab-reviews">Reviews (1)</button>
          <button class="prod-tab-btn" data-tab="tab-comments">Comments & Questions</button>
        </div>

        <!-- Tab 1: Description -->
        <div id="tab-desc" class="tab-pane text-slate-600 text-sm leading-relaxed space-y-4">
          <div id="productFullDesc">
            <!-- Full description HTML -->
          </div>
        </div>

        <!-- Tab 2: Contents -->
        <div id="tab-contents" class="tab-pane hidden text-slate-600 text-sm leading-relaxed">
          <h4 class="font-bold text-slate-800 mb-3 text-base">What's Inside the SkillBox Kit:</h4>
          <div id="contentsList" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Injected via JS -->
          </div>
        </div>

        <!-- Tab 3: Reviews -->
        <div id="tab-reviews" class="tab-pane hidden">
          <div class="bg-slate-50 p-6 rounded-xl border border-slate-200/80 mb-6">
            <div class="flex items-center justify-between mb-3">
              <div>
                <h5 class="font-bold text-slate-900 text-base">Excellent hands-on kit!</h5>
                <div class="flex text-amber-400 text-xs gap-0.5 mt-1">
                  <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                </div>
              </div>
              <span class="text-xs text-slate-400">Verified Buyer</span>
            </div>
            <p class="text-sm text-slate-600 leading-relaxed">
              "The activities are well-curated and very easy to follow. My students enjoyed every project, and the materials are safe and durable."
            </p>
          </div>
        </div>

        <!-- Tab 4: Comments -->
        <div id="tab-comments" class="tab-pane hidden text-slate-500 text-sm py-4">
          <p class="text-center">Have a question about this kit? Contact our learning support or leave a comment below.</p>
        </div>

      </div>

      <!-- ── Similar Products ────────────────────────────────────── -->
      <div class="mb-12">
        <h3 class="text-xl font-bold text-slate-900 mb-6">Similar Learning Resources & Kits</h3>
        <div id="similarProductsGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <!-- Injected via JS -->
        </div>
      </div>

    </div>

  </div>
</div>

<!-- ── SkillBox Cart Drawer / Modal ──────────────────────────────── -->
<div id="skillboxCartDrawer" class="modal-overlay">
  <div class="modal-container p-0 max-w-lg w-full flex flex-col max-h-[92vh] overflow-hidden">
    <!-- Cart Header -->
    <div class="flex items-center justify-between p-5 border-b border-slate-100 bg-slate-50/50">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center text-sm font-bold">
          <i class="fa-solid fa-cart-shopping"></i>
        </div>
        <div>
          <h3 class="text-base font-bold text-slate-900">Your Shopping Cart</h3>
          <p class="text-xs text-slate-500" id="cartItemCountSubtitle">0 items</p>
        </div>
      </div>
      <button type="button" id="closeCartDrawerBtn" class="text-slate-400 hover:text-slate-700 text-2xl leading-none transition-colors p-1">
        &times;
      </button>
    </div>

    <!-- Cart Items List Container -->
    <div id="cartItemsList" class="p-5 flex-grow overflow-y-auto space-y-3">
      <!-- Items dynamically injected -->
    </div>

    <!-- Empty Cart State -->
    <div id="cartEmptyState" class="p-10 text-center flex-grow flex flex-col items-center justify-center hidden">
      <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mb-3">
        <i class="fa-solid fa-basket-shopping"></i>
      </div>
      <h4 class="text-base font-bold text-slate-800 mb-1">Your cart is empty</h4>
      <p class="text-xs text-slate-500 mb-4 max-w-xs">Explore hands-on kits and activity boxes to add to your cart.</p>
      <a href="{{ route('shop') }}" class="btn-purchase-now text-xs py-2 px-4 rounded-lg inline-block">
        Explore Kits
      </a>
    </div>

    <!-- Cart Footer & Checkout -->
    <div id="cartFooterSection" class="p-5 border-t border-slate-100 bg-slate-50/80 space-y-3">
      <div class="space-y-1.5 text-xs text-slate-600">
        <div class="flex justify-between">
          <span>Subtotal</span>
          <strong id="cartSubtotalText" class="text-slate-900 font-bold">₹0</strong>
        </div>
        <div class="flex justify-between">
          <span>Delivery Charges</span>
          <span class="text-emerald-600 font-bold">FREE Express Delivery</span>
        </div>
        <div class="flex justify-between text-sm pt-2 border-t border-slate-200/80">
          <span class="font-bold text-slate-900">Total Amount</span>
          <strong id="cartGrandTotalText" class="font-extrabold text-slate-900 text-base">₹0</strong>
        </div>
      </div>

      <div class="pt-2 flex flex-col gap-2">
        <button type="button" id="cartProceedCheckoutBtn" class="btn-purchase-now w-full py-3 text-sm font-bold">
          <i class="fa-solid fa-lock mr-2"></i> Proceed to Checkout
        </button>
        <button type="button" id="clearCartBtn" class="text-[11px] text-slate-400 hover:text-red-500 text-center transition-colors">
          Clear all items
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ── Delivery Address & Checkout Modal ──────────────────────────── -->
<div id="deliveryModal" class="modal-overlay">
  <div class="modal-container p-6 sm:p-8">
    
    <!-- Modal Header -->
    <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100">
      <div>
        <h3 class="text-lg sm:text-xl font-extrabold text-slate-900">Delivery Address & Payment</h3>
        <p class="text-xs text-slate-500 mt-0.5">Please provide your shipping information to proceed with the order.</p>
      </div>
      <button type="button" id="closeDeliveryModalBtn" class="text-slate-400 hover:text-slate-700 text-2xl leading-none transition-colors p-1">
        &times;
      </button>
    </div>

    <!-- Order Summary Card -->
    <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-4 mb-6">
      <div class="flex items-center gap-4">
        <div class="w-16 h-16 rounded-lg bg-white border border-slate-200 flex items-center justify-center p-1.5 flex-shrink-0">
          <img id="modalSummaryImg" src="" alt="Kit" class="max-w-full max-h-full object-contain" />
        </div>
        <div class="flex-grow min-w-0">
          <h4 id="modalSummaryTitle" class="text-sm font-bold text-slate-900 truncate">Product Title</h4>
          <div class="flex items-center gap-3 text-xs text-slate-500 mt-1">
            <span>Qty: <strong id="modalSummaryQty" class="text-slate-800">1</strong></span>
            <span>•</span>
            <span>Unit: <strong id="modalSummaryUnitPrice" class="text-slate-800">₹0</strong></span>
          </div>
        </div>
        <div class="text-right flex-shrink-0">
          <span class="text-xs text-slate-400 block">Total Payable</span>
          <span id="modalSummaryTotal" class="text-lg font-extrabold text-red-600">₹0</span>
        </div>
      </div>
    </div>

    <!-- Delivery Form -->
    <form id="deliveryAddressForm" class="space-y-4">
      
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Full Name -->
        <div>
          <label class="form-label-custom">Full Name <span class="text-red-500">*</span></label>
          <input type="text" id="addrFullName" class="form-input-custom" placeholder="e.g. Rahul Sharma" required />
          <span id="errFullName" class="text-[11px] text-red-500 hidden mt-1">Please enter your full name.</span>
        </div>

        <!-- Phone -->
        <div>
          <label class="form-label-custom">Mobile Number <span class="text-red-500">*</span></label>
          <input type="tel" id="addrPhone" class="form-input-custom" placeholder="10-digit mobile number" maxlength="10" required />
          <span id="errPhone" class="text-[11px] text-red-500 hidden mt-1">Enter a valid 10-digit phone number.</span>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Pincode -->
        <div>
          <label class="form-label-custom">
            Pincode <span class="text-red-500">*</span>
            <span id="pincodeLoader" class="hidden text-[10px] text-red-600 font-normal lowercase ml-1">
              <i class="fa-solid fa-spinner fa-spin"></i> fetching...
            </span>
          </label>
          <input type="text" id="addrPincode" class="form-input-custom" placeholder="6-digit pincode" maxlength="6" required />
          <span id="errPincode" class="text-[11px] text-red-500 hidden mt-1">Enter valid 6-digit pincode.</span>
        </div>

        <!-- City / District -->
        <div>
          <label class="form-label-custom">City / District <span class="text-red-500">*</span></label>
          <input type="text" id="addrCity" class="form-input-custom" placeholder="City / District" required />
          <span id="errCity" class="text-[11px] text-red-500 hidden mt-1">City is required.</span>
        </div>

        <!-- State -->
        <div>
          <label class="form-label-custom">State <span class="text-red-500">*</span></label>
          <input type="text" id="addrState" class="form-input-custom" placeholder="State" required />
          <span id="errState" class="text-[11px] text-red-500 hidden mt-1">State is required.</span>
        </div>
      </div>

      <!-- Country (Hidden or read-only default) -->
      <input type="hidden" id="addrCountry" value="India" />

      <!-- Full Street Address -->
      <div>
        <label class="form-label-custom">Street Address & Landmark <span class="text-red-500">*</span></label>
        <textarea id="addrStreet" rows="2" class="form-input-custom" placeholder="House No., Building, Street Area, Landmark" required></textarea>
        <span id="errStreet" class="text-[11px] text-red-500 hidden mt-1">Please enter your delivery street address.</span>
      </div>

      <!-- Address Type Selector -->
      <div>
        <label class="form-label-custom">Address Type</label>
        <div class="flex gap-2.5">
          <button type="button" class="addr-type-btn active" data-type="Home"><i class="fa-solid fa-house mr-1 text-xs"></i> Home</button>
          <button type="button" class="addr-type-btn" data-type="Work / Office"><i class="fa-solid fa-briefcase mr-1 text-xs"></i> Work / Office</button>
          <button type="button" class="addr-type-btn" data-type="Other"><i class="fa-solid fa-location-dot mr-1 text-xs"></i> Other</button>
        </div>
      </div>

      <!-- Payment CTA -->
      <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2 text-xs text-slate-500">
          <i class="fa-solid fa-shield-halved text-emerald-600 text-sm"></i>
          <span>100% Safe & Secure Checkout with Razorpay</span>
        </div>

        <button type="submit" id="btnProceedRazorpay" class="btn-purchase-now w-full sm:w-auto px-8 py-3 text-base">
          <i class="fa-solid fa-lock mr-2"></i> <span id="btnPayAmountText">Proceed to Pay</span>
        </button>
      </div>

    </form>

  </div>
</div>

<!-- ── Order Success Modal ────────────────────────────────────────── -->
<div id="orderSuccessModal" class="modal-overlay">
  <div class="modal-container p-6 sm:p-8 max-w-lg text-center">
    <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-3xl mx-auto mb-4">
      <i class="fa-solid fa-circle-check"></i>
    </div>
    <h3 class="text-2xl font-black text-slate-900 mb-2">Order Confirmed!</h3>
    <p class="text-sm text-slate-600 mb-4">Thank you for your purchase. Your SkillBox kit is being prepared for dispatch.</p>

    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-left text-xs text-slate-600 space-y-2 mb-6">
      <div class="flex justify-between border-b border-slate-200/60 pb-1.5">
        <span class="text-slate-400">Payment ID:</span>
        <span id="successPaymentId" class="font-bold text-slate-800 font-mono">-</span>
      </div>
      <div class="flex justify-between border-b border-slate-200/60 pb-1.5">
        <span class="text-slate-400">Kit Ordered:</span>
        <span id="successItemTitle" class="font-bold text-slate-800">-</span>
      </div>
      <div class="flex justify-between border-b border-slate-200/60 pb-1.5">
        <span class="text-slate-400">Total Paid:</span>
        <span id="successTotalPaid" class="font-bold text-emerald-600">-</span>
      </div>
      <div class="flex justify-between">
        <span class="text-slate-400">Shipping To:</span>
        <span id="successShippingAddress" class="font-bold text-slate-800 text-right">-</span>
      </div>
    </div>

    <div class="flex flex-col sm:flex-row gap-3 justify-center">
      <a href="{{ route('shop') }}" class="btn-purchase-now">
        <i class="fa-solid fa-store mr-2"></i> Continue Shopping
      </a>
      <button type="button" id="closeSuccessModalBtn" class="btn-add-cart">
        Close
      </button>
    </div>
  </div>
</div>

<!-- ── Toast Alert ────────────────────────────────────────────────── -->
<div id="prodToast" class="skillbox-toast">
  <i id="toastIcon" class="fa-solid fa-circle-check text-emerald-400 text-xl"></i>
  <div>
    <h4 id="toastTitle" class="text-sm font-bold">Item Added</h4>
    <p id="toastMessage" class="text-xs text-slate-300">Action completed successfully.</p>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
  (function() {
    'use strict';

    const productLookup = @json($id);
    let productId = productLookup;
    let productData = null;
    let quantity = 1;
    let selectedAddressType = 'Home';
    let checkoutMode = 'direct'; // 'direct' or 'cart'

    // Cart array from localStorage
    let skillboxCart = JSON.parse(localStorage.getItem('skillbox_cart') || '[]');

    // Elements
    const productLoading = document.getElementById('productLoading');
    const productContent = document.getElementById('productContent');
    const breadcrumbTitle = document.getElementById('breadcrumbTitle');
    const productTitle = document.getElementById('productTitle');
    const productPrice = document.getElementById('productPrice');
    const productOldPrice = document.getElementById('productOldPrice');
    const unitPriceNote = document.getElementById('unitPriceNote');
    const productShortDesc = document.getElementById('productShortDesc');
    const productFullDesc = document.getElementById('productFullDesc');
    const mainProductImg = document.getElementById('mainProductImg');
    const thumbnailsStrip = document.getElementById('thumbnailsStrip');
    const stockUnitsText = document.getElementById('stockUnitsText');
    const qtyVal = document.getElementById('qtyVal');
    const qtyIncBtn = document.getElementById('qtyIncBtn');
    const qtyDecBtn = document.getElementById('qtyDecBtn');
    const addToCartBtn = document.getElementById('addToCartBtn');
    const purchaseNowBtn = document.getElementById('purchaseNowBtn');
    const shareWa = document.getElementById('shareWa');
    const shareFb = document.getElementById('shareFb');
    const shareTw = document.getElementById('shareTw');
    const contentsList = document.getElementById('contentsList');
    const similarProductsGrid = document.getElementById('similarProductsGrid');
    
    // Toast
    const toast = document.getElementById('prodToast');
    const toastTitle = document.getElementById('toastTitle');
    const toastMessage = document.getElementById('toastMessage');

    // Cart Drawer Elements
    const openCartBtn = document.getElementById('openCartBtn');
    const skillboxCartDrawer = document.getElementById('skillboxCartDrawer');
    const closeCartDrawerBtn = document.getElementById('closeCartDrawerBtn');
    const cartItemsList = document.getElementById('cartItemsList');
    const cartEmptyState = document.getElementById('cartEmptyState');
    const cartFooterSection = document.getElementById('cartFooterSection');
    const cartSubtotalText = document.getElementById('cartSubtotalText');
    const cartGrandTotalText = document.getElementById('cartGrandTotalText');
    const cartItemCountSubtitle = document.getElementById('cartItemCountSubtitle');
    const cartProceedCheckoutBtn = document.getElementById('cartProceedCheckoutBtn');
    const clearCartBtn = document.getElementById('clearCartBtn');

    // Delivery Modal Elements
    const deliveryModal = document.getElementById('deliveryModal');
    const closeDeliveryModalBtn = document.getElementById('closeDeliveryModalBtn');
    const deliveryAddressForm = document.getElementById('deliveryAddressForm');
    const modalSummaryImg = document.getElementById('modalSummaryImg');
    const modalSummaryTitle = document.getElementById('modalSummaryTitle');
    const modalSummaryQty = document.getElementById('modalSummaryQty');
    const modalSummaryUnitPrice = document.getElementById('modalSummaryUnitPrice');
    const modalSummaryTotal = document.getElementById('modalSummaryTotal');
    const btnPayAmountText = document.getElementById('btnPayAmountText');

    // Form inputs
    const addrFullName = document.getElementById('addrFullName');
    const addrPhone = document.getElementById('addrPhone');
    const addrPincode = document.getElementById('addrPincode');
    const addrCity = document.getElementById('addrCity');
    const addrState = document.getElementById('addrState');
    const addrCountry = document.getElementById('addrCountry');
    const addrStreet = document.getElementById('addrStreet');
    const pincodeLoader = document.getElementById('pincodeLoader');

    // Success Modal Elements
    const orderSuccessModal = document.getElementById('orderSuccessModal');
    const successPaymentId = document.getElementById('successPaymentId');
    const successItemTitle = document.getElementById('successItemTitle');
    const successTotalPaid = document.getElementById('successTotalPaid');
    const successShippingAddress = document.getElementById('successShippingAddress');
    const closeSuccessModalBtn = document.getElementById('closeSuccessModalBtn');

    function showToast(title, msg) {
      if (!toast) return;
      toastTitle.textContent = title;
      toastMessage.textContent = msg;
      toast.classList.add('show');
      setTimeout(() => toast.classList.remove('show'), 3500);
    }

    // Cart Management
    function saveCart() {
      localStorage.setItem('skillbox_cart', JSON.stringify(skillboxCart));
      updateAllCartBadges();
      renderCartDrawer();
    }

    function updateAllCartBadges() {
      const totalCount = skillboxCart.reduce((sum, item) => sum + (Number(item.quantity) || 1), 0);
      const badges = document.querySelectorAll('#headerCartBadge, #cartCountBadge, .mini-cart-count');
      badges.forEach(b => {
        b.textContent = totalCount;
      });
    }

    function renderCartDrawer() {
      const totalCount = skillboxCart.reduce((sum, item) => sum + (Number(item.quantity) || 1), 0);
      cartItemCountSubtitle.textContent = `${totalCount} ${totalCount === 1 ? 'item' : 'items'}`;

      if (skillboxCart.length === 0) {
        cartItemsList.classList.add('hidden');
        cartFooterSection.classList.add('hidden');
        cartEmptyState.classList.remove('hidden');
        return;
      }

      cartEmptyState.classList.add('hidden');
      cartItemsList.classList.remove('hidden');
      cartFooterSection.classList.remove('hidden');

      let subtotal = 0;
      cartItemsList.innerHTML = skillboxCart.map((item, idx) => {
        const itemPrice = Number(item.price_discounted || item.price || 0);
        const itemTotal = itemPrice * (item.quantity || 1);
        subtotal += itemTotal;

        return `
          <div class="cart-item-row">
            <div class="cart-item-img">
              <img src="${item.image || 'https://pedaskills.com/static/media/skillBoxImg.54ef565f4b0836600035.png'}" alt="${item.title}" onerror="this.src='https://pedaskills.com/static/media/skillBoxImg.54ef565f4b0836600035.png';" />
            </div>
            <div class="flex-grow min-w-0">
              <a href="/ProductDetails/${item.id}" class="text-xs font-bold text-slate-800 line-clamp-1 hover:text-red-600 transition-colors">${item.title}</a>
              <div class="text-xs font-semibold text-slate-900 mt-0.5">₹ ${itemPrice.toLocaleString('en-IN')}</div>
              <div class="flex items-center gap-2 mt-1.5">
                <div class="inline-flex items-center border border-slate-200 rounded-md overflow-hidden bg-white">
                  <button type="button" class="w-6 h-6 bg-slate-100 text-slate-600 flex items-center justify-center text-[10px] hover:bg-red-50 hover:text-red-600" onclick="window.updateCartItemQty(${idx}, -1)">-</button>
                  <span class="w-7 text-center text-xs font-bold text-slate-800">${item.quantity || 1}</span>
                  <button type="button" class="w-6 h-6 bg-slate-100 text-slate-600 flex items-center justify-center text-[10px] hover:bg-red-50 hover:text-red-600" onclick="window.updateCartItemQty(${idx}, 1)">+</button>
                </div>
                <span class="text-[11px] text-slate-400 font-medium ml-auto">₹ ${itemTotal.toLocaleString('en-IN')}</span>
              </div>
            </div>
            <button type="button" class="text-slate-400 hover:text-red-600 p-1 transition-colors ml-1" onclick="window.removeCartItem(${idx})" title="Remove item">
              <i class="fa-solid fa-trash-can text-xs"></i>
            </button>
          </div>
        `;
      }).join('');

      cartSubtotalText.textContent = `₹ ${subtotal.toLocaleString('en-IN')}`;
      cartGrandTotalText.textContent = `₹ ${subtotal.toLocaleString('en-IN')}`;
    }

    window.updateCartItemQty = function(index, delta) {
      if (!skillboxCart[index]) return;
      skillboxCart[index].quantity = (skillboxCart[index].quantity || 1) + delta;
      if (skillboxCart[index].quantity <= 0) {
        skillboxCart.splice(index, 1);
      }
      saveCart();
    };

    window.removeCartItem = function(index) {
      if (!skillboxCart[index]) return;
      const title = skillboxCart[index].title;
      skillboxCart.splice(index, 1);
      saveCart();
      showToast('Item Removed', `"${title}" removed from cart.`);
    };

    function openSkillboxCart() {
      renderCartDrawer();
      skillboxCartDrawer.classList.add('active');
    }

    function closeSkillboxCart() {
      skillboxCartDrawer.classList.remove('active');
    }

    if (openCartBtn) {
      openCartBtn.addEventListener('click', (e) => {
        e.preventDefault();
        openSkillboxCart();
      });
    }

    if (closeCartDrawerBtn) {
      closeCartDrawerBtn.addEventListener('click', closeSkillboxCart);
    }

    skillboxCartDrawer.addEventListener('click', (e) => {
      if (e.target === skillboxCartDrawer) {
        closeSkillboxCart();
      }
    });

    if (clearCartBtn) {
      clearCartBtn.addEventListener('click', () => {
        if (confirm('Are you sure you want to clear your cart?')) {
          skillboxCart = [];
          saveCart();
        }
      });
    }

    // Hook into navbar header cart icons
    document.querySelectorAll('.mini-cart-icon a.cart-count').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        openSkillboxCart();
      });
    });

    // Dynamic Price Calculation for product page
    function updatePriceDisplay() {
      if (!productData) return;
      const unitPrice = Number(productData.price || 0);
      const unitDiscounted = Number(productData.price_discounted || unitPrice);

      const totalPrice = unitDiscounted * quantity;
      const totalOldPrice = unitPrice * quantity;

      productPrice.textContent = `₹ ${totalPrice.toLocaleString('en-IN')}`;
      
      if (unitDiscounted < unitPrice) {
        productOldPrice.textContent = `₹ ${totalOldPrice.toLocaleString('en-IN')}`;
        productOldPrice.classList.remove('hidden');
        unitPriceNote.textContent = `₹ ${unitDiscounted.toLocaleString('en-IN')} / unit`;
      } else {
        productOldPrice.classList.add('hidden');
        unitPriceNote.textContent = '';
      }
    }

    // Load Product Data
    async function loadProductDetails() {
      try {
        const res = await fetch(`/api/product-details/${encodeURIComponent(productLookup)}`);
        const json = await res.json();
        
        if (json && json.data) {
          productData = json.data;
          productId = productData.id;
          renderProduct(productData);
          loadSimilarProducts(productData.category_id || (productData.category ? productData.category.id : null));
        } else {
          document.getElementById('breadcrumbTitle').textContent = 'Product not found';
          productLoading.innerHTML = '<div class="text-center py-12 text-slate-500 font-bold">Product not found. <a href="/shop" class="text-red-600 underline ml-2">Back to Shop</a></div>';
        }
      } catch (err) {
        console.error('Error fetching product details:', err);
        productLoading.innerHTML = '<div class="text-center py-12 text-slate-500 font-bold">Error loading product details. <a href="/shop" class="text-red-600 underline ml-2">Back to Shop</a></div>';
      }
    }

    function renderProduct(p) {
      productLoading.classList.add('hidden');
      productContent.classList.remove('hidden');

      const title = p.title || p.course_name || 'Activity Kit';
      breadcrumbTitle.textContent = title;
      productTitle.textContent = title;
      document.title = `${title} — {{ config('app.name', 'Skillvation') }}`;

      // Update Prices dynamically
      updatePriceDisplay();

      // Descriptions
      productShortDesc.textContent = p.short_description || 'Engaging hands-on activity kit for preschool and school learners.';
      productFullDesc.innerHTML = p.description || '<p>Comprehensive hands-on learning box packed with curated materials and step-by-step guides.</p>';

      // Stock
      if (p.stock) {
        stockUnitsText.textContent = `${p.stock} Units Left!`;
      } else {
        stockUnitsText.textContent = 'In Stock';
      }

      // Images
      let images = [];
      if (p.images && Array.isArray(p.images) && p.images.length > 0) {
        images = p.images;
      } else if (p.image) {
        images = [p.image];
      } else if (p.image_small) {
        images = [p.image_small];
      } else {
        images = ['https://pedaskills.com/static/media/skillBoxImg.54ef565f4b0836600035.png'];
      }

      mainProductImg.src = images[0];

      thumbnailsStrip.innerHTML = images.map((imgUrl, idx) => `
        <div class="prod-thumbnail ${idx === 0 ? 'active' : ''}" onclick="window.switchMainImage('${imgUrl}', this)">
          <img src="${imgUrl}" alt="Thumb ${idx + 1}" />
        </div>
      `).join('');

      // Parse contents
      const defaultItems = [
        'Curated hands-on project materials',
        'Step-by-step pictorial instruction manual',
        'Child-safe tools & learning components',
        'Assessment checklist & achievement badge',
        'NEP 2020 aligned learning guide'
      ];
      contentsList.innerHTML = defaultItems.map(item => `
        <div class="flex items-center gap-2 p-2.5 bg-slate-50 rounded-lg border border-slate-200/60">
          <i class="fa-solid fa-check text-emerald-500 text-xs"></i>
          <span class="text-slate-700 font-medium">${item}</span>
        </div>
      `).join('');

      // Share links
      const shareUrl = encodeURIComponent(window.location.href);
      const shareText = encodeURIComponent(`Check out this SkillBox: ${title}`);
      shareWa.href = `https://wa.me/?text=${shareText}%20${shareUrl}`;
      shareFb.href = `https://www.facebook.com/sharer/sharer.php?u=${shareUrl}`;
      shareTw.href = `https://twitter.com/intent/tweet?text=${shareText}&url=${shareUrl}`;
    }

    // Switch Gallery Image
    window.switchMainImage = function(src, elem) {
      mainProductImg.src = src;
      document.querySelectorAll('.prod-thumbnail').forEach(t => t.classList.remove('active'));
      if (elem) elem.classList.add('active');
    };

    // Load Similar Products
    async function loadSimilarProducts(catId) {
      try {
        const url = catId ? `/api/shop-products?category_id=${catId}` : '/api/shop-products';
        const res = await fetch(url);
        const json = await res.json();
        const items = (json.data || []).filter(item => String(item.id) !== String(productId)).slice(0, 4);

        if (items.length > 0) {
          similarProductsGrid.innerHTML = items.map(item => {
            const id = item.id;
            const detailKey = encodeURIComponent(item.slug || String(id)).replace(/'/g, '%27');
            const title = item.title || item.course_name || 'Activity Kit';
            const price = Number(item.price || 0);
            const discounted = Number(item.price_discounted || price);
            const img = item.image || item.image_small || 'https://pedaskills.com/static/media/skillBoxImg.54ef565f4b0836600035.png';

            return `
              <div class="rel-card">
                <div class="rel-card__img-box" onclick="window.location.href='/ProductDetails/${detailKey}'">
                  <img src="${img}" alt="${title}" class="rel-card__img" loading="lazy" onerror="this.src='https://pedaskills.com/static/media/skillBoxImg.54ef565f4b0836600035.png';" />
                </div>
                <div class="p-4 flex flex-col flex-grow bg-white cursor-pointer" onclick="window.location.href='/ProductDetails/${detailKey}'">
                  <h4 class="text-sm font-semibold text-slate-800 line-clamp-2 mb-2">${title}</h4>
                  <div class="flex items-center justify-between mt-auto pt-2 border-t border-slate-100">
                    <span class="text-base font-bold text-slate-900">₹ ${discounted.toLocaleString('en-IN')}</span>
                    <span class="text-xs font-semibold text-red-600">View Kit →</span>
                  </div>
                </div>
              </div>
            `;
          }).join('');
        } else {
          document.querySelector('#similarProductsGrid').parentElement.classList.add('hidden');
        }
      } catch (err) {
        console.error('Error loading similar products:', err);
      }
    }

    // Quantity Handlers with Dynamic Price Updating
    qtyIncBtn.addEventListener('click', () => {
      quantity++;
      qtyVal.textContent = quantity;
      updatePriceDisplay();
    });

    qtyDecBtn.addEventListener('click', () => {
      if (quantity > 1) {
        quantity--;
        qtyVal.textContent = quantity;
        updatePriceDisplay();
      }
    });

    // Add To Cart Handler
    addToCartBtn.addEventListener('click', () => {
      if (!productData) return;
      const unitPrice = Number(productData.price || 0);
      const unitDiscounted = Number(productData.price_discounted || unitPrice);
      const title = productData.title || productData.course_name || 'Activity Kit';
      const img = (productData.images && productData.images[0]) || productData.image || 'https://pedaskills.com/static/media/skillBoxImg.54ef565f4b0836600035.png';

      const existingIdx = skillboxCart.findIndex(item => String(item.id) === String(productId));
      if (existingIdx > -1) {
        skillboxCart[existingIdx].quantity = (skillboxCart[existingIdx].quantity || 1) + quantity;
      } else {
        skillboxCart.push({
          id: productId,
          title: title,
          price: unitPrice,
          price_discounted: unitDiscounted,
          image: img,
          quantity: quantity
        });
      }

      saveCart();
      showToast('Added to Cart', `Added ${quantity} x "${title}" to your cart.`);
      openSkillboxCart(); // Open cart drawer immediately
    });

    // Address Type Pill Selection
    document.querySelectorAll('.addr-type-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        document.querySelectorAll('.addr-type-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        selectedAddressType = this.getAttribute('data-type');
      });
    });

    // Live Pincode Lookup via postalpincode.in API
    addrPincode.addEventListener('input', async function() {
      const pin = this.value.trim();
      document.getElementById('errPincode').classList.add('hidden');
      this.classList.remove('error');

      if (pin.length === 6 && /^\d{6}$/.test(pin)) {
        pincodeLoader.classList.remove('hidden');
        try {
          const res = await fetch(`https://api.postalpincode.in/pincode/${pin}`);
          const data = await res.json();
          if (data && data[0] && data[0].Status === 'Success' && data[0].PostOffice && data[0].PostOffice.length > 0) {
            const po = data[0].PostOffice[0];
            if (!addrCity.value || addrCity.dataset.autofilled === 'true') {
              addrCity.value = po.District || po.Block || po.Name;
              addrCity.dataset.autofilled = 'true';
            }
            if (!addrState.value || addrState.dataset.autofilled === 'true') {
              addrState.value = po.State;
              addrState.dataset.autofilled = 'true';
            }
            if (po.Country) {
              addrCountry.value = po.Country;
            }
          }
        } catch (e) {
          console.warn('Could not auto-fetch postal code details', e);
        } finally {
          pincodeLoader.classList.add('hidden');
        }
      }
    });

    function openDeliveryModalForDirectBuy() {
      if (!productData) return;
      checkoutMode = 'direct';
      
      const title = productData.title || productData.course_name || 'Activity Kit';
      const unitPrice = Number(productData.price || 0);
      const unitDiscounted = Number(productData.price_discounted || unitPrice);
      const totalPrice = unitDiscounted * quantity;

      modalSummaryTitle.textContent = title;
      modalSummaryQty.textContent = quantity;
      modalSummaryUnitPrice.textContent = `₹ ${unitDiscounted.toLocaleString('en-IN')}`;
      modalSummaryTotal.textContent = `₹ ${totalPrice.toLocaleString('en-IN')}`;
      btnPayAmountText.textContent = `Pay ₹ ${totalPrice.toLocaleString('en-IN')}`;
      modalSummaryImg.src = mainProductImg.src;

      loadSavedAddress();
      deliveryModal.classList.add('active');
    }

    function openDeliveryModalForCart() {
      if (skillboxCart.length === 0) return;
      checkoutMode = 'cart';
      closeSkillboxCart();

      const totalItems = skillboxCart.reduce((sum, item) => sum + (item.quantity || 1), 0);
      let subtotal = 0;
      skillboxCart.forEach(item => {
        subtotal += Number(item.price_discounted || item.price || 0) * (item.quantity || 1);
      });

      modalSummaryTitle.textContent = `Cart Checkout (${totalItems} items)`;
      modalSummaryQty.textContent = totalItems;
      modalSummaryUnitPrice.textContent = '-';
      modalSummaryTotal.textContent = `₹ ${subtotal.toLocaleString('en-IN')}`;
      btnPayAmountText.textContent = `Pay ₹ ${subtotal.toLocaleString('en-IN')}`;
      modalSummaryImg.src = skillboxCart[0]?.image || mainProductImg.src;

      loadSavedAddress();
      deliveryModal.classList.add('active');
    }

    function loadSavedAddress() {
      const savedAddress = JSON.parse(localStorage.getItem('skillbox_delivery_address') || '{}');
      if (savedAddress.fullName && !addrFullName.value) addrFullName.value = savedAddress.fullName;
      if (savedAddress.phone && !addrPhone.value) addrPhone.value = savedAddress.phone;
      if (savedAddress.pincode && !addrPincode.value) addrPincode.value = savedAddress.pincode;
      if (savedAddress.city && !addrCity.value) addrCity.value = savedAddress.city;
      if (savedAddress.state && !addrState.value) addrState.value = savedAddress.state;
      if (savedAddress.street && !addrStreet.value) addrStreet.value = savedAddress.street;
    }

    // Direct Purchase Now button
    purchaseNowBtn.addEventListener('click', openDeliveryModalForDirectBuy);

    // Cart Drawer Proceed to Checkout button
    cartProceedCheckoutBtn.addEventListener('click', openDeliveryModalForCart);

    // Close Delivery Modal
    closeDeliveryModalBtn.addEventListener('click', () => {
      deliveryModal.classList.remove('active');
    });

    // Close on backdrop click
    deliveryModal.addEventListener('click', (e) => {
      if (e.target === deliveryModal) {
        deliveryModal.classList.remove('active');
      }
    });

    // Validate & Proceed to Razorpay Payment
    deliveryAddressForm.addEventListener('submit', function(e) {
      e.preventDefault();

      let isValid = true;
      const fullName = addrFullName.value.trim();
      const phone = addrPhone.value.trim();
      const pincode = addrPincode.value.trim();
      const city = addrCity.value.trim();
      const state = addrState.value.trim();
      const street = addrStreet.value.trim();

      // Reset errors
      document.querySelectorAll('.form-input-custom').forEach(input => input.classList.remove('error'));
      document.querySelectorAll('[id^="err"]').forEach(err => err.classList.add('hidden'));

      if (!fullName) {
        addrFullName.classList.add('error');
        document.getElementById('errFullName').classList.remove('hidden');
        isValid = false;
      }

      if (!phone || !/^\d{10}$/.test(phone)) {
        addrPhone.classList.add('error');
        document.getElementById('errPhone').classList.remove('hidden');
        isValid = false;
      }

      if (!pincode || !/^\d{6}$/.test(pincode)) {
        addrPincode.classList.add('error');
        document.getElementById('errPincode').classList.remove('hidden');
        isValid = false;
      }

      if (!city) {
        addrCity.classList.add('error');
        document.getElementById('errCity').classList.remove('hidden');
        isValid = false;
      }

      if (!state) {
        addrState.classList.add('error');
        document.getElementById('errState').classList.remove('hidden');
        isValid = false;
      }

      if (!street) {
        addrStreet.classList.add('error');
        document.getElementById('errStreet').classList.remove('hidden');
        isValid = false;
      }

      if (!isValid) return;

      // Save delivery address for future convenience
      localStorage.setItem('skillbox_delivery_address', JSON.stringify({
        fullName, phone, pincode, city, state, street, addressType: selectedAddressType
      }));

      // Calculate total payable
      let totalPayableRupees = 0;
      let orderTitle = '';
      let orderQty = 0;

      if (checkoutMode === 'cart') {
        skillboxCart.forEach(item => {
          totalPayableRupees += Number(item.price_discounted || item.price || 0) * (item.quantity || 1);
        });
        orderQty = skillboxCart.reduce((sum, item) => sum + (item.quantity || 1), 0);
        orderTitle = `SkillBox Cart Order (${orderQty} items)`;
      } else {
        const unitPrice = Number(productData.price || 0);
        const unitDiscounted = Number(productData.price_discounted || unitPrice);
        totalPayableRupees = unitDiscounted * quantity;
        orderQty = quantity;
        orderTitle = productData.title || productData.course_name || 'SkillBox Kit';
      }

      const totalPayablePaise = Math.round(totalPayableRupees * 100);

      // Hide delivery modal
      deliveryModal.classList.remove('active');

      @php
        $rzpKey = \DB::table('payment_gateways')->where('key', 'razorpay_key')->value('value') ?? 'rzp_test_RmNXpiPry9Pf7U';
      @endphp

      const razorpayKey = "{{ $rzpKey }}";

      // Prepare items payload
      let orderItemsPayload = [];
      if (checkoutMode === 'cart') {
        orderItemsPayload = skillboxCart.map(item => ({
          product_id: item.id || null,
          title: item.title,
          image: item.image || null,
          unit_price: parseFloat(item.price) || 0,
          quantity: parseInt(item.qty) || 1
        }));
      } else {
        orderItemsPayload = [{
          product_id: currentProductId || null,
          title: orderTitle,
          image: (productData.thumbnail_image || productData.image || null),
          unit_price: unitDiscounted,
          quantity: quantity
        }];
      }

      function recordShopOrder(paymentId, payStatus) {
        fetch('/api/shop-orders/create', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({
            customer_name: fullName,
            customer_phone: phone,
            customer_email: "{{ userAuth() ? userAuth()->email : '' }}",
            pincode: pincode,
            city: city,
            state: state,
            country: 'India',
            address: street,
            address_type: selectedAddressType,
            payment_method: 'razorpay',
            payment_id: paymentId,
            payment_status: payStatus || 'paid',
            total_amount: totalPayableRupees,
            items: orderItemsPayload
          })
        }).then(r => r.json()).then(data => {
          console.log('Shop order saved successfully:', data);
        }).catch(err => {
          console.error('Error saving shop order:', err);
        });
      }

      const rzpOptions = {
        key: razorpayKey,
        amount: totalPayablePaise,
        currency: "INR",
        name: "{{ config('app.name', 'Skillvation') }}",
        description: `${orderTitle}`,
        image: "{{ asset('frontend/img/logo/logo.png') }}",
        prefill: {
          name: fullName,
          contact: phone,
          email: "{{ userAuth() ? userAuth()->email : 'customer@myskill.club' }}"
        },
        notes: {
          order_title: orderTitle,
          quantity: orderQty,
          full_address: `${street}, ${city}, ${state} - ${pincode}`,
          address_type: selectedAddressType
        },
        theme: {
          color: "#E2002B"
        },
        handler: function(response) {
          const paymentId = response.razorpay_payment_id || 'PAY_' + Math.random().toString(36).substr(2, 9).toUpperCase();
          successPaymentId.textContent = paymentId;
          successItemTitle.textContent = `${orderTitle}`;
          successTotalPaid.textContent = `₹ ${totalPayableRupees.toLocaleString('en-IN')}`;
          successShippingAddress.textContent = `${fullName}, ${street}, ${city}, ${state} - ${pincode}`;

          // Save order to backend database
          recordShopOrder(paymentId, 'paid');

          // Clear cart if cart checkout
          if (checkoutMode === 'cart') {
            skillboxCart = [];
            saveCart();
          }

          orderSuccessModal.classList.add('active');
        },
        modal: {
          ondismiss: function() {
            showToast('Payment Cancelled', 'You closed the payment window.');
          }
        }
      };

      try {
        if (typeof Razorpay !== 'undefined') {
          const rzp = new Razorpay(rzpOptions);
          rzp.on('payment.failed', function(response) {
            alert('Payment Failed: ' + (response.error.description || 'Unknown error'));
          });
          rzp.open();
        } else {
          throw new Error('Razorpay SDK unavailable');
        }
      } catch (err) {
        console.error('Opening Razorpay fallback:', err);
        const demoPaymentId = 'DEMO_PAY_' + Math.random().toString(36).substr(2, 9).toUpperCase();
        successPaymentId.textContent = demoPaymentId;
        successItemTitle.textContent = `${orderTitle}`;
        successTotalPaid.textContent = `₹ ${totalPayableRupees.toLocaleString('en-IN')}`;
        successShippingAddress.textContent = `${fullName}, ${street}, ${city}, ${state} - ${pincode}`;
        
        recordShopOrder(demoPaymentId, 'paid');

        if (checkoutMode === 'cart') {
          skillboxCart = [];
          saveCart();
        }

        orderSuccessModal.classList.add('active');
      }
    });

    // Close Order Success Modal
    closeSuccessModalBtn.addEventListener('click', () => {
      orderSuccessModal.classList.remove('active');
    });

    orderSuccessModal.addEventListener('click', (e) => {
      if (e.target === orderSuccessModal) {
        orderSuccessModal.classList.remove('active');
      }
    });

    // Tab Switching
    document.querySelectorAll('.prod-tab-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        document.querySelectorAll('.prod-tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.add('hidden'));

        this.classList.add('active');
        const targetId = this.getAttribute('data-tab');
        const targetPane = document.getElementById(targetId);
        if (targetPane) targetPane.classList.remove('hidden');
      });
    });

    // Initialize
    updateAllCartBadges();
    loadProductDetails();

  })();
</script>
@endpush
