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
<section class="py-20 relative">
  <div class="absolute left-0 top-20 w-48 h-48 border border-blue-200 rounded-r-full -translate-x-1/2"></div>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="text-center mb-16">
      <p class="text-blue-600 font-medium mb-2">Turning knowledge into powerful learning experience</p>
      <h2 class="text-3xl font-bold text-slate-900 mb-4">General Teacher Training Modules</h2>
      <p class="text-slate-600 max-w-2xl mx-auto">Our general training modules focus on the core competencies every teacher needs to succeed</p>
    </div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center relative">
      <div class="hidden md:block absolute top-12 left-24 right-24 h-0.5 bg-slate-200 -z-10"></div>
      
      <!-- Module 1 -->
      <div class="flex flex-col items-center text-center max-w-xs mx-auto mb-10 md:mb-0 bg-white z-10 px-4">
        <div class="w-24 h-24 rounded-full border-4 border-white shadow-sm overflow-hidden mb-6">
          <img src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=200&h=200&q=80"
               alt="Educational Psychology"
               class="w-full h-full object-cover" />
        </div>
        <h3 class="text-lg font-semibold text-blue-600 mb-2">Educational Psychology</h3>
        <p class="text-slate-600 text-sm">Understanding child behaviour, learning styles, motivation, and emotional needs</p>
      </div>

      <!-- Module 2 -->
      <div class="flex flex-col items-center text-center max-w-xs mx-auto mb-10 md:mb-0 bg-white z-10 px-4">
        <div class="w-24 h-24 rounded-full border-4 border-white shadow-sm overflow-hidden mb-6">
          <img src="https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&w=200&h=200&q=80"
               alt="Classroom Management"
               class="w-full h-full object-cover" />
        </div>
        <h3 class="text-lg font-semibold text-blue-600 mb-2">Classroom Management</h3>
        <p class="text-slate-600 text-sm">Techniques to create structured, inclusive, and disciplined learning environments</p>
      </div>

      <!-- Module 3 -->
      <div class="flex flex-col items-center text-center max-w-xs mx-auto mb-10 md:mb-0 bg-white z-10 px-4">
        <div class="w-24 h-24 rounded-full border-4 border-white shadow-sm overflow-hidden mb-6">
          <img src="https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=200&h=200&q=80"
               alt="Student Engagement Strategies"
               class="w-full h-full object-cover" />
        </div>
        <h3 class="text-lg font-semibold text-blue-600 mb-2">Student Engagement Strategies</h3>
        <p class="text-slate-600 text-sm">Interactive methods to encourage participation, curiosity, and critical thinking</p>
      </div>

      <!-- Module 4 -->
      <div class="flex flex-col items-center text-center max-w-xs mx-auto bg-white z-10 px-4">
        <div class="w-24 h-24 rounded-full border-4 border-white shadow-sm overflow-hidden mb-6">
          <img src="https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=200&h=200&q=80"
               alt="Assessment & Feedback Practices"
               class="w-full h-full object-cover" />
        </div>
        <h3 class="text-lg font-semibold text-blue-600 mb-2">Assessment &amp; Feedback Practices</h3>
        <p class="text-slate-600 text-sm">Designing meaningful assessments and constructive feedback for student growth</p>
      </div>

    </div>
  </div>
</section>
<!-- END: General Teacher Training Modules -->

