@extends('frontend.home-four.layouts.master')

@section('meta_title', 'Train the Trainer (TTT) — ' . config('app.name', 'Skillvation'))
@section('meta_description', 'Empowering teachers for effective, modern classrooms. Comprehensive professional development initiative.')

@push('styles')
<style>
  /* ── Hero Banner (Matched exactly with skillvation.com/TTT) ── */
  .ttt-hero {
    position: relative;
    width: 100%;
    min-height: 480px;
    height: 480px;
    display: flex;
    align-items: center;
    background-image: url('{{ asset('designs/img/TTT-1.png') }}');
    background-size: cover;
    background-position: 50% 50%;
    background-repeat: no-repeat;
    overflow: hidden;
  }
  .ttt-hero::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(281.88deg, rgba(0, 0, 0, 0) 0.3%, rgba(0, 0, 0, 0.7) 56.82%, rgba(0, 0, 0, 0.7) 97.64%);
    z-index: 1;
    pointer-events: none;
  }
  .ttt-hero__container {
    position: relative;
    z-index: 2;
    width: 100%;
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 2rem;
  }
  .ttt-hero__inner {
    max-width: 68%;
    color: #ffffff;
    text-align: left;
  }
  .ttt-hero__title {
    font-size: 32px;
    font-weight: 500;
    line-height: 1.35;
    color: #ffffff;
    margin: 0 0 16px 0;
  }
  .ttt-hero__divider {
    border: none;
    border-top: 1px solid rgba(255, 255, 255, 0.25);
    margin: 14px 0 16px 0;
    width: 100%;
  }
  .ttt-hero__desc {
    font-size: 16px;
    font-weight: 300;
    line-height: 1.65;
    color: #ffffff;
    opacity: 0.95;
    margin: 0;
  }

  @media (max-width: 991px) {
    .ttt-hero {
      height: auto;
      min-height: 420px;
      padding: 4rem 0;
    }
    .ttt-hero::after {
      background: linear-gradient(to bottom, rgba(0, 0, 0, 0.5) 0%, rgba(0, 0, 0, 0.85) 100%);
    }
    .ttt-hero__inner {
      max-width: 100%;
    }
    .ttt-hero__title {
      font-size: 26px;
    }
    .ttt-hero__desc {
      font-size: 15px;
    }
  }
</style>
@endpush

@section('contents')
<!-- BEGIN: Hero Section -->
<section class="ttt-hero">
  <div class="ttt-hero__container">
    <div class="ttt-hero__inner">
      <h1 class="ttt-hero__title">Empowering Teachers for Effective, Modern Classrooms</h1>
      <hr class="ttt-hero__divider" />
      <p class="ttt-hero__desc">
        Skillvation’s Train the Teacher Programme is a comprehensive professional-development initiative designed to strengthen both foundational teaching skills and subject-specific classroom delivery. The programme equips educators with practical strategies to improve student engagement, concept clarity, and classroom effectiveness—aligned with today’s learner needs and modern educational practices.
      </p>
    </div>
  </div>
</section>
<!-- END: Hero Section -->

