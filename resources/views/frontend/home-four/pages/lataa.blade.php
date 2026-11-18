@extends('frontend.home-four.layouts.master')

@section('meta_title', 'LATaA OS — Learning & Teaching AI Assistant Operating System | Skillvation')
@section('meta_description', 'An Intelligent AI Ecosystem Built Exclusively for Education. LATaA OS transforms how educational institutions teach, how teachers prepare, and how students learn.')

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

  /* ── Interactive 3-User Cards ── */
  .lataa-persona-card {
    background: #ffffff;
    border-radius: 14px;
    padding: 34px 28px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    height: 100%;
    position: relative;
    overflow: hidden;
  }

  .lataa-persona-card::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    transition: height 0.3s ease;
  }

  .persona-student::after { background: linear-gradient(90deg, #0284c7, #38bdf8); }
  .persona-teacher::after { background: linear-gradient(90deg, #7c3aed, #a855f7); }
  .persona-institution::after { background: linear-gradient(90deg, #d97706, #fbbf24); }

  .lataa-persona-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(15, 23, 42, 0.12);
  }

  .persona-icon-circle {
    width: 60px;
    height: 60px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
    margin-bottom: 20px;
  }

  .persona-student .persona-icon-circle { background: #e0f2fe; color: #0284c7; }
  .persona-teacher .persona-icon-circle { background: #f3e8ff; color: #7c3aed; }
  .persona-institution .persona-icon-circle { background: #fef3c7; color: #d97706; }

  .lataa-checklist {
    list-style: none;
    padding: 0;
    margin: 20px 0 0 0;
  }

  .lataa-checklist li {
    position: relative;
    padding-left: 26px;
    margin-bottom: 12px;
    color: #475569;
    font-size: 0.93rem;
    line-height: 1.5;
  }

  .lataa-checklist li::before {
    content: '✓';
    position: absolute;
    left: 0;
    top: 1px;
    font-weight: 800;
  }

  .persona-student .lataa-checklist li::before { color: #0284c7; }
  .persona-teacher .lataa-checklist li::before { color: #7c3aed; }
  .persona-institution .lataa-checklist li::before { color: #d97706; }

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
        <h1 class="lataa-hero-title">LATaA OS</h1>
        <div class="lataa-hero-sub">Learning and Teaching AI Assistant Operating System</div>
        <p class="lead text-light mb-3 font-weight-bold" style="color:#38bdf8 !important;">
          An Intelligent AI Ecosystem Built Exclusively for Education.
        </p>
        <p class="lataa-hero-desc">
          A purpose-built AI operating system designed to transform how educational institutions teach, how teachers prepare, and how students learn. LATaA OS brings intelligent assistance, personalised learning, and academic support into one connected educational environment.
        </p>
        <p class="text-white-50 mb-4" style="font-size:0.95rem;">
          <strong class="text-white">One platform. Three key users. Endless learning possibilities.</strong>
        </p>
        <div class="lataa-cta-group">
          <a href="{{ asset('uploads/docs/LATaA_OS_Platform_Overview.pdf') }}" target="_blank" class="btn-lataa-primary">
            <i class="fas fa-file-pdf"></i> Download Official Overview (PDF)
          </a>
          <a href="#three-experiences" class="btn-lataa-outline">
            <i class="fas fa-compass"></i> Explore The Ecosystem
          </a>
        </div>
      </div>
      <div class="col-lg-5 mt-5 mt-lg-0 text-center">
        <div style="position:relative; display:inline-block; border-radius:18px; padding:10px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.15); box-shadow: 0 20px 50px rgba(0,0,0,0.5);">
          <img src="{{ asset('frontend/img/skillbox/ai_empowered.jpg') }}" alt="LATaA OS - AI Empowered Education" class="img-fluid rounded" style="max-height:420px; object-fit:cover;">
          <div style="position:absolute; bottom:-15px; right:-15px; background:#06b6d4; color:#042f2e; padding:10px 18px; border-radius:10px; font-weight:800; font-size:0.85rem; box-shadow:0 10px 20px rgba(6,182,212,0.4);">
            <i class="fas fa-microchip"></i> 100% Education-First AI
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
          <span class="text-primary font-weight-bold text-uppercase" style="letter-spacing:1px; font-size:0.85rem;">Section 1 &bull; Overview</span>
          <h2 class="h1 font-weight-bold mt-2 mb-3" style="color:#0f172a;">AI Designed Around Education.<br><span style="color:#0284c7;">Not Just Adapted for It.</span></h2>
          <p class="text-secondary" style="font-size:1.05rem; line-height:1.7;">
            <strong>LATaA OS</strong> is an AI-powered learning and teaching assistant operating system developed specifically for educational institutions.
          </p>
          <p class="text-secondary" style="line-height:1.7;">
            It bridges the gap between traditional teaching methods and intelligent, personalised learning by providing AI-powered assistance to students, teachers, and academic institutions.
          </p>
          <p class="text-secondary" style="line-height:1.7;">
            Whether it is helping a student understand a difficult concept, assisting a teacher in preparing a lesson, or supporting an institution in strengthening its academic delivery, LATaA OS brings these requirements together through one intelligent platform.
          </p>
          <div class="p-3 mt-4 rounded" style="background:#f0f9ff; border-left:4px solid #0284c7;">
            <p class="mb-0 text-dark font-weight-bold" style="font-size:0.95rem;">
              Unlike general AI applications, LATaA OS is designed with educational content, academic objectives, teaching methodologies, and learner requirements at its core.
            </p>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="row g-3">
          <div class="col-sm-6">
            <div class="p-4 rounded-3 h-100" style="background:#f8fafc; border:1px solid #e2e8f0;">
              <div class="h3 font-weight-bold text-primary mb-2">360°</div>
              <h5 class="font-weight-bold text-dark">Holistic Ecosystem</h5>
              <p class="text-muted small mb-0">Unifies learner, instructor, and administration workflows in one shared space.</p>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="p-4 rounded-3 h-100" style="background:#f8fafc; border:1px solid #e2e8f0;">
              <div class="h3 font-weight-bold text-success mb-2">100%</div>
              <h5 class="font-weight-bold text-dark">Curriculum-Aligned</h5>
              <p class="text-muted small mb-0">Calibrated to CBSE, NEP 2020, and national academic learning competencies.</p>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="p-4 rounded-3 h-100" style="background:#f8fafc; border:1px solid #e2e8f0;">
              <div class="h3 font-weight-bold text-warning mb-2">3 Tiers</div>
              <h5 class="font-weight-bold text-dark">Adaptive Levels</h5>
              <p class="text-muted small mb-0">Easy, Medium, and Hard dynamic difficulty scaling for truly personalized support.</p>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="p-4 rounded-3 h-100" style="background:#f8fafc; border:1px solid #e2e8f0;">
              <div class="h3 font-weight-bold text-info mb-2">24/7</div>
              <h5 class="font-weight-bold text-dark">Beyond Classroom</h5>
              <p class="text-muted small mb-0">Continuous learning guidance and interactive practice for students anytime, anywhere.</p>
            </div>
          </div>
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
      <span class="text-primary font-weight-bold text-uppercase" style="letter-spacing:1px; font-size:0.85rem;">Section 2 &bull; User Personas</span>
      <h2 class="h1 font-weight-bold mt-2 mb-3" style="color:#0f172a;">One AI. Three Educational Experiences.</h2>
      <p class="text-secondary" style="font-size:1.05rem;">
        LATaA OS empowers every stakeholder in the school ecosystem with specialized AI intelligence.
      </p>
    </div>

    <div class="row g-4">
      <!-- 1. STUDENTS -->
      <div class="col-lg-4">
        <div class="lataa-persona-card persona-student">
          <div class="persona-icon-circle">
            <i class="fas fa-user-graduate"></i>
          </div>
          <h3 class="h4 font-weight-bold text-dark mb-1">1. LATaA for Students</h3>
          <p class="text-primary font-weight-bold mb-3" style="font-size:0.95rem;">An intelligent learning companion.</p>
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
          <div class="persona-icon-circle">
            <i class="fas fa-chalkboard-teacher"></i>
          </div>
          <h3 class="h4 font-weight-bold text-dark mb-1">2. LATaA for Teachers</h3>
          <p class="text-purple font-weight-bold mb-3" style="color:#7c3aed; font-size:0.95rem;">An intelligent teaching assistant.</p>
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
          <div class="persona-icon-circle">
            <i class="fas fa-school"></i>
          </div>
          <h3 class="h4 font-weight-bold text-dark mb-1">3. LATaA for Institutions</h3>
          <p class="text-warning font-weight-bold mb-3" style="color:#d97706; font-size:0.95rem;">An intelligent academic support ecosystem.</p>
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
      <span class="text-primary font-weight-bold text-uppercase" style="letter-spacing:1px; font-size:0.85rem;">Section 3 &bull; Core Capabilities</span>
      <h2 class="h1 font-weight-bold mt-2 mb-3" style="color:#0f172a;">Intelligent Features of LATaA OS</h2>
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
        <span class="text-primary font-weight-bold text-uppercase" style="letter-spacing:1px; font-size:0.85rem;">Section 4 &bull; Methodology</span>
        <h2 class="h1 font-weight-bold mt-2 mb-3" style="color:#0f172a;">How LATaA OS Works</h2>
        <h4 class="h5 text-primary mb-3">Intelligence That Understands the Learning Process</h4>
        <p class="text-secondary" style="line-height:1.7;">
          LATaA OS brings together academic content, AI intelligence, and educational expertise to deliver relevant teaching and learning assistance.
        </p>
        <p class="text-secondary" style="line-height:1.7;">
          The platform follows a structured, scientifically proven methodology that ensures accuracy, safety, and instructional integrity at every step.
        </p>
        <div class="mt-4">
          <a href="{{ asset('uploads/docs/LATaA_OS_Platform_Overview.pdf') }}" target="_blank" class="btn btn-outline-primary font-weight-bold px-4 py-2">
            <i class="fas fa-download me-1"></i> Get Whitepaper PDF
          </a>
        </div>
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
            <p class="text-muted mb-0 small">LATaA OS uses the integrated knowledge and user requirements to generate relevant learning and teaching assistance.</p>
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
      <span class="text-primary font-weight-bold text-uppercase" style="letter-spacing:1px; font-size:0.85rem;">Section 6 &bull; Differentiators</span>
      <h2 class="h1 font-weight-bold mt-2 mb-3" style="color:#0f172a;">Why LATaA OS is Different</h2>
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
            <th class="th-lataa"><i class="fas fa-check-circle text-info me-2"></i> LATaA OS (Education-First)</th>
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
        LATaA OS combines AI technology with educational expertise to make AI meaningful in everyday teaching and learning.
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
      <span class="text-info font-weight-bold text-uppercase" style="letter-spacing:1px; font-size:0.85rem;">Section 7 &bull; Unified Matrix</span>
      <h2 class="h1 font-weight-bold mt-2 mb-3 text-white">LATaA OS &amp; Skillvation: One Connected Ecosystem</h2>
      <h4 class="h5" style="color:#38bdf8;">AI That Doesn't Stop at the Screen. It Enters the Classroom.</h4>
      <p class="text-white-50" style="font-size:1.05rem;">
        LATaA OS is not just a digital learning platform within Skillvation. It is the intelligence that strengthens the delivery of our complete education ecosystem.
      </p>
    </div>

    <div class="lataa-eco-hub">
      <div class="text-center mb-4">
        <div class="display-6 font-weight-bold text-info mb-1">LATaA OS</div>
        <div class="text-white-50">The Intelligence Behind Skillvation</div>
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
        Through this integration, Skillvation brings together the physical, academic, and digital dimensions of education, with LATaA OS supporting the teaching and learning journey.
      </p>
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
        With LATaA OS, we bring AI closer to the classroom and make intelligent education accessible to the entire school ecosystem.
      </p>
      <div class="h4 font-weight-bold text-info mb-4">
        LATaA OS &mdash; Your Intelligent Partner in Teaching and Learning.
      </div>
      <div>
        <a href="{{ asset('uploads/docs/LATaA_OS_Platform_Overview.pdf') }}" target="_blank" class="btn-lataa-primary">
          <i class="fas fa-file-pdf"></i> Download Complete LATaA OS PDF Document
        </a>
      </div>
    </div>
  </div>
</section>

@endsection

