@extends('frontend.home-four.layouts.master')

@section('meta_title', 'Skill 2 Skool — ' . config('app.name', 'Skillvation'))
@section('meta_description', 'Equip your students with future ready skills that matter. Aligned with NEP 2020, NCF 2023, SAFAL, and SQAAF.')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
  :root {
    --primary: #1976d2;
    --primary-dark: #115293;
    --on-surface: #1f2937;
    --on-surface-variant: #4b5563;
    --surface-container: #f9fafb;
    --outline: #e5e7eb;
  }

  .s2s-page {
    font-family: 'Plus Jakarta Sans', sans-serif;
    color: var(--on-surface);
    background: #ffffff;
    overflow-x: hidden;
  }

  /* ── feature cards ── */
  .feature-card { transition: transform .3s ease, box-shadow .3s ease; }
  .feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px -5px rgba(0,0,0,.1), 0 8px 10px -6px rgba(0,0,0,.1);
  }

  /* ── blob image shape ── */
  .blob-shape { border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; overflow: hidden; }

  /* ── curved divider ── */
  .curved-divider { position:relative; background:#f9fafb; height:100px; overflow:hidden; }
  .curved-divider::before {
    content:''; position:absolute; top:0; left:0; width:100%; height:100%;
    background:#fff; border-radius:0 0 50% 50% / 0 0 100% 100%;
  }

  /* ── CAMPS letters ── */
  .camps-wrap {
    display: flex;
    align-items: stretch;
    justify-content: center;
    gap: 0;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(0,0,0,.12);
  }
  .camps-col {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    padding: 28px 14px 24px;
    color: #fff;
    text-align: center;
    position: relative;
    min-height: 320px;
  }
  .camps-letter {
    font-size: clamp(3.5rem, 6vw, 5.5rem);
    font-weight: 900;
    line-height: 1;
    text-shadow: 0 3px 14px rgba(0,0,0,.22);
    margin-bottom: 8px;
  }
  .camps-icon { font-size: 32px; margin-bottom: 8px; }
  .camps-title { font-size: 14px; font-weight: 800; margin-bottom: 6px; line-height: 1.3; }
  .camps-desc  { font-size: 11.5px; opacity: .88; line-height: 1.55; }

  /* ── page layout helpers ── */
  .s2s-container { max-width: 1280px; margin: 0 auto; padding: 0 24px; }
  @media(min-width:768px){ .s2s-container { padding: 0 32px; } }

  /* ── responsive grids ── */
  .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; align-items: center; }
  @media(max-width:768px){ .grid-2 { grid-template-columns: 1fr; gap: 2rem; } }
  .grid-4 { display: grid; grid-template-columns: repeat(4,1fr); gap: 1.5rem; }
  @media(max-width:900px){ .grid-4 { grid-template-columns: 1fr 1fr; } }
  @media(max-width:500px){ .grid-4 { grid-template-columns: 1fr; } }
  .grid-2col { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; }
  @media(max-width:640px){ .grid-2col { grid-template-columns: 1fr; } }

  @media(max-width:768px){
    .camps-wrap { flex-direction: column; border-radius: 12px; }
    .camps-col  { min-height: 160px; }
  }
</style>
@endpush

@section('contents')
<div class="s2s-page">

{{-- ══════════════════════════════════════════════
     1. HERO
══════════════════════════════════════════════ --}}
<section style="position:relative;padding-top:3rem;padding-bottom:6rem;overflow:hidden;background-color:#e0f0ff;">
  {{-- background image --}}
  <div style="position:absolute;inset:0;z-index:0;opacity:.2;">
    <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDUYC7eKIiyFoTvzMIfyJR8x7NqwZo5tvtU2ADBk4nccg1GlzO0BAm2zAnt9B4qNf_DDXo2xBmVop1d1PwqzS4w514XX2htixzf3vq9Mk_tgoNTmGMa0X12fr5Hj9RWfiPIQTxC7e066S-3Z4Yf8U0RgejE_B-5NrOPQdxQ34dkoPUKhyMExAm5cycdNLB-sdsLtkAOnoCLONen2k7RnwK62zccGPlwMlQ0qpX-_PfS5xU6XW0au6ER1A"
         alt="" style="width:100%;height:100%;object-fit:cover;">
  </div>
  <div style="position:absolute;inset:0;background:linear-gradient(to bottom,transparent,#1976d2);z-index:0;"></div>

  <div class="s2s-container" style="position:relative;z-index:10;text-align:center;color:#fff;margin-top:6rem;">
    <h1 style="font-size:clamp(1.9rem,4vw,3rem);font-weight:800;margin-bottom:1.5rem;line-height:1.2;">
      Equip Your Students With Future Ready Skills<br>That Matter
    </h1>
    <p style="font-size:clamp(1rem,1.4vw,1.2rem);max-width:56rem;margin:0 auto;color:#fff;opacity:.92;line-height:1.78;">
      Camps enables schools to deliver new-age skill education aligned with NEP 2020, NCF 2023, SAFAL, and
      SQAAF, ensuring students are prepared not just for assessments—but for life beyond the classroom. Our skill
      ecosystem is designed to complement academics while strengthening competency, confidence, and real-world readiness.
    </p>
  </div>
</section>

{{-- ═════════════════════════════════════════════════════════════════════
     1.5 MAKING FOUNDATIONAL SKILL EDUCATION SMARTER WITH LATAA OS
     ═════════════════════════════════════════════════════════════════════ --}}
