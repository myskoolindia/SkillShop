@extends('frontend.home-four.layouts.master')

@section('meta_title', 'LATAA OS — Learning & Teaching AI Assistant Operating System | Skillvation')
@section('meta_description', 'An Intelligent AI Ecosystem Built Exclusively for Education. LATAA OS transforms how educational institutions teach, how teachers prepare, and how students learn.')

@push('styles')
<style>
  :root {
    --lataa-primary: #0f172a;
    --lataa-indigo: #4f46e5;
    --lataa-cyan: #06b6d4;
    --lataa-purple: #9333ea;
    --lataa-emerald: #10b981;
    --lataa-amber: #f59e0b;
    --lataa-bg: #f8fafc;
  }

  /* ── Keyframe Animations ── */
  @keyframes lataaPulseGlow {
    0%, 100% {
      box-shadow: 0 0 25px rgba(6, 182, 212, 0.4), 0 0 50px rgba(79, 70, 229, 0.2);
    }
    50% {
      box-shadow: 0 0 40px rgba(6, 182, 212, 0.7), 0 0 80px rgba(147, 51, 234, 0.4);
    }
  }

  @keyframes lataaFloat {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-10px) rotate(1deg); }
  }

  @keyframes lataaShimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
  }

  @keyframes lataaGradientMesh {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }

  /* ── Hero Section ── */
  .lataa-hero {
    position: relative;
    background: radial-gradient(circle at 80% 20%, rgba(6, 182, 212, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 20% 80%, rgba(99, 102, 241, 0.2) 0%, transparent 50%),
                linear-gradient(135deg, #0b1120 0%, #0f172a 50%, #1e1b4b 100%);
    background-size: 200% 200%;
    animation: lataaGradientMesh 15s ease infinite;
    color: #ffffff;
    padding: 90px 0 80px 0;
    overflow: hidden;
  }

  .lataa-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px);
    background-size: 28px 28px;
    opacity: 0.6;
    pointer-events: none;
  }

  .lataa-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(6, 182, 212, 0.12);
    border: 1px solid rgba(6, 182, 212, 0.4);
    padding: 6px 16px;
    border-radius: 50px;
    color: #38bdf8;
    font-size: 0.85rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    margin-bottom: 20px;
    animation: lataaFloat 4s ease-in-out infinite;
  }

  .lataa-badge-dot {
    width: 8px;
    height: 8px;
    background: #06b6d4;
    border-radius: 50%;
    box-shadow: 0 0 10px #06b6d4;
  }

  .lataa-hero-title {
    font-size: 3.4rem;
    font-weight: 800;
    line-height: 1.15;
    margin-bottom: 15px;
    background: linear-gradient(135deg, #ffffff 30%, #38bdf8 70%, #a855f7 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  .lataa-hero-sub {
    font-size: 1.35rem;
    font-weight: 600;
    color: #7dd3fc;
    margin-bottom: 18px;
  }

  .lataa-hero-desc {
    font-size: 1.1rem;
    color: #cbd5e1;
    line-height: 1.65;
    max-width: 680px;
    margin-bottom: 30px;
  }

  .lataa-cta-group {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    align-items: center;
  }

  .btn-lataa-primary {
    background: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%);
    color: #ffffff;
    font-weight: 700;
    padding: 14px 28px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
    box-shadow: 0 10px 25px rgba(6, 182, 212, 0.35);
    text-decoration: none;
    border: none;
  }

  .btn-lataa-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(6, 182, 212, 0.55);
    color: #ffffff;
  }

  .btn-lataa-outline {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #ffffff;
    font-weight: 600;
    padding: 14px 26px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    text-decoration: none;
  }

  .btn-lataa-outline:hover {
    background: rgba(255, 255, 255, 0.12);
    border-color: #38bdf8;
    color: #38bdf8;
    transform: translateY(-3px);
  }

  /* ── Interactive 3-User Cards (Multiple Colors) ── */
  .lataa-persona-card {
    border-radius: 22px;
    padding: 36px 30px;
    transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    height: 100%;
    position: relative;
    overflow: hidden;
    color: #ffffff;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
  }

  .lataa-persona-card::before {
    content: '';
    position: absolute;
    top: -50px;
    right: -50px;
    width: 150px;
    height: 150px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.22) 0%, transparent 70%);
    pointer-events: none;
  }

  .lataa-persona-card:hover {
    transform: translateY(-8px) scale(1.02);
  }

  /* 1. Student Card - Vibrant Ocean Blue */
  .persona-student {
    background: linear-gradient(145deg, #0284c7 0%, #0369a1 45%, #075985 100%);
    box-shadow: 0 16px 36px -6px rgba(2, 132, 199, 0.42), 0 6px 14px rgba(7, 89, 133, 0.25);
    border: 1px solid rgba(186, 230, 253, 0.45);
  }
  .persona-student:hover {
    box-shadow: 0 24px 48px -6px rgba(2, 132, 199, 0.58), 0 8px 20px rgba(7, 89, 133, 0.35);
  }

  /* 2. Teacher Card - Vibrant Royal Violet */
  .persona-teacher {
    background: linear-gradient(145deg, #7c3aed 0%, #6d28d9 45%, #581c87 100%);
    box-shadow: 0 16px 36px -6px rgba(124, 58, 237, 0.42), 0 6px 14px rgba(88, 28, 135, 0.25);
    border: 1px solid rgba(233, 213, 255, 0.45);
  }
  .persona-teacher:hover {
    box-shadow: 0 24px 48px -6px rgba(124, 58, 237, 0.58), 0 8px 20px rgba(88, 28, 135, 0.35);
  }

  /* 3. Institution Card - Vibrant Sunset Amber / Gold */
  .persona-institution {
    background: linear-gradient(145deg, #ea580c 0%, #d97706 45%, #b45309 100%);
    box-shadow: 0 16px 36px -6px rgba(234, 88, 12, 0.42), 0 6px 14px rgba(180, 83, 9, 0.25);
    border: 1px solid rgba(254, 215, 170, 0.45);
  }
  .persona-institution:hover {
    box-shadow: 0 24px 48px -6px rgba(234, 88, 12, 0.58), 0 8px 20px rgba(180, 83, 9, 0.35);
  }

  .persona-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 22px;
  }

  .persona-icon-circle {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    background: rgba(255, 255, 255, 0.22);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.35);
    color: #ffffff !important;
    margin-bottom: 0;
  }

  .persona-badge {
    display: inline-block;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(6px);
    border: 1px solid rgba(255, 255, 255, 0.35);
    padding: 4px 12px;
    border-radius: 999px;
    color: #ffffff;
  }

  .lataa-persona-card h3 {
    font-size: 1.35rem;
    font-weight: 800;
    color: #ffffff !important;
    margin-bottom: 6px;
    letter-spacing: -0.01em;
  }

  .lataa-persona-card .persona-tagline {
    font-size: 0.95rem;
    font-weight: 700;
    margin-bottom: 18px;
    line-height: 1.4;
  }
  .persona-student .persona-tagline { color: #bae6fd !important; }
  .persona-teacher .persona-tagline { color: #f5d0fe !important; }
  .persona-institution .persona-tagline { color: #fef08a !important; }

  .lataa-checklist {
    list-style: none;
    padding: 0;
    margin: 14px 0 0 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .lataa-checklist li {
    position: relative;
    padding-left: 28px;
    color: rgba(255, 255, 255, 0.93);
    font-size: 0.92rem;
    line-height: 1.55;
    font-weight: 500;
  }

  .lataa-checklist li::before {
    content: '✓';
    position: absolute;
    left: 0;
    top: 2px;
    font-size: 11px;
    font-weight: 900;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.22);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff !important;
  }

  /* ── Features Grid ── */
  .lataa-feature-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 26px;
    transition: all 0.3s ease;
    height: 100%;
    border-left: 4px solid #06b6d4;
  }

  .lataa-feature-box:hover {
    transform: translateY(-5px);
    border-left-color: #4f46e5;
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.06);
  }

  .feat-num-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    background: #0f172a;
    color: #ffffff;
    border-radius: 50%;
    font-weight: 700;
    font-size: 0.85rem;
    margin-bottom: 12px;
  }

  /* ── Workflow Steps ── */
  .wf-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px 24px;
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 16px;
    transition: all 0.3s ease;
  }

  .wf-card:hover {
    transform: translateX(8px);
    border-color: #06b6d4;
    box-shadow: 0 10px 25px rgba(6, 182, 212, 0.08);
  }

  .wf-step-num {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #0284c7, #38bdf8);
    color: #ffffff;
    font-weight: 800;
    font-size: 1.25rem;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  /* ── Comparison Table ── */
  .lataa-table-wrapper {
    background: #ffffff;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
  }

  .lataa-comp-table {
    width: 100%;
    border-collapse: collapse;
  }

  .lataa-comp-table th {
    padding: 18px 24px;
    font-size: 1.05rem;
    font-weight: 700;
  }

  .lataa-comp-table td {
    padding: 16px 24px;
    border-top: 1px solid #f1f5f9;
    font-size: 0.95rem;
  }

  .lataa-comp-table tr:hover td {
    background: #f8fafc;
  }

  .th-gen {
    background: #f8fafc;
    color: #64748b;
    width: 45%;
  }

  .th-lataa {
    background: #1e1b4b;
    color: #38bdf8;
    width: 55%;
  }

  .td-lataa {
    font-weight: 600;
    color: #0f172a;
    background: #f0fdf4 !important;
    border-left: 3px solid #16a34a;
  }

  /* ── Connected Ecosystem ── */
  .lataa-eco-hub {
    background: linear-gradient(135deg, #0b1120 0%, #1e1b4b 100%);
    border-radius: 20px;
    padding: 50px 40px;
    color: #ffffff;
    position: relative;
    overflow: hidden;
  }

  .eco-pillar-card {
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 12px;
    padding: 24px 20px;
    text-align: center;
    transition: all 0.3s ease;
    height: 100%;
  }

  .eco-pillar-card:hover {
    background: rgba(255, 255, 255, 0.12);
    transform: translateY(-5px);
    border-color: #38bdf8;
  }

  .eco-banner-ribbon {
    background: rgba(6, 182, 212, 0.15);
    border: 1px solid #06b6d4;
    border-radius: 50px;
    padding: 12px 24px;
    text-align: center;
    font-weight: 700;
    font-size: 0.95rem;
    color: #ffffff;
    letter-spacing: 0.5px;
    margin-top: 30px;
  }

  /* ── Closing Banner ── */
  .lataa-close-banner {
    background: radial-gradient(circle at 10% 50%, rgba(245, 158, 11, 0.15) 0%, transparent 40%),
                linear-gradient(135deg, #1e1b4b 0%, #0f172a 100%);
    border-radius: 20px;
    padding: 60px 40px;
    color: #ffffff;
    text-align: center;
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
  }
</style>
@endpush

@section('contents')

<!-- ══════════════════════════════════════════════════════════════ -->
<!-- HERO SECTION                                                   -->
<!-- ══════════════════════════════════════════════════════════════ -->
<section class="lataa-hero">
  <div class="container position-relative">
    <div class="row align-items-center">
      <div class="col-lg-7">
        <div class="lataa-badge">
          <span class="lataa-badge-dot"></span>
          Next-Generation EdTech Architecture
        </div>
        <h1 class="lataa-hero-title">LATAA OS</h1>
        <div class="lataa-hero-sub">Learning and Teaching AI Assistant Operating System</div>
        <p class="lead text-light mb-3 font-weight-bold" style="color:#38bdf8 !important;">
          An Intelligent AI Ecosystem Built Exclusively for Education.
        </p>
        <p class="lataa-hero-desc">
          A purpose-built AI operating system designed to transform how educational institutions teach, how teachers prepare, and how students learn. LATAA OS brings intelligent assistance, personalised learning, and academic support into one connected educational environment.
        </p>
        <p class="text-white-50 mb-4" style="font-size:0.95rem;">
          <strong class="text-white">One platform. Three key users. Endless learning possibilities.</strong>
        </p>
        <!-- <div class="lataa-cta-group">
          <a href="{{ asset('uploads/docs/LATAA_OS_Platform_Overview.pdf') }}" target="_blank" class="btn-lataa-primary">
            <i class="fas fa-file-pdf"></i> Download Official Overview (PDF)
          </a>
          <a href="#three-experiences" class="btn-lataa-outline">
            <i class="fas fa-compass"></i> Explore The Ecosystem
          </a>
        </div> -->
      </div>
      <div class="col-lg-5 mt-5 mt-lg-0 text-center">
        <div style="position:relative; display:inline-block; border-radius:18px; padding:8px; background:rgba(255,255,255,0.06); border:1px solid rgba(6,182,212,0.35); box-shadow: 0 25px 60px rgba(0,0,0,0.6);">
          <img src="{{ asset('frontend/img/skillbox/lataa_hero_ai.jpg') }}" alt="LATAA OS - AI Empowered Education" class="img-fluid rounded" style="max-height:430px; width:100%; object-fit:cover; box-shadow:0 10px 30px rgba(0,0,0,0.4);">
          <div style="position:absolute; bottom:-12px; right:-10px; background:linear-gradient(135deg, #06b6d4, #3b82f6); color:#ffffff; padding:10px 18px; border-radius:10px; font-weight:800; font-size:0.85rem; box-shadow:0 10px 25px rgba(6,182,212,0.5);">
            <i class="fas fa-microchip me-1"></i> 100% Education-First AI
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════════ -->
<!-- SECTION 1: WHAT IS LATAA OS?                                   -->
<!-- ══════════════════════════════════════════════════════════════ -->
<section class="py-5" style="background:#ffffff;">
  <div class="container py-4">
    <div class="row align-items-center">
      <div class="col-lg-6 mb-4 mb-lg-0">
        <div class="pe-lg-4">
          <!-- <span class="text-primary font-weight-bold text-uppercase" style="letter-spacing:1px; font-size:0.85rem;">Section 1 &bull; Overview</span> -->
          <h2 class="h1 font-weight-bold mt-2 mb-3" style="color:#0f172a;">AI Designed Around Education.<br><span style="color:#0284c7;">Not Just Adapted for It.</span></h2>
          <p class="text-secondary" style="font-size:1.05rem; line-height:1.7;">
            <strong>LATAA OS</strong> is an AI-powered learning and teaching assistant operating system developed specifically for educational institutions.
          </p>
          <p class="text-secondary" style="line-height:1.7;">
            It bridges the gap between traditional teaching methods and intelligent, personalised learning by providing AI-powered assistance to students, teachers, and academic institutions.
          </p>
          <p class="text-secondary" style="line-height:1.7;">
            Whether it is helping a student understand a difficult concept, assisting a teacher in preparing a lesson, or supporting an institution in strengthening its academic delivery, LATAA OS brings these requirements together through one intelligent platform.
          </p>
          <div class="p-3 mt-4 rounded" style="background:#f0f9ff; border-left:4px solid #0284c7;">
            <p class="mb-0 text-dark font-weight-bold" style="font-size:0.95rem;">
              Unlike general AI applications, LATAA OS is designed with educational content, academic objectives, teaching methodologies, and learner requirements at its core.
            </p>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div style="position:relative; border-radius:20px; overflow:hidden; box-shadow:0 16px 40px -10px rgba(15,23,42,0.18); border:1px solid #e2e8f0; background:#ffffff; transition:transform .3s ease, box-shadow .3s ease;">
          <img src="{{ asset('frontend/img/skillbox/lataa_designed_around_education.jpg') }}" alt="LATAA OS - AI Designed Around Education, Not Just Adapted for It" class="img-fluid" style="width:100%; height:auto; display:block; object-fit:contain;">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════════ -->
<!-- SECTION 2: ONE AI. THREE EDUCATIONAL EXPERIENCES               -->
<!-- ══════════════════════════════════════════════════════════════ -->
<section id="three-experiences" class="py-5" style="background:#f8fafc;">
  <div class="container py-4">
    <div class="text-center max-w-700 mx-auto mb-5">
      <!-- <span class="text-primary font-weight-bold text-uppercase" style="letter-spacing:1px; font-size:0.85rem;">Section 2 &bull; User Personas</span> -->
      <h2 class="h1 font-weight-bold mt-2 mb-3" style="color:#0f172a;">One AI. Three Educational Experiences.</h2>
      <p class="text-secondary" style="font-size:1.05rem;">
        LATAA OS empowers every stakeholder in the school ecosystem with specialized AI intelligence.
      </p>
    </div>

    <div class="row g-4">
      <!-- 1. STUDENTS -->
      <div class="col-lg-4">
        <div class="lataa-persona-card persona-student">
          <div class="persona-card-header">
            <div class="persona-icon-circle">
              <i class="fas fa-user-graduate"></i>
            </div>
            <span class="persona-badge">Learners</span>
          </div>
          <h3>1. LATAA OS for Students</h3>
          <p class="persona-tagline">An intelligent learning companion.</p>
          <ul class="lataa-checklist">
            <li>Understand concepts through interactive explanations.</li>
            <li>Learn at an appropriate level of difficulty.</li>
            <li>Practise through questions and activities.</li>
            <li>Strengthen subject knowledge across all disciplines.</li>
            <li>Get learning support beyond classroom hours.</li>
            <li>Develop independent learning and inquiry habits.</li>
          </ul>
        </div>
      </div>

      <!-- 2. TEACHERS -->
      <div class="col-lg-4">
        <div class="lataa-persona-card persona-teacher">
          <div class="persona-card-header">
            <div class="persona-icon-circle">
              <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <span class="persona-badge">Educators</span>
          </div>
          <h3>2. LATAA OS for Teachers</h3>
          <p class="persona-tagline">An intelligent teaching assistant.</p>
          <ul class="lataa-checklist">
            <li>Generate structured, board-aligned lesson plans.</li>
            <li>Create teaching and learning materials (TLM).</li>
            <li>Prepare classroom teaching scripts and prompts.</li>
            <li>Generate question banks, quizzes, and assessments.</li>
            <li>Create worksheets, formative tests, and activities.</li>
            <li>Adapt resources to different student learning levels.</li>
            <li>Save preparation time and improve classroom readiness.</li>
          </ul>
        </div>
      </div>

      <!-- 3. INSTITUTIONS -->
      <div class="col-lg-4">
        <div class="lataa-persona-card persona-institution">
          <div class="persona-card-header">
            <div class="persona-icon-circle">
              <i class="fas fa-school"></i>
            </div>
            <span class="persona-badge">Institutions</span>
          </div>
          <h3>3. LATAA OS for Institutions</h3>
          <p class="persona-tagline">An intelligent academic support ecosystem.</p>
          <ul class="lataa-checklist">
            <li>Support consistent, standardized academic delivery.</li>
            <li>Enable structured teaching and learning resources.</li>
            <li>Support curriculum-based academic planning.</li>
            <li>Encourage technology-enabled teaching practices.</li>
            <li>Strengthen institutional teaching capabilities.</li>
            <li>Support scalable, governed AI adoption school-wide.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════════ -->
<!-- SECTION 3: INTELLIGENT FEATURES OF LATAA OS                    -->
<!-- ══════════════════════════════════════════════════════════════ -->
<section class="py-5" style="background:#ffffff;">
  <div class="container py-4">
    <div class="text-center max-w-700 mx-auto mb-5">
      <!-- <span class="text-primary font-weight-bold text-uppercase" style="letter-spacing:1px; font-size:0.85rem;">Section 3 &bull; Core Capabilities</span> -->
      <h2 class="h1 font-weight-bold mt-2 mb-3" style="color:#0f172a;">Intelligent Features of LATAA OS</h2>
      <p class="text-secondary" style="font-size:1.05rem;">
        Built from the ground up to solve real classroom and institutional challenges.
      </p>
    </div>

    <div class="row g-4">
      <div class="col-lg-6">
        <div class="lataa-feature-box">
          <span class="feat-num-badge">1</span>
          <h4 class="h5 font-weight-bold text-dark mb-2">Personalised Learning Assistance</h4>
          <p class="text-muted mb-0" style="line-height:1.6;">
            Learning support that can adapt explanations, conceptual depth, and practice tasks dynamically to different student learning levels and individual learning speeds.
          </p>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="lataa-feature-box">
          <span class="feat-num-badge">2</span>
          <h4 class="h5 font-weight-bold text-dark mb-2">AI Lesson Planner</h4>
          <p class="text-muted mb-0" style="line-height:1.6;">
            Generate structured lesson plans, classroom teaching scripts, discussion hooks, and classroom-ready pedagogical resources in seconds.
          </p>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="lataa-feature-box">
          <span class="feat-num-badge">3</span>
          <h4 class="h5 font-weight-bold text-dark mb-2">Assessment &amp; Question Bank Generator</h4>
          <p class="text-muted mb-0" style="line-height:1.6;">
            Create quizzes, worksheets, comprehensive question banks, and rubric-backed assessments to support active teaching, formative feedback, and student practice.
          </p>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="lataa-feature-box">
          <span class="feat-num-badge">4</span>
          <h4 class="h5 font-weight-bold text-dark mb-2">Teaching Learning Material (TLM) Generator</h4>
          <p class="text-muted mb-0" style="line-height:1.6;">
            Generate instructional materials and engaging resources that make classroom teaching more interactive, visual, and systematically structured.
          </p>
        </div>
      </div>

      <div class="col-12">
        <div class="lataa-feature-box" style="border-left-color:#7c3aed;">
          <span class="feat-num-badge" style="background:#7c3aed;">5</span>
          <h4 class="h5 font-weight-bold text-dark mb-2">Differentiated Learning Support</h4>
          <p class="text-muted mb-0" style="line-height:1.6;">
            Create learning resources at <strong>Easy, Medium, and Hard</strong> levels, with additional bespoke adaptation for students who are <em>below, at, or above</em> the expected learning level.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════════ -->
<!-- SECTION 4: HOW LATAA OS WORKS                                  -->
<!-- ══════════════════════════════════════════════════════════════ -->
<section class="py-5" style="background:#f8fafc;">
  <div class="container py-4">
    <div class="row align-items-center">
      <div class="col-lg-5 mb-4 mb-lg-0">
        <!-- <span class="text-primary font-weight-bold text-uppercase" style="letter-spacing:1px; font-size:0.85rem;">Section 4 &bull; Methodology</span> -->
        <h2 class="h1 font-weight-bold mt-2 mb-3" style="color:#0f172a;">How LATAA OS Works</h2>
        <h4 class="h5 text-primary mb-3">Intelligence That Understands the Learning Process</h4>
        <p class="text-secondary" style="line-height:1.7;">
          LATAA OS brings together academic content, AI intelligence, and educational expertise to deliver relevant teaching and learning assistance.
        </p>
        <p class="text-secondary" style="line-height:1.7;">
          The platform follows a structured, scientifically proven methodology that ensures accuracy, safety, and instructional integrity at every step.
        </p>
        <!-- <div class="mt-4">
          <a href="{{ asset('uploads/docs/LATAA_OS_Platform_Overview.pdf') }}" target="_blank" class="btn btn-outline-primary font-weight-bold px-4 py-2">
            <i class="fas fa-download me-1"></i> Get Whitepaper PDF
          </a>
        </div> -->
      </div>

      <div class="col-lg-7">
        <div class="wf-card">
          <div class="wf-step-num">1</div>
          <div>
            <h5 class="font-weight-bold text-dark mb-1">Academic Content Integration</h5>
            <p class="text-muted mb-0 small">Educational content and subject-specific knowledge are organised to support relevant academic requirements.</p>
          </div>
        </div>

        <div class="wf-card">
          <div class="wf-step-num" style="background:linear-gradient(135deg, #0284c7, #38bdf8);">2</div>
          <div>
            <h5 class="font-weight-bold text-dark mb-1">Subject Expert Knowledge</h5>
            <p class="text-muted mb-0 small">Subject matter experts contribute to developing, calibrating, and validating academic content and learning resources.</p>
          </div>
        </div>

        <div class="wf-card">
          <div class="wf-step-num" style="background:linear-gradient(135deg, #7c3aed, #a855f7);">3</div>
          <div>
            <h5 class="font-weight-bold text-dark mb-1">AI-Powered Processing</h5>
            <p class="text-muted mb-0 small">LATAA OS uses the integrated knowledge and user requirements to generate relevant learning and teaching assistance.</p>
          </div>
        </div>

        <div class="wf-card">
          <div class="wf-step-num" style="background:linear-gradient(135deg, #d97706, #fbbf24);">4</div>
          <div>
            <h5 class="font-weight-bold text-dark mb-1">Level-Based Adaptation</h5>
            <p class="text-muted mb-0 small">Learning resources can be adapted to different difficulty levels and student learning requirements.</p>
          </div>
        </div>

        <div class="wf-card">
          <div class="wf-step-num" style="background:linear-gradient(135deg, #16a34a, #4ade80);">5</div>
          <div>
            <h5 class="font-weight-bold text-dark mb-1">Teaching and Learning Application</h5>
            <p class="text-muted mb-0 small">Teachers use the resources for classroom delivery, while students use AI assistance to strengthen their understanding and practice.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════════ -->
<!-- SECTION 6: WHY LATAA OS IS DIFFERENT                           -->
<!-- ══════════════════════════════════════════════════════════════ -->
<section class="py-5" style="background:#ffffff;">
  <div class="container py-4">
    <div class="text-center max-w-700 mx-auto mb-5">
      <!-- <span class="text-primary font-weight-bold text-uppercase" style="letter-spacing:1px; font-size:0.85rem;">Section 6 &bull; Differentiators</span> -->
      <h2 class="h1 font-weight-bold mt-2 mb-3" style="color:#0f172a;">Why LATAA OS is Different</h2>
      <h4 class="h5 text-primary mb-3">Purpose-Built for the Education Sector</h4>
      <p class="text-secondary" style="font-size:1.05rem;">
        General-purpose AI tools are designed to answer a wide range of questions. Educational institutions, however, require much more than general answers: academic relevance, structured learning, teaching methodologies, age-appropriate explanations, and curriculum-oriented resources.
      </p>
    </div>

    <div class="lataa-table-wrapper max-w-900 mx-auto">
      <table class="lataa-comp-table">
        <thead>
          <tr>
            <th class="th-gen"><i class="fas fa-times-circle text-muted me-2"></i> General-Purpose AI</th>
            <th class="th-lataa"><i class="fas fa-check-circle text-info me-2"></i> LATAA OS (Education-First)</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="text-muted">Broad, general knowledge assistance</td>
            <td class="td-lataa">Education-focused AI assistance</td>
          </tr>
          <tr>
            <td class="text-muted">General responses</td>
            <td class="td-lataa">Academic and learning-oriented resources</td>
          </tr>
          <tr>
            <td class="text-muted">Generic content generation</td>
            <td class="td-lataa">Teaching and learning applications</td>
          </tr>
          <tr>
            <td class="text-muted">Limited educational customisation</td>
            <td class="td-lataa">Institution and academic content orientation</td>
          </tr>
          <tr>
            <td class="text-muted">General user experience</td>
            <td class="td-lataa">Designed around students, teachers and schools</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="mt-4 p-4 rounded-3 text-center" style="background:#f0fdf4; border:1px solid #86efac; max-width:850px; margin:0 auto;">
      <h5 class="font-weight-bold text-success mb-1">Our Core Differentiator</h5>
      <p class="mb-0 text-dark" style="font-size:1rem;">
        LATAA OS combines AI technology with educational expertise to make AI meaningful in everyday teaching and learning.
      </p>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════════ -->
<!-- SECTION 7: LATAA OS AND SKILLVATION CONNECTED ECOSYSTEM        -->
<!-- ══════════════════════════════════════════════════════════════ -->
<section class="py-5" style="background:#0b1120; color:#ffffff;">
  <div class="container py-4">
    <div class="text-center max-w-700 mx-auto mb-5">
      <!-- <span class="text-info font-weight-bold text-uppercase" style="letter-spacing:1px; font-size:0.85rem;">Section 7 &bull; Unified Matrix</span> -->
      <h2 class="h1 font-weight-bold mt-2 mb-3 text-white">LATAA OS &amp; Skillvation: One Connected Ecosystem</h2>
      <h4 class="h5" style="color:#38bdf8;">AI That Doesn't Stop at the Screen. It Enters the Classroom.</h4>
      <p class="text-white-50" style="font-size:1.05rem;">
        LATAA OS is not just a digital learning platform within Skillvation. It is the intelligence that strengthens the delivery of our complete education ecosystem.
      </p>
    </div>

    <div class="lataa-eco-hub">
      <div class="row align-items-center mb-5">
        <div class="col-lg-5 text-center text-lg-start mb-4 mb-lg-0">
          <div class="display-6 font-weight-bold text-info mb-1">LATAA OS</div>
          <div class="h5 text-white mb-3">The Intelligence Behind Skillvation</div>
          <p class="text-white-50 mb-0" style="line-height:1.7;">
            A centralized AI intelligence engine seamlessly interconnected with student learning camps, teacher enablement, professional upskilling, and hands-on robotics maker labs.
          </p>
        </div>
        <div class="col-lg-7 text-center">
          <div style="border-radius:14px; overflow:hidden; border:1px solid rgba(6,182,212,0.4); box-shadow:0 15px 40px rgba(0,0,0,0.6);">
            <img src="{{ asset('frontend/img/skillbox/lataa_ecosystem_ai.jpg') }}" alt="LATAA OS Connected AI Ecosystem" class="img-fluid" style="width:100%; max-height:300px; object-fit:cover; display:block;">
          </div>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-md-3 col-sm-6">
          <div class="eco-pillar-card">
            <h4 class="h5 font-weight-bold text-info mb-2">CAMPS</h4>
            <p class="small text-white-50 mb-0">AI-supported student learning and skill development.</p>
          </div>
        </div>

        <div class="col-md-3 col-sm-6">
          <div class="eco-pillar-card">
            <h4 class="h5 font-weight-bold text-purple mb-2" style="color:#c084fc;">TTT</h4>
            <p class="small text-white-50 mb-0">AI-assisted teacher preparation and classroom delivery.</p>
          </div>
        </div>

        <div class="col-md-3 col-sm-6">
          <div class="eco-pillar-card">
            <h4 class="h5 font-weight-bold text-warning mb-2">U4T</h4>
            <p class="small text-white-50 mb-0">AI-enabled teacher learning and professional growth.</p>
          </div>
        </div>

        <div class="col-md-3 col-sm-6">
          <div class="eco-pillar-card">
            <h4 class="h5 font-weight-bold text-success mb-2">Skill Labs</h4>
            <p class="small text-white-50 mb-0">AI-assisted practical learning and curriculum implementation.</p>
          </div>
        </div>
      </div>

      <div class="eco-banner-ribbon">
        ONE CONNECTED EDUCATIONAL ECOSYSTEM &bull; Curriculum &bull; Content &bull; Teachers &bull; Students &bull; Practical Learning
      </div>
    </div>

    <div class="text-center mt-4">
      <p class="text-white-50 mx-auto" style="max-width:800px; font-size:1.05rem;">
        Through this integration, Skillvation brings together the physical, academic, and digital dimensions of education, with LATAA OS supporting the teaching and learning journey.
      </p>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════════ -->
<!-- SECTION: CREATE MY CURRICULUM                               -->
<!-- ══════════════════════════════════════════════════════════════ -->
<section class="py-5" style="background:#f8fafc;" id="skill2school-ai-credits">
  <div class="container py-4">
    <div class="text-center max-w-700 mx-auto mb-5">
      <div style="display:inline-flex; align-items:center; gap:8px; padding:6px 16px; border-radius:9999px; background:#e0f2fe; border:1px solid #7dd3fc; color:#0369a1; font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:12px;">
        <i class="fa-solid fa-coins text-cyan-600"></i> AI Teacher LATAA-OS Credits
      </div>
      <h2 class="display-6 text-dark mb-3" style="font-weight:800 !important; letter-spacing:-0.5px;">
        Create My Curriculum
      </h2>
      <p class="text-secondary" style="font-size:1.05rem;">
        Fuel your classroom with LATAA-OS credits for instant NEP-aligned lesson plans, automated student assessment rubrics, and dynamic printable skill worksheets.
      </p>
    </div>

    <div id="lataa-create-products-grid" class="row g-4 max-w-1100 mx-auto align-items-stretch justify-content-center">
      
      <!-- Product 1: 10 Credits (Free) -->
      <div class="col-lg-4 col-md-6 d-flex">
        <div class="card w-100 border-0 rounded-4 p-4 shadow-sm hover-shadow transition-all d-flex flex-column justify-content-between" style="background:#ffffff; border:1px solid #e2e8f0 !important; border-radius:24px;">
          <div>
            <div class="d-flex align-items-center justify-content-between mb-3">
              <span class="badge" style="background:#ecfdf5; color:#047857; border:1px solid #a7f3d0; font-size:11px; padding:6px 10px; border-radius:9999px; font-weight:700;">
                <i class="fa-solid fa-gift me-1"></i> FREE TRIAL
              </span>
              <span class="text-muted small fw-bold text-uppercase" style="letter-spacing:0.5px;">Product 1</span>
            </div>

            <div class="d-inline-flex align-items-center justify-content-center text-white mb-4" style="width:54px; height:54px; border-radius:16px; background:linear-gradient(135deg, #34d399 0%, #14b8a6 100%); font-size:22px; box-shadow:0 4px 12px rgba(20,184,166,0.3);">
              <i class="fa-solid fa-wand-magic-sparkles"></i>
            </div>

            <h3 class="h5 font-weight-bold text-dark mb-1">Product 1</h3>
            <p class="text-muted small mb-3">Starter Pack &bull; Free Trial for Teachers</p>

            <div class="d-flex align-items-baseline gap-2 mb-3 pb-3 border-bottom">
              <span class="h1 font-weight-bold text-dark mb-0">₹0</span>
              <span class="badge bg-success-subtle text-success fw-bold text-uppercase ms-1">Free</span>
            </div>

            <div class="p-2 px-3 rounded-3 mb-4 d-flex align-items-center justify-content-between" style="background:#f8fafc; border:1px solid #e2e8f0;">
              <span class="text-dark fw-bold small"><i class="fa-solid fa-coins text-warning me-1"></i> Credits:</span>
              <span class="h6 font-weight-bold text-success mb-0">10</span>
            </div>

            <ul class="list-unstyled text-secondary small mb-4" style="line-height:2;">
              <li><i class="fa-solid fa-circle-check text-success me-2"></i><strong>10 AI Generation Credits</strong> for trial</li>
              <li><i class="fa-solid fa-circle-check text-success me-2"></i>Instant LATAA OS Lesson Plans</li>
              <li><i class="fa-solid fa-circle-check text-success me-2"></i>Sample Student Skill Assessment</li>
              <li><i class="fa-solid fa-circle-check text-success me-2"></i>No Payment Required &bull; Instant Access</li>
            </ul>
          </div>

          <div>
            <button type="button" onclick="buyCreditProduct(10, event)" id="credit-btn-10" class="btn w-100 py-3 fw-bold rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2" style="background:#059669 !important; color:#ffffff !important; border:none !important; border-radius:14px; font-size:0.95rem; box-shadow:0 4px 14px rgba(5,150,105,0.25);">
              <i class="fa-solid fa-gift"></i>
              <span>Buy Now (Free)</span>
            </button>
            <small class="d-block text-center text-muted mt-2">Free instant activation</small>
          </div>
        </div>
      </div>

      <!-- Product 2: 100 Credits (₹100) - Most Popular -->
      <div class="col-lg-4 col-md-6 d-flex">
        <div class="card w-100 border-0 rounded-4 p-4 shadow-lg transition-all d-flex flex-column justify-content-between position-relative" style="background:linear-gradient(180deg, #0f172a 0%, #0e1e3e 100%); color:#ffffff; border:2px solid #38bdf8 !important; border-radius:24px;">
          <!-- Ribbon -->
          <div class="position-absolute" style="top:-14px; left:50%; transform:translateX(-50%); background:linear-gradient(90deg, #38bdf8 0%, #3b82f6 100%); color:#0f172a; font-weight:800; font-size:11px; padding:4px 16px; border-radius:9999px; text-transform:uppercase; letter-spacing:0.5px; box-shadow:0 4px 10px rgba(56,189,248,0.4); white-space:nowrap;">
            ★ Most Popular
          </div>

          <div>
            <div class="d-flex align-items-center justify-content-between mb-3 mt-1">
              <span class="badge" style="background:rgba(56,189,248,0.15); color:#38bdf8; border:1px solid rgba(56,189,248,0.4); font-size:11px; padding:6px 10px; border-radius:9999px; font-weight:700;">
                <i class="fa-solid fa-bolt me-1"></i> STANDARD PACK
              </span>
              <span class="text-white-50 small fw-bold text-uppercase" style="letter-spacing:0.5px;">Product 2</span>
            </div>

            <div class="d-inline-flex align-items-center justify-content-center text-white mb-4" style="width:54px; height:54px; border-radius:16px; background:linear-gradient(135deg, #38bdf8 0%, #2563eb 100%); font-size:22px; box-shadow:0 6px 16px rgba(56,189,248,0.4);">
              <i class="fa-solid fa-cubes-stacked"></i>
            </div>

            <h3 class="h5 font-weight-bold text-white mb-1">Product 2</h3>
            <p class="text-white-50 small mb-3">Standard School Pack &bull; Regular Use</p>

            <div class="d-flex align-items-baseline gap-2 mb-3 pb-3" style="border-bottom:1px solid rgba(255,255,255,0.1);">
              <span class="h1 font-weight-bold text-white mb-0">₹100</span>
              <span class="text-white-50 small ms-1">/ 100 Credits (₹1/credit)</span>
            </div>

            <div class="p-2 px-3 rounded-3 mb-4 d-flex align-items-center justify-content-between" style="background:rgba(255,255,255,0.08); border:1px solid rgba(56,189,248,0.3);">
              <span class="text-white fw-bold small"><i class="fa-solid fa-coins text-warning me-1"></i> Credits:</span>
              <span class="h6 font-weight-bold text-info mb-0" style="color:#38bdf8 !important;">100</span>
            </div>

            <ul class="list-unstyled text-white-50 small mb-4" style="line-height:2;">
              <li><i class="fa-solid fa-circle-check text-info me-2" style="color:#38bdf8 !important;"></i><strong class="text-white">100 Full AI Generation Credits</strong></li>
              <li><i class="fa-solid fa-circle-check text-info me-2" style="color:#38bdf8 !important;"></i>CAMPS Curriculum with LATAA-OS across Grades 1-5</li>
              <li><i class="fa-solid fa-circle-check text-info me-2" style="color:#38bdf8 !important;"></i>Printable Student Assessment Worksheets</li>
              <li><i class="fa-solid fa-circle-check text-info me-2" style="color:#38bdf8 !important;"></i>Classroom Mapping &amp; Evaluation Rubrics</li>
            </ul>
          </div>

          <div>
            <button type="button" onclick="buyCreditProduct(100, event)" id="credit-btn-100" class="btn w-100 py-3 fw-bold rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2" style="background:linear-gradient(90deg, #38bdf8 0%, #3b82f6 100%); color:#0f172a; font-weight:800; border:none; border-radius:14px; font-size:0.95rem; box-shadow:0 6px 18px rgba(56,189,248,0.35);">
              <i class="fa-solid fa-bolt"></i>
              <span>Buy Now</span>
            </button>
            <small class="d-block text-center text-white-50 mt-2">Instant checkout &bull; ₹100</small>
          </div>
        </div>
      </div>

      <!-- Product 3: 1000 Credits (₹1,000) - Pro Pack -->
      <div class="col-lg-4 col-md-6 d-flex">
        <div class="card w-100 border-0 rounded-4 p-4 shadow-sm hover-shadow transition-all d-flex flex-column justify-content-between" style="background:#ffffff; border:1px solid #e2e8f0 !important; border-radius:24px;">
          <div>
            <div class="d-flex align-items-center justify-content-between mb-3">
              <span class="badge" style="background:#faf5ff; color:#7e22ce; border:1px solid #e9d5ff; font-size:11px; padding:6px 10px; border-radius:9999px; font-weight:700;">
                <i class="fa-solid fa-crown me-1 text-purple"></i> BEST VALUE
              </span>
              <span class="text-muted small fw-bold text-uppercase" style="letter-spacing:0.5px;">Product 3</span>
            </div>

            <div class="d-inline-flex align-items-center justify-content-center text-white mb-4" style="width:54px; height:54px; border-radius:16px; background:linear-gradient(135deg, #a855f7 0%, #6366f1 100%); font-size:22px; box-shadow:0 4px 12px rgba(168,85,247,0.3);">
              <i class="fa-solid fa-gem"></i>
            </div>

            <h3 class="h5 font-weight-bold text-dark mb-1">Product 3</h3>
            <p class="text-muted small mb-3">Enterprise Pack &bull; Annual Primary Wing</p>

            <div class="d-flex align-items-baseline gap-2 mb-3 pb-3 border-bottom">
              <span class="h1 font-weight-bold text-dark mb-0">₹1,000</span>
              <span class="text-muted small ms-1">/ 1000 Credits</span>
            </div>

            <div class="p-2 px-3 rounded-3 mb-4 d-flex align-items-center justify-content-between" style="background:#f8fafc; border:1px solid #e2e8f0;">
              <span class="text-dark fw-bold small"><i class="fa-solid fa-coins text-warning me-1"></i> Credits:</span>
              <span class="h6 font-weight-bold mb-0" style="color:#9333ea;">1000</span>
            </div>

            <ul class="list-unstyled text-secondary small mb-4" style="line-height:2;">
              <li><i class="fa-solid fa-circle-check text-purple me-2" style="color:#9333ea;"></i><strong class="text-dark">1,000 High-Volume AI Credits</strong></li>
              <li><i class="fa-solid fa-circle-check text-purple me-2" style="color:#9333ea;"></i>Full Primary Wing Multi-Teacher Access</li>
              <li><i class="fa-solid fa-circle-check text-purple me-2" style="color:#9333ea;"></i>Unlimited Student Activity Generation</li>
              <li><i class="fa-solid fa-circle-check text-purple me-2" style="color:#9333ea;"></i>Priority AI Processing &amp; Custom Rubrics</li>
            </ul>
          </div>

          <div>
            <button type="button" onclick="buyCreditProduct(1000, event)" id="credit-btn-1000" class="btn w-100 py-3 fw-bold rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2" style="background:#9333ea; color:#ffffff; border-radius:14px; font-size:0.95rem; box-shadow:0 4px 12px rgba(147,51,234,0.25);">
              <i class="fa-solid fa-crown"></i>
              <span>Buy Now</span>
            </button>
            <small class="d-block text-center text-muted mt-2">Instant checkout &bull; ₹1,000</small>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════════ -->
<!-- SECTION 8: CLOSING BANNER                                      -->
<!-- ══════════════════════════════════════════════════════════════ -->
<section class="py-5" style="background:#ffffff;">
  <div class="container py-4">
    <div class="lataa-close-banner">
      <span class="badge bg-warning text-dark font-weight-bold px-3 py-2 text-uppercase mb-3" style="font-size:0.8rem;">
        Section 8 &bull; The Vision
      </span>
      <h2 class="display-5 font-weight-bold text-white mb-3">
        Reimagining Education.<br><span style="color:#fbbf24;">One Intelligent Learning Ecosystem at a Time.</span>
      </h2>
      <p class="text-light mx-auto mb-4" style="max-width:760px; font-size:1.1rem; line-height:1.7;">
        The future of education is not about replacing teachers with technology. It is about empowering teachers, supporting students, and helping institutions deliver better learning experiences.
      </p>
      <p class="text-light mx-auto mb-5" style="max-width:760px; font-size:1.1rem; line-height:1.7;">
        With LATAA OS, we bring AI closer to the classroom and make intelligent education accessible to the entire school ecosystem.
      </p>
      <div class="h4 font-weight-bold text-info mb-4">
        LATAA OS &mdash; Your Intelligent Partner in Teaching and Learning.
      </div>
      <!-- <div>
        <a href="{{ asset('uploads/docs/LATAA_OS_Platform_Overview.pdf') }}" target="_blank" class="btn-lataa-primary">
          <i class="fas fa-file-pdf"></i> Download Complete LATAA OS PDF Document
        </a>
      </div> -->
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script>
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

const lataaApiBaseUrl = '{{ rtrim(env("APP_API_BASE", "http://devcollab.local"), "/") }}';

function renderLataaCreateCourses(courses, container) {
  if (!courses || courses.length === 0) return;

  const colClass = courses.length === 1 
    ? 'col-lg-5 col-md-8 mx-auto' 
    : (courses.length === 2 ? 'col-lg-5 col-md-6' : 'col-lg-4 col-md-6');

  const themes = [
    {
      isDark: false,
      badgeText: 'FREE TRIAL',
      badgeBg: '#ecfdf5',
      badgeColor: '#047857',
      badgeBorder: '#a7f3d0',
      badgeIcon: 'fa-gift',
      icon: 'fa-wand-magic-sparkles',
      iconBg: 'linear-gradient(135deg, #34d399 0%, #14b8a6 100%)',
      iconShadow: '0 4px 12px rgba(20,184,166,0.3)',
      creditsColor: '#059669',
      btnClass: 'btn-emerald',
      btnStyle: 'background:#059669 !important; color:#ffffff !important; border:none !important; border-radius:14px; font-size:0.95rem; box-shadow:0 4px 14px rgba(5,150,105,0.25);',
      checkColor: 'text-success'
    },
    {
      isDark: true,
      badgeText: 'STANDARD PACK',
      badgeBg: 'rgba(56,189,248,0.15)',
      badgeColor: '#38bdf8',
      badgeBorder: 'rgba(56,189,248,0.4)',
      badgeIcon: 'fa-bolt',
      icon: 'fa-cubes-stacked',
      iconBg: 'linear-gradient(135deg, #38bdf8 0%, #2563eb 100%)',
      iconShadow: '0 6px 16px rgba(56,189,248,0.4)',
      creditsColor: '#38bdf8',
      btnClass: 'btn-info',
      btnStyle: 'background:linear-gradient(90deg, #38bdf8 0%, #3b82f6 100%); color:#0f172a; font-weight:800; border:none; border-radius:14px; font-size:0.95rem; box-shadow:0 6px 18px rgba(56,189,248,0.35);',
      checkColor: 'text-info'
    },
    {
      isDark: false,
      badgeText: 'BEST VALUE',
      badgeBg: '#faf5ff',
      badgeColor: '#7e22ce',
      badgeBorder: '#e9d5ff',
      badgeIcon: 'fa-crown',
      icon: 'fa-gem',
      iconBg: 'linear-gradient(135deg, #a855f7 0%, #6366f1 100%)',
      iconShadow: '0 4px 12px rgba(168,85,247,0.3)',
      creditsColor: '#9333ea',
      btnClass: 'btn-purple',
      btnStyle: 'background:#9333ea; color:#ffffff; border-radius:14px; font-size:0.95rem; box-shadow:0 4px 12px rgba(147,51,234,0.25);',
      checkColor: 'text-purple'
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
        coverImgUrl = lataaApiBaseUrl + '/assets/images/event/cover/' + rawCover;
      }
    }

    const cardBg = isDark
      ? 'background:linear-gradient(180deg, #0f172a 0%, #0e1e3e 100%); color:#ffffff; border:2px solid #38bdf8 !important;'
      : 'background:#ffffff; border:1px solid #e2e8f0 !important;';

    const ribbon = (isDark || course.is_featured || isSingle) ? `
      <div class="position-absolute" style="top:-14px; left:50%; transform:translateX(-50%); background:linear-gradient(90deg, #38bdf8 0%, #3b82f6 100%); color:#0f172a; font-weight:800; font-size:11px; padding:4px 16px; border-radius:9999px; text-transform:uppercase; letter-spacing:0.5px; box-shadow:0 4px 10px rgba(56,189,248,0.4); white-space:nowrap; z-index:5;">
        ★ Most Popular
      </div>
    ` : '';

    const priceBadge = price === 0
      ? `<span class="badge bg-success-subtle text-success fw-bold text-uppercase ms-1">Free</span>`
      : `<span class="${isDark ? 'text-white-50' : 'text-muted'} small ms-1">/ ${credits} Credits ${credits > 0 ? ('(₹' + (price/credits).toFixed(0) + '/credit)') : ''}</span>`;

    const badgeLabel = price === 0 ? 'FREE TRIAL' : (theme.badgeText || `PRODUCT ${idx + 1}`);

    return `
      <div class="${colClass} d-flex">
        <div class="card w-100 border-0 rounded-4 p-4 ${isDark ? 'shadow-lg' : 'shadow-sm hover-shadow'} transition-all d-flex flex-column justify-content-between position-relative" style="${cardBg} border-radius:24px;">
          ${ribbon}
          <div>
            <div class="d-flex align-items-center justify-content-between mb-3 ${isDark ? 'mt-1' : ''}">
              <span class="badge" style="background:${theme.badgeBg}; color:${theme.badgeColor}; border:1px solid ${theme.badgeBorder}; font-size:11px; padding:6px 10px; border-radius:9999px; font-weight:700;">
                <i class="fa-solid ${theme.badgeIcon} me-1"></i> ${badgeLabel}
              </span>
              <span class="${isDark ? 'text-white-50' : 'text-muted'} small fw-bold text-uppercase" style="letter-spacing:0.5px;">PRODUCT ${idx + 1}</span>
            </div>

            ${coverImgUrl ? `
              <div class="mb-3 overflow-hidden rounded-3 position-relative" style="height:140px; background:${isDark ? 'rgba(255,255,255,0.05)' : '#f1f5f9'}; border:1px solid ${isDark ? 'rgba(56,189,248,0.3)' : '#e2e8f0'}; border-radius:18px;">
                <img src="${coverImgUrl}" alt="${title}" style="width:100%; height:100%; object-fit:cover; display:block;" onerror="this.parentElement.style.display='none'">
              </div>
            ` : `
              <div class="d-inline-flex align-items-center justify-content-center text-white mb-4" style="width:54px; height:54px; border-radius:16px; background:${theme.iconBg}; font-size:22px; box-shadow:${theme.iconShadow};">
                <i class="fa-solid ${theme.icon}"></i>
              </div>
            `}

            <h3 class="h5 font-weight-bold ${isDark ? 'text-white' : 'text-dark'} mb-1">${title}</h3>
            <p class="${isDark ? 'text-white-50' : 'text-muted'} small mb-3">${shortDesc}</p>

            <div class="d-flex align-items-baseline gap-2 mb-3 pb-3" style="border-bottom:1px solid ${isDark ? 'rgba(255,255,255,0.1)' : '#e2e8f0'};">
              <span class="h1 font-weight-bold ${isDark ? 'text-white' : 'text-dark'} mb-0">₹${Number(price).toLocaleString('en-IN')}</span>
              ${priceBadge}
            </div>

            <div class="p-2 px-3 rounded-3 mb-4 d-flex align-items-center justify-content-between" style="${isDark ? 'background:rgba(255,255,255,0.08); border:1px solid rgba(56,189,248,0.3);' : 'background:#f8fafc; border:1px solid #e2e8f0;'}">
              <span class="${isDark ? 'text-white' : 'text-dark'} fw-bold small"><i class="fa-solid fa-coins text-warning me-1"></i> Credits:</span>
              <span class="h6 font-weight-bold mb-0" style="color:${theme.creditsColor} !important;">${credits}</span>
            </div>

            <ul class="list-unstyled ${isDark ? 'text-white-50' : 'text-secondary'} small mb-4" style="line-height:2;">
              <li><i class="fa-solid fa-circle-check ${theme.checkColor} me-2"></i><strong class="${isDark ? 'text-white' : 'text-dark'}">${credits} Full AI Generation Credits</strong></li>
              <li><i class="fa-solid fa-circle-check ${theme.checkColor} me-2"></i>CAMPS Curriculum with LATAA-OS Integration</li>
              <li><i class="fa-solid fa-circle-check ${theme.checkColor} me-2"></i>Automated Student Assessment Rubrics</li>
              <li><i class="fa-solid fa-circle-check ${theme.checkColor} me-2"></i>Dynamic Printable Skill Worksheets</li>
            </ul>
          </div>

          <div>
            <button type="button" onclick="buyLataaCourse(${courseId}, ${credits}, ${price}, event)" class="btn ${theme.btnClass} w-100 py-3 fw-bold rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2" style="${theme.btnStyle}">
              <i class="fa-solid ${price === 0 ? 'fa-gift' : 'fa-bolt'}"></i>
              <span>${price === 0 ? 'Buy Now (Free)' : 'Buy Now'}</span>
            </button>
            <small class="d-block text-center ${isDark ? 'text-white-50' : 'text-muted'} mt-2">${price === 0 ? 'Free instant activation' : ('Instant checkout • ₹' + Number(price).toLocaleString('en-IN'))}</small>
          </div>
        </div>
      </div>
    `;
  }).join('');
}

async function loadLataaCreateCourses() {
  const container = document.getElementById('lataa-create-products-grid');
  if (!container) return;

  const directApiUrl = lataaApiBaseUrl + '/api/collab-courses?type=15';
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
      // Filter condition: "lata_type": "create"
      courses = rawList.filter(item => {
        const lataType = (item.lata_type || '').toLowerCase().trim();
        return lataType === 'create';
      });
    }
  } catch (err) {
    console.warn('Could not load LATAA create courses from API:', err);
  }

  if (courses && courses.length > 0) {
    renderLataaCreateCourses(courses, container);
  }
}

document.addEventListener('DOMContentLoaded', loadLataaCreateCourses);
if (document.readyState === 'complete' || document.readyState === 'interactive') {
  loadLataaCreateCourses();
}
</script>
@endpush

