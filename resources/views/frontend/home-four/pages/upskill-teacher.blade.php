@extends('frontend.home-four.layouts.master')

@section('meta_title', 'Upskill 4 Teacher — ' . config('app.name', 'Skillvation'))
@section('meta_description', 'Why Teacher UpSkilling is essential for CBSE Skill Education. Empowering teachers to guide students better.')

@push('styles')
<style>
  .list-star::before {
    content: "★";
    position: absolute;
    left: 0;
    top: 2px;
    color: #1976d2;
    font-size: 14px;
  }
  .list-burst::before {
    content: "✦";
    position: absolute;
    left: 0;
    top: 2px;
    color: #1976d2;
    font-size: 14px;
  }
</style>
@endpush

@section('contents')
<!-- BEGIN: Hero Section -->
<section class="py-24 md:py-32 relative overflow-hidden bg-slate-900 text-white">
  <div class="absolute inset-0 z-0">
    <img alt="Teachers and students" class="w-full h-full object-cover opacity-30" src="{{ asset('designs/img/Upskill4Teacher-1.jpg') }}" />
  </div>
  <div class="container max-w-7xl mx-auto px-4 md:px-8 relative z-10 text-white">
    <div class="max-w-2xl">
      <h1 class="text-4xl md:text-4xl font-medium mb-4 leading-tight text-white">Why Teacher UpSkilling Is Important for CBSE Skill Education</h1>
      <p class="text-lg md:text-xl font-light leading-relaxed text-white">Learn from experts through structured, engaging, and career-ready lessons designed to help you grow.</p>
    </div>
  </div>
</section>
<!-- END: Hero Section -->

<!-- BEGIN: Intro Section -->
<section class="py-16 md:py-24 relative overflow-hidden bg-white">
  <div class="container max-w-5xl mx-auto px-4 md:px-8 text-center">
    <h3 class="text-primary font-medium mb-2 text-sm uppercase tracking-wider">Transform your teaching, Elevate your impact</h3>
    <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-6">Empowering Teachers to Guide Students Better</h2>
    <p class="text-slate-600 leading-relaxed text-base md:text-lg">
      The role of a teacher has evolved beyond traditional textbook instruction. CBSE's Skill Education framework requires teachers to adopt hands-on learning, interdisciplinary teaching, and real-world application of concepts. CBSE skill courses demand activity-based, project-driven, and experiential learning. Students today are digital natives who respond better to interactive, personalized instruction. Teachers must be equipped to guide students through skill discovery, interest-based learning, and practical assessments. Upskilling bridges the gap between academic teaching and vocational skill delivery.
    </p>
  </div>
</section>
<!-- END: Intro Section -->

<!-- BEGIN: Objectives Section -->
<section class="bg-amber-50/50 py-16 md:py-24 relative overflow-hidden border-y border-amber-100/60">
  <div class="container max-w-7xl mx-auto px-4 md:px-8">
    <div class="flex flex-col lg:flex-row items-center gap-12">
      <!-- Text Content -->
      <div class="lg:w-1/2">
        <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-8">Objectives</h2>
        <ul class="space-y-6">
          <li class="relative pl-8 list-star text-slate-700">Strengthen teacher expertise across 33 NEP-aligned skill courses</li>
          <li class="relative pl-8 list-star text-slate-700">Promote competency-based and activity-oriented classroom practices</li>
          <li class="relative pl-8 list-star text-slate-700">Enhance digital literacy, innovation, and 21st-century teaching skills</li>
          <li class="relative pl-8 list-star text-slate-700">Enable practical implementation of skill education in day-to-day teaching</li>
          <li class="relative pl-8 list-star text-slate-700">Foster continuous professional growth and career advancement</li>
        </ul>
      </div>
      <!-- Images -->
      <div class="lg:w-1/2 relative w-full">
        <img alt="Teachers in Class" class="w-full h-auto rounded-2xl shadow-lg border border-white" src="{{ asset('designs/img/Upskill4Teacher-3.png') }}" />
      </div>
    </div>
  </div>
</section>
<!-- END: Objectives Section -->

