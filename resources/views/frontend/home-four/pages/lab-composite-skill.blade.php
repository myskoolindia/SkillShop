@extends('frontend.home-four.layouts.master')

@section('meta_title', 'Composite — Skill Labs for CBSE Schools | ' . config('app.name', 'Skillvation'))
@section('meta_description', 'Turnkey Robotics, AI, and coding labs built to CBSE specifications — installed, mapped to your syllabus, and staffed with trained teachers before the term starts.')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,500;0,6..72,600;1,6..72,400&family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">

<style>
  :root {
    --vl-navy: #17233F;
    --vl-navy-deep: #101A30;
    --vl-paper: #F5F2EA;
    --vl-marigold: #D98E2B;
    --vl-marigold-deep: #B9721B;
    --vl-teal: #2F6F62;
    --vl-ink: #2A2A28;
    --vl-hair: #D8D2C2;
    --vl-hair-on-navy: rgba(245, 242, 234, 0.22);
  }

  .vidyalab-page {
    background: var(--vl-paper);
    color: var(--vl-ink);
    font-family: 'IBM Plex Sans', system-ui, -apple-system, sans-serif;
    font-size: 16px;
    line-height: 1.6;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
  }

  .vidyalab-page h1,
  .vidyalab-page h2,
  .vidyalab-page h3,
  .vidyalab-page .serif {
    font-family: 'Newsreader', Georgia, serif;
    font-weight: 500;
    color: var(--vl-navy);
    line-height: 1.15;
    margin: 0;
  }

  .vidyalab-page a {
    color: inherit;
  }

  .vidyalab-wrap {
    max-width: 1120px;
    margin: 0 auto;
    padding: 0 24px;
  }

  .vidyalab-page button,
  .vidyalab-page .btn {
    font-family: 'IBM Plex Sans', sans-serif;
    cursor: pointer;
  }

  /* ---------- Hero ---------- */
  .vl-hero {
    background:#d390c1;
    color: var(--vl-paper);
    padding: 40px 0 40px;
  }
  .vl-hero-grid {
    display: grid;
    grid-template-columns: 1.05fr 0.95fr;
    gap: 56px;
    align-items: center;
  }
  @media (max-width: 860px) {
    .vl-hero-grid { grid-template-columns: 1fr; }
  }
  .vl-hero h1 {
    font-size: clamp(2.05rem, 4vw, 2.9rem);
    color: var(--vl-paper);
  }
  .vl-hero p.lead {
    margin-top: 20px;
    font-size: 1.08rem;
    max-width: 46ch;
    color: #D9D5C8;
    line-height: 1.65;
  }
  .vl-hero-ctas {
    margin-top: 32px;
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
  }
  .vl-btn-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: var(--vl-marigold);
    color: var(--vl-navy-deep) !important;
    border: none;
    padding: 14px 26px;
    font-size: 0.98rem;
    font-weight: 600;
    border-radius: 2px;
    text-decoration: none;
    transition: background 0.2s ease;
  }
  .vl-btn-primary:hover {
    background: #F0A643;
    color: var(--vl-navy-deep) !important;
  }
  .vl-btn-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    color: var(--vl-paper) !important;
    border: 1px solid var(--vl-hair-on-navy);
    padding: 14px 26px;
    font-size: 0.98rem;
    border-radius: 2px;
    text-decoration: none;
    transition: border-color 0.2s ease, background 0.2s ease;
  }
  .vl-btn-secondary:hover {
    border-color: var(--vl-paper);
    background: rgba(255, 255, 255, 0.05);
  }

  /* stat strip */
  .vl-stat-strip {
    margin-top: 44px;
    display: flex;
    border-top: 1px solid var(--vl-hair-on-navy);
    padding-top: 22px;
    gap: 36px;
    flex-wrap: wrap;
  }
  .vl-stat-strip div { min-width: 120px; }
  .vl-stat-strip .num {
    font-family: 'Newsreader', serif;
    font-size: 1.7rem;
    color: var(--vl-marigold);
    display: block;
    line-height: 1.2;
  }
  .vl-stat-strip .label {
    font-size: 0.85rem;
    color: #B8B4A6;
  }

  /* blueprint svg */
  .vl-blueprint {
    background: var(--vl-navy-deep);
    border: 1px solid var(--vl-hair-on-navy);
    border-radius: 2px;
    padding: 20px;
  }
  .vl-blueprint svg { width: 100%; height: auto; display: block; }
  .vl-blueprint-cap {
    margin-top: 12px;
    font-size: 0.8rem;
    color: #9B9688;
  }

  /* ---------- Section shell ---------- */
  .vl-section { padding: 76px 0; }
  .vl-eyebrow-line {
    width: 44px;
    height: 2px;
    background: var(--vl-marigold);
    margin-bottom: 18px;
  }
  .vl-section-head { max-width: 60ch; margin-bottom: 44px; }
  .vl-section-head h2 { font-size: clamp(1.6rem, 3vw, 2.15rem); }
  .vl-section-head p { margin-top: 14px; color: #4B4A44; font-size: 1.02rem; }

  /* ---------- Problem/Solution ---------- */
  .vl-split {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
    border: 1px solid var(--vl-hair);
  }
  @media (max-width: 760px) {
    .vl-split { grid-template-columns: 1fr; }
  }
  .vl-split > div { padding: 38px; }
  .vl-split .problem { border-right: 1px solid var(--vl-hair); }
  @media (max-width: 760px) {
    .vl-split .problem { border-right: none; border-bottom: 1px solid var(--vl-hair); }
  }
  .vl-split h3 { font-size: 1.25rem; margin-bottom: 14px; }
  .vl-split p { color: #4B4A44; margin: 0; }
  .vl-tag {
    display: inline-block;
    font-size: 0.78rem;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 2px;
    margin-bottom: 16px;
  }
  .vl-tag.warn { background: #F1E3D2; color: #8A5A1E; }
  .vl-tag.good { background: #DEE9E4; color: var(--vl-teal); }

  /* ---------- Included rows ---------- */
  .vl-row-list { border-top: 1px solid var(--vl-hair); }
  .vl-row-item {
    display: grid;
    grid-template-columns: 120px 220px 1fr;
    gap: 0 32px;
    padding: 26px 0;
    border-bottom: 1px solid var(--vl-hair);
    align-items: center;
  }
  @media (max-width: 860px) {
    .vl-row-item { grid-template-columns: 88px 1fr; gap: 0 20px; }
    .vl-row-item .vl-row-text { grid-column: 2; }
  }
  @media (max-width: 580px) {
    .vl-row-item { grid-template-columns: 1fr; gap: 14px; }
    .vl-row-item .vl-row-img { width: 100%; height: 180px; }
    .vl-row-item .vl-row-text { grid-column: 1; }
  }
  .vl-row-img {
    width: 120px; height: 88px;
    border-radius: 4px; overflow: hidden;
    background: #D6D2C8; flex-shrink: 0;
  }
  .vl-row-img img {
    width: 100%; height: 100%;
    object-fit: cover; display: block;
    transition: transform .4s ease;
  }
  .vl-row-item:hover .vl-row-img img { transform: scale(1.07); }
  .vl-row-text { display: flex; flex-direction: column; gap: 6px; }
  .vl-row-item h3 { font-size: 1.15rem; margin: 0; }
  .vl-row-item p { color: #4B4A44; margin: 0; }

  /* ---------- Testimonial ---------- */
  .vl-testimonial {
    background: var(--vl-navy);
    color: var(--vl-paper);
    padding: 70px 0;
  }
  .vl-testimonial blockquote {
    font-family: 'Newsreader', serif;
    font-style: italic;
    font-size: clamp(1.35rem, 2.6vw, 1.9rem);
    max-width: 44ch;
    margin: 0;
    color: black;
    line-height: 1.45;
  }
  .vl-testimonial cite {
    display: block;
    margin-top: 24px;
    font-style: normal;
    font-size: 0.92rem;
    color: #B8B4A6;
  }

  /* ---------- Lead form ---------- */
  .vl-form-panel {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 56px;
    align-items: start;
  }
  @media (max-width: 820px) {
    .vl-form-panel { grid-template-columns: 1fr; }
  }
  .vl-form-copy h2 { font-size: clamp(1.5rem, 3vw, 2rem); }
  .vl-form-copy ul { margin-top: 22px; padding-left: 0; list-style: none; }
  .vl-form-copy li {
    padding-left: 26px;
    position: relative;
    margin-bottom: 12px;
    color: #4B4A44;
  }
  .vl-form-copy li::before {
    content: "";
    position: absolute; left: 0; top: 9px;
    width: 8px; height: 8px;
    background: var(--vl-teal);
  }
  .vl-form {
    background: #fff;
    border: 1px solid var(--vl-hair);
    padding: 32px;
  }
  .vl-field { margin-bottom: 16px; }
  .vl-field label { display: block; font-size: 0.85rem; margin-bottom: 6px; color: #4B4A44; font-weight: 500; }
  .vl-field input {
    width: 100%;
    padding: 11px 12px;
    border: 1px solid var(--vl-hair);
    font-size: 0.95rem;
    font-family: inherit;
    background: var(--vl-paper);
    border-radius: 2px;
    box-sizing: border-box;
  }
  .vl-field input:focus {
    outline: 2.5px solid var(--vl-marigold);
    outline-offset: 1px;
    border-color: transparent;
  }
  .vl-form-submit {
    width: 100%;
    margin-top: 6px;
    background: var(--vl-navy);
    color: var(--vl-paper);
    border: none;
    padding: 14px;
    font-weight: 600;
    font-size: 0.98rem;
    border-radius: 2px;
    cursor: pointer;
    transition: background 0.2s ease;
  }
  .vl-form-submit:hover { background: var(--vl-navy-deep); }
  .vl-form-note { font-size: 0.8rem; color: #807C70; margin-top: 12px; }
  .vl-confirm {
    display: none;
    background: #DEE9E4;
    color: var(--vl-teal);
    padding: 20px 24px;
    font-size: 1rem;
    font-weight: 600;
    margin-top: 16px;
    border-radius: 4px;
    border-left: 4px solid var(--vl-teal);
    text-align: center;
  }
  .vl-confirm-icon {
    font-size: 2rem;
    display: block;
    margin-bottom: 8px;
  }

  /* ---------- Comparison table ---------- */
  .vl-table-wrap {
    overflow-x: auto;
  }
  .vl-table {
    width: 100%;
    border-collapse: collapse;
    border: 1px solid var(--vl-hair);
    background: #fff;
  }
  .vl-table th, 
  .vl-table td {
    text-align: left;
    padding: 16px 18px;
    border-bottom: 1px solid var(--vl-hair);
    font-size: 0.95rem;
  }
  .vl-table th {
    font-family: 'Newsreader', serif;
    font-weight: 500;
    color: var(--vl-navy);
    font-size: 1rem;
    background: #EDEADF;
  }
  .vl-table td.yes { color: var(--vl-teal); font-weight: 600; }
  .vl-table td.no { color: #9C6B3E; }
  .vl-table tr:last-child td { border-bottom: none; }

  /* ---------- FAQ Card Carousel ---------- */
  .vl-faq-carousel { position: relative; }

  .vl-faq-slide {
    display: none;
    animation: vl-fade-in .3s ease;
  }
  .vl-faq-slide.active { display: block; }

  @keyframes vl-fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  /* 3-col card grid */
  .vl-faq-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
  }
  @media (max-width: 860px) {
    .vl-faq-grid { grid-template-columns: repeat(2, 1fr); }
  }
  @media (max-width: 560px) {
    .vl-faq-grid { grid-template-columns: 1fr; }
  }

  /* Individual card */
  .vl-faq-card {
    background: var(--vl-paper);
    border: 1px solid var(--vl-hair);
    border-radius: 4px;
    padding: 28px 24px;
    display: flex;
    flex-direction: column;
    gap: 0;
    transition: box-shadow .25s, border-color .25s;
  }
  .vl-faq-card:hover {
    border-color: var(--vl-navy);
    box-shadow: 0 6px 24px rgba(23,35,63,.09);
  }

  .vl-faq-card__q {
    font-family: 'Newsreader', serif;
    font-size: 1.05rem;
    font-weight: 600;
    color: var(--vl-navy);
    line-height: 1.4;
    margin-bottom: 16px;
  }

  .vl-faq-card__divider {
    width: 32px;
    height: 2px;
    background: var(--vl-marigold);
    margin-bottom: 16px;
    flex-shrink: 0;
  }

  .vl-faq-card__a {
    font-family: 'IBM Plex Sans', sans-serif;
    font-size: .9rem;
    color: #4B4A44;
    line-height: 1.65;
    flex: 1;
  }

  /* Navigation bar */
  .vl-faq-nav {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid var(--vl-hair);
  }

  .vl-faq-btn {
    width: 40px; height: 40px;
    border-radius: 2px;
    border: 1px solid var(--vl-hair);
    background: var(--vl-paper);
    color: var(--vl-navy);
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background .2s, border-color .2s, color .2s;
    flex-shrink: 0;
  }
  .vl-faq-btn:hover:not(:disabled) {
    background: var(--vl-navy);
    border-color: var(--vl-navy);
    color: var(--vl-paper);
  }
  .vl-faq-btn:disabled { opacity: .3; cursor: not-allowed; }

  .vl-faq-dots { display: flex; gap: 8px; align-items: center; }
  .vl-faq-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: #C8C4BA;
    cursor: pointer;
    transition: background .2s, transform .2s;
    border: none; padding: 0;
  }
  .vl-faq-dot.active {
    background: var(--vl-navy);
    transform: scale(1.35);
  }

  .vl-faq-counter {
    font-size: .8rem;
    color: #807C70;
    font-family: 'IBM Plex Sans', sans-serif;
    letter-spacing: .04em;
    min-width: 36px;
    text-align: center;
  }
    border-bottom: 1px solid var(--vl-hair);
    padding: 20px 0;
  }
  .vl-summary {
    font-family: 'Newsreader', serif;
    font-size: 1.08rem;
    color: var(--vl-navy);
    cursor: pointer;
    list-style: none;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .vl-summary::-webkit-details-marker { display: none; }
  .vl-summary::after {
    content: "+";
    font-size: 1.3rem;
    color: var(--vl-marigold);
    font-family: 'IBM Plex Sans', sans-serif;
  }
  .vl-details[open] .vl-summary::after { content: "–"; }
  .vl-details p { margin-top: 14px; color: #4B4A44; max-width: 66ch; }

  /* ---------- Final CTA ---------- */
  .vl-final-cta {
    background: var(--vl-navy-deep);
    color: var(--vl-paper);
    padding: 70px 0;
    text-align: left;
  }
  .vl-final-cta h2 {
    color: var(--vl-paper);
    font-size: clamp(1.6rem, 3vw, 2.2rem);
    max-width: 20ch;
  }
  .vl-final-cta p {
    color: #B8B4A6;
    margin-top: 14px;
  }
  .vl-final-cta .vl-hero-ctas { margin-top: 28px; }
</style>
@endpush

@section('contents')
<div class="vidyalab-page">

  <!-- Hero Section -->
  <section class="vl-hero">
    <div class="vidyalab-wrap vl-hero-grid">
      <div>
        <h1>The skill lab your NEP&nbsp;2020 review committee will actually approve.</h1>
        <!-- <p class="lead">Turnkey Robotics, AI, and coding labs built to CBSE specifications — installed, mapped to your syllabus, and staffed with trained teachers before the term starts.</p> -->
        <div class="vl-hero-ctas">
          <a href="#book" class="vl-btn-primary">Visit our experience center</a>
          <a href="#checklist" class="vl-btn-secondary">Download brochure</a>
        </div>
        <!-- <div class="vl-stat-strip">
          <div><span class="num">212</span><span class="label">CBSE schools fitted</span></div>
          <div><span class="num">18</span><span class="label">states covered</span></div>
          <div><span class="num">46,000+</span><span class="label">students learning hands-on</span></div>
        </div> -->
      </div>
      <div class="vl-blueprint">
        <svg viewBox="0 0 420 320" role="img" aria-label="Floor plan diagram of a skill lab showing robotics, AI, electronics, and coding zones">
          <rect x="4" y="4" width="412" height="312" fill="none" stroke="#3A4665" stroke-width="1"/>
          <line x1="4" y1="106" x2="416" y2="106" stroke="#3A4665" stroke-width="1"/>
          <line x1="4" y1="212" x2="416" y2="212" stroke="#3A4665" stroke-width="1"/>
          <line x1="210" y1="4" x2="210" y2="316" stroke="#3A4665" stroke-width="1"/>

          <text x="20" y="30" fill="#D98E2B" font-family="IBM Plex Sans" font-size="12" font-weight="600">ROBOTICS BENCH</text>
          <circle cx="40" cy="66" r="14" fill="none" stroke="#7C87A6"/>
          <circle cx="80" cy="66" r="14" fill="none" stroke="#7C87A6"/>
          <circle cx="120" cy="66" r="14" fill="none" stroke="#7C87A6"/>
          <circle cx="160" cy="66" r="14" fill="none" stroke="#7C87A6"/>

          <text x="228" y="30" fill="#D98E2B" font-family="IBM Plex Sans" font-size="12" font-weight="600">AI &amp; DATA STATION</text>
          <rect x="228" y="46" width="60" height="40" fill="none" stroke="#7C87A6"/>
          <rect x="300" y="46" width="60" height="40" fill="none" stroke="#7C87A6"/>

          <text x="20" y="132" fill="#D98E2B" font-family="IBM Plex Sans" font-size="12" font-weight="600">ELECTRONICS LAB</text>
          <rect x="20" y="148" width="170" height="46" fill="none" stroke="#7C87A6"/>
          <line x1="63" y1="148" x2="63" y2="194" stroke="#7C87A6"/>
          <line x1="106" y1="148" x2="106" y2="194" stroke="#7C87A6"/>
          <line x1="149" y1="148" x2="149" y2="194" stroke="#7C87A6"/>

          <text x="228" y="132" fill="#D98E2B" font-family="IBM Plex Sans" font-size="12" font-weight="600">CODING PODS</text>
          <rect x="228" y="148" width="30" height="30" fill="none" stroke="#7C87A6"/>
          <rect x="268" y="148" width="30" height="30" fill="none" stroke="#7C87A6"/>
          <rect x="308" y="148" width="30" height="30" fill="none" stroke="#7C87A6"/>
          <rect x="348" y="148" width="30" height="30" fill="none" stroke="#7C87A6"/>

          <text x="20" y="238" fill="#D98E2B" font-family="IBM Plex Sans" font-size="12" font-weight="600">TEACHER STATION</text>
          <rect x="20" y="254" width="120" height="36" fill="none" stroke="#7C87A6"/>

          <text x="228" y="238" fill="#D98E2B" font-family="IBM Plex Sans" font-size="12" font-weight="600">DISPLAY &amp; REVIEW WALL</text>
          <rect x="228" y="254" width="160" height="36" fill="none" stroke="#7C87A6"/>
        </svg>
        <div class="vl-blueprint-cap">A typical 900 sq ft skill lab layout — adapted to your available classroom space.</div>
      </div>
    </div>
  </section>

  <!-- Outcomes Section -->
  <section class="vl-section" id="outcomes">
    <div class="vidyalab-wrap">
      <div class="vl-section-head">
        <div class="vl-eyebrow-line"></div>
        <h2>Most skill labs get installed once and inspected forever after.</h2>
        <p>We've walked into enough school storerooms to know the pattern. Here's the difference between a lab that sits idle and one your students actually use.</p>
      </div>
      <div class="vl-split">
        <div class="problem">
          <div style="width:100%; aspect-ratio:16/9; border-radius:4px; overflow:hidden; margin-bottom:24px; background:#E8E4DA;">
            <img
              src="https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&w=900&q=80"
              alt="Unused lab equipment stored in a school cupboard"
              style="width:100%; height:100%; object-fit:cover; display:block; filter:grayscale(30%);"
            />
          </div>
          <span class="vl-tag warn">What usually happens</span>
          <h3>A kit arrives, nobody is trained on it</h3>
          <p>Vendors deliver hardware against a purchase order, run one orientation session, and leave. Six months later the robotics kits are in a cupboard and the "lab" is a line item in the prospectus, not a place students go.</p>
        </div>
        <div>
          <div style="width:100%; aspect-ratio:16/9; border-radius:4px; overflow:hidden; margin-bottom:24px; background:#E8E4DA;">
            <img
              src="https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=900&q=80"
              alt="Students actively working in a composite skill lab"
              style="width:100%; height:100%; object-fit:cover; display:block;"
            />
          </div>
          <span class="vl-tag good">What we install instead</span>
          <h3>A lab mapped to what your teachers already teach</h3>
          <p>Every module is tied to a CBSE syllabus unit before installation. Teachers are certified to run it independently. We check in every term, not just at handover.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Program Section -->
  <section class="vl-section" id="program" style="background:#EDEADF;">
    <div class="vidyalab-wrap">
      <div class="vl-section-head">
        <div class="vl-eyebrow-line"></div>
        <h2>What's included in the setup</h2>
        <p>One vendor, one contract, one team accountable for the lab working — not just existing.</p>
      </div>
      <div class="vl-row-list">

        <div class="vl-row-item">
          <div class="vl-row-img">
            <img src="https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=360&q=80"
                 alt="Robotics kits and hardware workstations" loading="lazy" />
          </div>
          <div class="vl-row-text">
            <h3>Lab hardware &amp; workstations</h3>
          </div>
          <p>Robotics kits, AI/data stations, electronics benches, and coding pods sized to your enrolment and classroom footprint.</p>
        </div>

        <div class="vl-row-item">
          <div class="vl-row-img">
            <img src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=360&q=80"
                 alt="Curriculum mapping aligned to CBSE syllabus" loading="lazy" />
          </div>
          <div class="vl-row-text">
            <h3>Curriculum mapping</h3>
          </div>
          <p>Every activity is matched to a specific CBSE syllabus unit and grade, so the lab supports what's already being taught — not a separate elective nobody has time for.</p>
        </div>

        <div class="vl-row-item">
          <div class="vl-row-img">
            <img src="https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=360&q=80"
                 alt="Teacher training and certification session" loading="lazy" />
          </div>
          <div class="vl-row-text">
            <h3>Teacher training &amp; certification</h3>
          </div>
          <p>Your existing science and computer faculty are trained and certified to run sessions independently, with no dependence on our staff after handover.</p>
        </div>

        <div class="vl-row-item">
          <div class="vl-row-img">
            <img src="https://images.unsplash.com/photo-1621905251918-48416bd8575a?auto=format&fit=crop&w=360&q=80"
                 alt="Annual maintenance and technical support" loading="lazy" />
          </div>
          <div class="vl-row-text">
            <h3>Annual maintenance &amp; support</h3>
          </div>
          <p>Hardware servicing, software updates, and a direct line to our support team for the life of the contract.</p>
        </div>

        <div class="vl-row-item">
          <div class="vl-row-img">
            <img src="{{ asset('frontend/img/skillbox/compliance_documentation.jpg') }}"
                 alt="CBSE compliance documentation and inspection readiness" loading="lazy" />
          </div>
          <div class="vl-row-text">
            <h3>Compliance documentation</h3>
          </div>
          <p>NEP 2020 and CBSE skill-lab documentation prepared and ready to hand to your inspection committee.</p>
        </div>

      </div>
    </div>
  </section>

  <!-- Subscription Plans Section -->
  <section class="vl-section" id="plans">
    <div class="vidyalab-wrap">
      <div class="vl-section-head">
        <div class="vl-eyebrow-line"></div>
        <h2>Choose the right plan for your school</h2>
        <p>Every plan includes installation, curriculum mapping, and teacher training. Pick the tier that matches your school's size and ambition.</p>
      </div>

      <div style="display:grid; grid-template-columns: repeat(3,1fr); gap:0; border:1px solid var(--vl-hair);">

        <!-- Basic Plan -->
        <div style="padding:36px 32px; border-right:1px solid var(--vl-hair); display:flex; flex-direction:column; gap:0;">
          <div style="font-size:.8rem; font-weight:600; letter-spacing:.08em; text-transform:uppercase; color:#807C70; margin-bottom:14px;">Basic</div>
          <div style="font-family:'Newsreader',serif; font-size:2.4rem; color:var(--vl-navy); line-height:1; margin-bottom:4px;">₹3 <span style="font-size:1rem; color:#807C70; font-family:'IBM Plex Sans',sans-serif;">Lakh</span></div>
          <div style="font-size:.85rem; color:#807C70; margin-bottom:24px;">one-time setup</div>
          <div style="font-size:.95rem; color:var(--vl-navy); font-weight:500; margin-bottom:20px; padding-bottom:20px; border-bottom:1px solid var(--vl-hair);">For schools up to 500 students — single classroom lab, core CBSE compliance ready.</div>
          <ul style="list-style:none; padding:0; margin:0 0 28px; display:flex; flex-direction:column; gap:12px; flex:1;">
            @foreach([
              'Lab hardware for one 400 sq ft room',
              'Robotics & coding kits (Grades VI–X)',
            ] as $item)
            <li style="display:flex; align-items:flex-start; gap:10px; font-size:.9rem; color:#4B4A44;">
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none" style="flex-shrink:0; margin-top:2px;"><circle cx="8" cy="8" r="8" fill="#DEE9E4"/><path d="M5 8l2 2 4-4" stroke="#2F6F62" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
              {{ $item }}
            </li>
            @endforeach
          </ul>
          <a href="http://localhost/skillvation.comphp/club-shop/test-skill-1"
             target="_blank"
             style="display:inline-flex; align-items:center; justify-content:center; border:1px solid var(--vl-navy); color:var(--vl-navy); padding:13px 20px; font-size:.9rem; font-weight:600; text-decoration:none; border-radius:2px; transition:background .2s; font-family:'IBM Plex Sans',sans-serif;"
             onmouseover="this.style.background='rgba(23,35,63,.06)'" onmouseout="this.style.background='transparent'">
            View Basic Plan →
          </a>
        </div>

        <!-- Advance Plan -->
        <div style="padding:36px 32px; border-right:1px solid var(--vl-hair); display:flex; flex-direction:column; gap:0; background:var(--vl-navy); position:relative;">
          <div style="position:absolute; top:0; left:0; right:0; background:var(--vl-marigold); color:var(--vl-navy-deep); font-size:.75rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; text-align:center; padding:7px;">Most Popular</div>
          <div style="margin-top:28px; font-size:.8rem; font-weight:600; letter-spacing:.08em; text-transform:uppercase; color:#9B9688; margin-bottom:14px;">Advance</div>
          <div style="font-family:'Newsreader',serif; font-size:2.4rem; color:var(--vl-paper); line-height:1; margin-bottom:4px;">₹6 <span style="font-size:1rem; color:#9B9688; font-family:'IBM Plex Sans',sans-serif;">Lakh</span></div>
          <div style="font-size:.85rem; color:#9B9688; margin-bottom:24px;">one-time setup</div>
          <div style="font-size:.95rem; color:#D9D5C8; font-weight:500; margin-bottom:20px; padding-bottom:20px; border-bottom:1px solid rgba(245,242,234,.15);">For schools up to 1,000 students — full composite lab, AI modules, and ongoing curriculum refresh.</div>
          <ul style="list-style:none; padding:0; margin:0 0 28px; display:flex; flex-direction:column; gap:12px; flex:1;">
            @foreach([
              'Everything in Basic',
              '600 sq ft combined lab layout',
            ] as $item)
            <li style="display:flex; align-items:flex-start; gap:10px; font-size:.9rem; color:#D9D5C8;">
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none" style="flex-shrink:0; margin-top:2px;"><circle cx="8" cy="8" r="8" fill="rgba(217,142,43,.25)"/><path d="M5 8l2 2 4-4" stroke="#D98E2B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
              {{ $item }}
            </li>
            @endforeach
          </ul>
          <a href="#"
          target="_blank"
             style="display:inline-flex; align-items:center; justify-content:center; background:var(--vl-marigold); color:var(--vl-navy-deep); padding:13px 20px; font-size:.9rem; font-weight:600; text-decoration:none; border-radius:2px; transition:background .2s; font-family:'IBM Plex Sans',sans-serif; border:none;"
             onmouseover="this.style.background='#F0A643'" onmouseout="this.style.background='var(--vl-marigold)'">
            View Advance Plan →
          </a>
        </div>

        <!-- Premium Plan -->
        <div style="padding:36px 32px; display:flex; flex-direction:column; gap:0;">
          <div style="font-size:.8rem; font-weight:600; letter-spacing:.08em; text-transform:uppercase; color:#807C70; margin-bottom:14px;">Premium</div>
          <div style="font-family:'Newsreader',serif; font-size:2.4rem; color:var(--vl-navy); line-height:1; margin-bottom:4px;">₹10 <span style="font-size:1rem; color:#807C70; font-family:'IBM Plex Sans',sans-serif;">Lakh</span></div>
          <div style="font-size:.85rem; color:#807C70; margin-bottom:24px;">one-time setup</div>
          <div style="font-size:.95rem; color:var(--vl-navy); font-weight:500; margin-bottom:20px; padding-bottom:20px; border-bottom:1px solid var(--vl-hair);">For large schools and groups — dual-room composite lab, full digital infrastructure, and dedicated support.</div>
          <ul style="list-style:none; padding:0; margin:0 0 28px; display:flex; flex-direction:column; gap:12px; flex:1;">
            @foreach([
              'Everything in Advance',
              'Two separate labs (400 sq ft each)',
            ] as $item)
            <li style="display:flex; align-items:flex-start; gap:10px; font-size:.9rem; color:#4B4A44;">
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none" style="flex-shrink:0; margin-top:2px;"><circle cx="8" cy="8" r="8" fill="#DEE9E4"/><path d="M5 8l2 2 4-4" stroke="#2F6F62" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
              {{ $item }}
            </li>
            @endforeach
          </ul>
          <a href="#"
             target="_blank"
             style="display:inline-flex; align-items:center; justify-content:center; border:1px solid var(--vl-navy); color:var(--vl-navy); padding:13px 20px; font-size:.9rem; font-weight:600; text-decoration:none; border-radius:2px; transition:background .2s; font-family:'IBM Plex Sans',sans-serif;"
             onmouseover="this.style.background='rgba(23,35,63,.06)'" onmouseout="this.style.background='transparent'">
            View Premium Plan →
          </a>
        </div>

      </div>

      {{-- Responsive mobile stack --}}
      <style>
        @media (max-width: 740px) {
          #plans > .vidyalab-wrap > div[style*="grid-template-columns"] {
            grid-template-columns: 1fr !important;
          }
          #plans > .vidyalab-wrap > div[style*="grid-template-columns"] > div {
            border-right: none !important;
            border-bottom: 1px solid var(--vl-hair);
          }
          #plans > .vidyalab-wrap > div[style*="grid-template-columns"] > div:last-child {
            border-bottom: none;
          }
        }
      </style>

      <p style="margin-top:20px; font-size:.82rem; color:#807C70; text-align:center;">
        All prices are indicative. Final quote provided after a free site assessment. &nbsp;·&nbsp;
        <a href="#checklist" style="color:var(--vl-marigold); text-decoration:none;">Contact us</a> for multi-school or trust pricing.
      </p>
    </div>
  </section>

  <!-- Testimonial Section -->
  <!-- <section class="vl-testimonial">
    <div class="vidyalab-wrap">
      <blockquote>"Our last STEM kit sat in a storeroom for two years. This one has a timetable slot every week, and our own teachers run it without calling anyone for help."</blockquote>
      <cite>— Principal, Sacred Heart CBSE Sr. Sec. School, Coimbatore</cite>
    </div>
  </section> -->

  <!-- Checklist / Lead Form Section -->
  <section class="vl-section" id="checklist">
    <div class="vidyalab-wrap vl-form-panel">
      <div class="vl-form-copy">
        <div class="vl-eyebrow-line"></div>
        <h2>Get the CBSE Skill Lab Compliance Checklist 2026</h2>
        <p style="margin-top:14px; color:#4B4A44;">A working document you can hand to your inspection committee or trustees — before you talk to any vendor, including us.</p>
        <ul>
          <li>What CBSE and NEP 2020 actually require of a skill lab</li>
          <li>Space, wiring, and safety specifications</li>
          <li>Questions to ask any vendor before signing</li>
          <li>A budget range by school size</li>
        </ul>
      </div>
      <form class="vl-form" id="leadForm">
        <input type="hidden" name="course_title" value="CBSE Composite Skill Lab – Checklist Enquiry">
        <input type="hidden" name="source" value="composite-skill-lab">
        <div class="vl-field">
          <label for="lead_name">Your name</label>
          <input id="lead_name" name="name" type="text" required>
        </div>
        <div class="vl-field">
          <label for="lead_designation">Designation</label>
          <input id="lead_designation" name="designation" type="text" required>
        </div>
        <div class="vl-field">
          <label for="lead_school">School name</label>
          <input id="lead_school" name="school" type="text" required>
        </div>
        <div class="vl-field">
          <label for="lead_city">City</label>
          <input id="lead_city" name="city" type="text" required>
        </div>
        <div class="vl-field">
          <label for="lead_address">Address</label>
          <textarea id="lead_address" name="address"></textarea>
        </div>
        <div class="vl-field">
          <label for="lead_phone">Phone number</label>
          <input id="lead_phone" name="phone" type="tel" required>
        </div>
        <div class="vl-field">
          <label for="lead_email">Email address</label>
          <input id="lead_email" name="email" type="email" required>
        </div>
        <button type="submit" id="leadSubmitBtn" class="vl-form-submit">Send me the checklist</button>
        <div class="vl-form-note">We'll also follow up once by phone. No spam, no mailing list.</div>
      </form>

      {{-- Success message outside form so it stays visible when form hides --}}
      <div class="vl-confirm" id="confirmMsg">
        <span class="vl-confirm-icon">✓</span>
        Thanks — your details have been received.<br>
        <span style="font-weight:400; font-size:.9rem; color:#4B4A44;">The checklist is on its way to your email. We'll follow up within one working day.</span>
      </div>
    </div>
  </section>

  <!-- Compliance Comparison Section -->
  <section class="vl-section" id="compliance" style="background:#EDEADF;">
    <div class="vidyalab-wrap">
      <div class="vl-section-head">
        <div class="vl-eyebrow-line"></div>
        <h2>How this compares to a generic setup</h2>
      </div>
      <div class="vl-table-wrap">
        <table class="vl-table">
          <thead>
            <tr>
              <th>&nbsp;</th>
              <th>Generic / DIY vendor</th>
              <th>Skillvation</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Curriculum alignment</td>
              <td class="no">Left to your teachers</td>
              <td class="yes">Mapped before installation</td>
            </tr>
            <tr>
              <td>Installation time</td>
              <td class="no">8–14 weeks, variable</td>
              <td class="yes">4–6 weeks, fixed schedule</td>
            </tr>
            <tr>
              <td>Teacher training</td>
              <td class="no">One orientation session</td>
              <td class="yes">Full certification, ongoing refreshers</td>
            </tr>
            <tr>
              <td>Maintenance &amp; support</td>
              <td class="no">Case by case, extra cost</td>
              <td class="yes">Included for contract term</td>
            </tr>
            <tr>
              <td>Compliance documentation</td>
              <td class="no">Not provided</td>
              <td class="yes">Prepared and handed over</td>
            </tr>
            <tr>
              <td>Pricing</td>
              <td class="no">Itemised, often revised upward</td>
              <td class="yes">Fixed quote before you sign</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <!-- FAQ Section -->
  <section class="vl-section" id="faq" style="background:#EDEADF;">
    <div class="vidyalab-wrap">
      <div class="vl-section-head">
        <div class="vl-eyebrow-line"></div>
        <h2>Questions school owners usually ask</h2>
        <p>Everything you need to know before signing anything.</p>
      </div>

      {{-- ── FAQ Card Carousel ────────────────────────────────── --}}
      <div class="vl-faq-carousel" id="faqCarousel">

        {{-- Slide 1 — cards 1, 2, 3 --}}
        <div class="vl-faq-slide active">
          <div class="vl-faq-grid">

            <div class="vl-faq-card">
              <div class="vl-faq-card__q">What is a Composite Skill Lab?</div>
              <div class="vl-faq-card__divider"></div>
              <div class="vl-faq-card__a">A Composite Skill Lab is a hands-on learning space where students learn practical skills through projects, activities, experiments and real-life applications.</div>
            </div>

            <div class="vl-faq-card">
              <div class="vl-faq-card__q">Is a Composite Skill Lab required for CBSE schools?</div>
              <div class="vl-faq-card__divider"></div>
              <div class="vl-faq-card__a">CBSE has provided guidelines for establishing Composite Skill Labs to support experiential Skill Education. Skillvation helps schools set up labs aligned with these guidelines.</div>
            </div>

            <div class="vl-faq-card">
              <div class="vl-faq-card__q">What lab options does Skillvation offer?</div>
              <div class="vl-faq-card__divider"></div>
              <div class="vl-faq-card__a">
                <ul>
                  <li>Standard – Essential infrastructure, furniture, tools and materials.</li>
                  <li>Advanced – Standard package with additional equipment, tools and project resources</li>
                  <li>Premium – Comprehensive lab with advanced equipment, extensive project kits and enhanced learning resources.</li>
                </ul>
              </div>
            </div>

          </div>
        </div>

        {{-- Slide 2 — cards 4, 5, 6 --}}
        <div class="vl-faq-slide">
          <div class="vl-faq-grid">

            <div class="vl-faq-card">
              <div class="vl-faq-card__q">How do I choose the right lab?</div>
              <div class="vl-faq-card__divider"></div>
              <div class="vl-faq-card__a">The right package depends on your school's budget, student strength, available space and learning requirements. Our team can help you select the most suitable option.</div>
            </div>

            <div class="vl-faq-card">
              <div class="vl-faq-card__q">Can the lab be customised?</div>
              <div class="vl-faq-card__divider"></div>
              <div class="vl-faq-card__a">Yes. We can customise the lab based on your space, student strength, selected skill areas and existing infrastructure.</div>
            </div>

            <div class="vl-faq-card">
              <div class="vl-faq-card__q">What does Skillvation provide?</div>
              <div class="vl-faq-card__divider"></div>
              <div class="vl-faq-card__a">Our solutions can include furniture, tools, equipment, safety resources, consumables, teacher demonstration kits, student project kits and learning materials.</div>
            </div>

          </div>
        </div>

        {{-- Slide 3 — cards 7, 8, 9 --}}
        <div class="vl-faq-slide">
          <div class="vl-faq-grid">

            <div class="vl-faq-card">
              <div class="vl-faq-card__q">Do you provide student project kits?</div>
              <div class="vl-faq-card__divider"></div>
              <div class="vl-faq-card__a">Yes. We provide ready-to-use DIY project kits with materials and easy-to-follow instructions for hands-on activities.</div>
            </div>

            <div class="vl-faq-card">
              <div class="vl-faq-card__q">Do you provide teacher training?</div>
              <div class="vl-faq-card__divider"></div>
              <div class="vl-faq-card__a">Yes. Teacher orientation and training can be provided to help teachers effectively use the lab and conduct practical activities.</div>
            </div>

            <div class="vl-faq-card">
              <div class="vl-faq-card__q">Can we upgrade the lab later?</div>
              <div class="vl-faq-card__divider"></div>
              <div class="vl-faq-card__a">Yes. Our modular approach allows schools to start with Standard and upgrade to Advanced or Premium as their requirements grow.</div>
            </div>

          </div>
        </div>

        {{-- Slide 4 — cards 10, 11 --}}
        <div class="vl-faq-slide">
          <div class="vl-faq-grid">

            <div class="vl-faq-card">
              <div class="vl-faq-card__q">What happens after installation — are we on our own?</div>
              <div class="vl-faq-card__divider"></div>
              <div class="vl-faq-card__a">No. Annual maintenance, software updates, and a direct support line are included for the contract term, and we check in with your faculty every term.</div>
            </div>

            <div class="vl-faq-card">
              <div class="vl-faq-card__q">Why choose Skillvation?</div>
              <div class="vl-faq-card__divider"></div>
              <div class="vl-faq-card__a">Skillvation provides more than a physical lab. We bring together Infrastructure + Equipment + Project Kits + Teacher Support + Experiential Learning to create a complete skill-learning environment.</div>
            </div>

          </div>
        </div>


        {{-- Navigation --}}
        <div class="vl-faq-nav">
          <button class="vl-faq-btn" id="faqPrev" aria-label="Previous questions" disabled>
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M11 13L7 9l4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
          <div class="vl-faq-dots" id="faqDots">
            <button class="vl-faq-dot active" data-slide="0" aria-label="Slide 1"></button>
            <button class="vl-faq-dot" data-slide="1" aria-label="Slide 2"></button>
            <button class="vl-faq-dot" data-slide="2" aria-label="Slide 3"></button>
            <button class="vl-faq-dot" data-slide="3" aria-label="Slide 4"></button>
          </div>
          <span class="vl-faq-counter" id="faqCounter">1 / 4</span>
          <button class="vl-faq-btn" id="faqNext" aria-label="Next questions">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M7 5l4 4-4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
        </div>

      </div>
      {{-- ── End FAQ Card Carousel ────────────────────────────── --}}

    </div>
  </section>

  <!-- Final CTA Section -->
  <section class="vl-final-cta" id="book">
    <div class="vidyalab-wrap">
      <h2>Ready to see what a working lab looks like at your school?</h2>
      <p>A 15-minute call with our education consultant — no obligation, no sales pitch, just a straight answer on fit and cost.</p>
      <div class="vl-hero-ctas">
        <a href="#checklist" class="vl-btn-primary">Visit our experience center</a>
      </div>
      <p style="margin-top:18px; font-size:0.85rem; color:#9B9688;">We respond within one working day.</p>
    </div>
  </section>

</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {

    // ── FAQ Card Carousel ─────────────────────────────────────
    const slides   = document.querySelectorAll('.vl-faq-slide');
    const dots     = document.querySelectorAll('.vl-faq-dot');
    const prevBtn  = document.getElementById('faqPrev');
    const nextBtn  = document.getElementById('faqNext');
    const counter  = document.getElementById('faqCounter');
    const total    = slides.length;
    let current    = 0;

    function goTo(idx) {
      slides[current].classList.remove('active');
      dots[current].classList.remove('active');

      current = (idx + total) % total;

      slides[current].classList.add('active');
      dots[current].classList.add('active');

      if (counter) counter.textContent = (current + 1) + ' / ' + total;
      prevBtn.disabled = current === 0;
      nextBtn.disabled = current === total - 1;
    }

    // init state
    if (prevBtn) prevBtn.disabled = true;
    if (nextBtn) nextBtn.disabled = total <= 1;

    if (prevBtn) prevBtn.addEventListener('click', () => goTo(current - 1));
    if (nextBtn) nextBtn.addEventListener('click', () => goTo(current + 1));
    dots.forEach((dot, i) => dot.addEventListener('click', () => goTo(i)));

    // ── Lead Form ─────────────────────────────────────────────
    const leadForm = document.getElementById('leadForm');
    if (leadForm) {
      leadForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const btn        = document.getElementById('leadSubmitBtn');
        const confirmMsg = document.getElementById('confirmMsg');
        const formData   = new FormData(this);

        btn.disabled    = true;
        btn.textContent = 'Sending…';

        fetch('{{ route("course.enquiry.store") }}', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'Accept': 'application/json',
          },
          body: formData,
        })
        .then(res => res.json())
        .then(data => {
          leadForm.reset();
          leadForm.style.display = 'none';
          if (confirmMsg) {
            confirmMsg.style.display = 'block';
            confirmMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
        })
        .catch(() => {
          leadForm.reset();
          leadForm.style.display = 'none';
          if (confirmMsg) {
            confirmMsg.style.display = 'block';
            confirmMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
        })
        .finally(() => {
          btn.disabled    = false;
          btn.textContent = 'Send me the checklist';
        });
      });
    }
  });
</script>
@endpush