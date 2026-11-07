@extends('frontend.home-four.layouts.master')

@section('meta_title', $offer->title . ' — Exclusive School Promotion | Skillvation')
@section('meta_description', $offer->subtitle ?? 'Equip your school with turnkey STEM, Robotics, and CBSE Composite Skill Labs at exclusive discounted prices.')

@push('styles')
<style>
  .offer-gradient-bg {
    background: radial-gradient(120% 120% at 50% 0%, #1e1b4b 0%, #0f172a 60%, #020617 100%);
  }
  .card-glow:hover {
    box-shadow: 0 20px 40px -15px rgba(244, 116, 43, 0.25);
    transform: translateY(-4px);
  }
  .countdown-box {
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.15);
  }
  .pulse-orange {
    animation: pulseOrange 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
  }
  @keyframes pulseOrange {
    0%, 100% { opacity: 1; }
    50% { opacity: .5; }
  }
  /* Modal backdrop and scroll isolation */
  .checkout-modal-backdrop {
    background-color: rgba(15, 23, 42, 0.85);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
  }
  /* Prevent background page scroll when any modal is open (locks html and body, kills multi-scroll) */
  html.modal-open,
  body.modal-open {
    overflow: hidden !important;
    height: 100% !important;
  }
  /* Custom sleek scrollbar for modal interior */
  .custom-modal-scroll::-webkit-scrollbar {
    width: 6px;
  }
  .custom-modal-scroll::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 9999px;
  }
  .custom-modal-scroll::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 9999px;
  }
  .custom-modal-scroll::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
  }
  /* Smooth shake animation for unverified notice */
  @keyframes shakeErr {
    0%, 100% { transform: translateX(0); }
    20%, 60% { transform: translateX(-6px); }
    40%, 80% { transform: translateX(6px); }
  }
  .shake-error {
    animation: shakeErr 0.4s ease-in-out;
  }
</style>
@endpush

@section('contents')

{{-- Admin preview alert if campaign is disabled and admin is forcing preview --}}
@if(!$offer->is_active && $isAdmin && !empty($isAdminPreview))
<div class="bg-amber-500 text-slate-900 py-3 px-4 font-bold text-center text-sm shadow-md sticky top-0 z-50 flex items-center justify-center gap-2">
  <i class="fa-solid fa-triangle-exclamation"></i>
  <span>ADMIN PREVIEW MODE (?preview=1): This Lab Offer campaign is currently <strong>DISABLED</strong> in Admin Panel. Regular visitors see the offer concluded page.</span>
  <a href="{{ route('admin.lab-offers') }}" class="underline hover:text-black ml-2 font-extrabold">Open Admin Settings &rarr;</a>
</div>
@endif

@if(!$isActive)
{{-- =========================================================================
     OFFER INACTIVE / CONCLUDED STATE (When Disabled)
     ========================================================================= --}}
<section class="offer-gradient-bg py-24 text-white min-h-[70vh] flex items-center">
  <div class="max-w-4xl mx-auto px-4 text-center">
    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 text-amber-400 font-bold text-xs uppercase tracking-wider mb-6 border border-white/15">
      <i class="fa-solid fa-bell"></i> Offer Window Closed
    </div>
    <h1 class="text-3xl sm:text-5xl font-extrabold mb-6 text-white tracking-tight leading-tight">
      This Lab Promotional Offer is Currently Inactive
    </h1>
    <p class="text-lg text-slate-300 mb-8 max-w-2xl mx-auto leading-relaxed">
      Our seasonal special promotion has concluded, but turnkey CBSE Composite Skill Labs, STEM Innovation Hubs, and Robotics Labs are always available with full curriculum, equipment, and teacher training.
    </p>

    <div class="flex flex-wrap items-center justify-center gap-4">
      <a href="{{ url('/labs') }}" class="px-8 py-3.5 rounded-xl bg-brand-orange hover:bg-orange-600 text-white font-extrabold text-base shadow-lg transition-all hover:scale-105 inline-flex items-center gap-2">
        <i class="fa-solid fa-microchip"></i> Explore All Standard Lab Models
      </a>
      <a href="{{ url('/contact') }}" class="px-8 py-3.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-base border border-white/20 transition-all inline-flex items-center gap-2">
        <i class="fa-solid fa-envelope"></i> Request Institutional Quote
      </a>
    </div>

    @if($isAdmin)
    <div class="mt-8 p-3.5 rounded-2xl bg-amber-500/15 border border-amber-500/40 text-amber-300 text-xs font-bold inline-flex items-center gap-2">
      <i class="fa-solid fa-gear text-amber-400"></i>
      <span>Admin Notice: Campaign is currently <strong>DISABLED</strong>.</span>
      <a href="{{ route('admin.lab-offers') }}" class="underline hover:text-white font-black ml-1">Enable in Admin</a>
      <span>•</span>
      <a href="{{ route('lab.offer', ['preview' => 1]) }}" class="underline hover:text-white font-black">Force Preview Active Layout</a>
    </div>
    @endif

    <div class="mt-12 pt-8 border-t border-white/10 text-slate-400 text-sm">
      Have an ongoing quotation? Contact your designated academic consultant or reach us at <a href="mailto:{{ $offer->support_email ?? 'admissions@skillvation.com' }}" class="text-amber-400 underline">{{ $offer->support_email ?? 'admissions@skillvation.com' }}</a>.
    </div>
  </div>
</section>

@else
{{-- =========================================================================
     ACTIVE PROMOTIONAL CAMPAIGN
     ========================================================================= --}}

@php
  $enquiryWhatsApp = '+91 99456 12226';
  $enquiryWhatsAppUrl = 'https://wa.me/919945612226?text=' . urlencode('Hi Skillvation Team, I am enquiring about the Special School Lab Offer & Packages.');
@endphp

{{-- 1. HERO SECTION WITH COUNTDOWN --}}
<section class="offer-gradient-bg pt-16 pb-16 lg:pt-20 lg:pb-24 text-white relative overflow-hidden">
  {{-- Ambient light effects --}}
  <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-96 bg-brand-orange/15 blur-[120px] rounded-full pointer-events-none"></div>

  <div class="max-w-container-max mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="text-center max-w-4xl mx-auto">
      
      {{-- Event Badge pill --}}
      <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-brand-orange/20 text-amber-300 font-extrabold text-xs sm:text-sm uppercase tracking-wider mb-6 border border-brand-orange/40 shadow-inner">
        <span class="w-2.5 h-2.5 rounded-full bg-brand-orange pulse-orange"></span>
        {{ $offer->badge_text ?: '🔥 EXCLUSIVE SCHOOL FESTIVAL OFFER — LIMITED TIME DISCOUNTS' }}
      </div>

      {{-- Title --}}
      <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-white tracking-tight leading-[1.15] mb-6">
        {{ $offer->title ?: 'Special School Lab Festival Offer' }}
      </h1>

      {{-- Subtitle --}}
      <p class="text-base sm:text-xl text-slate-300 leading-relaxed max-w-3xl mx-auto mb-8">
        {{ $offer->subtitle ?: 'Transform your school with state-of-the-art STEM, Robotics, AI, and CBSE Composite Skill Labs at exclusive promotional rates. Pre-book with a flexible ₹25,000 token today or save an instant extra 10% on full payment.' }}
      </p>

      {{-- Live Countdown Timer --}}
      @php
        $targetCountdown = $offer->countdown_end ?? \Carbon\Carbon::parse('2026-11-25 10:00:00');
      @endphp
      <div class="mb-10 max-w-xl mx-auto" id="countdownWrapper" data-end="{{ $targetCountdown->toIso8601String() }}">
        <div class="text-xs uppercase tracking-widest text-slate-400 font-bold mb-3 flex items-center justify-center gap-2">
          <i class="fa-solid fa-stopwatch text-amber-400"></i> Promotional Pricing Closes In
        </div>
        <div class="grid grid-cols-4 gap-3 sm:gap-4 text-center">
          <div class="countdown-box rounded-2xl p-3 sm:p-4">
            <span class="text-2xl sm:text-4xl font-black text-white" id="cdDays">00</span>
            <span class="block text-[11px] sm:text-xs text-slate-400 uppercase font-bold mt-1">Days</span>
          </div>
          <div class="countdown-box rounded-2xl p-3 sm:p-4">
            <span class="text-2xl sm:text-4xl font-black text-white" id="cdHours">00</span>
            <span class="block text-[11px] sm:text-xs text-slate-400 uppercase font-bold mt-1">Hours</span>
          </div>
          <div class="countdown-box rounded-2xl p-3 sm:p-4">
            <span class="text-2xl sm:text-4xl font-black text-white" id="cdMinutes">00</span>
            <span class="block text-[11px] sm:text-xs text-slate-400 uppercase font-bold mt-1">Minutes</span>
          </div>
          <div class="countdown-box rounded-2xl p-3 sm:p-4">
            <span class="text-2xl sm:text-4xl font-black text-amber-400" id="cdSeconds">00</span>
            <span class="block text-[11px] sm:text-xs text-slate-400 uppercase font-bold mt-1">Seconds</span>
          </div>
        </div>
      </div>

      {{-- Key highlights pill checklist --}}
      @if(!empty($offer->highlights) && is_array($offer->highlights))
      <div class="flex flex-wrap items-center justify-center gap-2 sm:gap-3 max-w-4xl mx-auto mb-10">
        @foreach($offer->highlights as $hl)
          <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg bg-slate-800/80 border border-slate-700 text-slate-200 text-xs sm:text-sm font-semibold">
            <i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i>
            {{ $hl }}
          </span>
        @endforeach
      </div>
      @endif

      {{-- Primary Action Buttons --}}
      <div class="flex flex-wrap items-center justify-center gap-3 sm:gap-4">
        <a href="#discountRegisterSection" class="px-7 py-3.5 rounded-xl bg-brand-orange hover:bg-orange-600 text-white font-extrabold text-sm sm:text-base shadow-xl shadow-brand-orange/30 transition-all hover:scale-105 inline-flex items-center gap-2.5">
          <i class="fa-solid fa-percent"></i>
          <span>Lock 10% Extra Discount &amp; Pre-Book</span>
        </a>

        <a href="{{ $enquiryWhatsAppUrl }}" target="_blank" class="px-6 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm sm:text-base shadow-lg transition-all inline-flex items-center gap-2">
          <i class="fa-brands fa-whatsapp text-lg"></i>
          <span>WhatsApp: +91 99456 12226</span>
        </a>

        <a href="{{ route('login', ['redirect' => route('lab.offer')]) }}" class="px-5 py-3.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-sm sm:text-base border border-white/20 transition-all inline-flex items-center gap-2">
          <i class="fa-solid fa-arrow-right-to-bracket"></i>
          <span>Redirect to Login</span>
        </a>
      </div>

    </div>
  </div>
</section>

{{-- 2. TRUST STATS STRIP --}}
<section class="bg-slate-900 border-y border-slate-800 py-6">
  <div class="max-w-container-max mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
      <div>
        <div class="text-2xl sm:text-3xl font-black text-amber-400">100+</div>
        <div class="text-xs sm:text-sm text-slate-400 font-semibold uppercase mt-0.5">Partner Schools</div>
      </div>
      <div>
        <div class="text-2xl sm:text-3xl font-black text-emerald-400">100%</div>
        <div class="text-xs sm:text-sm text-slate-400 font-semibold uppercase mt-0.5">CBSE &amp; NEP Aligned</div>
      </div>
      <div>
        <div class="text-2xl sm:text-3xl font-black text-blue-400">600+</div>
        <div class="text-xs sm:text-sm text-slate-400 font-semibold uppercase mt-0.5">Turnkey Lab Items</div>
      </div>
      <div>
        <div class="text-2xl sm:text-3xl font-black text-purple-400">1,500+</div>
        <div class="text-xs sm:text-sm text-slate-400 font-semibold uppercase mt-0.5">Certified Teachers</div>
      </div>
    </div>
  </div>
</section>

{{-- =========================================================================
     1.3. 10% DISCOUNT & PRE-BOOKING ACTIVATION FUNNEL (WHATSAPP MOBILE + OTP)
     ========================================================================= --}}
