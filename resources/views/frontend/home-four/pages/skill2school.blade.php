@extends('frontend.home-four.layouts.master')

@section('meta_title', 'Skill 2 Skool — Empowering Preschools Through Structured Skill Education')
@section('meta_description', 'Skill 2 Skool empowers preschools with structured, activity-based skill education. Explore LMS, worksheets, activity kits, and teacher training for early childhood learning.')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>

/* ──────────────────────────────────────────
   PAGE BASE
────────────────────────────────────────── */
.ps-page {
  font-family: 'Plus Jakarta Sans', sans-serif;
  color: #1a1a2e;
  background: #fff;
  --ps-orange: #f97316;
  --ps-red:    #e63946;
  --ps-blue:   #1d6fa4;
  --ps-teal:   #0d9488;
  --ps-yellow: #fbbf24;
  --ps-green:  #16a34a;
  --ps-gray:   #64748b;
  --ps-light:  #f8f9fa;
}

/* ──────────────────────────────────────────
   HERO
────────────────────────────────────────── */
.ps-hero {
  background: linear-gradient(135deg, #fff8f0 0%, #fff3e0 100%);
  padding: 64px 0 0;
  overflow: hidden;
  position: relative;
}
.ps-hero::before {
  content: '';
  position: absolute;
  top: -60px; right: -60px;
  width: 280px; height: 280px;
  background: radial-gradient(circle, rgba(249,115,22,.12) 0%, transparent 70%);
  border-radius: 50%;
  pointer-events: none;
}
.ps-hero::after {
  content: '';
  position: absolute;
  bottom: 0; left: -40px;
  width: 200px; height: 200px;
  background: radial-gradient(circle, rgba(29,111,164,.1) 0%, transparent 70%);
  border-radius: 50%;
  pointer-events: none;
}
.ps-hero__inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 48px;
  align-items: center;
}
.ps-hero__brand {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 20px;
}
.ps-hero__brand img {
  height: 36px;
  width: auto;
}
.ps-hero__tag {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: rgba(249,115,22,.1);
  color: var(--ps-orange);
  border: 1px solid rgba(249,115,22,.25);
  border-radius: 999px;
  padding: 4px 14px;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: .06em;
  text-transform: uppercase;
  margin-bottom: 20px;
}
.ps-hero h1 {
  font-size: clamp(1.9rem, 3.2vw, 2.85rem);
  font-weight: 900;
  line-height: 1.2;
  color: #1a1a2e;
  margin-bottom: 18px;
}
.ps-hero h1 span { color: var(--ps-orange); }
.ps-hero p {
  font-size: 1rem;
  color: var(--ps-gray);
  line-height: 1.8;
  margin-bottom: 28px;
  max-width: 480px;
}
.ps-btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: var(--ps-orange);
  color: #fff;
  padding: 13px 28px;
  border-radius: 999px;
  font-weight: 700;
  font-size: 14px;
  text-decoration: none;
  box-shadow: 0 4px 16px rgba(249,115,22,.35);
  transition: background .2s, transform .2s;
  margin-right: 12px;
}
.ps-btn-primary:hover { background: #ea6c0a; transform: translateY(-2px); color: #fff; }
.ps-btn-outline {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: #fff;
  color: var(--ps-blue);
  border: 2px solid var(--ps-blue);
  padding: 11px 26px;
  border-radius: 999px;
  font-weight: 700;
  font-size: 14px;
  text-decoration: none;
  transition: all .2s;
}
.ps-btn-outline:hover { background: var(--ps-blue); color: #fff; }
.ps-hero__img {
  position: relative;
  z-index: 2;
}
.ps-hero__img img {
  width: 100%;
  height: auto;
  border-radius: 24px 24px 0 0;
  box-shadow: 0 20px 50px rgba(0,0,0,.12);
}
.ps-hero__float-badge {
  position: absolute;
  bottom: 24px;
  left: -20px;
  background: #fff;
  border-radius: 14px;
  padding: 12px 18px;
  box-shadow: 0 8px 24px rgba(0,0,0,.12);
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 13px;
  font-weight: 700;
  color: #1a1a2e;
}
.ps-hero__float-badge .dot {
  width: 10px; height: 10px;
  background: #22c55e;
  border-radius: 50%;
  animation: ps-pulse 2s infinite;
}
@keyframes ps-pulse {
  0%,100% { transform: scale(1); opacity: 1; }
  50%      { transform: scale(1.4); opacity: .7; }
}

/* ──────────────────────────────────────────
   SECTION COMMONS
────────────────────────────────────────── */
.ps-section { padding: 72px 0; }
.ps-section--gray { background: #f8f9fa; }
.ps-container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
.ps-section-head { text-align: center; margin-bottom: 48px; }
.ps-eyebrow {
  display: inline-flex; align-items: center; gap: 6px;
  font-size: 12px; font-weight: 700; letter-spacing: .1em;
  text-transform: uppercase; color: var(--ps-orange); margin-bottom: 12px;
}
.ps-eyebrow::before, .ps-eyebrow::after {
  content: ''; flex: 1 0 28px; height: 2px;
  background: linear-gradient(90deg, transparent, rgba(249,115,22,.4));
}
.ps-eyebrow::before { transform: scaleX(-1); }
.ps-h2 {
  font-size: clamp(1.6rem, 2.8vw, 2.4rem);
  font-weight: 800;
  color: #1a1a2e;
  line-height: 1.25;
  margin-bottom: 14px;
}
.ps-h2 span { color: var(--ps-orange); }
.ps-lead { font-size: 1rem; color: var(--ps-gray); line-height: 1.75; max-width: 600px; margin: 0 auto; }

/* ──────────────────────────────────────────
   WHY MATTERS — 4 icon features
────────────────────────────────────────── */
.ps-features-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 24px;
}
@media(max-width:900px){ .ps-features-grid { grid-template-columns: 1fr 1fr; } }
@media(max-width:540px){ .ps-features-grid { grid-template-columns: 1fr; } }
.ps-feature-card {
  text-align: center;
  padding: 28px 20px;
  border-radius: 18px;
  background: #fff;
  border: 1px solid #f0e8e0;
  transition: transform .25s, box-shadow .25s;
}
.ps-feature-card:hover { transform: translateY(-5px); box-shadow: 0 12px 30px rgba(249,115,22,.1); }
.ps-feature-icon {
  width: 60px; height: 60px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 16px;
  font-size: 24px;
}
.ps-feature-card h4 { font-size: 14px; font-weight: 700; color: #1a1a2e; margin-bottom: 8px; line-height: 1.4; }
.ps-feature-card p  { font-size: 13px; color: var(--ps-gray); line-height: 1.6; margin: 0; }

/* ──────────────────────────────────────────
   WHY PRESCHOOL — image mosaic + feature list
────────────────────────────────────────── */
.ps-why-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 48px;
  align-items: start;
}
@media(max-width:768px){ .ps-why-grid { grid-template-columns: 1fr; } }
.ps-img-mosaic {
  display: grid;
  grid-template-columns: 1fr 1fr;
  grid-template-rows: auto auto;
  gap: 12px;
}
.ps-img-mosaic img {
  width: 100%; height: 180px;
  object-fit: cover; border-radius: 14px;
  transition: transform .35s;
}
.ps-img-mosaic img:hover { transform: scale(1.03); }
.ps-img-mosaic__big {
  grid-column: 1 / -1;
  height: 220px !important;
}
.ps-feature-list { list-style: none; padding: 0; margin: 0; }
.ps-feature-list li {
  display: flex;
  gap: 14px;
  padding: 16px 0;
  border-bottom: 1px solid #f0f0f0;
  align-items: flex-start;
}
.ps-feature-list li:last-child { border-bottom: none; }
.ps-feature-list__icon {
  width: 44px; height: 44px; border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 18px; flex-shrink: 0; margin-top: 2px;
}
.ps-feature-list h5 { font-size: 14px; font-weight: 800; color: #1a1a2e; margin: 0 0 4px; line-height: 1.3; }
.ps-feature-list p  { font-size: 13px; color: var(--ps-gray); margin: 0; line-height: 1.6; }

/* ──────────────────────────────────────────
   LMS + WORKSHEET CARDS
────────────────────────────────────────── */
.ps-product-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 28px;
}
@media(max-width:640px){ .ps-product-grid { grid-template-columns: 1fr; } }
.ps-product-card {
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 8px 30px rgba(0,0,0,.08);
  border: 1px solid #f0e8e0;
  transition: transform .25s, box-shadow .25s;
}
.ps-product-card:hover { transform: translateY(-4px); box-shadow: 0 16px 40px rgba(0,0,0,.12); }
.ps-product-card img { width: 100%; height: 200px; object-fit: cover; display: block; }
.ps-product-card__body { padding: 20px 22px; background: #fff; }
.ps-product-card__tag {
  display: inline-block;
  font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase;
  padding: 3px 10px; border-radius: 999px; margin-bottom: 10px;
}
.ps-product-card h4 { font-size: 16px; font-weight: 800; color: #1a1a2e; margin-bottom: 8px; line-height: 1.3; }
.ps-product-card p  { font-size: 13px; color: var(--ps-gray); margin: 0 0 16px; line-height: 1.6; }

/* ──────────────────────────────────────────
   CERTIFICATE SECTION
────────────────────────────────────────── */
.ps-cert-grid {
  display: grid;
  grid-template-columns: 1.1fr 0.9fr;
  gap: 60px;
  align-items: center;
}
@media(max-width:768px){ .ps-cert-grid { grid-template-columns: 1fr; } }
.ps-cert-img {
  position: relative;
}
.ps-cert-img img {
  width: 100%;
  border-radius: 16px;
  box-shadow: 0 20px 50px rgba(0,0,0,.15);
}
.ps-cert-img__ribbon {
  position: absolute;
  top: -16px; right: -16px;
  background: var(--ps-orange);
  color: #fff;
  border-radius: 50%;
  width: 72px; height: 72px;
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  font-size: 10px; font-weight: 800;
  text-align: center; line-height: 1.2;
  box-shadow: 0 4px 16px rgba(249,115,22,.4);
}

/* ──────────────────────────────────────────
   COURSE LIST
────────────────────────────────────────── */
.ps-course-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}
@media(max-width:900px){ .ps-course-grid { grid-template-columns: 1fr 1fr; } }
@media(max-width:540px){ .ps-course-grid { grid-template-columns: 1fr; } }
.ps-course-card {
  background: #fff;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 18px rgba(0,0,0,.07);
  border: 1px solid #eee;
  transition: transform .25s, box-shadow .25s;
}
.ps-course-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(0,0,0,.12); }
.ps-course-card img { width: 100%; height: 160px; object-fit: cover; display: block; }
.ps-course-card__body { padding: 14px 16px; }
.ps-course-card h5 { font-size: 13px; font-weight: 700; color: #1a1a2e; margin-bottom: 6px; line-height: 1.4; }
.ps-course-card__price { font-size: 15px; font-weight: 800; color: var(--ps-orange); }
.ps-course-card__rating { display: flex; align-items: center; gap: 4px; font-size: 12px; color: #f59e0b; margin-top: 6px; }
.ps-course-card__cart {
  display: flex; align-items: center; justify-content: center;
  width: 32px; height: 32px; border-radius: 50%;
  background: var(--ps-orange); color: #fff;
  font-size: 14px; text-decoration: none; margin-left: auto;
  transition: background .2s;
}
.ps-course-card__cart:hover { background: #ea6c0a; color: #fff; }

/* ──────────────────────────────────────────
   BENEFITS — 4 image cards
────────────────────────────────────────── */
.ps-benefits-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
}
@media(max-width:900px){ .ps-benefits-grid { grid-template-columns: 1fr 1fr; } }
@media(max-width:540px){ .ps-benefits-grid { grid-template-columns: 1fr; } }
.ps-benefit-card {
  border-radius: 18px;
  overflow: hidden;
  position: relative;
  box-shadow: 0 6px 24px rgba(0,0,0,.1);
  cursor: pointer;
}
.ps-benefit-card img {
  width: 100%; height: 220px; object-fit: cover; display: block;
  transition: transform .4s;
}
.ps-benefit-card:hover img { transform: scale(1.06); }
.ps-benefit-card__overlay {
  position: absolute; inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,.65) 0%, transparent 50%);
}
.ps-benefit-card__text {
  position: absolute; bottom: 0; left: 0; right: 0;
  padding: 16px;
  color: #fff;
}
.ps-benefit-card__text h5 { font-size: 14px; font-weight: 800; margin: 0 0 4px; line-height: 1.3; }
.ps-benefit-card__text p  { font-size: 12px; margin: 0; opacity: .85; line-height: 1.5; }

/* ──────────────────────────────────────────
   OUR APPROACH
────────────────────────────────────────── */
.ps-approach-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 60px;
  align-items: center;
}
@media(max-width:768px){ .ps-approach-grid { grid-template-columns: 1fr; } }
.ps-approach-img img {
  width: 100%; border-radius: 20px;
  box-shadow: 0 16px 48px rgba(0,0,0,.12);
}
.ps-approach-steps { display: flex; flex-direction: column; gap: 20px; margin-top: 28px; }
.ps-step {
  display: flex; gap: 16px; align-items: flex-start;
  padding: 18px 20px;
  background: #fff;
  border-radius: 14px;
  border: 1px solid #f0e8e0;
  box-shadow: 0 2px 8px rgba(0,0,0,.04);
  transition: box-shadow .2s;
}
.ps-step:hover { box-shadow: 0 6px 20px rgba(249,115,22,.1); }
.ps-step__num {
  width: 36px; height: 36px; border-radius: 10px;
  background: var(--ps-orange); color: #fff;
  font-size: 15px; font-weight: 800;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.ps-step h5 { font-size: 14px; font-weight: 800; color: #1a1a2e; margin-bottom: 4px; line-height: 1.3; }
.ps-step p  { font-size: 13px; color: var(--ps-gray); margin: 0; line-height: 1.6; }

/* ──────────────────────────────────────────
   CTA BANNER
────────────────────────────────────────── */
.ps-cta {
  background: linear-gradient(135deg, #f97316 0%, #e63946 50%, #be185d 100%);
  padding: 60px 24px;
  text-align: center;
  color: #fff;
  position: relative;
  overflow: hidden;
}
.ps-cta::before {
  content: '';
  position: absolute;
  top: -80px; left: -80px;
  width: 300px; height: 300px;
  border-radius: 50%;
  background: rgba(255,255,255,.08);
  pointer-events: none;
}
.ps-cta::after {
  content: '';
  position: absolute;
  bottom: -60px; right: -60px;
  width: 240px; height: 240px;
  border-radius: 50%;
  background: rgba(255,255,255,.06);
  pointer-events: none;
}
.ps-cta h2 {
  font-size: clamp(1.6rem,3vw,2.4rem);
  font-weight: 900;
  color: #fff;
  margin-bottom: 14px;
  line-height: 1.2;
  position: relative;
  z-index: 1;
}
.ps-cta p {
  font-size: 1.05rem;
  color: rgba(255,255,255,.88);
  margin-bottom: 32px;
  max-width: 560px;
  margin-left: auto; margin-right: auto;
  line-height: 1.75;
  position: relative;
  z-index: 1;
}
.ps-cta__btns { display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; position: relative; z-index: 1; }
.ps-cta__btn-white {
  display: inline-flex; align-items: center; gap: 8px;
  background: #fff; color: var(--ps-orange);
  padding: 13px 28px; border-radius: 999px;
  font-weight: 800; font-size: 14px; text-decoration: none;
  box-shadow: 0 6px 20px rgba(0,0,0,.15);
  transition: transform .2s;
}
.ps-cta__btn-white:hover { transform: translateY(-2px); color: var(--ps-orange); }
.ps-cta__btn-border {
  display: inline-flex; align-items: center; gap: 8px;
  background: transparent; color: #fff;
  border: 2px solid rgba(255,255,255,.6);
  padding: 11px 26px; border-radius: 999px;
  font-weight: 700; font-size: 14px; text-decoration: none;
  transition: background .2s;
}
.ps-cta__btn-border:hover { background: rgba(255,255,255,.15); color: #fff; }

/* ──────────────────────────────────────────
   SCROLL REVEAL
────────────────────────────────────────── */
[data-ps-reveal] {
  opacity: 0;
  transform: translateY(32px);
  transition: opacity .7s cubic-bezier(.22,1,.36,1), transform .7s cubic-bezier(.22,1,.36,1);
}
[data-ps-reveal].ps-show { opacity: 1; transform: translateY(0); }

/* ──────────────────────────────────────────
   DECORATIVE SHAPES (orange/blue triangles)
────────────────────────────────────────── */
.ps-shape {
  position: absolute;
  pointer-events: none;
  opacity: .18;
}

@media(max-width:768px){
  .ps-hero__inner { grid-template-columns: 1fr; padding-bottom: 32px; }
  .ps-hero__img { order: -1; }
}
</style>
@endpush

@section('contents')
<div class="ps-page">

{{-- ═══════════════════════════════════════════
     1. HERO
═══════════════════════════════════════════ --}}
<section class="ps-hero">
  {{-- decorative shapes --}}
  <div class="ps-shape" style="top:10%;right:5%;width:80px;height:80px;background:var(--ps-orange);clip-path:polygon(50% 0%,100% 100%,0% 100%);"></div>
  <div class="ps-shape" style="bottom:10%;left:3%;width:60px;height:60px;background:var(--ps-blue);clip-path:polygon(50% 0%,100% 100%,0% 100%);transform:rotate(20deg);"></div>
  <div class="ps-shape" style="top:20%;left:8%;width:40px;height:40px;background:var(--ps-yellow);border-radius:50%;"></div>
  <div class="ps-shape" style="bottom:20%;right:8%;width:50px;height:50px;background:var(--ps-teal);border-radius:50%;"></div>

  <div class="ps-hero__inner">
    <div data-ps-reveal>
      {{-- brand mark --}}
      <div class="ps-hero__brand">
        <img src="{{ asset('designs/img/logo.png') }}" alt="Pedaskills" onerror="this.style.display='none'">
        <span style="font-size:13px;font-weight:700;color:var(--ps-orange);letter-spacing:.04em;text-transform:uppercase;">Pedaskills</span>
      </div>

      <div class="ps-hero__tag">
        <i class="fa-solid fa-star" style="font-size:9px;"></i> Preschool to Grade 5
      </div>

      <h1>Empowering Preschools Through <span>Structured Skill Education</span></h1>

      <p>
        We design and deliver structured skill education programmes, activity-based learning solutions,
        curriculum resources, DIY kits and experiential learning environments for children across
        pre-primary and primary school education.
      </p>

      <div>
        <a href="{{ route('contact.index') }}" class="ps-btn-primary">
          <i class="fa-solid fa-calendar-check"></i> Book a Demo
        </a>
        <a href="#why-matters" class="ps-btn-outline">
          Learn More
        </a>
      </div>

      {{-- trust chips --}}
      <div style="display:flex;flex-wrap:wrap;gap:10px;margin-top:28px;">
        @foreach(['NEP 2020 Aligned','Activity-Based Learning','Grades Pre-K to 5','Teacher Training Included'] as $chip)
        <span style="display:inline-flex;align-items:center;gap:6px;background:rgba(249,115,22,.08);border:1px solid rgba(249,115,22,.2);border-radius:999px;padding:5px 14px;font-size:12px;font-weight:600;color:#c2410c;">
          <i class="fa-solid fa-circle-check" style="font-size:10px;"></i> {{ $chip }}
        </span>
        @endforeach
      </div>
    </div>

    <div class="ps-hero__img" data-ps-reveal style="transition-delay:.15s;">
      <img src="{{ asset('frontend/img/skillbox/preschool.jpg') }}"
           alt="Happy preschool children learning together">
      <div class="ps-hero__float-badge">
        <div class="dot"></div>
        <span>500+ Schools Enrolled</span>
      </div>
    </div>
  </div>
</section>

{{-- ═══════════════════════════════════════════
     2. WHY SKILL EDUCATION IN EARLY CHILDHOOD MATTERS
═══════════════════════════════════════════ --}}
<section class="ps-section" id="why-matters">
  <div class="ps-container">
    <div class="ps-section-head" data-ps-reveal>
      <div class="ps-eyebrow">Early Childhood Learning</div>
      <h2 class="ps-h2">Why Skill Education in <span>Early Childhood Matters</span></h2>
      <p class="ps-lead">
        The foundational years of a child's development are the most critical. Structured skill education
        during preschool builds the competencies, confidence, and curiosity that form the basis for
        lifelong learning and future success.
      </p>
    </div>

    <div class="ps-features-grid" data-ps-reveal>
      @php
      $earlyFeatures = [
        ['🧠','#eff6ff','#0962be','Brain Development',    'Early years shape 90% of brain development. Skill education builds the cognitive foundations children carry for life.'],
        ['🎨','#fff7ed','#ea580c','Creativity & Expression','Activity-based learning nurtures imagination, self-expression, and creative thinking from the earliest age.'],
        ['🤝','#f0fdf4','#16a34a','Social Skills',         'Collaborative learning builds empathy, communication, and teamwork — skills that matter in school and beyond.'],
        ['⭐','#fefce8','#ca8a04','Confidence Building',   'Age-appropriate challenges and achievements build a child\'s self-belief and enthusiasm for learning.'],
      ];
      @endphp
      @foreach($earlyFeatures as [$emoji, $bg, $color, $title, $desc])
      <div class="ps-feature-card">
        <div class="ps-feature-icon" style="background:{{ $bg }};color:{{ $color }};font-size:28px;">{{ $emoji }}</div>
        <h4>{{ $title }}</h4>
        <p>{{ $desc }}</p>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ═══════════════════════════════════════════
     3. WHY IS PRESCHOOL THE FIRST BIG STEP?
═══════════════════════════════════════════ --}}
<section class="ps-section ps-section--gray">
  <div class="ps-container">
    <div class="ps-section-head" data-ps-reveal>
      <div class="ps-eyebrow">Our Approach</div>
      <h2 class="ps-h2">Why is Preschool the <span>First Big Step?</span></h2>
    </div>

    <div class="ps-why-grid">

      {{-- Image mosaic left --}}
      <div class="ps-img-mosaic" data-ps-reveal>
        <img class="ps-img-mosaic__big"
             src="{{ asset('frontend/img/skillbox/forchildren.jpg') }}"
             alt="Children learning activity" />
        <img src="{{ asset('designs/img/skill2school-2.jpeg') }}"
             alt="Classroom activity" />
        <img src="{{ asset('designs/img/skill2school-4.jpeg') }}"
             alt="Teacher and students" />
      </div>

      {{-- Feature list right --}}
      <div data-ps-reveal style="transition-delay:.1s;">
        <ul class="ps-feature-list">
          @php
          $whyItems = [
            ['🎯','#fff7ed','#ea580c','Joyful Learners',
             'Children learn best through play, activity, and joy. Our programmes are designed to make every session an experience children love.'],
            ['📚','#eff6ff','#0962be','Lively Classroom Activity Kits',
             'Ready-to-use activity kits that transform any classroom into an engaging, hands-on learning space without extensive preparation.'],
            ['👩‍🏫','#f0fdf4','#16a34a','Join Our Teaching Families',
             'We train and support teachers to deliver structured skill sessions confidently, turning every educator into a skill facilitator.'],
            ['🏫','#fef2f2','#dc2626','CBSE & NEP 2020 Ready',
             'All our programmes and kits are aligned with CBSE guidelines, NEP 2020, and NCF 2023 — meeting inspection and curriculum requirements.'],
            ['🌱','#fefce8','#ca8a04','Builds Real-World Readiness',
             'We focus on life skills, communication, problem-solving, and creativity — competencies that prepare children for school and life beyond it.'],
          ];
          @endphp
          @foreach($whyItems as [$emoji, $bg, $color, $title, $desc])
          <li>
            <div class="ps-feature-list__icon" style="background:{{ $bg }};color:{{ $color }};font-size:20px;">{{ $emoji }}</div>
            <div>
              <h5>{{ $title }}</h5>
              <p>{{ $desc }}</p>
            </div>
          </li>
          @endforeach
        </ul>
      </div>

    </div>
  </div>
</section>

{{-- ═══════════════════════════════════════════
     4. LMS + ACTIVITY WORKSHEET
═══════════════════════════════════════════ --}}
<section class="ps-section">
  <div class="ps-container">
    <div class="ps-section-head" data-ps-reveal>
      <div class="ps-eyebrow">Digital & Physical Learning Tools</div>
      <h2 class="ps-h2">Learn Anywhere, <span>Anytime</span></h2>
      <p class="ps-lead">We combine a powerful digital LMS with beautifully designed physical activity worksheets and kits — giving children the best of both worlds.</p>
    </div>

    <div class="ps-product-grid" data-ps-reveal>
      {{-- LMS Card --}}
      <div class="ps-product-card">
        <img src="{{ asset('frontend/img/skillbox/homeimg01.jpeg') }}" alt="Skill 2 Skool LMS Platform">
        <div class="ps-product-card__body">
          <span class="ps-product-card__tag" style="background:#eff6ff;color:#0962be;">Digital Platform</span>
          <h4>Skill 2 Skool LMS</h4>
          <p>Our digital learning platform offers 33+ structured skill education courses for students from pre-primary to Grade 5, accessible from any device, anytime.</p>
          <a href="{{ route('skill2school') }}" class="ps-btn-primary" style="font-size:13px;padding:10px 22px;">
            Explore LMS <i class="fa-solid fa-arrow-right" style="font-size:11px;"></i>
          </a>
        </div>
      </div>

      {{-- Activity Worksheet Card --}}
      <div class="ps-product-card">
        <img src="{{ asset('designs/img/skill2school-3.png') }}" alt="Activity Worksheets and Kits"
             style="object-fit:contain;background:#fff8f0;padding:16px;">
        <div class="ps-product-card__body">
          <span class="ps-product-card__tag" style="background:#fff7ed;color:#ea580c;">Physical Kits</span>
          <h4>Activity Worksheets & Kits</h4>
          <p>Curriculum-linked, age-appropriate activity worksheets and DIY kits designed for hands-on, classroom and home-based skill development.</p>
          <a href="{{ route('contact.index') }}" class="ps-btn-primary" style="font-size:13px;padding:10px 22px;background:var(--ps-orange);">
            Get Kits <i class="fa-solid fa-arrow-right" style="font-size:11px;"></i>
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ═══════════════════════════════════════════
     5. CERTIFICATE SECTION
═══════════════════════════════════════════ --}}
<section class="ps-section ps-section--gray">
  <div class="ps-container">
    <div class="ps-cert-grid">

      <div class="ps-cert-img" data-ps-reveal>
        <img src="{{ asset('designs/img/skill2school-4.png') }}"
             alt="Pedaskills Certificate of Completion"
             onerror="this.src='{{ asset('designs/img/skill2school-4.jpeg') }}'">
        <div class="ps-cert-img__ribbon">
          <span style="font-size:18px;">🏆</span>
          <span>Certified</span>
        </div>
      </div>

      <div data-ps-reveal style="transition-delay:.12s;">
        <div class="ps-eyebrow" style="justify-content:flex-start;">Certification</div>
        <h2 class="ps-h2" style="text-align:left;">Recognised <span>Certificates</span> for Every Learner</h2>
        <p style="color:var(--ps-gray);line-height:1.8;margin-bottom:24px;">
          Every student who completes a Skill 2 Skool programme receives a structured certificate of
          achievement — recognised by Pedaskills and aligned with CBSE and NEP 2020 skill education
          guidelines.
        </p>
        <ul class="ps-feature-list" style="margin-bottom:28px;">
          @foreach([
            ['🎓','Certificate of Achievement for students'],
            ['🏫','School-branded certification option'],
            ['📋','Aligned with NEP 2020 & CBSE skill guidelines'],
            ['👩‍🏫','Teacher training completion certificates'],
          ] as [$emoji, $text])
          <li style="padding:10px 0;">
            <div class="ps-feature-list__icon" style="background:#fff7ed;color:var(--ps-orange);font-size:18px;width:38px;height:38px;border-radius:10px;">{{ $emoji }}</div>
            <div><p style="margin:0;font-size:14px;font-weight:600;color:#1a1a2e;">{{ $text }}</p></div>
          </li>
          @endforeach
        </ul>
        <a href="{{ route('contact.index') }}" class="ps-btn-primary">
          <i class="fa-solid fa-certificate"></i> Learn About Certification
        </a>
      </div>

    </div>
  </div>
</section>

{{-- ═══════════════════════════════════════════
     6. COURSE LIST
═══════════════════════════════════════════ --}}
<section class="ps-section">
  <div class="ps-container">
    <div class="ps-section-head" data-ps-reveal>
      <div class="ps-eyebrow">Skill Education Courses</div>
      <h2 class="ps-h2">Explore Our <span>Course List</span></h2>
      <p class="ps-lead">33+ structured skill education courses covering communication, creativity, STEM thinking, life skills, and more — designed for pre-primary to Grade 5.</p>
    </div>

    {{-- Dynamic courses from LMS --}}
    @include('frontend.home-four.components.course-carousel', [
      'type'     => '8',
      'upskill'  => '0',
      'lms_only' => '1',
      'title'    => '',
      'subtitle' => '',
      'tagline'  => ''
    ])

    <div class="text-center mt-8" data-ps-reveal>
      <a href="{{ url('/courses') }}" class="ps-btn-primary">
        <i class="fa-solid fa-graduation-cap"></i> View All Courses
      </a>
    </div>
  </div>
</section>

{{-- ═══════════════════════════════════════════
     7. SKILL READINESS & STUDENT MAPPING — UNCHANGED
═══════════════════════════════════════════ --}}
<section class="py-16 text-center max-w-container-max mx-auto px-margin-mobile md:px-gutter">
  <p class="text-primary font-semibold mb-2 text-sm uppercase tracking-wider">Skill Readiness &amp; Student Mapping</p>
  <h2 class="text-3xl md:text-4xl font-bold mb-6 text-on-surface">Understanding Strengths Beyond Marks</h2>
  <p class="text-on-surface-variant max-w-3xl mx-auto text-lg">
    Skill 2 Skool supports schools in identifying and nurturing student strengths through structured observation,
    reflection, and skill mapping. By focusing on aptitude, interest, and behaviour patterns, schools gain deeper
    insights into student potential—supporting informed guidance, confidence building, and holistic development.
  </p>
</section>

{{-- ═══════════════════════════════════════════
     8. BENEFITS FOR PRESCHOOLS
═══════════════════════════════════════════ --}}
<section class="ps-section ps-section--gray">
  <div class="ps-container">
    <div class="ps-section-head" data-ps-reveal>
      <div class="ps-eyebrow">What Schools Gain</div>
      <h2 class="ps-h2">Benefits for <span>Preschools</span></h2>
    </div>

    <div class="ps-benefits-grid" data-ps-reveal>
      @php
      $benefits = [
        [asset('frontend/img/skillbox/forchildren.jpg'),    '#16a34a','Complete Lab Setup',        'Turnkey skill lab installation with all tools, furniture, and materials included.'],
        [asset('designs/img/skill2school-2.jpeg'),           '#0962be','Teacher Training Support',  'Ongoing training and upskilling programmes for all educators.'],
        [asset('frontend/img/skillbox/teacher_upskilling.jpg'),'#ea580c','Curriculum Alignment',   'All content aligned with NEP 2020, CBSE, and state board guidelines.'],
        [asset('designs/img/skill2school-5.jpeg'),           '#7c3aed','Parent Engagement Tools',   'Resources to keep parents engaged and informed about their child\'s skill journey.'],
      ];
      @endphp
      @foreach($benefits as [$img, $color, $title, $desc])
      <div class="ps-benefit-card">
        <img src="{{ $img }}" alt="{{ $title }}">
        <div class="ps-benefit-card__overlay"></div>
        <div class="ps-benefit-card__text">
          <div style="display:inline-block;background:{{ $color }};border-radius:8px;padding:3px 10px;font-size:10px;font-weight:700;color:#fff;margin-bottom:6px;letter-spacing:.06em;text-transform:uppercase;">{{ $title }}</div>
          <p>{{ $desc }}</p>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ═══════════════════════════════════════════
     9. OUR APPROACH
═══════════════════════════════════════════ --}}
<section class="ps-section">
  <div class="ps-container">
    <div class="ps-approach-grid">

      <div class="ps-approach-img" data-ps-reveal>
        <img src="{{ asset('designs/img/skill2school-5.jpeg') }}" alt="Students engaged in activity-based learning">
      </div>

      <div data-ps-reveal style="transition-delay:.12s;">
        <div class="ps-eyebrow" style="justify-content:flex-start;">How We Work</div>
        <h2 class="ps-h2" style="text-align:left;">Our <span>Approach</span></h2>
        <p style="color:var(--ps-gray);line-height:1.8;margin-bottom:8px;">
          We work with preschools and primary schools as a complete skill education partner — designing,
          delivering, and supporting every stage of your skill education journey.
        </p>

        <div class="ps-approach-steps">
          @php
          $steps = [
            ['Understand',  'We listen to your school\'s objectives, grade structure, and constraints before proposing a solution.'],
            ['Design',      'A customised skill education plan — aligned with your timetable, curriculum, and infrastructure.'],
            ['Deliver',     'Activity kits, LMS access, teacher training, and implementation support delivered together.'],
            ['Support',     'Ongoing academic guidance, refresher training, and curriculum updates throughout the year.'],
          ];
          @endphp
          @foreach($steps as $i => [$title, $desc])
          <div class="ps-step">
            <div class="ps-step__num">{{ $i + 1 }}</div>
            <div>
              <h5>{{ $title }}</h5>
              <p>{{ $desc }}</p>
            </div>
          </div>
          @endforeach
        </div>
      </div>

    </div>
  </div>
