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
          <a href="{{ route('home') }}" class="hover:text-brand-orange transition-colors">Home</a>
          <i class="fa-solid fa-chevron-right text-[9px]"></i>
          <a href="{{ route('labs') }}" class="hover:text-brand-orange transition-colors">Labs</a>
          <i class="fa-solid fa-chevron-right text-[9px]"></i>
          <span class="text-brand-navy font-bold">Basic Composite Skill Lab</span>
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
          <a href="#package-details"
             class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl border-2 border-brand-navy text-brand-navy hover:bg-brand-navy hover:text-white text-sm font-bold transition-all hover:-translate-y-0.5">
            <i class="fa-solid fa-box-archive"></i> Request Detailed Package
          </a>
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
    <div class="mt-14 grid grid-cols-2 lg:grid-cols-4 gap-4">
      @foreach([
        ['icon'=>'fa-scale-balanced', 'stat'=>'CBSE-Aligned', 'label'=>'Circular Skill-75/2024 & 01/2025'],
        ['icon'=>'fa-shapes',         'stat'=>'Multi-Domain', 'label'=>'Life Forms, Machines & Services'],
        ['icon'=>'fa-boxes-stacked',   'stat'=>'100% Turnkey', 'label'=>'Infrastructure + Tools + Storage'],
        ['icon'=>'fa-graduation-cap', 'stat'=>'Classes VI–XII','label'=>'600 sq ft or 2×400 sq ft Options'],
      ] as $s)
      <div class="bg-white rounded-2xl p-5 flex items-center gap-4 border border-slate-200/90 shadow-sm hover:border-brand-orange/40 transition-colors">
        <div class="w-12 h-12 rounded-xl bg-brand-navy/10 flex items-center justify-center text-brand-navy text-xl flex-shrink-0">
          <i class="fa-solid {{ $s['icon'] }}"></i>
        </div>
        <div>
          <div class="text-lg font-extrabold text-brand-navy leading-tight">{{ $s['stat'] }}</div>
          <div class="text-xs text-slate-500 font-medium">{{ $s['label'] }}</div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- =====================================================================
     2. CBSE COMPOSITE SKILL LAB REQUIREMENT
     ===================================================================== --}}
<section class="py-16 lg:py-20 bg-slate-50 border-t border-b border-slate-200">
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
</section>

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
        Designed to Meet the CBSE Composite Skill Lab Requirement
      </p>
      <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
        The Skillvation Basic Composite Skill Lab is structured as an essential, cost-effective lab solution for schools looking to establish a functional Composite Skill Lab in line with CBSE's requirement.
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
<section class="py-16 lg:py-24 bg-gradient-to-br from-brand-navy via-brand-darknavy to-slate-900 text-white relative overflow-hidden">
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
</section>

{{-- =====================================================================
     5. WHAT STUDENTS EXPERIENCE
     ===================================================================== --}}
<section class="py-16 lg:py-24 bg-white">
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
</section>

{{-- =====================================================================
     6. MULTI-DOMAIN SKILL LEARNING (3 Forms of Work)
     ===================================================================== --}}
<section class="py-16 lg:py-24 bg-slate-50 border-t border-slate-200">
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
</section>

{{-- =====================================================================
     7. DYNAMIC KIT EXPLORER (Live Category & Component Inventory)
     ===================================================================== --}}