<section class="py-12 sm:py-16 bg-slate-900 border-b border-slate-800 text-white relative overflow-hidden" id="discountRegisterSection">
  <a id="vipRegisterSection"></a> {{-- anchor fallback --}}
  <div class="max-w-3xl mx-auto px-4 sm:px-6 relative z-10">

    <div class="bg-gradient-to-b from-slate-800 to-slate-900 rounded-3xl border border-slate-700 p-6 sm:p-10 shadow-2xl relative">
      <div class="absolute -top-12 -right-12 w-48 h-48 bg-brand-orange/20 rounded-full blur-3xl pointer-events-none"></div>

      <div class="text-center mb-8">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-black uppercase tracking-wider mb-3 border border-amber-500/30">
          <i class="fa-solid fa-percent"></i> Instant 10% Extra Discount &amp; Pre-Booking
        </span>
        <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
          Verify WhatsApp Number &amp; Lock 10% Extra Discount
        </h2>
        <p class="text-slate-300 text-xs sm:text-sm mt-2 max-w-xl mx-auto leading-relaxed">
          Enter your WhatsApp mobile number to verify and instantly unlock 10% extra discount and &#8377;25,000 pre-booking privileges across all lab packages.
        </p>
      </div>

      {{-- STAGE A: PHONE + WHATSAPP OTP VERIFICATION --}}
      <div id="vipOtpContainer" class="space-y-4">
        
        {{-- Phone Input Row --}}
        <div id="vipPhoneInputBlock">
          <label class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider mb-1.5">
            Step 1: Enter WhatsApp Mobile Number <span class="text-red-400">*</span>
          </label>
          <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
            <div class="relative flex-1">
              <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400 select-none">+91</span>
              <input type="tel" id="vipPhoneInput" maxlength="10" placeholder="9876543210"
                     value="{{ $user->phone ?? '' }}"
                     class="w-full pl-14 pr-4 py-3.5 rounded-xl bg-slate-950/80 border border-slate-600 text-white font-bold text-base focus:ring-2 focus:ring-brand-orange focus:border-brand-orange placeholder:text-slate-500">
            </div>
            <button type="button" id="btnVipSendOtp" onclick="sendVipWhatsAppOtp()"
                    class="px-6 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-sm shadow-lg transition-all flex items-center justify-center gap-2 shrink-0">
              <i class="fa-brands fa-whatsapp text-lg"></i>
              <span id="btnVipSendOtpText">Get WhatsApp OTP</span>
            </button>
          </div>
          <div id="vipPhoneErr" class="hidden mt-2 text-xs text-red-400 font-bold flex items-center gap-1.5">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span id="vipPhoneErrText">Please enter a valid 10-digit mobile number.</span>
          </div>
        </div>

        {{-- OTP Drawer (Hidden until sent) --}}
        <div id="vipOtpDrawer" class="hidden p-4 sm:p-5 rounded-2xl bg-slate-950/90 border border-emerald-500/40 space-y-3">
          <div class="flex items-center justify-between flex-wrap gap-2">
            <div class="text-xs text-emerald-300 font-bold" id="vipOtpStatusMsg">
              <i class="fa-brands fa-whatsapp mr-1 text-base"></i> Enter the 6-digit OTP sent to your WhatsApp number
            </div>
            <span id="vipOtpCountdown" class="text-xs font-mono font-bold text-slate-400"></span>
          </div>

          <div class="flex items-center gap-2.5">
            <input type="text" id="vipOtpCode" maxlength="6" placeholder="• • • • • •"
                   class="flex-1 px-4 py-3 rounded-xl bg-slate-900 border border-emerald-500/60 text-white font-mono font-black text-lg tracking-widest text-center focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                   oninput="if(this.value.length === 6) verifyVipWhatsAppOtp()">
            <button type="button" id="btnVipVerifyOtp" onclick="verifyVipWhatsAppOtp()"
                    class="px-6 py-3 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-black text-sm shadow transition-all shrink-0">
              Verify OTP
            </button>
          </div>

          <div class="flex items-center justify-between text-xs text-slate-400">
            <button type="button" id="btnVipResend" onclick="sendVipWhatsAppOtp(true)" class="hidden text-amber-400 hover:underline font-bold">
              <i class="fa-solid fa-arrow-rotate-right mr-1"></i> Resend WhatsApp OTP
            </button>
            <span class="text-[11px] text-slate-500">Takes ~5-10 seconds to arrive</span>
          </div>
        </div>

      </div>

      {{-- STAGE B: ATTENDEE PROFILE & DISCOUNT ACTIVATION (Appears immediately upon OTP verification) --}}
      <div id="vipProfileBlock" class="hidden space-y-4 pt-4 border-t border-slate-700">
        <div class="p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/40 text-emerald-300 text-xs font-bold flex items-center justify-between">
          <div class="flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-400 text-base"></i>
            <span>WhatsApp Mobile Verified: <strong class="text-white font-mono" id="vipVerifiedPhoneBadge">+91 XXXXXXXXXX</strong></span>
          </div>
          <button type="button" onclick="editVipPhone()" class="text-xs text-amber-400 hover:underline font-bold">Change</button>
        </div>

        <form id="vipProfileForm" onsubmit="generateVipPass(event)" class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <div>
              <label class="block text-xs font-bold text-slate-300 mb-1">Your Full Name <span class="text-red-400">*</span></label>
              <input type="text" id="vipFullName" required placeholder="Enter your full name"
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/80 border border-slate-600 text-white font-semibold text-sm focus:ring-brand-orange">
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-300 mb-1">School / Institution Name <span class="text-red-400">*</span></label>
              <input type="text" id="vipSchoolName" required placeholder="Enter school / institution name"
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/80 border border-slate-600 text-white font-semibold text-sm focus:ring-brand-orange">
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <div>
              <label class="block text-xs font-bold text-slate-300 mb-1">Designation / Role <span class="text-red-400">*</span></label>
              <select id="vipDesignation" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/80 border border-slate-600 text-white font-semibold text-sm focus:ring-brand-orange">
                <option value="Principal">School Principal</option>
                <option value="Trustee / Director">Trustee / Management Director</option>
                <option value="Lab Coordinator">STEM / Lab Coordinator</option>
                <option value="Senior Teacher">Senior Teacher / Faculty</option>
                <option value="Academic Consultant">Academic Consultant / Other</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-300 mb-1">City <span class="text-red-400">*</span></label>
              <input type="text" id="vipCity" required placeholder="Enter your city"
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/80 border border-slate-600 text-white font-semibold text-sm focus:ring-brand-orange">
            </div>
          </div>

          <button type="submit" id="btnGeneratePass"
                  class="w-full py-3.5 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-black text-sm sm:text-base shadow-xl transition-all flex items-center justify-center gap-2">
            <i class="fa-solid fa-lock-open"></i>
            <span id="btnGeneratePassText">Activate 10% Extra Discount &amp; Pre-Booking Privileges</span>
          </button>
        </form>
      </div>

      {{-- STAGE C: CONFIRMATION CARD (Activated upon submission) --}}
      <div id="vipPassCard" class="hidden pt-6 space-y-4">
        <div class="p-6 rounded-2xl bg-gradient-to-r from-emerald-950/70 via-slate-900 to-emerald-950/70 border border-emerald-500/50 shadow-2xl text-center space-y-3">
          <div class="w-14 h-14 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-2xl mx-auto">
            <i class="fa-solid fa-circle-check"></i>
          </div>
          <span class="inline-block px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-black uppercase tracking-wider">
            10% Extra Discount &amp; &#8377;25K Token Privileges Unlocked
          </span>
          <h3 class="text-xl sm:text-2xl font-black text-white" id="passHolderName">-</h3>
          <p class="text-xs text-amber-300 font-bold" id="passSchoolName">-</p>
          <div class="p-3 rounded-xl bg-slate-950/80 border border-slate-700 max-w-md mx-auto text-xs text-slate-300 flex items-center justify-between">
            <span>Verified Mobile: <strong class="text-white font-mono" id="passVerifiedPhoneText"></strong></span>
            <span class="text-emerald-400 font-bold">10% OFF Active</span>
          </div>
          <p class="text-xs text-slate-400 max-w-md mx-auto">
            Your special promotional privileges are now active on this device. Choose your lab package below and complete booking with a flexible &#8377;25,000 token or save 10% on full payment!
          </p>
        </div>

        <div class="text-center">
          <a href="#offers" class="px-8 py-3.5 rounded-xl bg-brand-orange hover:bg-orange-600 text-white font-black text-sm shadow-xl transition-all inline-flex items-center gap-2">
            <i class="fa-solid fa-tags"></i>
            <span>Explore Promotional Lab Packages Below &darr;</span>
          </a>
        </div>
      </div>

    </div>

  </div>
</section>