<!-- BEGIN: General Teacher Training Modules -->
<section class="py-20 bg-white relative">
  <div class="absolute left-0 top-20 w-48 h-48 border border-blue-200 rounded-r-full -translate-x-1/2 pointer-events-none"></div>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="text-center mb-16">
      <p class="text-blue-600 font-medium mb-2">Turning knowledge into powerful learning experience</p>
      <h2 class="text-3xl font-bold text-slate-900 mb-4">General Teacher Training Modules</h2>
      <p class="text-slate-600 max-w-2xl mx-auto">Our general training modules focus on the core competencies every teacher needs to succeed:</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      
      <!-- Module 1: Educational Psychology -->
      <div class="bg-white rounded-2xl overflow-hidden border border-blue-100/80 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col group">
        <div class="relative h-44 overflow-hidden bg-slate-100">
          <img src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=600&q=80"
               alt="Educational Psychology Module"
               class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
          <div class="absolute inset-0 bg-gradient-to-t from-slate-900/70 via-slate-900/20 to-transparent"></div>
          <div class="absolute bottom-3 left-4 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-white/95 backdrop-blur-sm flex items-center justify-center text-blue-600 shadow-sm">
              <i class="fa-solid fa-brain text-sm"></i>
            </span>
            <span class="text-white font-semibold text-lg drop-shadow-sm">Educational Psychology</span>
          </div>
        </div>
        <div class="p-6 flex-1 flex flex-col justify-between bg-white">
          <p class="text-slate-600 text-sm leading-relaxed">Understanding child behaviour, learning styles, motivation, and emotional needs.</p>
        </div>
      </div>

      <!-- Module 2: Classroom Management -->
      <div class="bg-white rounded-2xl overflow-hidden border border-blue-100/80 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col group">
        <div class="relative h-44 overflow-hidden bg-slate-100">
          <img src="https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&w=600&q=80"
               alt="Classroom Management Module"
               class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
          <div class="absolute inset-0 bg-gradient-to-t from-slate-900/70 via-slate-900/20 to-transparent"></div>
          <div class="absolute bottom-3 left-4 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-white/95 backdrop-blur-sm flex items-center justify-center text-blue-600 shadow-sm">
              <i class="fa-solid fa-chalkboard-user text-sm"></i>
            </span>
            <span class="text-white font-semibold text-lg drop-shadow-sm">Classroom Management</span>
          </div>
        </div>
        <div class="p-6 flex-1 flex flex-col justify-between bg-white">
          <p class="text-slate-600 text-sm leading-relaxed">Techniques to create structured, inclusive, and disciplined learning environments.</p>
        </div>
      </div>

      <!-- Module 3: Student Engagement Strategies -->
      <div class="bg-white rounded-2xl overflow-hidden border border-blue-100/80 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col group">
        <div class="relative h-44 overflow-hidden bg-slate-100">
          <img src="https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=600&q=80"
               alt="Student Engagement Strategies Module"
               class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
          <div class="absolute inset-0 bg-gradient-to-t from-slate-900/70 via-slate-900/20 to-transparent"></div>
          <div class="absolute bottom-3 left-4 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-white/95 backdrop-blur-sm flex items-center justify-center text-blue-600 shadow-sm">
              <i class="fa-solid fa-users text-sm"></i>
            </span>
            <span class="text-white font-semibold text-lg drop-shadow-sm">Student Engagement</span>
          </div>
        </div>
        <div class="p-6 flex-1 flex flex-col justify-between bg-white">
          <p class="text-slate-600 text-sm leading-relaxed">Interactive methods to encourage participation, curiosity, and critical thinking.</p>
        </div>
      </div>

      <!-- Module 4: Assessment & Feedback Practices -->
      <div class="bg-white rounded-2xl overflow-hidden border border-blue-100/80 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col group">
        <div class="relative h-44 overflow-hidden bg-slate-100">
          <img src="https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=600&q=80"
               alt="Assessment & Feedback Practices Module"
               class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
          <div class="absolute inset-0 bg-gradient-to-t from-slate-900/70 via-slate-900/20 to-transparent"></div>
          <div class="absolute bottom-3 left-4 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-white/95 backdrop-blur-sm flex items-center justify-center text-blue-600 shadow-sm">
              <i class="fa-solid fa-clipboard-check text-sm"></i>
            </span>
            <span class="text-white font-semibold text-lg drop-shadow-sm">Assessment &amp; Feedback</span>
          </div>
        </div>
        <div class="p-6 flex-1 flex flex-col justify-between bg-white">
          <p class="text-slate-600 text-sm leading-relaxed">Designing meaningful assessments and constructive feedback for student growth.</p>
        </div>
      </div>

    </div>
  </div>
</section>
<!-- END: General Teacher Training Modules -->

