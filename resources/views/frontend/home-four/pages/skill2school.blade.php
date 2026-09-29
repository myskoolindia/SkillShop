@extends('frontend.home-four.layouts.master')

@section('meta_title', 'Skill 2 Skool — ' . config('app.name', 'Skillvation'))
@section('meta_description', 'Equip your students with future ready skills that matter. Aligned with NEP 2020, NCF 2023, SAFAL, and SQAAF.')

@push('styles')
<style>
  /* ── Hero ──────────────────────────────────────────────── */
  .s2s-hero {
    position: relative;
    width: 100%;
    min-height: 90vh;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    overflow: hidden;
    font-family: 'Plus Jakarta Sans', sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
  }
  .s2s-hero__bg {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: top center;
  }
  /* Crisp fade from transparent top to rich vibrant gradient at bottom for strong text readability */
  .s2s-hero__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
      to bottom,
      rgba(15, 23, 42, 0)     0%,
      rgba(15, 23, 42, 0.08)  25%,
      rgba(17, 82, 147, 0.72) 50%,
      rgba(25, 118, 210, 0.95) 75%,
      rgba(25, 118, 210, 1)   100%
    );
  }
  .s2s-hero__content {
    position: relative;
    z-index: 2;
    padding: 0 1.5rem 4.5rem;
    text-align: center;
    color: #ffffff;
    max-width: 960px;
    margin: 0 auto;
  }
  .s2s-hero__content h1 {
    font-size: clamp(2.1rem, 3.8vw, 3.25rem);
    font-weight: 800;
    line-height: 1.25;
    letter-spacing: -0.02em;
    margin-bottom: 1.25rem;
    color: #ffffff;
    text-shadow: 0 2px 14px rgba(0, 0, 0, 0.45);
  }
  .s2s-hero__content p {
    font-size: clamp(1.05rem, 1.45vw, 1.25rem);
    font-weight: 500;
    opacity: 0.98;
    line-height: 1.8;
    max-width: 820px;
    margin: 0 auto;
    color: #ffffff;
    text-shadow: 0 1px 8px rgba(0, 0, 0, 0.35);
  }

  /* ── Video card float ──────────────────────────────────── */
  .s2s-video-wrap {
    margin-top: -80px;
    position: relative;
    z-index: 10;
  }

  /* ── Feature cards ─────────────────────────────────────── */
  .feature-card {
    transition: transform .3s ease, box-shadow .3s ease;
  }
  .feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px -5px rgba(0,0,0,.1), 0 8px 10px -6px rgba(0,0,0,.1);
  }
  .blob-shape {
    border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
    overflow: hidden;
  }
</style>
@endpush

@section('contents')

{{-- ═══════════════════════════════════════════════════════════
     HERO — full-viewport image → blue gradient → text
     ═══════════════════════════════════════════════════════════ --}}
<section class="s2s-hero">
  {{-- Full background image — fully visible, not dimmed --}}
  <img
    src="{{ asset('designs/img/finalll_d7ef4fc1.jpeg') }}"
    alt="Happy students giving thumbs up"
    class="s2s-hero__bg"
  />
  {{-- Gradient overlay: clear at top, blue at bottom --}}
  <div class="s2s-hero__overlay"></div>

  {{-- Text content sits on the blue gradient band --}}
  <div class="s2s-hero__content">
    <h1>Equip Your Students With Future Ready Skills<br>That Matter</h1>
    <p>
      Skill 2 Skool enables schools to deliver new-age skill education aligned with NEP 2020, NCF 2023, SAFAL, and
      SQAAF, ensuring students are prepared not just for assessments—but for life beyond the classroom. Our skill
      ecosystem is designed to complement academics while strengthening competency, confidence, and real-world
      readiness.
    </p>
  </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     VIDEO — floats up over the hero bottom edge
     ═══════════════════════════════════════════════════════════ --}}
<section class="s2s-video-wrap max-w-container-max mx-auto px-margin-mobile md:px-gutter mb-16">
  <div class="rounded-2xl overflow-hidden shadow-2xl bg-white border border-slate-200 p-2">
    <iframe
      class="w-full aspect-video rounded-xl"
      src="https://www.youtube.com/embed/LGab-8Rf1jQ"
      title="At Skillvation, we believe teachers shape society."
      frameborder="0"
      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
      referrerpolicy="strict-origin-when-cross-origin"
      allowfullscreen>
    </iframe>
  </div>
  <div class="text-center mt-8">
    <a href="{{ route('contact.index') }}"
       class="inline-block bg-primary hover:bg-primary-dark text-white px-8 py-3 rounded-full font-semibold transition-colors shadow-md">
      Request For Demo
    </a>
  </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     FRAMEWORK — 4 feature cards
     ═══════════════════════════════════════════════════════════ --}}