<!-- BEGIN: LATAA OS Continuous Learning Section -->
<section class="py-20 md:py-28 bg-gradient-to-br from-slate-950 via-[#0a152e] to-slate-950 text-white relative overflow-hidden border-y border-cyan-500/20">
  <!-- Ambient background glow elements -->
  <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full bg-cyan-500/15 blur-3xl pointer-events-none"></div>
  <div class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full bg-blue-600/15 blur-3xl pointer-events-none"></div>

  <div class="container max-w-7xl mx-auto px-4 md:px-8 relative z-10">
    
    <!-- Section Header -->
    <div class="text-center max-w-3xl mx-auto mb-14">
      <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cyan-500/10 border border-cyan-400/30 text-cyan-400 text-xs font-bold uppercase tracking-widest shadow-sm mb-4">
        <i class="fa-solid fa-microchip"></i> AI-Powered Professional Development
      </div>
      <h2 class="text-3xl md:text-5xl font-black text-white tracking-tight mb-5 leading-tight">
        Your Continuous Learning Partner &ndash; <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-sky-300 to-blue-400">LATAA OS</span>
      </h2>
      <p class="text-slate-300 text-base md:text-lg leading-relaxed m-0">
        <strong>LATAA OS</strong> extends the U4T learning experience by providing AI-powered academic assistance and resources that support teachers in their continuous professional development.
      </p>
    </div>

    <!-- Infographic Sub-Badge -->
    <div class="text-center mb-10">
      <div class="inline-flex items-center gap-2.5 px-5 py-2 rounded-full bg-slate-900/80 border border-cyan-500/30 text-cyan-300 text-xs font-bold uppercase tracking-wider shadow-inner">
        <i class="fa-solid fa-wand-magic-sparkles text-cyan-400"></i> Five Core Intelligent Capabilities for Teachers
      </div>
    </div>

    <!-- 5-Node Capability Infographic Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
      
      <!-- Card 1 -->
      <div class="group relative p-7 rounded-2xl bg-slate-900/70 border border-white/10 hover:border-cyan-400/50 transition-all duration-300 shadow-xl hover:-translate-y-1.5 hover:shadow-cyan-500/10">
        <div class="flex items-center justify-between mb-5">
          <div class="w-12 h-12 rounded-xl bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 flex items-center justify-center text-xl shadow-xs group-hover:scale-110 group-hover:bg-cyan-500/20 transition-all">
            <i class="fa-solid fa-brain"></i>
          </div>
          <!-- <span class="text-xs font-extrabold uppercase px-2.5 py-1 rounded-md bg-cyan-500/10 text-cyan-300 border border-cyan-500/20">Node 01</span> -->
        </div>
        <h3 class="text-lg font-bold text-white mb-2 group-hover:text-cyan-300 transition-colors">Smart Teaching Resources</h3>
        <p class="text-slate-400 text-sm leading-relaxed m-0">Access AI-assisted teaching resources.</p>
      </div>

      <!-- Card 2 -->
      <div class="group relative p-7 rounded-2xl bg-slate-900/70 border border-white/10 hover:border-indigo-400/50 transition-all duration-300 shadow-xl hover:-translate-y-1.5 hover:shadow-indigo-500/10">
        <div class="flex items-center justify-between mb-5">
          <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/30 text-indigo-400 flex items-center justify-center text-xl shadow-xs group-hover:scale-110 group-hover:bg-indigo-500/20 transition-all">
            <i class="fa-solid fa-clipboard-list"></i>
          </div>
          <!-- <span class="text-xs font-extrabold uppercase px-2.5 py-1 rounded-md bg-indigo-500/10 text-indigo-300 border border-indigo-500/20">Node 02</span> -->
        </div>
        <h3 class="text-lg font-bold text-white mb-2 group-hover:text-indigo-300 transition-colors">Lesson Plans & Activities</h3>
        <p class="text-slate-400 text-sm leading-relaxed m-0">Prepare lesson plans and classroom activities.</p>
      </div>

      <!-- Card 3 -->
      <div class="group relative p-7 rounded-2xl bg-slate-900/70 border border-white/10 hover:border-emerald-400/50 transition-all duration-300 shadow-xl hover:-translate-y-1.5 hover:shadow-emerald-500/10">
        <div class="flex items-center justify-between mb-5">
          <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center justify-center text-xl shadow-xs group-hover:scale-110 group-hover:bg-emerald-500/20 transition-all">
            <i class="fa-solid fa-file-circle-check"></i>
          </div>
          <!-- <span class="text-xs font-extrabold uppercase px-2.5 py-1 rounded-md bg-emerald-500/10 text-emerald-300 border border-emerald-500/20">Node 03</span> -->
        </div>
        <h3 class="text-lg font-bold text-white mb-2 group-hover:text-emerald-300 transition-colors">Evaluations & Assessments</h3>
        <p class="text-slate-400 text-sm leading-relaxed m-0">Create worksheets, quizzes, and assessments.</p>
      </div>

    </div>

    <!-- Bottom Row (2 Balanced Cards Centered) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl mx-auto mb-14">
      
      <!-- Card 4 -->
      <div class="group relative p-7 rounded-2xl bg-slate-900/70 border border-white/10 hover:border-amber-400/50 transition-all duration-300 shadow-xl hover:-translate-y-1.5 hover:shadow-amber-500/10">
        <div class="flex items-center justify-between mb-5">
          <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-400 flex items-center justify-center text-xl shadow-xs group-hover:scale-110 group-hover:bg-amber-500/20 transition-all">
            <i class="fa-solid fa-compass"></i>
          </div>
          <!-- <span class="text-xs font-extrabold uppercase px-2.5 py-1 rounded-md bg-amber-500/10 text-amber-300 border border-amber-500/20">Node 04</span> -->
        </div>
        <h3 class="text-lg font-bold text-white mb-2 group-hover:text-amber-300 transition-colors">Pedagogy & Methodologies</h3>
        <p class="text-slate-400 text-sm leading-relaxed m-0">Explore different teaching approaches and methodologies.</p>
      </div>

      <!-- Card 5 -->
      <div class="group relative p-7 rounded-2xl bg-slate-900/70 border border-white/10 hover:border-purple-400/50 transition-all duration-300 shadow-xl hover:-translate-y-1.5 hover:shadow-purple-500/10">
        <div class="flex items-center justify-between mb-5">
          <div class="w-12 h-12 rounded-xl bg-purple-500/10 border border-purple-500/30 text-purple-400 flex items-center justify-center text-xl shadow-xs group-hover:scale-110 group-hover:bg-purple-500/20 transition-all">
            <i class="fa-solid fa-arrow-trend-up"></i>
          </div>
          <!-- <span class="text-xs font-extrabold uppercase px-2.5 py-1 rounded-md bg-purple-500/10 text-purple-300 border border-purple-500/20">Node 05</span> -->
        </div>
        <h3 class="text-lg font-bold text-white mb-2 group-hover:text-purple-300 transition-colors">Classroom Delivery & Mastery</h3>
        <p class="text-slate-400 text-sm leading-relaxed m-0">Strengthen subject knowledge and improve classroom delivery.</p>
      </div>

    </div>

    <!-- Visual 3-Stage Transformation Infographic Banner -->
    <div class="p-8 sm:p-10 rounded-3xl bg-gradient-to-r from-slate-900/90 via-[#0d1d3d] to-slate-900/90 border border-cyan-500/30 shadow-2xl relative">
      <div class="text-center mb-8">
        <span class="text-xs font-bold uppercase tracking-widest text-cyan-400">The Upskilling Transformation Loop</span>
        <h3 class="text-xl sm:text-2xl font-bold text-white mt-1">Bridging Workshop Theory into Everyday Teaching</h3>
      </div>

      <!-- 3 Flow Steps -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative">
        
        <!-- Step 1 -->
        <div class="flex items-center gap-4 p-4 rounded-2xl bg-white/5 border border-white/10 hover:border-blue-400/40 transition-colors">
          <div class="w-12 h-12 rounded-xl bg-blue-500/20 border border-blue-400/30 text-blue-400 flex items-center justify-center text-xl flex-shrink-0">
            <i class="fa-solid fa-chalkboard-user"></i>
          </div>
          <div>
            <!-- <div class="text-xs font-bold uppercase text-slate-400">Stage 01</div> -->
            <div class="text-white font-bold text-base">U4T Courses & Workshops</div>
            <p class="text-slate-300 text-xs mt-0.5 m-0 leading-normal">Structured skill learning & certification</p>
          </div>
        </div>

        <!-- Step 2: Core Intelligence -->
        <div class="flex items-center gap-4 p-4 rounded-2xl bg-gradient-to-r from-blue-600/20 to-cyan-500/20 border border-cyan-400/50 shadow-lg shadow-cyan-500/10 relative overflow-hidden">
          <span class="absolute top-2 right-2 px-2 py-0.5 rounded-full bg-cyan-400 text-slate-950 text-[9px] font-black uppercase tracking-wider">AI Layer</span>
          <div class="w-12 h-12 rounded-xl bg-cyan-500/30 border border-cyan-400/40 text-cyan-300 flex items-center justify-center text-xl flex-shrink-0">
            <i class="fa-solid fa-microchip"></i>
          </div>
          <div>
            <!-- <div class="text-xs font-bold uppercase text-cyan-400">Stage 02 &bull; Daily AI Hub</div> -->
            <div class="text-cyan-200 font-extrabold text-base">LATAA OS Assistance</div>
            <p class="text-slate-200 text-xs mt-0.5 m-0 leading-normal">Intelligent planning & classroom tools</p>
          </div>
        </div>

        <!-- Step 3 -->
        <div class="flex items-center gap-4 p-4 rounded-2xl bg-white/5 border border-white/10 hover:border-emerald-400/40 transition-colors">
          <div class="w-12 h-12 rounded-xl bg-emerald-500/20 border border-emerald-400/30 text-emerald-400 flex items-center justify-center text-xl flex-shrink-0">
            <i class="fa-solid fa-graduation-cap"></i>
          </div>
          <div>
            <!-- <div class="text-xs font-bold uppercase text-slate-400">Stage 03</div> -->
            <div class="text-white font-bold text-base">Everyday Impact</div>
            <p class="text-slate-300 text-xs mt-0.5 m-0 leading-normal">High-confidence, engaging teaching</p>
          </div>
        </div>

      </div>

      <!-- Narrative & Outcome Footnote -->
      <div class="mt-8 pt-6 border-t border-white/10 flex flex-col lg:flex-row items-center justify-between gap-6 text-center lg:text-left">
        <div class="space-y-1.5 max-w-3xl">
          <p class="text-sm sm:text-base text-slate-200 leading-relaxed m-0">
            Through <strong>LATAA OS</strong>, teacher upskilling goes beyond attending courses and workshops. Teachers gain access to intelligent academic assistance that supports their everyday teaching needs.
          </p>
          <div class="text-sm sm:text-base font-semibold text-cyan-300">
            <span class="text-white font-bold uppercase tracking-wider text-xs mr-1.5">The Outcome:</span>
            Continuous professional development supported by AI-driven teaching assistance.
          </div>
        </div>
        <a href="{{ route('lataa') }}" class="inline-flex items-center gap-2.5 px-7 py-3.5 rounded-full bg-gradient-to-r from-cyan-400 to-blue-500 hover:from-cyan-300 hover:to-blue-400 text-slate-950 font-extrabold text-sm transition-all duration-300 shadow-lg shadow-cyan-500/25 flex-shrink-0 hover:scale-105">
          <span>Explore LATAA OS</span>
          <i class="fa-solid fa-arrow-right text-xs"></i>
        </a>
      </div>

    </div>

  </div>