<!-- BEGIN: LATAA OS Foundational Skill Education Section -->
<section class="py-20 lg:py-28 bg-gradient-to-br from-slate-950 via-[#0a1630] to-slate-950 text-white relative overflow-hidden border-y border-cyan-500/20">
  <!-- Ambient background glow elements -->
  <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full bg-cyan-500/15 blur-3xl pointer-events-none"></div>
  <div class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full bg-blue-600/15 blur-3xl pointer-events-none"></div>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    
    <!-- Section Header -->
    <div class="text-center max-w-3xl mx-auto mb-14">
      <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cyan-500/10 border border-cyan-400/30 text-cyan-400 text-xs font-bold uppercase tracking-widest shadow-sm mb-4">
        <i class="fa-solid fa-microchip"></i> Foundational Skill Education &bull; LATAA OS
      </div>
      <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight mb-5 leading-tight">
        Making Foundational Skill Education <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-sky-300 to-blue-400">Smarter and More Effective</span>
      </h2>
      <p class="text-slate-300 text-base sm:text-lg leading-relaxed mb-3">
        <strong>Skill2Skool</strong> brings the CAMPS framework into everyday school learning through structured activities, experiential learning and practical skill development for Grades 1 to 5.
      </p>
      <p class="text-cyan-300/90 text-base sm:text-lg font-medium leading-relaxed m-0">
        <strong>LATAA OS</strong> strengthens this programme by providing AI-powered academic assistance to teachers, helping them plan, prepare and deliver meaningful CAMPS learning experiences.
      </p>
    </div>

    <!-- Infographic Sub-Badge -->
    <div class="text-center mb-10">
      <div class="inline-flex items-center gap-2.5 px-5 py-2 rounded-full bg-slate-900/80 border border-cyan-500/30 text-cyan-300 text-xs font-bold uppercase tracking-wider shadow-inner">
        <i class="fa-solid fa-layer-group text-cyan-400"></i> How LATAA OS Strengthens CAMPS
      </div>
    </div>

    <!-- 5 CAMPS Framework Infographic Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5 mb-12">
      
      <!-- C - Communication -->
      <div class="group relative p-6 rounded-2xl bg-slate-900/70 border border-red-500/20 hover:border-red-400/60 transition-all duration-300 shadow-xl hover:-translate-y-1.5 hover:shadow-red-500/10 flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 flex items-center justify-center text-xl shadow-xs group-hover:scale-110 group-hover:bg-red-500/20 transition-all">
              <i class="fa-solid fa-comments"></i>
            </div>
            <span class="text-xs font-black px-2.5 py-1 rounded-md bg-red-500/10 text-red-300 border border-red-500/20">C</span>
          </div>
          <h3 class="text-base font-bold text-white mb-2 group-hover:text-red-300 transition-colors">Communication</h3>
          <p class="text-slate-400 text-xs leading-relaxed m-0">Supports teachers with speaking activities, vocabulary exercises, storytelling and presentation ideas.</p>
        </div>
      </div>

      <!-- A - Art & Craft -->
      <div class="group relative p-6 rounded-2xl bg-slate-900/70 border border-purple-500/20 hover:border-purple-400/60 transition-all duration-300 shadow-xl hover:-translate-y-1.5 hover:shadow-purple-500/10 flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-purple-500/10 border border-purple-500/30 text-purple-400 flex items-center justify-center text-xl shadow-xs group-hover:scale-110 group-hover:bg-purple-500/20 transition-all">
              <i class="fa-solid fa-palette"></i>
            </div>
            <span class="text-xs font-black px-2.5 py-1 rounded-md bg-purple-500/10 text-purple-300 border border-purple-500/20">A</span>
          </div>
          <h3 class="text-base font-bold text-white mb-2 group-hover:text-purple-300 transition-colors">Art &amp; Craft</h3>
          <p class="text-slate-400 text-xs leading-relaxed m-0">Assists in developing creative activities, craft ideas and hands-on learning experiences.</p>
        </div>
      </div>

      <!-- M - Math for Real Life -->
      <div class="group relative p-6 rounded-2xl bg-slate-900/70 border border-blue-500/20 hover:border-blue-400/60 transition-all duration-300 shadow-xl hover:-translate-y-1.5 hover:shadow-blue-500/10 flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/30 text-blue-400 flex items-center justify-center text-xl shadow-xs group-hover:scale-110 group-hover:bg-blue-500/20 transition-all">
              <i class="fa-solid fa-calculator"></i>
            </div>
            <span class="text-xs font-black px-2.5 py-1 rounded-md bg-blue-500/10 text-blue-300 border border-blue-500/20">M</span>
          </div>
          <h3 class="text-base font-bold text-white mb-2 group-hover:text-blue-300 transition-colors">Math for Real Life</h3>
          <p class="text-slate-400 text-xs leading-relaxed m-0">Helps create practical mathematical activities connected to everyday situations.</p>
        </div>
      </div>

      <!-- P - Personality Development -->
      <div class="group relative p-6 rounded-2xl bg-slate-900/70 border border-emerald-500/20 hover:border-emerald-400/60 transition-all duration-300 shadow-xl hover:-translate-y-1.5 hover:shadow-emerald-500/10 flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center justify-center text-xl shadow-xs group-hover:scale-110 group-hover:bg-emerald-500/20 transition-all">
              <i class="fa-solid fa-star"></i>
            </div>
            <span class="text-xs font-black px-2.5 py-1 rounded-md bg-emerald-500/10 text-emerald-300 border border-emerald-500/20">P</span>
          </div>
          <h3 class="text-base font-bold text-white mb-2 group-hover:text-emerald-300 transition-colors">Personality Development</h3>
          <p class="text-slate-400 text-xs leading-relaxed m-0">Supports activities focusing on confidence, values, teamwork, decision-making and social skills.</p>
        </div>
      </div>

      <!-- S - Science & STEM -->
      <div class="group relative p-6 rounded-2xl bg-slate-900/70 border border-amber-500/20 hover:border-amber-400/60 transition-all duration-300 shadow-xl hover:-translate-y-1.5 hover:shadow-amber-500/10 flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-400 flex items-center justify-center text-xl shadow-xs group-hover:scale-110 group-hover:bg-amber-500/20 transition-all">
              <i class="fa-solid fa-flask"></i>
            </div>
            <span class="text-xs font-black px-2.5 py-1 rounded-md bg-amber-500/10 text-amber-300 border border-amber-500/20">S</span>
          </div>
          <h3 class="text-base font-bold text-white mb-2 group-hover:text-amber-300 transition-colors">Science &amp; STEM</h3>
          <p class="text-slate-400 text-xs leading-relaxed m-0">Assists teachers with experiments, activity ideas, models and project-based learning resources.</p>
        </div>
      </div>

    </div>

    <!-- Active Transformation Infographic Banner: Empowering Teachers -->
    <div class="p-8 sm:p-10 rounded-3xl bg-gradient-to-r from-slate-900/90 via-[#0d1d3d] to-slate-900/90 border border-cyan-500/30 shadow-2xl relative">
      <div class="text-center mb-8">
        <span class="text-xs font-bold uppercase tracking-widest text-cyan-400">Intelligent Teaching Assistance</span>
        <h3 class="text-2xl sm:text-3xl font-extrabold text-white mt-1">Empowering Teachers. Enriching Student Experiences.</h3>
        <p class="text-slate-300 text-sm mt-2 max-w-2xl mx-auto">LATAA OS supports teachers with practical, AI-assisted tools built directly for foundational classrooms:</p>
      </div>

      <!-- 5 Teacher Support Points Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        
        <!-- Support Point 1 -->
        <div class="flex flex-col items-center text-center p-4 rounded-2xl bg-white/5 border border-white/10 hover:border-cyan-400/40 transition-colors group">
          <div class="w-11 h-11 rounded-xl bg-cyan-500/10 border border-cyan-400/30 text-cyan-300 flex items-center justify-center text-lg mb-3 group-hover:scale-110 transition-transform">
            <i class="fa-solid fa-file-lines"></i>
          </div>
          <p class="text-slate-200 text-xs font-medium leading-relaxed m-0">AI-assisted lesson plans and teaching scripts.</p>
        </div>

        <!-- Support Point 2 -->
        <div class="flex flex-col items-center text-center p-4 rounded-2xl bg-white/5 border border-white/10 hover:border-sky-400/40 transition-colors group">
          <div class="w-11 h-11 rounded-xl bg-sky-500/10 border border-sky-400/30 text-sky-300 flex items-center justify-center text-lg mb-3 group-hover:scale-110 transition-transform">
            <i class="fa-solid fa-bullseye"></i>
          </div>
          <p class="text-slate-200 text-xs font-medium leading-relaxed m-0">Activity ideas aligned with CAMPS learning objectives.</p>
        </div>

        <!-- Support Point 3 -->
        <div class="flex flex-col items-center text-center p-4 rounded-2xl bg-white/5 border border-white/10 hover:border-indigo-400/40 transition-colors group">
          <div class="w-11 h-11 rounded-xl bg-indigo-500/10 border border-indigo-400/30 text-indigo-300 flex items-center justify-center text-lg mb-3 group-hover:scale-110 transition-transform">
            <i class="fa-solid fa-book-open"></i>
          </div>
          <p class="text-slate-200 text-xs font-medium leading-relaxed m-0">Teaching-learning materials and worksheets.</p>
        </div>

        <!-- Support Point 4 -->
        <div class="flex flex-col items-center text-center p-4 rounded-2xl bg-white/5 border border-white/10 hover:border-emerald-400/40 transition-colors group">
          <div class="w-11 h-11 rounded-xl bg-emerald-500/10 border border-emerald-400/30 text-emerald-300 flex items-center justify-center text-lg mb-3 group-hover:scale-110 transition-transform">
            <i class="fa-solid fa-clipboard-check"></i>
          </div>
          <p class="text-slate-200 text-xs font-medium leading-relaxed m-0">Age-appropriate quizzes and assessments.</p>
        </div>

        <!-- Support Point 5 -->
        <div class="flex flex-col items-center text-center p-4 rounded-2xl bg-white/5 border border-white/10 hover:border-purple-400/40 transition-colors group">
          <div class="w-11 h-11 rounded-xl bg-purple-500/10 border border-purple-400/30 text-purple-300 flex items-center justify-center text-lg mb-3 group-hover:scale-110 transition-transform">
            <i class="fa-solid fa-shapes"></i>
          </div>
          <p class="text-slate-200 text-xs font-medium leading-relaxed m-0">Structured classroom activity resources.</p>
        </div>

      </div>

      <!-- Narrative & Outcome Footnote -->
      <div class="mt-8 pt-6 border-t border-white/10 flex flex-col lg:flex-row items-center justify-between gap-6 text-center lg:text-left">
        <div class="space-y-1.5 max-w-3xl">
          <p class="text-sm sm:text-base text-slate-200 leading-relaxed m-0">
            With <strong>LATAA OS</strong>, Skill2Skool ensures that teachers have the exact guidance, creative resources, and day-to-day intelligence to make foundational skill learning joyful and impactful.
          </p>
        </div>
        <a href="{{ route('lataa') }}" class="inline-flex items-center gap-2.5 px-7 py-3.5 rounded-full bg-gradient-to-r from-cyan-400 to-blue-500 hover:from-cyan-300 hover:to-blue-400 text-slate-950 font-extrabold text-sm transition-all duration-300 shadow-lg shadow-cyan-500/25 flex-shrink-0 hover:scale-105">
          <span>Explore LATAA OS</span>
          <i class="fa-solid fa-arrow-right text-xs"></i>
        </a>
      </div>

    </div>

  </div>