<!-- BEGIN: Subject-Specific Training Modules -->
<section class="py-20 bg-slate-50 relative">
  <div class="absolute right-0 top-40 w-64 h-64 border border-blue-200 rounded-l-full translate-x-1/2 pointer-events-none"></div>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="text-center mb-16">
      <p class="text-blue-600 font-medium mb-2">From subject experts to master facilitator</p>
      <h2 class="text-3xl font-bold text-slate-900 mb-4">Subject-Specific Training Modules</h2>
      <p class="text-slate-600 max-w-2xl mx-auto">We offer specialised training to help teachers teach subjects conceptually and practically, rather than through rote methods:</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      
      <!-- Module 1: Mathematics -->
      <div class="bg-white rounded-2xl overflow-hidden border border-blue-100/80 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col group">
        <div class="relative h-44 overflow-hidden bg-slate-100">
          <img src="https://images.unsplash.com/photo-1509228468518-180dd4864904?auto=format&fit=crop&w=600&q=80"
               alt="Mathematics Training Module"
               class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
          <div class="absolute inset-0 bg-gradient-to-t from-slate-900/70 via-slate-900/20 to-transparent"></div>
          <div class="absolute bottom-3 left-4 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-white/95 backdrop-blur-sm flex items-center justify-center text-blue-600 shadow-sm">
              <i class="fa-solid fa-calculator text-sm"></i>
            </span>
            <span class="text-white font-semibold text-lg drop-shadow-sm">Mathematics</span>
          </div>
        </div>
        <div class="p-6 flex-1 flex flex-col justify-between bg-white">
          <p class="text-slate-600 text-sm leading-relaxed">Teaching math using real-life applications, visual models, and logical reasoning.</p>
        </div>
      </div>

      <!-- Module 2: Science -->
      <div class="bg-white rounded-2xl overflow-hidden border border-blue-100/80 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col group">
        <div class="relative h-44 overflow-hidden bg-slate-100">
          <img src="https://images.unsplash.com/photo-1532094349884-543bc11b234d?auto=format&fit=crop&w=600&q=80"
               alt="Science Training Module"
               class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
          <div class="absolute inset-0 bg-gradient-to-t from-slate-900/70 via-slate-900/20 to-transparent"></div>
          <div class="absolute bottom-3 left-4 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-white/95 backdrop-blur-sm flex items-center justify-center text-blue-600 shadow-sm">
              <i class="fa-solid fa-flask text-sm"></i>
            </span>
            <span class="text-white font-semibold text-lg drop-shadow-sm">Science</span>
          </div>
        </div>
        <div class="p-6 flex-1 flex flex-col justify-between bg-white">
          <p class="text-slate-600 text-sm leading-relaxed">Concept explanation through experiments, diagrams, and everyday examples.</p>
        </div>
      </div>

      <!-- Module 3: Languages -->
      <div class="bg-white rounded-2xl overflow-hidden border border-blue-100/80 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col group">
        <div class="relative h-44 overflow-hidden bg-slate-100">
          <img src="https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?auto=format&fit=crop&w=600&q=80"
               alt="Languages Training Module"
               class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
          <div class="absolute inset-0 bg-gradient-to-t from-slate-900/70 via-slate-900/20 to-transparent"></div>
          <div class="absolute bottom-3 left-4 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-white/95 backdrop-blur-sm flex items-center justify-center text-blue-600 shadow-sm">
              <i class="fa-solid fa-book-open text-sm"></i>
            </span>
            <span class="text-white font-semibold text-lg drop-shadow-sm">Languages</span>
          </div>
        </div>
        <div class="p-6 flex-1 flex flex-col justify-between bg-white">
          <p class="text-slate-600 text-sm leading-relaxed">Improving comprehension, communication, vocabulary, and expression skills.</p>
        </div>
      </div>

      <!-- Module 4: Concept Visualisation -->
      <div class="bg-white rounded-2xl overflow-hidden border border-blue-100/80 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col group">
        <div class="relative h-44 overflow-hidden bg-slate-100">
          <img src="https://images.unsplash.com/photo-1531403009284-440f080d1e12?auto=format&fit=crop&w=600&q=80"
               alt="Concept Visualisation Module"
               class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
          <div class="absolute inset-0 bg-gradient-to-t from-slate-900/70 via-slate-900/20 to-transparent"></div>
          <div class="absolute bottom-3 left-4 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-white/95 backdrop-blur-sm flex items-center justify-center text-blue-600 shadow-sm">
              <i class="fa-solid fa-shapes text-sm"></i>
            </span>
            <span class="text-white font-semibold text-lg drop-shadow-sm">Concept Visualisation</span>
          </div>
        </div>
        <div class="p-6 flex-1 flex flex-col justify-between bg-white">
          <p class="text-slate-600 text-sm leading-relaxed">Effective ways to explain diagrams, processes, and abstract ideas clearly.</p>
        </div>
      </div>

    </div>
  </div>
</section>
<!-- END: Subject-Specific Training Modules -->