{{-- 3. PROMOTIONAL PRODUCTS GRID --}}
<section class="py-16 sm:py-24 bg-slate-50" id="offers">
  <div class="max-w-container-max mx-auto px-4 sm:px-6 lg:px-8">
    
    <div class="text-center max-w-3xl mx-auto mb-12">
      <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-orange/10 text-brand-orange text-xs font-bold uppercase tracking-wider mb-3">
        <i class="fa-solid fa-tags"></i> LIMITED-TIME PROMOTIONAL PACKAGES
      </div>
      <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight">
        Featured Promotional Lab Packages
      </h2>
      <p class="text-slate-600 text-base sm:text-lg mt-4">
        Transform your school with state-of-the-art STEM, Robotics, AI, and CBSE Composite Skill Labs at exclusive promotional rates. Lock in special rates today with our flexible &#8377;25,000 pre-booking token, or pay in full online to unlock an extra 10% instant discount!
      </p>
    </div>

    @php
      $bookingActive = !empty($offer->booking_enabled);
      $tokenAmt = (float)($offer->booking_token_amount ?? 25000);
      $extraPct = (float)($offer->booking_extra_discount_percent ?? 10);
    @endphp

    {{-- Pre-Booking & Extra Discount Banner --}}
    @if($bookingActive)
    <div class="mb-12 p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 border border-slate-800 text-white shadow-2xl relative overflow-hidden">
      <div class="absolute -right-16 -top-16 w-64 h-64 bg-brand-orange/15 rounded-full blur-3xl pointer-events-none"></div>
      <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-emerald-500/15 rounded-full blur-3xl pointer-events-none"></div>

      <div class="flex flex-col lg:flex-row items-center justify-between gap-6 relative z-10">
        <div class="text-center lg:text-left space-y-2 max-w-2xl">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-orange/20 border border-brand-orange/40 text-amber-300 text-xs font-black uppercase tracking-wider">
            <i class="fa-solid fa-fire text-brand-orange"></i> {{ $offer->booking_title ?? 'Special Pre-Booking & Advance Payment Privileges' }}
          </div>
          <h3 class="text-2xl sm:text-3xl font-black text-white leading-tight">
            Pre-Book with ₹{{ number_format($tokenAmt) }} Token <span class="text-brand-orange">OR</span> Pay Full for Extra {{ $extraPct }}% OFF!
          </h3>
          <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
            {{ $offer->booking_subtitle ?? 'Secure promotional pricing today with a flexible advance token (balance payable upon lab setup), OR complete 100% payment online today to unlock an additional instant discount!' }}
          </p>
        </div>

        <div class="grid grid-cols-2 gap-3 shrink-0 w-full lg:w-auto">
          {{-- Advance Token Badge --}}
          <div class="p-4 rounded-2xl bg-slate-800/90 border border-amber-500/30 text-center">
            <span class="block text-[11px] uppercase font-black tracking-wider text-amber-400">Advance Token</span>
            <span class="text-2xl sm:text-3xl font-black text-white mt-0.5 block">₹{{ number_format($tokenAmt) }}</span>
            <span class="block text-[10px] text-slate-400 mt-0.5 font-medium">Balance on delivery</span>
          </div>

          {{-- Extra Discount Badge --}}
          <div class="p-4 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-700 text-center shadow-lg shadow-emerald-900/30">
            <span class="block text-[11px] uppercase font-black tracking-wider text-emerald-100">Full Payment</span>
            <span class="text-2xl sm:text-3xl font-black text-white mt-0.5 block">+{{ $extraPct }}% OFF</span>
            <span class="block text-[10px] text-emerald-100 mt-0.5 font-bold">Instant extra deduction</span>
          </div>
        </div>
      </div>
    </div>
    @endif

    @php
      $promoList = is_array($offer->products) ? $offer->products : [];
      $promoList = array_filter($promoList, fn($p) => !empty($p['is_active']));
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-8">
      @foreach($promoList as $p)
        @php
          $mrp = (float)($p['mrp'] ?? 0);
          $offerPrice = (float)($p['offer_price'] ?? 0);
          $savings = $mrp > $offerPrice ? ($mrp - $offerPrice) : (float)($p['discount_amount'] ?? 0);
          $savingsPct = $mrp > 0 && $savings > 0 ? round(($savings / $mrp) * 100) : 0;
          $features = is_array($p['features'] ?? null) ? $p['features'] : [];
          
          $extraDiscountVal = round(($offerPrice * $extraPct) / 100);
          $fullDiscountedPrice = $offerPrice - $extraDiscountVal;
          $balanceDueOnToken = max(0, $offerPrice - $tokenAmt);
        @endphp

        <div class="bg-white rounded-3xl border-2 border-slate-200/80 p-6 sm:p-8 flex flex-col justify-between transition-all duration-300 card-glow relative overflow-hidden shadow-sm">
          
          {{-- Badge banner --}}
          @if(!empty($p['badge']))
          <div class="absolute top-0 right-0">
            <div class="bg-brand-orange text-white text-[11px] font-black uppercase px-4 py-1.5 rounded-bl-2xl shadow-md tracking-wider">
              {{ $p['badge'] }}
            </div>
          </div>
          @endif

          <div>
            {{-- Title & Subtitle --}}
            <div class="pr-20 mb-3">
              <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 leading-snug">
                {{ $p['title'] }}
              </h3>
              @if(!empty($p['subtitle']))
              <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">
                {{ $p['subtitle'] }}
              </p>
              @endif
            </div>

            {{-- Price Display --}}
            <div class="my-6 p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
              <div class="flex items-baseline gap-3 flex-wrap">
                <span class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                  ₹{{ number_format($offerPrice) }}
                </span>
                @if($mrp > $offerPrice)
                <span class="text-lg sm:text-xl text-slate-400 line-through font-bold">
                  ₹{{ number_format($mrp) }}
                </span>
                @endif
              </div>

              @if($savings > 0)
              <div class="mt-2 flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-100 text-emerald-800 text-xs font-extrabold">
                  <i class="fa-solid fa-tag"></i> Save ₹{{ number_format($savings) }} ({{ $savingsPct }}% OFF)
                </span>
                <span class="text-[11px] text-slate-500 font-medium">Turnkey Inclusive</span>
              </div>
              @endif

              {{-- Booking / Extra Discount Plan Comparison Pills --}}
              @if($bookingActive)
              <div class="mt-4 pt-3 border-t border-slate-200/80 grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                <div class="p-3 rounded-xl bg-amber-50/80 border border-amber-200">
                  <div class="font-extrabold text-amber-900 flex items-center gap-1">
                    <i class="fa-solid fa-bookmark text-amber-600"></i> Pre-Book with Token
                  </div>
                  <div class="text-slate-900 font-black text-sm mt-0.5">Pay ₹{{ number_format($tokenAmt) }}</div>
                  <div class="text-[10px] text-slate-500 font-medium">Balance ₹{{ number_format($balanceDueOnToken) }} on delivery</div>
                </div>

                <div class="p-3 rounded-xl bg-emerald-50/80 border border-emerald-200">
                  <div class="font-extrabold text-emerald-900 flex items-center gap-1">
                    <i class="fa-solid fa-percent text-emerald-600"></i> Pay Full (Extra {{ $extraPct }}% Off)
                  </div>
                  <div class="text-emerald-700 font-black text-sm mt-0.5">Pay ₹{{ number_format($fullDiscountedPrice) }}</div>
                  <div class="text-[10px] text-emerald-600 font-extrabold">Extra ₹{{ number_format($extraDiscountVal) }} instant discount</div>
                </div>
              </div>
              @endif
            </div>

            {{-- Features checklist --}}
            @if(!empty($features))
            <div class="space-y-3 mb-8">
              <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">
                What's Included in This Package:
              </h4>
              <ul class="space-y-2.5 text-sm text-slate-700">
                @foreach($features as $feat)
                <li class="flex items-start gap-2.5">
                  <i class="fa-solid fa-check text-emerald-500 mt-1 shrink-0"></i>
                  <span class="font-medium">{{ $feat }}</span>
                </li>
                @endforeach
              </ul>
            </div>
            @endif
          </div>

          {{-- Action Buttons --}}
          <div class="space-y-3 pt-6 border-t border-slate-100">
            @if($bookingActive)
            {{-- Dual Action Buttons: Advance Token vs Full Payment --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
              <button type="button"
                      onclick="openCheckoutModal({
                        id: '{{ $p['id'] }}',
                        productId: '{{ $p['product_id'] ?? '' }}',
                        title: '{{ addslashes($p['title']) }}',
                        mrp: {{ $mrp }},
                        offerPrice: {{ $offerPrice }},
                        savings: {{ $savings }},
                        packageSlug: '{{ $p['package_slug'] ?? 'skillvation-lab-stem' }}'
                      }, 'token')"
                      class="w-full py-3.5 px-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs sm:text-sm shadow-md transition-all hover:scale-[1.02] flex items-center justify-center gap-1.5 text-center">
                <i class="fa-solid fa-bookmark"></i>
                <span>Pre-Book (₹{{ number_format($tokenAmt) }})</span>
              </button>

              <button type="button"
                      onclick="openCheckoutModal({
                        id: '{{ $p['id'] }}',
                        productId: '{{ $p['product_id'] ?? '' }}',
                        title: '{{ addslashes($p['title']) }}',
                        mrp: {{ $mrp }},
                        offerPrice: {{ $offerPrice }},
                        savings: {{ $savings }},
                        packageSlug: '{{ $p['package_slug'] ?? 'skillvation-lab-stem' }}'
                      }, 'full')"
                      class="w-full py-3.5 px-3 rounded-xl bg-brand-orange hover:bg-orange-600 text-white font-extrabold text-xs sm:text-sm shadow-md transition-all hover:scale-[1.02] flex items-center justify-center gap-1.5 text-center">
                <i class="fa-solid fa-bolt"></i>
                <span>Pay Full (-{{ $extraPct }}% Extra)</span>
              </button>
            </div>
            @else
            {{-- Standard Buy Button --}}
            <button type="button"
                    onclick="openCheckoutModal({
                      id: '{{ $p['id'] }}',
                      productId: '{{ $p['product_id'] ?? '' }}',
                      title: '{{ addslashes($p['title']) }}',
                      mrp: {{ $mrp }},
                      offerPrice: {{ $offerPrice }},
                      savings: {{ $savings }},
                      packageSlug: '{{ $p['package_slug'] ?? 'skillvation-lab-stem' }}'
                    }, 'full')"
                    class="w-full py-4 rounded-xl bg-brand-orange hover:bg-orange-600 text-white font-extrabold text-base shadow-lg shadow-brand-orange/20 transition-all hover:scale-[1.02] flex items-center justify-center gap-2">
              <i class="fa-solid fa-bolt"></i>
              <span>Claim Offer &amp; Buy Now</span>
            </button>
            @endif

            {{-- Secondary Buttons Row --}}
            <div class="grid grid-cols-2 gap-2">
              {{-- Request Official Proposal --}}
              <button type="button"
                      onclick="openProposalModal('{{ addslashes($p['title']) }}', '{{ $p['package_slug'] ?? 'skillvation-lab-stem' }}', {{ $offerPrice }})"
                      class="w-full py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs sm:text-sm transition-all flex items-center justify-center gap-1.5">
                <i class="fa-solid fa-file-pdf text-red-500"></i>
                <span>Get Proposal</span>
              </button>

              {{-- Customize in Club Shop Cart --}}
              <a href="{{ url('/sso/shop') }}?target={{ urlencode($p['cart_url'] ?? '/skillvation-cart') }}"
                 class="w-full py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs sm:text-sm transition-all flex items-center justify-center gap-1.5">
                <i class="fa-solid fa-sliders text-blue-500"></i>
                <span>Customize</span>
              </a>
            </div>
          </div>

        </div>
      @endforeach
    </div>

    {{-- Terms note --}}
    @if(!empty($offer->terms))
    <div class="mt-12 p-6 rounded-2xl bg-white border border-slate-200 text-xs text-slate-500 leading-relaxed max-w-4xl mx-auto text-center">
      <strong>Terms &amp; Promotional Conditions:</strong> {{ $offer->terms }}
    </div>
    @endif

  </div>
</section>

{{-- 4. WHY CHOOSE SKILLVATION SECTION --}}
<section class="py-20 bg-white border-t border-slate-200">
  <div class="max-w-container-max mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-3xl mx-auto mb-16">
      <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
        Why Top Schools Commission Skillvation Labs
      </h2>
      <p class="text-slate-600 text-base mt-3">
        Everything required to operate an authentic, inspection-ready innovation lab with zero friction.
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <div class="p-8 rounded-3xl bg-slate-50 border border-slate-200">
        <div class="w-14 h-14 rounded-2xl bg-orange-100 text-brand-orange flex items-center justify-center text-2xl mb-6 font-black">
          <i class="fa-solid fa-screwdriver-wrench"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-3">100% Turnkey Hardware</h3>
        <p class="text-slate-600 text-sm leading-relaxed">
          From microcontrollers, IoT boards, and 3D printers to workbenches, storage racks, and safety gear — completely assembled on-site by our technical engineers.
        </p>
      </div>

      <div class="p-8 rounded-3xl bg-slate-50 border border-slate-200">
        <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl mb-6 font-black">
          <i class="fa-solid fa-graduation-cap"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-3">Certified Teacher Training</h3>
        <p class="text-slate-600 text-sm leading-relaxed">
          Multi-day on-campus faculty certification, grade-mapped lesson plans, SOPs, and project assessment rubrics so your teachers lead every class with confidence.
        </p>
      </div>

      <div class="p-8 rounded-3xl bg-slate-50 border border-slate-200">
        <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-2xl mb-6 font-black">
          <i class="fa-solid fa-certificate"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-3">CBSE &amp; NEP 2020 Compliance</h3>
        <p class="text-slate-600 text-sm leading-relaxed">
          Full alignment with CBSE Circular Skill-75/2024 and NCF-SE 3 Forms of Work. Guaranteed readiness for affiliation inspections and regional skill showcases.
        </p>
      </div>
    </div>
  </div>
</section>

{{-- =========================================================================
     4.5. DEDICATED EVENT ENQUIRIES & DIRECT CONSULTATION STRIP
     ========================================================================= --}}
<section class="py-16 bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 text-white border-t border-slate-800">
  <div class="max-w-container-max mx-auto px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto rounded-3xl bg-slate-900/90 border border-slate-700/80 p-8 sm:p-10 shadow-2xl relative overflow-hidden">
      <div class="absolute -top-16 -right-16 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

      <div class="flex flex-col md:flex-row items-center justify-between gap-8 relative z-10">
        <div class="space-y-3 text-center md:text-left">
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-black uppercase tracking-wider">
            <i class="fa-solid fa-headset"></i> Immediate Institutional Support
          </span>
          <h3 class="text-2xl sm:text-3xl font-black text-white leading-tight">
            Have Questions About School Lab Packages or Setup?
          </h3>
          <p class="text-slate-300 text-xs sm:text-sm leading-relaxed max-w-xl">
            Connect directly with our senior curriculum architects via WhatsApp for consultations, request an official institutional proposal, or log in to your school dashboard.
          </p>
        </div>

        <div class="flex flex-col sm:flex-row md:flex-col gap-3 shrink-0 w-full sm:w-auto">
          {{-- WhatsApp CTA --}}
          <a href="{{ $enquiryWhatsAppUrl }}" target="_blank"
             class="px-6 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-sm shadow-xl transition-all flex items-center justify-center gap-2">
            <i class="fa-brands fa-whatsapp text-lg"></i>
            <span>WhatsApp: +91 99456 12226</span>
          </a>

          {{-- Redirect to Login CTA --}}
          <a href="{{ route('login') }}"
             class="px-6 py-3.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white font-extrabold text-sm border border-slate-600 shadow transition-all flex items-center justify-center gap-2">
            <i class="fa-solid fa-arrow-right-to-bracket"></i>
            <span>School Portal Login &rarr;</span>
          </a>
        </div>
      </div>

    </div>
  </div>
</section>

{{-- =========================================================================
     5. CHECKOUT & PAYMENT MODAL (DIRECT PROCUREMENT FLOW)
     ========================================================================= --}}
