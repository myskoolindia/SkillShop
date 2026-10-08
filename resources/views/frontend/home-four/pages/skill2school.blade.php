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
      Skill 2 Skool enables schools to deliver new-age skill education aligned with NEP 2020, NCF 2023, SAFAL, and
      SQAAF, ensuring students are prepared not just for assessments—but for life beyond the classroom. Our skill
      ecosystem is designed to complement academics while strengthening competency, confidence, and real-world readiness.
    </p>
  </div>
</section>

{{-- ══════════════════════════════════════════════
     2. IMPORTANCE OF THE CURRICULUM (image)
══════════════════════════════════════════════ --}}
<section style="padding:0 24px;margin-top:-4rem;position:relative;z-index:20;margin-bottom:4rem;">
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
      Skill 2 Skool Skill Framework
    </p>
    <h2 style="font-size:clamp(1.5rem,2.8vw,2.25rem);font-weight:800;color:var(--on-surface);margin-bottom:1rem;">
      Structured. Aligned. Measurable
    </h2>
    <p style="color:var(--on-surface-variant);max-width:42rem;margin:0 auto 3rem;line-height:1.7;">
      Skill 2 Skool provides schools with a structured skill framework that focuses on
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
      Skill 2 Skool supports schools in identifying and nurturing student strengths through structured observation,
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
        Why is Preschool the First Big Step?
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
    @include('frontend.home-four.components.course-carousel', [
      'type'     => '8',
      'upskill'  => '0',
      'lms_only' => '1',
      'title'    => '',
      'subtitle' => '',
      'tagline'  => 'Skill Readiness & Student Mapping'
    ])
  </div>
</section>

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
      Skill 2 Skool enables schools to offer exposure to diverse skill domains, supporting well-rounded development
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
        <h3 class="text-2xl font-bold mb-6 text-on-surface">Skill 2 Skool contributes to key SQAAF domains, including</h3>
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
        <h3 class="text-2xl font-bold mb-6 text-on-surface">Skill 2 Skool strengthens competencies assessed under SAFAL by promoting</h3>
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
