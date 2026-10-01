@extends('frontend.home-four.layouts.master')

@section('meta_title', "Home — " . config('app.name', 'Skillvation'))
@section('meta_description', "The Global Skills Academy is dedicated to addressing labour skills gaps and empowering individuals for a future-ready workforce.")

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

<style>
  :root {
    --skillvation-blue: #0077d4;
    --skillvation-dark-blue: #0056b3;
    --skillvation-navy: #0b2545;
    --skillvation-bg-light: #f5f7fa;
    --skillvation-border: #e2e8f0;
    --skillvation-text-main: #1a202c;
    --skillvation-text-muted: #4a5568;
    --skillvation-card-stem: #0c4980;
    --skillvation-card-green: #5a7722;
    --skillvation-card-informal: #802330;
    --skillvation-card-gender: #9b6215;
  }

  .skillvation-page {
    color: var(--skillvation-text-main);
    background-color: #ffffff;
    font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 17px;
    line-height: 1.7;
  }

  .skillvation-page h1,
  .skillvation-page h2,
  .skillvation-page h3,
  .skillvation-page h4 {
    color: var(--skillvation-text-main);
    font-weight: 700;
    line-height: 1.25;
    margin-top: 0;
  }

  .skillvation-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px;
  }

  /* ── Hero Banner ─────────────────────────────────── */
  .skillvation-hero-banner {
    background: linear-gradient(rgba(11, 37, 69, 0.8), rgba(0, 86, 179, 0.75)), url("{{asset('frontend/img/banner/homeban01.jpeg')}}");
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    color: #ffffff;
    padding: 70px 0;
  }
  .skillvation-hero-banner h1 {
    color: #ffffff;
    font-size: clamp(34px, 4.5vw, 52px);
    margin-bottom: 12px;
    font-weight: 700;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.6);
  }
  .skillvation-hero-banner p {
    font-size: 20px;
    color: #f0f4f8;
    margin: 0;
    max-width: 800px;
    font-weight: 500;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.6);
  }

  /* ── Pill Buttons ─────────────────────────────────── */
  .skillvation-pill-btn {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    background-color: var(--skillvation-blue);
    color: #ffffff !important;
    padding: 12px 26px;
    border-radius: 9999px;
    font-weight: 700;
    font-size: 15px;
    text-decoration: none;
    transition: all 0.25s ease;
    box-shadow: 0 4px 12px rgba(0, 119, 212, 0.25);
    border: none;
    cursor: pointer;
  }
  .skillvation-pill-btn:hover {
    background-color: var(--skillvation-dark-blue);
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(0, 119, 212, 0.35);
  }
  .skillvation-pill-btn i {
    font-size: 13px;
    transition: transform 0.2s ease;
  }
  .skillvation-pill-btn:hover i {
    transform: translateX(3px);
  }

  /* ── Sections Layout ─────────────────────────────── */
  .skillvation-section {
    padding: 65px 0;
    border-bottom: 1px solid #edf2f7;
  }
  .skillvation-section.no-border {
    border-bottom: none;
  }
  .skillvation-section.bg-light {
    background-color: var(--skillvation-bg-light);
  }

  .skillvation-grid-2col {
    display: grid;
    grid-template-columns: 1.15fr 0.85fr;
    gap: 48px;
    align-items: center;
  }
  .skillvation-grid-2col.equal {
    grid-template-columns: 1fr 1fr;
  }

  /* ── Video / Media Cards ─────────────────────────── */
  .skillvation-media-card {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    background: #000;
  }
  .skillvation-media-card img {
    width: 100%;
    height: 320px;
    object-fit: cover;
    display: block;
    transition: transform 0.4s ease;
  }
  .skillvation-media-card:hover img {
    transform: scale(1.03);
  }
  .skillvation-media-caption {
    font-size: 12px;
    color: #718096;
    margin-top: 8px;
    text-align: right;
  }
  .skillvation-play-btn {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 68px;
    height: 68px;
    background-color: var(--skillvation-blue);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 24px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
    transition: all 0.3s ease;
    text-decoration: none;
  }
  .skillvation-media-card:hover .skillvation-play-btn {
    transform: translate(-50%, -50%) scale(1.1);
    background-color: #ffffff;
    color: var(--skillvation-blue);
  }

  /* ── 4 Color Stat Cards ──────────────────────────── */
  .skillvation-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin: 40px 0;
  }
  .skillvation-stat-card {
    padding: 32px 24px;
    border-radius: 4px;
    color: #ffffff;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    min-height: 220px;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
  }
  .skillvation-stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.2);
    color: #ffffff !important;
  }
  .skillvation-stat-card .stat-title {
    font-size: 26px;
    font-weight: 800;
    line-height: 1.15;
    margin-bottom: 12px;
    color: #ffffff;
  }
  .skillvation-stat-card .stat-desc {
    font-size: 15px;
    line-height: 1.5;
    color: rgba(255, 255, 255, 0.92);
  }

  .stat-card-stem { background-color: var(--skillvation-card-stem); }
  .stat-card-green { background-color: var(--skillvation-card-green); }
  .stat-card-informal { background-color: var(--skillvation-card-informal); }
  .stat-card-gender { background-color: var(--skillvation-card-gender); }

  /* ── Split Training Banner ───────────────────────── */
  .skillvation-split-banner {
    display: grid;
    grid-template-columns: 1fr 1fr;
    background: #0077d4;
    color: #ffffff;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 50px;
  }
  .skillvation-split-banner-left {
    padding: 50px 45px;
    /* display: flex;
    flex-direction: column;
    justify-content: center; */
    border: 2px solid rgba(255, 255, 255, 0.3);
    margin: 20px;
    border-radius: 2px;
  }
  .skillvation-split-banner-left h2 {
    color: #ffffff;
    font-size: 36px;
    margin-bottom: 16px;
  }
  .skillvation-split-banner-left p {
    font-size: 18px;
    color: #e2e8f0;
    margin: 0;
  }
  .skillvation-split-banner-right img {
    width: 100%;
    height: 535px;
    /* min-height: 320px; */
    object-fit: cover;
  }

  /* ── Partner Lists ───────────────────────────────── */
  .skillvation-partner-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 18px;
    padding: 0;
    list-style: none;
  }
  .skillvation-partner-pills li a {
    display: inline-block;
    padding: 6px 14px;
    background: #e8f3fc;
    color: var(--skillvation-blue);
    font-weight: 600;
    font-size: 14px;
    border-radius: 4px;
    text-decoration: none;
    transition: all 0.2s ease;
  }
  .skillvation-partner-pills li a:hover {
    background: var(--skillvation-blue);
    color: #ffffff;
  }
  .skillvation-partner-pills li span {
    display: inline-block;
    padding: 6px 14px;
    background: #edf2f7;
    color: var(--skillvation-text-muted);
    font-weight: 600;
    font-size: 14px;
    border-radius: 4px;
  }

  /* ── Quotes ──────────────────────────────────────── */
  .skillvation-quote-box {
    margin: 40px 0;
    padding: 30px 36px;
    background: #f7fafc;
    border-left: 5px solid var(--skillvation-blue);
    border-radius: 0 8px 8px 0;
  }
  .skillvation-quote-box p {
    font-size: 18px;
    font-style: italic;
    color: #2d3748;
    margin: 0 0 12px;
    line-height: 1.65;
  }
  .skillvation-quote-box cite {
    font-size: 14px;
    font-weight: 700;
    color: var(--skillvation-blue);
    font-style: normal;
  }

  /* ── GSA Mission in Figures Cards ────────────────── */
  .skillvation-figures-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
    margin-top: 32px;
  }
  .skillvation-figure-card {
    background-color: #f1f4f6;
    padding: 36px 28px;
    border-radius: 8px;
    min-height: 380px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    border: none;
    box-shadow: none;
    text-align: left;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
  }
  .skillvation-figure-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
  }
  .skillvation-figure-circle {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    overflow: hidden;
    margin-bottom: 28px;
    flex-shrink: 0;
  }
  .skillvation-figure-circle img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }
  .skillvation-figure-number {
    font-size: 44px;
    font-weight: 700;
    color: #212121;
    line-height: 1.1;
    margin-bottom: 8px;
  }
  .skillvation-figure-label {
    font-size: 18px;
    font-weight: 600;
    color: #212121;
    line-height: 1.3;
    text-transform: none;
    letter-spacing: normal;
  }
  .skillvation-figure-subtext {
    font-size: 16px;
    font-weight: 400;
    color: #212121;
    margin-top: 6px;
    line-height: 1.4;
  }

  /* ── Regional Statistics ─────────────────────────── */
  .skillvation-regional-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
    margin-top: 36px;
  }
  .skillvation-regional-card {
    background: #ffffff;
    border: 1px solid var(--skillvation-border);
    border-top: 4px solid var(--skillvation-blue);
    padding: 28px;
    border-radius: 4px;
    text-decoration: none;
    color: inherit;
    transition: all 0.25s ease;
  }
  .skillvation-regional-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
    color: inherit;
  }
  .skillvation-regional-card h3 {
    font-size: 20px;
    color: var(--skillvation-blue);
    margin-bottom: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .skillvation-regional-card p {
    font-size: 15px;
    color: var(--skillvation-text-muted);
    margin: 0;
  }

  /* ── News Cards ──────────────────────────────────── */
  .skillvation-news-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
    margin-top: 40px;
  }
  .skillvation-news-card {
    background: #ffffff;
    border: 1px solid var(--skillvation-border);
    border-radius: 8px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    text-decoration: none;
    color: inherit;
    transition: all 0.25s ease;
  }
  .skillvation-news-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.1);
    color: inherit;
  }
  .skillvation-news-content {
    padding: 22px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    justify-content: space-between;
  }
  .skillvation-news-tag {
    color: var(--skillvation-blue);
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 10px;
  }
  .skillvation-news-title {
    font-size: 16px;
    font-weight: 700;
    line-height: 1.45;
    color: var(--skillvation-text-main);
    margin: 0 0 16px;
  }
  .skillvation-news-date {
    font-size: 13px;
    color: #a0aec0;
    font-weight: 500;
  }

  /* ── Global Coalition Footer Block ────────────────── */
  .skillvation-coalition-block {
    background: var(--skillvation-navy);
    color: #ffffff;
    padding: 60px 0;
  }
  .skillvation-coalition-block h2 {
    color: #ffffff;
    font-size: 34px;
    margin-bottom: 16px;
  }
  .skillvation-coalition-block p {
    color: #cbd5e0;
    font-size: 17px;
    max-width: 600px;
  }
  .skillvation-social-links {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    margin-top: 24px;
  }
  .skillvation-social-links a {
    color: #ffffff;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    padding: 6px 14px;
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 4px;
    transition: all 0.2s ease;
  }
  .skillvation-social-links a:hover {
    background-color: var(--skillvation-blue);
    border-color: var(--skillvation-blue);
    color: #ffffff;
  }

  /* ── Responsive Queries ──────────────────────────── */
  @media (max-width: 992px) {
    .skillvation-grid-2col,
    .skillvation-grid-2col.equal {
      grid-template-columns: 1fr;
      gap: 36px;
    }
    .skillvation-stats-grid {
      grid-template-columns: repeat(2, 1fr);
    }
    .skillvation-split-banner {
      grid-template-columns: 1fr;
    }
    .skillvation-figures-grid {
      grid-template-columns: repeat(2, 1fr);
    }
    .skillvation-regional-grid {
      grid-template-columns: 1fr;
    }
    .skillvation-news-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  @media (max-width: 600px) {
    .skillvation-stats-grid,
    .skillvation-figures-grid,
    .skillvation-news-grid {
      grid-template-columns: 1fr;
    }
    .skillvation-split-banner-left {
      padding: 30px 20px;
      margin: 10px;
    }
  }

  /* ═══════════════════════════════════════════════════
     SECTION BACKGROUND COLORS
  ═══════════════════════════════════════════════════ */

  /* Section 2 — Intro / Video: clean white with subtle blue-left accent */
  .sv-section-intro {
    background: #ffffff;
    border-left: 5px solid #0077d4;
  }

  /* Section 3 — Skills for the Future: cool light slate */
  .sv-section-skills {
    background: linear-gradient(160deg, #eef4fb 0%, #f0f4ff 100%);
  }

  /* Section 4 — AI EmpowerED: warm amber/cream */
  .sv-section-ai {
    background: linear-gradient(160deg, #fffbf0 0%, #fff3e0 100%);
    border-bottom: 1px solid #ffe0b2;
  }

  /* Section 5 — Upskilling Opportunities: soft teal/green */
  .sv-section-upskill {
    background: linear-gradient(160deg, #f0faf4 0%, #e8f5e9 100%);
  }

  /* Section 6 — Working Model: soft lavender/purple */
  .sv-section-model {
    background: linear-gradient(160deg, #f5f3ff 0%, #ede9fe 100%);
  }

  /* Section 9 — Leaders of Learning CTA: deep indigo */
  .sv-section-cta {
    background: linear-gradient(135deg, #1e3a5f 0%, #0b2545 60%, #0d1b2a 100%);
    color: #ffffff;
  }
  .sv-section-cta h2,
  .sv-section-cta p {
    color: #ffffff !important;
  }
  .sv-section-cta .text-gray-700 {
    color: rgba(255, 255, 255, 0.85) !important;
  }

  /* ═══════════════════════════════════════════════════
     SCROLL-REVEAL ANIMATION ENGINE
     Sections start invisible; IntersectionObserver
     adds .sv-revealed when they enter the viewport,
     triggering the matching keyframe.
  ═══════════════════════════════════════════════════ */

  /* --- base hidden state --- */
  [data-reveal] {
    opacity: 0;
    will-change: opacity, transform;
  }

  /* --- revealed state (added by JS) --- */
  [data-reveal].sv-revealed {
    animation-fill-mode: both;
    animation-timing-function: cubic-bezier(0.22, 1, 0.36, 1);
    animation-duration: 0.85s;
  }

  /* --- per-effect keyframes & trigger classes --- */

  /* fade-up: sections 2, 9 */
  @keyframes sv-fadeUp {
    from { opacity: 0; transform: translateY(52px); }
    to   { opacity: 1; transform: translateY(0); }
  }
  [data-reveal="fade-up"].sv-revealed {
    animation-name: sv-fadeUp;
  }

  /* slide-right (enters from left): section 3 */
  @keyframes sv-slideRight {
    from { opacity: 0; transform: translateX(-60px); }
    to   { opacity: 1; transform: translateX(0); }
  }
  [data-reveal="slide-right"].sv-revealed {
    animation-name: sv-slideRight;
  }

  /* slide-left (enters from right): section 4, 6 */
  @keyframes sv-slideLeft {
    from { opacity: 0; transform: translateX(60px); }
    to   { opacity: 1; transform: translateX(0); }
  }
  [data-reveal="slide-left"].sv-revealed {
    animation-name: sv-slideLeft;
  }

  /* zoom-in: section 5 */
  @keyframes sv-zoomIn {
    from { opacity: 0; transform: scale(0.92); }
    to   { opacity: 1; transform: scale(1); }
  }
  [data-reveal="zoom-in"].sv-revealed {
    animation-name: sv-zoomIn;
  }

  /* flip-up: hero title (optional subtle entrance) */
  @keyframes sv-flipUp {
    from { opacity: 0; transform: perspective(600px) rotateX(18deg) translateY(30px); }
    to   { opacity: 1; transform: perspective(600px) rotateX(0deg) translateY(0); }
  }
  [data-reveal="flip-up"].sv-revealed {
    animation-name: sv-flipUp;
  }

  /* --- inner child stagger (cards, track divs) --- */
  [data-reveal].sv-revealed [data-stagger] {
    opacity: 0;
    animation-fill-mode: both;
    animation-timing-function: cubic-bezier(0.22, 1, 0.36, 1);
    animation-duration: 0.65s;
    animation-name: sv-fadeUp;
  }
  [data-reveal].sv-revealed [data-stagger="1"] { animation-delay: 0.10s; }
  [data-reveal].sv-revealed [data-stagger="2"] { animation-delay: 0.22s; }
  [data-reveal].sv-revealed [data-stagger="3"] { animation-delay: 0.34s; }
  [data-reveal].sv-revealed [data-stagger="4"] { animation-delay: 0.46s; }

  /* hero text animation on page load */
  .sv-hero-text {
    animation: sv-flipUp 1s cubic-bezier(0.22, 1, 0.36, 1) both;
    animation-delay: 0.15s;
  }

  /* reduce motion for accessibility */
  @media (prefers-reduced-motion: reduce) {
    [data-reveal],
    [data-reveal].sv-revealed,
    [data-reveal].sv-revealed [data-stagger],
    .sv-hero-text {
      animation: none !important;
      opacity: 1 !important;
      transform: none !important;
    }
  }
</style>
@endpush

@section('contents')
<div class="skillvation-page">

  <!-- 1. Hero Title Banner -->
  <section class="skillvation-hero-banner">
    <div class="skillvation-container">
      <div class="sv-hero-text">
        <h1>India's Favourite Skill Platform</h1>
        <p>Empowering students and teachers for the future of education</p>
      </div>
    </div>
  </section>

  <!-- 2. Intro Section with Video Feature -->
  <section class="skillvation-section sv-section-intro" data-reveal="fade-up">
    <div class="skillvation-container">
      <div class="skillvation-grid-2col">
        <div>
          <h2 class="mb-4">
            Empowering Schools for the Future of Education
          </h2>

          <p>
            Education is evolving from knowledge acquisition to the development of
            skills, competencies and real-world capabilities. As schools increasingly
            embrace experiential and competency-based learning, there is a growing need
            for structured skill education that complements the academic curriculum.
          </p>

          <p>
            Skillvation partners with schools to make this transition meaningful,
            structured and sustainable.
          </p>

          <p>
            We work alongside schools to create a comprehensive skill education
            ecosystem that enables students to explore their interests, develop
            practical competencies and connect classroom learning with real-world
            applications.
          </p>

          <p>
            Our approach goes beyond introducing individual activities or programmes.
            We work as an integral skill education partner, supporting schools with the
            expertise, programmes, resources and implementation framework required to
            embed skill development into the school’s learning environment.
          </p>

          
        </div>
        
        <div>
          <div class="skillvation-media-card" style="height: auto !important; aspect-ratio: 16/9; overflow: hidden; border-radius: 8px;">
            <iframe
              src="https://www.youtube.com/embed/LGab-8Rf1jQ"
              title="Skillvation Education Video"
              style="width: 100%; height: 100%; min-height: 280px; border: none; display: block;"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen>
            </iframe>
          </div>
        </div>
      </div>

      <div class="mt-8">
      </div>
    </div>
  </section>

  <!-- 3. Skills for the Future Global Platform -->
  <section class="skillvation-section bg-light sv-section-skills" data-reveal="slide-right">
    <div class="skillvation-container">
      <div class="skillvation-grid-2col">
        <div>
          <h2 class="text-3xl font-bold mb-4">Skills for the Future</h2>
          <!-- <p>
            The <a href="https://www.unesco.org/en/global-education-coalition/skills-academy/skills-future?hub=182955" target="_blank" rel="noopener" class="text-blue-600 underline font-semibold">Skills for the Future platform</a> is an open-access global hub convened by UNESCO’s Global Skills Academy, in collaboration with KPMG International. The platform empowers businesses, civil society, and youth to scale up impact, foster inclusive partnerships, and accelerate progress toward SDG 4 on Quality Education. By connecting initiatives and amplifying collective action, it aims to build a more inclusive, resilient, and future-ready generation.
          </p> -->
          <p>Skill 2 Skool supports schools in identifying and nurturing student strengths through structured observation, reflection, and skill mapping. By focusing on aptitude, interest, and behaviour patterns, schools gain deeper insights into student potential—supporting informed guidance, confidence building, and holistic development.</p>
          <p class="font-bold text-gray-900 mt-4 mb-6">
            Become part of a global movement to equip young students for the future!
          </p>
          <!-- <a href="/skill2school" target="_blank" rel="noopener" class="skillvation-pill-btn">
            <span>Explore existing skills initiatives</span>
            <i class="fa-solid fa-arrow-right"></i>
          </a> -->
        </div>

        <div>
          <div class="skillvation-media-card" style="height: auto !important; background: #ffffff; border: 1px solid var(--skillvation-border); padding: 10px;">
            <img src="{{ asset('/frontend/img/skillbox/homeimg01.jpeg')}}" alt="Skills for the Future" style="width: 100%; height: auto; object-fit: contain;">
          </div>
          <!-- <div class="skillvation-media-caption">© UNESCO</div> -->
        </div>
      </div>

      <!-- 4 Colored Stat Cards -->
      <div class="skillvation-stats-grid">
        <a href="#" target="_blank" rel="noopener" class="skillvation-stat-card stat-card-stem" data-stagger="1">
          <div class="stat-title">The Skill Gap</div>
          <div class="stat-desc">Independent findings from the companion India Skills Report 2026 indicate that while national youth employability has marginally risen to 56.35%, nearly 43.65% of Indian graduates still lack the necessary skills to be hired immediately by industry standards.</div>
        </a>

        <a href="#" target="_blank" rel="noopener" class="skillvation-stat-card stat-card-green" data-stagger="2">
          <div class="stat-title">The NEET Cohort</div>
          <div class="stat-desc">According to the NITI Aayog framework using NSSO baselines, 8.9 crore (89 million) young Indians between the ages of 15 and 29 fall under the category of NEET (Not in Education, Employment, or Training).</div>
        </a>

        <a href="#" target="_blank" rel="noopener" class="skillvation-stat-card stat-card-informal" data-stagger="3">
          <div class="stat-title">Graduate Unemployment</div>
          <div class="stat-desc">The transition from university to the corporate sector remains severely strained. Roughly 40% of young graduates under the age of 25 are unemployed. Out of 6.3 crore graduates in the 20–29 age bracket, 1.1 crore remain jobless due to skill mismatches.</div>
        </a>

        <a href="#" target="_blank" rel="noopener" class="skillvation-stat-card stat-card-gender" data-stagger="4">
          <div class="stat-title">Gender gap</div>
          <div class="stat-desc">In digital access and divide is the biggest obstacle for development for skills for the future</div>
        </a>
      </div>

      <div class="space-y-4 text-gray-700">
        <!-- <p>
          Globally, one out of five individuals aged 15-34 remain disengaged from education, employment, or training (<a href="https://www.ilo.org/global/research/global-reports/weso/WCMS_865332/lang--en/index.htm" target="_blank" rel="noopener" class="text-blue-600 underline font-medium">International Labour Organization <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i></a>). That translates to 360 million young individuals seeking opportunities to build a brighter future through quality education, training and employment opportunities.
        </p>
        <p>
          The rapid pace of technological, economic, and societal transformations compounds this issue. Recent reports from the World Economic Forum indicate that 43% of business tasks are expected to be automated by 2027. This suggests the need for widespread reskilling (the process of acquiring new skills or knowledge to perform a different job or task) or upskilling initiatives to ensure employees globally can navigate the changing demands of the labour market (<a href="https://www.weforum.org/publications/the-future-of-jobs-report-2025/" target="_blank" rel="noopener" class="text-blue-600 underline font-medium">World’s Economic Forum “ Futures of Jobs” Report <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i></a>).
        </p> -->
        <p>The data surrounding Indian students and their lack of employable skills comes from the National Sample Survey Office (NSSO) data utilized in the comprehensive report titled Reimagining Skilling for Viksit Bharat@2047 released by NITI Aayog. This study builds directly on foundational NSSO survey metrics regarding youth training and employment to outline India's current skill deficit. 
          </p>
          <p>The core issue highlighted is that India is facing a crisis of "job-readiness" rather than just a crisis of "job availability." </p>(<a href="https://www.academicmantraservices.com/blog/why-57-of-indian-graduates-cant-get-hired-despite-23-crore-vacancies-2026-analysis" target="_blank" rel="noopener" class="text-blue-600 underline font-medium">International Labour Organization <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i></a>).<a href=""></a>
        </p>
      </div>
    </div>
  </section>

  <!-- 4. AI EmpowerED Section -->
  <section class="skillvation-section sv-section-ai" data-reveal="slide-left">
    <div class="skillvation-container">
      <div class="skillvation-grid-2col">
        <div>
          <h3 style="color:#1e1b4b; font-size:clamp(20px,2.5vw,28px); font-weight:800; margin-bottom:24px; line-height:1.3;">
            What Skillvation Brings to Your School
          </h3>

          <style>
            .sv-brings-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
            .sv-brings-item { display:flex; align-items:flex-start; gap:12px; padding:14px 16px; background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 4px rgba(0,0,0,.04); transition:box-shadow .25s ease, transform .25s ease; }
            .sv-brings-item:hover { box-shadow:0 6px 22px rgba(0,0,0,.09); transform:translateY(-2px); }
            .sv-brings-icon { width:38px; height:38px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:2px; }
            .sv-brings-item strong { font-size:16px; font-weight:700; color:#1e1b4b; display:block; margin-bottom:3px; line-height:1.3; }
            .sv-brings-item span { font-size:12.5px; color:#64748b; line-height:1.5; }
            @media(max-width:600px){ .sv-brings-grid { grid-template-columns:1fr; } }
          </style>

          <div class="sv-brings-grid">

            <div class="sv-brings-item">
              <div class="sv-brings-icon" style="background:#eef2ff;">
                <i class="fa-solid fa-layer-group" style="font-size:15px;color:#4f46e5;"></i>
              </div>
              <div>
                <strong>Structured Skill Education Programmes</strong>
                <span>Aligned with the school’s learning objectives</span>
              </div>
            </div>

            <div class="sv-brings-item">
              <div class="sv-brings-icon" style="background:#e0f2fe;">
                <i class="fa-solid fa-flask" style="font-size:15px;color:#0369a1;"></i>
              </div>
              <div>
                <strong>Experiential &amp; Hands-On Learning</strong>
                <span>Encourages students to learn by doing</span>
              </div>
            </div>

            <div class="sv-brings-item">
              <div class="sv-brings-icon" style="background:#fffbeb;">
                <i class="fa-solid fa-lightbulb" style="font-size:15px;color:#b45309;"></i>
              </div>
              <div>
                <strong>Future-Ready Skills</strong>
                <span>Creativity, problem-solving, critical thinking, communication &amp; collaboration</span>
              </div>
            </div>

            <div class="sv-brings-item">
              <div class="sv-brings-icon" style="background:#ecfdf5;">
                <i class="fa-solid fa-link" style="font-size:15px;color:#047857;"></i>
              </div>
              <div>
                <strong>Real-World Connections</strong>
                <span>Connects academic concepts with practical applications</span>
              </div>
            </div>

            <div class="sv-brings-item">
              <div class="sv-brings-icon" style="background:#fdf2f8;">
                <i class="fa-solid fa-arrow-trend-up" style="font-size:15px;color:#be185d;"></i>
              </div>
              <div>
                <strong>Age-Appropriate Skill Pathways</strong>
                <span>Students progressively build competencies at every stage</span>
              </div>
            </div>

            <div class="sv-brings-item">
              <div class="sv-brings-icon" style="background:#f3e8ff;">
                <i class="fa-solid fa-person-chalkboard" style="font-size:15px;color:#6d28d9;"></i>
              </div>
              <div>
                <strong>Teacher Support &amp; Guidance</strong>
                <span>Implementation support to embed skill education effectively</span>
              </div>
            </div>

            <div class="sv-brings-item">
              <div class="sv-brings-icon" style="background:#ecfeff;">
                <i class="fa-solid fa-compass" style="font-size:15px;color:#0891b2;"></i>
              </div>
              <div>
                <strong>Engaging Learning Experiences</strong>
                <span>Encourage curiosity, exploration and independent thinking</span>
              </div>
            </div>

            <div class="sv-brings-item">
              <div class="sv-brings-icon" style="background:#f0fdf4;">
                <i class="fa-solid fa-arrows-up-to-line" style="font-size:15px;color:#15803d;"></i>
              </div>
              <div>
                <strong>Scalable Framework</strong>
                <span>Evolves with the school’s requirements and student needs</span>
              </div>
            </div>

          </div>
        </div>

        <div>
          <div class="skillvation-media-card">
            <img src="{{ asset('/frontend/img/skillbox/ai_empowered.jpg') }}" alt="AI EmpowerED Learning Session">
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- 5. Our Training Opportunities (Split Banner + 4 Tracks) -->
  <!-- <section class="skillvation-section bg-light sv-section-upskill" id="training-opportunities" data-reveal="zoom-in">
    <div class="skillvation-container"> -->
      
      <!-- Split Blue Hero Block -->
      <!-- <div class="skillvation-split-banner">
        <div class="skillvation-split-banner-left">
          <h2>Our Upskilling opportunities</h2>

          <p>Building the capability to deliver experiential and skill-based education Skillvation's Upskilling for Teachers program is designed to equip educators with the practical knowledge, tools and facilitation skills required to implement experiential Skill Education effectively. Our program enables them to extend their existing subject expertise into practical, interdisciplinary and work-oriented learning experiences.</p>
        </div>
        <div class="skillvation-split-banner-right">
          <img src="{{ asset('/frontend/img/skillbox/teacher_upskilling.jpg') }}" alt="Our Upskilling Opportunities - Teacher Training">
        </div>
      </div> -->

      <!-- Track 1: Digital Skills -->
      <!-- <div class="bg-white p-8 rounded-lg shadow-sm border border-gray-200 mb-8" data-stagger="1">
        <div class="skillvation-grid-2col">
          <div>
            <h2 class="text-2xl font-bold mb-3 text-blue-900">Work on Life Forms</h2>
            
            <p class="text-gray-700 mb-4">
            Connecting academic knowledge with life, nature and living systems
            This training area focuses on developing teachers ability to facilitate practical learning around living systems
            and life-related activities.
            Teachers are introduced to concepts and practical approaches related to food, plants, health, nutrition, nature,
            agriculture, sustainability and everyday life. The emphasis is on converting theoretical concepts into
            meaningful activities, investigations, demonstrations and projects.
            Science and Biology teachers can strengthen their ability to design and facilitate hands-on experiences
            involving living systems, food science, environmental practices and health-related applications.
              </p>
              <p class="font-semibold text-gray-800 mb-2">
            Outcome: Teachers gain the confidence to transform life-science concepts into practical, contextual and
            experiential learning experiences.
            </p>
            
          </div>
          <div>
            <div class="skillvation-media-card">
              <img src="{{ asset('/frontend/img/skillbox/Workonlifeforms.jpeg') }}" alt="Work on Life Forms">
            </div>
            
          </div>
        </div>
      </div> -->

      <!-- Track 2: Green Skills -->
      <!-- <div class="bg-white p-8 rounded-lg shadow-sm border border-gray-200 mb-8" data-stagger="2">
        <div class="skillvation-grid-2col">
          <div>
            <h2 class="text-2xl font-bold mb-3 text-green-900">Work on Materials & Machines</h2>
            
            <p class="text-gray-700 mb-4">
            Building capability in making, designing, technology and innovation
            This area focuses on developing teachers&#39; practical understanding of materials, tools, machines, technology
            and the processes involved in designing and creating products.
            Teachers are introduced to making-oriented experiences such as STEM, electronics, coding, digital
            technologies, handicrafts, design, fabrication and problem-solving.
            The training can be particularly relevant for Physics, Computer Science, Mathematics, Art, Design and
            Technology teachers, while also enabling teachers from other disciplines to participate in interdisciplinary
            making and innovation activities.
            <p>
            <p class="font-semibold text-gray-800 mb-2">
            Outcome: Teachers develop the ability to guide students from idea → design → making → testing →
            improvement, creating a stronger culture of innovation and practical problem-solving.
            </p>
            
          </div>
          <div>
            <div class="skillvation-media-card">
              <img src="{{ asset('/frontend/img/skillbox/Workonmaterialsandmachines.jpeg') }}" alt="Work on Materials and Machines">
            </div>
          </div>
        </div>
      </div> -->

      <!-- Track 3: Entrepreneurial Skills -->
      <!-- <div class="bg-white p-8 rounded-lg shadow-sm border border-gray-200 mb-8" data-stagger="3">
        <div class="skillvation-grid-2col">
          <div>
            <h2 class="text-2xl font-bold mb-3 text-red-900">Work on Human Services</h2>
            
            <p class="text-gray-700 mb-4">
            Developing capability in people, services and real-world applications
            This area focuses on the human, social and service dimensions of work. Teachers learn how to facilitate
            activities that help connect classroom learning with real-world situations involving people, communities,
            organisations and services.
            The training can cover areas such as communication, financial literacy, tourism, entrepreneurship,
            leadership, community engagement and service-oriented activities.
            </p>
            <p class="font-semibold text-gray-800 mb-2">
            Outcome: Teachers become better equipped to facilitate real-world, people-centred learning and help students understand how
            knowledge translates into services, careers and community impact.
            </p>
            
          </div>
          <div>
            <div class="skillvation-media-card">
              <img src="{{ asset('/frontend/img/skillbox/workonhumanservices.jpeg') }}" alt="Work on Human Services">
            </div>
            
          </div>
        </div>
      </div>

    </div>
  </section> -->

  <!-- 6. Our Working Model -->
  <!-- <section class="skillvation-section sv-section-model" data-reveal="slide-left">
    <div class="skillvation-container">
      <div class="skillvation-grid-2col">
        <div>
          <h2 class="text-3xl font-bold mb-4">Our working model</h2>
          <p class="text-gray-700 mb-4">
            Partnerships sit at the heart of Skillvation's success. We leverage om multi-stakeholder partnerships approach and mobilizes multiple Art, Technical and Vocational Eucation.
          </p>
          <p class="font-semibold text-gray-800 mb-2">Skillvation connects:</p>
          <ul class="list-disc pl-6 space-y-2 text-gray-700 mb-4">
            <li>School</li>
            <li>Teacher</li>
            <li>Students</li>
          </ul>
          <p class="text-gray-700">
            A wide range of training programs and projects offered. This expansive knowledge enables learners to explore various courses, ensuring that no one is left behind in accessing quality education.
          </p>
        </div>

        <div>
          <div class="skillvation-media-card bg-white p-4 border border-gray-200">
            <img src="{{ asset('/frontend/img/skillbox/our_working_model.jpg') }}" alt="Our Working Model" style="object-fit: contain;">
          </div>
        </div>
      </div>
    </div>
  </section> -->

  <!-- 7. GSA Mission in Figures -->
  

  <!-- 8. Regional Statistics -->
  

  <!-- 9. Ready to make a positive impact? CTA -->
  <!-- <section class="skillvation-section bg-light sv-section-cta" data-reveal="fade-up">
    <div class="skillvation-container">
      <div class="skillvation-grid-2col">
        <div>
          <h2 class="text-3xl font-bold mb-4">Leaders of Learning</h2>
          <p class="text-gray-700 mb-6">
            Experience a hassle-free onboarding process designed for your comfort. From key handovers to utility setups, we take care of everything so you can settle into your new home with ease.
          </p>
          <a href="/ttt" class="skillvation-pill-btn">
            <span>Know more</span>
            <i class="fa-solid fa-arrow-up-right-from-square"></i>
          </a>
        </div>

        <div>
          <div class="skillvation-media-card">
            <img src="{{ asset('/frontend/img/skillbox/leaders_of_learning.jpg') }}" alt="Leaders of Learning - School with Children">
          </div>
        </div>
      </div>
    </div>
  </section> -->

  <!-- 10. Building Future-Ready Schools — Ecosystem Overview -->
  <section class="skillvation-section" id="sv-ecosystem" style="background:#f8faff; padding-top:80px; padding-bottom:80px; overflow:hidden;">

    <style>
      .sv-eco-eyebrow{display:inline-flex;align-items:center;gap:8px;font-size:12px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:#6d28d9;background:#f3e8ff;border:1px solid #e9d5ff;padding:5px 14px;border-radius:999px;margin-bottom:18px;}
      .sv-eco-intro{text-align:center;max-width:840px;margin:0 auto 72px;}
      .sv-eco-intro h2{font-size:clamp(28px,3.8vw,44px);font-weight:800;color:#1e1b4b;line-height:1.2;margin-bottom:22px;}
      .sv-eco-intro p{font-size:17px;color:#475569;line-height:1.8;margin-bottom:14px;}
      .sv-eco-stripe{display:flex;align-items:center;gap:20px;margin:0 auto 52px;}
      .sv-eco-stripe-line{flex:1;height:2px;background:linear-gradient(90deg,transparent,#c7d2fe);}
      .sv-eco-stripe-line.rev{background:linear-gradient(90deg,#c7d2fe,transparent);}
      .sv-eco-stripe h2{font-size:clamp(20px,2.5vw,30px);font-weight:800;color:#1e1b4b;white-space:nowrap;margin:0;}

      /* Row */
      .sv-eco-row{display:grid;grid-template-columns:1fr 1fr;border-radius:20px;overflow:hidden;box-shadow:0 8px 40px rgba(30,27,75,.09);margin-bottom:6px;}
      .sv-eco-row.rev-layout{direction:rtl;}
      .sv-eco-row.rev-layout>*{direction:ltr;}

      /* Image pane */
      .sv-eco-img-pane{position:relative;overflow:hidden;min-height:380px;}
      .sv-eco-img-pane img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .7s cubic-bezier(.22,1,.36,1);}
      .sv-eco-row:hover .sv-eco-img-pane img{transform:scale(1.06);}
      .sv-eco-img-overlay{position:absolute;inset:0;}
      .sv-eco-badge{position:absolute;bottom:20px;left:20px;display:flex;align-items:center;gap:10px;background:rgba(255,255,255,.93);backdrop-filter:blur(8px);border-radius:40px;padding:8px 18px 8px 10px;box-shadow:0 4px 16px rgba(0,0,0,.15);}
      .sv-eco-badge-icon{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
      .sv-eco-badge span{font-size:13px;font-weight:700;color:#1e1b4b;}

      /* Number accent on image */
      .sv-eco-num{position:absolute;top:20px;right:20px;width:48px;height:48px;border-radius:50%;background:rgba(255,255,255,.18);border:2px solid rgba(255,255,255,.4);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:800;color:#fff;}

      /* Content pane */
      .sv-eco-content{background:#ffffff;padding:44px 48px;display:flex;flex-direction:column;justify-content:center;}
      .sv-eco-tag{display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;padding:4px 12px;border-radius:999px;margin-bottom:16px;width:fit-content;}
      .sv-eco-content h3{font-size:26px;font-weight:800;color:#1e1b4b;margin-bottom:6px;line-height:1.25;}
      .sv-eco-sub-label{font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-bottom:18px;}
      .sv-eco-content p.body{font-size:15px;color:#475569;line-height:1.75;margin-bottom:20px;}
      .sv-eco-bullets{list-style:none;padding:0;margin:0 0 22px;display:grid;grid-template-columns:1fr 1fr;gap:8px 14px;}
      .sv-eco-bullets li{display:flex;align-items:flex-start;gap:8px;font-size:13.5px;color:#334155;}
      .sv-eco-bullets li i{margin-top:3px;flex-shrink:0;font-size:12px;}
      .sv-eco-quote{font-size:13.5px;font-style:italic;font-weight:700;padding-top:16px;border-top:1px solid #e2e8f0;margin:0;display:flex;align-items:flex-start;gap:8px;}
      .sv-eco-quote i{opacity:.45;margin-top:2px;flex-shrink:0;}

      /* Entrance animations */
      .sv-eco-row{opacity:0;transform:translateY(50px);transition:opacity .8s cubic-bezier(.22,1,.36,1),transform .8s cubic-bezier(.22,1,.36,1);}
      .sv-eco-row.sv-visible{opacity:1;transform:translateY(0);}
      .sv-eco-img-pane{opacity:0;transform:translateX(-44px);transition:opacity .9s .2s cubic-bezier(.22,1,.36,1),transform .9s .2s cubic-bezier(.22,1,.36,1);}
      .sv-eco-row.rev-layout .sv-eco-img-pane{transform:translateX(44px);}
      .sv-eco-row.sv-visible .sv-eco-img-pane{opacity:1;transform:translateX(0);}
      .sv-eco-content{opacity:0;transform:translateX(44px);transition:opacity .9s .35s cubic-bezier(.22,1,.36,1),transform .9s .35s cubic-bezier(.22,1,.36,1);}
      .sv-eco-row.rev-layout .sv-eco-content{transform:translateX(-44px);}
      .sv-eco-row.sv-visible .sv-eco-content{opacity:1;transform:translateX(0);}

      /* Pipeline block */
      .sv-eco-pipeline{margin-top:72px;border-radius:20px;overflow:hidden;background:linear-gradient(135deg,#1e3a5f 0%,#0b2545 55%,#12003d 100%);padding:60px 52px;position:relative;}
      .sv-eco-pipeline::before{content:'';position:absolute;top:-80px;right:-80px;width:340px;height:340px;border-radius:50%;background:radial-gradient(circle,rgba(99,102,241,.22) 0%,transparent 70%);pointer-events:none;}
      .sv-eco-pipeline::after{content:'';position:absolute;bottom:-70px;left:-70px;width:280px;height:280px;border-radius:50%;background:radial-gradient(circle,rgba(168,85,247,.18) 0%,transparent 70%);pointer-events:none;}
      .sv-eco-pipeline h2{font-size:clamp(22px,3vw,36px);font-weight:800;color:#fff;margin-bottom:10px;text-align:center;}
      .sv-pipe-sub{text-align:center;color:#93c5fd;font-size:13px;font-weight:700;margin-bottom:48px;letter-spacing:.08em;text-transform:uppercase;}
      .sv-journey-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:52px;}
      .sv-journey-card{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.11);border-radius:16px;padding:28px 22px;text-align:center;transition:background .3s,transform .3s;}
      .sv-journey-card:hover{background:rgba(255,255,255,.11);transform:translateY(-4px);}
      .sv-journey-icon{width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:22px;}
      .sv-journey-card h4{font-size:13px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;margin-bottom:12px;}
      .sv-steps{display:flex;flex-wrap:wrap;justify-content:center;gap:6px;}
      .sv-step{font-size:12px;font-weight:600;color:#e2e8f0;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);border-radius:999px;padding:3px 10px;}
      .sv-commit{display:grid;grid-template-columns:1fr auto;gap:40px;align-items:center;padding-top:40px;border-top:1px solid rgba(255,255,255,.1);}
      .sv-commit p{font-size:15px;color:#cbd5e0;line-height:1.8;margin-bottom:12px;}
      .sv-commit p:last-child{margin:0;}
      .sv-tagline{text-align:center;padding:30px 36px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:16px;min-width:260px;}
      .sv-tagline p{font-size:26px;font-weight:800;color:#fff;margin-bottom:8px;}
      .sv-tagline em{font-size:14px;font-style:italic;color:#a5b4fc;font-weight:600;}

      @media(max-width:860px){
        .sv-eco-row,.sv-eco-row.rev-layout{grid-template-columns:1fr;direction:ltr;}
        .sv-eco-img-pane{min-height:240px;}
        .sv-eco-content{padding:32px 28px;}
        .sv-eco-bullets{grid-template-columns:1fr;}
        .sv-journey-grid{grid-template-columns:1fr;}
        .sv-commit{grid-template-columns:1fr;}
        .sv-tagline{min-width:0;}
        .sv-eco-pipeline{padding:40px 24px;}
      }
    </style>

    <div class="skillvation-container">

      {{-- Intro --}}
      <div class="sv-eco-intro" data-reveal="fade-up">
        <div class="sv-eco-eyebrow"><i class="fa-solid fa-star" style="font-size:10px;"></i> Our Approach</div>
        <h2>Building Future-Ready Schools<br>Through Skill Education</h2>
        <p>Education is evolving beyond the traditional boundaries of knowledge acquisition. Schools today are increasingly focused on developing <strong>competencies, practical skills, creativity, critical thinking and real-world readiness</strong> alongside academic learning.</p>
        <p><strong>Skillvation partners with schools to make skill education a structured and sustainable part of the learning ecosystem.</strong></p>
        <p>We bring together <strong>curriculum-aligned content, digital learning, AI, teacher development, practical infrastructure and project-based learning</strong> — supporting the entire journey from <strong>learning and understanding to practising, creating and applying</strong>.</p>
      </div>

      {{-- Sub-heading stripe --}}
      <div class="sv-eco-stripe">
        <div class="sv-eco-stripe-line"></div>
        <h2>Our Skill Education Ecosystem</h2>
        <div class="sv-eco-stripe-line rev"></div>
      </div>

      {{-- 5 alternating image + content rows --}}
      @php
      $ecoCards = [
        [
          'rev'   => true,
          'num'   => '01',
          'img'   => asset('frontend/img/skillbox/Workonmaterialsandmachines.jpeg'),
          'alt'   => 'Skill Education Infrastructure — Labs',
          'grad'  => 'linear-gradient(135deg,rgba(180,83,9,.6),rgba(245,158,11,.35))',
          'bBg'   => '#b45309','bIcon'=>'fa-building-columns','bLabel'=>'Skill Labs',
          'tagBg' => '#fffbeb','tagClr'=>'#b45309','tagIcon'=>'fa-building-columns','tagLabel'=>'Learning Environments',
          'clr'   => '#b45309','title'=>'Skill Education Infrastructure',
          'sub'   => 'Creating Environments for Experiential Learning',
          'body'  => 'Supports schools in designing <strong>purpose-driven skill education environments and lab setups</strong> aligned with chosen skill areas — bridging theory and application.',
          'pts'   => ['CBSE aligned composite skill lab','Purpose-built skill infrastructure','Theory–application bridge','Dedicated experiential spaces','Hands-on exploration support','Strengthens skill ecosystem'],
          'qt'    => 'Creating the environment where learning becomes experience.',
        ],
        [
          'rev'   => true,
          'num'   => '02',
          'img'   => asset('frontend/img/skillbox/ai_empowered.jpg'),
          'alt'   => 'AI Lataa — AI-Powered Education',
          'grad'  => 'linear-gradient(135deg,rgba(3,105,161,.6),rgba(14,165,233,.35))',
          'bBg'   => '#0369a1','bIcon'=>'fa-robot','bLabel'=>'AI Lataa',
          'tagBg' => '#e0f2fe','tagClr'=>'#0369a1','tagIcon'=>'fa-robot','tagLabel'=>'Artificial Intelligence',
          'clr'   => '#0369a1','title'=>'AI Lataa (Learning & teaching assist)',
          'sub'   => 'AI-Powered Support for Learning & Teaching',
          'body'  => 'Brings artificial intelligence into the educational ecosystem — an intelligent support layer for <strong>teachers and students</strong> to explore, interact with and learn through AI-enabled experiences.',
          'pts'   => ['AI assistance for teachers','Enhanced student engagement','Practical AI exposure','Digital readiness building','Adapts to emerging tech'],
          'qt'    => 'Enabling schools to embrace AI as part of modern education.',
        ],
        [
          'rev'   => false,
          'num'   => '03',
          'img'   => asset('frontend/img/skillbox/teacher_upskilling.jpg'),
          'alt'   => 'Train the Teacher Programme',
          'grad'  => 'linear-gradient(135deg,rgba(4,120,87,.6),rgba(16,185,129,.35))',
          'bBg'   => '#047857','bIcon'=>'fa-chalkboard-user','bLabel'=>'TTT Programme',
          'tagBg' => '#ecfdf5','tagClr'=>'#047857','tagIcon'=>'fa-chalkboard-user','tagLabel'=>'Teacher Development',
          'clr'   => '#047857','title'=>'TTT | Train the Teacher',
          'sub'   => 'Empowering Educators to Deliver Skill-Based Learning',
          'body'  => 'Helps educators develop the knowledge, methodologies and confidence to facilitate <strong>experiential, competency-based and skill-oriented learning</strong> — shifting from content delivery to facilitation.',
          'pts'   => ['Teacher skill education capability','Experiential methodology support','Continuous professional development','Internal capacity building','Practical facilitation skills'],
          'qt'    => 'Building the teacher capability required for future-ready education.',
        ],
        [
          'rev'   => true,
          'num'   => '04',
          'img'   => asset('frontend/img/skillbox/teacher_upskilling.jpg'),
          'alt'   => 'Upskill For Teacher Programme',
          'grad'  => 'linear-gradient(135deg,rgba(223, 63, 63, 0.6),rgba(226, 127, 88, 0.35))',
          'bBg'   => '#882b00','bIcon'=>'fa-chalkboard-user','bLabel'=>'U4T Programme',
          'tagBg' => '#ecfdf5','tagClr'=>'#882b00','tagIcon'=>'fa-chalkboard-user','tagLabel'=>'Teacher Development',
          'clr'   => '#882b00','title'=>'U4T | Upskill For Teacher',
          'sub'   => 'Empowering Educators to Deliver Skill-Based Learning',
          'body'  => 'Helps educators develop the knowledge, methodologies and confidence to facilitate <strong>experiential, competency-based and skill-oriented learning</strong> — shifting from content delivery to facilitation.',
          'pts'   => ['Teacher skill education capability','Experiential methodology support','Continuous professional development','Internal capacity building','Practical facilitation skills'],
          'qt'    => 'Building the teacher capability required for future-ready education.',
        ],
        [
          'rev'   => false,
          'num'   => '05',
          'img'   => asset('frontend/img/skillbox/Workonlifeforms.jpeg'),
          'alt'   => 'Project-Based Learning Kits',
          'grad'  => 'linear-gradient(135deg,rgba(190,24,93,.6),rgba(236,72,153,.35))',
          'bBg'   => '#be185d','bIcon'=>'fa-screwdriver-wrench','bLabel'=>'PBL Kits',
          'tagBg' => '#fdf2f8','tagClr'=>'#be185d','tagIcon'=>'fa-screwdriver-wrench','tagLabel'=>'Project-Based Learning',
          'clr'   => '#be185d','title'=>'Project-Based Learning Kits',
          'sub'   => 'Turning Concepts into Creation',
          'body'  => 'Structured kits enabling students to <strong>design, build, experiment, test, collaborate and solve problems</strong> — developing competencies well beyond academic knowledge.',
          'pts'   => ['Practical & engaging learning','Creativity & critical thinking','Collaboration & innovation','Tangible student outcomes','Ready-to-use resources'],
          'qt'    => 'Turning knowledge into skills through purposeful creation.',
        ],
      ];
      @endphp

      <div style="display:flex;flex-direction:column;gap:6px;">
        @foreach($ecoCards as $c)
        <div class="sv-eco-row {{ $c['rev'] ? 'rev-layout' : '' }}" data-eco-row>

          {{-- Image pane --}}
          <div class="sv-eco-img-pane">
            <img src="{{ $c['img'] }}" alt="{{ $c['alt'] }}">
            <div class="sv-eco-img-overlay" style="background:{{ $c['grad'] }};"></div>
            <div class="sv-eco-num">{{ $c['num'] }}</div>
            <div class="sv-eco-badge">
              <div class="sv-eco-badge-icon" style="background:{{ $c['bBg'] }};">
                <i class="fa-solid {{ $c['bIcon'] }}" style="color:#fff;font-size:15px;"></i>
              </div>
              <span>{{ $c['bLabel'] }}</span>
            </div>
          </div>

          {{-- Content pane --}}
          <div class="sv-eco-content">
            <div class="sv-eco-tag" style="background:{{ $c['tagBg'] }};color:{{ $c['tagClr'] }};">
              <i class="fa-solid {{ $c['tagIcon'] }}" style="font-size:11px;"></i>
              {{ $c['tagLabel'] }}
            </div>
            <h3>{{ $c['title'] }}</h3>
            <p class="sv-eco-sub-label" style="color:{{ $c['clr'] }};">{{ $c['sub'] }}</p>
            <p class="body">{!! $c['body'] !!}</p>
            <ul class="sv-eco-bullets">
              @foreach($c['pts'] as $pt)
              <li><i class="fa-solid fa-circle-check" style="color:{{ $c['clr'] }};"></i>{{ $pt }}</li>
              @endforeach
            </ul>
            <p class="sv-eco-quote" style="color:{{ $c['clr'] }};">
              <i class="fa-solid fa-quote-left" style="font-size:12px;"></i>
              {{ $c['qt'] }}
            </p>
          </div>

        </div>
        @endforeach
      </div>

      {{-- One Partner. One Integrated Ecosystem. --}}
      <div class="sv-eco-pipeline" data-eco-pipeline>
        <p class="sv-pipe-sub"><i class="fa-solid fa-infinity" style="margin-right:8px;"></i>The Complete Picture</p>
        <h2>One Partner. One Integrated Ecosystem.</h2>
        <p style="text-align:center;color:#cbd5e0;font-size:16px;max-width:640px;margin:0 auto 48px;line-height:1.7;">
          Skillvation brings these capabilities together to support schools across the complete skill education journey.
        </p>

        <div class="sv-journey-grid">
          @php
          $journeys = [
            ['fa-school','#f472b6','rgba(244,114,182,.15)','For Schools',  ['Plan','Implement','Develop','Scale']],
            ['fa-person-chalkboard','#34d399','rgba(52,211,153,.15)','For Teachers',['Train','Adapt','Facilitate','Innovate']],
            ['fa-user-graduate','#60a5fa','rgba(96,165,250,.15)','For Students', ['Learn','Explore','Practise','Create','Apply']],
          ];
          @endphp
          @foreach($journeys as $j)
          <div class="sv-journey-card" data-eco-jcard>
            <div class="sv-journey-icon" style="background:{{ $j[2] }};"><i class="fa-solid {{ $j[0] }}" style="color:{{ $j[1] }};"></i></div>
            <h4 style="color:{{ $j[1] }};">{{ $j[3] }}</h4>
            <div class="sv-steps">
              @foreach($j[4] as $s)<span class="sv-step">{{ $s }}</span>@endforeach
            </div>
          </div>
          @endforeach
        </div>

        <div class="sv-commit">
          <div>
            <h3 style="font-size:20px;font-weight:800;color:#fff;margin-bottom:16px;">Our Commitment to Schools</h3>
            <p>We work alongside schools as a <strong style="color:#fff;">long-term Skill Education Partner</strong>, adapting our solutions to the school's academic environment, student needs and implementation goals.</p>
            <p>Our objective is to help schools build skill education as an <strong style="color:#fff;">integral part of the student learning journey</strong> — not just an additional programme.</p>
          </div>
          <div class="sv-tagline">
            <p>Skillvation</p>
            <em>Enabling Schools to Move<br>from Learning to Capability.</em>
          </div>
        </div>
      </div>

    </div>
  </section>

  @push('scripts')
  <script>
  (function(){
    'use strict';
    /* Eco rows slide in */
    var rows = document.querySelectorAll('[data-eco-row]');
    if(rows.length){
      var ro = new IntersectionObserver(function(entries){
        entries.forEach(function(e){
          if(e.isIntersecting){ e.target.classList.add('sv-visible'); ro.unobserve(e.target); }
        });
      },{threshold:0.12,rootMargin:'0px 0px -40px 0px'});
      rows.forEach(function(r){ ro.observe(r); });
    }
    /* Pipeline fade up */
    var pipe = document.querySelector('[data-eco-pipeline]');
    if(pipe){
      pipe.style.cssText += 'opacity:0;transform:translateY(40px);transition:opacity .9s .1s cubic-bezier(.22,1,.36,1),transform .9s .1s cubic-bezier(.22,1,.36,1);';
      var po = new IntersectionObserver(function(entries){
        if(entries[0].isIntersecting){ pipe.style.opacity='1'; pipe.style.transform='translateY(0)'; po.disconnect(); }
      },{threshold:0.12});
      po.observe(pipe);
    }
    /* Journey cards stagger */
    var jCards = document.querySelectorAll('[data-eco-jcard]');
    jCards.forEach(function(c,i){
      c.style.cssText += 'opacity:0;transform:translateY(28px);transition:opacity .6s '+(0.3+i*.13)+'s ease,transform .6s '+(0.3+i*.13)+'s ease;';
    });
    if(pipe){
      var jo = new IntersectionObserver(function(entries){
        if(entries[0].isIntersecting){
          jCards.forEach(function(c){ c.style.opacity='1'; c.style.transform='translateY(0)'; });
          jo.disconnect();
        }
      },{threshold:0.2});
      jo.observe(pipe);
    }
  })();
  </script>
  @endpush

  <!-- 10. News Section -->
  <!-- <section class="skillvation-section">
    <div class="skillvation-container">
      <h2 class="text-3xl font-bold mb-2">News</h2>
      <p class="text-gray-600">Latest updates from the Global Skills Academy network</p>

      <div class="skillvation-news-grid">
        <a href="https://www.unesco.org/en/articles/china-southeast-asia-tvet-management-capacity-building-workshop-successfully-concluded" target="_blank" rel="noopener" class="skillvation-news-card">
          <div class="skillvation-news-content">
            <div>
              <div class="skillvation-news-tag">News</div>
              <div class="skillvation-news-title">China-Southeast Asia TVET Management Capacity Building Workshop Successfully Concluded</div>
            </div>
            <div class="skillvation-news-date">3 July 2026</div>
          </div>
        </a>

        <a href="https://www.unesco.org/en/articles/unescos-global-skills-academy-expanding-digital-and-ai-skills-across-tvet-systems-kenya" target="_blank" rel="noopener" class="skillvation-news-card">
          <div class="skillvation-news-content">
            <div>
              <div class="skillvation-news-tag">News</div>
              <div class="skillvation-news-title">UNESCO's Global Skills Academy: expanding digital and AI skills across TVET systems in Kenya</div>
            </div>
            <div class="skillvation-news-date">18 June 2026</div>
          </div>
        </a>

        <a href="https://www.unesco.org/en/articles/unescos-global-skills-academy-tesda-expands-access-free-digital-skills-and-ai-courses-philippines" target="_blank" rel="noopener" class="skillvation-news-card">
          <div class="skillvation-news-content">
            <div>
              <div class="skillvation-news-tag">News</div>
              <div class="skillvation-news-title">UNESCO's Global Skills Academy: TESDA expands access to free digital skills and AI courses in the Philippines</div>
            </div>
            <div class="skillvation-news-date">10 June 2026</div>
          </div>
        </a>

        <a href="https://www.unesco.org/en/articles/unescos-global-education-coalition-empowers-ugandas-educators-ai-and-digital-skills-inclusive-tvet" target="_blank" rel="noopener" class="skillvation-news-card">
          <div class="skillvation-news-content">
            <div>
              <div class="skillvation-news-tag">Article</div>
              <div class="skillvation-news-title">UNESCO's Global Education Coalition empowers Uganda's educators with AI and digital skills</div>
            </div>
            <div class="skillvation-news-date">29 April 2026</div>
          </div>
        </a>
      </div>
    </div>
  </section> -->

</div>
@endsection

@push('scripts')
<script>
(function () {
  'use strict';

  /* ── IntersectionObserver scroll-reveal ───────────────────────── */
  var targets = document.querySelectorAll('[data-reveal]');
  if (!targets.length) return;

  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('sv-revealed');
        observer.unobserve(entry.target); // fire once
      }
    });
  }, {
    threshold: 0.10,   // 10 % of section visible = trigger
    rootMargin: '0px 0px -60px 0px'
  });

  targets.forEach(function (el) {
    observer.observe(el);
  });
})();
</script>
@endpush