<div id="checkoutModal" class="fixed inset-0 z-[99999] hidden checkout-modal-backdrop overflow-y-auto p-2 sm:p-4 flex items-center justify-center">
  <div class="bg-white rounded-2xl sm:rounded-3xl max-w-2xl sm:max-w-3xl w-full shadow-2xl border border-slate-200 overflow-hidden relative my-auto max-h-[96vh] flex flex-col animate-in fade-in zoom-in-95 duration-200">
    
    {{-- Modal Header (Pinned, Sleek & Compact) --}}
    <div class="bg-slate-900 text-white px-5 py-3 sm:px-6 flex justify-between items-center border-b border-slate-800 shrink-0">
      <div class="flex items-center gap-2.5 min-w-0">
        <span class="inline-block px-2 py-0.5 rounded bg-brand-orange text-white text-[10px] font-black uppercase tracking-wider shrink-0">
          SPECIAL PROMOTION
        </span>
        <h3 class="text-sm sm:text-base font-black text-white truncate" id="modalProductTitle">
          Order Summary
        </h3>
      </div>
      <button type="button" onclick="closeCheckoutModal()" class="text-slate-400 hover:text-white text-xl p-1 rounded-lg hover:bg-slate-800 transition-all shrink-0 ml-2" title="Close">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    {{-- Modal Step Indicator (Pinned, Sleek & Compact) --}}
    <div class="px-5 sm:px-6 py-2 bg-slate-100 border-b border-slate-200 flex items-center justify-between text-xs font-bold shrink-0">
      <div class="flex items-center gap-2 cursor-pointer" onclick="goToCheckoutStep(1)" id="stepIndicator1">
        <span class="w-5 h-5 rounded-full bg-brand-orange text-white flex items-center justify-center text-[10px] font-black" id="stepBadge1">1</span>
        <span class="text-slate-900 font-extrabold text-xs" id="stepText1">Phone Validation</span>
      </div>
      <div class="h-0.5 flex-1 bg-slate-300 mx-3" id="stepConnector"></div>
      <div class="flex items-center gap-2 opacity-50 cursor-pointer" onclick="goToCheckoutStep(2)" id="stepIndicator2">
        <span class="w-5 h-5 rounded-full bg-slate-300 text-slate-700 flex items-center justify-center text-[10px] font-black" id="stepBadge2">2</span>
        <span class="text-slate-500 font-semibold text-xs" id="stepText2">Details &amp; Payment</span>
      </div>
    </div>

    {{-- Modal Body (Compact, Fits Without Scroll) --}}
    <form id="directCheckoutForm" class="p-4 sm:p-5 space-y-3 overflow-y-auto flex-1 custom-modal-scroll">
      
      {{-- Selected Offer Summary Strip (Compact 1-row bar) --}}
      <div class="p-2.5 px-3.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between flex-wrap gap-2 text-xs">
        <div class="flex items-center gap-2 min-w-0">
          <span class="text-[10px] uppercase font-black text-slate-400 tracking-wider">Package:</span>
          <span class="text-xs sm:text-sm font-extrabold text-slate-900 truncate" id="summaryProdName">-</span>
          <span class="text-[11px] text-brand-orange font-bold hidden sm:inline" id="summaryPlanLabel">(Pre-Booking Advance Token)</span>
        </div>
        <div class="flex items-center gap-2 shrink-0">
          <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Due Now:</span>
          <span class="text-base sm:text-lg font-black text-slate-900" id="summaryTotalPayable">₹0</span>
          <span id="summarySavingsBadge" class="text-xs text-emerald-700 font-black"></span>
          <span id="summaryBalanceNotice" class="text-[11px] text-amber-700 font-semibold hidden md:inline"></span>
        </div>
      </div>

      {{-- =============================================================
           STEP 1 BLOCK: PHONE VALIDATION VIA WHATSAPP OTP FIRST
           ============================================================= --}}
      <div id="checkoutStep1Block" class="space-y-3">
        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-2.5">
          <div class="flex items-center justify-between">
            <label class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
              <i class="fa-brands fa-whatsapp text-emerald-600 text-sm"></i>
              <span>Verify WhatsApp Number <span class="text-red-500">*</span></span>
            </label>
            <span class="text-[11px] text-slate-500 font-bold">Step 1 of 2</span>
          </div>
          <p class="text-[11px] text-slate-500 leading-tight">
            We validate your mobile number first via an instant WhatsApp OTP to secure your booking.
          </p>

          {{-- Phone input row --}}
          <div id="phoneInputRow" class="flex items-center gap-2">
            <div class="relative flex-1">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-500 select-none">+91</span>
              <input type="tel" id="chkPhone" maxlength="10" pattern="[0-9]{10}"
                     value="{{ $user->phone ?? '' }}"
                     class="w-full pl-11 pr-3 py-2 rounded-lg border border-slate-300 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-brand-orange focus:border-brand-orange"
                     placeholder="Enter 10-digit mobile number" oninput="onPhoneInputChanged()">
            </div>
            <button type="button" id="btnSendPhoneOtp" onclick="sendWhatsAppOtp()"
                    class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow transition-all flex items-center gap-1.5 shrink-0">
              <i class="fa-brands fa-whatsapp text-sm"></i>
              <span id="btnSendPhoneOtpText">Verify via OTP</span>
            </button>
          </div>

          {{-- OTP input drawer --}}
          <div id="otpInputDrawer" class="hidden p-2.5 rounded-lg bg-amber-50 border border-amber-300 flex items-center gap-2 flex-wrap">
            <input type="text" id="chkOtpCode" maxlength="6" pattern="[0-9]{6}" inputmode="numeric"
                   class="w-32 px-2.5 py-1.5 text-center text-sm font-mono font-black tracking-widest rounded-lg border border-amber-300 bg-white focus:ring-2 focus:ring-brand-orange"
                   placeholder="••••••" onkeyup="if(this.value.length === 6) verifyWhatsAppOtp()">
            <button type="button" id="btnVerifyOtp" onclick="verifyWhatsAppOtp()"
                    class="px-3.5 py-1.5 rounded-lg bg-brand-orange hover:bg-orange-600 text-white font-extrabold text-xs shadow flex items-center gap-1 shrink-0">
              <i class="fa-solid fa-check"></i>
              <span>Verify</span>
            </button>
            <span class="text-[11px] text-slate-600 font-mono ml-auto" id="otpCountdownText"></span>
            <span id="otpStatusMsg" class="text-[11px] text-amber-900 font-semibold"></span>
            <button type="button" id="btnResendOtp" onclick="sendWhatsAppOtp(true)" class="hidden text-brand-orange text-xs font-bold hover:underline">
              Resend Code
            </button>
          </div>

          {{-- Verified state badge --}}
          <div id="phoneVerifiedBox" class="hidden p-2.5 rounded-lg bg-emerald-50 border border-emerald-300 flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs font-black text-emerald-950">
              <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
              <span>WhatsApp Verified: <strong class="font-mono text-emerald-900" id="phoneVerifiedNumberText">+91 9876543210</strong></span>
            </div>
            <button type="button" onclick="editPhoneNumber()" class="text-xs font-bold text-slate-600 hover:text-slate-900 underline">
              Change Number
            </button>
          </div>

          <div id="phoneValidationErr" class="hidden text-xs text-red-600 font-bold mt-1 flex items-center gap-1">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span id="phoneValidationErrText"></span>
          </div>
        </div>

        {{-- Step 1 Actions --}}
        <div class="flex items-center justify-between pt-1">
          <button type="button" onclick="closeCheckoutModal()" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-600 font-bold text-xs hover:bg-slate-100">
            Cancel
          </button>
          <button type="button" onclick="goToCheckoutStep(2)" class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-black text-white font-black text-xs shadow transition-all flex items-center gap-2">
            <span>Proceed to Details &amp; Payment</span>
            <i class="fa-solid fa-arrow-right text-[10px]"></i>
          </button>
        </div>
      </div>

      {{-- =============================================================
           STEP 2 BLOCK: DETAILS & PAYMENT
           ============================================================= --}}
      <div id="checkoutStep2Block" class="hidden space-y-3">

        {{-- Phone Verified Reminder Badge --}}
        <div class="p-2.5 px-3.5 rounded-xl bg-emerald-50 border border-emerald-300 flex items-center justify-between">
          <div class="flex items-center gap-2 text-xs text-emerald-950 font-bold">
            <i class="fa-solid fa-circle-check text-emerald-600"></i>
            <span>Verified WhatsApp Mobile: <strong class="font-mono text-emerald-900" id="step2VerifiedPhoneText">+91 9876543210</strong></span>
          </div>
          <button type="button" onclick="goToCheckoutStep(1)" class="text-xs font-bold text-slate-600 hover:text-slate-900 underline">
            Change
          </button>
        </div>

        {{-- Booking Plan Selection (Pre-Book Token vs Full Payment) --}}
        @if($bookingActive)
        <div class="space-y-1.5">
          <label class="text-[11px] font-black text-slate-800 uppercase tracking-wider flex items-center gap-1">
            <i class="fa-solid fa-hand-holding-dollar text-brand-orange"></i> Choose Booking Plan:
          </label>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5" id="bookingPlanOptions">
            {{-- Option A: Advance Token --}}
            <label class="relative flex items-center gap-2.5 p-2.5 rounded-xl border-2 cursor-pointer transition-all plan-card border-brand-orange bg-orange-50/50 shadow-sm" id="planCardToken">
              <input type="radio" name="booking_plan" id="radioPlanToken" value="token" checked onchange="switchBookingPlan('token')" class="text-brand-orange focus:ring-brand-orange shrink-0">
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-1">
                  <span class="text-xs font-black text-slate-900">Pre-Book Token</span>
                  <span class="text-xs sm:text-sm font-black text-brand-orange">₹{{ number_format($tokenAmt) }}</span>
                </div>
                <div class="text-[11px] text-amber-700 font-bold leading-tight" id="tokenBalanceDueText">Balance due on delivery</div>
              </div>
            </label>

            {{-- Option B: Full Payment with Extra Discount --}}
            <label class="relative flex items-center gap-2.5 p-2.5 rounded-xl border-2 cursor-pointer transition-all plan-card border-slate-200 bg-white hover:bg-slate-50" id="planCardFull">
              <input type="radio" name="booking_plan" id="radioPlanFull" value="full" onchange="switchBookingPlan('full')" class="text-brand-orange focus:ring-brand-orange shrink-0">
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-1">
                  <span class="text-xs font-black text-slate-900">Pay Full Online</span>
                  <span class="text-xs sm:text-sm font-black text-emerald-600" id="fullPayableText">₹0</span>
                </div>
                <div class="text-[11px] text-emerald-700 font-bold leading-tight flex items-center justify-between">
                  <span id="fullExtraSavingsBadge">Save extra {{ $extraPct }}%</span>
                  <span class="text-[10px] bg-emerald-100 text-emerald-800 font-extrabold px-1.5 py-0.2 rounded">Instant OFF</span>
                </div>
              </div>
            </label>
          </div>
        </div>
        @endif

        {{-- School & Contact Details Grid --}}
        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-2.5">
          <div class="flex items-center justify-between text-xs">
            <span class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">School &amp; Delivery Details</span>
            <span class="text-slate-500 text-[10px] font-bold">(* Required fields)</span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            <div>
              <label class="block text-[11px] font-bold text-slate-700 mb-0.5">Contact Person Name <span class="text-red-500">*</span></label>
              <input type="text" id="chkContactName" required class="w-full px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-semibold focus:ring-1 focus:ring-brand-orange" placeholder="Enter contact person name">
            </div>
            <div>
              <label class="block text-[11px] font-bold text-slate-700 mb-0.5">School / Institution Name <span class="text-red-500">*</span></label>
              <input type="text" id="chkSchoolName" required class="w-full px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-semibold focus:ring-1 focus:ring-brand-orange" placeholder="Enter school / institution name">
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            <div>
              <label class="block text-[11px] font-bold text-slate-700 mb-0.5">Official Email <span class="text-slate-400 font-normal">(Optional)</span></label>
              <input type="email" id="chkEmail" value="{{ $user->email ?? '' }}" class="w-full px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-semibold focus:ring-1 focus:ring-brand-orange" placeholder="email@school.edu.in">
            </div>
            <div>
              <label class="block text-[11px] font-bold text-slate-700 mb-0.5">City &amp; State <span class="text-red-500">*</span></label>
              <div class="grid grid-cols-2 gap-1.5">
                <input type="text" id="chkCity" required class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 text-xs font-semibold" placeholder="City *">
                <input type="text" id="chkState" required class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 text-xs font-semibold" placeholder="State *">
              </div>
            </div>
          </div>

          <div class="grid grid-cols-3 gap-2">
            <div class="col-span-2">
              <label class="block text-[11px] font-bold text-slate-700 mb-0.5">Campus Delivery Address <span class="text-slate-400 font-normal">(Optional)</span></label>
              <input type="text" id="chkAddress" class="w-full px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-semibold" placeholder="Campus Street Address / Landmark">
            </div>
            <div>
              <label class="block text-[11px] font-bold text-slate-700 mb-0.5">Pincode <span class="text-slate-400 font-normal">(Optional)</span></label>
              <input type="text" id="chkPincode" maxlength="6" class="w-full px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-semibold" placeholder="560001">
            </div>
          </div>
        </div>

        {{-- Payment Method Selection (2 compact tiles) --}}
        <div class="space-y-1.5">
          <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">Choose Payment Flow:</label>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            <label class="flex items-center gap-2.5 p-2.5 rounded-xl border-2 border-brand-orange bg-orange-50/50 cursor-pointer">
              <input type="radio" name="pay_option" value="razorpay" checked class="text-brand-orange focus:ring-brand-orange shrink-0">
              <div class="min-w-0">
                <div class="text-xs font-extrabold text-slate-900 flex items-center gap-1.5">
                  <i class="fa-solid fa-credit-card text-brand-orange"></i> Online Payment
                </div>
                <div class="text-[10px] text-slate-500 truncate">UPI, Cards, NetBanking (Razorpay)</div>
              </div>
            </label>

            <label class="flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 cursor-pointer">
              <input type="radio" name="pay_option" value="bank_transfer" class="text-brand-orange focus:ring-brand-orange shrink-0">
              <div class="min-w-0">
                <div class="text-xs font-extrabold text-slate-900 flex items-center gap-1.5">
                  <i class="fa-solid fa-building-columns text-slate-700"></i> Bank Transfer / PO
                </div>
                <div class="text-[10px] text-slate-500 truncate">RTGS / NEFT / Institutional Cheque</div>
              </div>
            </label>
          </div>
        </div>

        {{-- Step 2 Submit Actions --}}
        <div class="pt-2 flex items-center justify-between gap-3">
          <button type="button" onclick="goToCheckoutStep(1)" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-600 font-bold text-xs hover:bg-slate-100 flex items-center gap-1">
            <i class="fa-solid fa-arrow-left text-[10px]"></i>
            <span>Back to Phone</span>
          </button>
          <button type="submit" id="btnConfirmPay" class="px-7 py-2.5 rounded-xl bg-brand-orange hover:bg-orange-600 text-white font-black text-sm shadow-lg shadow-brand-orange/25 transition-all flex items-center gap-1.5">
            <i class="fa-solid fa-lock text-xs"></i>
            <span id="btnPayText">Confirm &amp; Proceed to Pay</span>
          </button>
        </div>

      </div>

    </form>
  </div>