</section>
<!-- END: LATAA OS Foundational Skill Education Section -->

{{-- ══════════════════════════════════════════════
     2. IMPORTANCE OF THE CURRICULUM (image)
══════════════════════════════════════════════ --}}
<section style="padding:0 24px;margin-top:4rem;position:relative;z-index:20;margin-bottom:4rem;">
  <div style="max-width:1280px;margin:0 auto;">
    <div style="border-radius:1rem;overflow:hidden;box-shadow:0 25px 50px -12px rgba(0,0,0,.25);background:#fff;padding:8px;">
      <img src="{{asset('frontend/img/skillbox/curriculum_1176f84f.jpeg')}}"
           alt="Importance of the Curriculum"
           style="width:100%;height:1015px;object-fit:cover;border-radius:.5rem;display:block;">
    </div>
    <div style="text-align:center;margin-top:2rem;">
      <a href="{{ route('contact.index') }}"
         style="display:inline-block;background:#1976d2;color:#fff;padding:.75rem 2rem;border-radius:9999px;font-weight:600;font-size:.95rem;text-decoration:none;box-shadow:0 4px 14px rgba(25,118,210,.35);transition:background .2s;"
         onmouseover="this.style.background='#115293'"
         onmouseout="this.style.background='#1976d2'">
        Request For Demo
      </a>
    </div>
  </div>
</section>

{{-- ══════════════════════════════════════════════
     3. FRAMEWORK — 4 cards
══════════════════════════════════════════════ --}}
<section style="padding:4rem 0;">
  <div class="s2s-container" style="text-align:center;">
    <p style="color:var(--primary);font-weight:600;margin-bottom:.5rem;font-size:.875rem;text-transform:uppercase;letter-spacing:.06em;">
      Camps Skill Framework
    </p>
    <h2 style="font-size:clamp(1.5rem,2.8vw,2.25rem);font-weight:800;color:var(--on-surface);margin-bottom:1rem;">
      Structured. Aligned. Measurable
    </h2>
    <p style="color:var(--on-surface-variant);max-width:42rem;margin:0 auto 3rem;line-height:1.7;">
      Camps provides schools with a structured skill framework that focuses on
    </p>

    <div class="grid-4" style="text-align:left;">

      <div class="feature-card" style="background:var(--surface-container);border-radius:.75rem;padding:1.5rem;border:1px solid rgba(229,231,235,.3);display:flex;flex-direction:column;">
        <div style="width:3rem;height:3rem;background:#dbeafe;border-radius:50%;display:flex;align-items:center;justify-content:center;margin-bottom:1rem;color:var(--primary);">
          <svg style="width:1.5rem;height:1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
        </div>
        <h3 style="font-weight:700;font-size:1.1rem;margin-bottom:.5rem;color:var(--on-surface);">Competency-based learning</h3>
        <p style="font-size:.875rem;color:var(--on-surface-variant);flex:1;line-height:1.6;margin:0;">We provide simple, practical learning tools that help teachers upgrade skills and stay confident in the classroom.</p>
      </div>

      <div class="feature-card" style="background:var(--surface-container);border-radius:.75rem;padding:1.5rem;border:1px solid rgba(229,231,235,.3);display:flex;flex-direction:column;">
        <div style="width:3rem;height:3rem;background:#dbeafe;border-radius:50%;display:flex;align-items:center;justify-content:center;margin-bottom:1rem;color:var(--primary);">
          <svg style="width:1.5rem;height:1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
        </div>
        <h3 style="font-weight:700;font-size:1.1rem;margin-bottom:.5rem;color:var(--on-surface);">Experiential and application-oriented pedagogy</h3>
        <p style="font-size:.875rem;color:var(--on-surface-variant);flex:1;line-height:1.6;margin:0;">Our structured courses, expert guidance, and real-world strategies support every stage of a teacher's professional growth.</p>
      </div>

      <div class="feature-card" style="background:var(--surface-container);border-radius:.75rem;padding:1.5rem;border:1px solid rgba(229,231,235,.3);display:flex;flex-direction:column;">
        <div style="width:3rem;height:3rem;background:#dbeafe;border-radius:50%;display:flex;align-items:center;justify-content:center;margin-bottom:1rem;color:var(--primary);">
          <svg style="width:1.5rem;height:1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
        </div>
        <h3 style="font-weight:700;font-size:1.1rem;margin-bottom:.5rem;color:var(--on-surface);">Observable and documentable student outcomes</h3>
        <p style="font-size:.875rem;color:var(--on-surface-variant);flex:1;line-height:1.6;margin:0;">Track student progress with structured observation tools that make learning outcomes visible and reportable.</p>
      </div>

      <div class="feature-card" style="background:var(--surface-container);border-radius:.75rem;padding:1.5rem;border:1px solid rgba(229,231,235,.3);display:flex;flex-direction:column;">
        <div style="width:3rem;height:3rem;background:#dbeafe;border-radius:50%;display:flex;align-items:center;justify-content:center;margin-bottom:1rem;color:var(--primary);">
          <svg style="width:1.5rem;height:1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
        </div>
        <h3 style="font-weight:700;font-size:1.1rem;margin-bottom:.5rem;color:var(--on-surface);">Seamless integration into existing school systems</h3>
        <p style="font-size:.875rem;color:var(--on-surface-variant);flex:1;line-height:1.6;margin:0;">Designed to complement your school's current schedule, curriculum, and processes without disruption.</p>
      </div>

    </div>
  </div>
</section>

{{-- ══════════════════════════════════════════════
     4. STRENGTHS — unchanged
══════════════════════════════════════════════ --}}
<section style="padding:4rem 0;text-align:center;">
  <div class="s2s-container">
    <p style="color:var(--primary);font-weight:600;margin-bottom:.5rem;font-size:.875rem;text-transform:uppercase;letter-spacing:.06em;">Skill Readiness &amp; Student Mapping</p>
    <h2 style="font-size:clamp(1.5rem,3vw,2.25rem);font-weight:700;margin-bottom:1.5rem;color:var(--on-surface);">Understanding Strengths Beyond Marks</h2>
    <p style="color:var(--on-surface-variant);max-width:48rem;margin:0 auto;font-size:1.1rem;line-height:1.78;">
      Camps supports schools in identifying and nurturing student strengths through structured observation,
      reflection, and skill mapping. By focusing on aptitude, interest, and behaviour patterns, schools gain deeper
      insights into student potential—supporting informed guidance, confidence building, and holistic development.
    </p>
  </div>
</section>

