@extends('frontend.home-four.layouts.master')

@section('meta_title', 'Basic Composite Skill Lab | CBSE-Aligned & School-Ready | Skillvation')
@section('meta_description', 'CBSE-aligned Basic Composite Skill Lab for schools. Complete setup with lab infrastructure, tools, equipment, and experiential learning resources aligned with NEP 2020 and CBSE Circular Skill-75/2024.')

@push('styles')
<style>
  .dot-bg {
    background-image: radial-gradient(#e2e8f0 1px, transparent 1px);
    background-size: 22px 22px;
  }
  details summary::-webkit-details-marker { display: none; }
  details[open] .faq-chevron { transform: rotate(180deg); }

  /* ==================== FAQ CAROUSEL ==================== */
  .vl-faq-carousel { position: relative; }
  .vl-faq-slide { display: none; animation: vl-fade-in 0.35s ease; }
  .vl-faq-slide.active { display: block; }
  @keyframes vl-fade-in {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
  }
  .vl-faq-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 26px;
  }
  @media (max-width: 920px) { .vl-faq-grid { grid-template-columns: repeat(2, 1fr); } }
  @media (max-width: 600px) { .vl-faq-grid { grid-template-columns: 1fr; } }
  .vl-faq-card {
    background: #ffffff;
    border: 2px solid #ede9fe;
    border-radius: 20px;
    padding: 32px 28px;
    display: flex;
    flex-direction: column;
    box-shadow: 0 4px 16px rgba(99,102,241,0.07);
    transition: all 0.25s ease;
  }
  .vl-faq-card:hover {
    border-color: #c084fc;
    box-shadow: 0 12px 32px rgba(99,102,241,0.14);
    transform: translateY(-5px);
  }
  .vl-faq-card__q {
    font-size: 1.1rem;
    font-weight: 800;
    color: #1e1b4b;
    line-height: 1.35;
    margin-bottom: 14px;
  }
  .vl-faq-card__divider {
    width: 40px; height: 4px;
    background: linear-gradient(90deg, #f97316 0%, #ec4899 100%);
    border-radius: 4px;
    margin-bottom: 16px;
  }
  .vl-faq-card__a { font-size: 0.96rem; color: #475569; line-height: 1.68; flex: 1; }
  .vl-faq-card__a ul { padding-left: 18px; margin: 8px 0 0; }
  .vl-faq-card__a li { margin-bottom: 6px; }
  .vl-faq-nav {
    display: flex; align-items: center; justify-content: center;
    gap: 20px; margin-top: 40px; padding-top: 26px;
    border-top: 2px solid #ede9fe;
  }
  .vl-faq-btn {
    width: 48px; height: 48px; border-radius: 14px;
    border: 2px solid #ddd6fe; background: #ffffff; color: #6d28d9;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    transition: all 0.2s ease; box-shadow: 0 2px 8px rgba(99,102,241,0.08);
  }
  .vl-faq-btn:hover:not(:disabled) {
    background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
    border-color: #6366f1; color: #ffffff;
    transform: translateY(-2px); box-shadow: 0 8px 20px rgba(99,102,241,0.35);
  }
  .vl-faq-btn:disabled { opacity: 0.35; cursor: not-allowed; }
  .vl-faq-dots { display: flex; gap: 8px; align-items: center; }
  .vl-faq-dot {
    width: 10px; height: 10px; border-radius: 50%;
    background: #ddd6fe; cursor: pointer; transition: all 0.25s ease;
    border: none; padding: 0;
  }
  .vl-faq-dot.active {
    background: linear-gradient(135deg, #f97316 0%, #ec4899 100%);
    width: 28px; border-radius: 12px;
  }
  .vl-faq-counter { font-size: 0.95rem; color: #6d28d9; font-weight: 800; min-width: 50px; text-align: center; }
  .feature-card { transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
  .feature-card:hover { transform: translateY(-4px); box-shadow: 0 16px 36px rgba(40, 36, 111, 0.08); }
  
  /* Flow arrow indicator */
  .flow-step { position: relative; }
  .flow-step:not(:last-child)::after {
    content: '→';
    position: absolute;
    right: -14px;
    top: 50%;
    transform: translateY(-50%);
    color: #f05f43;
    font-weight: 800;
    font-size: 16px;
  }
  @media (max-width: 768px) {
    .flow-step:not(:last-child)::after {
      content: '↓';
      position: static;
      display: block;
      margin: 4px auto;
      transform: none;
    }
  }

  /* Kit section styles */
  #skill-sectors-columns { display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-start; }
  #skill-sectors-columns > div.hidden { display: none !important; }
  #skill-sectors-columns > div:not(.hidden):only-child { flex: 0 0 100% !important; }
  .skill-acc-row button:focus-visible { outline: 2px solid #3b82f6; outline-offset: -2px; }

  /* Kit Carousel styles */
  .sks-nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #1e293b;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 14px rgba(0,0,0,0.12);
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .sks-nav-btn:hover {
    background: #ffffff;
    color: #f4742b;
    border-color: #fdba74;
    box-shadow: 0 8px 24px rgba(244,116,43,0.22);
    transform: translateY(-50%) scale(1.08);
  }
  .sks-nav-btn:active {
    transform: translateY(-50%) scale(0.96);
  }
  .sks-nav-prev { left: -22px; }
  .sks-nav-next { right: -22px; }
  @media (max-width: 768px) {
    .sks-nav-prev { left: 4px; }
    .sks-nav-next { right: 4px; }
    .sks-nav-btn {
      width: 38px;
      height: 38px;
      background: rgba(255,255,255,0.92);
      backdrop-filter: blur(6px);
      box-shadow: 0 2px 10px rgba(0,0,0,0.15);
    }
  }
  .sks-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #cbd5e1;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .sks-dot.active {
    width: 26px;
    border-radius: 13px;
    background: #f4742b;
  }
  .sks-box-card {
    flex-shrink: 0;
    box-sizing: border-box;
    width: 100%;
  }
</style>
@endpush

@section('contents')

{{-- =====================================================================
     1. HERO SECTION: BASIC COMPOSITE SKILL LAB
     ===================================================================== --}}
<section class="dot-bg pt-12 pb-16 lg:pt-16 lg:pb-24 bg-gradient-to-b from-white via-slate-50/50 to-white relative overflow-hidden">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">

      {{-- Left Content --}}
      <div class="lg:col-span-7 space-y-6">
        <div class="flex items-center gap-2 text-xs text-slate-400 font-semibold">
          <!-- <a href="{{ route('home') }}" class="hover:text-brand-orange transition-colors">Home</a>
          <i class="fa-solid fa-chevron-right text-[9px]"></i>
          <a href="{{ route('labs') }}" class="hover:text-brand-orange transition-colors">Labs</a>
          <i class="fa-solid fa-chevron-right text-[9px]"></i>
          <span class="text-brand-navy font-bold">Basic Composite Skill Lab</span> -->
        </div>

        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-orange/10 text-brand-orange border border-brand-orange/20 text-xs font-bold uppercase tracking-wider">
          <i class="fa-solid fa-certificate text-brand-orange"></i> CBSE-Aligned · Practical · Ready for Implementation
        </div>

        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-brand-navy leading-[1.15] tracking-tight">
          BASIC COMPOSITE<br>
          <span class="text-brand-orange">SKILL LAB</span>
        </h1>

        <p class="text-base sm:text-lg text-slate-700 leading-relaxed font-medium">
          Create a dedicated space for hands-on skill education with the <strong class="text-brand-navy font-bold">Skillvation Basic Composite Skill Lab</strong> — a practical, school-ready solution designed around the Composite Skill Lab requirement issued by CBSE.
        </p>

        <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
          The lab brings together essential infrastructure, tools, equipment and learning resources to help schools provide students with meaningful opportunities for experiential, project-based and hands-on skill learning.
        </p>

        {{-- Quick value highlights --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
          <div class="flex items-center gap-2.5 text-sm text-slate-700 bg-white p-2.5 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs flex-shrink-0 font-bold">✓</span>
            <span class="font-semibold">CBSE Circular Skill-75/2024 & Skill-01/2025</span>
          </div>
          <div class="flex items-center gap-2.5 text-sm text-slate-700 bg-white p-2.5 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs flex-shrink-0 font-bold">✓</span>
            <span class="font-semibold">Single Integrated Turnkey Setup</span>
          </div>
          <div class="flex items-center gap-2.5 text-sm text-slate-700 bg-white p-2.5 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs flex-shrink-0 font-bold">✓</span>
            <span class="font-semibold">Covers 3 Core Forms of Work</span>
          </div>
          <div class="flex items-center gap-2.5 text-sm text-slate-700 bg-white p-2.5 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs flex-shrink-0 font-bold">✓</span>
            <span class="font-semibold">Scalable & Inspection-Ready</span>
          </div>
        </div>

        <div class="flex flex-wrap gap-4 pt-3">
          <a href="#cta-section"
             class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-brand-orange hover:bg-brand-orangehover text-white text-sm font-bold shadow-lg shadow-brand-orange/25 transition-all hover:-translate-y-0.5">
            <i class="fa-solid fa-calendar-check"></i> Book a Skill Lab Visit
          </a>
          <!-- <a href="#package-details"
             class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl border-2 border-brand-navy text-brand-navy hover:bg-brand-navy hover:text-white text-sm font-bold transition-all hover:-translate-y-0.5">
            <i class="fa-solid fa-box-archive"></i> Request Detailed Package
          </a> -->
        </div>
      </div>

      {{-- Right Graphic / Visual --}}
      <div class="lg:col-span-5 relative">
        <div class="absolute -inset-4 bg-gradient-to-tr from-brand-orange/20 to-brand-navy/10 rounded-3xl rotate-2 blur-md"></div>
        <div class="relative rounded-2xl overflow-hidden shadow-2xl border border-slate-200 bg-white p-2">
          <img src="https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=1000&q=85"
               alt="Skillvation Basic Composite Skill Lab in school"
               class="w-full aspect-[4/3] object-cover rounded-xl" />
          <div class="p-4 bg-brand-navy text-white rounded-xl mt-2 flex items-center justify-between">
            <div>
              <p class="text-xs text-brand-peach font-bold uppercase tracking-wider">Ready for Implementation</p>
              <p class="text-sm font-extrabold text-white">Full Lab Setup · Fast Turnaround</p>
            </div>
            <span class="px-3 py-1 bg-brand-orange text-white text-xs font-black rounded-lg uppercase">NEP 2020</span>
          </div>
        </div>
      </div>
    </div>

    {{-- Stats Row --}}
    <div class="mt-12 sm:mt-14 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
      @foreach([
        ['icon'=>'fa-scale-balanced', 'stat'=>'CBSE-Aligned', 'label'=>'Circular Skill-75/2024 & 01/2025'],
        ['icon'=>'fa-shapes',         'stat'=>'Multi-Domain', 'label'=>'Life Forms, Machines & Services'],
        ['icon'=>'fa-boxes-stacked',   'stat'=>'100% Turnkey', 'label'=>'Infrastructure + Tools + Storage'],
        ['icon'=>'fa-graduation-cap', 'stat'=>'Classes VI–XII','label'=>'600 sq ft or 2×400 sq ft Options'],
      ] as $s)
      <div class="bg-white rounded-2xl p-4 sm:p-5 flex items-center gap-3.5 sm:gap-4 border border-slate-200/90 shadow-sm hover:border-brand-orange/40 hover:shadow-md transition-all">
        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-brand-navy/10 flex items-center justify-center text-brand-navy text-lg sm:text-xl flex-shrink-0">
          <i class="fa-solid {{ $s['icon'] }}"></i>
        </div>
        <div class="min-w-0 flex-1">
          <div class="text-base sm:text-lg font-extrabold text-brand-navy leading-tight truncate sm:whitespace-normal">{{ $s['stat'] }}</div>
          <div class="text-xs text-slate-500 font-medium mt-0.5 leading-snug">{{ $s['label'] }}</div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- =====================================================================
     2. CBSE COMPOSITE SKILL LAB REQUIREMENT
     ===================================================================== --}}
<!-- <section class="py-16 lg:py-20 bg-slate-50 border-t border-b border-slate-200">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">

      <div class="lg:col-span-5 space-y-4">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold uppercase tracking-wider border border-red-200">
          <i class="fa-solid fa-landmark"></i> Official Mandate
        </div>
        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-brand-navy leading-tight">
          CBSE COMPOSITE SKILL LAB REQUIREMENT
        </h2>
        <p class="text-base text-brand-orange font-bold">
          A Mandatory Step Towards Experiential Skill Education
        </p>
        <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
          CBSE, through <strong class="text-brand-navy">Circular No. Skill-75/2024 dated 23 August 2024</strong>, directed all CBSE-affiliated schools to establish a Composite Skill Lab with the necessary equipment and machinery to support the implementation of skill education in alignment with <strong class="text-brand-navy">NEP 2020</strong> and the <strong class="text-brand-navy">National Curriculum Framework for School Education (NCF-SE)</strong>.
        </p>
        <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs sm:text-sm leading-relaxed space-y-1">
          <div class="font-bold flex items-center gap-2 text-amber-800">
            <i class="fa-solid fa-bullhorn text-amber-600"></i> Skill-01/2025 Reiteration
          </div>
          <p>
            CBSE subsequently reiterated the Composite Skill Lab requirement in its <strong>Skill-01/2025 circular dated 10 January 2025</strong>, highlighting the importance of adequate equipment and facilities for effective implementation of Skill Education.
          </p>
        </div>
      </div>

      <div class="lg:col-span-7">
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-md space-y-4">
          <h3 class="text-lg font-black text-brand-navy border-b border-slate-100 pb-3 flex items-center gap-2">
            <i class="fa-solid fa-file-contract text-brand-orange"></i> The CBSE Circular States That:
          </h3>
          
          <ul class="space-y-3.5">
            <li class="flex items-start gap-3 text-sm text-slate-700">
              <span class="w-6 h-6 rounded-full bg-brand-orange/10 text-brand-orange flex items-center justify-center text-xs font-bold mt-0.5 flex-shrink-0">1</span>
              <div>
                <strong class="text-brand-navy font-bold">Fresh CBSE Affiliation:</strong> Schools seeking fresh CBSE affiliation must have a Composite Skill Lab with the necessary equipment and machinery.
              </div>
            </li>
            <li class="flex items-start gap-3 text-sm text-slate-700">
              <span class="w-6 h-6 rounded-full bg-brand-orange/10 text-brand-orange flex items-center justify-center text-xs font-bold mt-0.5 flex-shrink-0">2</span>
              <div>
                <strong class="text-brand-navy font-bold">Already Affiliated Schools:</strong> Schools already affiliated with CBSE are required to establish a Composite Skill Lab within three years from the date of the circular.
              </div>
            </li>
            <li class="flex items-start gap-3 text-sm text-slate-700">
              <span class="w-6 h-6 rounded-full bg-brand-orange/10 text-brand-orange flex items-center justify-center text-xs font-bold mt-0.5 flex-shrink-0">3</span>
              <div>
                <strong class="text-brand-navy font-bold">Flexible Space Norms:</strong> Schools may establish <strong>one Composite Skill Lab of 600 sq. ft.</strong> for Classes VI–XII, or <strong>two separate labs of 400 sq. ft. each</strong> (one for Classes VI–X and another for Classes XI–XII).
              </div>
            </li>
            <li class="flex items-start gap-3 text-sm text-slate-700">
              <span class="w-6 h-6 rounded-full bg-brand-orange/10 text-brand-orange flex items-center justify-center text-xs font-bold mt-0.5 flex-shrink-0">4</span>
              <div>
                <strong class="text-brand-navy font-bold">Hands-on Experiential Learning:</strong> The lab is intended to provide students with opportunities for practical, real-world tasks and project-based skill learning.
              </div>
            </li>
          </ul>

          <div class="pt-2">
            <a href="#cta-section" class="inline-flex items-center gap-2 text-xs font-bold text-brand-navy hover:text-brand-orange transition-colors">
              <span>Have questions regarding your school's compliance timeline?</span>
              <i class="fa-solid fa-arrow-right text-[11px]"></i>
            </a>
          </div>
        </div>
      </div>

    </div>
  </div>
</section> -->

{{-- =====================================================================
     3. SKILLVATION BASIC PACKAGE: What is Included
     ===================================================================== --}}
<section class="py-16 lg:py-24 bg-white" id="package-details">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
      <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-navy/10 text-brand-navy text-xs font-bold uppercase tracking-wider">
        Complete Solution
      </div>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-navy">
        SKILLVATION BASIC PACKAGE
      </h2>
      <p class="text-base sm:text-lg text-brand-orange font-bold">
        <!-- Designed to Meet the CBSE Composite Skill Lab Requirement -->
         Essential infrastructure. Essential equipment. CBSE-aligned skill learning.
      </p>
      <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
        <!-- The Skillvation Basic Composite Skill Lab is structured as an essential, cost-effective lab solution for schools looking to establish a functional Composite Skill Lab in line with CBSE's requirement. -->
         A practical, well-equipped Composite Skill Lab designed to help schools meet the CBSE requirement while providing students with opportunities for hands-on, project-based skill learning.
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      
      {{-- 1. Lab Infrastructure --}}
      <div class="feature-card bg-slate-50/80 rounded-2xl border border-slate-200 p-6 space-y-4">
        <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl">
          <i class="fa-solid fa-cubes-stacked"></i>
        </div>
        <h3 class="text-lg font-extrabold text-brand-navy flex items-center gap-2">
          <span class="text-brand-orange">✓</span> Lab Infrastructure
        </h3>
        <p class="text-sm text-slate-600 leading-relaxed">
          Functional student workstations, furniture and essential infrastructure for conducting hands-on activities.
        </p>
      </div>

      {{-- 2. Essential Tools & Equipment --}}
      <div class="feature-card bg-slate-50/80 rounded-2xl border border-slate-200 p-6 space-y-4">
        <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl">
          <i class="fa-solid fa-screwdriver-wrench"></i>
        </div>
        <h3 class="text-lg font-extrabold text-brand-navy flex items-center gap-2">
          <span class="text-brand-orange">✓</span> Essential Tools & Equipment
        </h3>
        <p class="text-sm text-slate-600 leading-relaxed">
          A curated range of tools and equipment required for practical skill activities across multiple domains.
        </p>
      </div>

      {{-- 3. Skill Activity Materials --}}
      <div class="feature-card bg-slate-50/80 rounded-2xl border border-slate-200 p-6 space-y-4">
        <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl">
          <i class="fa-solid fa-palette"></i>
        </div>
        <h3 class="text-lg font-extrabold text-brand-navy flex items-center gap-2">
          <span class="text-brand-orange">✓</span> Skill Activity Materials
        </h3>
        <p class="text-sm text-slate-600 leading-relaxed">
          Essential materials required for students to participate in guided activities and projects.
        </p>
      </div>

      {{-- 4. Demonstration Resources --}}
      <div class="feature-card bg-slate-50/80 rounded-2xl border border-slate-200 p-6 space-y-4">
        <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-xl">
          <i class="fa-solid fa-chalkboard-user"></i>
        </div>
        <h3 class="text-lg font-extrabold text-brand-navy flex items-center gap-2">
          <span class="text-brand-orange">✓</span> Demonstration Resources
        </h3>
        <p class="text-sm text-slate-600 leading-relaxed">
          Resources that enable teachers to demonstrate processes and introduce practical concepts effectively.
        </p>
      </div>

      {{-- 5. Project-Based Learning Support --}}
      <div class="feature-card bg-slate-50/80 rounded-2xl border border-slate-200 p-6 space-y-4">
        <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl">
          <i class="fa-solid fa-lightbulb"></i>
        </div>
        <h3 class="text-lg font-extrabold text-brand-navy flex items-center gap-2">
          <span class="text-brand-orange">✓</span> Project-Based Learning Support
        </h3>
        <p class="text-sm text-slate-600 leading-relaxed">
          Materials and resources to facilitate student projects, making and application-oriented activities.
        </p>
      </div>

      {{-- 6. Organised Storage --}}
      <div class="feature-card bg-slate-50/80 rounded-2xl border border-slate-200 p-6 space-y-4">
        <div class="w-12 h-12 rounded-xl bg-cyan-100 text-cyan-600 flex items-center justify-center text-xl">
          <i class="fa-solid fa-box-open"></i>
        </div>
        <h3 class="text-lg font-extrabold text-brand-navy flex items-center gap-2">
          <span class="text-brand-orange">✓</span> Organised Storage
        </h3>
        <p class="text-sm text-slate-600 leading-relaxed">
          Storage solutions for maintaining tools, equipment and consumable materials in an organised manner.
        </p>
      </div>

    </div>
  </div>
</section>

{{-- =====================================================================
     4. CBSE COMPLIANCE, SIMPLIFIED (Flow & Coordination)
     ===================================================================== --}}
<!-- <section class="py-16 lg:py-24 bg-gradient-to-br from-brand-navy via-brand-darknavy to-slate-900 text-white relative overflow-hidden">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

    <div class="text-center max-w-3xl mx-auto mb-14 space-y-3">
      <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 text-brand-peach text-xs font-bold uppercase tracking-wider">
        End-to-End Coordination
      </div>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-white">
        CBSE COMPLIANCE, SIMPLIFIED
      </h2>
      <p class="text-lg font-bold text-brand-orange">
        One Lab. Multiple Skill Areas. A Structured Learning Environment.
      </p>
      <p class="text-blue-100 text-sm sm:text-base leading-relaxed">
        The Skillvation Basic Package is designed to help schools address the infrastructure and equipment component of the CBSE Composite Skill Lab requirement through a single integrated setup.
      </p>
      <p class="text-slate-300 text-sm">
        Instead of sourcing furniture, tools, equipment and activity resources from multiple vendors, schools can establish their skill lab through one coordinated solution.
      </p>
    </div>

    {{-- Interactive Visual Pipeline Flow --}}
    <div class="bg-white/10 backdrop-blur-md rounded-3xl p-6 sm:p-8 border border-white/15 shadow-2xl mb-12">
      <h3 class="text-center text-xs sm:text-sm font-extrabold uppercase tracking-widest text-brand-peach mb-6">
        Integrated Learning Flow
      </h3>
      <div class="grid grid-cols-2 md:grid-cols-6 gap-3 text-center">
        <div class="flow-step bg-white/10 p-4 rounded-xl border border-white/10">
          <div class="w-8 h-8 mx-auto rounded-full bg-brand-orange text-white text-xs font-black flex items-center justify-center mb-2">1</div>
          <div class="font-extrabold text-sm text-white">Space</div>
        </div>
        <div class="flow-step bg-white/10 p-4 rounded-xl border border-white/10">
          <div class="w-8 h-8 mx-auto rounded-full bg-brand-orange text-white text-xs font-black flex items-center justify-center mb-2">2</div>
          <div class="font-extrabold text-sm text-white">Infrastructure</div>
        </div>
        <div class="flow-step bg-white/10 p-4 rounded-xl border border-white/10">
          <div class="w-8 h-8 mx-auto rounded-full bg-brand-orange text-white text-xs font-black flex items-center justify-center mb-2">3</div>
          <div class="font-extrabold text-sm text-white">Equipment</div>
        </div>
        <div class="flow-step bg-white/10 p-4 rounded-xl border border-white/10">
          <div class="w-8 h-8 mx-auto rounded-full bg-brand-orange text-white text-xs font-black flex items-center justify-center mb-2">4</div>
          <div class="font-extrabold text-sm text-white">Materials</div>
        </div>
        <div class="flow-step bg-white/10 p-4 rounded-xl border border-white/10">
          <div class="w-8 h-8 mx-auto rounded-full bg-brand-orange text-white text-xs font-black flex items-center justify-center mb-2">5</div>
          <div class="font-extrabold text-sm text-white">Activities</div>
        </div>
        <div class="flow-step bg-white/10 p-4 rounded-xl border border-white/10">
          <div class="w-8 h-8 mx-auto rounded-full bg-brand-orange text-white text-xs font-black flex items-center justify-center mb-2">6</div>
          <div class="font-extrabold text-sm text-white">Student Projects</div>
        </div>
      </div>
      <p class="text-center text-sm font-semibold text-white/90 mt-6">
        Everything comes together to create a functional environment for experiential skill education.
      </p>
    </div>

    {{-- Highlight Box & Disclaimer Note --}}
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
      <div class="md:col-span-6 bg-brand-orange/20 border border-brand-orange/40 rounded-2xl p-5 text-white space-y-2">
        <h4 class="text-base font-extrabold text-brand-peach flex items-center gap-2">
          <i class="fa-solid fa-circle-check text-brand-orange"></i> CBSE-Aligned Composite Skill Lab Solution
        </h4>
        <p class="text-xs sm:text-sm text-slate-200 leading-relaxed">
          Designed to support schools in meeting the Composite Skill Lab infrastructure and equipment requirement specified by CBSE.
        </p>
      </div>
      <div class="md:col-span-6 bg-white/5 border border-white/10 rounded-2xl p-5 text-slate-300 text-xs leading-relaxed space-y-1">
        <span class="text-slate-400 font-bold uppercase tracking-wider">Note:</span>
        <p>
          CBSE compliance is subject to the school's overall implementation, applicable CBSE norms, prescribed space requirements, equipment specifications and other requirements in force at the time. Skillvation provides the lab setup and resources designed around these requirements.
        </p>
      </div>
    </div>

  </div>
</section> -->

{{-- =====================================================================
     5. WHAT STUDENTS EXPERIENCE
     ===================================================================== --}}
<!-- <section class="py-16 lg:py-24 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
      <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-orange/10 text-brand-orange text-xs font-bold uppercase tracking-wider">
        Student-Centric Learning
      </div>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-navy">
        WHAT STUDENTS EXPERIENCE
      </h2>
      <p class="text-slate-600 text-base sm:text-lg">
        The lab moves learning beyond textbooks and classrooms. Students get opportunities to:
      </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
      
      {{-- 1. EXPLORE --}}
      <div class="feature-card bg-gradient-to-b from-blue-50/60 to-white rounded-2xl border border-blue-100 p-6 text-center space-y-3">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-blue-600 text-white flex items-center justify-center text-2xl shadow-md shadow-blue-500/20">
          <i class="fa-solid fa-compass"></i>
        </div>
        <h3 class="text-lg font-black text-brand-navy uppercase tracking-wide">EXPLORE</h3>
        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
          Understand different skill areas and real-world applications.
        </p>
      </div>

      {{-- 2. CREATE --}}
      <div class="feature-card bg-gradient-to-b from-orange-50/60 to-white rounded-2xl border border-orange-100 p-6 text-center space-y-3">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-brand-orange text-white flex items-center justify-center text-2xl shadow-md shadow-orange-500/20">
          <i class="fa-solid fa-wand-magic-sparkles"></i>
        </div>
        <h3 class="text-lg font-black text-brand-navy uppercase tracking-wide">CREATE</h3>
        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
          Work with tools, materials and guided activity kits.
        </p>
      </div>

      {{-- 3. EXPERIMENT --}}
      <div class="feature-card bg-gradient-to-b from-emerald-50/60 to-white rounded-2xl border border-emerald-100 p-6 text-center space-y-3">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-2xl shadow-md shadow-emerald-500/20">
          <i class="fa-solid fa-flask-vial"></i>
        </div>
        <h3 class="text-lg font-black text-brand-navy uppercase tracking-wide">EXPERIMENT</h3>
        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
          Test ideas, observe results and learn from the process.
        </p>
      </div>

      {{-- 4. SOLVE --}}
      <div class="feature-card bg-gradient-to-b from-purple-50/60 to-white rounded-2xl border border-purple-100 p-6 text-center space-y-3">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-purple-600 text-white flex items-center justify-center text-2xl shadow-md shadow-purple-500/20">
          <i class="fa-solid fa-puzzle-piece"></i>
        </div>
        <h3 class="text-lg font-black text-brand-navy uppercase tracking-wide">SOLVE</h3>
        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
          Apply concepts to practical problems and projects.
        </p>
      </div>

      {{-- 5. PRESENT --}}
      <div class="feature-card bg-gradient-to-b from-pink-50/60 to-white rounded-2xl border border-pink-100 p-6 text-center space-y-3">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-pink-600 text-white flex items-center justify-center text-2xl shadow-md shadow-pink-500/20">
          <i class="fa-solid fa-presentation-screen"></i>
        </div>
        <h3 class="text-lg font-black text-brand-navy uppercase tracking-wide">PRESENT</h3>
        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
          Communicate their work, demonstrate outcomes and reflect on their learning.
        </p>
      </div>

    </div>
  </div>
</section> -->

{{-- =====================================================================
     6. MULTI-DOMAIN SKILL LEARNING (3 Forms of Work)
     ===================================================================== --}}
<!-- <section class="py-16 lg:py-24 bg-slate-50 border-t border-slate-200">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
      <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-navy/10 text-brand-navy text-xs font-bold uppercase tracking-wider">
        Comprehensive Coverage
      </div>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-navy">
        MULTI-DOMAIN SKILL LEARNING
      </h2>
      <p class="text-slate-600 text-sm sm:text-base">
        The Composite Skill Lab can support activities across broad skill domains such as:
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

      {{-- 1. LIFE FORMS --}}
      <div class="feature-card bg-white rounded-3xl border border-emerald-200 shadow-sm overflow-hidden flex flex-col">
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 py-6 text-white">
          <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center text-2xl mb-3">
            <i class="fa-solid fa-seedling"></i>
          </div>
          <h3 class="text-xl font-extrabold">LIFE FORMS</h3>
          <p class="text-emerald-100 text-xs mt-1">Biology, wellness & living systems</p>
        </div>
        <div class="p-6 space-y-3 flex-1 flex flex-col justify-between">
          <div class="flex flex-wrap gap-2">
            @foreach(['Health & Wellness', 'Food', 'Food Preservation', 'Herbal Skills', 'First Aid & Hygiene'] as $tag)
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold">
              <i class="fa-solid fa-leaf text-emerald-500 text-[10px]"></i> {{ $tag }}
            </span>
            @endforeach
          </div>
          <div class="pt-4 border-t border-slate-100 text-xs text-slate-500">
            Covers essential life skills, hygiene protocols, and organic production.
          </div>
        </div>
      </div>

      {{-- 2. MATERIALS & MACHINES --}}
      <div class="feature-card bg-white rounded-3xl border border-blue-200 shadow-sm overflow-hidden flex flex-col">
        <div class="bg-gradient-to-r from-brand-navy to-blue-900 px-6 py-6 text-white">
          <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center text-2xl mb-3">
            <i class="fa-solid fa-gears"></i>
          </div>
          <h3 class="text-xl font-extrabold">MATERIALS & MACHINES</h3>
          <p class="text-blue-100 text-xs mt-1">Craftsmanship, design & technology</p>
        </div>
        <div class="p-6 space-y-3 flex-1 flex flex-col justify-between">
          <div class="flex flex-wrap gap-2">
            @foreach(['Handicrafts', 'Pottery', 'Embroidery', 'Coding', 'Design Thinking', 'Technology', 'Photography', 'Media'] as $tag)
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 border border-blue-200 text-brand-navy text-xs font-bold">
              <i class="fa-solid fa-microchip text-blue-500 text-[10px]"></i> {{ $tag }}
            </span>
            @endforeach
          </div>
          <div class="pt-4 border-t border-slate-100 text-xs text-slate-500">
            Integrates traditional craftsmanship with digital fabrication and STEM skills.
          </div>
        </div>
      </div>

      {{-- 3. HUMAN SERVICES --}}
      <div class="feature-card bg-white rounded-3xl border border-orange-200 shadow-sm overflow-hidden flex flex-col">
        <div class="bg-gradient-to-r from-brand-orange to-amber-600 px-6 py-6 text-white">
          <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center text-2xl mb-3">
            <i class="fa-solid fa-hand-holding-heart"></i>
          </div>
          <h3 class="text-xl font-extrabold">HUMAN SERVICES</h3>
          <p class="text-orange-100 text-xs mt-1">Finance, commerce & communications</p>
        </div>
        <div class="p-6 space-y-3 flex-1 flex flex-col justify-between">
          <div class="flex flex-wrap gap-2">
            @foreach(['Financial Literacy', 'Retail', 'Marketing', 'Tourism', 'Digital Citizenship', 'Communication & Application Skills'] as $tag)
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-orange-50 border border-orange-200 text-brand-orangehover text-xs font-bold">
              <i class="fa-solid fa-coins text-brand-orange text-[10px]"></i> {{ $tag }}
            </span>
            @endforeach
          </div>
          <div class="pt-4 border-t border-slate-100 text-xs text-slate-500">
            Builds entrepreneurial mindset, financial aptitude, and public service abilities.
          </div>
        </div>
      </div>

    </div>

    {{-- Bottom Alignment Note --}}
    <div class="mt-10 bg-white rounded-2xl p-5 border border-slate-200 text-center text-sm font-semibold text-slate-700 shadow-sm">
      <i class="fa-solid fa-arrows-to-dot text-brand-orange mr-2"></i>
      Activities and resources can be mapped to the school's selected skill subjects, curriculum requirements and student projects.
    </div>

  </div>
</section> -->

{{-- =====================================================================
     7. DYNAMIC KIT EXPLORER (Live Category & Component Inventory)
     ===================================================================== --}}
<section class="py-16 lg:py-20 bg-white" id="skill-sectors-section">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="text-center max-w-2xl mx-auto mb-12">
      <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-navy/10 text-brand-navy text-xs font-bold uppercase tracking-wider mb-3">
        Basic package
      </div>
      <h2 class="text-2xl sm:text-3xl font-extrabold text-brand-navy leading-tight">
        Explore Components in the Basic Skill Lab Kit
      </h2>
      <p class="mt-3 text-slate-500 text-sm sm:text-base">
        Browse the curated equipment, activity items, and consumable resources included in the Skillvation Basic Package.
      </p>
    </div>

    {{-- Loading Indicator --}}
    <div id="sks-loading" class="text-center py-16">
      <div class="inline-flex items-center gap-3 text-brand-navy font-medium text-sm">
        <i class="fa-solid fa-circle-notch fa-spin text-xl text-brand-orange"></i>
        <span>Loading kit inventory details…</span>
      </div>
    </div>

    {{-- Error State --}}
    <div id="sks-error" class="hidden max-w-md mx-auto bg-red-50 border border-red-200 text-red-700 rounded-2xl p-6 text-center">
      <i class="fa-solid fa-triangle-exclamation text-2xl mb-2 text-red-400"></i>
      <p class="text-sm font-medium">Unable to load kit details at this moment. Please refresh the page.</p>
    </div>

    {{-- Dynamic Carousel Container --}}
    <div id="sks-content" class="hidden">
      <div style="position:relative;">
        <button id="sks-box-prev" type="button" aria-label="Previous"
          class="sks-nav-btn sks-nav-prev">
          <i class="fa-solid fa-chevron-left" style="font-size:12px;"></i>
        </button>
        <button id="sks-box-next" type="button" aria-label="Next"
          class="sks-nav-btn sks-nav-next">
          <i class="fa-solid fa-chevron-right" style="font-size:12px;"></i>
        </button>

        <div style="overflow:hidden;border-radius:12px;" id="sks-carousel-viewport">
          <div id="sks-boxes"
               style="display:flex;gap:16px;transition:transform .45s cubic-bezier(.25,1,.5,1);will-change:transform;align-items:flex-start;"></div>
        </div>

        {{-- Dots pagination indicator --}}
        <div id="sks-dots" style="display:flex;justify-content:center;align-items:center;gap:8px;margin-top:18px;"></div>
      </div>

      {{-- CTA Bar inside Kit Box --}}
      <div style="margin-top:40px;border-radius:16px;background:linear-gradient(135deg,#0b2545 0%,#1e3a8a 100%);padding:28px 32px;display:flex;flex-wrap:wrap;align-items:center;gap:20px;box-shadow:0 8px 30px rgba(11,37,69,.25);">
        <div style="flex:1;min-width:220px;">
          <h4 style="font-size:17px;font-weight:800;color:#fff;margin:0 0 6px;">Need a Tailored Configuration?</h4>
          <p style="font-size:13px;color:#bfdbfe;margin:0;line-height:1.6;">Adjust quantities, add optional modules or custom skill domains to match your school's exact requirements.</p>
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:10px;">
          <a id="sks-btn-customise" href="#cta-section"
             style="display:inline-flex;align-items:center;gap:8px;padding:11px 22px;border-radius:10px;background:#f4742b;color:#fff;font-size:13px;font-weight:700;text-decoration:none;transition:all .2s;white-space:nowrap;">
            <i class="fa-solid fa-sliders" style="font-size:11px;"></i> Customise This Lab
          </a>
          <a id="" href="#cta-section"
             style="display:inline-flex;align-items:center;gap:8px;padding:11px 22px;border-radius:10px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);color:#fff;font-size:13px;font-weight:700;text-decoration:none;transition:all .2s;white-space:nowrap;">
            <i class="fa-solid fa-file-lines" style="font-size:11px;"></i> Request Proposal
          </a>
        </div>
      </div>

      {{-- Count Badge --}}
      <div style="text-align:center;margin-top:20px;">
        <span style="display:inline-flex;align-items:center;gap:8px;padding:6px 16px;border-radius:20px;background:rgba(11,37,69,.05);border:1px solid rgba(11,37,69,.1);color:#0b2545;font-size:11px;font-weight:700;">
          <i class="fa-solid fa-circle-check" style="color:#22c55e;"></i>
          <span id="sks-count">…</span> categories · <span id="sks-items">…</span> items in this lab
        </span>
      </div>
    </div>

  </div>
</section>

{{-- =====================================================================
     8. WHY SKILLVATION?
     ===================================================================== --}}
<section class="py-16 lg:py-24 bg-slate-50 border-t border-slate-200">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
      <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-orange/10 text-brand-orange text-xs font-bold uppercase tracking-wider">
        Your Trusted Implementation Partner
      </div>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-navy">
        WHY SKILLVATION?
      </h2>
      <p class="text-slate-600 text-sm sm:text-base">
        A complete, scalable, and school-tested implementation roadmap.
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

      {{-- 1. CBSE-ALIGNED --}}
      <div class="feature-card bg-white rounded-2xl border border-slate-200 p-6 space-y-3">
        <div class="w-12 h-12 rounded-xl bg-brand-navy/10 text-brand-navy flex items-center justify-center text-xl">
          <i class="fa-solid fa-award"></i>
        </div>
        <h3 class="text-base font-extrabold text-brand-navy">CBSE-ALIGNED</h3>
        <p class="text-sm text-slate-600 leading-relaxed">
          Designed around the Composite Skill Lab requirement communicated by CBSE.
        </p>
      </div>

      {{-- 2. COMPLETE SETUP --}}
      <div class="feature-card bg-white rounded-2xl border border-slate-200 p-6 space-y-3">
        <div class="w-12 h-12 rounded-xl bg-brand-orange/10 text-brand-orange flex items-center justify-center text-xl">
          <i class="fa-solid fa-boxes-packing"></i>
        </div>
        <h3 class="text-base font-extrabold text-brand-navy">COMPLETE SETUP</h3>
        <p class="text-sm text-slate-600 leading-relaxed">
          Infrastructure, tools, equipment and activity resources brought together under one solution.
        </p>
      </div>

      {{-- 3. EXPERIENTIAL LEARNING --}}
      <div class="feature-card bg-white rounded-2xl border border-slate-200 p-6 space-y-3">
        <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl">
          <i class="fa-solid fa-hands-holding-circle"></i>
        </div>
        <h3 class="text-base font-extrabold text-brand-navy">EXPERIENTIAL LEARNING</h3>
        <p class="text-sm text-slate-600 leading-relaxed">
          A learning environment focused on doing, creating, experimenting and applying.
        </p>
      </div>

      {{-- 4. SCHOOL-FRIENDLY --}}
      <div class="feature-card bg-white rounded-2xl border border-slate-200 p-6 space-y-3">
        <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-xl">
          <i class="fa-solid fa-school"></i>
        </div>
        <h3 class="text-base font-extrabold text-brand-navy">SCHOOL-FRIENDLY</h3>
        <p class="text-sm text-slate-600 leading-relaxed">
          A practical solution designed for schools looking to establish their skill lab without managing multiple suppliers.
        </p>
      </div>

      {{-- 5. SCALABLE --}}
      <div class="feature-card bg-white rounded-2xl border border-slate-200 p-6 space-y-3 md:col-span-2 lg:col-span-2">
        <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl">
          <i class="fa-solid fa-chart-line-up"></i>
        </div>
        <h3 class="text-base font-extrabold text-brand-navy">SCALABLE</h3>
        <p class="text-sm text-slate-600 leading-relaxed">
          Schools can begin with the Basic Package and enhance the lab with additional resources, equipment and advanced skill areas as their requirements evolve.
        </p>
      </div>

    </div>

  </div>
</section>

{{-- =====================================================================
     9. LAB CONFIGURATION OPTIONS (600 sq ft vs 2x400 sq ft)
     ===================================================================== --}}
<!-- <section class="py-16 lg:py-20 bg-white border-t border-slate-100">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="text-center max-w-3xl mx-auto mb-14 space-y-3">
      <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-navy/10 text-brand-navy text-xs font-bold uppercase tracking-wider">
        Flexible Room Planning
      </div>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-navy">
        Composite Skill Lab Layout Configurations
      </h2>
      <p class="text-slate-600 text-sm sm:text-base">
        Choose the space architecture specified under CBSE Circular No. Skill-75/2024 that best matches your campus.
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
      {{-- Option A --}}
      <div class="rounded-3xl border-2 border-brand-navy/20 bg-brand-bluelight/40 p-8 space-y-4 text-center hover:border-brand-navy transition-colors">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-brand-navy text-white flex items-center justify-center text-2xl shadow">
          <i class="fa-solid fa-door-closed"></i>
        </div>
        <h3 class="text-2xl font-black text-brand-navy">Option A</h3>
        <div class="text-4xl font-black text-brand-orange">600 sq. ft.</div>
        <p class="text-sm font-bold text-brand-navy">One Single Composite Skill Lab for Classes VI–XII</p>
        <p class="text-sm text-slate-600 leading-relaxed">
          A centralized, multifunctional space serving all grades (6 to 12) with configurable workstations and integrated storage.
        </p>
        <div class="inline-block px-4 py-1.5 rounded-full bg-brand-navy text-white text-xs font-bold">
          Ideal for Mid-Size Schools
        </div>
      </div>

      {{-- Option B --}}
      <div class="rounded-3xl border-2 border-brand-orange/30 bg-brand-peachlight/50 p-8 space-y-4 text-center hover:border-brand-orange transition-colors">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-brand-orange text-white flex items-center justify-center text-2xl shadow">
          <i class="fa-solid fa-door-open"></i>
        </div>
        <h3 class="text-2xl font-black text-brand-navy">Option B</h3>
        <div class="text-4xl font-black text-brand-navy">2 × 400 sq. ft.</div>
        <p class="text-sm font-bold text-brand-navy">Two Separate Specialized Skill Labs</p>
        <p class="text-sm text-slate-600 leading-relaxed">
          <strong>Lab 1 (400 sq. ft.):</strong> Classes VI–X &nbsp;|&nbsp; <strong>Lab 2 (400 sq. ft.):</strong> Classes XI–XII tailored for senior skill curricula.
        </p>
        <div class="inline-block px-4 py-1.5 rounded-full bg-brand-orange text-white text-xs font-bold">
          Ideal for Larger Campuses
        </div>
      </div>
    </div>

  </div>
</section> -->

{{-- =====================================================================
     10. CTA / CONSULTATION & BOOKING FORM
     ===================================================================== --}}
<section class="py-16 lg:py-24 bg-gradient-to-br from-brand-peachlight via-white to-brand-bluelight border-t border-slate-200" id="cta-section">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">

      {{-- Left Information --}}
      <div class="lg:col-span-6 space-y-6">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-navy text-white text-xs font-bold uppercase tracking-wider shadow-sm">
          <i class="fa-solid fa-shield-halved text-brand-orange"></i> Build With Confidence
        </div>
        
        <h2 class="text-3xl sm:text-4xl font-black text-brand-navy leading-tight">
          BUILD YOUR SCHOOL'S SKILL LAB WITH CONFIDENCE
        </h2>

        <p class="text-base sm:text-lg text-slate-700 leading-relaxed font-medium">
          The <strong class="text-brand-navy">Skillvation Basic Composite Skill Lab</strong> provides schools with a structured foundation for implementing hands-on skill education while addressing the infrastructure and equipment expectations associated with the CBSE Composite Skill Lab requirement.
        </p>

        <div class="p-5 rounded-2xl bg-brand-navy text-white space-y-2 shadow-lg">
          <p class="text-xs font-bold uppercase tracking-widest text-brand-peach">Core Mission</p>
          <p class="text-lg sm:text-xl font-extrabold text-white">
            TURN YOUR SKILL LAB INTO A SPACE WHERE STUDENTS LEARN BY DOING.
          </p>
        </div>

        <div class="space-y-3 pt-2">
          <div class="flex items-center gap-3 text-sm text-slate-700 font-bold">
            <span class="w-8 h-8 rounded-xl bg-brand-orange text-white flex items-center justify-center text-sm shadow flex-shrink-0">
              <i class="fa-solid fa-location-dot"></i>
            </span>
            <span>Book a Skill Lab Visit</span>
          </div>
          <div class="flex items-center gap-3 text-sm text-slate-700 font-bold">
            <span class="w-8 h-8 rounded-xl bg-brand-navy text-white flex items-center justify-center text-sm shadow flex-shrink-0">
              <i class="fa-solid fa-file-invoice"></i>
            </span>
            <span>Request a Detailed Package</span>
          </div>
          <div class="flex items-center gap-3 text-sm text-slate-700 font-bold">
            <span class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-sm shadow flex-shrink-0">
              <i class="fa-solid fa-user-group"></i>
            </span>
            <span>Schedule a Demo with Our Academic Team</span>
          </div>
        </div>
      </div>

      {{-- Right Form --}}
      <div class="lg:col-span-6">
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-200">
          <div class="mb-6">
            <h3 class="text-xl font-black text-brand-navy">Get Your Skill Lab Proposal</h3>
            <p class="text-xs text-slate-500 mt-1">Fill out the form below to receive detailed equipment lists and layout plans.</p>
          </div>

          <form class="space-y-4" id="planEnquiryForm" data-source="composite-skill-lab-basic" data-title="Skillvation Basic Composite Skill Lab – Enquiry">
            <input type="hidden" name="source"       id="planSource">
            <input type="hidden" name="course_title" id="planTitle">

            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">School Name *</label>
              <input type="text" name="school" required placeholder="e.g. Delhi Public School" class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm focus:border-brand-navy focus:outline-none focus:ring-2 focus:ring-brand-navy/20 transition-all" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Contact Person *</label>
                <input type="text" name="name" required placeholder="Your full name" class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm focus:border-brand-navy focus:outline-none focus:ring-2 focus:ring-brand-navy/20 transition-all" />
              </div>
              <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Designation</label>
                <input type="text" name="designation" placeholder="e.g. Principal / Academic Director" class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm focus:border-brand-navy focus:outline-none focus:ring-2 focus:ring-brand-navy/20 transition-all" />
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Phone Number *</label>
                <input type="tel" name="phone" required placeholder="+91 98765 43210" class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm focus:border-brand-navy focus:outline-none focus:ring-2 focus:ring-brand-navy/20 transition-all" />
              </div>
              <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Email Address *</label>
                <input type="email" name="email" required placeholder="principal@school.edu.in" class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm focus:border-brand-navy focus:outline-none focus:ring-2 focus:ring-brand-navy/20 transition-all" />
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">City *</label>
                <input type="text" name="city" required placeholder="e.g. Mumbai, Maharashtra" class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm focus:border-brand-navy focus:outline-none focus:ring-2 focus:ring-brand-navy/20 transition-all" />
              </div>
              <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Request Type *</label>
                <select name="message" required class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm focus:border-brand-navy focus:outline-none focus:ring-2 focus:ring-brand-navy/20 transition-all">
                  <option value="Request a Detailed Package">Request a Detailed Package</option>
                  <option value="Book a Skill Lab Visit">Book a Skill Lab Visit</option>
                  <option value="Schedule a Demo with Academic Team">Schedule a Demo with Academic Team</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Message / Space Available</label>
              <textarea name="address" rows="3" placeholder="Tell us about your space dimensions (e.g. 600 sq ft) or specific skill areas of interest..." class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm focus:border-brand-navy focus:outline-none focus:ring-2 focus:ring-brand-navy/20 transition-all"></textarea>
            </div>

            <button type="submit" id="planEnquiryBtn" class="w-full py-4 rounded-xl bg-brand-orange hover:bg-brand-orangehover text-white text-sm font-bold shadow-lg shadow-brand-orange/20 transition-all duration-200">
              Request Callback
            </button>

            <p class="text-xs text-center text-slate-400 mt-2">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:-1px;"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              Your information is confidential. No spam.
            </p>
          </form>

          <div id="planEnquirySuccess" style="display:none;" class="text-center py-8">
            <div class="text-5xl mb-4">🎉</div>
            <h3 class="text-xl font-black text-brand-navy mb-2">Thank you!</h3>
            <p class="text-sm text-slate-600">Our Skill Lab specialist will contact you within 1 working day with your detailed Basic Package proposal.</p>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

{{-- =====================================================================
     11. FREQUENTLY ASKED QUESTIONS
     ===================================================================== --}}
<section class="py-16 lg:py-20 bg-white" id="faq">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="text-center mb-12 space-y-3">
      <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-navy/10 text-brand-navy text-xs font-bold uppercase tracking-wider">
        <span>★</span> Frequently Asked Questions
      </div>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-navy">
        Questions school management usually ask
      </h2>
      <p class="text-slate-500 text-sm max-w-2xl mx-auto">Everything you need to know about setup, CBSE affiliation guidelines, and the Basic package.</p>
    </div>

    <div class="vl-faq-carousel" id="faqCarousel">

      {{-- Slide 1 --}}
      <div class="vl-faq-slide active">
        <div class="vl-faq-grid">
          <div class="vl-faq-card">
            <div class="vl-faq-card__q">What is a Composite Skill Lab?</div>
            <div class="vl-faq-card__divider"></div>
            <div class="vl-faq-card__a">A Composite Skill Lab is an experiential multidisciplinary learning space where students gain hands-on proficiency in Robotics, Coding, AI, Electronics, and Design Thinking through structured curriculum projects.</div>
          </div>
          <div class="vl-faq-card">
            <div class="vl-faq-card__q">Is it mandatory for CBSE affiliated schools?</div>
            <div class="vl-faq-card__divider"></div>
            <div class="vl-faq-card__a">CBSE and NEP 2020 guidelines strongly emphasize setting up Composite Skill Labs for 21st-century skill education. Skillvation ensures your lab satisfies all affiliation inspection norms.</div>
          </div>
          <div class="vl-faq-card">
            <div class="vl-faq-card__q">What lab tiers does Skillvation offer?</div>
            <div class="vl-faq-card__divider"></div>
            <div class="vl-faq-card__a">
              <ul>
                <li><strong>Basic:</strong> Core infrastructure, robotics &amp; coding tools.</li>
                <li><strong>Advance:</strong> Expanded composite layout with AI &amp; IoT stations.</li>
                <li><strong>Premium:</strong> Dual labs with advanced robotics, 3D printing &amp; dedicated support.</li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      {{-- Slide 2 --}}
      <div class="vl-faq-slide">
        <div class="vl-faq-grid">
          <div class="vl-faq-card">
            <div class="vl-faq-card__q">What is included in the Skillvation Basic Package?</div>
            <div class="vl-faq-card__divider"></div>
            <div class="vl-faq-card__a">The Basic Package includes core lab infrastructure &amp; furniture, essential multi-domain tools &amp; equipment, student activity kits and materials, teacher demonstration resources, project-based learning guides, and organized storage.</div>
          </div>
          <div class="vl-faq-card">
            <div class="vl-faq-card__q">What are the lab space options allowed by CBSE?</div>
            <div class="vl-faq-card__divider"></div>
            <div class="vl-faq-card__a">CBSE allows two configurations: (1) One single Composite Skill Lab of 600 sq. ft. for Classes VI–XII, or (2) Two separate labs of 400 sq. ft. each — one for Classes VI–X and one for Classes XI–XII.</div>
          </div>
          <div class="vl-faq-card">
            <div class="vl-faq-card__q">How does the Basic Package support skill domains?</div>
            <div class="vl-faq-card__divider"></div>
            <div class="vl-faq-card__a">The setup supports all three core forms of work defined by CBSE/NCF-SE: Life Forms (food, health, herbal skills), Materials &amp; Machines (coding, robotics, pottery, crafts, media), and Human Services (finance, retail, tourism, communications).</div>
          </div>
        </div>
      </div>

      {{-- Slide 3 --}}
      <div class="vl-faq-slide">
        <div class="vl-faq-grid">
          <div class="vl-faq-card">
            <div class="vl-faq-card__q">How do we select the right package for our campus?</div>
            <div class="vl-faq-card__divider"></div>
            <div class="vl-faq-card__a">The package depends on your student strength, room dimensions, budget, and timetable structure. Our academic advisors offer a free on-site spatial survey to guide your selection.</div>
          </div>
          <div class="vl-faq-card">
            <div class="vl-faq-card__q">Can the lab layout and equipment be customized?</div>
            <div class="vl-faq-card__divider"></div>
            <div class="vl-faq-card__a">Yes, absolutely. We customize furniture layout, computer stations, safety provisions, and project kit quantities according to your exact room size and student batch counts.</div>
          </div>
          <div class="vl-faq-card">
            <div class="vl-faq-card__q">Can we upgrade or customize the Basic Package later?</div>
            <div class="vl-faq-card__divider"></div>
            <div class="vl-faq-card__a">Yes, the Basic Package is modular and scalable. Schools can start with the foundational setup and seamlessly add advanced equipment, AI/Robotics modules, or specialized vocation kits over time.</div>
          </div>
        </div>
      </div>

      {{-- Slide 4 --}}
      <div class="vl-faq-slide">
        <div class="vl-faq-grid">
          <div class="vl-faq-card">
            <div class="vl-faq-card__q">Are student DIY project kits provided?</div>
            <div class="vl-faq-card__divider"></div>
            <div class="vl-faq-card__a">Yes. We supply modular, reusable STEM &amp; robotics kits with step-by-step guides so students can build tangible working models across physics, computing, and mechanics.</div>
          </div>
          <div class="vl-faq-card">
            <div class="vl-faq-card__q">How is teacher training conducted?</div>
            <div class="vl-faq-card__divider"></div>
            <div class="vl-faq-card__a">We conduct an intensive 3 to 5-day on-site training certification for your science and computer faculty, backed by video tutorials, lesson plans, and scheduled term refreshers.</div>
          </div>
          <div class="vl-faq-card">
            <div class="vl-faq-card__q">What happens after installation — are we supported?</div>
            <div class="vl-faq-card__divider"></div>
            <div class="vl-faq-card__a">No school is left on their own. Annual preventive maintenance, software patches, fast spare parts replacement, and ongoing pedagogy support are bundled for the full agreement period.</div>
          </div>
        </div>
      </div>

      {{-- Navigation Controls --}}
      <div class="vl-faq-nav">
        <button class="vl-faq-btn" id="faqPrev" aria-label="Previous questions" disabled>
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
        </button>
        <div class="vl-faq-dots" id="faqDots">
          <button class="vl-faq-dot active" data-slide="0" aria-label="Slide 1"></button>
          <button class="vl-faq-dot" data-slide="1" aria-label="Slide 2"></button>
          <button class="vl-faq-dot" data-slide="2" aria-label="Slide 3"></button>
          <button class="vl-faq-dot" data-slide="3" aria-label="Slide 4"></button>
        </div>
        <span class="vl-faq-counter" id="faqCounter">1 / 4</span>
        <button class="vl-faq-btn" id="faqNext" aria-label="Next questions">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </button>
      </div>

    </div>
  </div>
</section>

{{-- =====================================================================
     12. OTHER LABS
     ===================================================================== --}}
<section class="py-16 bg-slate-50 border-t border-slate-200">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-10">
      <h2 class="text-2xl sm:text-3xl font-extrabold text-brand-navy mb-2">Explore Our Innovation Labs</h2>
      <p class="text-slate-500 text-sm">Comprehensive hands-on STEM &amp; Skill solutions for modern schools.</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
      @foreach([
        [route('labs.composite-skill-advance'),        'https://images.unsplash.com/photo-1635070041078-e363dbe005cb?auto=format&fit=crop&w=500&q=80', 'Advance Package', 'Early childhood exploration & creativity'],
        [route('labs.composite-skill-premium'), 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=500&q=80', 'Premium Package', 'Hands-on robotics, coding & AI experiments'],
        [route('labs.stem'),        'https://images.unsplash.com/photo-1516339901601-2e1b62dc0c45?auto=format&fit=crop&w=500&q=80', 'Skillvation Lab', 'Electronics, IoT & project-based learning'],
      ] as [$url, $img, $name, $desc])
      <a href="{{ $url }}" class="group flex flex-col gap-3 p-4 rounded-2xl border border-slate-200 hover:border-brand-orange hover:shadow-lg transition-all bg-white">
        <div class="aspect-[4/3] rounded-xl overflow-hidden bg-slate-100">
          <img src="{{ $img }}" alt="{{ $name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
        </div>
        <div>
          <p class="font-bold text-brand-navy group-hover:text-brand-orange transition-colors text-sm">{{ $name }}</p>
          <p class="text-xs text-slate-500 mt-0.5">{{ $desc }}</p>
        </div>
      </a>
      @endforeach
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script>
// ── Plan Enquiry Form AJAX ────────────────────────────────
(function () {
  var form    = document.getElementById('planEnquiryForm');
  var btn     = document.getElementById('planEnquiryBtn');
  var success = document.getElementById('planEnquirySuccess');
  var srcEl   = document.getElementById('planSource');
  var ttlEl   = document.getElementById('planTitle');

  if (!form) return;

  // Populate hidden fields from data attributes
  if (srcEl) srcEl.value = form.dataset.source  || '';
  if (ttlEl) ttlEl.value = form.dataset.title   || '';

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var originalText   = btn.textContent;
    btn.disabled       = true;
    btn.textContent    = 'Submitting…';

    fetch('{{ route("course.enquiry.store") }}', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        'Accept': 'application/json',
      },
      body: new FormData(form),
    })
    .then(function (r) { return r.json(); })
    .then(function () {
      form.style.display    = 'none';
      success.style.display = 'block';
    })
    .catch(function () {
      form.style.display    = 'none';
      success.style.display = 'block';
    })
    .finally(function () {
      btn.disabled    = false;
      btn.textContent = originalText;
    });
  });
}());
</script>
<script>
(function () {
  var slides  = document.querySelectorAll('#faqCarousel .vl-faq-slide');
  var dots    = document.querySelectorAll('#faqDots .vl-faq-dot');
  var prevBtn = document.getElementById('faqPrev');
  var nextBtn = document.getElementById('faqNext');
  var counter = document.getElementById('faqCounter');
  var total   = slides.length;
  var current = 0;

  function goTo(idx) {
    if (idx < 0 || idx >= total) return;
    slides[current].classList.remove('active');
    dots[current].classList.remove('active');
    current = idx;
    slides[current].classList.add('active');
    dots[current].classList.add('active');
    if (counter) counter.textContent = (current + 1) + ' / ' + total;
    if (prevBtn) prevBtn.disabled = current === 0;
    if (nextBtn) nextBtn.disabled = current === total - 1;
  }

  if (prevBtn) { prevBtn.disabled = true; prevBtn.addEventListener('click', function () { goTo(current - 1); }); }
  if (nextBtn) { nextBtn.disabled = total <= 1; nextBtn.addEventListener('click', function () { goTo(current + 1); }); }
  dots.forEach(function (dot, i) { dot.addEventListener('click', function () { goTo(i); }); });
}());
</script>
<script>
(function(){
'use strict';

var BASE = (function() {
  var path = window.location.pathname;
  var sub = path.indexOf('/skillvation.comphp') === 0 ? '/skillvation.comphp/club-shop' : '/club-shop';
  return window.location.origin + sub;
})();

var THEMES = [
  { keys:['life science','life form','biology','agriculture','food','health'],
    hdrBg:'#059669', border:'#a7f3d0', bg:'#f0fdf4', dotColor:'#059669', hoverBg:'#dcfce7', shadow:false },
  { keys:['human service','healthcare service','finance','service','retail','tourism'],
    hdrBg:'#ea580c', border:'#fed7aa', bg:'#fff7ed', dotColor:'#ea580c', hoverBg:'#ffedd5', shadow:false },
  { keys:['machine','material','technology','tech','stem','electr','robot','coding','craft'],
    hdrBg:'#28246f', border:'#bfdbfe', bg:'#eff6ff', dotColor:'#28246f', hoverBg:'#dbeafe', shadow:false },
  { keys:['infra','construct','civil','storage','furniture'],
    hdrBg:'#d97706', border:'#fde68a', bg:'#fffbeb', dotColor:'#d97706', hoverBg:'#fef3c7', shadow:false },
];
var DEFAULT_THEME = { hdrBg:'#28246f', border:'#bfdbfe', bg:'#eff6ff', dotColor:'#28246f', hoverBg:'#dbeafe', shadow:false };

function getTheme(parentName){
  var n = (parentName||'').toLowerCase();
  for(var i=0;i<THEMES.length;i++)
    if(THEMES[i].keys.some(function(k){ return n.includes(k); })) return THEMES[i];
  return DEFAULT_THEME;
}

var ICONS=[
  [['food','kitchen','cup','spoon','bowl','whisk','spatula','rolling','measur','cook','bak'],'fa-utensils'],
  [['agri','farm','crop','soil'],'fa-leaf'],
  [['horti','plant','nursery'],'fa-tree'],
  [['animal','livestock','dairy'],'fa-cow'],
  [['sustain','eco','recycle'],'fa-recycle'],
  [['health','medical','first-aid','first aid','clinic','aid kit'],'fa-heart-pulse'],
  [['beauty','wellness','spa'],'fa-spa'],
  [['retail','shop','store'],'fa-shop'],
  [['bank','financ','bfsi'],'fa-landmark'],
  [['robot','ai','automat'],'fa-robot'],
  [['electron','ece','circuit'],'fa-bolt'],
  [['cod','program','it ','ites','software','data'],'fa-desktop'],
  [['carpent','wood'],'fa-hammer'],
  [['design','innovat'],'fa-pen-ruler'],
  [['apparel','fashion','stitch'],'fa-shirt'],
  [['photo','media','film','video','avgc'],'fa-camera'],
  [['avion','drone','aeronaut'],'fa-plane'],
  [['ar &','vr','3d print','virtual','augment'],'fa-vr-cardboard'],
  [['potter','craft','clay'],'fa-paint-brush'],
  [['infra','build','construct'],'fa-building'],
  [['tour','hospitality'],'fa-map-location-dot'],
  [['activity'],'fa-star'],
];
function getIcon(n){
  n=(n||'').toLowerCase();
  for(var i=0;i<ICONS.length;i++)
    if(ICONS[i][0].some(function(k){ return n.includes(k); })) return ICONS[i][1];
  return 'fa-cube';
}

function esc(s){ return String(s??'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function rupee(n){ return '₹\u202F'+Number(n).toLocaleString('en-IN',{minimumFractionDigits:0,maximumFractionDigits:2}); }

function openRow(row){
  var body = row.querySelector('.sks-body');
  var chev = row.querySelector('.sks-chev');
  if(!body) return;
  var wrap = row.closest('[data-sks-rows]');
  if(wrap) wrap.querySelectorAll('.sks-row[data-open="1"]').forEach(function(r){
    if(r===row) return;
    r.dataset.open='0';
    var b=r.querySelector('.sks-body'), c=r.querySelector('.sks-chev');
    if(b) b.style.maxHeight='0';
    if(c) c.style.transform='rotate(0deg)';
  });
  body.style.maxHeight = (body.scrollHeight + 300) + 'px';
  if(chev) chev.style.transform='rotate(180deg)';
  row.dataset.open='1';
}
function closeRow(row){
  var body=row.querySelector('.sks-body'), chev=row.querySelector('.sks-chev');
  if(body) body.style.maxHeight='0';
  if(chev) chev.style.transform='rotate(0deg)';
  row.dataset.open='0';
}

function buildRow(catName, comps, theme, productUrl, isOpenByDefault){
  var icon  = getIcon(catName);
  var rowId = 'sks-r-'+Math.random().toString(36).slice(2,8);
  var totalQ=0, totalV=0;
  comps.forEach(function(c){
    var q = c.is_optional ? 0 : Number(c.required_quantity || 0);
    totalQ += q;
    totalV += Number(c.unit_price) * q;
  });
  var subtitle = totalQ > 0
    ? rupee(totalV) + ' · ' + totalQ + ' unit' + (totalQ !== 1 ? 's' : '')
    : comps.length + ' item' + (comps.length !== 1 ? 's' : '') + ' (optional)';

  var itemRows = comps.map(function(c){
    var qty    = c.is_optional ? 0 : Number(c.required_quantity || 0);
    var line   = Number(c.unit_price) * qty;
    var opt    = c.is_optional
      ? '<span style="margin-left:5px;padding:1px 5px;border-radius:3px;background:#fef3c7;color:#92400e;border:1px solid #fde68a;font-size:9px;font-weight:700;">optional</span>'
      : '';
    var rawImg = c.image_small || c.image || '';
    var imgSrc = '';
    if(rawImg){
      if(rawImg.startsWith('http://') || rawImg.startsWith('https://') || rawImg.startsWith('/')){
        imgSrc = rawImg;
      } else {
        imgSrc = BASE + '/' + (rawImg.startsWith('uploads/') ? rawImg : 'uploads/images/' + rawImg);
      }
    }
    var img = imgSrc
      ? '<img src="'+esc(imgSrc)+'" alt="'+esc(c.title)+'" loading="lazy" '
          +'style="width:36px;height:36px;object-fit:cover;border-radius:6px;border:1px solid #e2e8f0;flex-shrink:0;" '
          +'onerror="this.style.display=\'none\';">'
      : '<span style="width:36px;height:36px;border-radius:6px;background:#f1f5f9;border:1px solid #e2e8f0;flex-shrink:0;display:inline-flex;align-items:center;justify-content:center;">'
          +'<i class="fa-solid fa-cube" style="color:#cbd5e1;font-size:12px;"></i></span>';

    return '<div style="display:flex;align-items:center;gap:12px;padding:10px 14px;border-bottom:1px solid #f1f5f9;">'
      +img
      +'<div style="flex:1;min-width:0;">'
        +'<div style="font-size:13px;font-weight:600;color:#1e293b;line-height:1.3;">'+esc(c.title)+opt+'</div>'
        +(c.sku?'<div style="font-size:10.5px;color:#94a3b8;font-family:monospace;margin-top:2px;">'+esc(c.sku)+'</div>':'')
      +'</div>'
      +'<div style="text-align:right;flex-shrink:0;">'
        +'<div style="font-size:12.5px;font-weight:700;color:'+(qty===0?'#94a3b8':theme.dotColor)+';">'+(qty===0?'—':rupee(line))+'</div>'
        +'<div style="font-size:10.5px;color:#94a3b8;">'+(qty===0?'0 (optional)':qty+' × '+rupee(c.unit_price))+'</div>'
      +'</div>'
    +'</div>';
  }).join('');

  var initialOpen = isOpenByDefault ? '1' : '0';
  var initialMaxHeight = isOpenByDefault ? 'max-height:800px;' : 'max-height:0;';
  var initialChev = isOpenByDefault ? 'transform:rotate(180deg);' : 'transform:rotate(0deg);';

  return '<div class="sks-row" id="'+rowId+'" data-open="'+initialOpen+'" '
      +'style="border-bottom:1px solid rgba(0,0,0,.06);cursor:pointer;" '
      +'data-hover="'+esc(theme.hoverBg)+'">'
    +'<button type="button" '
        +'onclick="(function(){var r=document.getElementById(\''+rowId+'\');r.dataset.open===\'1\'?sksClose(r):sksOpen(r);})()" '
        +'style="width:100%;display:flex;align-items:center;gap:12px;padding:12px 16px;background:transparent;border:none;cursor:pointer;text-align:left;transition:background .15s;">'
      +'<span style="width:34px;height:34px;border-radius:50%;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">'
        +'<i class="fa-solid '+icon+'" style="font-size:13px;color:'+theme.dotColor+';"></i>'
      +'</span>'
      +'<div style="flex:1;min-width:0;">'
        +'<div style="font-size:14px;font-weight:600;color:#1e293b;">'+esc(catName)+'</div>'
        +'<div style="font-size:11px;color:#94a3b8;margin-top:2px;">'+subtitle+'</div>'
      +'</div>'
      +'<i class="fa-solid fa-chevron-down sks-chev" style="font-size:11px;color:#94a3b8;transition:transform .25s;flex-shrink:0;'+initialChev+'"></i>'
    +'</button>'
    +'<div class="sks-body" style="'+initialMaxHeight+'overflow:hidden;transition:max-height .35s ease;">'
      +itemRows
      +(totalQ>0?'<div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:#f8fafc;border-top:1px solid #e2e8f0;">'
          +'<span style="font-size:11.5px;font-weight:700;color:#64748b;">'+comps.length+' item'+(comps.length!==1?'s':'')+' · '+totalQ+' units</span>'
          +'<span style="font-size:13.5px;font-weight:800;color:'+theme.dotColor+';">'+rupee(totalV)+'</span>'
        +'</div>':'')
    +'</div>'
  +'</div>';
}

window.sksOpen  = openRow;
window.sksClose = closeRow;

function bootHover(root){
  root.querySelectorAll('.sks-row').forEach(function(row){
    var timer=null, btn=row.querySelector('button'), body=row.querySelector('.sks-body');
    var hoverBg=row.getAttribute('data-hover')||'#f1f5f9';
    row.addEventListener('mouseenter',function(){
      clearTimeout(timer); if(btn) btn.style.background=hoverBg;
    });
    row.addEventListener('mouseleave',function(){
      if(btn) btn.style.background='transparent';
    });
  });
}

function buildBox(parentName, parentId, subGroups, theme, productUrl, isCenter){
  var el = document.createElement('div');
  el.className = 'sks-box-card';
  el.style.cssText = 'border:2px solid '+theme.border+';background:'+theme.bg+';'
    +'overflow:hidden;border-radius:12px;box-sizing:border-box;'
    +(theme.shadow?'box-shadow:0 8px 24px rgba(0,0,0,.08);':'');

  var catNames = Object.keys(subGroups);

  var hdr = '<div style="background:'+theme.hdrBg+';padding:13px 18px;display:flex;align-items:center;gap:10px;">'
    +'<i class="fa-solid '+getIcon(parentName)+'" style="color:#fff;font-size:16px;"></i>'
    +'<span style="font-weight:700;color:#fff;font-size:15.5px;">'+esc(parentName)+'</span>'
    +'<span style="margin-left:auto;background:rgba(255,255,255,.22);color:#fff;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;">'
      +catNames.length+' categor'+(catNames.length!==1?'ies':'y')
    +'</span>'
  +'</div>';

  var rows = '<div data-sks-rows>'
    + catNames.map(function(cn, idx){ return buildRow(cn, subGroups[cn], theme, productUrl, idx === 0); }).join('')
  + '</div>';

  el.innerHTML = hdr + rows;
  bootHover(el);
  return el;
}

var loadEl    = document.getElementById('sks-loading');
var errEl     = document.getElementById('sks-error');
var contentEl = document.getElementById('sks-content');
var boxesEl   = document.getElementById('sks-boxes');
var countEl   = document.getElementById('sks-count');
var itemsEl   = document.getElementById('sks-items');

Promise.all([
  fetch(BASE+'/api/products/slug/basic-skill-1').then(function(r){if(!r.ok)throw r.status;return r.json();}),
  fetch(BASE+'/api/categories/tree').then(function(r){if(!r.ok)throw r.status;return r.json();}),
])
.then(function(res){
  var product    = res[0].data;
  var allCats    = res[1].data || [];
  var components = product.bundle_components || [];
  if(!components.length) throw new Error('empty bundle');

  var tree = {};
  allCats.forEach(function(c){ tree[c.id]=c; });

  var productUrl   = product.url || (BASE+'/'+product.slug);
  var customiseUrl = (BASE+'/basic-cart') || productUrl;

  var groups   = {};
  var usedCats = {};
  var totalItems = 0;

  components.forEach(function(c){
    var catId  = Number(c.category_id);
    var catNode= tree[catId] || {id:catId, name:c.category_name||'General', parent_id:0, parent_name:''};
    var parentId, parentName;

    if(!catNode.parent_id || catNode.parent_id===0){
      parentId   = catId;
      parentName = catNode.name;
    } else {
      parentId   = catNode.parent_id;
      var pNode  = tree[parentId] || {};
      parentName = pNode.name || catNode.parent_name || 'General';
    }

    if(!groups[parentId]) groups[parentId]={ name:parentName, subGroups:{} };
    var subCat = c.category_name || catNode.name || 'General';
    if(!groups[parentId].subGroups[subCat]) groups[parentId].subGroups[subCat]=[];
    groups[parentId].subGroups[subCat].push(c);
    usedCats[(subCat).toLowerCase()] = true;
    totalItems++;
  });

  var parentIds = Object.keys(groups);
  var totalCats = parentIds.length;

  var maxItems  = 0, maxKey = parentIds[0];
  parentIds.forEach(function(pid){
    var count=Object.values(groups[pid].subGroups).reduce(function(s,a){return s+a.length;},0);
    if(count>maxItems){ maxItems=count; maxKey=pid; }
  });

  if(countEl) countEl.textContent = totalCats;
  if(itemsEl) itemsEl.textContent = totalItems;
  var btnC=document.getElementById('sks-btn-customise'), btnP=document.getElementById('sks-btn-product');
  if(btnC) btnC.href = customiseUrl;
  if(btnP) btnP.href = productUrl;

  // Unhide content container BEFORE building cards and measuring layout
  if(loadEl) loadEl.classList.add('hidden');
  if(contentEl) contentEl.classList.remove('hidden');

  boxesEl.innerHTML = '';
  parentIds.forEach(function(pid){
    var g      = groups[pid];
    var theme  = getTheme(g.name);
    var isCenter = (pid===maxKey && parentIds.length>1);
    var box    = buildBox(g.name, pid, g.subGroups, theme, productUrl, isCenter);
    boxesEl.appendChild(box);
  });

  (function(){
    var GAP = 16;
    var current = 0;
    var boxes = Array.prototype.slice.call(boxesEl.children);
    var total = boxes.length;
    var prevBtn = document.getElementById('sks-box-prev');
    var nextBtn = document.getElementById('sks-box-next');
    var dotsContainer = document.getElementById('sks-dots');
    var viewport = document.getElementById('sks-carousel-viewport');

    function getVisibleCount(){
      var w = window.innerWidth;
      if (w < 640) return 1;
      if (w < 1024) return Math.min(2, total);
      return Math.min(3, total);
    }

    function getContainerW(){
      var w = 0;
      if (viewport && viewport.clientWidth > 0) {
        w = viewport.clientWidth;
      } else if (contentEl && contentEl.clientWidth > 0) {
        w = contentEl.clientWidth;
      } else if (boxesEl.parentElement && boxesEl.parentElement.clientWidth > 0) {
        w = boxesEl.parentElement.clientWidth;
      } else {
        w = window.innerWidth - 32;
      }
      return w;
    }

    function getCardW(){
      var visible = getVisibleCount();
      var containerW = getContainerW();
      var totalGap = GAP * (visible - 1);
      var w = Math.floor((containerW - totalGap) / visible);
      return Math.max(240, w);
    }

    function applyWidths(){
      var w = getCardW();
      boxes.forEach(function(b){
        b.style.width     = w + 'px';
        b.style.minWidth  = w + 'px';
        b.style.maxWidth  = w + 'px';
        b.style.flex      = '0 0 ' + w + 'px';
      });
    }

    function renderDots(){
      if (!dotsContainer) return;
      dotsContainer.innerHTML = '';
      var visible = getVisibleCount();
      var maxIdx = Math.max(0, total - visible);
      var dotCount = maxIdx + 1;
      if (dotCount <= 1) {
        dotsContainer.style.display = 'none';
        return;
      }
      dotsContainer.style.display = 'flex';
      for (var i = 0; i < dotCount; i++) {
        var dot = document.createElement('span');
        dot.className = 'sks-dot' + (i === current ? ' active' : '');
        dot.setAttribute('data-idx', i);
        dot.addEventListener('click', (function(idx){
          return function(){ go(idx); };
        })(i));
        dotsContainer.appendChild(dot);
      }
    }

    function updateDots(){
      if (!dotsContainer) return;
      var dots = dotsContainer.querySelectorAll('.sks-dot');
      dots.forEach(function(d, i){
        if (i === current) {
          d.classList.add('active');
        } else {
          d.classList.remove('active');
        }
      });
    }

    function go(idx){
      var visible = getVisibleCount();
      var maxIdx = Math.max(0, total - visible);
      if (maxIdx === 0) {
        current = 0;
      } else if (idx > maxIdx) {
        current = 0; // Infinite loop wrap-around
      } else if (idx < 0) {
        current = maxIdx; // Infinite loop wrap-around to end
      } else {
        current = idx;
      }

      var w = getCardW();
      boxesEl.style.transform = 'translateX(-' + (current * (w + GAP)) + 'px)';
      updateDots();
    }

    requestAnimationFrame(function(){
      applyWidths();
      renderDots();
      go(0);
    });

    if (prevBtn) {
      prevBtn.addEventListener('click', function(){ go(current - 1); });
    }
    if (nextBtn) {
      nextBtn.addEventListener('click', function(){ go(current + 1); });
    }

    // Touch & swipe support for mobile
    var touchStartX = 0;
    var touchEndX = 0;
    var touchStartY = 0;
    var touchEndY = 0;

    if (viewport) {
      viewport.addEventListener('touchstart', function(e){
        touchStartX = e.changedTouches[0].screenX;
        touchStartY = e.changedTouches[0].screenY;
      }, {passive: true});

      viewport.addEventListener('touchend', function(e){
        touchEndX = e.changedTouches[0].screenX;
        touchEndY = e.changedTouches[0].screenY;
        var diffX = touchStartX - touchEndX;
        var diffY = touchStartY - touchEndY;
        if (Math.abs(diffX) > 35 && Math.abs(diffX) > Math.abs(diffY)) {
          if (diffX > 0) {
            go(current + 1); // Swiped left -> next
          } else {
            go(current - 1); // Swiped right -> prev
          }
        }
      }, {passive: true});
    }

    var resizeTimer;
    window.addEventListener('resize', function(){
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(function(){
        applyWidths();
        renderDots();
        go(current);
      }, 80);
    });
  })();
})
.catch(function(e){
  console.log('sks notice (offline/static fallback):', e);
  if(loadEl) loadEl.classList.add('hidden');
  if(errEl) errEl.classList.remove('hidden');
});

})();
</script>
@endpush