<!-- BEGIN: Strengthened by LATAA OS Section -->
<section class="py-20 bg-gradient-to-b from-blue-50/60 via-white to-slate-50 relative overflow-hidden border-b border-slate-200/70">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center mb-12">
      
      <!-- Left Column: Header and Overview -->
      <div class="lg:col-span-7">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-100 text-blue-700 text-xs font-bold uppercase tracking-wider mb-4 border border-blue-200 shadow-sm">
          <i class="fa-solid fa-microchip text-blue-600"></i> Strengthened by LATAA OS
        </div>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-4 leading-tight">
          AI-Powered Teaching Assistance for Skill Education
        </h2>
        <p class="text-slate-600 text-lg leading-relaxed mb-6">
          <strong>LATAA OS</strong> strengthens the TTT programme by integrating AI-powered academic assistance into teacher preparation and classroom delivery.
        </p>

        <h3 class="text-base font-bold text-slate-800 uppercase tracking-wide mb-4 flex items-center gap-2">
          <i class="fa-solid fa-wand-magic-sparkles text-blue-600"></i> It supports teachers with:
        </h3>

        <div class="space-y-3.5">
          <div class="flex items-start gap-3.5 p-3.5 rounded-xl bg-white border border-slate-200/80 shadow-sm hover:shadow-md hover:border-blue-300 transition-all duration-300">
            <span class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0 text-base shadow-xs mt-0.5">
              <i class="fa-solid fa-clipboard-list"></i>
            </span>
            <div class="text-slate-700 font-medium text-base leading-snug pt-1">
              AI-assisted lesson planning and teaching scripts.
            </div>
          </div>

          <div class="flex items-start gap-3.5 p-3.5 rounded-xl bg-white border border-slate-200/80 shadow-sm hover:shadow-md hover:border-indigo-300 transition-all duration-300">
            <span class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0 text-base shadow-xs mt-0.5">
              <i class="fa-solid fa-folder-open"></i>
            </span>
            <div class="text-slate-700 font-medium text-base leading-snug pt-1">
              Structured teaching resources and learning materials.
            </div>
          </div>

          <div class="flex items-start gap-3.5 p-3.5 rounded-xl bg-white border border-slate-200/80 shadow-sm hover:shadow-md hover:border-amber-300 transition-all duration-300">
            <span class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0 text-base shadow-xs mt-0.5">
              <i class="fa-solid fa-lightbulb"></i>
            </span>
            <div class="text-slate-700 font-medium text-base leading-snug pt-1">
              Activity-based teaching ideas and classroom support.
            </div>
          </div>

          <div class="flex items-start gap-3.5 p-3.5 rounded-xl bg-white border border-slate-200/80 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all duration-300">
            <span class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0 text-base shadow-xs mt-0.5">
              <i class="fa-solid fa-file-circle-check"></i>
            </span>
            <div class="text-slate-700 font-medium text-base leading-snug pt-1">
              Question banks, worksheets, and assessments.
            </div>
          </div>

          <div class="flex items-start gap-3.5 p-3.5 rounded-xl bg-white border border-slate-200/80 shadow-sm hover:shadow-md hover:border-purple-300 transition-all duration-300">
            <span class="w-9 h-9 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0 text-base shadow-xs mt-0.5">
              <i class="fa-solid fa-cubes-stacked"></i>
            </span>
            <div class="text-slate-700 font-medium text-base leading-snug pt-1">
              Assistance in translating curriculum concepts into practical learning experiences.
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Visual Card -->
      <div class="lg:col-span-5">
        <div class="relative rounded-2xl overflow-hidden shadow-2xl border border-blue-200/60 bg-slate-900 group">
          <img src="{{ asset('frontend/img/skillbox/lataa_hero_ai.jpg') }}" alt="LATAA OS AI-Powered Teaching Assistance" class="w-full h-[430px] object-cover group-hover:scale-105 transition-transform duration-700 opacity-90" />
          <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/40 to-transparent"></div>
          
          <div class="absolute top-4 right-4">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-cyan-500/90 text-slate-950 text-xs font-extrabold uppercase tracking-wider backdrop-blur-md shadow-lg">
              <i class="fa-solid fa-sparkles text-[10px]"></i> AI Assistant
            </span>
          </div>

          <div class="absolute bottom-6 left-6 right-6 text-white">
            <div class="text-xs uppercase tracking-widest text-cyan-400 font-bold mb-1">LATAA OS FOR TEACHERS</div>
            <h4 class="text-xl font-bold mb-2">Empowering Modern Educators</h4>
            <p class="text-slate-300 text-sm leading-relaxed mb-4">
              AI intelligence that saves teacher preparation time while elevating experiential classroom delivery.
            </p>
            <a href="{{ route('lataa') }}" class="inline-flex items-center gap-2 text-cyan-300 hover:text-white font-semibold text-sm transition-colors duration-200">
              Discover LATAA OS Platform <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
          </div>
        </div>
      </div>

    </div>

    <!-- Outcome Callout Banner -->
    <div class="bg-gradient-to-r from-blue-900 via-indigo-950 to-slate-900 text-white rounded-2xl p-6 sm:p-8 shadow-xl border border-blue-500/30 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
      <div class="flex items-start gap-4">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-400 flex items-center justify-center text-white text-xl flex-shrink-0 shadow-lg mt-0.5">
          <i class="fa-solid fa-graduation-cap"></i>
        </div>
        <div>
          <span class="inline-block text-xs font-bold tracking-widest uppercase text-cyan-300 mb-1">The Outcome</span>
          <p class="text-base sm:text-lg text-slate-100 font-medium leading-relaxed m-0">
            Teachers are equipped not only with the methodology to teach skill education but also with intelligent tools to plan, prepare, and deliver lessons effectively.
          </p>
        </div>
      </div>
      <a href="{{ route('lataa') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-cyan-400 hover:bg-cyan-300 text-slate-950 font-bold text-sm transition-all duration-300 shadow-md flex-shrink-0 hover:scale-105">
        Explore LATAA OS <i class="fa-solid fa-arrow-right text-xs"></i>
      </a>
    </div>

  </div>