{{-- ══════════════════════════════════════════════
     5. CAMPS — colorful letter blocks
══════════════════════════════════════════════ --}}
<section style="padding:1rem 0 4rem;">
  <div class="s2s-container">

    <div style="text-align:center;margin-bottom:2rem;">
      <p style="color:var(--primary);font-weight:600;margin-bottom:.5rem;font-size:.875rem;text-transform:uppercase;letter-spacing:.06em;">Our Core Framework</p>
      <h2 style="font-size:clamp(1.5rem,3vw,2.25rem);font-weight:800;color:var(--on-surface);margin-bottom:.75rem;">
        CAMPS FRAMEWORK
      </h2>
      <p style="color:var(--on-surface-variant);max-width:40rem;margin:0 auto;line-height:1.7;">
        Our CAMPS framework structures every learning experience around 5 core developmental areas.
      </p>
    </div>

    <div class="camps-wrap">
      {{-- C --}}
      <div class="camps-col" style="background:#e53935;">
        <div class="camps-icon">💬</div>
        <div class="camps-letter">C</div>
        <div class="camps-title">Communication</div>
        <div class="camps-desc">Speaking, listening, vocabulary, expression and confidence.</div>
      </div>
      {{-- A --}}
      <div class="camps-col" style="background:#8e24aa;">
        <div class="camps-icon">🎨</div>
        <div class="camps-letter">A</div>
        <div class="camps-title">Art &amp; Craft</div>
        <div class="camps-desc">Creativity, imagination, fine motor skills and self-expression.</div>
      </div>
      {{-- M --}}
      <div class="camps-col" style="background:#1e88e5;">
        <div class="camps-icon">🔢</div>
        <div class="camps-letter">M</div>
        <div class="camps-title">Math for Real Life</div>
        <div class="camps-desc">Numbers, measurement, estimation, money and patterns.</div>
      </div>
      {{-- P --}}
      <div class="camps-col" style="background:#43a047;">
        <div class="camps-icon">🌟</div>
        <div class="camps-letter">P</div>
        <div class="camps-title">Personality Development</div>
        <div class="camps-desc">Values, teamwork, empathy, confidence and leadership.</div>
      </div>
      {{-- S --}}
      <div class="camps-col" style="background:#fb8c00;">
        <div class="camps-icon">🔬</div>
        <div class="camps-letter">S</div>
        <div class="camps-title">Science &amp; STEM</div>
        <div class="camps-desc">Observation, experimentation, exploration and logical thinking.</div>
      </div>
    </div>

  </div>
</section>