</div>

{{-- =========================================================================
     6. REQUEST OFFICIAL PROPOSAL MODAL
     ========================================================================= --}}
<div id="proposalModal" class="fixed inset-0 z-[99999] hidden checkout-modal-backdrop overflow-y-auto p-3 sm:p-6 flex items-start justify-center">
  <div class="bg-white rounded-3xl max-w-xl w-full shadow-2xl border border-slate-200 overflow-hidden relative my-4 sm:my-8 max-h-[calc(100vh-3rem)] sm:max-h-[calc(100vh-4rem)] flex flex-col animate-in fade-in zoom-in-95 duration-200">
    
    <div class="bg-slate-900 text-white p-5 sm:p-6 flex justify-between items-center shrink-0 border-b border-slate-800">
      <div>
        <h3 class="text-xl font-bold text-white">Request Proposal &amp; Quotation</h3>
        <p class="text-xs text-slate-400 mt-0.5" id="propPackageLabel">Standard Skillvation Lab</p>
      </div>
      <button type="button" onclick="closeProposalModal()" class="text-slate-400 hover:text-white text-2xl p-2 rounded-lg hover:bg-slate-800 transition-all">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <form id="proposalForm" onsubmit="submitProposalEnquiry(event)" class="p-5 sm:p-6 space-y-4 overflow-y-auto flex-1 custom-modal-scroll">
      <input type="hidden" id="propSource" value="skillvation-lab-stem">
      <input type="hidden" id="propTitle" value="">

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">School / Institution Name <span class="text-red-500">*</span></label>
        <input type="text" id="propSchool" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-brand-orange" placeholder="e.g. St. Xavier High School">
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Contact Name <span class="text-red-500">*</span></label>
          <input type="text" id="propName" required value="{{ $user->name ?? '' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Designation</label>
          <input type="text" id="propDesignation" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm" placeholder="Principal / Trustee">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Email <span class="text-red-500">*</span></label>
          <input type="email" id="propEmail" required value="{{ $user->email ?? '' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">WhatsApp / Phone <span class="text-red-500">*</span></label>
          <input type="tel" id="propPhone" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm" placeholder="9876543210">
        </div>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">City <span class="text-red-500">*</span></label>
          <input type="text" id="propCity" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">School Board</label>
          <select id="propBoard" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm">
            <option value="CBSE">CBSE</option>
            <option value="ICSE">ICSE</option>
            <option value="State Board">State Board</option>
            <option value="IB / Cambridge">IB / Cambridge</option>
          </select>
        </div>
      </div>

      <div id="proposalSuccessMsg" class="hidden p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold">
        <i class="fa-solid fa-circle-check text-emerald-600 mr-1"></i>
        Proposal PDF has been generated and dispatched to your email and WhatsApp!
      </div>

      <div class="pt-3 border-t flex justify-end gap-3">
        <button type="button" onclick="closeProposalModal()" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-600 text-sm font-bold hover:bg-slate-100">
          Cancel
        </button>
        <button type="submit" id="btnPropSubmit" class="px-6 py-2.5 rounded-xl bg-brand-orange hover:bg-orange-600 text-white font-extrabold text-sm shadow">
          Generate &amp; Send Proposal PDF
        </button>
      </div>
    </form>
  </div>
</div>

{{-- =========================================================================
     7. SUCCESS CONFIRMATION MODAL (Fits on Screen Without Scrolling)
     ========================================================================= --}}
<div id="successModal" class="fixed inset-0 z-[99999] hidden checkout-modal-backdrop overflow-y-auto p-3 sm:p-4 flex items-center justify-center">
  <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200 p-4 sm:p-6 text-center relative my-auto animate-in fade-in zoom-in-95 duration-200 max-h-[95vh] flex flex-col justify-between">
    
    {{-- Header with Checkmark --}}
    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3 shrink-0">
      <div class="flex items-center gap-3 text-left">
        <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl shrink-0">
          <i class="fa-solid fa-circle-check"></i>
        </div>
        <div>
          <h3 class="text-base sm:text-lg font-black text-slate-900 leading-tight">Order Confirmed!</h3>
          <p class="text-slate-500 text-[11px] font-semibold">Thank you for choosing Skillvation. Your lab order is recorded.</p>
        </div>
      </div>
      <button type="button" onclick="closeSuccessModal()" class="text-slate-400 hover:text-slate-700 text-lg p-1 rounded-full hover:bg-slate-100 transition-all shrink-0 ml-2" title="Close">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    {{-- Order Details Summary Card --}}
    <div class="p-3 sm:p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-left mb-3 space-y-1.5 text-xs">
      <div class="flex justify-between text-slate-500 font-semibold">
        <span>Order Number:</span>
        <span class="text-slate-900 font-black font-mono" id="successOrderNum">-</span>
      </div>
      <div class="flex justify-between text-slate-500 font-semibold">
        <span>Payment Status:</span>
        <span class="text-emerald-600 font-black" id="successPayStatus">Paid</span>
      </div>
      <div class="flex justify-between text-slate-500 font-semibold">
        <span>Package:</span>
        <span class="text-slate-900 font-bold truncate max-w-[260px]" id="successPkgName">-</span>
      </div>
      <div class="flex justify-between text-slate-500 font-semibold">
        <span>Booking Plan:</span>
        <span class="text-brand-orange font-black" id="successPlanName">-</span>
      </div>
      <div class="flex justify-between text-slate-500 font-semibold pt-1 border-t border-slate-200/60">
        <span>Amount Paid:</span>
        <span class="text-slate-900 font-black text-sm" id="successAmount">-</span>
      </div>
      <div class="flex justify-between text-slate-500 font-semibold" id="successBalanceRow">
        <span>Balance Due on Delivery:</span>
        <span class="text-amber-700 font-black text-sm" id="successBalanceAmt">₹0</span>
      </div>
    </div>

    {{-- What Happens Next --}}
    <div class="p-2.5 rounded-xl bg-blue-50 border border-blue-200 text-[11px] text-blue-900 text-left mb-3.5 space-y-0.5 leading-snug shrink-0">
      <strong class="font-extrabold flex items-center gap-1 text-xs"><i class="fa-solid fa-calendar-check text-blue-600"></i> What Happens Next:</strong>
      <p>1. Our academic engineering lead will contact the principal/trustee within 24 hours.</p>
      <p>2. Lab space blueprint planning and hardware dispatch scheduling will commence.</p>
      <p>3. Teacher orientation schedule will be mutually confirmed.</p>
    </div>

    {{-- Action Buttons --}}
    <div class="flex items-center gap-2 shrink-0">
      <button type="button" onclick="window.print()" class="px-3.5 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-100 font-bold text-xs inline-flex items-center justify-center gap-1.5 transition-all">
        <i class="fa-solid fa-print"></i>
        <span>Print Receipt</span>
      </button>
      <button type="button" onclick="closeSuccessModal()" class="flex-1 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-100 font-extrabold text-xs transition-all">
        Close
      </button>
      <button type="button" onclick="window.location.reload()" class="flex-1 py-2 rounded-xl bg-slate-900 hover:bg-black text-white font-extrabold text-xs shadow transition-all">
        Return to Offers
      </button>
    </div>
  </div>
</div>

@endif

@endsection

@push('scripts')
{{-- Razorpay Checkout script --}}
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
// ─────────────────────────────────────────────────────────────────
// COUNTDOWN TIMER
// ─────────────────────────────────────────────────────────────────
(function initCountdown() {
  var wrapper = document.getElementById('countdownWrapper');
  if (!wrapper) return;
  var endStr = wrapper.getAttribute('data-end');
  if (!endStr) return;

  var targetTime = new Date(endStr).getTime();
  if (isNaN(targetTime)) return;

  var elDays = document.getElementById('cdDays');
  var elHours = document.getElementById('cdHours');
  var elMinutes = document.getElementById('cdMinutes');
  var elSeconds = document.getElementById('cdSeconds');

  function update() {
    var now = new Date().getTime();
    var diff = targetTime - now;
    if (diff <= 0) {
      if (elDays) elDays.textContent = '00';
      if (elHours) elHours.textContent = '00';
      if (elMinutes) elMinutes.textContent = '00';
      if (elSeconds) elSeconds.textContent = '00';
      return;
    }

    var days = Math.floor(diff / (1000 * 60 * 60 * 24));
    var hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((diff % (1000 * 60)) / 1000);

    if (elDays) elDays.textContent = String(days).padStart(2, '0');
    if (elHours) elHours.textContent = String(hours).padStart(2, '0');
    if (elMinutes) elMinutes.textContent = String(minutes).padStart(2, '0');
    if (elSeconds) elSeconds.textContent = String(seconds).padStart(2, '0');
  }

  update();
  setInterval(update, 1000);
})();

// ─────────────────────────────────────────────────────────────────
// CHECKOUT & PAYMENT MODAL STATE
// ─────────────────────────────────────────────────────────────────
var currentCheckoutItem = null;
var razorpayKey = "{{ $rzpKey }}";

var bookingConfig = {
  enabled: {{ $bookingActive ? 'true' : 'false' }},
  tokenAmount: {{ $tokenAmt }},
  extraDiscountPercent: {{ $extraPct }}
};

var currentBookingPlan = 'token'; // 'token' or 'full'

// WhatsApp OTP verification state
var isPhoneVerified = false;
var verifiedPhone = '';
var otpTimerInterval = null;

function onPhoneInputChanged() {
  var currentVal = document.getElementById('chkPhone').value.trim();
  if (isPhoneVerified && currentVal !== verifiedPhone) {
    isPhoneVerified = false;
    verifiedPhone = '';
    document.getElementById('phoneVerifiedBox').classList.add('hidden');
    document.getElementById('phoneInputRow').classList.remove('hidden');
    document.getElementById('btnSendPhoneOtpText').textContent = 'Verify via OTP';
  }
}