</section>
<!-- END: Strengthened by LATAA OS Section -->

<!-- BEGIN: Why Choose Section -->
<section class="py-20 relative">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
      <div class="rounded-2xl overflow-hidden shadow-xl">
        <img alt="Teacher interacting with students" class="w-full h-auto object-cover"
          src="{{ asset('designs/img/TTT-2.jpeg') }}" />
      </div>
      <div>
        <h2 class="text-3xl font-bold text-slate-900 mb-8 leading-tight">Why Choose Skillvation's Train the Teacher Programme?</h2>
        <ul class="space-y-6">
          <li class="flex items-start">
            <span class="text-blue-500 mr-4 mt-1">
              <i class="fa-solid fa-circle-check text-xl"></i>
            </span>
            <span class="text-lg text-slate-700">Practical, classroom-ready training</span>
          </li>
          <li class="flex items-start">
            <span class="text-blue-500 mr-4 mt-1">
              <i class="fa-solid fa-circle-check text-xl"></i>
            </span>
            <span class="text-lg text-slate-700">Balanced focus on pedagogy and subject mastery</span>
          </li>
          <li class="flex items-start">
            <span class="text-blue-500 mr-4 mt-1">
              <i class="fa-solid fa-circle-check text-xl"></i>
            </span>
            <span class="text-lg text-slate-700">Aligned with modern teaching expectations and NEP principles</span>
          </li>
          <li class="flex items-start">
            <span class="text-blue-500 mr-4 mt-1">
              <i class="fa-solid fa-circle-check text-xl"></i>
            </span>
            <span class="text-lg text-slate-700">Suitable for new and experienced teachers</span>
          </li>
          <li class="flex items-start">
            <span class="text-blue-500 mr-4 mt-1">
              <i class="fa-solid fa-circle-check text-xl"></i>
            </span>
            <span class="text-lg text-slate-700">Customisable for school needs</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</section>
<!-- END: Why Choose Section -->

<!-- BEGIN: Course Carousel Section -->
@include('frontend.home-four.components.course-carousel', [
  'type' => '10',
  'lms_only' => '1',
  'title' => 'Explore Our Teachers Training Program',
  'subtitle' => 'Our specialised modules focus on practical classroom delivery and competencies',
  'tagline' => 'Teacher Development Courses'
])
<!-- END: Course Carousel Section -->