</section>
<!-- END: LATAA OS Continuous Learning Section -->

<!-- BEGIN: Benefits Section -->
<section class="py-16 md:py-24 relative overflow-hidden bg-white">
  <div class="container max-w-7xl mx-auto px-4 md:px-8 text-center mb-12">
    <h3 class="text-primary font-medium mb-2 text-sm uppercase tracking-wider">Empowering educators to teach smarter, not harder</h3>
    <h2 class="text-3xl md:text-4xl font-bold text-slate-900">Benefits for Teachers</h2>
  </div>
  <div class="container max-w-7xl mx-auto px-4 md:px-8 flex flex-col lg:flex-row gap-8 items-stretch">
    <div class="lg:w-1/2">
      <img alt="Teachers in Classroom" class="w-full h-full object-cover rounded-2xl shadow-md min-h-[300px]"
        src="{{ asset('designs/img/Upskill4Teacher-4.jpeg') }}" />
    </div>
    <div class="lg:w-1/2 bg-blue-50/70 rounded-2xl p-8 md:p-12 shadow-sm flex items-center border border-blue-100">
      <ul class="space-y-6">
        <li class="relative pl-8 list-burst text-slate-700">Gain expertise in 33 skill-focused courses spanning Communication, STEM, Life Skills, Arts, and Personality Development</li>
        <li class="relative pl-8 list-burst text-slate-700">Access to AI-supported lesson planning tools and resources</li>
        <li class="relative pl-8 list-burst text-slate-700">Certification recognized for professional growth and advancement</li>
        <li class="relative pl-8 list-burst text-slate-700">Opportunities for collaborative learning and peer engagement</li>
        <li class="relative pl-8 list-burst text-slate-700">Boost confidence and teaching effectiveness with hands-on strategies</li>
      </ul>
    </div>
  </div>