function sendWhatsAppOtp(isResend) {
  var phoneEl = document.getElementById('chkPhone');
  var phoneVal = phoneEl.value.trim().replace(/[^0-9]/g, '');
  var errBox = document.getElementById('phoneValidationErr');
  var errText = document.getElementById('phoneValidationErrText');
  var btn = document.getElementById('btnSendPhoneOtp');
  var btnText = document.getElementById('btnSendPhoneOtpText');
  var drawer = document.getElementById('otpInputDrawer');
  var statusMsg = document.getElementById('otpStatusMsg');
  var resendBtn = document.getElementById('btnResendOtp');
  var countdownEl = document.getElementById('otpCountdownText');

  errBox.classList.add('hidden');

  if (!phoneVal || phoneVal.length !== 10) {
    errText.textContent = 'Please enter a valid 10-digit mobile number.';
    errBox.classList.remove('hidden');
    phoneEl.focus();
    return;
  }

  btn.disabled = true;
  btn.classList.add('opacity-70');
  var origText = btnText.innerHTML;
  btnText.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Sending…';

  fetch('{{ route("phone.otp.send") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({ phone: phoneVal })
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    btn.disabled = false;
    btn.classList.remove('opacity-70');
    btnText.innerHTML = 'Resend OTP';

    if (data.status === 'sent' || data.status === 'success') {
      drawer.classList.remove('hidden');
      var debugMsg = data.debug_otp ? ' <span class="bg-amber-200 text-amber-900 px-1.5 py-0.5 rounded font-mono font-bold text-xs ml-1">(Code: ' + data.debug_otp + ')</span>' : '';
      statusMsg.innerHTML = '<span class="text-emerald-700 font-bold"><i class="fa-brands fa-whatsapp text-emerald-600 mr-1"></i> Code sent to WhatsApp +91 ' + phoneVal + '</span>' + debugMsg;
      
      var otpInput = document.getElementById('chkOtpCode');
      otpInput.value = '';
      otpInput.focus();

      // Start 45s countdown
      if (resendBtn) resendBtn.classList.add('hidden');
      var secondsLeft = 45;
      if (countdownEl) countdownEl.textContent = 'Resend in ' + secondsLeft + 's';
      
      if (otpTimerInterval) clearInterval(otpTimerInterval);
      otpTimerInterval = setInterval(function() {
        secondsLeft--;
        if (secondsLeft <= 0) {
          clearInterval(otpTimerInterval);
          if (countdownEl) countdownEl.textContent = '';
          if (resendBtn) resendBtn.classList.remove('hidden');
        } else {
          if (countdownEl) countdownEl.textContent = 'Resend in ' + secondsLeft + 's';
        }
      }, 1000);

    } else {
      errText.textContent = data.message || 'Could not send WhatsApp OTP. Please check the number.';
      errBox.classList.remove('hidden');
      btnText.innerHTML = origText;
    }
  })
  .catch(function(err) {
    btn.disabled = false;
    btn.classList.remove('opacity-70');
    btnText.innerHTML = origText;
    errText.textContent = 'Could not deliver WhatsApp OTP. Please check connection and try again.';
    errBox.classList.remove('hidden');
  });
}

function verifyWhatsAppOtp() {
  var phoneVal = document.getElementById('chkPhone').value.trim().replace(/[^0-9]/g, '');
  var otpVal = document.getElementById('chkOtpCode').value.trim();
  var errBox = document.getElementById('phoneValidationErr');
  var errText = document.getElementById('phoneValidationErrText');
  var statusMsg = document.getElementById('otpStatusMsg');
  var btn = document.getElementById('btnVerifyOtp');

  errBox.classList.add('hidden');

  if (!otpVal || otpVal.length !== 6) {
    statusMsg.innerHTML = '<span class="text-red-600 font-bold">Please enter all 6 digits of the OTP.</span>';
    return;
  }

  var origHtml = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

  fetch('{{ route("phone.otp.verify") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({ phone: phoneVal, otp: otpVal })
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    btn.disabled = false;
    btn.innerHTML = origHtml;

    if (data.status === 'verified' || data.status === 'success') {
      isPhoneVerified = true;
      verifiedPhone = phoneVal;

      // Update UI to verified state
      document.getElementById('otpInputDrawer').classList.add('hidden');
      document.getElementById('phoneInputRow').classList.add('hidden');
      document.getElementById('phoneVerifiedBox').classList.remove('hidden');
      document.getElementById('phoneVerifiedNumberText').textContent = '+91 ' + phoneVal;
      if (otpTimerInterval) clearInterval(otpTimerInterval);

      // Auto-advance to Step 2
      setTimeout(function() {
        goToCheckoutStep(2);
      }, 350);

    } else {
      statusMsg.innerHTML = '<span class="text-red-600 font-bold"><i class="fa-solid fa-circle-exclamation mr-1"></i> ' + (data.message || 'Incorrect OTP code. Please try again.') + '</span>';
      document.getElementById('chkOtpCode').focus();
    }
  })
  .catch(function(err) {
    btn.disabled = false;
    btn.innerHTML = origHtml;
    statusMsg.innerHTML = '<span class="text-red-600 font-bold">Verification failed. Please try again.</span>';
  });
}

// ─────────────────────────────────────────────────────────────────
// CHECKOUT MULTI-STEP NAVIGATION (STEP 1: PHONE OTP -> STEP 2: DETAILS)
// ─────────────────────────────────────────────────────────────────
function goToCheckoutStep(step) {
  var chkForm = document.getElementById('directCheckoutForm');
  if (chkForm) chkForm.scrollTop = 0;

  var step1Block = document.getElementById('checkoutStep1Block');
  var step2Block = document.getElementById('checkoutStep2Block');
  var ind1 = document.getElementById('stepIndicator1');
  var ind2 = document.getElementById('stepIndicator2');
  var badge1 = document.getElementById('stepBadge1');
  var badge2 = document.getElementById('stepBadge2');
  var text1 = document.getElementById('stepText1');
  var text2 = document.getElementById('stepText2');
  var conn = document.getElementById('stepConnector');

  if (step === 2) {
    var phoneVal = document.getElementById('chkPhone').value.trim().replace(/[^0-9]/g, '');
    if (!isPhoneVerified || verifiedPhone !== phoneVal) {
      var errBox = document.getElementById('phoneValidationErr');
      var errText = document.getElementById('phoneValidationErrText');
      if (errText) errText.textContent = 'Please verify your WhatsApp mobile number with OTP first.';
      if (errBox) errBox.classList.remove('hidden');

      var phoneRow = document.getElementById('phoneInputRow');
      if (phoneRow) {
        phoneRow.classList.add('shake-error');
        setTimeout(function() { phoneRow.classList.remove('shake-error'); }, 500);
      }

      if (phoneVal.length === 10 && document.getElementById('otpInputDrawer').classList.contains('hidden')) {
        sendWhatsAppOtp();
      }
      return false;
    }

    // Step 2 is active
    if (step1Block) step1Block.classList.add('hidden');
    if (step2Block) step2Block.classList.remove('hidden');

    // Update Step 1 Indicator (Completed)
    if (ind1) ind1.classList.remove('opacity-50');
    if (badge1) {
      badge1.className = 'w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-black';
      badge1.innerHTML = '<i class="fa-solid fa-check text-[10px]"></i>';
    }
    if (text1) {
      text1.className = 'text-emerald-700 font-bold';
      text1.textContent = 'Phone Verified';
    }

    // Update Step 2 Indicator (Active)
    if (ind2) ind2.classList.remove('opacity-50');
    if (badge2) {
      badge2.className = 'w-6 h-6 rounded-full bg-brand-orange text-white flex items-center justify-center text-xs font-black';
      badge2.textContent = '2';
    }
    if (text2) {
      text2.className = 'text-slate-900 font-extrabold';
    }
    if (conn) conn.className = 'h-0.5 flex-1 bg-emerald-500 mx-3';

    var step2PhoneBadge = document.getElementById('step2VerifiedPhoneText');
    if (step2PhoneBadge) step2PhoneBadge.textContent = '+91 ' + verifiedPhone;

  } else {
    // Step 1 is active
    if (step1Block) step1Block.classList.remove('hidden');
    if (step2Block) step2Block.classList.add('hidden');

    if (ind1) ind1.classList.remove('opacity-50');
    if (badge1) {
      if (isPhoneVerified) {
        badge1.className = 'w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-black';
        badge1.innerHTML = '<i class="fa-solid fa-check text-[10px]"></i>';
        if (text1) {
          text1.className = 'text-emerald-700 font-bold';
          text1.textContent = 'Phone Verified';
        }
      } else {
        badge1.className = 'w-6 h-6 rounded-full bg-brand-orange text-white flex items-center justify-center text-xs font-black';
        badge1.textContent = '1';
        if (text1) {
          text1.className = 'text-slate-900 font-extrabold';
          text1.textContent = 'Phone Validation';
        }
      }
    }

    if (ind2) ind2.classList.add('opacity-50');
    if (badge2) {
      badge2.className = 'w-6 h-6 rounded-full bg-slate-300 text-slate-700 flex items-center justify-center text-xs font-black';
      badge2.textContent = '2';
    }
    if (text2) text2.className = 'text-slate-500 font-semibold';
    if (conn) conn.className = 'h-0.5 flex-1 bg-slate-200 mx-3';
  }
}

function editPhoneNumber() {
  isPhoneVerified = false;
  verifiedPhone = '';
  document.getElementById('phoneVerifiedBox').classList.add('hidden');
  document.getElementById('phoneInputRow').classList.remove('hidden');
  document.getElementById('btnSendPhoneOtpText').textContent = 'Verify via OTP';
  goToCheckoutStep(1);
  document.getElementById('chkPhone').focus();
}

// ─────────────────────────────────────────────────────────────────
// VIP PASS & REGISTRATION (PHONE + WHATSAPP OTP FIRST)
// ─────────────────────────────────────────────────────────────────
var vipOtpTimerInterval = null;

function sendVipWhatsAppOtp(isResend) {
  var phoneEl = document.getElementById('vipPhoneInput');
  var phoneVal = phoneEl.value.trim().replace(/[^0-9]/g, '');
  var errBox = document.getElementById('vipPhoneErr');
  var errText = document.getElementById('vipPhoneErrText');
  var btn = document.getElementById('btnVipSendOtp');
  var btnText = document.getElementById('btnVipSendOtpText');
  var drawer = document.getElementById('vipOtpDrawer');
  var statusMsg = document.getElementById('vipOtpStatusMsg');
  var resendBtn = document.getElementById('btnVipResend');
  var countdownEl = document.getElementById('vipOtpCountdown');

  errBox.classList.add('hidden');

  if (!phoneVal || phoneVal.length !== 10) {
    errText.textContent = 'Please enter a valid 10-digit mobile number.';
    errBox.classList.remove('hidden');
    phoneEl.focus();
    return;
  }

  btn.disabled = true;
  btn.classList.add('opacity-70');
  var origText = btnText.innerHTML;
  btnText.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Sending…';

  fetch('{{ route("phone.otp.send") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({ phone: phoneVal })
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    btn.disabled = false;
    btn.classList.remove('opacity-70');
    btnText.innerHTML = 'Resend OTP';

    if (data.status === 'sent' || data.status === 'success') {
      drawer.classList.remove('hidden');
      var debugMsg = data.debug_otp ? ' <span class="bg-amber-300 text-amber-950 px-1.5 py-0.5 rounded font-mono font-bold text-xs ml-1">(Code: ' + data.debug_otp + ')</span>' : '';
      statusMsg.innerHTML = '<span class="text-emerald-300 font-bold"><i class="fa-brands fa-whatsapp text-emerald-400 mr-1"></i> Code sent to WhatsApp +91 ' + phoneVal + '</span>' + debugMsg;
      
      var otpInput = document.getElementById('vipOtpCode');
      otpInput.value = '';
      otpInput.focus();

      // Start 45s countdown
      if (resendBtn) resendBtn.classList.add('hidden');
      var secondsLeft = 45;
      if (countdownEl) countdownEl.textContent = 'Resend in ' + secondsLeft + 's';
      
      if (vipOtpTimerInterval) clearInterval(vipOtpTimerInterval);
      vipOtpTimerInterval = setInterval(function() {
        secondsLeft--;
        if (secondsLeft <= 0) {
          clearInterval(vipOtpTimerInterval);
          if (countdownEl) countdownEl.textContent = '';
          if (resendBtn) resendBtn.classList.remove('hidden');
        } else {
          if (countdownEl) countdownEl.textContent = 'Resend in ' + secondsLeft + 's';
        }
      }, 1000);

    } else {
      errText.textContent = data.message || 'Could not send WhatsApp OTP. Please check the number.';
      errBox.classList.remove('hidden');
      btnText.innerHTML = origText;
    }
  })
  .catch(function(err) {
    btn.disabled = false;
    btn.classList.remove('opacity-70');
    btnText.innerHTML = origText;
    errText.textContent = 'Could not deliver WhatsApp OTP. Please check connection and try again.';
    errBox.classList.remove('hidden');
  });
}

