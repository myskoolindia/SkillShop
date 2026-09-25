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
  'lms_only' => '1',
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