{{-- ══════════════════════════════════════════════
     6. COURSES CAROUSEL
══════════════════════════════════════════════ --}}
<section style="padding:1rem 0 4rem;background:var(--surface-container);">
  <div class="s2s-container">
    <div style="text-align:center;margin-bottom:2.5rem;">
      <p style="color:var(--primary);font-weight:600;margin-bottom:.5rem;font-size:.875rem;text-transform:uppercase;letter-spacing:.06em;">Skill Readiness &amp; Student Mapping</p>
      <h2 style="font-size:clamp(1.5rem,3vw,2.25rem);font-weight:700;color:var(--on-surface);">Start Learning What Matters</h2>
    </div>

    <!-- CAMPS Complete 5-Grade Programme (Single Card) -->
    <div class="mb-12 max-w-5xl mx-auto rounded-3xl bg-gradient-to-br from-slate-900 via-[#0a1630] to-slate-900 text-white p-8 md:p-10 shadow-2xl border border-cyan-500/30 relative overflow-hidden" id="camps-package-card">
      <!-- Decorative background glow -->
      <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full bg-cyan-500/15 blur-3xl pointer-events-none"></div>
      <div class="absolute -bottom-24 -left-24 w-80 h-80 rounded-full bg-blue-600/15 blur-3xl pointer-events-none"></div>

      <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-8">
        <div class="lg:w-2/3 space-y-4 text-left">
          <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-cyan-500/20 border border-cyan-400/40 text-cyan-300 text-xs font-extrabold uppercase tracking-wider">
            <i class="fa-solid fa-layer-group"></i> All 5 Grades Included &bull; Single School Package &bull; CAMPS
          </div>
          <h3 class="text-2xl sm:text-3xl font-black text-white tracking-tight leading-snug">
            CAMPS Complete Programme <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-sky-300 to-blue-400">(Grades 1 to 5)</span>
          </h3>
          <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
            All five grades are included together in the single complete CAMPS school package. Enrolling equips your entire primary wing with structured foundational CAMPS skill curriculum, LATAA-OS AI teacher assistant, worksheets, and competency assessments.
          </p>

          <!-- 5-Grades Preview Strip inside the Single Card -->
          <div class="pt-2">
            <div class="text-xs font-bold text-cyan-300 uppercase tracking-wider mb-2.5">
              <i class="fa-solid fa-graduation-cap me-1"></i> Grades Included in this Package:
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 text-center">
              <a href="{{ url('/course-detail/grade-1') }}" class="p-2.5 rounded-xl bg-white/5 border border-white/10 hover:border-cyan-400 hover:bg-white/10 transition-all block group cursor-pointer">
                <span class="block text-xs font-black text-white group-hover:text-cyan-300 transition-colors">Grade 1</span>
                <span class="block text-[10px] text-slate-400">Foundational</span>
                <span class="block text-[9px] text-cyan-400/90 font-semibold mt-1 group-hover:underline">View Details <i class="fa-solid fa-arrow-right text-[8px] ms-0.5"></i></span>
              </a>
              <a href="{{ url('/course-detail/grade-2') }}" class="p-2.5 rounded-xl bg-white/5 border border-white/10 hover:border-cyan-400 hover:bg-white/10 transition-all block group cursor-pointer">
                <span class="block text-xs font-black text-white group-hover:text-cyan-300 transition-colors">Grade 2</span>
                <span class="block text-[10px] text-slate-400">Experiential</span>
                <span class="block text-[9px] text-cyan-400/90 font-semibold mt-1 group-hover:underline">View Details <i class="fa-solid fa-arrow-right text-[8px] ms-0.5"></i></span>
              </a>
              <a href="{{ url('/course-detail/grade-3') }}" class="p-2.5 rounded-xl bg-white/5 border border-white/10 hover:border-cyan-400 hover:bg-white/10 transition-all block group cursor-pointer">
                <span class="block text-xs font-black text-white group-hover:text-cyan-300 transition-colors">Grade 3</span>
                <span class="block text-[10px] text-slate-400">Cognitive</span>
                <span class="block text-[9px] text-cyan-400/90 font-semibold mt-1 group-hover:underline">View Details <i class="fa-solid fa-arrow-right text-[8px] ms-0.5"></i></span>
              </a>
              <a href="{{ url('/course-detail/grade-4') }}" class="p-2.5 rounded-xl bg-white/5 border border-white/10 hover:border-cyan-400 hover:bg-white/10 transition-all block group cursor-pointer">
                <span class="block text-xs font-black text-white group-hover:text-cyan-300 transition-colors">Grade 4</span>
                <span class="block text-[10px] text-slate-400">Analytical</span>
                <span class="block text-[9px] text-cyan-400/90 font-semibold mt-1 group-hover:underline">View Details <i class="fa-solid fa-arrow-right text-[8px] ms-0.5"></i></span>
              </a>
              <a href="{{ url('/course-detail/grade-5') }}" class="p-2.5 rounded-xl bg-white/5 border border-white/10 hover:border-cyan-400 hover:bg-white/10 transition-all block group cursor-pointer col-span-2 sm:col-span-1">
                <span class="block text-xs font-black text-white group-hover:text-cyan-300 transition-colors">Grade 5</span>
                <span class="block text-[10px] text-slate-400">Advanced</span>
                <span class="block text-[9px] text-cyan-400/90 font-semibold mt-1 group-hover:underline">View Details <i class="fa-solid fa-arrow-right text-[8px] ms-0.5"></i></span>
              </a>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-2 text-xs sm:text-sm text-slate-200">
            <div class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-cyan-400"></i> Grade 1 to Grade 5 Full Access</div>
            <div class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-cyan-400"></i> NEP 2020 &amp; NCF 2023 Aligned</div>
            <div class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-cyan-400"></i> LATAA-OS AI Lesson Plans</div>
            <div class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-cyan-400"></i> Activity Kits &amp; Assessment Worksheets</div>
          </div>
        </div>

        <div class="lg:w-1/3 w-full flex flex-col items-center justify-center p-6 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm text-center">
          <span class="text-xs uppercase font-bold text-slate-300 tracking-wider">Complete Package Price</span>
          <div class="text-3xl sm:text-4xl font-black text-white mt-1 mb-1">
            ₹25,000
          </div>
          <span class="text-xs text-cyan-300 mb-5 font-semibold">Includes All 5 Grades (Single Purchase)</span>
          
          <button type="button" onclick="buySkill2SchoolBundle(event)" id="s2s-bundle-btn" class="w-full py-3.5 px-6 rounded-full bg-gradient-to-r from-cyan-400 to-blue-500 hover:from-cyan-300 hover:to-blue-400 text-slate-950 font-black text-sm transition-all duration-300 shadow-lg shadow-cyan-500/25 active:scale-95 flex items-center justify-center gap-2">
            <i class="fa-solid fa-bolt"></i>
            <span>Buy Now</span>
          </button>
          <span class="text-[11px] text-slate-400 mt-2.5 font-medium">( CAMPS Grade 1 to 5)</span>
        </div>
      </div>
    </div>

    <!-- ══════════════════════════════════════════════
         3 CREDIT PRODUCTS FOR SALE
    ══════════════════════════════════════════════ -->
    <div class="mt-20 pt-16 border-t border-slate-200/80" id="skill2school-ai-credits">
      <div class="text-center max-w-3xl mx-auto mb-12">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cyan-100/80 border border-cyan-300 text-cyan-800 text-xs font-black uppercase tracking-wider mb-3 shadow-sm">
          <i class="fa-solid fa-coins text-cyan-600"></i> AI Teacher LATAA-OS Credits
        </div>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-3">
          Create My Curriculum
        </h2>
        <p class="text-slate-600 text-base leading-relaxed">
          Fuel your classroom with LATAA-OS credits for instant NEP-aligned lesson plans, automated student assessment rubrics, and dynamic printable skill worksheets.
        </p>
      </div>

      <div id="s2s-integrate-products-grid" class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto items-stretch">
        
        <!-- Product 1: 10 Credits (Free) -->
        <div class="relative flex flex-col justify-between rounded-3xl bg-white border border-slate-200/90 p-8 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
          <div>
            <div class="flex items-center justify-between mb-4">
              <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200">
                <i class="fa-solid fa-gift text-emerald-500"></i> FREE TRIAL
              </span>
              <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Product 1</span>
            </div>

            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-400 to-teal-500 flex items-center justify-center text-white text-2xl shadow-md mb-5">
              <i class="fa-solid fa-wand-magic-sparkles"></i>
            </div>

            <h3 class="text-xl font-bold text-slate-900 mb-1">Product 1</h3>
            <p class="text-xs text-slate-500 mb-4">Starter Pack &bull; Free Trial for Teachers</p>

            <div class="flex items-baseline gap-2 mb-2 pb-4 border-b border-slate-100">
              <span class="text-4xl font-black text-slate-900">₹0</span>
              <span class="text-sm font-semibold text-emerald-600 uppercase">Free</span>
            </div>

            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-slate-50 border border-slate-200/80 text-slate-800 font-extrabold text-sm mb-6 w-full">
              <i class="fa-solid fa-coins text-amber-500"></i>
              <span>Credits: <strong class="text-emerald-600 text-base">10</strong></span>
            </div>

            <!-- Features Infographics List -->
            <ul class="space-y-3 text-xs sm:text-sm text-slate-600 mb-8">
              <li class="flex items-start gap-2.5">
                <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5 text-sm"></i>
                <span><strong>10 AI Generation Credits</strong> for trial</span>
              </li>
              <li class="flex items-start gap-2.5">
                <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5 text-sm"></i>
                <span>Instant LATAA OS Lesson Plans</span>
              </li>
              <li class="flex items-start gap-2.5">
                <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5 text-sm"></i>
                <span>Sample Student Skill Assessment</span>
              </li>
              <li class="flex items-start gap-2.5">
                <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5 text-sm"></i>
                <span>No Payment Required &bull; Instant Access</span>
              </li>
            </ul>
          </div>

          <div>
            <button type="button" onclick="buyCreditProduct(10, event)" id="credit-btn-10" class="w-full py-3.5 px-6 rounded-2xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold text-sm transition-all duration-200 shadow-md shadow-emerald-600/20 flex items-center justify-center gap-2">
              <i class="fa-solid fa-gift"></i>
              <span>Buy Now (Free)</span>
            </button>
            <span class="block text-center text-[11px] text-slate-400 mt-2 font-medium">Free instant activation</span>
          </div>
        </div>

        <!-- Product 2: 100 Credits (₹100) - Most Popular -->
        <div class="relative flex flex-col justify-between rounded-3xl bg-gradient-to-b from-slate-900 to-[#0e1e3e] text-white p-8 shadow-2xl border-2 border-cyan-400 hover:border-cyan-300 transition-all duration-300 hover:-translate-y-2">
          <!-- Popular ribbon badge -->
          <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-4 py-1 rounded-full bg-gradient-to-r from-cyan-400 to-blue-500 text-slate-950 font-black text-[11px] uppercase tracking-wider shadow-md">
            ★ Most Popular
          </div>

          <div>
            <div class="flex items-center justify-between mb-4 mt-1">
              <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-cyan-500/20 text-cyan-300 text-xs font-bold border border-cyan-400/40">
                <i class="fa-solid fa-bolt text-cyan-300"></i> STANDARD PACK
              </span>
              <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Product 2</span>
            </div>

            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-cyan-400 to-blue-600 flex items-center justify-center text-white text-2xl shadow-lg mb-5 shadow-cyan-500/30">
              <i class="fa-solid fa-cubes-stacked"></i>
            </div>

            <h3 class="text-xl font-bold text-white mb-1">Product 2</h3>
            <p class="text-xs text-slate-300 mb-4">Standard School Pack &bull; Regular Use</p>

            <div class="flex items-baseline gap-2 mb-2 pb-4 border-b border-white/10">
              <span class="text-4xl font-black text-white">₹100</span>
              <span class="text-xs font-medium text-slate-300">/ 100 Credits (₹1/credit)</span>
            </div>

            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white/10 border border-cyan-400/30 text-white font-extrabold text-sm mb-6 w-full">
              <i class="fa-solid fa-coins text-amber-400"></i>
              <span>Credits: <strong class="text-cyan-300 text-base">100</strong></span>
            </div>

            <!-- Features Infographics List -->
            <ul class="space-y-3 text-xs sm:text-sm text-slate-200 mb-8">
              <li class="flex items-start gap-2.5">
                <i class="fa-solid fa-circle-check text-cyan-400 mt-0.5 text-sm"></i>
                <span><strong>100 Full AI Generation Credits</strong></span>
              </li>
              <li class="flex items-start gap-2.5">
                <i class="fa-solid fa-circle-check text-cyan-400 mt-0.5 text-sm"></i>
                <span>CAMPS Curriculum with LATAA-OS across Grades 1-5</span>
              </li>
              <li class="flex items-start gap-2.5">
                <i class="fa-solid fa-circle-check text-cyan-400 mt-0.5 text-sm"></i>
                <span>Printable Student Assessment Worksheets</span>
              </li>
              <li class="flex items-start gap-2.5">
                <i class="fa-solid fa-circle-check text-cyan-400 mt-0.5 text-sm"></i>
                <span>Classroom Mapping &amp; Evaluation Rubrics</span>
              </li>
            </ul>
          </div>

          <div>
            <button type="button" onclick="buyCreditProduct(100, event)" id="credit-btn-100" class="w-full py-3.5 px-6 rounded-2xl bg-gradient-to-r from-cyan-400 to-blue-500 hover:from-cyan-300 hover:to-blue-400 active:scale-95 text-slate-950 font-black text-sm transition-all duration-200 shadow-lg shadow-cyan-500/30 flex items-center justify-center gap-2">
              <i class="fa-solid fa-bolt"></i>
              <span>Buy Now</span>
            </button>
            <span class="block text-center text-[11px] text-slate-400 mt-2 font-medium">Instant checkout &bull; ₹100</span>
          </div>
        </div>

        <!-- Product 3: 1000 Credits (₹1,000) - Pro Pack -->
        <div class="relative flex flex-col justify-between rounded-3xl bg-white border border-slate-200/90 p-8 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
          <div>
            <div class="flex items-center justify-between mb-4">
              <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-purple-50 text-purple-700 text-xs font-bold border border-purple-200">
                <i class="fa-solid fa-crown text-purple-600"></i> BEST VALUE
              </span>
              <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Product 3</span>
            </div>

            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-purple-500 to-indigo-600 flex items-center justify-center text-white text-2xl shadow-md mb-5">
              <i class="fa-solid fa-gem"></i>
            </div>

            <h3 class="text-xl font-bold text-slate-900 mb-1">Product 3</h3>
            <p class="text-xs text-slate-500 mb-4">Enterprise Pack &bull; Annual Primary Wing</p>

            <div class="flex items-baseline gap-2 mb-2 pb-4 border-b border-slate-100">
              <span class="text-4xl font-black text-slate-900">₹1,000</span>
              <span class="text-xs font-medium text-slate-500">/ 1000 Credits</span>
            </div>

            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-slate-50 border border-slate-200/80 text-slate-800 font-extrabold text-sm mb-6 w-full">
              <i class="fa-solid fa-coins text-amber-500"></i>
              <span>Credits: <strong class="text-purple-600 text-base">1000</strong></span>
            </div>

            <!-- Features Infographics List -->
            <ul class="space-y-3 text-xs sm:text-sm text-slate-600 mb-8">
              <li class="flex items-start gap-2.5">
                <i class="fa-solid fa-circle-check text-purple-600 mt-0.5 text-sm"></i>
                <span><strong>1,000 High-Volume AI Credits</strong></span>
              </li>
              <li class="flex items-start gap-2.5">
                <i class="fa-solid fa-circle-check text-purple-600 mt-0.5 text-sm"></i>
                <span>Full Primary Wing Multi-Teacher Access</span>
              </li>
              <li class="flex items-start gap-2.5">
                <i class="fa-solid fa-circle-check text-purple-600 mt-0.5 text-sm"></i>
                <span>Unlimited Student Activity Generation</span>
              </li>
              <li class="flex items-start gap-2.5">
                <i class="fa-solid fa-circle-check text-purple-600 mt-0.5 text-sm"></i>
                <span>Priority AI Processing &amp; Custom Rubrics</span>
              </li>
            </ul>
          </div>

          <div>
            <button type="button" onclick="buyCreditProduct(1000, event)" id="credit-btn-1000" class="w-full py-3.5 px-6 rounded-2xl bg-purple-600 hover:bg-purple-700 active:scale-95 text-white font-bold text-sm transition-all duration-200 shadow-md shadow-purple-600/20 flex items-center justify-center gap-2">
              <i class="fa-solid fa-crown"></i>
              <span>Buy Now</span>
            </button>
            <span class="block text-center text-[11px] text-slate-400 mt-2 font-medium">Instant checkout &bull; ₹1,000</span>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