</section>
<!-- END: Benefits Section -->

<!-- BEGIN: Explore Courses Section -->
@include('frontend.home-four.components.course-carousel', [
  'type' => '8',
  'upskill' => '1',
  'title' => 'Explore Our Courses',
  'subtitle' => 'Because great teachers never stop learning. Discover 33+ NEP-aligned professional tracks.',
  'tagline' => 'CBSE Skill Education'
])
<!-- END: Explore Courses Section -->

<!-- BEGIN: CTA Section -->
<!-- <section class="py-20 bg-slate-50 border-t border-slate-100">
  <div class="max-w-4xl mx-auto px-4 text-center space-y-6">
    <h2 class="text-3xl md:text-4xl font-bold text-slate-900">Ready to Elevate Your Teaching Career?</h2>
    <p class="text-slate-600 max-w-2xl mx-auto text-base md:text-lg">
      Join thousands of educators mastering modern pedagogy, AI tools, and hands-on classroom methodologies.
    </p>
    <div class="pt-2">
      <a href="{{ route('courses') }}" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-full bg-primary hover:bg-primary-dark text-white font-semibold transition-all shadow-md">
        <span>Explore Courses</span>
        <i class="fa-solid fa-arrow-right text-xs"></i>
      </a>
    </div>
  </div>
</section> -->
<!-- END: CTA Section -->
@endsection