<!-- BEGIN: Subject-Specific Training Modules -->
<section class="py-20 bg-slate-50 relative">
  <div class="absolute right-0 top-40 w-64 h-64 border border-blue-200 rounded-l-full translate-x-1/2"></div>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="text-center mb-16">
      <p class="text-blue-600 font-medium mb-2">From subject experts to master facilitator</p>
      <h2 class="text-3xl font-bold text-slate-900 mb-4">Subject-Specific Training Modules</h2>
      <p class="text-slate-600 max-w-2xl mx-auto">We offer specialised training to help teachers teach subjects conceptually and practically, rather than through rote methods:</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-blue-50/50 rounded-xl p-8 text-center border border-blue-100 hover:shadow-md transition">
        <h3 class="text-lg font-semibold text-blue-600 mb-6">Mathematics</h3>
        <div class="w-16 h-16 bg-white rounded-lg shadow-sm flex items-center justify-center mx-auto mb-6">
          <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
          </svg>
        </div>
        <p class="text-slate-600 text-sm">Teaching math using real-life applications, visual models, and logical reasoning</p>
      </div>

      <div class="bg-blue-50/50 rounded-xl p-8 text-center border border-blue-100 hover:shadow-md transition">
        <h3 class="text-lg font-semibold text-blue-600 mb-6">Science</h3>
        <div class="w-16 h-16 bg-white rounded-lg shadow-sm flex items-center justify-center mx-auto mb-6">
          <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
          </svg>
        </div>
        <p class="text-slate-600 text-sm">Concept explanation through experiments, diagrams, and everyday examples</p>
      </div>

      <div class="bg-blue-50/50 rounded-xl p-8 text-center border border-blue-100 hover:shadow-md transition">
        <h3 class="text-lg font-semibold text-blue-600 mb-6">Languages</h3>
        <div class="w-16 h-16 bg-white rounded-lg shadow-sm flex items-center justify-center mx-auto mb-6">
          <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
          </svg>
        </div>
        <p class="text-slate-600 text-sm">Languages improving comprehension, communication, vocabulary, and expression skills</p>
      </div>

      <div class="bg-blue-50/50 rounded-xl p-8 text-center border border-blue-100 hover:shadow-md transition">
        <h3 class="text-lg font-semibold text-blue-600 mb-6">Concept Visualisation</h3>
        <div class="w-16 h-16 bg-white rounded-lg shadow-sm flex items-center justify-center mx-auto mb-6">
          <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
          </svg>
        </div>
        <p class="text-slate-600 text-sm">Effective ways to explain diagrams, processes, and abstract ideas clearly</p>
      </div>
    </div>
  </div>
</section>
<!-- END: Subject-Specific Training Modules -->

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
  'title' => 'Explore Our Courses',
  'subtitle' => 'Our specialised modules focus on practical classroom delivery and competencies',
  'tagline' => 'Teacher Development Courses'
])
<!-- END: Course Carousel Section -->

<!-- BEGIN: Specialised Teacher Training Programmes -->
<section class="py-20 bg-slate-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-16">
      <p class="text-blue-600 font-medium mb-2">Shape trainers who shape futures</p>
      <h2 class="text-3xl font-bold text-slate-900 mb-4">Specialised Teacher Training Programmes</h2>
      <p class="text-slate-600 max-w-2xl mx-auto">Skillvation also offers structured certification-oriented programmes for early-years educators</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <div class="bg-orange-50/50 rounded-2xl overflow-hidden border border-orange-100">
        <img alt="Nursery Teacher Training" class="w-full h-48 object-cover"
          src="https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=600&q=80" />
        <div class="p-6">
          <h3 class="text-lg font-bold text-slate-900 mb-2">Nursery Teacher Training</h3>
          <p class="text-slate-600 text-sm">Foundational training for preschool educators in child development and early learning</p>
        </div>
      </div>

      <div class="bg-blue-50/50 rounded-2xl overflow-hidden border border-blue-100">
        <img alt="Montessori Teacher Training" class="w-full h-48 object-cover"
          src="https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=600&q=80" />
        <div class="p-6">
          <h3 class="text-lg font-bold text-slate-900 mb-2">Montessori Teacher Training</h3>
          <p class="text-slate-600 text-sm">Montessori-based pedagogy focusing on self-directed, activity-based learning</p>
        </div>
      </div>

      <div class="bg-orange-50/50 rounded-2xl overflow-hidden border border-orange-100">
        <img alt="Early Childhood Education" class="w-full h-48 object-cover"
          src="https://images.unsplash.com/photo-1581092795360-fd1ca04f0952?auto=format&fit=crop&w=600&q=80" />
        <div class="p-6">
          <h3 class="text-lg font-bold text-slate-900 mb-2">Early Childhood Education</h3>
          <p class="text-slate-600 text-sm">Holistic training in early-years teaching, classroom setup, and child engagement</p>
        </div>
      </div>
    </div>
  </div>
</section>
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