<!-- BEGIN: Specialised Teacher Training Programmes -->
<!-- <section class="py-20 bg-slate-50 relative">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="text-center mb-16">
      <p class="text-blue-600 font-medium mb-2">Shape trainers who shape futures</p>
      <h2 class="text-3xl font-bold text-slate-900 mb-4">Specialised Teacher Training Programmes</h2>
      <p class="text-slate-600 max-w-2xl mx-auto">Skillvation also offers structured certification-oriented programmes for early-years educators:</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8"> -->
      
      <!-- Programme 1: Nursery Teacher Training -->
      <!-- <div class="bg-white rounded-2xl overflow-hidden border border-blue-100/80 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col group">
        <div class="relative h-52 overflow-hidden bg-slate-100">
          <img src="https://images.unsplash.com/photo-1587654780291-39c9404d746b?auto=format&fit=crop&w=600&q=80"
               alt="Nursery Teacher Training"
               class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
          <div class="absolute inset-0 bg-gradient-to-t from-slate-900/70 via-slate-900/20 to-transparent"></div>
          <div class="absolute bottom-3 left-4 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-white/95 backdrop-blur-sm flex items-center justify-center text-blue-600 shadow-sm">
              <i class="fa-solid fa-seedling text-sm"></i>
            </span>
            <span class="text-white font-semibold text-lg drop-shadow-sm">Nursery Teacher Training</span>
          </div>
        </div>
        <div class="p-6 flex-1 flex flex-col justify-between bg-white">
          <p class="text-slate-600 text-sm leading-relaxed">Foundational training for preschool educators in child development and early learning.</p>
        </div>
      </div> -->

      <!-- Programme 2: Montessori Teacher Training -->
      <!-- <div class="bg-white rounded-2xl overflow-hidden border border-blue-100/80 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col group">
        <div class="relative h-52 overflow-hidden bg-slate-100">
          <img src="https://images.unsplash.com/photo-1596495577886-d920f1fb7238?auto=format&fit=crop&w=600&q=80"
               alt="Montessori Teacher Training"
               class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
          <div class="absolute inset-0 bg-gradient-to-t from-slate-900/70 via-slate-900/20 to-transparent"></div>
          <div class="absolute bottom-3 left-4 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-white/95 backdrop-blur-sm flex items-center justify-center text-blue-600 shadow-sm">
              <i class="fa-solid fa-cubes text-sm"></i>
            </span>
            <span class="text-white font-semibold text-lg drop-shadow-sm">Montessori Teacher Training</span>
          </div>
        </div>
        <div class="p-6 flex-1 flex flex-col justify-between bg-white">
          <p class="text-slate-600 text-sm leading-relaxed">Montessori-based pedagogy focusing on self-directed, activity-based learning.</p>
        </div>
      </div> -->

      <!-- Programme 3: Early Childhood Education -->
      <!-- <div class="bg-white rounded-2xl overflow-hidden border border-blue-100/80 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col group">
        <div class="relative h-52 overflow-hidden bg-slate-100">
          <img src="https://images.unsplash.com/photo-1588072432836-e10032774350?auto=format&fit=crop&w=600&q=80"
               alt="Early Childhood Education"
               class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
          <div class="absolute inset-0 bg-gradient-to-t from-slate-900/70 via-slate-900/20 to-transparent"></div>
          <div class="absolute bottom-3 left-4 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-white/95 backdrop-blur-sm flex items-center justify-center text-blue-600 shadow-sm">
              <i class="fa-solid fa-hands-holding-child text-sm"></i>
            </span>
            <span class="text-white font-semibold text-lg drop-shadow-sm">Early Childhood Education</span>
          </div>
        </div>
        <div class="p-6 flex-1 flex flex-col justify-between bg-white">
          <p class="text-slate-600 text-sm leading-relaxed">Holistic training in early-years teaching, classroom setup, and child engagement.</p>
        </div>
      </div> -->

    <!-- </div>
  </div>
</section> -->
<!-- END: Specialised Teacher Training Programmes -->

<!-- BEGIN: CTA Section -->
<section class="py-24 bg-white relative overflow-hidden">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
    <p class="text-blue-600 font-medium mb-4">Powerful delivery starts with powerful trainers</p>
    <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-6">Building Confident Teachers, Stronger Classrooms</h2>
    <p class="text-lg text-slate-600 mb-8">
      Skillvation's Train the Teacher Programme enables educators to teach with clarity, confidence, and creativity, ensuring better learning outcomes and more engaging classrooms.
    </p>
    <!-- <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-full bg-primary hover:bg-primary-dark text-white font-semibold transition-all shadow-md">
      <span>Get Started Today</span>
      <i class="fa-solid fa-arrow-right text-xs"></i>
    </a> -->
  </div>
</section>
<!-- END: CTA Section -->
@endsection
