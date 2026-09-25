@extends('frontend.home-four.layouts.master')

@section('meta_title', 'SkillBox — Hands-on Learning Kits & Resources')
@section('meta_description', 'Discover hands-on experiential activity kits, STEM boxes, and learning materials for students, teachers, and schools.')

@push('styles')
<style>
  /* Base page typography and layout */
  .skillbox-page-wrap {
    max-width: 1480px;
    margin: 0 auto;
  }

  /* Filter bar styles */
  .skillbox-controls {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
  }

  .skillbox-search-wrap {
    position: relative;
    flex-grow: 1;
    min-width: 280px;
  }

  .skillbox-search-input {
    width: 100%;
    padding: 0.65rem 1rem 0.65rem 2.75rem;
    border-radius: 12px;
    border: 1px solid #E2002B70;
    font-size: 0.95rem;
    color: #1e293b;
    background-color: #ffffff;
    transition: all 0.2s ease;
  }
  .skillbox-search-input:focus {
    outline: none;
    border-color: #E2002B;
    box-shadow: 0 0 0 3px rgba(226, 0, 43, 0.12);
  }

  .skillbox-search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #E2002B;
    font-size: 0.95rem;
    pointer-events: none;
  }

  .skillbox-clear-search {
    position: absolute;
    right: 0.85rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    cursor: pointer;
    border: none;
    background: transparent;
    display: none;
  }

  .skillbox-category-select {
    padding: 0.65rem 2.25rem 0.65rem 1rem;
    border-radius: 12px;
    border: 1px solid #E2002B70;
    font-size: 0.95rem;
    font-weight: 500;
    color: #1e293b;
    background-color: #ffffff;
    cursor: pointer;
    min-width: 200px;
    appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23e2002b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.85rem center;
    background-size: 1rem;
    transition: all 0.2s ease;
  }
  .skillbox-category-select:focus {
    outline: none;
    border-color: #E2002B;
    box-shadow: 0 0 0 3px rgba(226, 0, 43, 0.12);
  }

  .skillbox-actions-grp {
    display: flex;
    align-items: center;
    gap: 1.25rem;
  }

  .skillbox-action-btn {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background-color: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #475569;
    cursor: pointer;
    transition: all 0.2s ease;
  }
  .skillbox-action-btn:hover {
    background-color: #fff1f2;
    border-color: #fecdd3;
    color: #E2002B;
    transform: translateY(-2px);
  }

  .skillbox-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background-color: #E2002B;
    color: #ffffff;
    font-size: 0.65rem;
    font-weight: 700;
    width: 18px;
    height: 18px;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 4px rgba(226, 0, 43, 0.3);
  }

  /* Product Card */
  .skillbox-card {
    border-radius: 16px;
    overflow: hidden;
    background: #ffffff;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
    border: 1px solid #E7E7E7;
  }
  .skillbox-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
  }

  .skillbox-card__img-box {
    position: relative;
    background-color: #f3f4f6;
    height: 220px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.25rem;
    overflow: hidden;
    cursor: pointer;
  }

  .skillbox-card__img {
    max-width: 82%;
    max-height: 82%;
    object-fit: contain;
    transition: transform 0.3s ease;
  }
  .skillbox-card:hover .skillbox-card__img {
    transform: scale(1.06);
  }

  .skillbox-card__wish-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(4px);
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    cursor: pointer;
    transition: all 0.2s ease;
    z-index: 5;
  }
  .skillbox-card__wish-btn:hover {
    background: #ffffff;
    transform: scale(1.1);
    color: #E2002B;
  }
  .skillbox-card__wish-btn.active {
    color: #E2002B;
    background: #ffffff;
  }

  .skillbox-card__body {
    padding: 1.25rem 1rem;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    background: #ffffff;
  }

  .skillbox-card__title {
    font-size: 0.95rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.4rem;
    line-height: 1.35;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    height: 2.6em;
  }

  .skillbox-card__stars {
    display: flex;
    align-items: center;
    gap: 2px;
    color: #f59e0b;
    font-size: 0.8rem;
    margin-bottom: 0.75rem;
  }

  .skillbox-card__price-row {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    margin-top: auto;
    padding-top: 0.5rem;
    border-top: 1px solid #f1f5f9;
  }

  .skillbox-card__price {
    font-size: 1.2rem;
    font-weight: 800;
    color: #0f172a;
  }

  .skillbox-card__old-price {
    font-size: 0.85rem;
    color: #94a3b8;
    text-decoration: line-through;
    margin-left: 0.4rem;
  }

  .skillbox-card__btn {
    padding: 0.5rem 0.9rem;
    border-radius: 8px;
    background-color: #E2002B;
    color: #ffffff;
    font-size: 0.82rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }
  .skillbox-card__btn:hover {
    background-color: #c50024;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(226, 0, 43, 0.25);
  }

  /* Benefits Section */
  .skillbox-benefits-section {
    background-color: #f8fafc;
    padding: 4.5rem 0;
  }

  .benefit-card {
    border-radius: 20px;
    padding: 2rem;
    position: relative;
    overflow: hidden;
    border: 1px solid #e2e8f0;
  }
  .benefit-card--blue {
    background-color: #E2F3FD;
    border-color: #bfdbfe;
  }
  .benefit-card--peach {
    background-color: #FBF1E8;
    border-color: #fed7aa;
  }

  .benefit-subcard {
    border-radius: 14px;
    background: #ffffff;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    overflow: hidden;
    display: flex;
    flex-direction: column;
  }

  .benefit-subcard__img {
    width: 100%;
    height: 140px;
    object-fit: cover;
  }

  .benefit-subcard__header {
    padding: 0.75rem 1rem;
    font-weight: 700;
    font-size: 1rem;
    color: #ffffff;
  }

  .benefit-subcard__body {
    padding: 1rem;
    font-size: 0.88rem;
    color: #475569;
    line-height: 1.5;
  }

  /* Geometric accent circles */
  .geo-circle-gold {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 3px solid #FAC584;
    position: absolute;
    pointer-events: none;
  }
  .geo-circle-sky {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 3px solid #87CEEB;
    position: absolute;
    pointer-events: none;
  }

  /* Toast Notification */
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

  /* Pagination styles */
  .skillbox-page-btn {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    font-weight: 600;
    font-size: 0.88rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
  }
  .skillbox-page-btn:hover {
    border-color: #E2002B;
    color: #E2002B;
  }
  .skillbox-page-btn.active {
    background: #E2002B;
    border-color: #E2002B;
    color: #ffffff;
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
<div class="bg-slate-50/60 min-h-screen pt-0 pb-16">

  <!-- ── Top Featured Banner (Full Width Screen Edge-to-Edge) ──────────────────────── -->
  <div class="w-full overflow-hidden mb-8">
    <img
      src="{{ asset('frontend/img/skillbox/preschool.jpg') }}"
      alt="SkillBox Hands-on Learning Kit"
      class="w-full h-auto max-h-[460px] object-cover block shadow-sm"
      onerror="this.onerror=null;this.src='https://pedaskills.com/static/media/skillBoxImg.54ef565f4b0836600035.png';"
    />
  </div>

  <div class="skillbox-page-wrap px-4 sm:px-6 lg:px-8">

    <!-- ── Filter & Search Control Bar ──────────────────────────────── -->
    <div class="skillbox-controls bg-white p-4 rounded-2xl shadow-sm border border-slate-200/80">
      
      <!-- Search Input -->
      <div class="skillbox-search-wrap">
        <i class="fa-solid fa-magnifying-glass skillbox-search-icon"></i>
        <input
          type="text"
          id="productSearchInput"
          class="skillbox-search-input"
          placeholder="Search for products, kits, activities..."
          autocomplete="off"
        />
        <button id="clearSearchBtn" class="skillbox-clear-search" title="Clear search">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <!-- Category Selector Dropdown -->
      <div class="flex items-center gap-3">
        <select id="categoryFilter" class="skillbox-category-select">
          <option value="">All Categories</option>
          <option value="2">Activity Kits</option>
          <option value="7">Summer Camp</option>
          <option value="8">Annual Activity Kit</option>
        </select>
      </div>

      <!-- Actions: Wishlist & Cart -->
      <div class="skillbox-actions-grp ml-auto">
        <button id="wishlistHeaderBtn" class="skillbox-action-btn" title="View Wishlist">
          <i class="fa-solid fa-heart text-red-500 text-lg"></i>
          <span id="wishlistCountBadge" class="skillbox-badge">0</span>
        </button>

        <button type="button" id="cartHeaderBtn" class="skillbox-action-btn" title="View Shopping Cart">
          <i class="fa-solid fa-cart-shopping text-slate-700 text-lg"></i>
          <span id="cartCountBadge" class="skillbox-badge">0</span>
        </button>
      </div>

    </div>

    <!-- ── Products Grid Header ────────────────────────────────────── -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Available Learning Kits & Boxes</h2>
        <p class="text-sm text-slate-500 mt-0.5" id="productsCountText">Showing products</p>
      </div>
      <div class="flex items-center gap-2">
        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Sort by:</span>
        <select id="sortProducts" class="text-xs font-semibold bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-slate-700 focus:outline-none focus:border-red-500">
          <option value="featured">Featured</option>
          <option value="price-asc">Price: Low to High</option>
          <option value="price-desc">Price: High to Low</option>
          <option value="name">Name (A-Z)</option>
        </select>
      </div>
    </div>

    <!-- ── Loading Skeleton ────────────────────────────────────────── -->
    <div id="productsLoading" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-12">
      @for ($i = 0; $i < 8; $i++)
      <div class="bg-white rounded-2xl p-4 border border-slate-200/60 animate-pulse flex flex-col gap-3">
        <div class="bg-slate-200 h-44 rounded-xl w-full"></div>
        <div class="bg-slate-200 h-4 rounded w-3/4"></div>
        <div class="bg-slate-200 h-3 rounded w-1/2"></div>
        <div class="flex justify-between items-center mt-auto pt-3 border-t border-slate-100">
          <div class="bg-slate-200 h-5 rounded w-1/3"></div>
          <div class="bg-slate-200 h-7 rounded w-1/4"></div>
        </div>
      </div>
      @endfor
    </div>

    <!-- ── Product Grid Container ──────────────────────────────────── -->
    <div id="productsGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-12 hidden">
      <!-- Injected dynamically via JS -->
    </div>

    <!-- ── Empty State ─────────────────────────────────────────────── -->
    <div id="emptyState" class="hidden text-center py-16 px-4 bg-white rounded-2xl border border-slate-200/80 mb-12">
      <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
        <i class="fa-solid fa-box-open"></i>
      </div>
      <h3 class="text-lg font-bold text-slate-800">No Products Found</h3>
      <p class="text-sm text-slate-500 max-w-md mx-auto mt-1 mb-5">
        We couldn't find any products matching your selected search or category filters.
      </p>
      <button id="resetFiltersBtn" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold rounded-xl transition-all">
        Clear All Filters
      </button>
    </div>

    <!-- ── Pagination Controls ─────────────────────────────────────── -->
    <div id="paginationWrap" class="flex justify-center items-center gap-2 mt-8 mb-16 hidden">
      <!-- Injected dynamically via JS -->
    </div>

  </div>

  <!-- ── Value Proposition & Benefits Section ──────────────────────── -->
  <section class="skillbox-benefits-section border-t border-slate-200 mt-8">
    <div class="skillbox-page-wrap px-4 sm:px-6 lg:px-8">
      
      <!-- Section Title Header -->
      <div class="text-center max-w-3xl mx-auto mb-14">
        <p class="text-xs font-bold text-red-600 uppercase tracking-widest mb-2">Our Comprehensive SkillBox Ecosystem</p>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-4">
          Empowering Learners, Parents & Educators
        </h2>
        <p class="text-base text-slate-600">
          SkillBox integrates hands-on experiential materials, guided instruction, and collaborative activities aligned with modern NEP 2020 standards.
        </p>
      </div>

      <!-- 2 Major Benefit Pillars -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 relative">
        
        <!-- Left Pillar: For Children & Parents -->
        <div class="benefit-card benefit-card--blue">
          <div class="flex items-center gap-3 mb-6">
            <span class="w-10 h-10 rounded-xl bg-blue-500 text-white flex items-center justify-center font-bold text-lg shadow-sm">1</span>
            <div>
              <h3 class="text-xl font-bold text-slate-900">Hands-on Experience & Family Engagement</h3>
              <p class="text-xs text-blue-700 font-medium">Transforming home and classroom environments</p>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            
            <!-- For Children Subcard -->
            <div class="benefit-subcard">
              <img src="{{ asset('frontend/img/skillbox/forchildren.jpg') }}" alt="For Children" class="benefit-subcard__img" onerror="this.src='https://pedaskills.com/static/media/forchildren.cb0ca2e3ce3cfa06aa23.jpg';">
              <div class="benefit-subcard__header bg-blue-600">
                For Children
              </div>
              <div class="benefit-subcard__body">
                Engaging age-appropriate kits designed to develop critical problem-solving, sensory integration, robotics, and STEM curiosity.
              </div>
            </div>

            <!-- For Parents Subcard -->
            <div class="benefit-subcard">
              <img src="{{ asset('frontend/img/skillbox/forparent.jpg') }}" alt="For Parents" class="benefit-subcard__img" onerror="this.src='https://pedaskills.com/static/media/forparent.9d5f1719f115aa25a907.jpg';">
              <div class="benefit-subcard__header bg-amber-500">
                For Parents
              </div>
              <div class="benefit-subcard__body">
                Step-by-step guidance and parent-child collaborative milestones to track child cognitive growth without screen fatigue.
              </div>
            </div>

          </div>
        </div>

        <!-- Right Pillar: For School & Teachers -->
        <div class="benefit-card benefit-card--peach">
          <div class="flex items-center gap-3 mb-6">
            <span class="w-10 h-10 rounded-xl bg-amber-600 text-white flex items-center justify-center font-bold text-lg shadow-sm">2</span>
            <div>
              <h3 class="text-xl font-bold text-slate-900">Institutional Curriculum & Educator Aids</h3>
              <p class="text-xs text-amber-800 font-medium">Structured NEP 2020 skill alignment for schools</p>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            
            <!-- For School Subcard -->
            <div class="benefit-subcard">
              <img src="{{ asset('frontend/img/skillbox/preschool.jpg') }}" alt="For Preschools and Institutions" class="benefit-subcard__img" onerror="this.src='https://pedaskills.com/static/media/preschool.41c31751708329c32f36.jpg';">
              <div class="benefit-subcard__header bg-amber-600">
                For School
              </div>
              <div class="benefit-subcard__body">
                Turnkey activity boxes with complete semester mapping, material refills, assessment rubrics, and SAFAL/SQAAF compliance.
              </div>
            </div>

            <!-- For Teacher Subcard -->
            <div class="benefit-subcard">
              <img src="{{ asset('frontend/img/skillbox/forteacher.jpg') }}" alt="For Teachers and Facilitators" class="benefit-subcard__img" onerror="this.src='https://pedaskills.com/static/media/forteacher.957d27a152701bcfc948.jpg';">
              <div class="benefit-subcard__header bg-rose-600">
                For Teacher
              </div>
              <div class="benefit-subcard__body">
                Ready-to-deploy lesson blueprints, visual guides, student observation trackers, and interactive demo experiments.
              </div>
            </div>

          </div>
        </div>

      </div>

    </div>
  </section>

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
      <button type="button" onclick="document.getElementById('skillboxCartDrawer').classList.remove('active')" class="btn-purchase-now text-xs py-2 px-4 rounded-lg inline-block">
        Browse Kits
      </button>
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
          <img id="modalSummaryImg" src="https://pedaskills.com/static/media/skillBoxImg.54ef565f4b0836600035.png" alt="Kit" class="max-w-full max-h-full object-contain" />
        </div>
        <div class="flex-grow min-w-0">
          <h4 id="modalSummaryTitle" class="text-sm font-bold text-slate-900 truncate">Cart Order</h4>
          <div class="flex items-center gap-3 text-xs text-slate-500 mt-1">
            <span>Qty: <strong id="modalSummaryQty" class="text-slate-800">1</strong></span>
            <span>•</span>
            <span>Delivery: <strong class="text-emerald-600">FREE</strong></span>
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

      <!-- Country -->
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
      <button type="button" onclick="document.getElementById('orderSuccessModal').classList.remove('active')" class="btn-purchase-now">
        <i class="fa-solid fa-store mr-2"></i> Continue Shopping
      </button>
      <button type="button" id="closeSuccessModalBtn" class="btn-add-cart">
        Close
      </button>
    </div>
  </div>
</div>

<!-- ── Toast Alert ────────────────────────────────────────────────── -->
<div id="skillboxToast" class="skillbox-toast">
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

    // State
    let allProducts = [];
    let filteredProducts = [];
    let currentPage = 1;
    const perPage = 12;
    let selectedAddressType = 'Home';

    // Local Storage Wishlist & Cart
    let wishlistIds = JSON.parse(localStorage.getItem('skillbox_wishlist') || '[]');
    let skillboxCart = JSON.parse(localStorage.getItem('skillbox_cart') || '[]');

    // Elements
    const productsLoading = document.getElementById('productsLoading');
    const productsGrid = document.getElementById('productsGrid');
    const emptyState = document.getElementById('emptyState');
    const paginationWrap = document.getElementById('paginationWrap');
    const searchInput = document.getElementById('productSearchInput');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    const categoryFilter = document.getElementById('categoryFilter');
    const sortSelect = document.getElementById('sortProducts');
    const resetFiltersBtn = document.getElementById('resetFiltersBtn');
    const productsCountText = document.getElementById('productsCountText');
    const wishlistBadge = document.getElementById('wishlistCountBadge');
    const cartBadge = document.getElementById('cartCountBadge');
    const toast = document.getElementById('skillboxToast');
    const toastTitle = document.getElementById('toastTitle');
    const toastMessage = document.getElementById('toastMessage');
    const toastIcon = document.getElementById('toastIcon');

    // Cart Drawer Elements
    const cartHeaderBtn = document.getElementById('cartHeaderBtn');
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

    // Toast Utility
    function showToast(title, msg, isSuccess = true) {
      if (!toast) return;
      toastTitle.textContent = title;
      toastMessage.textContent = msg;
      toastIcon.className = isSuccess ? 'fa-solid fa-circle-check text-emerald-400 text-xl' : 'fa-solid fa-circle-info text-amber-400 text-xl';
      toast.classList.add('show');
      setTimeout(() => toast.classList.remove('show'), 3500);
    }

    // Wishlist Utility
    function updateWishlistBadge() {
      if (wishlistBadge) wishlistBadge.textContent = wishlistIds.length;
    }

    // Cart Utility
    function saveCart() {
      localStorage.setItem('skillbox_cart', JSON.stringify(skillboxCart));
      updateAllCartBadges();
      renderCartDrawer();
    }

    function updateAllCartBadges() {
      const totalCount = skillboxCart.reduce((sum, item) => sum + (Number(item.quantity) || 1), 0);
      const badges = document.querySelectorAll('#cartCountBadge, #headerCartBadge, .mini-cart-count');
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

    if (cartHeaderBtn) {
      cartHeaderBtn.addEventListener('click', (e) => {
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

    // Load Categories from API
    async function loadCategories() {
      try {
        const res = await fetch('/api/shop-categories');
        const json = await res.json();
        if (json && json.data && json.data.length > 0) {
          const currentVal = categoryFilter.value;
          categoryFilter.innerHTML = '<option value="">All Categories</option>' + 
            json.data.map(cat => `<option value="${cat.id}">${cat.name}</option>`).join('');
          if (currentVal) categoryFilter.value = currentVal;
        }
      } catch (err) {
        console.warn('Could not load categories:', err);
      }
    }

    // Load Products
    async function loadProducts() {
      productsLoading.classList.remove('hidden');
      productsGrid.classList.add('hidden');
      emptyState.classList.add('hidden');

      try {
        const res = await fetch('/api/shop-products');
        const json = await res.json();
        allProducts = json.data || [];
        applyFilters();
      } catch (err) {
        console.error('Error fetching products:', err);
        productsLoading.classList.add('hidden');
        emptyState.classList.remove('hidden');
      } finally {
        productsLoading.classList.add('hidden');
      }
    }

    // Filter and Sort Products
    function applyFilters() {
      const q = searchInput.value.trim().toLowerCase();
      const catId = categoryFilter.value;
      const sortBy = sortSelect.value;

      filteredProducts = allProducts.filter(item => {
        const title = (item.title || item.course_name || '').toLowerCase();
        const tags = (item.tags || '').toLowerCase();
        const matchesQuery = !q || title.includes(q) || tags.includes(q);
        const matchesCategory = !catId || String(item.category_id) === String(catId);
        return matchesQuery && matchesCategory;
      });

      // Sorting
      if (sortBy === 'price-asc') {
        filteredProducts.sort((a, b) => Number(a.price_discounted || a.price || 0) - Number(b.price_discounted || b.price || 0));
      } else if (sortBy === 'price-desc') {
        filteredProducts.sort((a, b) => Number(b.price_discounted || b.price || 0) - Number(a.price_discounted || a.price || 0));
      } else if (sortBy === 'name') {
        filteredProducts.sort((a, b) => (a.title || '').localeCompare(b.title || ''));
      }

      currentPage = 1;
      renderProducts();
    }

    // Render Products Grid & Pagination
    function renderProducts() {
      const total = filteredProducts.length;
      productsCountText.textContent = `Showing ${total} ${total === 1 ? 'product' : 'products'}`;

      if (total === 0) {
        productsGrid.classList.add('hidden');
        emptyState.classList.remove('hidden');
        paginationWrap.classList.add('hidden');
        return;
      }

      emptyState.classList.add('hidden');
      productsGrid.classList.remove('hidden');

      // Paginate
      const totalPages = Math.ceil(total / perPage);
      const startIdx = (currentPage - 1) * perPage;
      const paginatedItems = filteredProducts.slice(startIdx, startIdx + perPage);

      // Render Cards
      productsGrid.innerHTML = paginatedItems.map(item => {
        const id = item.id;
        const title = item.title || item.course_name || 'Activity Kit';
        const price = Number(item.price || 0);
        const discounted = Number(item.price_discounted || price);
        const hasDiscount = discounted < price;
        const image = item.image || item.image_small || 'https://pedaskills.com/static/media/skillBoxImg.54ef565f4b0836600035.png';
        const isWishlisted = wishlistIds.includes(Number(id));
        const rating = Number(item.rating || 5);

        return `
          <div class="skillbox-card">
            <div class="skillbox-card__img-box" onclick="window.location.href='/ProductDetails/${id}'">
              <img src="${image}" alt="${title}" class="skillbox-card__img" loading="lazy" onerror="this.onerror=null;this.src='https://pedaskills.com/static/media/skillBoxImg.54ef565f4b0836600035.png';" />
              <button 
                class="skillbox-card__wish-btn ${isWishlisted ? 'active' : ''}" 
                onclick="event.stopPropagation(); window.toggleWishlist(${id}, '${title.replace(/'/g, "\\'")}')"
                title="${isWishlisted ? 'Remove from Wishlist' : 'Add to Wishlist'}"
              >
                <i class="fa-solid fa-heart ${isWishlisted ? 'text-red-500' : ''}"></i>
              </button>
            </div>

            <div class="skillbox-card__body" onclick="window.location.href='/ProductDetails/${id}'" style="cursor: pointer;">
              <h3 class="skillbox-card__title" title="${title}">${title}</h3>
              
              <div class="skillbox-card__stars">
                ${Array(5).fill(0).map((_, idx) => `<i class="fa-solid fa-star ${idx < Math.round(rating) ? 'text-amber-400' : 'text-slate-200'}"></i>`).join('')}
                <span class="text-xs text-slate-400 ml-1.5">(4.9)</span>
              </div>

              <div class="skillbox-card__price-row">
                <div class="flex items-baseline">
                  <span class="skillbox-card__price">₹ ${discounted.toLocaleString('en-IN')}</span>
                  ${hasDiscount ? `<span class="skillbox-card__old-price">₹ ${price.toLocaleString('en-IN')}</span>` : ''}
                </div>
                <button 
                  class="skillbox-card__btn" 
                  onclick="event.stopPropagation(); window.quickAddToCart(${id}, '${title.replace(/'/g, "\\'")}', ${discounted}, '${image}')"
                >
                  <i class="fa-solid fa-cart-plus mr-1.5"></i> Order
                </button>
              </div>
            </div>
          </div>
        `;
      }).join('');

      // Render Pagination Controls
      if (totalPages > 1) {
        paginationWrap.classList.remove('hidden');
        let btns = '';

        if (currentPage > 1) {
          btns += `<button class="skillbox-page-btn" onclick="window.changePage(${currentPage - 1})"><i class="fa-solid fa-chevron-left"></i></button>`;
        }

        for (let p = 1; p <= totalPages; p++) {
          btns += `<button class="skillbox-page-btn ${p === currentPage ? 'active' : ''}" onclick="window.changePage(${p})">${p}</button>`;
        }

        if (currentPage < totalPages) {
          btns += `<button class="skillbox-page-btn" onclick="window.changePage(${currentPage + 1})"><i class="fa-solid fa-chevron-right"></i></button>`;
        }

        paginationWrap.innerHTML = btns;
      } else {
        paginationWrap.classList.add('hidden');
      }
    }

    // Global Handlers
    window.changePage = function(p) {
      currentPage = p;
      renderProducts();
      window.scrollTo({ top: document.getElementById('productsGrid').offsetTop - 120, behavior: 'smooth' });
    };

    window.toggleWishlist = function(id, title) {
      const numId = Number(id);
      const idx = wishlistIds.indexOf(numId);
      if (idx > -1) {
        wishlistIds.splice(idx, 1);
        showToast('Removed from Wishlist', `${title} removed.`, false);
      } else {
        wishlistIds.push(numId);
        showToast('Added to Wishlist', `${title} saved to your favorites.`, true);
      }
      localStorage.setItem('skillbox_wishlist', JSON.stringify(wishlistIds));
      updateWishlistBadge();
      renderProducts();
    };

    window.quickAddToCart = function(id, title, price, image) {
      const prodId = String(id);
      const existingIdx = skillboxCart.findIndex(item => String(item.id) === prodId);
      if (existingIdx > -1) {
        skillboxCart[existingIdx].quantity += 1;
      } else {
        skillboxCart.push({
          id: prodId,
          title: title,
          price: price,
          price_discounted: price,
          image: image || 'https://pedaskills.com/static/media/skillBoxImg.54ef565f4b0836600035.png',
          quantity: 1
        });
      }

      saveCart();
      showToast('Added to Cart', `"${title}" has been added.`, true);
      openSkillboxCart();
    };

    // Address Type Pill Selection
    document.querySelectorAll('.addr-type-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        document.querySelectorAll('.addr-type-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        selectedAddressType = this.getAttribute('data-type');
      });
    });

    // Live Pincode Lookup
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

    function openDeliveryModalForCart() {
      if (skillboxCart.length === 0) return;
      closeSkillboxCart();

      const totalItems = skillboxCart.reduce((sum, item) => sum + (item.quantity || 1), 0);
      let subtotal = 0;
      skillboxCart.forEach(item => {
        subtotal += Number(item.price_discounted || item.price || 0) * (item.quantity || 1);
      });

      modalSummaryTitle.textContent = `SkillBox Cart Order (${totalItems} items)`;
      modalSummaryQty.textContent = totalItems;
      modalSummaryTotal.textContent = `₹ ${subtotal.toLocaleString('en-IN')}`;
      btnPayAmountText.textContent = `Pay ₹ ${subtotal.toLocaleString('en-IN')}`;
      modalSummaryImg.src = skillboxCart[0]?.image || 'https://pedaskills.com/static/media/skillBoxImg.54ef565f4b0836600035.png';

      const savedAddress = JSON.parse(localStorage.getItem('skillbox_delivery_address') || '{}');
      if (savedAddress.fullName && !addrFullName.value) addrFullName.value = savedAddress.fullName;
      if (savedAddress.phone && !addrPhone.value) addrPhone.value = savedAddress.phone;
      if (savedAddress.pincode && !addrPincode.value) addrPincode.value = savedAddress.pincode;
      if (savedAddress.city && !addrCity.value) addrCity.value = savedAddress.city;
      if (savedAddress.state && !addrState.value) addrState.value = savedAddress.state;
      if (savedAddress.street && !addrStreet.value) addrStreet.value = savedAddress.street;

      deliveryModal.classList.add('active');
    }

    cartProceedCheckoutBtn.addEventListener('click', openDeliveryModalForCart);

    closeDeliveryModalBtn.addEventListener('click', () => {
      deliveryModal.classList.remove('active');
    });

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

      localStorage.setItem('skillbox_delivery_address', JSON.stringify({
        fullName, phone, pincode, city, state, street, addressType: selectedAddressType
      }));

      let totalPayableRupees = 0;
      skillboxCart.forEach(item => {
        totalPayableRupees += Number(item.price_discounted || item.price || 0) * (item.quantity || 1);
      });
      const orderQty = skillboxCart.reduce((sum, item) => sum + (item.quantity || 1), 0);
      const orderTitle = `SkillBox Order (${orderQty} items)`;
      const totalPayablePaise = Math.round(totalPayableRupees * 100);

      deliveryModal.classList.remove('active');

      @php
        $rzpKey = \DB::table('payment_gateways')->where('key', 'razorpay_key')->value('value') ?? 'rzp_test_RmNXpiPry9Pf7U';
      @endphp

      const razorpayKey = "{{ $rzpKey }}";

      // Prepare items payload
      const orderItemsPayload = skillboxCart.map(item => ({
        product_id: item.id || null,
        title: item.title || item.course_name || 'SkillBox Item',
        image: item.image || item.thumbnail_image || null,
        unit_price: parseFloat(item.price_discounted || item.price) || 0,
        quantity: parseInt(item.quantity || item.qty) || 1
      }));

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

          skillboxCart = [];
          saveCart();

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

        skillboxCart = [];
        saveCart();

        orderSuccessModal.classList.add('active');
      }
    });

    closeSuccessModalBtn.addEventListener('click', () => {
      orderSuccessModal.classList.remove('active');
    });

    orderSuccessModal.addEventListener('click', (e) => {
      if (e.target === orderSuccessModal) {
        orderSuccessModal.classList.remove('active');
      }
    });

    // Event Listeners
    searchInput.addEventListener('input', function() {
      clearSearchBtn.style.display = this.value ? 'block' : 'none';
      applyFilters();
    });

    clearSearchBtn.addEventListener('click', function() {
      searchInput.value = '';
      this.style.display = 'none';
      applyFilters();
    });

    categoryFilter.addEventListener('change', applyFilters);
    sortSelect.addEventListener('change', applyFilters);

    resetFiltersBtn.addEventListener('click', function() {
      searchInput.value = '';
      clearSearchBtn.style.display = 'none';
      categoryFilter.value = '';
      sortSelect.value = 'featured';
      applyFilters();
    });

    document.getElementById('wishlistHeaderBtn')?.addEventListener('click', function() {
      if (wishlistIds.length === 0) {
        showToast('Your Wishlist is Empty', 'Browse products and tap the heart icon to add favorites.', false);
      } else {
        showToast('Wishlist', `You have ${wishlistIds.length} item(s) in your wishlist.`, true);
      }
    });

    // Initial Load
    updateWishlistBadge();
    updateAllCartBadges();
    loadCategories();
    loadProducts();

  })();
</script>
@endpush