<section class="py-16 lg:py-20 bg-white" id="skill-sectors-section">
  <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="text-center max-w-2xl mx-auto mb-12">
      <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-navy/10 text-brand-navy text-xs font-bold uppercase tracking-wider mb-3">
        Interactive Kit Contents
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
          style="position:absolute;left:-20px;top:50%;transform:translateY(-50%);z-index:10;
                 width:40px;height:40px;border-radius:50%;border:1px solid #e2e8f0;
                 background:#fff;color:#475569;cursor:pointer;
                 display:flex;align-items:center;justify-content:center;
                 box-shadow:0 2px 8px rgba(0,0,0,.10);transition:all .2s;">
          <i class="fa-solid fa-chevron-left" style="font-size:12px;"></i>
        </button>
        <button id="sks-box-next" type="button" aria-label="Next"
          style="position:absolute;right:-20px;top:50%;transform:translateY(-50%);z-index:10;
                 width:40px;height:40px;border-radius:50%;border:1px solid #e2e8f0;
                 background:#fff;color:#475569;cursor:pointer;
                 display:flex;align-items:center;justify-content:center;
                 box-shadow:0 2px 8px rgba(0,0,0,.10);transition:all .2s;">
          <i class="fa-solid fa-chevron-right" style="font-size:12px;"></i>
        </button>

        <div style="overflow:hidden;border-radius:12px;">
          <div id="sks-boxes"
               style="display:flex;gap:16px;transition:transform .35s cubic-bezier(.4,0,.2,1);will-change:transform;align-items:flex-start;"></div>
        </div>
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
            <i class="fa-solid fa-sliders" style="font-size:11px;"></i> Customise This Kit
          </a>
          <a id="sks-btn-product" href="#cta-section"
             style="display:inline-flex;align-items:center;gap:8px;padding:11px 22px;border-radius:10px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);color:#fff;font-size:13px;font-weight:700;text-decoration:none;transition:all .2s;white-space:nowrap;">
            <i class="fa-solid fa-file-lines" style="font-size:11px;"></i> Request Proposal
          </a>
        </div>
      </div>

      {{-- Count Badge --}}
      <div style="text-align:center;margin-top:20px;">
        <span style="display:inline-flex;align-items:center;gap:8px;padding:6px 16px;border-radius:20px;background:rgba(11,37,69,.05);border:1px solid rgba(11,37,69,.1);color:#0b2545;font-size:11px;font-weight:700;">
          <i class="fa-solid fa-circle-check" style="color:#22c55e;"></i>
          <span id="sks-count">…</span> categories · <span id="sks-items">…</span> items in this kit
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
<section class="py-16 lg:py-20 bg-white border-t border-slate-100">
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
</section>

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

          <form class="space-y-4" onsubmit="event.preventDefault(); alert('Thank you for reaching out! Our Skill Lab Specialists will contact you within 24 hours with the detailed Basic Composite Skill Lab package.');">
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">School Name *</label>
              <input type="text" required placeholder="e.g. Delhi Public School" class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm focus:border-brand-navy focus:outline-none focus:ring-2 focus:ring-brand-navy/20 transition-all" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Contact Person *</label>
                <input type="text" required placeholder="Your full name" class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm focus:border-brand-navy focus:outline-none focus:ring-2 focus:ring-brand-navy/20 transition-all" />
              </div>
              <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Phone Number *</label>
                <input type="tel" required placeholder="+91 98765 43210" class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm focus:border-brand-navy focus:outline-none focus:ring-2 focus:ring-brand-navy/20 transition-all" />
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Email Address *</label>
              <input type="email" required placeholder="principal@school.edu.in" class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm focus:border-brand-navy focus:outline-none focus:ring-2 focus:ring-brand-navy/20 transition-all" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">City / State *</label>
                <input type="text" required placeholder="e.g. Mumbai, Maharashtra" class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm focus:border-brand-navy focus:outline-none focus:ring-2 focus:ring-brand-navy/20 transition-all" />
              </div>
              <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Request Type *</label>
                <select required class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm focus:border-brand-navy focus:outline-none focus:ring-2 focus:ring-brand-navy/20 transition-all">
                  <option value="package">Request a Detailed Package</option>
                  <option value="visit">Book a Skill Lab Visit</option>
                  <option value="demo">Schedule a Demo with Academic Team</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Message / Space Available</label>
              <textarea rows="3" placeholder="Tell us about your space dimensions (e.g. 600 sq ft) or specific skill areas of interest..." class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm focus:border-brand-navy focus:outline-none focus:ring-2 focus:ring-brand-navy/20 transition-all"></textarea>
            </div>

            <button type="submit" class="w-full py-4 rounded-xl bg-brand-orange hover:bg-brand-orangehover text-white text-sm font-bold shadow-lg shadow-brand-orange/20 transition-all duration-200">
              Submit Request &amp; Get Detailed Brochure
            </button>
          </form>
        </div>
      </div>

    </div>
  </div>
