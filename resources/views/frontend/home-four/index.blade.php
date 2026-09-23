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
</style>
@endpush

@section('contents')
<div class="skillvation-page">

  <!-- 1. Hero Title Banner -->
  <section class="skillvation-hero-banner">
    <div class="skillvation-container">
      <h1>India's Favorite Skill Platform</h1>
      <p>Empowering students and teachers for the future of education</p>
    </div>
  </section>

  <!-- 2. Intro Section with Video Feature -->
  <section class="skillvation-section">
    <div class="skillvation-container">
      <div class="skillvation-grid-2col">
        <div>
          <!-- <p>
            The Global Skills Academy (GSA) is an initiative dedicated to addressing the pressing labour skills gaps and empowering individuals for a future-ready workforce. Under the umbrella of <a href="https://www.unesco.org/en/global-education-coalition" target="_blank" rel="noopener" class="text-blue-600 underline font-semibold">UNESCO Global Education Coalition</a> and in line with <a href="https://unesdoc.unesco.org/ark:/48223/pf0000383360" target="_blank" rel="noopener" class="text-blue-600 underline font-semibold">UNESCO Strategy for Technical and Vocational Education and Training (TVET)</a>, the GSA is committed to supporting ten million youth and adults globally in building essential skills for improved employability by 2029.
          </p>
          <p class="mt-4">
            The GSA focuses on empowering learners with key skills, including digital literacy, entrepreneurial skills, and green technologies. These skills are crucial for navigating the rapidly evolving job market driven by technological, economic, and societal transformations.
          </p> -->
          <!-- <p>
            At Skillvation, we believe that passionate teachers deserve opportunities that reward their talent, dedication, and expertise. Education is evolving, and so are the ways teachers can build meaningful careers. Our platform empowers educators to teach online, share their knowledge with students across the world, and earn a stable income from the comfort of their homesAt Skillvation, we believe that passionate teachers deserve opportunities that reward their talent, dedication, and expertise. Education is evolving, and so are the ways teachers can build meaningful careers. Our platform empowers educators to teach online, share their knowledge with students across the world, and earn a stable income from the comfort of their homes
          </p> -->
          <p>
          Education is evolving from knowing to doing.
          With the growing emphasis on skill education, competency-based learning and experiential learning,
          schools are increasingly expected to give students opportunities to develop skills through practical experiences
          rather than learning concepts only from textbooks.
          At Skillvation, we believe that this transformation needs more than a curriculum.
          It needs a place where skills can actually be practised.
          That is where our Skill Education Labs come in.
          We envision the school lab as an extension of the classroom — a dedicated environment where students can
          learn a concept, experience it, work with it and apply it.
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
          <!-- <div class="skillvation-media-caption">© UNESCO</div> -->
        </div>
      </div>

      <div class="mt-8">
        <!-- <p>
          To achieve this goal, the GSA leverages strategic partnerships and mobilizes 230 TVET institutions across 150 countries through the UNESCO and <a href="https://unevoc.unesco.org/home/fwd2About+the+UNEVOC+Network" target="_blank" rel="noopener" class="text-blue-600 underline font-semibold">UNEVOC networks</a>. By analyzing the evolving labour market’s skills supply and demand, the GSA offers free training and mentorship programs. These programs empower learners with in-demand skills, including digital literacy and skills, green technologies, and entrepreneurial capabilities.
        </p>
        <p class="mt-3">
          The GSA is dedicated to bridging this skills gap and empowering individuals to thrive in our 21st-century economy.
        </p> -->
        <!-- <p>We help teachers grow with practical, easy-to-learn skills designed for today’s classrooms. Our platform offers structured lessons, expert guidance, and real-world teaching strategies to support continuous improvement. We believe every teacher deserves the tools and confidence to inspire stronger learning outcomes.</p> -->
        <p>
        A Lab Designed Around Skill Education
        A Skillvation Lab brings together multiple dimensions of skill development within a structured learning
        environment.
        Students can explore Life Forms, Materials &amp; Machines, Human Services and interdisciplinary areas
        through practical activities, projects, experiments, making and problem-solving.
        Instead of simply asking students to learn about a skill, the lab gives them the opportunity to experience the
        skill first-hand.
        From a Lab to a Learning Ecosystem
        For Skillvation, a skill lab is not simply a room filled with equipment.
        It is a purpose-built learning ecosystem designed to answer a fundamental question:
        Our focus is therefore not only on setting up labs, but on making those labs active, accessible and relevant
        learning spaces where students continuously learn through experience.
        </p>
      </div>
    </div>
  </section>

  <!-- 3. Skills for the Future Global Platform -->
  <section class="skillvation-section bg-light">
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
          <a href="/skill2school" target="_blank" rel="noopener" class="skillvation-pill-btn">
            <span>Explore existing skills initiatives</span>
            <i class="fa-solid fa-arrow-right"></i>
          </a>
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
        <a href="#" target="_blank" rel="noopener" class="skillvation-stat-card stat-card-stem">
          <div class="stat-title">The Skill Gap</div>
          <div class="stat-desc">Independent findings from the companion India Skills Report 2026 indicate that while national youth employability has marginally risen to 56.35%, nearly 43.65% of Indian graduates still lack the necessary skills to be hired immediately by industry standards.</div>
        </a>

        <a href="#" target="_blank" rel="noopener" class="skillvation-stat-card stat-card-green">
          <div class="stat-title">The NEET Cohort</div>
          <div class="stat-desc">According to the NITI Aayog framework using NSSO baselines, 8.9 crore (89 million) young Indians between the ages of 15 and 29 fall under the category of NEET (Not in Education, Employment, or Training).</div>
        </a>

        <a href="#" target="_blank" rel="noopener" class="skillvation-stat-card stat-card-informal">
          <div class="stat-title">Graduate Unemployment</div>
          <div class="stat-desc">The transition from university to the corporate sector remains severely strained. Roughly 40% of young graduates under the age of 25 are unemployed. Out of 6.3 crore graduates in the 20–29 age bracket, 1.1 crore remain jobless due to skill mismatches.</div>
        </a>

        <a href="#" target="_blank" rel="noopener" class="skillvation-stat-card stat-card-gender">
          <div class="stat-title">Gender gap</div>
          <div class="stat-desc">in digital access and divide is the biggest obstacle for development for skills for the future</div>
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
  <section class="skillvation-section">
    <div class="skillvation-container">
      <div class="skillvation-grid-2col">
        <div>
          <h2 class="text-3xl font-bold mb-4">AI EmpowerED: Equipping teachers and learners for an AI-driven future</h2>
          <!-- <p>
            Through UNESCO’s Global Skills Academy, in partnership with Microsoft Elevate, KPMG International and Tablet Academy, AI EmpowerED supports TVET systems to equip educators and learners with practical and responsible AI skills for the future of work. By combining together global partnerships, national training networks and certification pathways, the programme expands access to AI learning at scale – empowering teachers to drive change in the classroom and enabling learners to develop the digital competencies needed to succeed in tomorrow’s economies.
          </p> -->
          <p>An AI Learning Management System (AI LMS) uses Artificial Intelligence to enhance teaching and learning by transforming traditional curriculum into skill-based, application-oriented content. Instead of limiting learning to textbooks and memorisation, an AI LMS supports lesson planning, assessments, and content delivery through smart recommendations, real-world examples, and competency-focused activities. Skillvation’s AI LMS, LATAA ( LEARNING AND TEACHING AI ASSISTANT) , is purpose-built for CBSE and NEP-aligned education, helping schools shift from rote learning to skill development and experiential learning—without disrupting their existing syllabus.
          </p>
          <p class="font-bold text-gray-900 mt-4 mb-6">
            Discover how AI-empowered skills are creating new pathways to inclusion and innovation.
          </p>
          <!-- <a href="https://www.unesco.org/en/global-education-coalition/skills-academy/ai-empowered-ed?hub=182955" target="_blank" rel="noopener" class="skillvation-pill-btn">
            <span>Learn more</span>
            <i class="fa-solid fa-arrow-right"></i>
          </a> -->
        </div>

        <div>
          <div class="skillvation-media-card">
            <img src="{{ asset('/frontend/img/skillbox/ai_empowered.jpg') }}" alt="AI EmpowerED Learning Session">
          </div>
          <!-- <div class="skillvation-media-caption">© Skillvation LATAA AI LMS</div> -->
        </div>
      </div>

      <!-- Quote -->
      <!-- <div class="skillvation-quote-box">
        <p>“I need to train in entrepreneurship and digital marketing. This way, I will be able to compete in the job market or start my own business.”</p>
        <cite>Jules Beugré Djoman, GSA student, Côte d'Ivoire</cite>
      </div> -->
    </div>
  </section>

  <!-- 5. Our Training Opportunities (Split Banner + 4 Tracks) -->
  <section class="skillvation-section bg-light" id="training-opportunities">
    <div class="skillvation-container">
      
      <!-- Split Blue Hero Block -->
      <div class="skillvation-split-banner">
        <div class="skillvation-split-banner-left">
          <h2>Our Upskilling opportunities</h2>
          <!-- <p>Explore our courses, certifiable training opportunities in Life Science, Machines & Materials, Human Services skills and other training programs.</p> -->
        <!-- <a href="/upskill4teacher" target="_blank" rel="noopener" class="skillvation-pill-btn">
            <span>Explore Courses</span>
            <i class="fa-solid fa-arrow-right"></i>
          </a> -->
          <p>Building the capability to deliver experiential and skill-based education Skillvation's Upskilling for Teachers program is designed to equip educators with the practical knowledge, tools and facilitation skills required to implement experiential Skill Education effectively. Our program enables them to extend their existing subject expertise into practical, interdisciplinary and work-oriented learning experiences.</p>
        </div>
        <div class="skillvation-split-banner-right">
          <img src="{{ asset('/frontend/img/skillbox/teacher_upskilling.jpg') }}" alt="Our Upskilling Opportunities - Teacher Training">
        </div>
      </div>

      <!-- Track 1: Digital Skills -->
      <div class="bg-white p-8 rounded-lg shadow-sm border border-gray-200 mb-8">
        <div class="skillvation-grid-2col">
          <div>
            <h2 class="text-2xl font-bold mb-3 text-blue-900">Work on Life Forms</h2>
            <!-- <p class="text-gray-700 mb-4">
              Digital competence receives a growing demand, with more than 75% of companies looking to adopt digital technologies such as big data, cloud computing and artificial intelligence, and 86% of companies incorporating digital platforms in their digital marketing strategies in the next five years (<a href="https://www.weforum.org/publications/the-future-of-jobs-report-2023/digest/" target="_blank" rel="noopener" class="text-blue-600 underline">The Future of Jobs Report 2023</a>).
            </p> -->
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
            <!-- <p class="font-semibold text-gray-800 mb-2">Access free, certifiable digital literacy and skills training with our partners:</p>
            <ul class="skillvation-partner-pills">
              <li><a href="https://www.unesco.org/en/global-education-coalition/skills-academy/aleph" target="_blank">Aleph Inc.</a></li>
              <li><a href="https://www.unesco.org/en/global-education-coalition/skills-academy/anthology" target="_blank">Anthology</a></li>
              <li><a href="https://www.unesco.org/en/global-education-coalition/skills-academy/china-pocy" target="_blank">China POCY Group</a></li>
              <li><span>Cisco</span></li>
              <li><span>Coursera</span></li>
              <li><span>Fundación Telefónica</span></li>
              <li><a href="https://www.unesco.org/en/global-education-coalition/skills-academy/giz-atingi" target="_blank">GIZ-atingi</a></li>
              <li><a href="https://www.unesco.org/en/global-education-coalition/skills-academy/huawei" target="_blank">Huawei</a></li>
              <li><a href="https://www.unesco.org/en/global-education-coalition/skills-academy/ibm" target="_blank">IBM</a></li>
              <li><span>ITU</span></li>
              <li><a href="https://www.unesco.org/en/global-education-coalition/skills-academy/ai-empowered-ed" target="_blank">Microsoft</a></li>
              <li><span>Orange</span></li>
              <li><a href="https://www.unesco.org/en/global-education-coalition/skills-academy/outsystems" target="_blank">Outsystems</a></li>
              <li><a href="https://www.unesco.org/en/global-education-coalition/skills-academy/pix" target="_blank">Pix</a></li>
              <li><a href="https://www.unesco.org/en/global-education-coalition/skills-academy/technovation" target="_blank">Technovation</a></li>
            </ul> -->
          </div>
          <div>
            <div class="skillvation-media-card">
              <img src="{{ asset('/frontend/img/skillbox/Workonlifeforms.jpeg') }}" alt="Work on Life Forms">
            </div>
            <!-- <div class="skillvation-media-caption">© Skillvation Life Forms Lab</div> -->
          </div>
        </div>
      </div>

      <!-- Track 2: Green Skills -->
      <div class="bg-white p-8 rounded-lg shadow-sm border border-gray-200 mb-8">
        <div class="skillvation-grid-2col">
          <div>
            <h2 class="text-2xl font-bold mb-3 text-green-900">Work on Materials & Machines</h2>
            <!-- <p class="text-gray-700 mb-4">
              Green expertise is hired 1.19x more, and demand for green and sustainability skills has grown by more than 60% since 2016 in economies like sustainable fashion, environmental services and renewable energy. Projection shows demand will outstrip supply in 5 years' time, emphasizing the critical need for green skills development (<a href="https://economicgraph.linkedin.com/research/global-green-skills-report" target="_blank" rel="noopener" class="text-blue-600 underline">Global Green Skills Report 2023</a>).
            </p> -->
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
            <!-- <p class="font-semibold text-gray-800 mb-2">Access free, certifiable green and sustainability skills training with our partners:</p>
            <ul class="skillvation-partner-pills">
              <li><a href="https://www.unesco.org/en/global-education-coalition/skills-academy/festo" target="_blank">FESTO</a></li>
              <li><a href="https://www.unesco.org/en/global-education-coalition/skills-academy/giz-atingi" target="_blank">GIZ-atingi</a></li>
              <li><a href="https://www.unesco.org/en/global-education-coalition/skills-academy/ibm" target="_blank">IBM</a></li>
              <li><a href="https://www.unesco.org/en/global-education-coalition/skills-academy/siemens-stiftung" target="_blank">Siemens Stiftung</a></li>
              <li><a href="https://www.unesco.org/en/global-education-coalition/skills-academy/skilled" target="_blank">SkillEd</a></li>
              <li><span>WHO Academy</span></li>
            </ul> -->
          </div>
          <div>
            <div class="skillvation-media-card">
              <img src="{{ asset('/frontend/img/skillbox/Workonmaterialsandmachines.jpeg') }}" alt="Work on Materials and Machines">
            </div>
            <!-- <div class="skillvation-media-caption">© Skillvation Materials & Machines Lab</div> -->
          </div>
        </div>
      </div>

      <!-- Track 3: Entrepreneurial Skills -->
      <div class="bg-white p-8 rounded-lg shadow-sm border border-gray-200 mb-8">
        <div class="skillvation-grid-2col">
          <div>
            <h2 class="text-2xl font-bold mb-3 text-red-900">Work on Human Services</h2>
            <!-- <p class="text-gray-700 mb-4">
              Entrepreneurial and transversal skills can boost careers by developing empathy, agility and readiness to learn, improving communication and project management, identifying opportunities and building leadership.
            </p> -->
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
            <!-- <p class="font-semibold text-gray-800 mb-2">Access free, certifiable entrepreneurial and transversal skills training with our partners:</p>
            <ul class="skillvation-partner-pills">
              <li><a href="https://www.unesco.org/en/global-education-coalition/skills-academy/generation-global" target="_blank">Generation Global</a></li>
              <li><a href="https://www.unesco.org/en/global-education-coalition/skills-academy/giz-atingi" target="_blank">GIZ-atingi</a></li>
              <li><a href="https://www.unesco.org/en/global-education-coalition/skills-academy/hp" target="_blank">HP LIFE</a></li>
            </ul> -->
          </div>
          <div>
            <div class="skillvation-media-card">
              <img src="{{ asset('/frontend/img/skillbox/workonhumanservices.jpeg') }}" alt="Work on Human Services">
            </div>
            <!-- <div class="skillvation-media-caption">© Skillvation Human Services Lab</div> -->
          </div>
        </div>
      </div>

      <!-- Track 4: Mentorship Programmes -->
      <!-- <div class="bg-white p-8 rounded-lg shadow-sm border border-gray-200 mb-8"> -->
        <!-- <div class="skillvation-grid-2col">
          <div>
            <h2 class="text-2xl font-bold mb-3 text-amber-900">Mentorship programmes</h2>
            <p class="text-gray-700 mb-4">
              Mentorship programs provide each mentee a unique experience through a dedicated mentor from industry, providing insights and experience about study and personal development, giving guidance in career planning and advancement, opening doors for potential job opportunities.
            </p>
            <p class="font-semibold text-gray-800 mb-2">Enroll in free mentorship programs with our partners:</p>
            <ul class="skillvation-partner-pills">
              <li><span>DIOR</span></li>
              <li><a href="https://www.unesco.org/en/global-education-coalition/skills-academy/kpmg" target="_blank">KPMG</a></li>
              <li><a href="https://www.unesco.org/en/global-education-coalition/skills-academy/ja-americas" target="_blank">Junior Achievement Americas</a></li>
            </ul>
          </div>
          <div>
            <div class="skillvation-media-card">
              <img src="https://www.unesco.org/sites/default/files/styles/paragraph_medium_tablet/public/2024-04/global-skills-academy-mentorship.jpg.webp?itok=1JonceNt" alt="Mentorship Programmes">
            </div>
            <div class="skillvation-media-caption">© UNESCO</div>
          </div>
        </div> -->

        <!-- <div class="skillvation-quote-box mt-6">
          <p>“I'd say that UNESCO is doing a great job in bridging the gap between students and quality education in developing countries. The Global Skills Academy Initiative has also exposed students like me to experience a new way, the digital way, of enjoying quality education.”</p>
          <cite>Tolulope Omoyeni, Women@DIOR Nigeria</cite>
        </div> -->
      <!-- </div> -->

    </div>
  </section>

  <!-- 6. Our Working Model -->
  <section class="skillvation-section">
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
          <!-- <div class="skillvation-media-caption">© UNESCO</div> -->
        </div>
      </div>
    </div>
  </section>

  <!-- 7. GSA Mission in Figures -->
  

  <!-- 8. Regional Statistics -->
  

  <!-- 9. Ready to make a positive impact? CTA -->
  <section class="skillvation-section bg-light">
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
          <!-- <div class="skillvation-media-caption">© Skillvation Leaders of Learning</div> -->
        </div>
      </div>
    </div>
  </section>

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