@push('scripts')
<script>
window.buyLataaCourse = async function(courseId, credits, price, e) {
  if (e) {
    e.preventDefault();
    e.stopPropagation();
  }
  const btn = e ? e.currentTarget : null;
  const oldHtml = btn ? btn.innerHTML : '';
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-sm"></i> Processing...';
  }

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

  try {
    const res = await fetch('{{ url('/add-to-cart-by-api-id') }}/' + courseId, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
      },
      body: JSON.stringify({ _token: csrfToken })
    });

    const data = await res.json();
    if (data.status === 'success' || data.status === 'already') {
      window.location.href = data.checkout_url || '{{ route("checkout.index") }}';
    } else {
      if (credits) {
        return buyCreditProduct(credits, e);
      }
      if (typeof toastr !== 'undefined') toastr.error(data.message || 'Error adding product to cart');
      else alert(data.message || 'Error adding product to cart');
      if (btn) { btn.disabled = false; btn.innerHTML = oldHtml; }
    }
  } catch (err) {
    console.error(err);
    if (btn) { btn.disabled = false; btn.innerHTML = oldHtml; }
    window.location.href = '{{ route("checkout.index") }}';
  }
};

function renderS2SIntegrateCourses(courses, container) {
  if (!courses || courses.length === 0) return;

  const themes = [
    {
      isDark: false,
      badgeText: 'STARTER PACK',
      badgeClass: 'bg-emerald-50 text-emerald-700 border-emerald-200',
      badgeIcon: 'fa-gift text-emerald-500',
      icon: 'fa-wand-magic-sparkles',
      iconBg: 'bg-gradient-to-tr from-emerald-400 to-teal-500 shadow-emerald-500/30',
      creditsColor: 'text-emerald-600',
      btnClass: 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-emerald-600/20',
      checkColor: 'text-emerald-500'
    },
    {
      isDark: true,
      badgeText: 'STANDARD PACK',
      badgeClass: 'bg-cyan-500/20 text-cyan-300 border-cyan-400/40',
      badgeIcon: 'fa-bolt text-cyan-300',
      icon: 'fa-cubes-stacked',
      iconBg: 'bg-gradient-to-tr from-cyan-400 to-blue-600 shadow-cyan-500/30',
      creditsColor: 'text-cyan-300',
      btnClass: 'bg-gradient-to-r from-cyan-400 to-blue-500 hover:from-cyan-300 hover:to-blue-400 text-slate-950 shadow-cyan-500/30 font-black',
      checkColor: 'text-cyan-400'
    },
    {
      isDark: false,
      badgeText: 'BEST VALUE',
      badgeClass: 'bg-purple-50 text-purple-700 border-purple-200',
      badgeIcon: 'fa-crown text-purple-600',
      icon: 'fa-gem',
      iconBg: 'bg-gradient-to-tr from-purple-500 to-indigo-600 shadow-purple-500/30',
      creditsColor: 'text-purple-600',
      btnClass: 'bg-purple-600 hover:bg-purple-700 text-white shadow-purple-600/20',
      checkColor: 'text-purple-600'
    }
  ];

  container.innerHTML = courses.map((course, idx) => {
    const isSingle = courses.length === 1;
    const theme = isSingle ? themes[1] : themes[idx % themes.length];
    const isDark = theme.isDark;
    const title = course.title || `Product ${idx + 1}`;
    const price = Number(course.price || 0);
    const credits = parseInt(course.credits || 0);
    const shortDesc = course.short_description || `LATAA-OS Credits Pack • ${credits} Credits`;
    const courseId = course.id || course.api_course_id;

    // Resolve cover image from API
    const rawCover = course.cover_image || course.raw_cover_image || course.thumbnail || '';
    let coverImgUrl = '';
    if (rawCover) {
      if (rawCover.startsWith('http') || rawCover.startsWith('/')) {
        coverImgUrl = rawCover;
      } else {
        coverImgUrl = 'http://devcollab.local/assets/images/event/cover/' + rawCover;
      }
    }

    const cardClasses = isDark
      ? 'relative flex flex-col justify-between rounded-3xl bg-gradient-to-b from-slate-900 to-[#0e1e3e] text-white p-8 shadow-2xl border-2 border-cyan-400 hover:border-cyan-300 transition-all duration-300 hover:-translate-y-2'
      : 'relative flex flex-col justify-between rounded-3xl bg-white border border-slate-200/90 p-8 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1';

    const ribbon = (isDark || course.is_featured) ? `
      <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-4 py-1 rounded-full bg-gradient-to-r from-cyan-400 to-blue-500 text-slate-950 font-black text-[11px] uppercase tracking-wider shadow-md whitespace-nowrap z-10">
        ★ Most Popular
      </div>
    ` : '';

    const priceHtml = price === 0
      ? `<span class="text-4xl font-black ${isDark ? 'text-white' : 'text-slate-900'}">₹0</span>
         <span class="text-sm font-semibold text-emerald-600 uppercase">Free</span>`
      : `<span class="text-4xl font-black ${isDark ? 'text-white' : 'text-slate-900'}">₹${price.toLocaleString('en-IN')}</span>
         <span class="text-xs font-medium ${isDark ? 'text-slate-300' : 'text-slate-500'}">/ ${credits} Credits ${credits > 0 ? ('(₹' + (price/credits).toFixed(0) + '/credit)') : ''}</span>`;

    const badgeLabel = price === 0 ? 'FREE TRIAL' : (theme.badgeText || `PRODUCT ${idx + 1}`);

    return `
      <div class="${cardClasses}">
        ${ribbon}
        <div>
          <div class="flex items-center justify-between mb-4 ${isDark ? 'mt-1' : ''}">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border ${theme.badgeClass}">
              <i class="fa-solid ${theme.badgeIcon}"></i> ${badgeLabel}
            </span>
            <span class="text-xs font-bold ${isDark ? 'text-slate-400' : 'text-slate-400'} uppercase tracking-wider">PRODUCT ${idx + 1}</span>
          </div>

          ${coverImgUrl ? `
            <div class="mb-4 overflow-hidden rounded-2xl relative h-36 border ${isDark ? 'border-cyan-400/30 bg-slate-800/40' : 'border-slate-100 bg-slate-50'}">
              <img src="${coverImgUrl}" alt="${title}" class="w-full h-full object-cover block" onerror="this.parentElement.style.display='none'">
            </div>
          ` : `
            <div class="w-14 h-14 rounded-2xl ${theme.iconBg} flex items-center justify-center text-white text-2xl shadow-md mb-5">
              <i class="fa-solid ${theme.icon}"></i>
            </div>
          `}

          <h3 class="text-xl font-bold ${isDark ? 'text-white' : 'text-slate-900'} mb-1">${title}</h3>
          <p class="text-xs ${isDark ? 'text-slate-300' : 'text-slate-500'} mb-4">${shortDesc}</p>

          <div class="flex items-baseline gap-2 mb-2 pb-4 border-b ${isDark ? 'border-white/10' : 'border-slate-100'}">
            ${priceHtml}
          </div>

          <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl ${isDark ? 'bg-white/10 border border-cyan-400/30 text-white' : 'bg-slate-50 border border-slate-200/80 text-slate-800'} font-extrabold text-sm mb-6 w-full">
            <i class="fa-solid fa-coins text-amber-400"></i>
            <span>Credits: <strong class="${theme.creditsColor} text-base">${credits}</strong></span>
          </div>

          <!-- Features Infographics List -->
          <ul class="space-y-3 text-xs sm:text-sm ${isDark ? 'text-slate-200' : 'text-slate-600'} mb-8">
            <li class="flex items-start gap-2.5">
              <i class="fa-solid fa-circle-check ${theme.checkColor} mt-0.5 text-sm"></i>
              <span><strong>${credits} Full AI Generation Credits</strong></span>
            </li>
            <li class="flex items-start gap-2.5">
              <i class="fa-solid fa-circle-check ${theme.checkColor} mt-0.5 text-sm"></i>
              <span>CAMPS Curriculum with LATAA-OS Integration</span>
            </li>
            <li class="flex items-start gap-2.5">
              <i class="fa-solid fa-circle-check ${theme.checkColor} mt-0.5 text-sm"></i>
              <span>Automated Student Assessment Rubrics</span>
            </li>
            <li class="flex items-start gap-2.5">
              <i class="fa-solid fa-circle-check ${theme.checkColor} mt-0.5 text-sm"></i>
              <span>Dynamic Printable Skill Worksheets</span>
            </li>
          </ul>
        </div>

        <div>
          <button type="button" onclick="buyLataaCourse(${courseId}, ${credits}, ${price}, event)" class="w-full py-3.5 px-6 rounded-2xl ${theme.btnClass} active:scale-95 text-sm transition-all duration-200 shadow-md flex items-center justify-center gap-2">
            <i class="fa-solid ${price === 0 ? 'fa-gift' : 'fa-bolt'}"></i>
            <span>${price === 0 ? 'Buy Now (Free)' : 'Buy Now'}</span>
          </button>
          <span class="block text-center text-[11px] ${isDark ? 'text-slate-400' : 'text-slate-400'} mt-2 font-medium">
            ${price === 0 ? 'Free instant activation' : ('Instant checkout • ₹' + price.toLocaleString('en-IN'))}
          </span>
        </div>
      </div>
    `;
  }).join('');
}