function verifyVipWhatsAppOtp() {
  var phoneVal = document.getElementById('vipPhoneInput').value.trim().replace(/[^0-9]/g, '');
  var otpVal = document.getElementById('vipOtpCode').value.trim();
  var statusMsg = document.getElementById('vipOtpStatusMsg');
  var btn = document.getElementById('btnVipVerifyOtp');

  if (!otpVal || otpVal.length !== 6) {
    statusMsg.innerHTML = '<span class="text-red-400 font-bold">Please enter all 6 digits of the OTP.</span>';
    return;
  }

  var origHtml = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

  fetch('{{ route("phone.otp.verify") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({ phone: phoneVal, otp: otpVal })
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    btn.disabled = false;
    btn.innerHTML = origHtml;

    if (data.status === 'verified' || data.status === 'success') {
      isPhoneVerified = true;
      verifiedPhone = phoneVal;

      if (vipOtpTimerInterval) clearInterval(vipOtpTimerInterval);

      // Transition to Stage B: Profile & Pass Form
      document.getElementById('vipOtpContainer').classList.add('hidden');
      document.getElementById('vipProfileBlock').classList.remove('hidden');
      document.getElementById('vipVerifiedPhoneBadge').textContent = '+91 ' + phoneVal;

      // Also pre-fill checkout modal
      var chkPhone = document.getElementById('chkPhone');
      if (chkPhone) chkPhone.value = phoneVal;

    } else {
      statusMsg.innerHTML = '<span class="text-red-400 font-bold"><i class="fa-solid fa-circle-exclamation mr-1"></i> ' + (data.message || 'Incorrect OTP code. Please try again.') + '</span>';
      document.getElementById('vipOtpCode').focus();
    }
  })
  .catch(function(err) {
    btn.disabled = false;
    btn.innerHTML = origHtml;
    statusMsg.innerHTML = '<span class="text-red-400 font-bold">Verification failed. Please try again.</span>';
  });
}

function editVipPhone() {
  document.getElementById('vipProfileBlock').classList.add('hidden');
  document.getElementById('vipOtpContainer').classList.remove('hidden');
  document.getElementById('vipOtpDrawer').classList.add('hidden');
  document.getElementById('vipPhoneInput').focus();
}

function generateVipPass(e) {
  e.preventDefault();
  var fullName = document.getElementById('vipFullName').value.trim();
  var schoolName = document.getElementById('vipSchoolName').value.trim();
  var designation = document.getElementById('vipDesignation').value;
  var city = document.getElementById('vipCity').value.trim();
  var phone = verifiedPhone || document.getElementById('vipPhoneInput').value.trim().replace(/[^0-9]/g, '');

  if (!fullName) {
    alert('Please enter your full name.');
    document.getElementById('vipFullName').focus();
    return false;
  }
  if (!schoolName) {
    alert('Please enter your school or institution name.');
    document.getElementById('vipSchoolName').focus();
    return false;
  }
  if (!city) {
    alert('Please enter your city.');
    document.getElementById('vipCity').focus();
    return false;
  }

  var btn = document.getElementById('btnGeneratePass');
  var origHtml = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Activating Discount Privileges…';

  var passRef = 'SKV-LAB-' + Math.floor(1000 + Math.random() * 9000);

  // Save to localStorage for automatic checkout prefill
  try {
    localStorage.setItem('skillbox_delivery_address', JSON.stringify({
      fullName: fullName,
      phone: phone,
      city: city || 'Bengaluru',
      state: 'Karnataka',
      pincode: '560001',
      street: schoolName,
      schoolName: schoolName
    }));
  } catch(e) {}

  // Dispatch background enquiry lead to backend
  var fd = new FormData();
  fd.append('name', fullName);
  fd.append('school', schoolName);
  fd.append('email', fullName.toLowerCase().replace(/[^a-z0-9]/g, '') + '@skillvation.com');
  fd.append('phone', phone);
  fd.append('designation', designation);
  fd.append('city', city);
  fd.append('source', 'SPECIAL_LAB_OFFER_10PCT');
  fd.append('course_title', 'Special School Lab Offer & 10% Discount Registration');
  fd.append('message', 'Special school lab 10% extra discount and pre-booking activated. Phone verified via WhatsApp.');

  fetch('{{ route("course.enquiry.store") }}', {
    method: 'POST',
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: fd
  }).catch(function() {});

  setTimeout(function() {
    btn.disabled = false;
    btn.innerHTML = origHtml;

    document.getElementById('vipProfileBlock').classList.add('hidden');
    document.getElementById('vipPassCard').classList.remove('hidden');

    document.getElementById('passHolderName').textContent = fullName + (designation ? ' (' + designation + ')' : '');
    document.getElementById('passSchoolName').textContent = schoolName + (city ? ', ' + city : '');
    var passIdCodeEl = document.getElementById('passIdCode');
    if (passIdCodeEl) passIdCodeEl.textContent = passRef;
    var passPhoneEl = document.getElementById('passVerifiedPhoneText');
    if (passPhoneEl) passPhoneEl.textContent = '+91 ' + phone;

    // Pre-fill checkout fields if user decides to book below
    var chkContact = document.getElementById('chkContactName');
    if (chkContact && fullName) chkContact.value = fullName;
    var chkSchool = document.getElementById('chkSchoolName');
    if (chkSchool && schoolName) chkSchool.value = schoolName;
    var chkCity = document.getElementById('chkCity');
    if (chkCity && city) chkCity.value = city;
  }, 400);
}

// ─────────────────────────────────────────────────────────────────
// CHECKOUT MODAL OPEN / CLOSE & PLAN SWITCHING
// ─────────────────────────────────────────────────────────────────
function openCheckoutModal(item, defaultPlan) {
  currentCheckoutItem = item;

  var titleEl = document.getElementById('modalProductTitle');
  if (titleEl) titleEl.textContent = item.title;
  var sumProd = document.getElementById('summaryProdName');
  if (sumProd) sumProd.textContent = item.title;

  var offerPrice = Number(item.offerPrice);
  var extraDiscount = bookingConfig.enabled ? Math.round((offerPrice * bookingConfig.extraDiscountPercent) / 100) : 0;
  var fullPayable = Math.max(0, offerPrice - extraDiscount);

  var fullPayTextEl = document.getElementById('fullPayableText');
  if (fullPayTextEl) fullPayTextEl.textContent = '₹ ' + fullPayable.toLocaleString('en-IN');
  var fullSavingsEl = document.getElementById('fullExtraSavingsBadge');
  if (fullSavingsEl) fullSavingsEl.textContent = 'Save extra ₹ ' + extraDiscount.toLocaleString('en-IN');

  // Pre-fill saved address if available
  try {
    var saved = JSON.parse(localStorage.getItem('skillbox_delivery_address') || '{}');
    if (saved.fullName) document.getElementById('chkContactName').value = saved.fullName;
    if (saved.phone) document.getElementById('chkPhone').value = saved.phone;
    if (saved.city) document.getElementById('chkCity').value = saved.city;
    if (saved.state) document.getElementById('chkState').value = saved.state;
    if (saved.pincode) document.getElementById('chkPincode').value = saved.pincode;
    if (saved.street) document.getElementById('chkAddress').value = saved.street;
    if (saved.schoolName) document.getElementById('chkSchoolName').value = saved.schoolName;
  } catch(e) {}

  // Check if phone was already verified via VIP pass registration or earlier OTP
  if (isPhoneVerified && verifiedPhone) {
    document.getElementById('chkPhone').value = verifiedPhone;
    var phoneBox = document.getElementById('phoneVerifiedBox');
    if (phoneBox) {
      phoneBox.classList.remove('hidden');
      document.getElementById('phoneVerifiedNumberText').textContent = '+91 ' + verifiedPhone;
    }
    var phoneRow = document.getElementById('phoneInputRow');
    if (phoneRow) phoneRow.classList.add('hidden');
    var otpDrawer = document.getElementById('otpInputDrawer');
    if (otpDrawer) otpDrawer.classList.add('hidden');
    goToCheckoutStep(2);
  } else {
    isPhoneVerified = false;
    verifiedPhone = '';
    var phoneBox = document.getElementById('phoneVerifiedBox');
    if (phoneBox) phoneBox.classList.add('hidden');
    var phoneRow = document.getElementById('phoneInputRow');
    if (phoneRow) phoneRow.classList.remove('hidden');
    var otpDrawer = document.getElementById('otpInputDrawer');
    if (otpDrawer) otpDrawer.classList.add('hidden');
    var errBox = document.getElementById('phoneValidationErr');
    if (errBox) errBox.classList.add('hidden');
    var btnSend = document.getElementById('btnSendPhoneOtpText');
    if (btnSend) btnSend.textContent = 'Verify via OTP';
    goToCheckoutStep(1);
  }

  // Set booking plan
  if (bookingConfig.enabled) {
    switchBookingPlan(defaultPlan || 'token');
  } else {
    currentBookingPlan = 'full';
    updatePricingSummary();
  }

  // Prevent background scrolling completely
  document.documentElement.classList.add('modal-open');
  document.body.classList.add('modal-open');

  var chkModal = document.getElementById('checkoutModal');
  var chkForm = document.getElementById('directCheckoutForm');
  if (chkModal) chkModal.scrollTop = 0;
  if (chkForm) chkForm.scrollTop = 0;

  document.getElementById('checkoutModal').classList.remove('hidden');
}

function closeCheckoutModal() {
  document.getElementById('checkoutModal').classList.add('hidden');
  document.documentElement.classList.remove('modal-open');
  document.body.classList.remove('modal-open');
}

function switchBookingPlan(plan) {
  currentBookingPlan = plan;

  var cardToken = document.getElementById('planCardToken');
  var cardFull  = document.getElementById('planCardFull');
  var rToken    = document.getElementById('radioPlanToken');
  var rFull     = document.getElementById('radioPlanFull');

  if (plan === 'token') {
    if (rToken) rToken.checked = true;
    if (cardToken) {
      cardToken.classList.add('border-brand-orange', 'bg-orange-50/50', 'shadow-sm');
      cardToken.classList.remove('border-slate-200', 'bg-white');
    }
    if (cardFull) {
      cardFull.classList.remove('border-brand-orange', 'bg-orange-50/50', 'shadow-sm');
      cardFull.classList.add('border-slate-200', 'bg-white');
    }
  } else {
    if (rFull) rFull.checked = true;
    if (cardFull) {
      cardFull.classList.add('border-brand-orange', 'bg-orange-50/50', 'shadow-sm');
      cardFull.classList.remove('border-slate-200', 'bg-white');
    }
    if (cardToken) {
      cardToken.classList.remove('border-brand-orange', 'bg-orange-50/50', 'shadow-sm');
      cardToken.classList.add('border-slate-200', 'bg-white');
    }
  }

  updatePricingSummary();
}

function updatePricingSummary() {
  if (!currentCheckoutItem) return;

  var offerPrice = Number(currentCheckoutItem.offerPrice);
  var planLabel = document.getElementById('summaryPlanLabel');
  var totalPayableEl = document.getElementById('summaryTotalPayable');
  var savingsBadgeEl = document.getElementById('summarySavingsBadge');
  var balanceNoticeEl = document.getElementById('summaryBalanceNotice');
  var btnPayText = document.getElementById('btnPayText');

  if (bookingConfig.enabled && currentBookingPlan === 'token') {
    var payable = bookingConfig.tokenAmount;
    var balanceDue = Math.max(0, offerPrice - payable);

    if (planLabel) planLabel.textContent = '🔒 Pre-Booking Advance Token Plan';
    if (totalPayableEl) totalPayableEl.textContent = '₹ ' + payable.toLocaleString('en-IN');
    if (savingsBadgeEl) savingsBadgeEl.textContent = 'Offer Price: ₹ ' + offerPrice.toLocaleString('en-IN') + ' (Locked)';
    if (balanceNoticeEl) balanceNoticeEl.innerHTML = '<span class="text-amber-700 font-bold">Remaining balance due on delivery: ₹ ' + balanceDue.toLocaleString('en-IN') + '</span>';
    if (btnPayText) btnPayText.textContent = 'Confirm & Pre-Book for ₹ ' + payable.toLocaleString('en-IN');
  } else {
    var extraDiscount = bookingConfig.enabled ? Math.round((offerPrice * bookingConfig.extraDiscountPercent) / 100) : 0;
    var finalAmount = Math.max(0, offerPrice - extraDiscount);

    if (planLabel) planLabel.textContent = '⚡ Full Upfront Payment Plan';
    if (totalPayableEl) totalPayableEl.textContent = '₹ ' + finalAmount.toLocaleString('en-IN');
    if (savingsBadgeEl) {
      if (extraDiscount > 0) {
        savingsBadgeEl.textContent = 'Extra ' + bookingConfig.extraDiscountPercent + '% Instant Discount Applied (Saved ₹ ' + extraDiscount.toLocaleString('en-IN') + ')';
      } else {
        savingsBadgeEl.textContent = 'Festival Promotional Price';
      }
    }
    if (balanceNoticeEl) balanceNoticeEl.innerHTML = '<span class="text-emerald-700 font-bold">✓ 100% Paid in Full — Zero balance on delivery</span>';
    if (btnPayText) btnPayText.textContent = 'Confirm & Pay ₹ ' + finalAmount.toLocaleString('en-IN');
  }
}