</section>

{{-- =====================================================================
     11. FREQUENTLY ASKED QUESTIONS
     ===================================================================== --}}
<section class="py-16 lg:py-20 bg-white">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="text-center mb-12 space-y-3">
      <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-navy/10 text-brand-navy text-xs font-bold uppercase tracking-wider">
        FAQs
      </div>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-navy">
        Composite Skill Lab FAQs
      </h2>
    </div>

    @php
    $faqs = [
      ['What is the CBSE mandate for the Composite Skill Lab?',
       'Under Circular No. Skill-75/2024 (and reiterated in Skill-01/2025), CBSE has mandated all affiliated schools to establish a Composite Skill Lab. Schools seeking fresh affiliation must have the lab ready, while existing schools are granted a 3-year transition window.'],
      ['What is included in the Skillvation Basic Package?',
       'The Basic Package includes core lab infrastructure & furniture, essential multi-domain tools & equipment, student activity kits and materials, teacher demonstration resources, project-based learning guides, and organized storage.'],
      ['What are the lab space options allowed by CBSE?',
       'CBSE allows two configurations: (1) One single Composite Skill Lab of 600 sq. ft. for Classes VI–XII, or (2) Two separate labs of 400 sq. ft. each — one for Classes VI–X and one for Classes XI–XII.'],
      ['How does the Basic Package support different skill domains?',
       'The setup supports all three core forms of work defined by CBSE/NCF-SE: Life Forms (food, health, herbal skills), Materials & Machines (coding, robotics, pottery, crafts, media), and Human Services (finance, retail, tourism, communications).'],
      ['Can we upgrade or customize the Basic Package later?',
       'Yes, the Basic Package is modular and scalable. Schools can start with the foundational setup and seamlessly add advanced equipment, AI/Robotics modules, or specialized vocation kits over time.'],
    ];
    @endphp

    <div class="space-y-3">
      @foreach($faqs as [$q, $a])
      <details class="group bg-slate-50/70 border border-slate-200 rounded-2xl overflow-hidden transition-all">
        <summary class="flex items-center justify-between gap-4 px-6 py-4 cursor-pointer select-none list-none">
          <span class="text-sm sm:text-base font-bold text-brand-navy">{{ $q }}</span>
          <i class="fa-solid fa-chevron-down faq-chevron text-slate-400 text-xs flex-shrink-0 transition-transform duration-200"></i>
        </summary>
        <div class="px-6 pb-5 text-sm text-slate-600 leading-relaxed border-t border-slate-200/60 pt-4">
          {{ $a }}
        </div>
      </details>
      @endforeach
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
        [route('labs.ai-robotics'), 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=500&q=80', 'AI & Robotics Lab', 'Hands-on robotics, coding & AI experiments'],
        [route('labs.stem'),        'https://images.unsplash.com/photo-1516339901601-2e1b62dc0c45?auto=format&fit=crop&w=500&q=80', 'STEM Lab', 'Electronics, IoT & project-based learning'],
        [route('labs.ecec'),        'https://images.unsplash.com/photo-1581092335397-9583fe92d232?auto=format&fit=crop&w=500&q=80', 'ECEC Lab', 'Early childhood exploration & creativity'],
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
(function(){
'use strict';

var BASE = window.location.protocol + '//' + window.location.hostname + '/club-shop';

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
  body.style.maxHeight = body.scrollHeight + 200 + 'px';
  if(chev) chev.style.transform='rotate(180deg)';
  row.dataset.open='1';
}
function closeRow(row){
  var body=row.querySelector('.sks-body'), chev=row.querySelector('.sks-chev');
  if(body) body.style.maxHeight='0';
  if(chev) chev.style.transform='rotate(0deg)';
  row.dataset.open='0';
}

function buildRow(catName, comps, theme, productUrl){
  var icon  = getIcon(catName);
  var rowId = 'sks-r-'+Math.random().toString(36).slice(2,8);
  var totalQ=0, totalV=0;
  comps.forEach(function(c){
    var q=c.is_optional?0:Number(c.required_quantity);
    totalQ+=q; totalV+=Number(c.unit_price)*q;
  });
  var subtitle = totalQ>0
    ? rupee(totalV)+' · '+totalQ+' unit'+(totalQ!==1?'s':'')
    : comps.length+' item'+(comps.length!==1?'s':'')+' (optional)';

  var itemRows = comps.map(function(c){
    var qty    = c.is_optional?0:Number(c.required_quantity);
    var line   = Number(c.unit_price)*qty;
    var opt    = c.is_optional
      ? '<span style="margin-left:5px;padding:1px 5px;border-radius:3px;background:#fef3c7;color:#92400e;border:1px solid #fde68a;font-size:9px;font-weight:700;">optional</span>'
      : '';
    var img    = c.image_small
      ? '<img src="'+esc(c.image_small)+'" alt="'+esc(c.title)+'" loading="lazy" '
          +'style="width:32px;height:32px;object-fit:cover;border-radius:4px;border:1px solid #e2e8f0;flex-shrink:0;" '
          +'onerror="this.style.display=\'none\'">'
      : '<span style="width:32px;height:32px;border-radius:4px;background:#f1f5f9;border:1px solid #e2e8f0;flex-shrink:0;display:inline-flex;align-items:center;justify-content:center;">'
          +'<i class="fa-solid fa-cube" style="color:#cbd5e1;font-size:11px;"></i></span>';

    return '<div style="display:flex;align-items:center;gap:10px;padding:8px 14px;border-bottom:1px solid #f1f5f9;">'
      +img
      +'<div style="flex:1;min-width:0;">'
        +'<div style="font-size:12.5px;font-weight:600;color:#1e293b;line-height:1.3;">'+esc(c.title)+opt+'</div>'
        +(c.sku?'<div style="font-size:10px;color:#94a3b8;font-family:monospace;">'+esc(c.sku)+'</div>':'')
      +'</div>'
      +'<div style="text-align:right;flex-shrink:0;">'
        +'<div style="font-size:12px;font-weight:700;color:'+(c.is_optional?'#94a3b8':theme.dotColor)+';">'+(c.is_optional?'—':rupee(line))+'</div>'
        +'<div style="font-size:10.5px;color:#94a3b8;">'+(c.is_optional?'0 (opt)':qty+' × '+rupee(c.unit_price))+'</div>'
      +'</div>'
    +'</div>';
  }).join('');

  return '<div class="sks-row" id="'+rowId+'" data-open="0" '
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
      +'<i class="fa-solid fa-chevron-down sks-chev" style="font-size:11px;color:#94a3b8;transition:transform .25s;flex-shrink:0;"></i>'
    +'</button>'
    +'<div class="sks-body" style="max-height:0;overflow:hidden;transition:max-height .3s ease;">'
      +itemRows
      +(totalQ>0?'<div style="display:flex;justify-content:space-between;align-items:center;padding:8px 14px;background:#f8fafc;border-top:1px solid #e2e8f0;">'
          +'<span style="font-size:11px;font-weight:700;color:#64748b;">'+comps.length+' item'+(comps.length!==1?'s':'')+' · '+totalQ+' units</span>'
          +'<span style="font-size:13px;font-weight:800;color:'+theme.dotColor+';">'+rupee(totalV)+'</span>'
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
      clearTimeout(timer); if(btn) btn.style.background=hoverBg; openRow(row);
    });
    row.addEventListener('mouseleave',function(){
      if(btn) btn.style.background='transparent';
      timer=setTimeout(function(){closeRow(row);},150);
    });
    if(body){
      body.addEventListener('mouseenter',function(){ clearTimeout(timer); });
      body.addEventListener('mouseleave',function(){
        timer=setTimeout(function(){closeRow(row);},150);
      });
    }
  });
}