<section class="py-16 max-w-container-max mx-auto px-margin-mobile md:px-gutter text-center">
  <p class="text-primary font-semibold mb-2 text-sm uppercase tracking-wider">Skill 2 Skool Skill Framework</p>
  <h2 class="text-3xl md:text-4xl font-bold mb-4 text-on-surface">Structured. Aligned. Measurable</h2>
  <p class="text-on-surface-variant max-w-2xl mx-auto mb-12">Skill 2 Skool provides schools with a structured skill framework that focuses on</p>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 text-left">
    <div class="bg-surface-container rounded-xl p-6 border border-outline/30 feature-card flex flex-col h-full">
      <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-4 text-primary">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
      </div>
      <h3 class="font-bold text-lg mb-2 text-on-surface">Competency-based learning</h3>
      <p class="text-on-surface-variant text-sm flex-grow">Simple, practical learning tools that help teachers upgrade skills and stay confident in the classroom.</p>
    </div>
    <div class="bg-surface-container rounded-xl p-6 border border-outline/30 feature-card flex flex-col h-full">
      <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-4 text-primary">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
      </div>
      <h3 class="font-bold text-lg mb-2 text-on-surface">Experiential and application-oriented pedagogy</h3>
      <p class="text-on-surface-variant text-sm flex-grow">Structured courses, expert guidance, and real-world strategies supporting every stage of a teacher's growth.</p>
    </div>
    <div class="bg-surface-container rounded-xl p-6 border border-outline/30 feature-card flex flex-col h-full">
      <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-4 text-primary">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
      </div>
      <h3 class="font-bold text-lg mb-2 text-on-surface">Observable and documentable student outcomes</h3>
      <p class="text-on-surface-variant text-sm flex-grow">Track student progress with structured observation tools that make learning outcomes visible and reportable.</p>
    </div>
    <div class="bg-surface-container rounded-xl p-6 border border-outline/30 feature-card flex flex-col h-full">
      <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-4 text-primary">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
      </div>
      <h3 class="font-bold text-lg mb-2 text-on-surface">Seamless integration into existing school systems</h3>
      <p class="text-on-surface-variant text-sm flex-grow">Designed to complement your school's current schedule, curriculum, and processes without disruption.</p>
    </div>
  </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     STRENGTHS
     ═══════════════════════════════════════════════════════════ --}}
<section class="py-16 text-center max-w-container-max mx-auto px-margin-mobile md:px-gutter">
  <p class="text-primary font-semibold mb-2 text-sm uppercase tracking-wider">Skill Readiness &amp; Student Mapping</p>
  <h2 class="text-3xl md:text-4xl font-bold mb-6 text-on-surface">Understanding Strengths Beyond Marks</h2>
  <p class="text-on-surface-variant max-w-3xl mx-auto text-lg">
    Skill 2 Skool supports schools in identifying and nurturing student strengths through structured observation,
    reflection, and skill mapping. By focusing on aptitude, interest, and behaviour patterns, schools gain deeper
    insights into student potential—supporting informed guidance, confidence building, and holistic development.
  </p>
</section>

{{-- ═══════════════════════════════════════════════════════════
     COURSES CAROUSEL
     ═══════════════════════════════════════════════════════════ --}}
@include('frontend.home-four.components.course-carousel', [
  'type' => '8',
  'upskill' => '0',
  'lms_only' => '1',
  'title' => 'Start Learning What Matters',
  'subtitle' => 'Interactive hands-on modules',
  'tagline' => 'Skill Readiness & Student Mapping'
])

{{-- ═══════════════════════════════════════════════════════════
     ACTIVE PARTICIPATION — 2-col image + text
     ═══════════════════════════════════════════════════════════ --}}
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

{{-- ═══════════════════════════════════════════════════════════
     SAFAL & SQAAF
     ═══════════════════════════════════════════════════════════ --}}
<section class="py-20 relative overflow-hidden">
  <img alt="" class="absolute left-0 top-0 w-64 opacity-10 pointer-events-none transform -scale-x-100"
    src="{{ asset('designs/img/skill2school-7.png') }}" />
  <img alt="" class="absolute right-0 bottom-0 w-64 pointer-events-none"
    src="{{ asset('designs/img/skill2school-6.png') }}" />
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

@endsection