async function loadS2SIntegrateCourses() {
  const container = document.getElementById('s2s-integrate-products-grid');
  if (!container) return;

  const directApiUrl = 'http://devcollab.local/api/collab-courses?type=15';
  const proxyApiUrl = '{{ url("/api/collab-courses?type=15") }}';

  let courses = [];
  try {
    let res = await fetch(directApiUrl).catch(() => null);
    if (!res || !res.ok) {
      res = await fetch(proxyApiUrl);
    }
    if (res && res.ok) {
      const json = await res.json();
      const rawList = json.courses?.data || json.data || (Array.isArray(json) ? json : []);
      // Filter condition: "lata_type": "integrate"
      courses = rawList.filter(item => {
        const lataType = (item.lata_type || '').toLowerCase().trim();
        return lataType === 'integrate';
      });
    }
  } catch (err) {
    console.warn('Could not load LATAA integrate courses from API:', err);
  }

  if (courses && courses.length > 0) {
    renderS2SIntegrateCourses(courses, container);
  }
}

document.addEventListener('DOMContentLoaded', loadS2SIntegrateCourses);
if (document.readyState === 'complete' || document.readyState === 'interactive') {
  loadS2SIntegrateCourses();
}

window.buyCreditProduct = async function(credits, e) {
  if (e) {
    e.preventDefault();
    e.stopPropagation();
  }
  const btn = document.getElementById('credit-btn-' + credits) || (e ? e.currentTarget : null);
  const oldHtml = btn ? btn.innerHTML : '';
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-sm"></i> Processing...';
  }

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

  try {
    const res = await fetch('{{ route("cart.add-credit-product") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
      },
      body: JSON.stringify({ credits: credits, _token: csrfToken })
    });

    const data = await res.json();
    if (data.status === 'success' || data.status === 'already') {
      window.location.href = data.checkout_url || '{{ route("checkout.index") }}';
    } else {
      if (typeof toastr !== 'undefined') toastr.error(data.message || 'Error adding credit package to cart');
      else alert(data.message || 'Error adding credit package to cart');
      if (btn) { btn.disabled = false; btn.innerHTML = oldHtml; }
    }
  } catch (err) {
    console.error(err);
    if (btn) { btn.disabled = false; btn.innerHTML = oldHtml; }
    window.location.href = '{{ route("checkout.index") }}';
  }
};
window.buySkill2SchoolBundle = async function(e) {
  if (e) {
    e.preventDefault();
    e.stopPropagation();
  }
  const btn = document.getElementById('s2s-bundle-btn') || (e ? e.currentTarget : null);
  const oldHtml = btn ? btn.innerHTML : '';
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-sm"></i> Adding All 5 Grades...';
  }

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

  try {
    const res = await fetch('{{ route("cart.add-skill2school-bundle") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
      },
      body: JSON.stringify({ _token: csrfToken })
    });

    const data = await res.json();
    if (data.status === 'success' || data.status === 'already') {
      window.location.href = data.checkout_url || '{{ route("checkout.index") }}';
    } else {
      if (typeof toastr !== 'undefined') toastr.error(data.message || 'Error adding bundle to cart');
      else alert(data.message || 'Error adding bundle to cart');
      if (btn) { btn.disabled = false; btn.innerHTML = oldHtml; }
    }
  } catch (err) {
    console.error(err);
    if (btn) { btn.disabled = false; btn.innerHTML = oldHtml; }
    window.location.href = '{{ route("checkout.index") }}';
  }
};
</script>
@endpush