// ─────────────────────────────────────────────────────────────────
// HANDLE DIRECT CHECKOUT SUBMISSION
// ─────────────────────────────────────────────────────────────────
document.getElementById('directCheckoutForm')?.addEventListener('submit', function(e) {
  e.preventDefault();
  if (!currentCheckoutItem) return;

  var schoolName  = document.getElementById('chkSchoolName').value.trim();
  var contactName = document.getElementById('chkContactName').value.trim();
  var email       = document.getElementById('chkEmail').value.trim();
  var phone       = document.getElementById('chkPhone').value.trim().replace(/[^0-9]/g, '');
  var address     = document.getElementById('chkAddress').value.trim();
  var city        = document.getElementById('chkCity').value.trim();
  var state       = document.getElementById('chkState').value.trim();
  var pincode     = document.getElementById('chkPincode').value.trim();
  var payOption   = document.querySelector('input[name="pay_option"]:checked')?.value || 'razorpay';

  // MANDATORY WHATSAPP OTP VERIFICATION CHECK
  if (!isPhoneVerified || verifiedPhone !== phone) {
    goToCheckoutStep(1);
    var errBox = document.getElementById('phoneValidationErr');
    var errText = document.getElementById('phoneValidationErrText');
    if (errText) errText.textContent = 'Please verify your WhatsApp mobile number with the OTP code before proceeding.';
    if (errBox) errBox.classList.remove('hidden');

    var phoneRow = document.getElementById('phoneInputRow');
    if (phoneRow) {
      phoneRow.classList.add('shake-error');
      setTimeout(function() { phoneRow.classList.remove('shake-error'); }, 600);
    }

    if (document.getElementById('otpInputDrawer').classList.contains('hidden')) {
      sendWhatsAppOtp();
    }
    return;
  }

  // Mandatory fields validation for Step 2
  if (!contactName) {
    alert('Please enter the contact person name.');
    document.getElementById('chkContactName')?.focus();
    return;
  }
  if (!schoolName) {
    alert('Please enter the school or institution name.');
    document.getElementById('chkSchoolName')?.focus();
    return;
  }
  if (!city) {
    alert('Please enter the city.');
    document.getElementById('chkCity')?.focus();
    return;
  }
  if (!state) {
    alert('Please enter the state.');
    document.getElementById('chkState')?.focus();
    return;
  }

  var safeContact = contactName;
  var safeSchool  = schoolName;
  var safeEmail   = email || ('booking.' + phone + '@skillvation.com');
  var safeCity    = city;
  var safeState   = state;
  var safePincode = pincode || '560001';
  var safeAddress = address ? (safeSchool + ', ' + address) : (safeSchool + ', Campus address to be confirmed on coordinator call');

  // Save address for convenience
  try {
    localStorage.setItem('skillbox_delivery_address', JSON.stringify({
      fullName: safeContact, phone: phone, city: safeCity, state: safeState, pincode: safePincode, street: safeAddress, schoolName: safeSchool
    }));
  } catch(e) {}

  var offerPrice = Number(currentCheckoutItem.offerPrice);
  var totalRupees = 0;
  var discountApplied = 0;
  var balanceDue = 0;
  var planTitle = '';
  var itemTitle = currentCheckoutItem.title;

  if (bookingConfig.enabled && currentBookingPlan === 'token') {
    totalRupees = bookingConfig.tokenAmount;
    balanceDue = Math.max(0, offerPrice - totalRupees);
    planTitle = 'Pre-Booking Advance Token (₹' + totalRupees.toLocaleString('en-IN') + ')';
    itemTitle += ' [Pre-Booking Advance Token: ₹' + totalRupees.toLocaleString('en-IN') + ' | Balance Due: ₹' + balanceDue.toLocaleString('en-IN') + ']';
  } else {
    discountApplied = bookingConfig.enabled ? Math.round((offerPrice * bookingConfig.extraDiscountPercent) / 100) : 0;
    totalRupees = Math.max(0, offerPrice - discountApplied);
    planTitle = 'Full Payment' + (discountApplied > 0 ? ' (Extra ' + bookingConfig.extraDiscountPercent + '% Discount Applied)' : '');
    itemTitle += discountApplied > 0 ? ' [Full Payment - Extra ' + bookingConfig.extraDiscountPercent + '% Booking Discount Applied: ₹' + totalRupees.toLocaleString('en-IN') + ']' : '';
  }

  var btn = document.getElementById('btnConfirmPay');
  var origBtnText = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing…';

  var orderPayload = {
    customer_name: safeContact + (schoolName ? (' (' + schoolName + ')') : ''),
    customer_email: safeEmail,
    customer_phone: phone,
    pincode: safePincode,
    city: safeCity,
    state: safeState,
    country: 'India',
    address: safeAddress,
    address_type: 'School / Institution',
    total_amount: totalRupees,
    discount: discountApplied,
    admin_notes: 'Booking Plan: ' + planTitle + ' | Product: ' + currentCheckoutItem.title + ' | Base Offer: ₹' + offerPrice.toLocaleString('en-IN') + (currentBookingPlan === 'token' ? ' | Advance Token Paid: ₹' + totalRupees.toLocaleString('en-IN') + ' | Balance Due: ₹' + balanceDue.toLocaleString('en-IN') : ' | Extra Discount: ₹' + discountApplied.toLocaleString('en-IN')),
    items: [{
      product_id: currentCheckoutItem.productId || null,
      title: itemTitle,
      unit_price: totalRupees,
      quantity: 1
    }]
  };

  if (payOption === 'bank_transfer') {
    // Record as bank transfer order
    orderPayload.payment_method = 'bank_transfer';
    orderPayload.payment_status = 'pending';

    fetch('{{ route("api.shop-orders.store") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify(orderPayload)
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
      btn.disabled = false;
      btn.innerHTML = origBtnText;
      if (res.status === 'success' || res.order_number) {
        closeCheckoutModal();
        showSuccessModal(res.order_number || (res.data && res.data.order_number) || 'PO-PROMO-' + Date.now(), 'Pending (Bank Transfer)', currentCheckoutItem.title, planTitle, totalRupees, balanceDue);
      } else {
        alert(res.message || 'Could not submit order. Please check details and try again.');
      }
    })
    .catch(function(err) {
      btn.disabled = false;
      btn.innerHTML = origBtnText;
      alert('Could not submit order. Please check connection and try again.');
    });

  } else {
    // Razorpay payment flow
    var rzpAmountPaise = Math.round(totalRupees * 100);

    var options = {
      key: razorpayKey,
      amount: rzpAmountPaise,
      currency: "INR",
      name: "Skillvation",
      description: planTitle + ' - ' + currentCheckoutItem.title,
      image: "{{ asset($setting->logo ?? '') }}",
      handler: function(response) {
        orderPayload.payment_method = 'razorpay';
        orderPayload.payment_id = response.razorpay_payment_id;
        orderPayload.payment_status = 'paid';

        fetch('{{ route("api.shop-orders.store") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify(orderPayload)
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
          btn.disabled = false;
          btn.innerHTML = origBtnText;
          closeCheckoutModal();
          showSuccessModal(res.order_number || (res.data && res.data.order_number) || 'ORD-' + response.razorpay_payment_id, 'Paid via Razorpay (' + response.razorpay_payment_id + ')', currentCheckoutItem.title, planTitle, totalRupees, balanceDue);
        })
        .catch(function(err) {
          closeCheckoutModal();
          showSuccessModal('ORD-' + response.razorpay_payment_id, 'Paid (' + response.razorpay_payment_id + ')', currentCheckoutItem.title, planTitle, totalRupees, balanceDue);
        });
      },
      prefill: {
        name: safeContact,
        email: safeEmail,
        contact: phone
      },
      theme: {
        color: "#f4742b"
      },
      modal: {
        ondismiss: function() {
          btn.disabled = false;
          btn.innerHTML = origBtnText;
        }
      }
    };

    var rzp1 = new Razorpay(options);
    rzp1.open();
  }
});

function showSuccessModal(orderNum, payStatus, pkgName, planName, amount, balanceDue) {
  document.getElementById('successOrderNum').textContent = orderNum;
  document.getElementById('successPayStatus').textContent = payStatus;
  document.getElementById('successPkgName').textContent = pkgName;
  document.getElementById('successPlanName').textContent = planName || 'Direct Booking';
  document.getElementById('successAmount').textContent = '₹ ' + Number(amount).toLocaleString('en-IN');
  
  var balanceRow = document.getElementById('successBalanceRow');
  var balanceAmt = document.getElementById('successBalanceAmt');
  if (balanceDue && balanceDue > 0) {
    if (balanceRow) balanceRow.classList.remove('hidden');
    if (balanceAmt) balanceAmt.textContent = '₹ ' + Number(balanceDue).toLocaleString('en-IN');
  } else {
    if (balanceRow) balanceRow.classList.add('hidden');
  }

  document.documentElement.classList.add('modal-open');
  document.body.classList.add('modal-open');
  var successModalEl = document.getElementById('successModal');
  if (successModalEl) {
    successModalEl.scrollTop = 0;
    successModalEl.classList.remove('hidden');
  }
}

function closeSuccessModal() {
  document.getElementById('successModal').classList.add('hidden');
  document.documentElement.classList.remove('modal-open');
  document.body.classList.remove('modal-open');
}

// ─────────────────────────────────────────────────────────────────
// PROPOSAL REQUEST MODAL
// ─────────────────────────────────────────────────────────────────
function openProposalModal(title, source, offerPrice) {
  document.getElementById('propTitle').value = title;
  document.getElementById('propSource').value = source;
  document.getElementById('propPackageLabel').textContent = title + ' (Offer: ₹' + Number(offerPrice).toLocaleString('en-IN') + ')';
  document.getElementById('proposalSuccessMsg').classList.add('hidden');
  document.documentElement.classList.add('modal-open');
  document.body.classList.add('modal-open');
  document.getElementById('proposalModal').classList.remove('hidden');
}

function closeProposalModal() {
  document.getElementById('proposalModal').classList.add('hidden');
  document.documentElement.classList.remove('modal-open');
  document.body.classList.remove('modal-open');
}

function submitProposalEnquiry(e) {
  e.preventDefault();
  var btn = document.getElementById('btnPropSubmit');
  var orig = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Generating Proposal…';

  var fd = new FormData();
  fd.append('name', document.getElementById('propName').value);
  fd.append('school', document.getElementById('propSchool').value);
  fd.append('email', document.getElementById('propEmail').value);
  fd.append('phone', document.getElementById('propPhone').value);
  fd.append('designation', document.getElementById('propDesignation').value);
  fd.append('city', document.getElementById('propCity').value);
  fd.append('board', document.getElementById('propBoard').value);
  fd.append('source', document.getElementById('propSource').value);
  fd.append('course_title', document.getElementById('propTitle').value);
  fd.append('message', 'Request Detailed Proposal — Lab Festival Offer');

  fetch('{{ route("course.enquiry.store") }}', {
    method: 'POST',
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: fd
  })
  .then(function(r) { return r.json(); })
  .then(function(res) {
    btn.disabled = false;
    btn.innerHTML = orig;
    document.getElementById('proposalSuccessMsg').classList.remove('hidden');
    setTimeout(function() {
      closeProposalModal();
    }, 3000);
  })
  .catch(function(err) {
    btn.disabled = false;
    btn.innerHTML = orig;
    alert('Thank you! Your proposal request has been registered.');
    closeProposalModal();
  });
}
</script>
@endpush