function buildBox(parentName, parentId, subGroups, theme, productUrl, isCenter){
  var el = document.createElement('div');
  var flex = isCenter ? '1.4 1 200px' : '1 1 180px';
  el.style.cssText = 'border:2px solid '+theme.border+';background:'+theme.bg+';'
    +'overflow:hidden;flex:'+flex+';min-width:180px;border-radius:12px;'
    +(theme.shadow?'box-shadow:0 8px 24px rgba(0,0,0,.08);':'');

  var catNames = Object.keys(subGroups);

  var hdr = '<div style="background:'+theme.hdrBg+';padding:11px 16px;display:flex;align-items:center;gap:10px;">'
    +'<i class="fa-solid '+getIcon(parentName)+'" style="color:#fff;font-size:15px;"></i>'
    +'<span style="font-weight:700;color:#fff;font-size:15px;">'+esc(parentName)+'</span>'
    +'<span style="margin-left:auto;background:rgba(255,255,255,.22);color:#fff;font-size:10px;font-weight:700;padding:2px 9px;border-radius:20px;">'
      +catNames.length+' categor'+(catNames.length!==1?'ies':'y')
    +'</span>'
  +'</div>';

  var rows = '<div data-sks-rows>'
    + catNames.map(function(cn){ return buildRow(cn, subGroups[cn], theme, productUrl); }).join('')
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
  var customiseUrl = productUrl + '#tab_bundle_contents';

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

  parentIds.forEach(function(pid){
    var g      = groups[pid];
    var theme  = getTheme(g.name);
    var isCenter = (pid===maxKey && parentIds.length>1);
    var box    = buildBox(g.name, pid, g.subGroups, theme, productUrl, isCenter);
    boxesEl.appendChild(box);
  });

  (function(){
    var VISIBLE = 2;
    var GAP     = 16;
    var current = 0;
    var boxes   = Array.prototype.slice.call(boxesEl.children);
    var total   = boxes.length;
    var prevBtn = document.getElementById('sks-box-prev');
    var nextBtn = document.getElementById('sks-box-next');

    function getCardW(){
      var containerW = boxesEl.parentElement.offsetWidth;
      return Math.floor((containerW - GAP * (VISIBLE - 1)) / VISIBLE);
    }

    function applyWidths(){
      var w = getCardW();
      boxes.forEach(function(b){
        b.style.minWidth  = w + 'px';
        b.style.maxWidth  = w + 'px';
        b.style.flex      = '0 0 ' + w + 'px';
      });
    }

    function go(idx){
      current = Math.max(0, Math.min(idx, Math.max(0, total - VISIBLE)));
      var w   = getCardW();
      boxesEl.style.transform = 'translateX(-' + (current * (w + GAP)) + 'px)';
      if(prevBtn) prevBtn.style.opacity = current === 0 ? '.3' : '1';
      if(nextBtn) nextBtn.style.opacity = current >= total - VISIBLE ? '.3' : '1';
    }

    applyWidths();
    go(0);

    if(total <= VISIBLE){
      if(prevBtn) prevBtn.style.display = 'none';
      if(nextBtn) nextBtn.style.display = 'none';
    } else {
      if(prevBtn) prevBtn.addEventListener('click', function(){ go(current - 1); });
      if(nextBtn) nextBtn.addEventListener('click', function(){ go(current + 1); });
    }

    window.addEventListener('resize', function(){ applyWidths(); go(current); });
  })();

  if(countEl) countEl.textContent = totalCats;
  if(itemsEl) itemsEl.textContent = totalItems;
  var btnC=document.getElementById('sks-btn-customise'), btnP=document.getElementById('sks-btn-product');
  if(btnC) btnC.href = customiseUrl;
  if(btnP) btnP.href = productUrl;

  loadEl.classList.add('hidden');
  contentEl.classList.remove('hidden');
})
.catch(function(e){
  console.log('sks notice (offline/static fallback):', e);
  if(loadEl) loadEl.classList.add('hidden');
  if(errEl) errEl.classList.add('hidden');
});

})();
</script>
@endpush