{{-- ══════════════════════════════════════════════
     7. ACTIVE PARTICIPATION
══════════════════════════════════════════════ --}}
<section class="py-16 relative overflow-hidden">
  <img alt="" class="absolute left-0 top-1/2 -translate-y-1/2 w-48 opacity-20 pointer-events-none"
    src="{{ asset('designs/img/skill2school-8.png') }}" />
  <div class="max-w-container-max mx-auto px-margin-mobile md:px-gutter">
    <div class="grid md:grid-cols-2 gap-12 items-center">
      <div class="order-2 md:order-1 relative">
        <div class="blob-shape">
          <img alt="Teacher and students in science lab" class="w-full h-auto object-cover"
            src="{{ asset('designs/img/skill2school-2.jpeg') }}" />
        </div>
      </div>
      <div class="order-1 md:order-2">
        <p class="text-primary font-semibold mb-2 text-sm uppercase tracking-wider">Engaging Skill Learning</p>
        <h2 class="text-3xl md:text-4xl font-bold mb-6 text-on-surface">Designed for Active Participation</h2>
        <p class="text-on-surface-variant mb-8 text-lg">
          Our learning design emphasizes student engagement, interaction, and application. This approach improves
          retention, participation, and skill transfer, moving learning beyond passive instruction.
        </p>
        <ul class="space-y-4">
          @foreach(['Activity-driven sessions','Collaborative tasks and challenges','Real-world scenarios and simulations','Reflection and presentation opportunities'] as $item)
          <li class="flex items-start gap-3">
            <span class="w-5 h-5 rounded-full bg-blue-100 text-primary flex items-center justify-center flex-shrink-0 mt-0.5 text-xs font-bold">✓</span>
            <span class="text-on-surface-variant">{{ $item }}</span>
          </li>
          @endforeach
        </ul>
      </div>
    </div>
  </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     ECOSYSTEM
     ═══════════════════════════════════════════════════════════ --}}
<section class="py-16 bg-surface-container relative overflow-hidden">
  <div class="max-w-container-max mx-auto px-margin-mobile md:px-gutter text-center">
    <p class="text-primary font-semibold mb-2 text-sm uppercase tracking-wider">Skill Readiness &amp; Student Mapping</p>
    <h2 class="text-3xl md:text-4xl font-bold mb-6 text-on-surface">A Skill Ecosystem for Your School</h2>
    <p class="text-on-surface-variant max-w-3xl mx-auto text-lg mb-12">
      Camps enables schools to offer exposure to diverse skill domains, supporting well-rounded development
      across cognitive, creative, digital, and life skills. These domains collectively contribute to
    </p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl mx-auto mb-16 text-left">
      @foreach(['Problem-solving and critical thinking','Creativity and innovation','Digital awareness and responsibility','Communication and collaboration'] as $point)
      <div class="flex items-start gap-3 bg-white p-4 rounded-xl shadow-sm border border-outline/20">
        <span class="w-6 h-6 rounded-full bg-primary/10 text-primary flex items-center justify-center flex-shrink-0 mt-0.5 text-xs font-bold">✓</span>
        <span class="text-on-surface-variant font-medium">{{ $point }}</span>
      </div>
      @endforeach
    </div>
    <div class="w-full max-w-5xl mx-auto">
      <img alt="Illustration of teacher and students" class="w-full h-auto" src="{{ asset('designs/img/skill2school-3.png') }}" />
    </div>
  </div>
</section>

{{-- curved divider --}}
<div class="curved-divider" style="border-bottom:1px solid var(--primary);"></div>

{{-- ══════════════════════════════════════════════
     9. OUR APPROACH (image)
══════════════════════════════════════════════ --}}
<section style="padding:5rem 0;background:var(--surface-container);">
  <div class="s2s-container" style="text-align:center;">
    <p style="color:var(--primary);font-weight:600;margin-bottom:.5rem;font-size:.875rem;text-transform:uppercase;letter-spacing:.06em;">How We Work</p>
    <h2 style="font-size:clamp(1.5rem,3vw,2.25rem);font-weight:700;color:var(--on-surface);margin-bottom:2.5rem;">Our Approach</h2>
    <img src="{{asset('frontend/img/skillbox/our-approach_fa29a3cc.jpeg')}}"
         alt="Our Approach"
         style="width:100%;max-width:860px;height:auto;display:block;margin:0 auto;">
  </div>
</section>

{{-- ══════════════════════════════════════════════
     10. EXPERTISE IN PEDASKILLS (image)
══════════════════════════════════════════════ --}}
<section style="padding:5rem 0;">
  <div class="s2s-container" style="text-align:center;">
    <p style="color:var(--primary);font-weight:600;margin-bottom:.5rem;font-size:.875rem;text-transform:uppercase;letter-spacing:.06em;">Our Expertise</p>
    <h2 style="font-size:clamp(1.5rem,3vw,2.25rem);font-weight:700;color:var(--on-surface);margin-bottom:2.5rem;">Expertise in Pedaskills</h2>
    <img src="{{asset('frontend/img/skillbox/experienc-expertise_82868586.jpeg')}}"
         alt="Expertise in Pedaskills"
         style="width:100%;max-width:720px;height:auto;display:block;margin:0 auto;">
  </div>
</section>

{{-- ══════════════════════════════════════════════
     11. LEARNING OUTCOMES (image)
══════════════════════════════════════════════ --}}
<section style="padding:5rem 0;background:var(--surface-container);">
  <div class="s2s-container" style="text-align:center;">
    <p style="color:var(--primary);font-weight:600;margin-bottom:.5rem;font-size:.875rem;text-transform:uppercase;letter-spacing:.06em;">What Students Gain</p>
    <h2 style="font-size:clamp(1.5rem,3vw,2.25rem);font-weight:700;color:var(--on-surface);margin-bottom:2.5rem;">Learning Outcomes</h2>
    <img src="{{asset('frontend/img/skillbox/learning-outcome_3085ddc8.jpeg')}}"
         alt="Learning Outcomes"
         style="width:100%;max-width:860px;height:auto;display:block;margin:0 auto;">
  </div>
</section>

{{-- ══════════════════════════════════════════════
     12. SAFAL & SQAAF
══════════════════════════════════════════════ --}}
<section class="py-20 relative overflow-hidden">
  <!-- <img alt="" class="absolute left-0 top-0 w-64 opacity-10 pointer-events-none transform -scale-x-100"
    src="{{ asset('designs/img/skill2school-7.png') }}" />
  <img alt="" class="absolute right-0 bottom-0 w-64 pointer-events-none"
    src="{{ asset('designs/img/skill2school-6.png') }}" /> -->
  <div class="max-w-container-max mx-auto px-margin-mobile md:px-gutter">
    <div class="text-center mb-16">
      <p class="text-primary font-semibold mb-2 text-sm uppercase tracking-wider">Skill Readiness &amp; Student Mapping</p>
      <h2 class="text-3xl md:text-4xl font-bold text-on-surface">Alignment with SAFAL &amp; SQAAF</h2>
    </div>
    <div class="grid md:grid-cols-2 gap-12 items-center mb-20">
      <div class="rounded-2xl overflow-hidden shadow-lg">
        <img alt="Teacher in classroom" class="w-full h-auto object-cover rounded-2xl"
          src="{{ asset('designs/img/skill2school-4.jpeg') }}" />
      </div>
      <div>
        <h3 class="text-2xl font-bold mb-6 text-on-surface">Camps contributes to key SQAAF domains, including</h3>
        <ul class="space-y-4">
          @foreach(['Curriculum & Pedagogy','Student Development','Teaching-Learning Practices','School Culture & Quality Processes'] as $item)
          <li class="flex items-center gap-3">
            <span class="w-6 h-6 rounded-full bg-primary/10 text-primary flex items-center justify-center flex-shrink-0 text-xs font-bold">✓</span>
            <span class="text-on-surface-variant text-lg">{{ $item }}</span>
          </li>
          @endforeach
        </ul>
      </div>
    </div>
    <div class="grid md:grid-cols-2 gap-12 items-center">
      <div class="order-2 md:order-1">
        <h3 class="text-2xl font-bold mb-6 text-on-surface">Camps strengthens competencies assessed under SAFAL by promoting</h3>
        <ul class="space-y-4">
          @foreach(['Application-based learning','Reasoning and analytical thinking','Communication and expression','Student confidence and engagement'] as $item)
          <li class="flex items-center gap-3">
            <span class="w-6 h-6 rounded-full bg-primary/10 text-primary flex items-center justify-center flex-shrink-0 text-xs font-bold">✓</span>
            <span class="text-on-surface-variant text-lg">{{ $item }}</span>
          </li>
          @endforeach
        </ul>
      </div>
      <div class="order-1 md:order-2 rounded-2xl overflow-hidden shadow-lg">
        <img alt="Student thumbs up" class="w-full h-auto object-cover rounded-2xl"
          src="{{ asset('designs/img/skill2school-5.jpeg') }}" />
      </div>
    </div>
  </div>
</section>

</div>{{-- /.s2s-page --}}
@endsection