</section>

{{-- ═══════════════════════════════════════════
     10. CTA BANNER
═══════════════════════════════════════════ --}}
<section class="ps-cta">
  <div style="display:flex;align-items:center;justify-content:center;gap:12px;margin-bottom:14px;position:relative;z-index:1;">
    <img src="{{ asset('designs/img/logo.png') }}" alt="Pedaskills" style="height:32px;filter:brightness(0) invert(1);opacity:.9;" onerror="this.style.display='none'">
  </div>
  <h2>Let's Build Confident, Capable, and<br>Innovative Young Learners Together</h2>
  <p>
    Whether you are looking for skill education programmes, activity kits, an LMS platform, or a complete
    skill education partnership — our team can help identify the right solution for your school.
  </p>
  <div class="ps-cta__btns">
    <a href="{{ route('contact.index') }}" class="ps-cta__btn-white">
      <i class="fa-solid fa-calendar-check"></i> Book a School Demo
    </a>
    <a href="{{ route('contact.index') }}" class="ps-cta__btn-border">
      <i class="fa-solid fa-phone"></i> Talk to Our Team
    </a>
  </div>
  <p style="font-size:13px;color:rgba(255,255,255,.7);margin-top:20px;position:relative;z-index:1;">
    📞 98450 26782 &nbsp;·&nbsp; ✉️ infomyskoolonline@gmail.com &nbsp;·&nbsp; Bengaluru, Karnataka
  </p>
</section>

</div>{{-- /.ps-page --}}
@endsection

@push('scripts')
<script>
(function () {
  'use strict';
  var els = document.querySelectorAll('[data-ps-reveal]');
  if (!els.length) return;
  var obs = new IntersectionObserver(function (entries) {
    entries.forEach(function (e) {
      if (e.isIntersecting) {
        e.target.classList.add('ps-show');
        obs.unobserve(e.target);
      }
    });
  }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
  els.forEach(function (el) { obs.observe(el); });
}());
</script>
@endpush
