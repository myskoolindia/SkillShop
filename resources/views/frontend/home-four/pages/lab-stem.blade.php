@extends('frontend.home-four.layouts.master')

@section('meta_title', 'Skillvation Lab — Building Schools That Prepare Students for the Future')
@section('meta_description', 'Skillvation Lab helps schools create structured, practical learning environments covering Robotics, AI, STEM, Electronics, Design Thinking, and more. Complete turnkey lab solution for ICSE and State Board schools.')

@push('styles')
<style>
  /* Kit section styles */
  #skill-sectors-columns { display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-start; }
  #skill-sectors-columns > div.hidden { display: none !important; }
  #skill-sectors-columns > div:not(.hidden):only-child { flex: 0 0 100% !important; }
  .skill-acc-row button:focus-visible { outline: 2px solid #3b82f6; outline-offset: -2px; }

  /* Kit Carousel styles */
  .sks-nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #1e293b;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 14px rgba(0,0,0,0.12);
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .sks-nav-btn:hover {
    background: #ffffff;
    color: #f4742b;
    border-color: #fdba74;
    box-shadow: 0 8px 24px rgba(244,116,43,0.22);
    transform: translateY(-50%) scale(1.08);
  }
  .sks-nav-btn:active {
    transform: translateY(-50%) scale(0.96);
  }
  .sks-nav-prev { left: -22px; }
  .sks-nav-next { right: -22px; }
  @media (max-width: 768px) {
    .sks-nav-prev { left: 4px; }
    .sks-nav-next { right: 4px; }
    .sks-nav-btn {
      width: 38px;
      height: 38px;
      background: rgba(255,255,255,0.92);
      backdrop-filter: blur(6px);
      box-shadow: 0 2px 10px rgba(0,0,0,0.15);
    }
  }
  .sks-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #cbd5e1;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .sks-dot.active {
    width: 26px;
    border-radius: 13px;
    background: #f4742b;
  }
  .sks-box-card {
    flex-shrink: 0;
    box-sizing: border-box;
    width: 100%;
  }
  /* ===== Custom Package Advisor styles (ported from Composite Skill Lab) ===== */
  :root {
    --vl-primary: #6366f1;
    --vl-primary-deep: #4338ca;
    --vl-heading: #0f172a;
    --vl-body: #334155;
    --vl-muted: #64748b;
    
    --vl-pink: #ec4899;
    --vl-rose: #f43f5e;
    --vl-coral: #f97316;
    --vl-amber: #f59e0b;
    --vl-violet: #8b5cf6;
    --vl-teal: #0d9488;
    --vl-cyan: #0284c7;

    --vl-bg-white: #ffffff;
    --vl-bg-soft: #fbf9fd;
    --vl-bg-peach: #fffaf5;
    --vl-bg-mint: #f4fbf9;
    --vl-bg-lavender: #f7f4fc;

    --vl-shadow-card: 0 10px 30px -5px rgba(99, 102, 241, 0.08), 0 4px 12px -2px rgba(236, 72, 153, 0.05);
    --vl-shadow-hover: 0 20px 40px -8px rgba(99, 102, 241, 0.16), 0 10px 20px -4px rgba(236, 72, 153, 0.12);
    --vl-shadow-featured: 0 25px 50px -12px rgba(99, 102, 241, 0.35);
  }

  .vidyalab-page {
    background-color: var(--vl-bg-white);
    color: var(--vl-body);
    font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
    font-size: 16px;
    line-height: 1.65;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
  }

  /* Typography Scale */
  .vidyalab-page h1,
  .vidyalab-page h2,
  .vidyalab-page h3,
  .vidyalab-page .display-heading {
    font-family: 'Outfit', 'Plus Jakarta Sans', sans-serif;
    font-weight: 800;
    color: var(--vl-heading);
    line-height: 1.14;
    letter-spacing: -0.03em;
    margin: 0;
  }

  .vidyalab-wrap {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px;
  }

  /* Gradient Text Fills (WOW effect) */
  .vl-grad-text {
    background: linear-gradient(135deg, #0284c7 0%, #6366f1 35%, #ec4899 75%, #f97316 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    display: inline;
  }

  .vl-grad-sunset {
    background: linear-gradient(135deg, #f43f5e 0%, #ec4899 50%, #f97316 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    display: inline;
  }

  .vl-grad-violet {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #db2777 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    display: inline;
  }

  .vl-grad-gold {
    background: linear-gradient(135deg, #ffffff 0%, #fef08a 60%, #fed7aa 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    display: inline;
  }

  /* Eyebrows & Subheadings */
  .vl-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-family: 'Outfit', sans-serif;
    font-size: 0.95rem;
    font-weight: 700;
    letter-spacing: -0.01em;
    color: #e11d48;
    margin-bottom: 12px;
  }

  .vl-eyebrow.purple { color: #7c3aed; }
  .vl-eyebrow.coral  { color: #ea580c; }
  .vl-eyebrow.teal   { color: #0d9488; }
  .vl-eyebrow.hero   { color: #fef08a; }

  .vl-badge-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 16px;
    border-radius: 9999px;
    font-family: 'Outfit', sans-serif;
    font-size: 0.82rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
  }

  .vl-badge-pill.hero {
    background: rgba(255, 255, 255, 0.2);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.4);
    backdrop-filter: blur(8px);
  }

  /* Buttons */
  .vl-btn-vibrant-cta {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    background: linear-gradient(135deg, #ffffff 0%, #ffedd5 100%);
    color: #db2777 !important;
    border: none;
    padding: 16px 20px;
    font-family: 'Outfit', sans-serif;
    font-size: 1.05rem;
    font-weight: 800;
    border-radius: 14px;
    text-decoration: none;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15), 0 4px 10px rgba(219, 39, 119, 0.2);
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  }

  .vl-btn-vibrant-cta:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2), 0 6px 16px rgba(219, 39, 119, 0.3);
    color: #be185d !important;
  }

  .vl-btn-glass-cta {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    background: rgba(255, 255, 255, 0.16);
    color: #ffffff !important;
    border: 1.5px solid rgba(255, 255, 255, 0.45);
    padding: 16px 30px;
    font-family: 'Outfit', sans-serif;
    font-size: 1.05rem;
    font-weight: 700;
    border-radius: 14px;
    text-decoration: none;
    backdrop-filter: blur(10px);
    transition: all 0.25s ease;
  }

  .vl-btn-glass-cta:hover {
    background: rgba(255, 255, 255, 0.28);
    border-color: #ffffff;
    transform: translateY(-3px);
  }

  /* ==================== HERO SECTION ==================== */
  .vl-hero-vibrant {
    position: relative;
    background: linear-gradient(135deg, #4338ca 0%, #6366f1 30%, #a855f7 65%, #ec4899 100%);
    color: #ffffff;
    padding: 76px 0 90px;
    overflow: hidden;
  }

  .vl-hero-vibrant::before {
    content: "";
    position: absolute;
    top: -100px;
    right: -60px;
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, rgba(254, 240, 138, 0.3) 0%, rgba(244, 114, 182, 0.2) 45%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
  }

  .vl-hero-vibrant::after {
    content: "";
    position: absolute;
    bottom: -120px;
    left: -80px;
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(56, 189, 248, 0.25) 0%, transparent 65%);
    border-radius: 50%;
    pointer-events: none;
  }

  .vl-hero-grid {
    position: relative;
    z-index: 2;
    display: grid;
    grid-template-columns: 1.15fr 0.85fr;
    gap: 52px;
    /* align-items: center; */
  }

  @media (max-width: 980px) {
    .vl-hero-grid {
      grid-template-columns: 1fr;
      gap: 40px;
    }
  }

  .vl-hero-vibrant h1 {
    font-size: clamp(2.5rem, 4.8vw, 3.85rem);
    color: #ffffff;
    line-height: 1.1;
    margin-top: 14px;
    letter-spacing: -0.035em;
  }

  .vl-hero-vibrant p.lead {
    margin-top: 22px;
    font-size: 1.18rem;
    max-width: 50ch;
    color: #f8fafc;
    line-height: 1.7;
    font-weight: 500;
  }

  .vl-hero-ctas {
    margin-top: 36px;
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
  }

  /* Hero Stat Blocks */
  .vl-hero-stats {
    margin-top: 48px;
    display: grid;
    /* grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); */
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    border-top: 1px solid rgba(255, 255, 255, 0.25);
    padding-top: 30px;
  }

  .vl-stat-box {
    background: rgba(255, 255, 255, 0.14);
    border: 1px solid rgba(255, 255, 255, 0.28);
    border-radius: 14px;
    padding: 14px 18px;
    backdrop-filter: blur(8px);
  }

  .vl-stat-box .num {
    font-family: 'Outfit', sans-serif;
    font-size: 2rem;
    font-weight: 900;
    color: #fef08a;
    display: block;
    line-height: 1.05;
  }

  .vl-stat-box .label {
    font-size: 0.82rem;
    color: #ffffff;
    font-weight: 600;
    margin-top: 5px;
    display: block;
  }

  /* Floor Plan Card */
  .vl-blueprint-glass {
    background: rgba(255, 255, 255, 0.96);
    border: 2px solid rgba(255, 255, 255, 0.9);
    border-radius: 22px;
    padding: 26px;
    box-shadow: 0 24px 50px -10px rgba(76, 29, 149, 0.35), 0 10px 24px rgba(0, 0, 0, 0.08);
  }

  .vl-blueprint-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1.5px solid #ede9fe;
  }

  .vl-blueprint-title {
    font-family: 'Outfit', sans-serif;
    font-size: 1.05rem;
    font-weight: 800;
    color: #4338ca;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .vl-blueprint-badge {
    font-family: 'Outfit', sans-serif;
    font-size: 0.76rem;
    padding: 5px 14px;
    background: #fdf2f8;
    color: #db2777;
    border: 1px solid #fbcfe8;
    border-radius: 20px;
    font-weight: 800;
  }

  .vl-blueprint-svg-wrap {
    background: #faf5ff;
    border: 1.5px solid #e9d5ff;
    border-radius: 14px;
    padding: 12px;
  }

  .vl-blueprint-svg-wrap svg {
    width: 100%;
    height: auto;
    display: block;
  }

  .vl-blueprint-cap {
    margin-top: 14px;
    font-size: 0.86rem;
    color: #6d28d9;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  /* ==================== SECTIONS & HEADINGS ==================== */
  .vl-section {
    padding: 92px 0;
    position: relative;
  }

  .vl-section.bg-soft     { background-color: var(--vl-bg-soft); }
  .vl-section.bg-peach    { background-color: var(--vl-bg-peach); }
  .vl-section.bg-mint     { background-color: var(--vl-bg-mint); }
  .vl-section.bg-lavender { background-color: var(--vl-bg-lavender); }
  .vl-section.bg-white    { background-color: var(--vl-bg-white); }

  .vl-section-head {
    max-width: 720px;
    margin-bottom: 52px;
  }

  .vl-section-head.center {
    margin-left: auto;
    margin-right: auto;
    text-align: center;
  }

  .vl-section-head h2 {
    font-size: clamp(2.1rem, 3.8vw, 3rem);
    color: var(--vl-heading);
    margin-top: 8px;
  }

  .vl-section-head p {
    margin-top: 16px;
    color: var(--vl-body);
    font-size: 1.14rem;
    line-height: 1.68;
    font-weight: 500;
  }

  /* ==================== OUTCOMES ==================== */
  .vl-outcomes-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 36px;
  }

  @media (max-width: 840px) {
    .vl-outcomes-grid {
      grid-template-columns: 1fr;
      gap: 28px;
    }
  }

  .vl-outcome-card {
    background: #ffffff;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: var(--vl-shadow-card);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    display: flex;
    flex-direction: column;
  }

  .vl-outcome-card:hover {
    transform: translateY(-6px);
    box-shadow: var(--vl-shadow-hover);
  }

  .vl-outcome-card.problem {
    border: 2px solid #fed7aa;
    border-top: 6px solid #f97316;
  }

  .vl-outcome-card.solution {
    border: 2px solid #ddd6fe;
    border-top: 6px solid #8b5cf6;
    box-shadow: 0 16px 36px -4px rgba(139, 92, 246, 0.18), var(--vl-shadow-card);
  }

  .vl-outcome-img {
    width: 100%;
    aspect-ratio: 16 / 9;
    overflow: hidden;
    position: relative;
    background: #fdf2f8;
  }

  .vl-outcome-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.5s ease;
  }

  .vl-outcome-card:hover .vl-outcome-img img {
    transform: scale(1.05);
  }

  .vl-outcome-content {
    padding: 34px;
    flex: 1;
    display: flex;
    flex-direction: column;
  }

  .vl-outcome-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-family: 'Outfit', sans-serif;
    font-size: 0.85rem;
    font-weight: 800;
    padding: 6px 14px;
    border-radius: 8px;
    margin-bottom: 14px;
    width: fit-content;
    text-transform: uppercase;
    letter-spacing: 0.04em;
  }

  .vl-outcome-tag.warn {
    background: #fff7ed;
    color: #ea580c;
    border: 1px solid #ffedd5;
  }

  .vl-outcome-tag.good {
    background: #fdf2f8;
    color: #db2777;
    border: 1px solid #fbcfe8;
  }

  .vl-outcome-card h3 {
    font-size: 1.55rem;
    font-weight: 800;
    color: var(--vl-heading);
    margin-bottom: 12px;
  }

  .vl-outcome-card p {
    color: var(--vl-body);
    font-size: 1.02rem;
    line-height: 1.68;
    margin: 0 0 22px;
  }

  .vl-outcome-features {
    list-style: none;
    padding: 0;
    margin: auto 0 0 0;
    display: flex;
    flex-direction: column;
    gap: 12px;
    border-top: 1.5px solid #f1f5f9;
    padding-top: 22px;
  }

  .vl-outcome-features li {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    font-size: 0.96rem;
    font-weight: 600;
  }

  .vl-outcome-card.problem .vl-outcome-features li { color: #c2410c; }
  .vl-outcome-card.solution .vl-outcome-features li { color: #6d28d9; }

  /* ==================== WHAT'S INCLUDED ==================== */
  .vl-features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 30px;
  }

  @media (max-width: 640px) {
    .vl-features-grid {
      grid-template-columns: 1fr;
    }
  }

  .vl-feature-card {
    background: #ffffff;
    border: 2px solid #f1f5f9;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: var(--vl-shadow-card);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    display: flex;
    flex-direction: column;
  }

  .vl-feature-card:hover {
    transform: translateY(-6px);
    box-shadow: var(--vl-shadow-hover);
    border-color: #ddd6fe;
  }

  .vl-feature-card-img {
    width: 100%;
    height: 200px;
    overflow: hidden;
    background: #fdf2f8;
    position: relative;
  }

  .vl-feature-card-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.4s ease;
  }

  .vl-feature-card:hover .vl-feature-card-img img {
    transform: scale(1.06);
  }

  .vl-feature-card-body {
    padding: 28px;
    flex: 1;
    display: flex;
    flex-direction: column;
  }

  .vl-feature-pill {
    font-family: 'Outfit', sans-serif;
    font-size: 0.78rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    padding: 5px 12px;
    border-radius: 6px;
    width: fit-content;
    margin-bottom: 12px;
  }

  .vl-feature-pill.f1 { background: #ede9fe; color: #6d28d9; }
  .vl-feature-pill.f2 { background: #fdf2f8; color: #db2777; }
  .vl-feature-pill.f3 { background: #fff7ed; color: #ea580c; }
  .vl-feature-pill.f4 { background: #f0fdfa; color: #0d9488; }
  .vl-feature-pill.f5 { background: #fef3c7; color: #d97706; }

  .vl-feature-card h3 {
    font-size: 1.35rem;
    font-weight: 800;
    color: var(--vl-heading);
    margin-bottom: 10px;
  }

  .vl-feature-card p {
    color: var(--vl-body);
    font-size: 0.98rem;
    line-height: 1.65;
    margin: 0;
  }

  /* ==================== PRICING PLANS ==================== */
  .vl-plans-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    align-items: stretch;
  }

  @media (max-width: 960px) {
    .vl-plans-grid {
      grid-template-columns: 1fr;
      max-width: 500px;
      margin: 0 auto;
    }
  }

  .vl-plan-card {
    background: #ffffff;
    border: 2px solid #ede9fe;
    border-radius: 24px;
    padding: 40px 32px;
    box-shadow: var(--vl-shadow-card);
    display: flex;
    flex-direction: column;
    position: relative;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  }

  .vl-plan-card:hover {
    transform: translateY(-7px);
    box-shadow: var(--vl-shadow-hover);
  }

  .vl-plan-card.featured {
    background: linear-gradient(160deg, #4338ca 0%, #6366f1 40%, #a855f7 78%, #ec4899 100%);
    color: #ffffff;
    border: 2px solid #ffffff;
    box-shadow: var(--vl-shadow-featured);
  }

  .vl-plan-badge-top {
    position: absolute;
    top: -16px;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(135deg, #fef08a 0%, #facc15 100%);
    color: #713f12;
    font-family: 'Outfit', sans-serif;
    font-size: 0.82rem;
    font-weight: 900;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    padding: 7px 22px;
    border-radius: 20px;
    box-shadow: 0 4px 14px rgba(234, 179, 8, 0.45);
    white-space: nowrap;
  }

  .vl-plan-tier {
    font-family: 'Outfit', sans-serif;
    font-size: 0.92rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #7c3aed;
    margin-bottom: 12px;
  }

  .vl-plan-card.featured .vl-plan-tier { color: #fef08a; }

  .vl-plan-price {
    font-family: 'Outfit', sans-serif;
    font-size: 3.2rem;
    font-weight: 900;
    color: var(--vl-heading);
    line-height: 1;
    margin-bottom: 4px;
    display: flex;
    align-items: baseline;
    gap: 6px;
  }

  .vl-plan-card.featured .vl-plan-price { color: #ffffff; }

  .vl-plan-price span {
    font-size: 1.25rem;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 600;
    color: var(--vl-muted);
  }

  .vl-plan-card.featured .vl-plan-price span { color: #f1f5f9; }

  .vl-plan-desc {
    font-size: 0.92rem;
    color: var(--vl-muted);
    font-weight: 600;
    margin-bottom: 24px;
  }

  .vl-plan-card.featured .vl-plan-desc { color: #e2e8f0; }

  .vl-plan-summary {
    font-size: 1rem;
    font-weight: 700;
    color: var(--vl-heading);
    padding-bottom: 22px;
    margin-bottom: 22px;
    border-bottom: 1.5px solid #f1f5f9;
    line-height: 1.55;
  }

  .vl-plan-card.featured .vl-plan-summary {
    color: #ffffff;
    border-bottom: 1.5px solid rgba(255, 255, 255, 0.25);
  }

  .vl-plan-list {
    list-style: none;
    padding: 0;
    margin: 0 0 34px;
    display: flex;
    flex-direction: column;
    gap: 15px;
    flex: 1;
  }

  .vl-plan-list li {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    font-size: 0.98rem;
    color: var(--vl-body);
    font-weight: 600;
  }

  .vl-plan-card.featured .vl-plan-list li { color: #ffffff; }

  .vl-plan-check-icon {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #ede9fe;
    color: #7c3aed;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: 900;
    flex-shrink: 0;
    margin-top: 2px;
  }

  .vl-plan-card.featured .vl-plan-check-icon {
    background: rgba(255, 255, 255, 0.25);
    color: #fef08a;
  }

  .vl-plan-btn-outline {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: 15px;
    font-family: 'Outfit', sans-serif;
    font-size: 1rem;
    font-weight: 800;
    border-radius: 12px;
    text-decoration: none;
    background: #ffffff;
    color: #6d28d9;
    border: 2px solid #ddd6fe;
    transition: all 0.25s ease;
  }

  .vl-plan-btn-outline:hover {
    background: #f5f3ff;
    border-color: #8b5cf6;
    transform: translateY(-2px);
  }

  .vl-plan-btn-featured {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: 16px;
    font-family: 'Outfit', sans-serif;
    font-size: 1.05rem;
    font-weight: 900;
    border-radius: 12px;
    text-decoration: none;
    background: linear-gradient(135deg, #ffffff 0%, #ffedd5 100%);
    color: #db2777 !important;
    border: none;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
    transition: all 0.25s ease;
  }

  .vl-plan-btn-featured:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
  }

  /* ==================== CUSTOM PACKAGE ADVISOR ==================== */
  .vl-pkg-toggle-wrapper {
    display: flex;
    justify-content: center;
    margin: 32px 0 36px;
  }
  .vl-pkg-toggle-pill {
    display: inline-flex;
    background: #f1f5f9;
    padding: 6px;
    border-radius: 9999px;
    border: 1.5px solid #e2e8f0;
    gap: 6px;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.04);
  }
  .vl-pkg-tab-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 26px;
    border-radius: 9999px;
    font-family: 'Outfit', sans-serif;
    font-size: 0.95rem;
    font-weight: 700;
    color: #475569;
    background: transparent;
    border: none;
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .vl-pkg-tab-btn:hover {
    color: #1e1b4b;
  }
  .vl-pkg-tab-btn.active {
    background: #ffffff;
    color: #6d28d9;
    box-shadow: 0 4px 14px rgba(99, 102, 241, 0.15), 0 2px 4px rgba(0,0,0,0.04);
  }
  .vl-sparkle-pill {
    font-size: 0.72rem;
    padding: 3px 8px;
    border-radius: 9999px;
    background: linear-gradient(135deg, #f97316 0%, #ec4899 100%);
    color: #ffffff;
    font-weight: 800;
    letter-spacing: 0.04em;
    text-transform: uppercase;
  }
  .vl-custom-advisor-banner {
    margin-top: 36px;
    background: linear-gradient(135deg, #ffffff 0%, #f5f3ff 100%);
    border: 2px dashed #c4b5fd;
    border-radius: 20px;
    padding: 28px 32px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    box-shadow: 0 8px 24px rgba(99, 102, 241, 0.06);
  }
  @media (max-width: 820px) {
    .vl-custom-advisor-banner {
      flex-direction: column;
      text-align: center;
      padding: 24px 20px;
    }
  }
  .vl-cab-content h3 {
    font-size: 1.25rem;
    font-weight: 800;
    color: #1e1b4b;
    margin: 6px 0 8px;
  }
  .vl-cab-content p {
    font-size: 0.95rem;
    color: #475569;
    margin: 0;
    max-width: 65ch;
  }
  .vl-cab-badge {
    font-family: 'Outfit', sans-serif;
    font-size: 0.78rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #7c3aed;
  }
  .vl-cab-cta-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
    color: #ffffff !important;
    padding: 14px 24px;
    border-radius: 12px;
    font-family: 'Outfit', sans-serif;
    font-size: 0.98rem;
    font-weight: 800;
    border: none;
    cursor: pointer;
    white-space: nowrap;
    box-shadow: 0 6px 18px rgba(99, 102, 241, 0.35);
    transition: all 0.25s ease;
  }
  .vl-cab-cta-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(99, 102, 241, 0.45);
  }

  /* Custom Package Configurator Container */
  .vl-custom-pkg-builder {
    display: none;
    animation: vl-fade-in 0.35s ease;
  }
  .vl-custom-pkg-builder.active {
    display: block;
  }
  /* ==================== EDUCATOR-FIRST CAMPUS PROFILE BAR ==================== */
  .vl-edu-campus-bar {
    background: #ffffff;
    border: 2px solid #ede9fe;
    border-radius: 20px;
    padding: 20px 24px;
    margin-bottom: 24px;
    box-shadow: 0 4px 18px rgba(99, 102, 241, 0.06);
  }
  .vl-edu-bar-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 20px;
    flex-wrap: wrap;
  }
  .vl-edu-kpi-pill {
    background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
    border: 1.5px solid #c7d2fe;
    border-radius: 14px;
    padding: 8px 18px;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    flex-shrink: 0;
  }
  .vl-edu-params-grid {
    display: grid;
    grid-template-columns: 1.15fr 1fr 1fr;
    gap: 20px;
  }
  @media (max-width: 960px) {
    .vl-edu-params-grid {
      grid-template-columns: 1fr 1fr;
      gap: 14px;
    }
  }
  @media (max-width: 600px) {
    .vl-edu-campus-bar {
      padding: 16px;
    }
    .vl-edu-bar-header {
      flex-direction: column;
      align-items: stretch;
    }
    .vl-edu-kpi-pill {
      align-items: center;
      flex-direction: row;
      justify-content: space-between;
      padding: 10px 14px;
    }
    .vl-edu-params-grid {
      grid-template-columns: 1fr;
      gap: 12px;
    }
  }
  .vl-edu-param-col {
    background: #fbf9fd;
    border: 1.5px solid #f1f5f9;
    border-radius: 14px;
    padding: 14px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }
  .vl-edu-param-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-family: 'Outfit', sans-serif;
    font-size: 0.88rem;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 10px;
    flex-wrap: wrap;
  }
  .vl-edu-tier-btns {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 10px;
  }
  .vl-edu-tier-btn, .vl-edu-chip {
    flex: 1;
    min-width: 60px;
    padding: 8px 6px;
    border-radius: 10px;
    border: 1.5px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    font-family: 'Outfit', sans-serif;
    font-size: 0.8rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
    text-align: center;
    white-space: nowrap;
  }
  .vl-edu-tier-btn:hover, .vl-edu-chip:hover {
    border-color: #cbd5e1;
    color: #1e1b4b;
  }
  .vl-edu-tier-btn.active, .vl-edu-chip.active {
    background: #6366f1;
    color: #ffffff;
    border-color: #6366f1;
    box-shadow: 0 2px 8px rgba(99, 102, 241, 0.25);
  }
  .vl-mini-star {
    font-size: 0.68rem;
    color: #fef08a;
    font-weight: 800;
  }
  .vl-edu-stat-duo {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin-bottom: 10px;
  }
  .vl-edu-stat-item {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 8px 10px;
    text-align: center;
  }
  .vl-edu-stat-num {
    font-family: 'Outfit', sans-serif;
    font-size: 0.95rem;
    font-weight: 900;
    color: #1e1b4b;
    line-height: 1.3;
  }
  .vl-edu-stat-sub {
    font-size: 0.65rem;
    color: #64748b;
    font-weight: 600;
    margin-top: 2px;
  }
  .vl-edu-grade-row {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: auto;
    padding-top: 6px;
  }
  .vl-edu-mini-pill {
    padding: 4px 10px;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #64748b;
    font-size: 0.74rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s;
  }
  .vl-edu-mini-pill.active {
    background: #f3e8ff;
    border-color: #c084fc;
    color: #7e22ce;
  }
  .vl-ps-grade-badge {
    font-size: 0.68rem;
    font-weight: 800;
    padding: 2px 7px;
    border-radius: 4px;
    background: #f0fdf4;
    color: #166534;
    border: 1px solid #bbf7d0;
  }
  .vl-ps-capacity-tag {
    font-size: 0.72rem;
    color: #64748b;
    font-weight: 600;
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 4px;
  }

  /* Slider Controls */
  .vl-cp-slider-wrap {
    position: relative;
    padding: 6px 0;
  }
  .vl-cp-slider {
    -webkit-appearance: none;
    appearance: none;
    width: 100%;
    height: 8px;
    border-radius: 5px;
    background: #e2e8f0;
    outline: none;
    transition: background 0.2s;
  }
  .vl-cp-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: linear-gradient(135deg, #6366f1, #a855f7);
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0,0,0,0.25);
    border: 2.5px solid #ffffff;
    transition: transform 0.15s ease;
  }
  .vl-cp-slider::-webkit-slider-thumb:hover {
    transform: scale(1.15);
  }
  .vl-cp-slider-ticks {
    display: flex;
    justify-content: space-between;
    font-size: 0.76rem;
    color: #94a3b8;
    margin-top: 6px;
    font-weight: 600;
  }

  /* Custom Proposal Modal Styles */
  .custom-pkg-modal-backdrop {
    position: fixed;
    inset: 0;
    z-index: 10000;
    background: rgba(15, 23, 42, 0.72);
    backdrop-filter: blur(6px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    animation: cbFadeIn .25s ease;
  }
  .custom-pkg-modal-box {
    position: relative;
    background: #ffffff;
    border-radius: 22px;
    width: 100%;
    max-width: 720px;
    max-height: 92vh;
    overflow-y: auto;
    padding: 38px 40px;
    box-shadow: 0 24px 64px rgba(0,0,0,.3);
    animation: cbSlideUp .3s cubic-bezier(.22,1,.36,1);
  }
  .custom-pkg-summary-pill {
    background: #faf5ff;
    border: 1.5px solid #ddd6fe;
    border-radius: 14px;
    padding: 14px 18px;
    margin-bottom: 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
  }
  /* ==================== PRODUCT SELECTION & INTELLIGENCE ENGINE ==================== */
  .vl-ps-section {
    margin-top: 40px;
    background: #ffffff;
    border: 2px solid #ede9fe;
    border-radius: 24px;
    padding: 34px 30px;
    box-shadow: var(--vl-shadow-card);
  }
  .vl-ps-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
    margin-bottom: 24px;
    flex-wrap: wrap;
  }
  .vl-ps-title-wrap h3 {
    font-size: 1.45rem;
    font-weight: 800;
    color: #1e1b4b;
    margin: 4px 0 6px;
  }
  .vl-ps-title-wrap p {
    font-size: 0.94rem;
    color: #64748b;
    margin: 0;
  }
  .vl-ps-header-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
  }
  .vl-ps-cat-suggest-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 18px;
    border-radius: 12px;
    background: #ffffff;
    border: 2px solid #6366f1;
    color: #4338ca !important;
    font-family: 'Outfit', sans-serif;
    font-size: 0.92rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
    box-shadow: 0 2px 8px rgba(99, 102, 241, 0.12);
  }
  .vl-ps-cat-suggest-btn:hover {
    background: #ede9fe;
    transform: translateY(-2px);
    box-shadow: 0 4px 14px rgba(99, 102, 241, 0.2);
  }
  .vl-ps-auto-suggest-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 22px;
    border-radius: 12px;
    background: linear-gradient(135deg, #f97316 0%, #ec4899 50%, #8b5cf6 100%);
    color: #ffffff !important;
    font-family: 'Outfit', sans-serif;
    font-size: 0.95rem;
    font-weight: 800;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 16px rgba(236, 72, 153, 0.35);
    transition: all 0.25s ease;
    white-space: nowrap;
  }
  .vl-ps-auto-suggest-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 22px rgba(236, 72, 153, 0.45);
  }

  /* Intelligence Meter Strip */
  .vl-ps-intel-box {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 1.5px solid #e2e8f0;
    border-radius: 18px;
    padding: 20px 24px;
    margin-bottom: 26px;
  }
  .vl-ps-intel-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 12px;
    flex-wrap: wrap;
  }
  .vl-ps-intel-stat {
    display: flex;
    align-items: baseline;
    gap: 6px;
  }
  .vl-ps-intel-label {
    font-size: 0.82rem;
    text-transform: uppercase;
    font-weight: 800;
    letter-spacing: 0.05em;
    color: #64748b;
  }
  .vl-ps-intel-val {
    font-family: 'Outfit', sans-serif;
    font-size: 1.35rem;
    font-weight: 900;
    color: #0f172a;
  }
  .vl-ps-progress-track {
    width: 100%;
    height: 10px;
    background: #e2e8f0;
    border-radius: 9999px;
    overflow: hidden;
    position: relative;
    margin-bottom: 12px;
  }
  .vl-ps-progress-fill {
    height: 100%;
    width: 0%;
    border-radius: 9999px;
    transition: width 0.35s ease, background 0.35s ease;
    background: linear-gradient(90deg, #10b981, #059669);
  }
  .vl-ps-progress-fill.warning {
    background: linear-gradient(90deg, #f59e0b, #d97706);
  }
  .vl-ps-progress-fill.over {
    background: linear-gradient(90deg, #ef4444, #dc2626);
  }
  .vl-ps-intel-feedback {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    font-size: 0.88rem;
    flex-wrap: wrap;
  }
  .vl-ps-feedback-msg {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 700;
  }
  .vl-ps-feedback-msg.safe { color: #059669; }
  .vl-ps-feedback-msg.warning { color: #d97706; }
  .vl-ps-feedback-msg.danger { color: #dc2626; }

  .vl-ps-cbse-meter {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.82rem;
    font-weight: 700;
  }
  .vl-ps-cbse-pill {
    padding: 3px 9px;
    border-radius: 6px;
    background: #e0f2fe;
    color: #0369a1;
  }
  .vl-ps-cbse-pill.active {
    background: #dcfce7;
    color: #15803d;
  }

  /* Category Allocation Guide */
  .vl-ps-alloc-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    margin-top: 14px;
    padding-top: 14px;
    border-top: 1px dashed #cbd5e1;
  }
  @media (max-width: 860px) {
    .vl-ps-alloc-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }
  .vl-ps-alloc-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 8px 12px;
    display: flex;
    flex-direction: column;
    gap: 3px;
    transition: all 0.2s;
  }
  .vl-ps-alloc-title {
    font-size: 0.72rem;
    font-weight: 800;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .vl-ps-alloc-val {
    font-family: 'Outfit', sans-serif;
    font-size: 0.95rem;
    font-weight: 800;
    color: #1e1b4b;
  }
  .vl-ps-alloc-bar {
    height: 4px;
    background: #f1f5f9;
    border-radius: 2px;
    overflow: hidden;
    margin-top: 2px;
  }
  .vl-ps-alloc-fill {
    height: 100%;
    background: #6366f1;
    border-radius: 2px;
    transition: width 0.3s ease;
  }

  /* Intelligent Suggestion Shelf */
  .vl-ps-shelf {
    background: linear-gradient(135deg, #fbf7ff 0%, #f5f3ff 100%);
    border: 1.5px solid #d8b4fe;
    border-radius: 18px;
    padding: 20px 22px;
    margin-bottom: 22px;
    box-shadow: 0 4px 18px rgba(139, 92, 246, 0.08);
  }
  .vl-ps-shelf-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 14px;
    flex-wrap: wrap;
  }
  .vl-ps-shelf-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.05rem;
    font-weight: 800;
    color: #1e1b4b;
  }
  .vl-ps-shelf-meta {
    font-size: 0.84rem;
    color: #6d28d9;
    font-weight: 700;
    background: #ede9fe;
    padding: 4px 12px;
    border-radius: 9999px;
  }
  .vl-ps-shelf-cards {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 14px;
  }
  .vl-ps-shelf-item {
    background: #ffffff;
    border: 1.5px solid #e9d5ff;
    border-radius: 14px;
    padding: 14px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: all 0.2s ease;
    box-shadow: 0 2px 8px rgba(99, 102, 241, 0.05);
  }
  .vl-ps-shelf-item:hover {
    border-color: #a855f7;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(168, 85, 247, 0.15);
  }
  .vl-ps-shelf-item-top {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 10px;
  }
  .vl-ps-shelf-item-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: #f3e8ff;
    color: #7e22ce;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
  }
  .vl-ps-shelf-item-name {
    font-size: 0.88rem;
    font-weight: 700;
    color: #1e1b4b;
    line-height: 1.3;
    margin-bottom: 4px;
  }
  .vl-ps-shelf-item-desc {
    font-size: 0.76rem;
    color: #64748b;
    line-height: 1.35;
  }
  .vl-ps-shelf-item-bottom {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    padding-top: 10px;
    border-top: 1px dashed #e2e8f0;
    margin-top: 8px;
  }
  .vl-ps-shelf-add-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 14px;
    border-radius: 8px;
    background: #6366f1;
    color: #ffffff;
    font-size: 0.82rem;
    font-weight: 700;
    border: none;
    cursor: pointer;
    transition: all 0.18s;
  }
  .vl-ps-shelf-add-btn:hover {
    background: #4f46e5;
    transform: translateY(-1px);
  }
  .vl-ps-ai-badge {
    background: linear-gradient(135deg, #fef08a 0%, #fde047 100%);
    color: #854d0e;
    font-size: 0.68rem;
    font-weight: 800;
    padding: 2px 7px;
    border-radius: 4px;
    border: 1px solid #facc15;
    animation: vl-pulse-subtle 2s infinite;
  }
  @keyframes vl-pulse-subtle {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.9; transform: scale(1.02); }
  }
  .vl-ps-pill-badge {
    display: inline-block;
    padding: 1px 7px;
    border-radius: 9999px;
    font-size: 0.72rem;
    font-weight: 800;
    background: rgba(0, 0, 0, 0.08);
  }
  .vl-ps-pill.active .vl-ps-pill-badge {
    background: rgba(255, 255, 255, 0.3);
    color: #ffffff;
  }
  .vl-ps-card.ai-suggested {
    border-color: #facc15;
    box-shadow: 0 0 0 1px #facc15, 0 4px 14px rgba(250, 204, 21, 0.18);
  }

  /* Filters & Search Toolbar */
  .vl-ps-toolbar {
    display: flex;
    flex-direction: column;
    gap: 14px;
    margin-bottom: 24px;
    padding-bottom: 20px;
    border-bottom: 1.5px solid #f1f5f9;
  }
  .vl-ps-search-row {
    display: flex;
    gap: 14px;
    align-items: center;
  }
  .vl-ps-search-wrap {
    flex: 1;
    position: relative;
  }
  .vl-ps-search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    pointer-events: none;
  }
  .vl-ps-search-input {
    width: 100%;
    padding: 12px 14px 12px 42px;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    font-size: 0.94rem;
    font-family: inherit;
    background: #fafafa;
    color: #0f172a;
    transition: all 0.2s;
    box-sizing: border-box;
  }
  .vl-ps-search-input:focus {
    outline: none;
    border-color: #6366f1;
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
  }
  .vl-ps-filter-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
  }
  .vl-ps-filter-label {
    font-size: 0.8rem;
    font-weight: 800;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-right: 4px;
  }
  .vl-ps-pill {
    padding: 6px 14px;
    border-radius: 9999px;
    font-family: 'Outfit', sans-serif;
    font-size: 0.84rem;
    font-weight: 700;
    border: 1.5px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    cursor: pointer;
    transition: all 0.2s ease;
  }
  .vl-ps-pill:hover {
    border-color: #cbd5e1;
    color: #1e1b4b;
  }
  .vl-ps-pill.active {
    background: #6366f1;
    color: #ffffff;
    border-color: #6366f1;
    box-shadow: 0 3px 10px rgba(99, 102, 241, 0.25);
  }
  .vl-ps-pill.active.pkg-basic {
    background: #0284c7;
    border-color: #0284c7;
  }
  .vl-ps-pill.active.pkg-advance {
    background: #7c3aed;
    border-color: #7c3aed;
  }
  .vl-ps-pill.active.pkg-premium {
    background: #db2777;
    border-color: #db2777;
  }

  /* Product Cards Grid */
  .vl-ps-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
    gap: 16px;
    max-height: 580px;
    overflow-y: auto;
    padding: 4px;
  }
  @media (max-width: 640px) {
    .vl-ps-grid {
      grid-template-columns: 1fr;
    }
  }
  .vl-ps-card {
    border: 2px solid #e2e8f0;
    border-radius: 16px;
    padding: 16px;
    background: #ffffff;
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: relative;
    cursor: pointer;
    user-select: none;
  }
  .vl-ps-card:hover {
    border-color: #cbd5e1;
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.06);
  }
  .vl-ps-card.selected {
    border-color: #6366f1;
    background: #fbf9fd;
    box-shadow: 0 6px 20px rgba(99, 102, 241, 0.14);
  }
  .vl-ps-card-top {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 12px;
  }
  .vl-ps-check-wrap {
    flex-shrink: 0;
    margin-top: 2px;
  }
  .vl-ps-checkbox {
    width: 20px;
    height: 20px;
    border-radius: 6px;
    border: 2px solid #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #ffffff;
    color: transparent;
    font-size: 12px;
    font-weight: 900;
    transition: all 0.2s;
  }
  .vl-ps-card.selected .vl-ps-checkbox {
    background: #6366f1;
    border-color: #6366f1;
    color: #ffffff;
  }
  .vl-ps-card-info {
    flex: 1;
    min-width: 0;
  }
  .vl-ps-badge-row {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 6px;
  }
  .vl-ps-pkg-badge {
    font-size: 0.68rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 2px 7px;
    border-radius: 4px;
  }
  .vl-ps-pkg-badge.basic { background: #e0f2fe; color: #0284c7; }
  .vl-ps-pkg-badge.advance { background: #ede9fe; color: #6d28d9; }
  .vl-ps-pkg-badge.premium { background: #fce7f3; color: #db2777; }

  .vl-ps-cat-badge {
    font-size: 0.68rem;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 4px;
    background: #f1f5f9;
    color: #475569;
  }
  .vl-ps-grade-badge {
    font-size: 0.68rem;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 4px;
    background: #fef3c7;
    color: #92400e;
    display: inline-flex;
    align-items: center;
    border: 1px solid #fde68a;
  }
  .vl-ps-capacity-badge {
    font-size: 0.68rem;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 4px;
    background: #ecfdf5;
    color: #065f46;
    display: inline-flex;
    align-items: center;
    border: 1px solid #a7f3d0;
  }
  .vl-ps-card-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: #1e293b;
    line-height: 1.35;
    margin-bottom: 4px;
  }
  .vl-ps-card-desc {
    font-size: 0.78rem;
    color: #64748b;
    line-height: 1.35;
  }
  .vl-ps-card-bottom {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 14px;
    padding-top: 12px;
    border-top: 1px solid #f1f5f9;
  }
  .vl-ps-card-price {
    font-family: 'Outfit', sans-serif;
    font-size: 1.05rem;
    font-weight: 800;
    color: #0f172a;
  }
  .vl-ps-qty-stepper {
    display: inline-flex;
    align-items: center;
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    overflow: hidden;
  }
  .vl-ps-qty-btn {
    width: 28px;
    height: 28px;
    background: #f8fafc;
    border: none;
    color: #475569;
    font-weight: 800;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.15s;
  }
  .vl-ps-qty-btn:hover { background: #e2e8f0; color: #0f172a; }
  .vl-ps-qty-val {
    width: 32px;
    text-align: center;
    font-family: 'Outfit', sans-serif;
    font-size: 0.9rem;
    font-weight: 800;
    color: #0f172a;
  }

  /* Summary Drawer / Bar */
  .vl-ps-summary-bar {
    margin-top: 24px;
    background: #1e1b4b;
    color: #ffffff;
    border-radius: 18px;
    padding: 20px 26px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    box-shadow: 0 14px 34px rgba(30, 27, 75, 0.25);
    flex-wrap: wrap;
  }
  .vl-ps-sum-metrics {
    display: flex;
    align-items: center;
    gap: 24px;
    flex-wrap: wrap;
  }
  .vl-ps-sum-val {
    font-family: 'Outfit', sans-serif;
    font-size: 1.45rem;
    font-weight: 900;
    color: #fef08a;
  }
  .vl-ps-sum-label {
    font-size: 0.76rem;
    text-transform: uppercase;
    font-weight: 700;
    letter-spacing: 0.04em;
    color: #c7d2fe;
  }
  .vl-ps-sum-actions {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .vl-ps-reset-btn {
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.25);
    color: #ffffff;
    padding: 10px 18px;
    border-radius: 10px;
    font-family: 'Outfit', sans-serif;
    font-size: 0.88rem;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.2s;
  }
  .vl-ps-reset-btn:hover {
    background: rgba(255, 255, 255, 0.2);
  }
  .vl-ps-order-btn {
    background: linear-gradient(135deg, #ffffff 0%, #ffedd5 100%);
    color: #db2777 !important;
    border: none;
    padding: 12px 24px;
    border-radius: 12px;
    font-family: 'Outfit', sans-serif;
    font-size: 0.98rem;
    font-weight: 900;
    cursor: pointer;
    box-shadow: 0 4px 14px rgba(0,0,0,0.2);
    transition: all 0.25s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
  }
  .vl-ps-order-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.28);
  }

  /* ==================== LEAD FORM ==================== */
  .vl-form-panel {
    display: grid;
    grid-template-columns: 1fr 1.15fr;
    gap: 52px;
    /* align-items: center; */
  }

  @media (max-width: 900px) {
    .vl-form-panel {
      grid-template-columns: 1fr;
      gap: 36px;
    }
  }

  .vl-checklist-items {
    margin-top: 30px;
    padding: 0;
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 18px;
  }

  .vl-checklist-items li {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    font-size: 1.06rem;
    color: var(--vl-body);
    font-weight: 600;
  }

  .vl-checklist-badge {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #fdf2f8;
    color: #db2777;
    border: 1.5px solid #fbcfe8;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 2px;
    font-weight: 900;
    font-size: 0.9rem;
  }

  .vl-form-card {
    background: #ffffff;
    border: 2px solid #e0e7ff;
    border-radius: 26px;
    padding: 40px;
    box-shadow: 0 18px 40px rgba(99, 102, 241, 0.12);
  }

  .vl-form-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
  }

  @media (max-width: 540px) {
    .vl-form-grid-2 {
      grid-template-columns: 1fr;
    }
  }

  .vl-field {
    margin-bottom: 18px;
  }

  .vl-field label {
    display: block;
    font-family: 'Outfit', sans-serif;
    font-size: 0.92rem;
    font-weight: 700;
    color: var(--vl-heading);
    margin-bottom: 6px;
  }

  .vl-field input,
  .vl-field textarea {
    width: 100%;
    padding: 14px 16px;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    font-size: 0.98rem;
    font-family: inherit;
    background: #faf5ff;
    color: var(--vl-heading);
    transition: all 0.2s ease;
    box-sizing: border-box;
  }

  .vl-field textarea {
    resize: vertical;
    min-height: 74px;
  }

  .vl-field input:focus,
  .vl-field textarea:focus {
    outline: none;
    border-color: #8b5cf6;
    background: #ffffff;
    box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.16);
  }

  .vl-form-submit-vibrant {
    width: 100%;
    background: linear-gradient(135deg, #f97316 0%, #ec4899 50%, #8b5cf6 100%);
    color: #ffffff;
    border: none;
    padding: 17px;
    font-family: 'Outfit', sans-serif;
    font-weight: 900;
    font-size: 1.1rem;
    border-radius: 14px;
    cursor: pointer;
    box-shadow: 0 10px 25px rgba(236, 72, 153, 0.4);
    transition: all 0.25s ease;
  }

  .vl-form-submit-vibrant:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 32px rgba(236, 72, 153, 0.55);
  }

  .vl-form-note {
    font-size: 0.86rem;
    color: var(--vl-muted);
    margin-top: 14px;
    text-align: center;
    font-weight: 500;
  }

  .vl-confirm {
    display: none;
    background: #fdf2f8;
    color: #db2777;
    padding: 32px;
    border-radius: 18px;
    border: 2px solid #fbcfe8;
    text-align: center;
    box-shadow: var(--vl-shadow-card);
  }

  .vl-confirm-icon {
    font-size: 3rem;
    display: inline-block;
    margin-bottom: 12px;
  }

  /* ==================== COMPARISON TABLE ==================== */
  .vl-table-card {
    background: #ffffff;
    border: 2px solid #ede9fe;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: var(--vl-shadow-card);
  }

  .vl-table-wrap {
    overflow-x: auto;
  }

  .vl-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
  }

  .vl-table th {
    padding: 22px 26px;
    font-family: 'Outfit', sans-serif;
    font-size: 1.05rem;
    font-weight: 900;
  }

  .vl-table th:first-child {
    background: #faf5ff;
    color: #6d28d9;
    width: 30%;
  }

  .vl-table th:nth-child(2) {
    background: #fff7ed;
    color: #ea580c;
    width: 35%;
  }

  .vl-table th:nth-child(3) {
    background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
    color: #ffffff;
    width: 35%;
  }

  .vl-table td {
    padding: 20px 26px;
    border-bottom: 1.5px solid #f1f5f9;
    font-size: 0.98rem;
  }

  .vl-table tr:last-child td {
    border-bottom: none;
  }

  .vl-table td:first-child {
    font-weight: 700;
    color: var(--vl-heading);
    background: #faf5ff;
  }

  .vl-table td.no {
    color: #ea580c;
    background: #fffbf7;
    font-weight: 600;
  }

  .vl-table td.yes {
    color: #6d28d9;
    font-weight: 800;
    background: #faf5ff;
  }

  /* ==================== FAQ CAROUSEL ==================== */
  .vl-faq-carousel {
    position: relative;
  }

  .vl-faq-slide {
    display: none;
    animation: vl-fade-in 0.35s ease;
  }

  .vl-faq-slide.active {
    display: block;
  }

  @keyframes vl-fade-in {
    from { opacity: 0; transform: translateY(12px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .vl-faq-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 26px;
  }

  @media (max-width: 920px) {
    .vl-faq-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  @media (max-width: 600px) {
    .vl-faq-grid {
      grid-template-columns: 1fr;
    }
  }

  .vl-faq-card {
    background: #ffffff;
    border: 2px solid #ede9fe;
    border-radius: 20px;
    padding: 32px 28px;
    display: flex;
    flex-direction: column;
    box-shadow: var(--vl-shadow-card);
    transition: all 0.25s ease;
  }

  .vl-faq-card:hover {
    border-color: #c084fc;
    box-shadow: var(--vl-shadow-hover);
    transform: translateY(-5px);
  }

  .vl-faq-card__q {
    font-family: 'Outfit', sans-serif;
    font-size: 1.26rem;
    font-weight: 800;
    color: var(--vl-heading);
    line-height: 1.35;
    margin-bottom: 14px;
  }

  .vl-faq-card__divider {
    width: 40px;
    height: 4px;
    background: linear-gradient(90deg, #f97316 0%, #ec4899 100%);
    border-radius: 4px;
    margin-bottom: 16px;
  }

  .vl-faq-card__a {
    font-size: 0.96rem;
    color: var(--vl-body);
    line-height: 1.68;
    flex: 1;
  }

  .vl-faq-card__a ul {
    padding-left: 18px;
    margin: 8px 0 0;
  }

  .vl-faq-card__a li {
    margin-bottom: 6px;
  }

  .vl-faq-nav {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    margin-top: 40px;
    padding-top: 26px;
    border-top: 2px solid #ede9fe;
  }

  .vl-faq-btn {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    border: 2px solid #ddd6fe;
    background: #ffffff;
    color: #6d28d9;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    box-shadow: var(--vl-shadow-card);
  }

  .vl-faq-btn:hover:not(:disabled) {
    background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
    border-color: #6366f1;
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(99, 102, 241, 0.35);
  }

  .vl-faq-btn:disabled {
    opacity: 0.35;
    cursor: not-allowed;
  }

  .vl-faq-dots {
    display: flex;
    gap: 8px;
    align-items: center;
  }

  .vl-faq-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #ddd6fe;
    cursor: pointer;
    transition: all 0.25s ease;
    border: none;
    padding: 0;
  }

  .vl-faq-dot.active {
    background: linear-gradient(135deg, #f97316 0%, #ec4899 100%);
    width: 28px;
    border-radius: 12px;
  }

  .vl-faq-counter {
    font-family: 'Outfit', sans-serif;
    font-size: 0.95rem;
    color: #6d28d9;
    font-weight: 800;
    min-width: 50px;
    text-align: center;
  }

  /* ==================== FINAL CALL TO ACTION ==================== */
  .vl-final-cta-vibrant {
    background: linear-gradient(135deg, #4338ca 0%, #7c3aed 38%, #db2777 75%, #f97316 100%);
    color: #ffffff;
    padding: 90px 0;
    position: relative;
    overflow: hidden;
  }

  .vl-final-cta-vibrant::before {
    content: "";
    position: absolute;
    top: 50%;
    right: 8%;
    transform: translateY(-50%);
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(254, 240, 138, 0.35) 0%, transparent 65%);
    border-radius: 50%;
    pointer-events: none;
  }

  .vl-final-cta-vibrant h2 {
    color: #ffffff;
    font-size: clamp(2.3rem, 4.2vw, 3.2rem);
    max-width: 24ch;
  }

  .vl-final-cta-vibrant p {
    color: #ffffff;
    margin-top: 18px;
    font-size: 1.2rem;
    max-width: 56ch;
    font-weight: 500;
  }

  /* ==================== CALLBACK MODAL ==================== */
  .cb-modal-backdrop {
    position: fixed;
    inset: 0;
    z-index: 9999;
    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    animation: cbFadeIn .25s ease;
  }
  @keyframes cbFadeIn {
    from { opacity: 0; }
    to   { opacity: 1; }
  }
  .cb-modal-box {
    position: relative;
    background: #ffffff;
    border-radius: 20px;
    width: 100%;
    max-width: 680px;
    max-height: 90vh;
    overflow-y: auto;
    padding: 40px 44px;
    box-shadow: 0 24px 64px rgba(0,0,0,.25);
    animation: cbSlideUp .3s cubic-bezier(.22,1,.36,1);
  }
  @keyframes cbSlideUp {
    from { opacity: 0; transform: translateY(30px) scale(.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
  }
  .cb-modal-close {
    position: absolute;
    top: 16px;
    right: 16px;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 1.5px solid #e2e8f0;
    background: #f8fafc;
    color: #64748b;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background .2s, color .2s;
  }
  .cb-modal-close:hover {
    background: #fee2e2;
    border-color: #fca5a5;
    color: #dc2626;
  }
  .cb-modal-header {
    margin-bottom: 28px;
    padding-bottom: 20px;
    border-bottom: 1px solid #f1f5f9;
  }
  @media (max-width: 600px) {
    .cb-modal-box { padding: 28px 20px; }
    .cb-modal-box .vl-form-grid-2 { grid-template-columns: 1fr !important; }
  }

  /* ==================== COMPREHENSIVE MOBILE RESPONSIVE ==================== */

  /* --- Global Mobile Adjustments --- */
  @media (max-width: 768px) {
    .vidyalab-wrap {
      padding: 0 16px;
    }
    .vl-section {
      padding: 56px 0;
    }
    .vl-section-head {
      margin-bottom: 32px;
    }
    .vl-section-head h2 {
      font-size: clamp(1.6rem, 6vw, 2.2rem);
    }
    .vl-section-head p {
      font-size: 1rem;
    }
  }

  @media (max-width: 480px) {
    .vidyalab-wrap {
      padding: 0 14px;
    }
    .vl-section {
      padding: 40px 0;
    }
    .vl-section-head {
      margin-bottom: 24px;
    }
    .vl-section-head h2 {
      font-size: clamp(1.45rem, 5.5vw, 1.85rem);
    }
    .vl-section-head p {
      font-size: 0.94rem;
      line-height: 1.6;
    }
  }

  /* --- Hero Section Mobile --- */
  @media (max-width: 768px) {
    .vl-hero-vibrant {
      padding: 48px 0 56px;
    }
    .vl-hero-vibrant h1 {
      font-size: clamp(1.75rem, 6.5vw, 2.6rem);
      margin-top: 10px;
    }
    .vl-hero-vibrant p.lead {
      font-size: 1rem;
      margin-top: 16px;
    }
    .vl-hero-ctas {
      margin-top: 24px;
      flex-direction: column;
      gap: 12px;
    }
    .vl-btn-vibrant-cta,
    .vl-btn-glass-cta {
      width: 100%;
      justify-content: center;
      padding: 14px 20px;
      font-size: 0.98rem;
    }
    .vl-hero-stats {
      margin-top: 28px;
      padding-top: 20px;
      gap: 10px;
    }
    .vl-stat-box {
      padding: 12px 14px;
      border-radius: 12px;
    }
    .vl-stat-box .num {
      font-size: 1.55rem;
    }
    .vl-stat-box .label {
      font-size: 0.75rem;
    }
  }

  @media (max-width: 480px) {
    .vl-hero-vibrant {
      padding: 36px 0 44px;
    }
    .vl-hero-vibrant h1 {
      font-size: clamp(1.5rem, 6vw, 2rem);
    }
    .vl-badge-pill {
      font-size: 0.72rem;
      padding: 5px 12px;
    }
  }

  /* --- Hero Image Responsive --- */
  .vl-hero-grid > div:last-child img {
    width: 100%;
    height: auto;
    border-radius: 16px;
    display: block;
    object-fit: cover;
    max-height: 340px;
  }
  @media (max-width: 980px) {
    .vl-hero-grid > div:last-child img {
      max-height: 280px;
      border-radius: 14px;
    }
  }
  @media (max-width: 480px) {
    .vl-hero-grid > div:last-child img {
      max-height: 200px;
      border-radius: 12px;
    }
  }

  /* --- Outcomes Section Mobile --- */
  @media (max-width: 768px) {
    .vl-outcomes-grid {
      grid-template-columns: 1fr;
      gap: 20px;
    }
    .vl-outcome-content {
      padding: 22px 20px;
    }
    .vl-outcome-card h3 {
      font-size: 1.3rem;
    }
    .vl-outcome-card p {
      font-size: 0.94rem;
    }
    .vl-outcome-img {
      aspect-ratio: 16 / 10;
    }
  }

  /* --- Features Section Mobile --- */
  @media (max-width: 768px) {
    .vl-features-grid {
      grid-template-columns: 1fr;
      gap: 20px;
    }
    .vl-feature-card-body {
      padding: 20px;
    }
    .vl-feature-card h3 {
      font-size: 1.18rem;
    }
    .vl-feature-card-img {
      height: 180px;
    }
  }

  /* --- Pricing Section Mobile --- */
  @media (max-width: 768px) {
    .vl-plans-grid {
      grid-template-columns: 1fr;
      max-width: 100%;
      gap: 24px;
    }
    .vl-plan-card {
      padding: 28px 22px;
      border-radius: 20px;
    }
    .vl-plan-price {
      font-size: 2.6rem;
    }
    .vl-plan-summary {
      font-size: 0.92rem;
    }
    .vl-plan-list li {
      font-size: 0.9rem;
      gap: 10px;
    }
    .vl-plan-check-icon {
      width: 20px;
      height: 20px;
      font-size: 0.72rem;
    }
    .vl-plan-badge-top {
      font-size: 0.74rem;
      padding: 6px 18px;
    }
  }

  /* --- Package Toggle Mobile --- */
  @media (max-width: 600px) {
    .vl-pkg-toggle-pill {
      flex-direction: column;
      border-radius: 16px;
      width: 100%;
    }
    .vl-pkg-tab-btn {
      width: 100%;
      justify-content: center;
      padding: 12px 16px;
      font-size: 0.88rem;
      border-radius: 12px;
    }
  }

  /* --- Custom Advisor Banner Mobile --- */
  @media (max-width: 768px) {
    .vl-custom-advisor-banner {
      flex-direction: column;
      text-align: center;
      padding: 20px 16px;
      gap: 16px;
    }
    .vl-cab-content h3 {
      font-size: 1.1rem;
    }
    .vl-cab-content p {
      font-size: 0.88rem;
    }
    .vl-cab-cta-btn {
      width: 100%;
    }
  }

  /* --- Custom Package Builder Mobile --- */
  @media (max-width: 768px) {
    .vl-edu-campus-bar {
      padding: 16px 14px;
      border-radius: 16px;
    }
    .vl-edu-bar-header {
      flex-direction: column;
      gap: 12px;
    }
    .vl-edu-kpi-pill {
      align-items: center;
      flex-direction: row;
      justify-content: space-between;
      padding: 10px 14px;
    }
    .vl-edu-params-grid {
      grid-template-columns: 1fr;
      gap: 14px;
    }
    .vl-edu-tier-btns {
      gap: 6px;
    }
    .vl-edu-tier-btn, .vl-edu-chip {
      min-width: 55px;
      padding: 8px 4px;
      font-size: 0.74rem;
    }
    .vl-cp-slider-ticks {
      font-size: 0.68rem;
    }
    .vl-ps-section {
      padding: 22px 16px;
      border-radius: 18px;
    }
    .vl-ps-header {
      flex-direction: column;
      gap: 14px;
    }
    .vl-ps-header-actions {
      width: 100%;
      flex-direction: column;
      gap: 10px;
    }
    .vl-ps-cat-suggest-btn,
    .vl-ps-auto-suggest-btn {
      width: 100%;
      justify-content: center;
      font-size: 0.85rem;
      padding: 12px 14px;
    }
    .vl-ps-intel-box {
      padding: 14px 16px;
      border-radius: 14px;
    }
    .vl-ps-intel-row {
      flex-direction: column;
      align-items: flex-start;
      gap: 10px;
    }
    .vl-ps-intel-val {
      font-size: 1.1rem;
    }
    .vl-ps-intel-feedback {
      flex-direction: column;
      align-items: flex-start;
      gap: 10px;
    }
    .vl-ps-cbse-meter {
      flex-wrap: wrap;
      gap: 6px;
    }
    .vl-ps-alloc-grid {
      grid-template-columns: 1fr 1fr;
      gap: 8px;
    }
    .vl-ps-shelf {
      padding: 16px;
      border-radius: 14px;
    }
    .vl-ps-shelf-head {
      flex-direction: column;
      align-items: flex-start;
      gap: 8px;
    }
    .vl-ps-shelf-title {
      font-size: 0.92rem;
      flex-wrap: wrap;
    }
    .vl-ps-shelf-cards {
      grid-template-columns: 1fr;
    }
    .vl-ps-grid {
      grid-template-columns: 1fr;
      max-height: 400px;
    }
    .vl-ps-summary-bar {
      flex-direction: column;
      align-items: stretch;
      gap: 16px;
      padding: 16px 18px;
      border-radius: 14px;
    }
    .vl-ps-sum-metrics {
      flex-direction: column;
      gap: 14px;
    }
    .vl-ps-sum-val {
      font-size: 1.2rem;
    }
    .vl-ps-sum-actions {
      flex-direction: column;
      gap: 10px;
    }
    .vl-ps-reset-btn,
    .vl-ps-order-btn {
      width: 100%;
      justify-content: center;
      text-align: center;
    }
    .vl-ps-title-wrap h3 {
      font-size: 1.15rem;
    }
  }

  /* --- Lead Form Section Mobile --- */
  @media (max-width: 768px) {
    .vl-form-panel {
      grid-template-columns: 1fr;
      gap: 28px;
    }
    .vl-form-card {
      padding: 24px 18px;
      border-radius: 20px;
    }
    .vl-form-grid-2 {
      grid-template-columns: 1fr;
      gap: 0;
    }
    .vl-field input,
    .vl-field textarea {
      padding: 12px 14px;
      font-size: 0.94rem;
      border-radius: 10px;
    }
    .vl-form-submit-vibrant {
      padding: 15px;
      font-size: 1rem;
      border-radius: 12px;
    }
    .vl-checklist-items {
      margin-top: 20px;
      gap: 14px;
    }
    .vl-checklist-items li {
      font-size: 0.94rem;
    }
  }

  /* --- Comparison Table Mobile (card-style stacking) --- */
  @media (max-width: 768px) {
    .vl-table-card {
      border-radius: 16px;
    }
    .vl-table th,
    .vl-table td {
      padding: 14px 16px;
      font-size: 0.85rem;
    }
    .vl-table th {
      font-size: 0.88rem;
    }
    .vl-table-wrap {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }
    .vl-table {
      min-width: 600px;
    }
  }

  /* --- FAQ Carousel Mobile --- */
  @media (max-width: 768px) {
    .vl-faq-grid {
      grid-template-columns: 1fr;
      gap: 16px;
    }
    .vl-faq-card {
      padding: 22px 20px;
      border-radius: 16px;
    }
    .vl-faq-card__q {
      font-size: 1.08rem;
    }
    .vl-faq-card__a {
      font-size: 0.9rem;
    }
    .vl-faq-nav {
      margin-top: 28px;
      padding-top: 20px;
      gap: 14px;
    }
    .vl-faq-btn {
      width: 42px;
      height: 42px;
      border-radius: 12px;
    }
  }

  /* --- Final CTA Mobile --- */
  @media (max-width: 768px) {
    .vl-final-cta-vibrant {
      padding: 56px 0;
    }
    .vl-final-cta-vibrant h2 {
      font-size: clamp(1.6rem, 5.5vw, 2.4rem);
    }
    .vl-final-cta-vibrant p {
      font-size: 1rem;
    }
  }

  @media (max-width: 480px) {
    .vl-final-cta-vibrant {
      padding: 40px 0;
    }
    .vl-final-cta-vibrant h2 {
      font-size: clamp(1.4rem, 5vw, 1.85rem);
    }
  }

  /* --- Modals Mobile --- */
  @media (max-width: 600px) {
    .cb-modal-backdrop,
    .custom-pkg-modal-backdrop {
      padding: 12px;
      align-items: flex-end;
    }
    .cb-modal-box,
    .custom-pkg-modal-box {
      padding: 24px 18px;
      border-radius: 20px 20px 0 0;
      max-height: 95vh;
    }
    .cb-modal-header {
      margin-bottom: 18px;
      padding-bottom: 14px;
    }
    .cb-modal-header h2,
    #cbModalTitle,
    #cpModalTitle {
      font-size: 1.25rem !important;
    }
    .custom-pkg-summary-pill {
      flex-direction: column;
      gap: 10px;
      text-align: center;
    }
    .custom-pkg-summary-pill > div:last-child {
      text-align: center;
    }
  }

  /* --- Fix Overflow Globally on Mobile --- */
  @media (max-width: 768px) {
    .vidyalab-page {
      overflow-x: hidden;
    }
    .vl-hero-vibrant::before {
      width: 300px;
      height: 300px;
      top: -60px;
      right: -40px;
    }
    .vl-hero-vibrant::after {
      width: 250px;
      height: 250px;
      bottom: -60px;
      left: -40px;
    }
    .vl-final-cta-vibrant::before {
      width: 250px;
      height: 250px;
    }
  }

  /* --- Touch-Friendly Interactions for Mobile --- */
  @media (max-width: 768px) {
    .vl-outcome-card:hover,
    .vl-feature-card:hover,
    .vl-plan-card:hover,
    .vl-faq-card:hover {
      transform: none;
    }
    .vl-outcome-card:active,
    .vl-feature-card:active {
      transform: scale(0.99);
    }
  }

  /* --- Blueprint Glass Card Mobile --- */
  @media (max-width: 768px) {
    .vl-blueprint-glass {
      padding: 18px;
      border-radius: 16px;
    }
    .vl-blueprint-head {
      flex-direction: column;
      gap: 8px;
      align-items: flex-start;
    }
  }

  /* --- Smooth scroll for mobile --- */
  @media (max-width: 768px) {
    html {
      scroll-behavior: smooth;
    }
  }
</style>
@endpush
@section('contents')

<!-- =====================================================================
     HERO
     ===================================================================== -->
<section class="relative pt-12 pb-16 lg:pt-20 lg:pb-24 overflow-hidden bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">

      <div class="lg:col-span-6 space-y-6">
       

        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold uppercase tracking-wider">
          <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
          Building Schools That Prepare Students for the Future
        </div>

        <h1 class="text-4xl sm:text-5xl lg:text-[52px] font-extrabold text-brand-navy leading-[1.12] tracking-tight">
          Skillvation <span class="text-brand-orange">Lab</span>
        </h1>

        <p class="text-base sm:text-lg text-slate-600 leading-relaxed max-w-xl">
          A school today is expected to do more than deliver academic knowledge. Students need opportunities to create, experiment, solve problems, use technology, communicate ideas and apply what they learn in real-life situations.
        </p>
        <p class="text-base sm:text-lg text-slate-600 leading-relaxed max-w-xl">
         Skillvation Lab helps schools create structured, practical learning environments where students can move from learning concepts to applying them.
        </p>


        <div class="flex flex-wrap gap-3">
          @foreach([
            ['fa-robot',     'text-emerald-500', 'Robotics & AI'],
            ['fa-flask',     'text-blue-500',    'STEM & Science'],
            ['fa-lightbulb', 'text-purple-500',  'Design Thinking'],
          ] as [$icon, $color, $label])
          <div class="inline-flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-full px-4 py-1.5 text-xs font-bold text-slate-700">
            <i class="fa-solid {{ $icon }} {{ $color }}"></i> {{ $label }}
          </div>
          @endforeach
        </div>

        <div class="flex flex-wrap items-center gap-4 pt-2">
          <a href="#demo" class="inline-flex items-center justify-center px-7 py-3.5 rounded-xl bg-brand-navy hover:bg-brand-darknavy text-white text-sm font-bold transition-all shadow-md hover:shadow-xl hover:-translate-y-0.5 group">
            Let's Build Your Skill Lab
            <i class="fa-solid fa-arrow-right ml-2.5 text-xs transition-transform group-hover:translate-x-1"></i>
          </a>
          
        </div>
      </div>

      <div class="lg:col-span-6">
        <div class="relative">
          <div class="absolute -inset-2 bg-gradient-to-tr from-emerald-200/40 to-brand-navy/10 rounded-3xl transform rotate-1 blur-sm"></div>
          <div class="relative bg-white p-3 rounded-3xl border-2 border-emerald-300/50 shadow-2xl overflow-hidden">
            <div class="aspect-[4/3] rounded-2xl overflow-hidden bg-slate-100">
              <img src="{{ asset('frontend/img/skillbox/Workonmaterialsandmachines.jpeg') }}"
                   alt="Students working in a Skillvation skill lab"
                   class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" />
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mt-14 lg:mt-20">
      <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-6 text-center shadow-sm">
        <div class="text-3xl lg:text-4xl font-black text-brand-navy mb-1">11+</div>
        <div class="text-xs lg:text-sm font-semibold text-slate-600">Skill Learning Domains</div>
      </div>
      <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-6 text-center shadow-sm">
        <div class="text-3xl lg:text-4xl font-black text-brand-orange mb-1">100%</div>
        <div class="text-xs lg:text-sm font-semibold text-slate-600">Turnkey — Infrastructure to Support</div>
      </div>
      <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-6 text-center shadow-sm">
        <div class="text-3xl lg:text-4xl font-black text-brand-navy mb-1">ICSE &amp; State</div>
        <div class="text-xs lg:text-sm font-semibold text-slate-600">Board Aligned</div>
      </div>
    </div>
  </div>
</section>

<!-- =====================================================================
     WHAT IS SKILLVATION LAB
     ===================================================================== -->
<section class="py-16 lg:py-24 bg-[#e4faed] border-y border-emerald-100">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">

      <div class="lg:col-span-5 space-y-6">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white text-emerald-700 text-xs font-bold uppercase tracking-wider border border-emerald-200 shadow-sm">
          About This Lab
        </div>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-navy leading-tight">
          What Is Skillvation Lab?
        </h2>
        <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
          Skillvation Lab is a multi-disciplinary skill-learning environment designed to bring practical, experiential and future-focused learning into the school ecosystem.
        </p>
        <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
          A school today is expected to do more than deliver academic knowledge. Students need opportunities to create, experiment, solve problems, use technology, communicate ideas and apply what they learn in real-life situations. Skillvation Lab makes this possible.
        </p>
        <div class="flex flex-col gap-3 pt-2">
          @foreach([
            'Integrated multi-domain skill learning environment',
            'Complete solution: infrastructure, equipment & curriculum',
            'Designed for ICSE, State Board and academic enhancement',
          ] as $point)
          <div class="flex items-start gap-3 bg-white rounded-xl p-3.5 border border-emerald-100 shadow-sm">
            <i class="fa-solid fa-check-circle text-emerald-600 mt-0.5 flex-shrink-0"></i>
            <span class="text-sm font-semibold text-slate-700">{{ $point }}</span>
          </div>
          @endforeach
        </div>
      </div>

      <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="rounded-2xl overflow-hidden border-2 border-emerald-200 shadow-lg aspect-[4/3] bg-slate-100 group">
          <img src="{{ asset('frontend/img/skillbox/Workonlifeforms.jpeg') }}"
               alt="Students engaged in hands-on skill learning"
               class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
        </div>
        <div class="rounded-2xl overflow-hidden border-2 border-emerald-200 shadow-lg aspect-[4/3] bg-slate-100 group">
          <img src="{{ asset('frontend/img/skillbox/homeimg01.jpeg') }}"
               alt="Skillvation lab in a school"
               class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
        </div>
      </div>

    </div>
  </div>
</section>

<!-- =====================================================================
     11 SKILL DOMAINS
     ===================================================================== -->
<section class="py-16 lg:py-24 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="text-center max-w-3xl mx-auto mb-14 space-y-3">
      <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold uppercase tracking-wider">
        What the Lab Can Bring
      </div>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-navy">11 Skill Learning Domains</h2>
      <p class="text-base text-slate-600">The lab can be configured to bring together any combination of these skill learning areas.</p>
    </div>

    @php
    $domains = [
      ['fa-robot',         'bg-emerald-100','text-emerald-600','Robotics & Coding',          'Hands-on robotics kits, coding platforms, and engineering challenges.'],
      ['fa-brain',         'bg-blue-100',   'text-blue-600',   'Artificial Intelligence',     'Introductory AI concepts, tools, and real-world application projects.'],
      ['fa-flask',         'bg-purple-100', 'text-purple-600', 'STEM & Science Exploration',  'Physics, chemistry, biology experiments and science-based project learning.'],
      ['fa-bolt',          'bg-yellow-100', 'text-yellow-600', 'Electronics & Tinkering',     'Breadboards, circuits, components, and electronics prototyping tools.'],
      ['fa-lightbulb',     'bg-orange-100', 'text-orange-600', 'Design Thinking',             'Structured design challenges, prototyping, and creative problem-solving.'],
      ['fa-calculator',    'bg-cyan-100',   'text-cyan-600',   'Mathematics & Financial Literacy','Applied maths, data tools, and real-world financial literacy programmes.'],
      ['fa-paint-brush',   'bg-pink-100',   'text-pink-600',   'Art, Craft & Handicrafts',    'Creative art projects, traditional crafts, and design-based skill activities.'],
      ['fa-bullhorn',      'bg-red-100',    'text-red-600',    'Media & Communication',        'Storytelling, content creation, public speaking, and media literacy.'],
      ['fa-laptop-code',   'bg-indigo-100', 'text-indigo-600', 'Digital Skills',              'Digital literacy, productivity tools, internet safety, and basic computing.'],
      ['fa-heart',         'bg-rose-100',   'text-rose-600',   'Life Skills',                 'Communication, collaboration, emotional intelligence, and leadership.'],
      ['fa-briefcase',     'bg-teal-100',   'text-teal-600',   'Vocational & Application Skills','Real-world vocational exposure and application-based skill development.'],
    ];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
      @foreach($domains as [$icon, $iconBg, $iconColor, $title, $desc])
      <div class="bg-[#e4faed] rounded-2xl p-5 border border-emerald-100 flex gap-4 items-start hover:shadow-md transition-shadow">
        <div class="w-11 h-11 rounded-xl {{ $iconBg }} {{ $iconColor }} flex items-center justify-center text-lg flex-shrink-0 mt-0.5">
          <i class="fa-solid {{ $icon }}"></i>
        </div>
        <div>
          <h3 class="text-sm font-bold text-brand-navy mb-1">{{ $title }}</h3>
          <p class="text-xs text-slate-600 leading-relaxed">{{ $desc }}</p>
        </div>
      </div>
      @endforeach
    </div>

  </div>
</section>

  <!-- ==================== PRICING PLANS & CUSTOM PACKAGE BUILDER ==================== -->
  <section class="vl-section bg-peach" id="plans">
    <div class="vidyalab-wrap">
      <div class="vl-section-head center">
        <div class="vl-eyebrow purple">
          <span>★</span> Skillvation Lab Packages
        </div>
        <h3>Explore Components in the Skillvation Lab Kit</h3>
        <!-- <h2>{{ ($setting->lab_customization_status ?? 'active') == 'active' ? 'Choose or customize the right lab package for' : 'Choose the right lab package for' }} <span class="vl-grad-violet">your campus</span></h2> -->
        <!-- <p>Every tier includes complete hardware setup, curriculum integration, and teacher certification.{{ ($setting->lab_customization_status ?? 'active') == 'active' ? ' Select a standard plan or use our intelligent advisor to tailor by your budget & student cohort.' : '' }}</p> -->
      </div>

      @if(($setting->lab_customization_status ?? 'active') == 'active')
      {{-- Mode Switcher Tabs --}}
      <div class="vl-pkg-toggle-wrapper">
        <div class="vl-pkg-toggle-pill" role="tablist">
          <button type="button" class="vl-pkg-tab-btn active" id="tabFixedPlans" onclick="switchPackageTab('fixed')" role="tab" aria-selected="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
            <span>Standard Packages</span>
          </button>
          <button type="button" class="vl-pkg-tab-btn" id="tabCustomAdvisor" onclick="switchPackageTab('custom')" role="tab" aria-selected="false">
            <span class="vl-sparkle-pill">✨ Smart Recommender</span>
            <span>Custom Package Advisor</span>
          </button>
        </div>
      </div>
      @endif

      {{-- VIEW 1: Standard Turnkey Packages --}}
      <div id="fixedPackagesView">
        <div id="skill-sectors-section" style="margin-top:56px;">
          <div class="max-w-7xl mx-auto">

            <!-- <div class="text-center max-w-2xl mx-auto mb-12">
              <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-navy/10 text-brand-navy text-xs font-bold uppercase tracking-wider mb-3">
                Skillvation Lab
              </div>
              <h2 class="text-2xl sm:text-3xl font-extrabold text-brand-navy leading-tight">
                Explore Components in the Skillvation Lab Kit
              </h2>
              <p class="mt-3 text-slate-500 text-sm sm:text-base">
                Browse the curated equipment, activity items, and consumable resources included in the Skillvation Lab.
              </p>
            </div> -->

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
                  class="sks-nav-btn sks-nav-prev">
                  <i class="fa-solid fa-chevron-left" style="font-size:12px;"></i>
                </button>
                <button id="sks-box-next" type="button" aria-label="Next"
                  class="sks-nav-btn sks-nav-next">
                  <i class="fa-solid fa-chevron-right" style="font-size:12px;"></i>
                </button>

                <div style="overflow:hidden;border-radius:12px;" id="sks-carousel-viewport">
                  <div id="sks-boxes"
                       style="display:flex;gap:16px;transition:transform .45s cubic-bezier(.25,1,.5,1);will-change:transform;align-items:flex-start;"></div>
                </div>

                {{-- Dots pagination indicator --}}
                <div id="sks-dots" style="display:flex;justify-content:center;align-items:center;gap:8px;margin-top:18px;"></div>
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
                    <i class="fa-solid fa-sliders" style="font-size:11px;"></i> Customise This Lab
                  </a>
                  <a href="#planEnquiryForm"
                     style="display:inline-flex;align-items:center;gap:8px;padding:11px 22px;border-radius:10px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);color:#fff;font-size:13px;font-weight:700;text-decoration:none;transition:all .2s;white-space:nowrap;">
                    <i class="fa-solid fa-file-lines" style="font-size:11px;"></i> Request Proposal
                  </a>
                </div>
              </div>

              {{-- Count Badge --}}
              <div style="text-align:center;margin-top:20px;">
                <span style="display:inline-flex;align-items:center;gap:8px;padding:6px 16px;border-radius:20px;background:rgba(11,37,69,.05);border:1px solid rgba(11,37,69,.1);color:#0b2545;font-size:11px;font-weight:700;">
                  <i class="fa-solid fa-circle-check" style="color:#22c55e;"></i>
                  <span id="sks-count">…</span> categories · <span id="sks-items">…</span> items in this lab
                </span>
              </div>
            </div>

          </div>
        </div>
      </div>

      @if(($setting->lab_customization_status ?? 'active') == 'active')
      {{-- VIEW 2: Custom Package Advisor & Recommender --}}
      <div id="customPackagesView" class="vl-custom-pkg-builder">
        {{-- Step 1: Campus Profile & Investment Baseline (Educator-First) --}}
        <div class="vl-edu-campus-bar">
          <div class="vl-edu-bar-header">
            <div>
              <div class="vl-eyebrow purple" style="margin-bottom:4px;">
                <span>★</span> Step 1: School Profile &amp; Investment Baseline
              </div>
              <h3 style="font-size:1.35rem;font-weight:800;color:#1e1b4b;margin:0 0 4px;">Configure for Your Campus Footprint</h3>
              <p style="font-size:0.9rem;color:#64748b;margin:0;">Select your starting tier or fine-tune budget and student batch size. Workstations, weekly rotation, and CBSE compliance calculate automatically.</p>
            </div>
            <div class="vl-edu-kpi-pill">
              <span style="font-size:0.75rem;color:#64748b;font-weight:700;text-transform:uppercase;">Estimated Investment</span>
              <span id="eduPerStudentTop" style="font-size:1.25rem;font-weight:900;color:#4338ca;">₹571 <span style="font-size:0.8rem;font-weight:600;color:#64748b;">/ student / yr</span></span>
            </div>
          </div>

          <div class="vl-edu-params-grid">
            {{-- Parameter 1: Base Tier & Budget --}}
            <div class="vl-edu-param-col">
              <label class="vl-edu-param-label">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                <span>Target Lab Budget:</span>
                <span id="budgetValueDisplay" style="color:#4338ca;font-weight:800;margin-left:auto;">₹6.00 Lakh</span>
              </label>
              
              <div class="vl-edu-tier-btns">
                <button type="button" class="vl-edu-tier-btn" data-cat="essential" onclick="selectBudgetCategory('essential')">
                  <strong>Basic</strong> · ₹3L
                </button>
                <button type="button" class="vl-edu-tier-btn active" data-cat="growth" onclick="selectBudgetCategory('growth')">
                  <strong>Advance</strong> · ₹6L <span class="vl-mini-star">★ Rec</span>
                </button>
                <button type="button" class="vl-edu-tier-btn" data-cat="premium" onclick="selectBudgetCategory('premium')">
                  <strong>Premium</strong> · ₹10L
                </button>
              </div>

              <div class="vl-cp-slider-wrap" style="margin-top:8px;">
                <input type="range" min="2.5" max="15" step="0.25" value="6.0" class="vl-cp-slider" id="budgetSlider" oninput="onBudgetSliderChange(this.value)">
                <div class="vl-cp-slider-ticks">
                  <span>₹2.5L</span>
                  <span>₹6.0L (Recommended)</span>
                  <span>₹10.0L</span>
                  <span>₹15L+</span>
                </div>
              </div>
            </div>

            {{-- Parameter 2: Student Cohort --}}
            <div class="vl-edu-param-col">
              <label class="vl-edu-param-label">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ec4899" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                <span>Student Cohort (Grades 6–12):</span>
                <span id="studentCountBadge" style="color:#db2777;font-weight:800;margin-left:auto;">350 Students</span>
              </label>

              <div class="vl-edu-tier-btns">
                <button type="button" class="vl-edu-chip" onclick="setStudentCount(150)">150</button>
                <button type="button" class="vl-edu-chip active" onclick="setStudentCount(350)">350</button>
                <button type="button" class="vl-edu-chip" onclick="setStudentCount(650)">650</button>
                <button type="button" class="vl-edu-chip" onclick="setStudentCount(1000)">1000+</button>
              </div>

              <div class="vl-cp-slider-wrap" style="margin-top:8px;">
                <input type="range" min="50" max="1500" step="25" value="350" class="vl-cp-slider" id="studentSlider" oninput="onStudentSliderChange(this.value)">
                <div class="vl-cp-slider-ticks">
                  <span>50</span>
                  <span>500</span>
                  <span>1,000</span>
                  <span>1,500+</span>
                </div>
              </div>
            </div>

            {{-- Parameter 3: Academic Capacity & Timetable Schedule --}}
            <div class="vl-edu-param-col">
              <label class="vl-edu-param-label">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <span>Lab Seating &amp; Rotation:</span>
              </label>

              <div class="vl-edu-stat-duo">
                <div class="vl-edu-stat-item">
                  <div class="vl-edu-stat-num" id="eduWorkstationsTop">12 Dual Units</div>
                  <div class="vl-edu-stat-sub">Seats 24 Students / Shift</div>
                </div>
                <div class="vl-edu-stat-item">
                  <div class="vl-edu-stat-num" id="eduShiftsTop">14 Weekly Shifts</div>
                  <div class="vl-edu-stat-sub">100% Practical Kit Access</div>
                </div>
              </div>

              <div class="vl-edu-grade-row" id="gradePills">
                <span style="font-size:0.75rem;font-weight:700;color:#64748b;">Grades:</span>
                <button type="button" class="vl-edu-mini-pill active" data-grade="6-8" onclick="toggleGradePill(this)">VI–VIII</button>
                <button type="button" class="vl-edu-mini-pill active" data-grade="9-10" onclick="toggleGradePill(this)">IX–X</button>
                <button type="button" class="vl-edu-mini-pill active" data-grade="11-12" onclick="toggleGradePill(this)">XI–XII</button>
              </div>
            </div>
          </div>
        </div>

        {{-- Step 2: Educational Component Selection & AI Intelligence Studio --}}
        <div class="vl-ps-section" id="productSelectionSection">
          <div class="vl-ps-header">
            <div class="vl-ps-title-wrap">
              <div class="vl-eyebrow purple" style="margin-bottom:4px;">
                <span>★</span> Step 2: Curriculum Modules &amp; Component Selector
              </div>
              <h3>Select Lab Components Across Any Package with <span class="vl-grad-violet">AI Intelligence</span></h3>
              <p>Explore and customize equipment across Basic, Advance, and Premium packages. The intelligent engine validates CBSE Circular Skill-75/2024 compliance and balances your campus budget in real time.</p>
            </div>
            <!-- <div class="vl-ps-header-actions">
              <button type="button" class="vl-ps-cat-suggest-btn" id="psCatSuggestBtn" onclick="autoSuggestForActiveCategory()">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
                <span id="psCatSuggestBtnLabel">Suggest for Current Category</span>
              </button>
              <button type="button" class="vl-ps-auto-suggest-btn" onclick="autoSuggestProductsForBudget()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                <span>Auto-Suggest Best Mix for Budget</span>
              </button>
            </div> -->
          </div>

          {{-- Intelligence Meter Strip --}}
          <div class="vl-ps-intel-box">
            <div class="vl-ps-intel-row">
              <div class="vl-ps-intel-stat">
                <span class="vl-ps-intel-label">Target Budget:</span>
                <span class="vl-ps-intel-val" id="psTargetBudget">₹6.00 L</span>
              </div>
              <div class="vl-ps-intel-stat">
                <span class="vl-ps-intel-label">Selected Products Total:</span>
                <span class="vl-ps-intel-val" style="color:#6d28d9;" id="psSelectedTotal">₹0</span>
              </div>
              <div class="vl-ps-intel-stat">
                <span class="vl-ps-intel-label">Budget Balance:</span>
                <span class="vl-ps-intel-val" style="color:#059669;" id="psRemainingBudget">+₹6.00 L</span>
              </div>
              <div class="vl-ps-intel-stat">
                <span class="vl-ps-intel-label">Products Selected:</span>
                <span class="vl-ps-intel-val" style="color:#db2777;" id="psSelectedCount">0 Items</span>
              </div>
            </div>

            {{-- Progress bar --}}
            <div class="vl-ps-progress-track">
              <div class="vl-ps-progress-fill" id="psProgressFill" style="width: 0%;"></div>
            </div>

            <div class="vl-ps-intel-feedback">
              <div class="vl-ps-feedback-msg safe" id="psFeedbackMsg">
                <span>💡</span>
                <span id="psFeedbackText">Click "Auto-Suggest Best Mix" or check individual products below to build your custom package.</span>
              </div>

            </div>
          </div>

          {{-- Filter by Category — pills generated dynamically from the live product categories --}}
          <div id="psCatFilterBar" style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin-bottom:20px;padding-bottom:16px;border-bottom:1.5px solid #f1f5f9;">
            <span style="font-size:0.8rem;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:0.04em;margin-right:4px;">Filter by:</span>
            <button type="button" class="vl-ps-pill active" data-cat-filter="all" onclick="setCategoryFilter('all', this)">All Components</button>
          </div>

          {{-- Hidden inputs to keep JS working (package filter defaults to all) --}}
          <input type="hidden" id="psSearchInput" value="">
          <span id="badgePkgAll" style="display:none;">0</span>
          <span id="badgePkgBasic" style="display:none;">0</span>
          <span id="badgePkgAdvance" style="display:none;">0</span>
          <span id="badgePkgPremium" style="display:none;">0</span>

          {{-- Product Cards Grid --}}
          <div class="vl-ps-grid" id="psProductsGrid">
            {{-- Dynamically populated by JS --}}
          </div>

          {{-- Summary & CTA Bottom Bar --}}
          <div class="vl-ps-summary-bar">
            <div class="vl-ps-sum-metrics">
              <div>
                <div class="vl-ps-sum-val" id="psBarTotal">₹0</div>
                <div class="vl-ps-sum-label">Custom Selection Total</div>
              </div>
              <div>
                <div class="vl-ps-sum-val" style="color:#a7f3d0;" id="psBarPerStudent">₹0 / student</div>
                <div class="vl-ps-sum-label">Cost per Student (<span id="psBarStudentCount">350</span> Students)</div>
              </div>
              <div>
                <div class="vl-ps-sum-val" style="color:#e0e7ff;" id="psBarItemCount">0 Items</div>
                <div class="vl-ps-sum-label">Selected Components</div>
              </div>
            </div>

            <div class="vl-ps-sum-actions">
              <button type="button" class="vl-ps-reset-btn" onclick="clearSelectedProducts()">
                Reset Selection
              </button>
              <button type="button" class="vl-ps-order-btn" onclick="openCustomProposalModal()">
                Request Proposal for Selected Items
                <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
              </button>
            </div>
          </div>

        </div>
      </div>
      @endif

      <p style="margin-top:30px; font-size:0.92rem; color:var(--vl-muted); text-align:center; font-weight:500;">
        All package prices and suggestions are indicative turnkey estimates. Exact formal quote is finalized after free architectural &amp; electrical spatial assessment. &nbsp;·&nbsp;
        <a href="#demo" style="color:#db2777; font-weight:800; text-decoration:underline;">Contact our advisors</a> for trust &amp; multi-branch group concessions.
      </p>
    </div>
  </section>

<!-- =====================================================================
     WHY SKILLVATION LAB
     ===================================================================== -->
<section class="py-16 lg:py-24 bg-slate-50 border-y border-slate-100">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="text-center max-w-3xl mx-auto mb-14 space-y-3">
      <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-navy/10 text-brand-navy text-xs font-bold uppercase tracking-wider">
        Why Skillvation Lab
      </div>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-navy">Learning Beyond the Classroom</h2>
      <p class="text-base text-slate-600 max-w-2xl mx-auto">A well-designed skill lab gives students the opportunity to learn by doing. Instead of only reading about a concept, students can:</p>
      <div class="flex flex-wrap justify-content-center gap-2 pt-1">
        @foreach(['Explore','Experiment','Create','Solve','Present','Reflect'] as $step)
        <span class="inline-flex items-center gap-1.5 bg-white border border-brand-orange/30 text-brand-navy font-bold text-xs px-3 py-1.5 rounded-full shadow-sm">
          <span class="w-1.5 h-1.5 rounded-full bg-brand-orange inline-block"></span>{{ $step }}
        </span>
        @endforeach
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
      <div class="rounded-2xl overflow-hidden shadow-xl border border-slate-200 aspect-[4/3]">
        <img src="{{ asset('frontend/img/skillbox/teacher_upskilling.jpg') }}"
             alt="Teacher and students in Skillvation Lab"
             class="w-full h-full object-cover" />
      </div>
      <div class="space-y-4">
        <p class="text-sm text-slate-600 leading-relaxed">Schools following ICSE or State Board curricula can use Skillvation Lab as an academic enhancement initiative to strengthen practical, experiential and future-ready learning.</p>
        @php
        $whyPoints = [
          ['fa-microchip',       'text-emerald-600', 'Introduce hands-on STEM and technology learning'],
          ['fa-link',            'text-blue-600',    'Strengthen practical application of concepts'],
          ['fa-lightbulb',       'text-orange-600',  'Build innovation and maker culture'],
          ['fa-satellite-dish',  'text-purple-600',  'Provide exposure to emerging technologies'],
          ['fa-comments',        'text-rose-600',    'Develop communication and life skills'],
          ['fa-folder-open',     'text-teal-600',    'Offer project-based learning experiences'],
          ['fa-layer-group',     'text-indigo-600',  'Create differentiated learning opportunities'],
          ['fa-graduation-cap',  'text-yellow-600',  'Strengthen the school\'s overall learning environment'],
        ];
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          @foreach($whyPoints as [$icon, $color, $text])
          <div class="flex items-start gap-3 bg-white rounded-xl p-3 border border-slate-200 shadow-sm">
            <i class="fa-solid {{ $icon }} {{ $color }} mt-0.5 text-sm flex-shrink-0"></i>
            <span class="text-xs font-semibold text-slate-700">{{ $text }}</span>
          </div>
          @endforeach
        </div>
      </div>
    </div>

  </div>
</section>

<!-- =====================================================================
     MORE THAN A LAB — 6 COMPONENTS
     ===================================================================== -->
<section class="py-16 lg:py-24 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="text-center max-w-3xl mx-auto mb-14 space-y-3">
      <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold uppercase tracking-wider">
        A Complete Solution
      </div>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-navy">More Than a Lab. A Learning Ecosystem.</h2>
      <p class="text-base text-slate-600">Skillvation Lab is designed as a complete solution rather than simply supplying equipment.</p>
    </div>

    @php
    $components = [
      ['1','fa-building',          'bg-emerald-100','text-emerald-600', 'Lab Infrastructure',
       'Space planning, furniture, workstations, storage and essential lab infrastructure designed around student activities.'],
      ['2','fa-box-open',          'bg-blue-100',   'text-blue-600',    'Equipment & Materials',
       'Age-appropriate tools, equipment, components, activity materials and consumables based on the selected lab configuration.'],
      ['3','fa-book-open',         'bg-purple-100', 'text-purple-600',  'Curriculum & Activities',
       'Structured activities and projects that enable students to use the lab meaningfully rather than simply observe demonstrations.'],
      ['4','fa-chalkboard-user',   'bg-orange-100', 'text-orange-600',  'Teacher Enablement',
       'Teacher orientation, training and implementation support to help educators confidently conduct practical learning sessions.'],
      ['5','fa-screwdriver-wrench','bg-rose-100',   'text-rose-600',    'Student Projects',
       'Hands-on projects that encourage students to design, build, test, modify and present their work.'],
      ['6','fa-headset',           'bg-teal-100',   'text-teal-600',    'Ongoing Support',
       'Implementation guidance and support to help the school keep the lab active, relevant and effectively utilised.'],
    ];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      @foreach($components as [$num, $icon, $iconBg, $iconColor, $title, $desc])
      <div class="bg-[#e4faed] rounded-2xl p-6 border border-emerald-100 space-y-3 hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg bg-brand-navy text-white flex items-center justify-center text-xs font-black flex-shrink-0">{{ $num }}</div>
          <div class="w-10 h-10 rounded-xl {{ $iconBg }} {{ $iconColor }} flex items-center justify-center text-lg">
            <i class="fa-solid {{ $icon }}"></i>
          </div>
        </div>
        <h3 class="text-base font-bold text-brand-navy">{{ $title }}</h3>
        <p class="text-sm text-slate-600 leading-relaxed">{{ $desc }}</p>
      </div>
      @endforeach
    </div>

  </div>
</section>

<!-- =====================================================================
     BENEFITS — dark navy band
     ===================================================================== -->
<section class="py-16 lg:py-24 bg-brand-navy">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="text-center max-w-3xl mx-auto mb-14 space-y-3">
      <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 text-white text-xs font-bold uppercase tracking-wider">
        Benefits to the School
      </div>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-white">What Your School Gains</h2>
    </div>

    @php
    $benefits = [
      ['fa-magnifying-glass', 'Strengthens Experiential Learning',
       'Provides students with opportunities to apply classroom concepts through practical activities and projects.'],
      ['fa-rocket',           'Builds a Future-Ready Learning Environment',
       'Introduces students to technology, innovation, problem-solving and emerging skill areas.'],
      ['fa-star',             'Enhances Student Engagement',
       'Hands-on learning encourages students to participate, explore and take ownership of their learning.'],
      ['fa-medal',            'Supports School Differentiation',
       'A dedicated skill-learning environment can become an important part of the school\'s academic and infrastructure offering.'],
      ['fa-eye',              'Creates Visible Learning Experiences',
       'Student projects, prototypes, exhibitions and demonstrations provide tangible evidence of learning.'],
      ['fa-people-group',     'Supports Parent & Community Engagement',
       'A well-utilised skill lab becomes a space for exhibitions, showcases, workshops, competitions and parent activities.'],
      ['fa-puzzle-piece',     'Develops Skills Beyond Academics',
       'Students build confidence, collaboration, creativity, communication and practical problem-solving abilities.'],
    ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
      @foreach($benefits as [$icon, $title, $desc])
      <div class="bg-white/10 border border-white/10 rounded-2xl p-5 space-y-3 hover:bg-white/15 transition-colors">
        <div class="w-11 h-11 rounded-xl bg-white/20 text-white flex items-center justify-center text-lg">
          <i class="fa-solid {{ $icon }}"></i>
        </div>
        <h3 class="text-sm font-bold text-white">{{ $title }}</h3>
        <p class="text-xs text-emerald-100 leading-relaxed">{{ $desc }}</p>
      </div>
      @endforeach
    </div>

  </div>
</section>

<!-- =====================================================================
     A LAB THAT GROWS WITH YOUR SCHOOL
     ===================================================================== -->
<section class="py-16 lg:py-24 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="text-center max-w-3xl mx-auto mb-14 space-y-3">
      <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold uppercase tracking-wider">
        Flexible by Design
      </div>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-navy">A Lab That Grows With Your School</h2>
      <p class="text-base text-slate-600">Every school has different requirements. That is why Skillvation Lab can be planned based on:</p>
    </div>

    @php
    $configs = [
      ['fa-school',         'bg-emerald-50','border-emerald-200','text-emerald-700','School Board',
       'ICSE | State Board',
       'Aligned to your board\'s curriculum and inspection requirements.'],
      ['fa-graduation-cap', 'bg-blue-50',   'border-blue-200',   'text-blue-700',  'Grades',
       'Primary | Middle School | Secondary',
       'Lab design and content calibrated to the right age group.'],
      ['fa-users',          'bg-purple-50', 'border-purple-200', 'text-purple-700','Student Strength',
       'Lab Capacity Designed Around Your School',
       'Lab size, workstations, and kit quantities matched to your student population.'],
      ['fa-ruler-combined', 'bg-orange-50', 'border-orange-200', 'text-orange-700','Available Space',
       'Planned Around Your Infrastructure',
       'Lab layout designed to fit your existing space with minimal civil work.'],
      ['fa-bullseye',       'bg-rose-50',   'border-rose-200',   'text-rose-700',  'School Vision',
       'Compliance | Enhancement | STEM | Innovation',
       'Configuration aligned to your academic and development objectives.'],
      ['fa-tags',           'bg-teal-50',   'border-teal-200',   'text-teal-700',  'Budget',
       'Multiple Configurations Available',
       'Modular options to suit different investment levels and timelines.'],
    ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
      @foreach($configs as [$icon, $bg, $border, $textColor, $title, $subtitle, $desc])
      <div class="rounded-2xl p-6 {{ $bg }} border {{ $border }} space-y-3 hover:shadow-md transition-shadow">
        <div class="w-11 h-11 rounded-xl bg-white {{ $textColor }} flex items-center justify-center text-lg shadow-sm">
          <i class="fa-solid {{ $icon }}"></i>
        </div>
        <div>
          <h3 class="text-sm font-black text-brand-navy">{{ $title }}</h3>
          <p class="text-xs font-bold {{ $textColor }} mt-0.5">{{ $subtitle }}</p>
        </div>
        <p class="text-xs text-slate-600 leading-relaxed">{{ $desc }}</p>
      </div>
      @endforeach
    </div>

  </div>
</section>

<!-- =====================================================================
     FROM INSTALLATION TO IMPLEMENTATION — 6 STEPS
     ===================================================================== -->
<section class="py-16 lg:py-24 bg-slate-50 border-y border-slate-100">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="text-center max-w-3xl mx-auto mb-14 space-y-3">
      <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-navy/10 text-brand-navy text-xs font-bold uppercase tracking-wider">
        Our Process
      </div>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-navy">From Installation to Implementation</h2>
      <p class="text-base text-slate-600">Setting up a lab is only the beginning. Skillvation supports schools through the entire journey.</p>
    </div>

    @php
    $steps = [
      ['fa-comments',        '#f97316','Understand', 'We understand your board, curriculum, grades, student strength and objectives.'],
      ['fa-pencil-ruler',    '#8b5cf6','Plan',        'We design the lab according to your available space and requirements.'],
      ['fa-screwdriver-wrench','#0ea5e9','Set Up',    'Infrastructure, equipment, materials and resources are installed and organised.'],
      ['fa-chalkboard-user', '#16a34a','Enable',      'Teachers receive orientation and support to use the lab effectively.'],
      ['fa-flask',           '#e63946','Implement',   'Students engage in structured activities, projects and practical learning.'],
      ['fa-star',            '#ca8a04','Showcase',    'Student work is presented through exhibitions, demonstrations and school events.'],
    ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
      @foreach($steps as $i => [$icon, $color, $title, $desc])
      <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow flex gap-4 items-start">
        <div class="flex-shrink-0">
          <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white text-base font-black shadow"
               style="background:{{ $color }};">{{ $i + 1 }}</div>
        </div>
        <div>
          <div class="flex items-center gap-2 mb-1.5">
            <i class="fa-solid {{ $icon }} text-sm" style="color:{{ $color }};"></i>
            <h3 class="text-sm font-black text-brand-navy">{{ $title }}</h3>
          </div>
          <p class="text-xs text-slate-600 leading-relaxed">{{ $desc }}</p>
        </div>
      </div>
      @endforeach
    </div>

  </div>
</section>

<!-- =====================================================================
     OTHER LABS
     ===================================================================== -->
<section class="py-16 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-10">
      <h2 class="text-2xl sm:text-3xl font-extrabold text-brand-navy mb-2">Explore Our Innovation Labs</h2>
      <p class="text-slate-500 text-sm">Comprehensive hands-on STEM &amp; Skill solutions for modern schools.</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
      @foreach([
        [route('labs.composite-skill-basic'), 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=500&q=80', 'Basic Package', 'Hands-on robotics, coding & AI experiments'],
        [route('labs.composite-skill-advance'),'https://images.unsplash.com/photo-1635070041078-e363dbe005cb?auto=format&fit=crop&w=500&q=80', 'Advanced Package', 'Electronics, IoT & project-based learning'],
        [route('labs.composite-skill-premium'), 'https://images.unsplash.com/photo-1516339901601-2e1b62dc0c45?auto=format&fit=crop&w=500&q=80', 'Premium Package', 'Early childhood exploration & creativity'],
      ] as [$url, $img, $name, $desc])
      
      <a href="{{ $url }}" class="group flex flex-col gap-3 p-4 rounded-2xl border border-slate-200 hover:border-brand-orange hover:shadow-lg transition-all duration-200 bg-white">
        <div class="aspect-[4/3] rounded-xl overflow-hidden bg-slate-100">
          <img src="{{ $img }}" alt="{{ $name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
        </div>
        <div>
          <p class="font-bold text-brand-navy group-hover:text-brand-orange transition-colors text-sm">{!! $name !!}</p>
          <p class="text-xs text-slate-500 mt-0.5">{!! $desc !!}</p>
        </div>
      </a>
      @endforeach
    </div>
  </div>
</section>

<!-- =====================================================================
     ENQUIRY FORM
     ===================================================================== -->
<section class="py-16 lg:py-24 bg-[#fedecf] border-y border-brand-orange/20" id="demo">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">

      <!-- Left: CTA copy -->
      <div class="lg:col-span-5 space-y-6">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white text-brand-orange text-xs font-bold uppercase tracking-wider shadow-sm">
          Let's Build Your Skill Lab
        </div>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-navy leading-tight">
          Build a School Where Students Learn by Doing.
        </h2>
        <p class="text-base text-slate-700 leading-relaxed">
          Whether your school is looking to strengthen experiential learning, introduce future-ready skills, or enhance its academic environment — Skillvation can help you plan and implement the right lab solution.
        </p>
        <ul class="space-y-3">
          @foreach([
            'Free lab consultation and space assessment',
            'Custom configuration based on board & grades',
            'Transparent pricing with multiple budget options',
            'End-to-end support from planning to implementation',
          ] as $item)
          <li class="flex items-center gap-3 text-sm text-slate-700 font-semibold">
            <span class="w-5 h-5 rounded-full bg-brand-orange/20 text-brand-orange flex items-center justify-center text-xs flex-shrink-0">✓</span>
            {{ $item }}
          </li>
          @endforeach
        </ul>
        <p class="text-sm text-slate-600 font-semibold pt-2">
          📞 98450 26782 &nbsp;·&nbsp; ✉️ infomyskoolonline@gmail.com
        </p>
      </div>

      <!-- Right: Form -->
      <div class="lg:col-span-7">
        <div class="bg-white/95 rounded-3xl p-6 sm:p-8 shadow-xl border border-white/80">
          <h3 class="text-lg font-black text-brand-navy mb-1">Talk to Our Team</h3>
          <p class="text-xs text-slate-500 mb-6">Fill out the form and we'll reach out to explore the right Skillvation Lab for your school.</p>

          <form class="space-y-4" id="planEnquiryForm"
                data-source="skillvation-lab-stem"
                data-title="Skillvation Lab — Enquiry">
            <input type="hidden" name="source"       id="planSource">
            <input type="hidden" name="course_title" id="planTitle">
            <input type="hidden" name="quotation"    id="planQuotation">

            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">School Name *</label>
              <input type="text" name="school" required placeholder="e.g. National Public School"
                     class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm focus:border-brand-navy focus:outline-none focus:ring-2 focus:ring-brand-navy/20 transition-all" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Contact Person *</label>
                <input type="text" name="name" required placeholder="Your full name"
                       class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm focus:border-brand-navy focus:outline-none focus:ring-2 focus:ring-brand-navy/20 transition-all" />
              </div>
              <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Designation</label>
                <input type="text" name="designation" placeholder="e.g. Principal / Coordinator"
                       class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm focus:border-brand-navy focus:outline-none focus:ring-2 focus:ring-brand-navy/20 transition-all" />
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="sm:col-span-2">
                @include('frontend.home-four.partials._phone_otp', [
                  'formId'       => 'planEnquiryForm',
                  'phoneInputId' => 'plan_phone',
                  'submitBtnId'  => 'planEnquiryBtn',
                  'inputClass'   => 'w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm transition-all',
                ])
              </div>
              <div class="sm:col-span-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Email Address *</label>
                <input type="email" name="email" required placeholder="principal@school.edu.in"
                       class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm focus:border-brand-navy focus:outline-none focus:ring-2 focus:ring-brand-navy/20 transition-all" />
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">City *</label>
                <input type="text" name="city" required placeholder="e.g. Bengaluru, Mumbai"
                       class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm focus:border-brand-navy focus:outline-none focus:ring-2 focus:ring-brand-navy/20 transition-all" />
              </div>
              <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">School Board</label>
                <select name="message" class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm focus:border-brand-navy focus:outline-none focus:ring-2 focus:ring-brand-navy/20 transition-all">
                  <option value="ICSE">ICSE</option>
                  <option value="State Board">State Board</option>
                  <option value="CBSE">CBSE</option>
                  <option value="Other">Other</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Message / Requirements</label>
              <textarea name="address" rows="3"
                        placeholder="Tell us about your available space, grade levels, or specific skill areas of interest..."
                        class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm focus:border-brand-navy focus:outline-none focus:ring-2 focus:ring-brand-navy/20 transition-all"></textarea>
            </div>

            <button type="submit" id="planEnquiryBtn"
                    class="w-full py-4 rounded-xl bg-brand-navy hover:bg-brand-darknavy text-white text-sm font-bold shadow-lg transition-all duration-200">
              Submit Enquiry
            </button>

            <p class="text-xs text-center text-slate-400">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:-1px;"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              Your information is confidential. No spam.
            </p>
          </form>

          <div id="planEnquirySuccess" style="display:none;" class="text-center py-8">
            <div class="text-5xl mb-4">🎉</div>
            <h3 class="text-xl font-black text-brand-navy mb-2">Thank you!</h3>
            <p class="text-sm text-slate-600">Our Skillvation Lab specialist will contact you within 1 working day to explore the right solution for your school.</p>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

@endsection

@push('scripts')
<script>
(function () {
  var form    = document.getElementById('planEnquiryForm');
  var btn     = document.getElementById('planEnquiryBtn');
  var success = document.getElementById('planEnquirySuccess');
  var srcEl   = document.getElementById('planSource');
  var ttlEl   = document.getElementById('planTitle');
  if (!form) return;
  if (srcEl) srcEl.value = form.dataset.source || '';
  if (ttlEl) ttlEl.value = form.dataset.title  || '';
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var orig = btn.textContent;
    btn.disabled = true; btn.textContent = 'Submitting…';
    fetch('{{ route("course.enquiry.store") }}', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        'Accept': 'application/json',
      },
      body: new FormData(form),
    })
    .then(function (r) { return r.json(); })
    .then(function () { form.style.display = 'none'; success.style.display = 'block'; })
    .catch(function () { form.style.display = 'none'; success.style.display = 'block'; })
    .finally(function () { btn.disabled = false; btn.textContent = orig; });
  });
}());
</script>
<script>
(function(){
'use strict';

var BASE = '<?= rtrim(env("APP_URL"), "/") ?>/club-shop';

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
  body.style.maxHeight = (body.scrollHeight + 300) + 'px';
  if(chev) chev.style.transform='rotate(180deg)';
  row.dataset.open='1';
}
function closeRow(row){
  var body=row.querySelector('.sks-body'), chev=row.querySelector('.sks-chev');
  if(body) body.style.maxHeight='0';
  if(chev) chev.style.transform='rotate(0deg)';
  row.dataset.open='0';
}

function buildRow(catName, comps, theme, productUrl, isOpenByDefault){
  var icon  = getIcon(catName);
  var rowId = 'sks-r-'+Math.random().toString(36).slice(2,8);
  var totalQ=0, totalV=0;
  comps.forEach(function(c){
    var q = c.is_optional ? 0 : Number(c.required_quantity || 0);
    totalQ += q;
    totalV += Number(c.unit_price) * q;
  });
  var subtitle = totalQ > 0
    ? rupee(totalV) + ' · ' + totalQ + ' unit' + (totalQ !== 1 ? 's' : '')
    : comps.length + ' item' + (comps.length !== 1 ? 's' : '') + ' (optional)';

  var itemRows = comps.map(function(c){
    var qty    = c.is_optional ? 0 : Number(c.required_quantity || 0);
    var line   = Number(c.unit_price) * qty;
    var opt    = c.is_optional
      ? '<span style="margin-left:5px;padding:1px 5px;border-radius:3px;background:#fef3c7;color:#92400e;border:1px solid #fde68a;font-size:9px;font-weight:700;">optional</span>'
      : '';
    var rawImg = c.image_small || c.image || '';
    var imgSrc = '';
    if(rawImg){
      if(rawImg.startsWith('http://') || rawImg.startsWith('https://') || rawImg.startsWith('/')){
        imgSrc = rawImg;
      } else {
        imgSrc = BASE + '/' + (rawImg.startsWith('uploads/') ? rawImg : 'uploads/images/' + rawImg);
      }
    }
    var img = imgSrc
      ? '<img src="'+esc(imgSrc)+'" alt="'+esc(c.title)+'" loading="lazy" '
          +'style="width:36px;height:36px;object-fit:cover;border-radius:6px;border:1px solid #e2e8f0;flex-shrink:0;" '
          +'onerror="this.style.display=\'none\';">'
      : '<span style="width:36px;height:36px;border-radius:6px;background:#f1f5f9;border:1px solid #e2e8f0;flex-shrink:0;display:inline-flex;align-items:center;justify-content:center;">'
          +'<i class="fa-solid fa-cube" style="color:#cbd5e1;font-size:12px;"></i></span>';

    return '<div style="display:flex;align-items:center;gap:12px;padding:10px 14px;border-bottom:1px solid #f1f5f9;">'
      +img
      +'<div style="flex:1;min-width:0;">'
        +'<div style="font-size:13px;font-weight:600;color:#1e293b;line-height:1.3;">'+esc(c.title)+opt+'</div>'
        +(c.sku?'<div style="font-size:10.5px;color:#94a3b8;font-family:monospace;margin-top:2px;">'+esc(c.sku)+'</div>':'')
      +'</div>'
      +'<div style="text-align:right;flex-shrink:0;">'
        +'<div style="font-size:12.5px;font-weight:700;color:'+(qty===0?'#94a3b8':theme.dotColor)+';">'+(qty===0?'—':rupee(line))+'</div>'
        +'<div style="font-size:10.5px;color:#94a3b8;">'+(qty===0?'0 (optional)':qty+' × '+rupee(c.unit_price))+'</div>'
      +'</div>'
    +'</div>';
  }).join('');

  var initialOpen = isOpenByDefault ? '1' : '0';
  var initialMaxHeight = isOpenByDefault ? 'max-height:800px;' : 'max-height:0;';
  var initialChev = isOpenByDefault ? 'transform:rotate(180deg);' : 'transform:rotate(0deg);';

  return '<div class="sks-row" id="'+rowId+'" data-open="'+initialOpen+'" '
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
      +'<i class="fa-solid fa-chevron-down sks-chev" style="font-size:11px;color:#94a3b8;transition:transform .25s;flex-shrink:0;'+initialChev+'"></i>'
    +'</button>'
    +'<div class="sks-body" style="'+initialMaxHeight+'overflow:hidden;transition:max-height .35s ease;">'
      +itemRows
      +(totalQ>0?'<div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:#f8fafc;border-top:1px solid #e2e8f0;">'
          +'<span style="font-size:11.5px;font-weight:700;color:#64748b;">'+comps.length+' item'+(comps.length!==1?'s':'')+' · '+totalQ+' units</span>'
          +'<span style="font-size:13.5px;font-weight:800;color:'+theme.dotColor+';">'+rupee(totalV)+'</span>'
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
      clearTimeout(timer); if(btn) btn.style.background=hoverBg;
    });
    row.addEventListener('mouseleave',function(){
      if(btn) btn.style.background='transparent';
    });
  });
}

function buildBox(parentName, parentId, subGroups, theme, productUrl, isCenter){
  var el = document.createElement('div');
  el.className = 'sks-box-card';
  el.style.cssText = 'border:2px solid '+theme.border+';background:'+theme.bg+';'
    +'overflow:hidden;border-radius:12px;box-sizing:border-box;'
    +(theme.shadow?'box-shadow:0 8px 24px rgba(0,0,0,.08);':'');

  var catNames = Object.keys(subGroups);

  var hdr = '<div style="background:'+theme.hdrBg+';padding:13px 18px;display:flex;align-items:center;gap:10px;">'
    +'<i class="fa-solid '+getIcon(parentName)+'" style="color:#fff;font-size:16px;"></i>'
    +'<span style="font-weight:700;color:#fff;font-size:15.5px;">'+esc(parentName)+'</span>'
    +'<span style="margin-left:auto;background:rgba(255,255,255,.22);color:#fff;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;">'
      +catNames.length+' categor'+(catNames.length!==1?'ies':'y')
    +'</span>'
  +'</div>';

  var rows = '<div data-sks-rows>'
    + catNames.map(function(cn, idx){ return buildRow(cn, subGroups[cn], theme, productUrl, idx === 0); }).join('')
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
  fetch(BASE+'/api/products/slug/skillvation-lab').then(function(r){if(!r.ok)throw r.status;return r.json();}),
  fetch(BASE+'/api/categories/tree').then(function(r){if(!r.ok)throw r.status;return r.json();}),
])
.then(function(res){
  var product    = res[0].data;
  var allCats    = res[1].data || [];
  var components = product.bundle_components || [];
  if(!components.length) throw new Error('empty bundle');

  // Store globally so the enquiry form can build a quotation
  window._sksComponents = components;

  var tree = {};
  allCats.forEach(function(c){ tree[c.id]=c; });

  var productUrl   = product.url || (BASE+'/'+product.slug);
  var customiseUrl = (BASE+'/skillvation-cart') || productUrl;

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

  if(countEl) countEl.textContent = totalCats;
  if(itemsEl) itemsEl.textContent = totalItems;
  var btnC=document.getElementById('sks-btn-customise'), btnP=document.getElementById('sks-btn-product');
  if(btnC) btnC.href = customiseUrl;
  if(btnP) btnP.href = productUrl;

  // Unhide content container BEFORE building cards and measuring layout
  if(loadEl) loadEl.classList.add('hidden');
  if(contentEl) contentEl.classList.remove('hidden');

  boxesEl.innerHTML = '';
  parentIds.forEach(function(pid){
    var g      = groups[pid];
    var theme  = getTheme(g.name);
    var isCenter = (pid===maxKey && parentIds.length>1);
    var box    = buildBox(g.name, pid, g.subGroups, theme, productUrl, isCenter);
    boxesEl.appendChild(box);
  });

  (function(){
    var GAP = 16;
    var current = 0;
    var boxes = Array.prototype.slice.call(boxesEl.children);
    var total = boxes.length;
    var prevBtn = document.getElementById('sks-box-prev');
    var nextBtn = document.getElementById('sks-box-next');
    var dotsContainer = document.getElementById('sks-dots');
    var viewport = document.getElementById('sks-carousel-viewport');

    function getVisibleCount(){
      var w = window.innerWidth;
      if (w < 640) return 1;
      if (w < 1024) return Math.min(2, total);
      return Math.min(3, total);
    }

    function getContainerW(){
      var w = 0;
      if (viewport && viewport.clientWidth > 0) {
        w = viewport.clientWidth;
      } else if (contentEl && contentEl.clientWidth > 0) {
        w = contentEl.clientWidth;
      } else if (boxesEl.parentElement && boxesEl.parentElement.clientWidth > 0) {
        w = boxesEl.parentElement.clientWidth;
      } else {
        w = window.innerWidth - 32;
      }
      return w;
    }

    function getCardW(){
      var visible = getVisibleCount();
      var containerW = getContainerW();
      var totalGap = GAP * (visible - 1);
      var w = Math.floor((containerW - totalGap) / visible);
      return Math.max(240, w);
    }

    function applyWidths(){
      var w = getCardW();
      boxes.forEach(function(b){
        b.style.width     = w + 'px';
        b.style.minWidth  = w + 'px';
        b.style.maxWidth  = w + 'px';
        b.style.flex      = '0 0 ' + w + 'px';
      });
    }

    function renderDots(){
      if (!dotsContainer) return;
      dotsContainer.innerHTML = '';
      var visible = getVisibleCount();
      var maxIdx = Math.max(0, total - visible);
      var dotCount = maxIdx + 1;
      if (dotCount <= 1) {
        dotsContainer.style.display = 'none';
        return;
      }
      dotsContainer.style.display = 'flex';
      for (var i = 0; i < dotCount; i++) {
        var dot = document.createElement('span');
        dot.className = 'sks-dot' + (i === current ? ' active' : '');
        dot.setAttribute('data-idx', i);
        dot.addEventListener('click', (function(idx){
          return function(){ go(idx); };
        })(i));
        dotsContainer.appendChild(dot);
      }
    }

    function updateDots(){
      if (!dotsContainer) return;
      var dots = dotsContainer.querySelectorAll('.sks-dot');
      dots.forEach(function(d, i){
        if (i === current) {
          d.classList.add('active');
        } else {
          d.classList.remove('active');
        }
      });
    }

    function go(idx){
      var visible = getVisibleCount();
      var maxIdx = Math.max(0, total - visible);
      if (maxIdx === 0) {
        current = 0;
      } else if (idx > maxIdx) {
        current = 0; // Infinite loop wrap-around
      } else if (idx < 0) {
        current = maxIdx; // Infinite loop wrap-around to end
      } else {
        current = idx;
      }

      var w = getCardW();
      boxesEl.style.transform = 'translateX(-' + (current * (w + GAP)) + 'px)';
      updateDots();
    }

    requestAnimationFrame(function(){
      applyWidths();
      renderDots();
      go(0);
    });

    if (prevBtn) {
      prevBtn.addEventListener('click', function(){ go(current - 1); });
    }
    if (nextBtn) {
      nextBtn.addEventListener('click', function(){ go(current + 1); });
    }

    // Touch & swipe support for mobile
    var touchStartX = 0;
    var touchEndX = 0;
    var touchStartY = 0;
    var touchEndY = 0;

    if (viewport) {
      viewport.addEventListener('touchstart', function(e){
        touchStartX = e.changedTouches[0].screenX;
        touchStartY = e.changedTouches[0].screenY;
      }, {passive: true});

      viewport.addEventListener('touchend', function(e){
        touchEndX = e.changedTouches[0].screenX;
        touchEndY = e.changedTouches[0].screenY;
        var diffX = touchStartX - touchEndX;
        var diffY = touchStartY - touchEndY;
        if (Math.abs(diffX) > 35 && Math.abs(diffX) > Math.abs(diffY)) {
          if (diffX > 0) {
            go(current + 1); // Swiped left -> next
          } else {
            go(current - 1); // Swiped right -> prev
          }
        }
      }, {passive: true});
    }

    var resizeTimer;
    window.addEventListener('resize', function(){
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(function(){
        applyWidths();
        renderDots();
        go(current);
      }, 80);
    });
  })();
})
.catch(function(e){
  console.log('sks notice (offline/static fallback):', e);
  if(loadEl) loadEl.classList.add('hidden');
  if(errEl) errEl.classList.remove('hidden');
});

})();
// ── Custom Package Advisor & Recommender Engine ────────────
    (function () {
      let state = {
        category: 'growth',
        budget: 6.0,
        students: 350,
        grades: ['6-8', '9-10', '11-12']
      };

      const baseUrls = {
        basic: '{{ url("/labs/composite-skill-basic") }}',
        advance: '{{ url("/labs/composite-skill-advance") }}',
        premium: '{{ url("/labs/composite-skill-premium") }}',
      };

      // Tab switcher
      window.switchPackageTab = function (mode) {
        const tabFixed  = document.getElementById('tabFixedPlans');
        const tabCustom = document.getElementById('tabCustomAdvisor');
        const viewFixed = document.getElementById('fixedPackagesView');
        const viewCustom= document.getElementById('customPackagesView');

        if (!viewCustom || !tabCustom) return;

        if (mode === 'custom') {
          if (tabFixed) {
            tabFixed.classList.remove('active');
            tabFixed.setAttribute('aria-selected', 'false');
          }
          tabCustom.classList.add('active');
          tabCustom.setAttribute('aria-selected', 'true');
          if (viewFixed) viewFixed.style.display  = 'none';
          viewCustom.classList.add('active');
          updateRecommendation();
        } else {
          tabCustom.classList.remove('active');
          tabCustom.setAttribute('aria-selected', 'false');
          if (tabFixed) {
            tabFixed.classList.add('active');
            tabFixed.setAttribute('aria-selected', 'true');
          }
          viewCustom.classList.remove('active');
          if (viewFixed) viewFixed.style.display  = 'block';
        }
      };

      // Budget Category Card click
      window.selectBudgetCategory = function (cat) {
        state.category = cat;
        if (cat === 'essential') state.budget = 3.0;
        else if (cat === 'growth') state.budget = 6.0;
        else if (cat === 'premium') state.budget = 10.0;

        const slider = document.getElementById('budgetSlider');
        if (slider) slider.value = state.budget;

        syncCategoryCards();
        autoSuggestProductsForBudget(false);
        updateRecommendation();
      };

      // Budget Slider Change
      window.onBudgetSliderChange = function (val) {
        state.budget = parseFloat(val);
        if (state.budget <= 4.0) state.category = 'essential';
        else if (state.budget <= 7.5) state.category = 'growth';
        else state.category = 'premium';

        syncCategoryCards();
        updateRecommendation();
      };

      function syncCategoryCards() {
        document.querySelectorAll('.vl-edu-tier-btn').forEach(card => {
          if (card.getAttribute('data-cat') === state.category) {
            card.classList.add('active');
          } else {
            card.classList.remove('active');
          }
        });
      }

      // Student count preset chips
      window.setStudentCount = function (num) {
        state.students = parseInt(num, 10);
        const slider = document.getElementById('studentSlider');
        if (slider) slider.value = state.students;

        syncStudentChips();
        autoSuggestProductsForBudget(false);
        updateRecommendation();
      };

      // Student Slider Change
      window.onStudentSliderChange = function (val) {
        state.students = parseInt(val, 10);
        syncStudentChips();
        updateRecommendation();
      };

      function syncStudentChips() {
        document.querySelectorAll('.vl-edu-chip').forEach(chip => {
          const match = chip.textContent.match(/\d+/);
          if (match && parseInt(match[0], 10) === state.students) {
            chip.classList.add('active');
          } else if (match) {
            chip.classList.remove('active');
          }
        });
      }

      // Grade Level Pills toggle
      window.toggleGradePill = function (btn) {
        btn.classList.toggle('active');
        const activeGrades = [];
        document.querySelectorAll('#gradePills .vl-edu-mini-pill.active').forEach(b => {
          activeGrades.push(b.getAttribute('data-grade'));
        });
        state.grades = activeGrades;
        updateRecommendation();
      };

      // Smart Suggestion Engine Core
      function updateRecommendation() {
        const budgetDisp = '₹' + state.budget.toFixed(2) + ' Lakh';
        const budgetValElem = document.getElementById('budgetValueDisplay');
        if (budgetValElem) budgetValElem.textContent = budgetDisp;

        const budgetCatBadge = document.getElementById('budgetCategoryBadge');
        if (budgetCatBadge) {
          if (state.category === 'essential') budgetCatBadge.textContent = 'Essential Tier';
          else if (state.category === 'growth') budgetCatBadge.textContent = 'Growth Tier';
          else budgetCatBadge.textContent = 'Premium Tier';
        }

        const studentBadge = document.getElementById('studentCountBadge');
        if (studentBadge) studentBadge.textContent = state.students + ' Students';

        // Calculated metrics
        const totalInvestmentRs = state.budget * 100000;
        const perStudentAnnual = Math.round(totalInvestmentRs / (state.students || 1));
        
        let workstations = 12;
        let kitsCount = 45;
        let recommendedTierName = 'Advance Package';
        let planUrl = baseUrls.advance;
        let planBtnText = 'View Advance Plan Details →';
        let badgeText = '★ Most Popular Campus Setup';
        let trainingText = '2 certified teachers + year-round curriculum planning & support.';
        let infraText = '';
        let kitsText = '';

        if (state.category === 'essential') {
          workstations = Math.max(8, Math.min(12, Math.round(state.students / 35)));
          kitsCount = Math.round(25 + (state.students * 0.03));
          recommendedTierName = 'Skillvation Basic Composite Skill Lab';
          planUrl = baseUrls.basic;
          planBtnText = 'View Basic Plan Details →';
          badgeText = '✓ CBSE Affiliation & Compliance Tier';
          trainingText = '1-2 teachers oriented on safety, lab inventory & CBSE activities.';
          infraText = `<strong>Lab Infrastructure:</strong> ${workstations} dual workstations (seats ${workstations * 2} students), safety busbars, tool racks & master teacher desk.`;
          kitsText = `<strong>Projects & DIY Kits:</strong> ${kitsCount}+ essential hands-on kits covering core CBSE mechanics, electronics & bio-tinkering.`;
        } else if (state.category === 'growth') {
          workstations = Math.max(12, Math.min(18, Math.round(state.students / 25)));
          kitsCount = Math.round(45 + (state.students * 0.04));
          recommendedTierName = 'Skillvation Advance Composite Skill Lab';
          planUrl = baseUrls.advance;
          planBtnText = 'View Advance Plan Details →';
          badgeText = '★ Most Popular Practical Campus Setup';
          trainingText = '2-3 certified teachers + 12-month ongoing lesson planning & mentorship.';
          infraText = `<strong>Lab Infrastructure:</strong> ${workstations} ergonomic dual workstations (seats ${workstations * 2} students), central lockable storage, safety partitions & interactive board integration.`;
          kitsText = `<strong>Projects & DIY Kits:</strong> ${kitsCount}+ expanded multi-domain kits (Robotics, IoT, Renewable energy, Prototyping & Sensor arrays).`;
        } else {
          workstations = Math.max(16, Math.min(24, Math.round(state.students / 18)));
          kitsCount = Math.round(75 + (state.students * 0.05));
          recommendedTierName = 'Skillvation Premium Innovation Hub';
          planUrl = baseUrls.premium;
          planBtnText = 'View Premium Plan Details →';
          badgeText = '🚀 Flagship Center of Excellence';
          trainingText = '4+ certified teachers + bi-monthly masterclasses, hackathon mentoring & continuous faculty upskilling.';
          infraText = `<strong>Lab Infrastructure:</strong> ${workstations} specialized modular pods (seats ${workstations * 2} students), 3D printing tinkering station, high-speed power bus & teacher command station.`;
          kitsText = `<strong>Projects & DIY Kits:</strong> ${kitsCount}+ advanced innovation kits (AI Vision camera modules, Autonomous robotics, Drone engineering, Microcontrollers & IoT).`;
        }

        const simultaneousCapacity = workstations * 2;
        const weeklyBatches = Math.max(6, Math.ceil(state.students / simultaneousCapacity));

        // Update Step 1 Campus Bar KPIs
        const eduPerStudentTop = document.getElementById('eduPerStudentTop');
        if (eduPerStudentTop) {
          eduPerStudentTop.innerHTML = `₹${perStudentAnnual.toLocaleString('en-IN')} <span style="font-size:0.8rem;font-weight:600;color:#64748b;">/ student / yr</span>`;
        }

        const eduWorkstationsTop = document.getElementById('eduWorkstationsTop');
        if (eduWorkstationsTop) {
          eduWorkstationsTop.textContent = `${workstations} Dual Units`;
        }

        const eduShiftsTop = document.getElementById('eduShiftsTop');
        if (eduShiftsTop) {
          eduShiftsTop.textContent = `${weeklyBatches} Weekly Shifts`;
        }

        // Update DOM elements
        const recBadge = document.getElementById('recBadge');
        if (recBadge) recBadge.textContent = badgeText;

        const recTitle = document.getElementById('recTitle');
        if (recTitle) recTitle.textContent = recommendedTierName;

        const recSubtitle = document.getElementById('recSubtitle');
        if (recSubtitle) {
          recSubtitle.textContent = `Optimized custom configuration based on ₹${state.budget.toFixed(2)} Lakh budget for ${state.students} students.`;
        }

        const recPrice = document.getElementById('recPrice');
        if (recPrice) recPrice.textContent = '₹' + state.budget.toFixed(2) + ' L';

        const recPerStudent = document.getElementById('recPerStudent');
        if (recPerStudent) recPerStudent.textContent = '₹' + perStudentAnnual.toLocaleString('en-IN');

        const recWorkstations = document.getElementById('recWorkstations');
        if (recWorkstations) recWorkstations.textContent = `${workstations} Units (${simultaneousCapacity} Seats)`;

        const recBatches = document.getElementById('recBatches');
        if (recBatches) recBatches.textContent = `${weeklyBatches} Weekly Shifts`;

        const recInfraText = document.getElementById('recInfraText');
        if (recInfraText) recInfraText.innerHTML = infraText;

        const recKitsText = document.getElementById('recKitsText');
        if (recKitsText) recKitsText.innerHTML = kitsText;

        const recTrainingText = document.getElementById('recTrainingText');
        if (recTrainingText) recTrainingText.innerHTML = `<strong>Faculty Onboarding:</strong> ${trainingText}`;

        const recPlanDetailsLink = document.getElementById('recPlanDetailsLink');
        if (recPlanDetailsLink) {
          recPlanDetailsLink.href = planUrl;
          recPlanDetailsLink.textContent = planBtnText;
        }

        // Advisory Note
        const advisoryText = document.getElementById('recAdvisoryText');
        if (advisoryText) {
          if (state.students >= 700 && state.category === 'essential') {
            advisoryText.textContent = `High Cohort Alert: For ${state.students} students with an Essential budget, schedule at least ${weeklyBatches} weekly batches or consider Growth tier to maximize hands-on kit access.`;
          } else if (state.students <= 200 && state.category === 'premium') {
            advisoryText.textContent = `Elite Innovation Ratio: With ${state.students} students in Premium tier, every student receives virtually dedicated hands-on equipment and advanced AI/Robotics kits.`;
          } else {
            advisoryText.textContent = `Optimal Balance: ${state.students} students scheduled over ~${weeklyBatches} weekly class shifts guarantees 100% hands-on kit access with zero idle time.`;
          }
        }

        // Update modal summary pill
        const modalSummaryTitle = document.getElementById('modalSummaryTitle');
        if (modalSummaryTitle) {
          modalSummaryTitle.textContent = `${recommendedTierName} (₹${state.budget.toFixed(2)}L · ${state.students} Students)`;
        }

        const modalSummaryCost = document.getElementById('modalSummaryCost');
        if (modalSummaryCost) {
          modalSummaryCost.textContent = `₹${perStudentAnnual.toLocaleString('en-IN')} / student`;
        }

        // Update hidden form fields for submission
        const cpFormTitle = document.getElementById('cpFormTitle');
        if (cpFormTitle) {
          cpFormTitle.value = `Custom Composite Lab: ${recommendedTierName} (₹${state.budget.toFixed(2)}L, ${state.students} Students)`;
        }

        const quotationData = {
          recommended_tier: recommendedTierName,
          budget_lakhs: state.budget,
          budget_category: state.category,
          student_count: state.students,
          grade_levels: state.grades,
          cost_per_student_annual: perStudentAnnual,
          workstations: workstations,
          seating_capacity: simultaneousCapacity,
          weekly_shifts: weeklyBatches,
          kits_count: kitsCount,
        };

        const cpFormQuotation = document.getElementById('cpFormQuotation');
        if (cpFormQuotation) cpFormQuotation.value = JSON.stringify(quotationData);

        const cpFormMessage = document.getElementById('cpFormMessage');
        if (cpFormMessage) {
          cpFormMessage.value = `Custom Composite Skill Lab Configurator Request:
      Budget: ₹${state.budget.toFixed(2)} Lakh (${state.category} Tier)
      Students: ${state.students}
      Grades: ${state.grades.join(', ') || 'All Grades'}
      Recommended Setup: ${recommendedTierName}
      Workstations: ${workstations} (Seats ${simultaneousCapacity})
      Weekly Shifts: ${weeklyBatches}
      DIY Kits: ${kitsCount}+`;
        }

        const confirmScopeText = document.getElementById('confirmScopeText');
        if (confirmScopeText) {
          confirmScopeText.textContent = `${recommendedTierName} (₹${state.budget.toFixed(2)}L for ${state.students} Students)`;
        }

        if (typeof updateProductSelectionIntelligence === 'function') {
          updateProductSelectionIntelligence();
        }
      }

      // ── Custom Proposal Modal Open / Close ───────────────────
      window.openCustomProposalModal = function () {
        // Build a summary of the currently selected items.
        const items = [];
        let total = 0;
        for (const id in selectedProducts) {
          const sel = selectedProducts[id];
          if (!sel || !sel.item) continue;
          const lineTotal = (sel.item.price || 0) * (sel.qty || 1);
          total += lineTotal;
          items.push({
            product_id: sel.item.real_id || null,
            title: sel.item.title,
            category: sel.item.real_category || sel.item.catLabel || '',
            qty: sel.qty || 1,
            unit_price: sel.item.price || 0,
            total_price: lineTotal
          });
        }

        // Populate the enquiry form's hidden quotation field (JSON for the backend).
        const qEl = document.getElementById('planQuotation');
        if (qEl) {
          qEl.value = JSON.stringify({
            type: 'custom_package_advisor',
            budget_lakhs: state.budget,
            students: state.students,
            total_amount: total,
            item_count: items.length,
            items: items
          });
        }

        // Pre-fill the requirements box with a human-readable summary.
        const msgBox = document.querySelector('#planEnquiryForm textarea[name="address"]');
        if (msgBox) {
          let txt = 'Custom Package Advisor request\n';
          txt += 'Target budget: ₹' + Number(state.budget).toFixed(2) + ' Lakh · Students: ' + state.students + '\n';
          txt += 'Selected items (' + items.length + ') — Total ₹' + total.toLocaleString('en-IN') + ':\n';
          items.forEach(function (it) {
            txt += '• ' + it.title + ' × ' + it.qty + ' (₹' + it.total_price.toLocaleString('en-IN') + ')\n';
          });
          // Only overwrite if the user hasn't typed something custom already.
          if (!msgBox.value || msgBox.dataset.autofilled === '1') {
            msgBox.value = txt;
            msgBox.dataset.autofilled = '1';
          }
        }

        // Scroll to the enquiry form so the user can complete the request.
        const target = document.getElementById('demo');
        if (target) {
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
          const firstInput = document.querySelector('#planEnquiryForm input[name="school"]');
          if (firstInput) setTimeout(function () { firstInput.focus(); }, 500);
        }
      };

      window.closeCustomProposalModal = function () {
        const modal = document.getElementById('customProposalModal');
        if (modal) {
          modal.style.display = 'none';
          document.body.style.overflow = '';
        }
      };

      // Click backdrop to close
      const cpModal = document.getElementById('customProposalModal');
      if (cpModal) {
        cpModal.addEventListener('click', function (e) {
          if (e.target === cpModal) closeCustomProposalModal();
        });
      }

      // Esc to close
      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && cpModal && cpModal.style.display === 'flex') {
          closeCustomProposalModal();
        }
      });

      // AJAX form submit
      const cpForm = document.getElementById('customProposalForm');
      if (cpForm) {
        cpForm.addEventListener('submit', function (e) {
          e.preventDefault();
          const submitBtn = document.getElementById('cpSubmitBtn');
          const confirmMsg = document.getElementById('cpConfirmMsg');
          const originalText = submitBtn.textContent;

          submitBtn.disabled = true;
          submitBtn.textContent = 'Generating & Submitting Proposal…';

          fetch('{{ route("course.enquiry.store") }}', {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
              'Accept': 'application/json',
            },
            body: new FormData(cpForm),
          })
          .then(res => res.json())
          .then(() => {
            cpForm.style.display = 'none';
            if (confirmMsg) confirmMsg.style.display = 'block';
          })
          .catch(() => {
            cpForm.style.display = 'none';
            if (confirmMsg) confirmMsg.style.display = 'block';
          })
          .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
          });
        });
      }

      // ── Product Catalog — built dynamically from the live club-shop product list ──
      // Each item mirrors the shape the advisor UI expects:
      //   { id, real_id, title, desc, pkg, pkgLabel, category, catLabel, real_category,
      //     price, defaultQty, priority, icon, grades, capacity }
      let masterCatalog = [];
      let catalogCategories = []; // [{ key, label, icon }]

      // Map a category name to an emoji/icon for the filter pill
      function catIconFor(name) {
        const n = (name || '').toLowerCase();
        if (n.includes('agricult') || n.includes('garden')) return '🌱';
        if (n.includes('apparel') || n.includes('textile')) return '🧵';
        if (n.includes('beauty') || n.includes('wellness')) return '💅';
        if (n.includes('food')) return '🍳';
        if (n.includes('health')) return '🩺';
        if (n.includes('electron') || n.includes('ece') || n.includes('communication')) return '🔌';
        if (n.includes('design')) return '🎨';
        if (n.includes('robot')) return '🤖';
        if (n.includes('maker') || n.includes('tool')) return '🛠️';
        if (n.includes('cod') || n.includes('digital')) return '💻';
        if (n.includes('pottery')) return '🏺';
        if (n.includes('satellite') || n.includes('rocket')) return '🚀';
        if (n.includes('ar') || n.includes('vr') || n.includes('reality')) return '🕶️';
        if (n.includes('media')) return '🎬';
        if (n.includes('pos') || n.includes('retail')) return '🛒';
        if (n.includes('mandatory') || n.includes('equipment') || n.includes('infra')) return '🏗️';
        return '📦';
      }

      // Derive the package tier from a product title
      function pkgFromTitle(title) {
        const t = (title || '').toLowerCase();
        if (t.includes('premium')) return { pkg: 'premium', label: 'Premium Plan' };
        if (t.includes('advanc')) return { pkg: 'advance', label: 'Advance Plan' };
        if (t.includes('basic')) return { pkg: 'basic', label: 'Basic Plan' };
        return { pkg: 'basic', label: 'Standard' };
      }

      // Sensible default quantity based on item type
      function defaultQtyFor(title) {
        const t = (title || '').toLowerCase();
        if (t.includes('lab') && !t.includes('kit')) return 1;      // shared lab resource
        if (t.includes('consumable')) return 4;
        if (t.includes('mandatory') || t.includes('equipment')) return 1;
        if (t.includes('kit')) return 4;
        return 1;
      }

      // Render the category filter pills from the live categories
      function renderCategoryPills() {
        const bar = document.getElementById('psCatFilterBar');
        if (!bar) return;
        // keep the "Filter by:" label + the "All Components" pill, drop the rest
        bar.querySelectorAll('.vl-ps-pill:not([data-cat-filter="all"])').forEach(b => b.remove());
        catalogCategories.forEach(c => {
          const btn = document.createElement('button');
          btn.type = 'button';
          btn.className = 'vl-ps-pill';
          btn.setAttribute('data-cat-filter', c.key);
          btn.textContent = c.icon + ' ' + c.label;
          btn.onclick = function () { setCategoryFilter(c.key, btn); };
          bar.appendChild(btn);
        });
      }

      // Build the advisor catalog from the components of the "skillvation-lab" bundle.
      // Only the categories/products that belong to this bundle are shown — the advisor
      // grows automatically as components are added to the bundle in admin.
      function buildCatalogFromShop() {
        const BASE = '<?= rtrim(env("APP_URL"), "/") ?>/club-shop';

        return fetch(BASE + '/api/products/slug/skillvation-lab')
          .then(r => r.ok ? r.json() : null)
          .then(res => {
            const comps = (res && res.data && Array.isArray(res.data.bundle_components))
              ? res.data.bundle_components : [];

            const catalog = [];
            const usedCatKeys = {}; // catKey -> {key,label,icon}
            const catOrder = [];    // preserve first-seen category order

            comps.forEach(c => {
              const price = Number(
                (c.unit_price && Number(c.unit_price) > 0) ? c.unit_price :
                (c.price_override && Number(c.price_override) > 0 ? c.price_override : 0)
              ) || 0;
              if (price <= 0) return;

              const catName = c.category_name || 'General';
              const catId   = (c.category_id != null && c.category_id !== '') ? c.category_id : ('x_' + catName);
              const catKey  = 'cat_' + catId;
              const tier    = pkgFromTitle(c.title);
              const qty     = (c.required_quantity && Number(c.required_quantity) > 0)
                ? Number(c.required_quantity) : defaultQtyFor(c.title);

              catalog.push({
                id: 'comp_' + (c.id || c.component_product_id),
                real_id: Number(c.component_product_id) || null,
                comp_id: c.id || null,
                title: c.title || ('Product #' + c.component_product_id),
                desc: 'Skillvation Lab Component · ' + catName + (c.sku ? (' · SKU: ' + c.sku) : ''),
                pkg: tier.pkg,
                pkgLabel: tier.label,
                category: catKey,
                catLabel: catName,
                real_category: catName,
                price: price,
                defaultQty: qty,
                priority: tier.pkg === 'basic' ? 1 : (tier.pkg === 'advance' ? 2 : 3),
                icon: 'fa-cube',
                grades: 'Grades VI–XII',
                capacity: (c.title || '').toLowerCase().includes('kit') ? '4–8 Students / Group' : 'Lab Shared Resource',
                image: c.image_small || c.image_default || '',
                url: ''
              });

              if (!usedCatKeys[catKey]) {
                usedCatKeys[catKey] = { key: catKey, label: catName, icon: catIconFor(catName) };
                catOrder.push(catKey);
              }
            });

            masterCatalog = catalog;
            catalogCategories = catOrder.map(k => usedCatKeys[k]);

            renderCategoryPills();
            return masterCatalog;
          })
          .catch(() => { masterCatalog = []; renderCategoryPills(); return []; });
      }


      // Selected items state: { [id]: { qty: Number, item: Object } }
      let selectedProducts = {};
      let activePkgFilter  = 'all';
      let activeCatFilter  = 'all';
      let searchQuery      = '';

      // Initialize default selection based on current growth tier
      function initDefaultProducts() {
        selectedProducts = {};
        autoSuggestProductsForBudget(false);
      }

      // Filter handling
      window.setPackageFilter = function (pkg, btn) {
        activePkgFilter = pkg;
        document.querySelectorAll('[data-pkg-filter]').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');
        renderProductCatalog();
        renderSmartSuggestions();
      };

      window.setCategoryFilter = function (cat, btn) {
        activeCatFilter = cat;
        document.querySelectorAll('[data-cat-filter]').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');

        // Update category suggest button label
        const btnLabel = document.getElementById('psCatSuggestBtnLabel');
        if (btnLabel) {
          const names = {
            'all': 'Suggest for Current Focus',
            'life': 'Suggest Form 1 (Life Forms)',
            'machines': 'Suggest Form 2 (Machines/IoT)',
            'digital': 'Suggest Form 3 (Human Services)',
            'infra': 'Suggest Infrastructure'
          };
          btnLabel.textContent = names[cat] || 'Suggest for Current Category';
        }

        renderProductCatalog();
        renderSmartSuggestions();
      };

      window.filterProductCatalog = function () {
        const input = document.getElementById('psSearchInput');
        searchQuery = input ? input.value.trim().toLowerCase() : '';
        renderProductCatalog();
      };

      // Intelligent Auto-Suggest for the CURRENT ACTIVE category from our real product list
      window.autoSuggestForActiveCategory = function () {
        const targetBudgetRs = state.budget * 100000;
        const cat = activeCatFilter === 'all' ? 'machines' : activeCatFilter;

        // Find candidate items in this category that are not yet selected
        const unselected = masterCatalog.filter(p => p.category === cat && !selectedProducts[p.id]);
        unselected.sort((a, b) => (a.priority || 2) - (b.priority || 2));

        let addedCount = 0;
        for (const item of unselected) {
          const currentTotal = calculateCurrentTotal();
          const itemCost = item.price * (item.defaultQty || 1);
          if (currentTotal + itemCost <= targetBudgetRs) {
            selectedProducts[item.id] = { qty: item.defaultQty || 1, item: item };
            addedCount++;
          }
        }

        renderProductCatalog();
        updateProductSelectionIntelligence();

        const shelf = document.getElementById('psSmartShelf');
        if (shelf) {
          shelf.style.animation = 'none';
          void shelf.offsetWidth;
          shelf.style.animation = 'cbSlideUp 0.35s ease';
        }
      };

      // Quick-add from smart suggestion shelf
      window.addSuggestedProduct = function (id) {
        const item = masterCatalog.find(p => p.id === id);
        if (item) {
          if (!selectedProducts[id]) {
            selectedProducts[id] = { qty: item.defaultQty || 1, item: item };
          } else {
            selectedProducts[id].qty += 1;
          }
          renderProductCatalog();
          updateProductSelectionIntelligence();
        }
      };

      // Dynamic AI Smart Suggestion Shelf using official products
      function renderSmartSuggestions() {
        const shelf = document.getElementById('psSmartShelf');
        const cardsGrid = document.getElementById('psShelfCardsGrid');
        const catTitle = document.getElementById('psShelfCategoryTitle');
        const budgetHint = document.getElementById('psShelfBudgetHint');
        if (!shelf || !cardsGrid) return;

        const targetBudgetRs = state.budget * 100000;
        const currentTotal = calculateCurrentTotal();
        const headroom = targetBudgetRs - currentTotal;

        const catNames = {
          'all': 'All Packages & Domains',
          'life': 'Form 1: Life Forms & Agri',
          'machines': 'Form 2: Machines, Robotics & IoT',
          'digital': 'Form 3: Human Services & Vocational',
          'infra': 'Infrastructure & Equipment'
        };

        if (catTitle) {
          catTitle.textContent = (activeCatFilter === 'all')
            ? `AI Recommendations for ${catNames['all']} (₹${state.budget.toFixed(2)}L Budget)`
            : `AI Recommendations for ${catNames[activeCatFilter] || activeCatFilter}`;
        }
        if (budgetHint) {
          budgetHint.textContent = headroom >= 0
            ? `₹${(headroom / 100000).toFixed(2)} Lakh headroom remaining`
            : `Over budget by ₹${(Math.abs(headroom) / 100000).toFixed(2)} Lakh`;
        }

        // Find candidate items that are not already selected
        let candidates = masterCatalog.filter(p => {
          if (selectedProducts[p.id]) return false;
          if (activePkgFilter !== 'all' && p.pkg !== activePkgFilter) return false;
          if (activeCatFilter !== 'all' && p.category !== activeCatFilter) return false;
          return true;
        });

        // If specific filter returned no candidates, pull from general pool
        if (candidates.length === 0) {
          candidates = masterCatalog.filter(p => !selectedProducts[p.id]);
        }

        // Prioritize items that fit cleanly within available headroom
        candidates.sort((a, b) => {
          const costA = a.price * (a.defaultQty || 1);
          const costB = b.price * (b.defaultQty || 1);
          const fitsA = costA <= headroom ? 1 : 0;
          const fitsB = costB <= headroom ? 1 : 0;
          if (fitsA !== fitsB) return fitsB - fitsA;
          return (a.priority || 2) - (b.priority || 2);
        });

        const top3 = candidates.slice(0, 3);
        if (top3.length === 0) {
          cardsGrid.innerHTML = `
            <div style="grid-column:1/-1;padding:16px;text-align:center;color:#64748b;font-size:0.88rem;">
              ✨ All products in this filter are currently included in your custom setup!
            </div>
          `;
          return;
        }

        let html = '';
        top3.forEach(item => {
          const cost = item.price * (item.defaultQty || 1);
          const fits = cost <= headroom;
          const tagBg = item.pkg === 'premium' ? '#fdf2f8' : (item.pkg === 'advance' ? '#eff6ff' : '#f0fdf4');
          const tagColor = item.pkg === 'premium' ? '#be185d' : (item.pkg === 'advance' ? '#1d4ed8' : '#15803d');

          html += `
            <div class="vl-shelf-card">
              <div class="vl-shelf-card-top">
                <span class="vl-shelf-card-pkg" style="background:${tagBg};color:${tagColor};">${item.pkgLabel}</span>
                <span class="vl-shelf-card-cat">${item.catLabel}</span>
              </div>
              <div class="vl-shelf-card-title">${item.title}</div>
              <div class="vl-shelf-card-desc">${item.desc}</div>
              <div class="vl-shelf-card-bottom">
                <div>
                  <div class="vl-shelf-card-price">₹${item.price.toLocaleString('en-IN')} <span style="font-size:0.75rem;font-weight:600;color:#64748b;">× ${item.defaultQty || 1}</span></div>
                  <div style="font-size:0.75rem;color:${fits ? '#059669' : '#dc2626'};font-weight:700;">
                    ${fits ? '✓ Fits in Budget' : '+ Adds ₹' + (cost / 100000).toFixed(2) + 'L'}
                  </div>
                </div>
                <button type="button" class="vl-shelf-card-btn" onclick="addSuggestedProduct('${item.id}')">
                  <i class="fa-solid fa-plus"></i> Add
                </button>
              </div>
            </div>
          `;
        });

        cardsGrid.innerHTML = html;
      }

      // Add all 3 suggested products
      window.addAllSmartSuggestions = function () {
        const targetBudgetRs = state.budget * 100000;
        const currentTotal = calculateCurrentTotal();
        const headroom = targetBudgetRs - currentTotal;

        const candidates = masterCatalog.filter(p => !selectedProducts[p.id]);
        candidates.sort((a, b) => {
          const costA = a.price * (a.defaultQty || 1);
          const costB = b.price * (b.defaultQty || 1);
          const fitsA = costA <= headroom ? 1 : 0;
          const fitsB = costB <= headroom ? 1 : 0;
          if (fitsA !== fitsB) return fitsB - fitsA;
          return (a.priority || 2) - (b.priority || 2);
        });

        const top3 = candidates.slice(0, 3);
        top3.forEach(item => {
          if (!selectedProducts[item.id]) {
            selectedProducts[item.id] = { qty: item.defaultQty || 1, item: item };
          }
        });

        renderProductCatalog();
        updateProductSelectionIntelligence();
      };

      // Select specific package preset as foundation
      window.selectPackagePreset = function (pkg) {
        selectedProducts = {};
        const items = masterCatalog.filter(p => p.pkg === pkg);
        for (const item of items) {
          selectedProducts[item.id] = { qty: item.defaultQty || 1, item: item };
        }
        renderProductCatalog();
        updateProductSelectionIntelligence();
      };

      // Select specific single product
      window.selectSingleProduct = function (id) {
        if (selectedProducts[id]) {
          delete selectedProducts[id];
        } else {
          const item = masterCatalog.find(p => p.id === id);
          if (item) {
            selectedProducts[id] = { qty: item.defaultQty || 1, item: item };
          }
        }
        renderProductCatalog();
        updateProductSelectionIntelligence();
      };

      // Toggle a product card's selected state (used by the product grid onclick)
      window.toggleProductSelection = function (id) {
        window.selectSingleProduct(id);
      };

      // Adjust quantity
      window.changeProductQty = function (e, id, delta) {
        e.stopPropagation();
        if (!selectedProducts[id]) {
          const item = masterCatalog.find(p => p.id === id);
          if (item) selectedProducts[id] = { qty: Math.max(1, delta), item: item };
        } else {
          const newQty = selectedProducts[id].qty + delta;
          if (newQty <= 0) {
            delete selectedProducts[id];
          } else {
            selectedProducts[id].qty = newQty;
          }
        }
        renderProductCatalog();
        updateProductSelectionIntelligence();
      };

      window.clearSelectedProducts = function () {
        selectedProducts = {};
        renderProductCatalog();
        updateProductSelectionIntelligence();
      };

      // Intelligent Auto-Suggest Algorithm across all Packages using our REAL product catalog
      window.autoSuggestProductsForBudget = function (animate = true) {
        const targetBudgetRs = state.budget * 100000;
        selectedProducts = {};

        // Determine target package tier based on budget & category
        let targetTier = 'basic';
        if (state.budget > 4.0 && state.budget <= 8.5) {
          targetTier = 'advance';
        } else if (state.budget > 8.5) {
          targetTier = 'premium';
        }

        // Determine student scaling for kit quantities
        let kitQty = 4;
        if (state.students < 150) kitQty = 2;
        else if (state.students > 500) kitQty = 6;

        // Core CBSE skill domains in our official product line
        const coreCurriculumTitles = [
          'Agriculture & Gardening',
          'ECE (Electronics & Communication)',
          'Design Thinking + 3D Printer',
          'Healthcare',
          'Makers Tools',
          'Food Production (Baking & Kitchen Skills)',
          'Beauty & Wellness',
          'Pottery',
          'Apparels'
        ];

        // 1. Mandatory Core Infrastructure Equipment
        let infraItem = null;
        if (targetTier === 'basic') {
          infraItem = masterCatalog.find(p => p.title.includes('Mandatory Basic Lab Equipment'));
        } else if (targetTier === 'advance') {
          infraItem = masterCatalog.find(p => p.title.includes('Mandotory Advanced Lab Equipments'));
        } else {
          infraItem = masterCatalog.find(p => p.title.includes('Mandotory Premium Lab Equipments'));
        }
        if (infraItem && infraItem.price <= targetBudgetRs * 0.45) {
          selectedProducts[infraItem.id] = { qty: 1, item: infraItem };
        }

        // 2. Add primary curriculum kits for each domain
        coreCurriculumTitles.forEach(domain => {
          let kit = masterCatalog.find(p => p.pkg === targetTier && p.title.includes(domain) && p.title.includes('Kit'));
          if (!kit && targetTier === 'premium') {
            kit = masterCatalog.find(p => p.pkg === 'advance' && p.title.includes(domain) && p.title.includes('Kit'));
          }
          if (!kit) {
            kit = masterCatalog.find(p => p.pkg === 'basic' && p.title.includes(domain) && p.title.includes('Kit'));
          }

          if (kit) {
            const currentTotal = calculateCurrentTotal();
            const kitCost = kit.price * kitQty;
            if (currentTotal + kitCost <= targetBudgetRs) {
              selectedProducts[kit.id] = { qty: kitQty, item: kit };
            } else if (currentTotal + (kit.price * 2) <= targetBudgetRs) {
              selectedProducts[kit.id] = { qty: 2, item: kit };
            } else if (currentTotal + kit.price <= targetBudgetRs) {
              selectedProducts[kit.id] = { qty: 1, item: kit };
            }
          }
        });

        // 3. Add corresponding consumables for selected kits
        for (const selId in { ...selectedProducts }) {
          const selItem = selectedProducts[selId].item;
          const domain = coreCurriculumTitles.find(d => selItem.title.includes(d));
          if (domain) {
            const consumable = masterCatalog.find(p => 
              p.pkg === selItem.pkg && 
              p.title.includes(domain) && 
              (p.title.includes('Consumable') || p.title.includes('consumable')) &&
              !selectedProducts[p.id]
            );
            if (consumable) {
              const currentTotal = calculateCurrentTotal();
              const cCost = consumable.price * selectedProducts[selId].qty;
              if (currentTotal + cCost <= targetBudgetRs) {
                selectedProducts[consumable.id] = { qty: selectedProducts[selId].qty, item: consumable };
              }
            }
          }
        }

        // 4. Fill remaining headroom intelligently with lab setups and curriculum modules up to ~95%
        const remainingCandidates = masterCatalog.filter(p => !selectedProducts[p.id]);
        remainingCandidates.sort((a, b) => {
          const tierRank = { 'premium': 3, 'advance': 2, 'basic': 1 };
          const targetRank = tierRank[targetTier];
          const diffA = Math.abs(tierRank[a.pkg] - targetRank);
          const diffB = Math.abs(tierRank[b.pkg] - targetRank);
          if (diffA !== diffB) return diffA - diffB;
          return (a.price * (a.defaultQty || 1)) - (b.price * (b.defaultQty || 1));
        });

        for (const cand of remainingCandidates) {
          const currentTotal = calculateCurrentTotal();
          const itemCost = cand.price * (cand.defaultQty || 1);
          if (currentTotal + itemCost <= targetBudgetRs * 0.98) {
            selectedProducts[cand.id] = { qty: cand.defaultQty || 1, item: cand };
          }
        }

        renderProductCatalog();
        updateProductSelectionIntelligence();

        if (animate) {
          const intelBox = document.querySelector('.vl-ps-intel-box');
          if (intelBox) {
            intelBox.style.animation = 'none';
            void intelBox.offsetWidth;
            intelBox.style.animation = 'cbSlideUp 0.35s ease';
          }
        }
      };

      function calculateCurrentTotal() {
        let total = 0;
        for (const id in selectedProducts) {
          total += selectedProducts[id].qty * selectedProducts[id].item.price;
        }
        return total;
      }

      // Render product cards with intelligent tags & package origins
      function renderProductCatalog() {
        const grid = document.getElementById('psProductsGrid');
        if (!grid) return;

        const targetBudgetRs = state.budget * 100000;
        const currentTotal = calculateCurrentTotal();
        const headroom = targetBudgetRs - currentTotal;

        const filtered = masterCatalog.filter(p => {
          // Package filter
          if (activePkgFilter !== 'all' && p.pkg !== activePkgFilter) return false;
          // Category filter
          if (activeCatFilter !== 'all' && p.category !== activeCatFilter) return false;
          // Search query
          if (searchQuery) {
            const haystack = (p.title + ' ' + p.desc + ' ' + p.catLabel + ' ' + p.pkgLabel).toLowerCase();
            if (!haystack.includes(searchQuery)) return false;
          }
          return true;
        });

        if (filtered.length === 0) {
          grid.innerHTML = '<div style="grid-column:1/-1;padding:40px;text-align:center;color:#94a3b8;font-size:1rem;">'
            + '<i class="fa-solid fa-box-open" style="font-size:32px;margin-bottom:10px;display:block;"></i>'
            + 'No products found matching your search and filter criteria. Try choosing "All Packages" or "All Categories".'
            + '</div>';
          return;
        }

        let html = '';
        filtered.forEach(p => {
          const isSelected = !!selectedProducts[p.id];
          const qty = isSelected ? selectedProducts[p.id].qty : p.defaultQty;
          const cost = p.price * p.defaultQty;
          const isAiSuggested = !isSelected && cost <= headroom && headroom > 0;

          html += `
            <div class="vl-ps-card ${isSelected ? 'selected' : ''} ${isAiSuggested ? 'ai-suggested' : ''}" onclick="toggleProductSelection('${p.id}')">
              <div>
                <div class="vl-ps-card-top">
                  <div class="vl-ps-check-wrap">
                    <div class="vl-ps-checkbox">✓</div>
                  </div>
                  <div class="vl-ps-card-info">
                    <div class="vl-ps-badge-row">
                      <span class="vl-ps-pkg-badge ${p.pkg}">${p.pkgLabel}</span>
                      <span class="vl-ps-cat-badge">${p.catLabel}</span>
                      <span class="vl-ps-grade-badge"><i class="fa-solid fa-graduation-cap" style="font-size:9px;margin-right:3px;"></i>${p.grades || 'All Grades'}</span>
                      <span class="vl-ps-capacity-badge"><i class="fa-solid fa-users" style="font-size:9px;margin-right:3px;"></i>${p.capacity || 'Group of 2–4'}</span>
                      ${isAiSuggested ? '<span class="vl-ps-ai-badge">★ AI Best Fit</span>' : ''}
                    </div>
                    <div class="vl-ps-card-title">${p.title}</div>
                    <div class="vl-ps-card-desc">${p.desc}</div>
                  </div>
                </div>
              </div>

              <div class="vl-ps-card-bottom" onclick="event.stopPropagation()">
                <div>
                  <div class="vl-ps-card-price">₹${p.price.toLocaleString('en-IN')}</div>
                  <div style="font-size:0.75rem;color:#94a3b8;font-weight:600;">Unit Price</div>
                </div>

                ${isSelected ? `
                  <div class="vl-ps-qty-stepper">
                    <button type="button" class="vl-ps-qty-btn" onclick="changeProductQty(event, '${p.id}', -1)" title="Decrease quantity">−</button>
                    <span class="vl-ps-qty-val">${qty}</span>
                    <button type="button" class="vl-ps-qty-btn" onclick="changeProductQty(event, '${p.id}', 1)" title="Increase quantity">+</button>
                  </div>
                ` : `
                  <button type="button" class="vl-ps-shelf-add-btn" onclick="toggleProductSelection('${p.id}')" style="padding:6px 12px;font-size:0.8rem;">
                    + Select
                  </button>
                `}
              </div>
            </div>
          `;
        });

        grid.innerHTML = html;
      }

      // Update Intelligence bar, Category allocations, & CBSE breakdown
      function updateProductSelectionIntelligence() {
        const targetBudgetRs = state.budget * 100000;
        const total = calculateCurrentTotal();
        const diff = targetBudgetRs - total;
        const count = Object.keys(selectedProducts).length;
        const perStudent = Math.round(total / (state.students || 1));

        // Format rupees
        const fmtTotal = '₹' + (total / 100000).toFixed(2) + ' L';
        const fmtTarget = '₹' + state.budget.toFixed(2) + ' L';
        
        // Update labels
        const targetEl = document.getElementById('psTargetBudget');
        if (targetEl) targetEl.textContent = fmtTarget;

        const totalEl = document.getElementById('psSelectedTotal');
        if (totalEl) totalEl.textContent = fmtTotal;

        const countEl = document.getElementById('psSelectedCount');
        if (countEl) countEl.textContent = `${count} Products`;

        const remEl = document.getElementById('psRemainingBudget');
        if (remEl) {
          if (diff >= 0) {
            remEl.textContent = '+₹' + (diff / 100000).toFixed(2) + ' L';
            remEl.style.color = '#059669';
          } else {
            remEl.textContent = '-₹' + (Math.abs(diff) / 100000).toFixed(2) + ' L';
            remEl.style.color = '#dc2626';
          }
        }

        // Progress bar
        const fillEl = document.getElementById('psProgressFill');
        if (fillEl) {
          const pct = Math.min(100, Math.round((total / targetBudgetRs) * 100));
          fillEl.style.width = pct + '%';
          fillEl.classList.remove('warning', 'over');
          if (total > targetBudgetRs) {
            fillEl.classList.add('over');
          } else if (pct >= 90) {
            fillEl.classList.add('warning');
          }
        }

        // Feedback message
        const msgEl = document.getElementById('psFeedbackMsg');
        const textEl = document.getElementById('psFeedbackText');
        if (msgEl && textEl) {
          msgEl.classList.remove('safe', 'warning', 'danger');
          if (count === 0) {
            msgEl.classList.add('safe');
            textEl.textContent = 'Click "Auto-Suggest Best Mix" or check individual products from any package to build your lab.';
          } else if (diff < 0) {
            msgEl.classList.add('danger');
            textEl.textContent = `⚠️ Over Budget by ₹${Math.abs(Math.round(diff)).toLocaleString('en-IN')}! Trim optional items or click "Auto-Suggest Best Mix" to re-balance.`;
          } else if (diff <= targetBudgetRs * 0.1) {
            msgEl.classList.add('safe');
            textEl.textContent = `🎯 Excellent Fit: ${( (total/targetBudgetRs)*100 ).toFixed(0)}% budget utilized. Balanced across mandatory CBSE categories with ₹${Math.round(diff).toLocaleString('en-IN')} buffer.`;
          } else {
            msgEl.classList.add('warning');
            textEl.textContent = `💡 Available Headroom: You have ₹${(diff / 100000).toFixed(2)} Lakh remaining. You can add more kits, workstations, or advanced AI modules.`;
          }
        }

        // CBSE Form balance counters and spend calculations
        let form1Count = 0, form2Count = 0, form3Count = 0, infraCount = 0;
        let form1Spend = 0, form2Spend = 0, form3Spend = 0, infraSpend = 0;
        let pkgBasicCount = 0, pkgAdvanceCount = 0, pkgPremiumCount = 0;

        for (const id in selectedProducts) {
          const p = selectedProducts[id];
          const lineCost = p.qty * p.item.price;

          if (p.item.category === 'life') {
            form1Count += p.qty;
            form1Spend += lineCost;
          } else if (p.item.category === 'machines') {
            form2Count += p.qty;
            form2Spend += lineCost;
          } else if (p.item.category === 'digital') {
            form3Count += p.qty;
            form3Spend += lineCost;
          } else {
            infraCount += p.qty;
            infraSpend += lineCost;
          }

          if (p.item.pkg === 'basic') pkgBasicCount += p.qty;
          else if (p.item.pkg === 'advance') pkgAdvanceCount += p.qty;
          else if (p.item.pkg === 'premium') pkgPremiumCount += p.qty;
        }

        // CBSE pill status
        const pill1 = document.getElementById('psPillForm1');
        if (pill1) {
          pill1.textContent = `Form 1: Life Forms (${form1Count})`;
          pill1.classList.toggle('active', form1Count > 0);
        }
        const pill2 = document.getElementById('psPillForm2');
        if (pill2) {
          pill2.textContent = `Form 2: Machines/IoT (${form2Count})`;
          pill2.classList.toggle('active', form2Count > 0);
        }
        const pill3 = document.getElementById('psPillForm3');
        if (pill3) {
          pill3.textContent = `Form 3: Digital/AI (${form3Count})`;
          pill3.classList.toggle('active', form3Count > 0);
        }
        const pillInfra = document.getElementById('psPillInfra');
        if (pillInfra) {
          pillInfra.textContent = `Infra & Lab (${infraCount})`;
          pillInfra.classList.toggle('active', infraCount > 0);
        }

        // Category Allocation Cards Breakdown
        const f1Pct = total > 0 ? Math.min(100, Math.round((form1Spend / total) * 100)) : 0;
        const f2Pct = total > 0 ? Math.min(100, Math.round((form2Spend / total) * 100)) : 0;
        const f3Pct = total > 0 ? Math.min(100, Math.round((form3Spend / total) * 100)) : 0;
        const infPct = total > 0 ? Math.min(100, Math.round((infraSpend / total) * 100)) : 0;

        const valF1 = document.getElementById('allocValForm1');
        const fillF1 = document.getElementById('allocFillForm1');
        const pillF1 = document.getElementById('allocPillForm1');
        if (valF1) valF1.innerHTML = `₹${(form1Spend/1000).toFixed(0)}k <span style="font-size:0.72rem;color:#94a3b8;font-weight:500;">(${f1Pct}%)</span>`;
        if (fillF1) fillF1.style.width = f1Pct + '%';
        if (pillF1) pillF1.textContent = `${form1Count} items`;

        const valF2 = document.getElementById('allocValForm2');
        const fillF2 = document.getElementById('allocFillForm2');
        const pillF2 = document.getElementById('allocPillForm2');
        if (valF2) valF2.innerHTML = `₹${(form2Spend/1000).toFixed(0)}k <span style="font-size:0.72rem;color:#94a3b8;font-weight:500;">(${f2Pct}%)</span>`;
        if (fillF2) fillF2.style.width = f2Pct + '%';
        if (pillF2) pillF2.textContent = `${form2Count} items`;

        const valF3 = document.getElementById('allocValForm3');
        const fillF3 = document.getElementById('allocFillForm3');
        const pillF3 = document.getElementById('allocPillForm3');
        if (valF3) valF3.innerHTML = `₹${(form3Spend/1000).toFixed(0)}k <span style="font-size:0.72rem;color:#94a3b8;font-weight:500;">(${f3Pct}%)</span>`;
        if (fillF3) fillF3.style.width = f3Pct + '%';
        if (pillF3) pillF3.textContent = `${form3Count} items`;

        const valInf = document.getElementById('allocValInfra');
        const fillInf = document.getElementById('allocFillInfra');
        const pillInf = document.getElementById('allocPillInfra');
        if (valInf) valInf.innerHTML = `₹${(infraSpend/1000).toFixed(0)}k <span style="font-size:0.72rem;color:#94a3b8;font-weight:500;">(${infPct}%)</span>`;
        if (fillInf) fillInf.style.width = infPct + '%';
        if (pillInf) pillInf.textContent = `${infraCount} items`;

        // Package tabs live count badges
        const bAll = document.getElementById('badgePkgAll');
        if (bAll) bAll.textContent = masterCatalog.length;
        const bBasic = document.getElementById('badgePkgBasic');
        if (bBasic) bBasic.textContent = pkgBasicCount > 0 ? `${pkgBasicCount} in pack` : '0';
        const bAdv = document.getElementById('badgePkgAdvance');
        if (bAdv) bAdv.textContent = pkgAdvanceCount > 0 ? `${pkgAdvanceCount} in pack` : '0';
        const bPrem = document.getElementById('badgePkgPremium');
        if (bPrem) bPrem.textContent = pkgPremiumCount > 0 ? `${pkgPremiumCount} in pack` : '0';

        // Bottom Summary Bar
        const barTotal = document.getElementById('psBarTotal');
        if (barTotal) barTotal.textContent = fmtTotal;

        const barPerStudent = document.getElementById('psBarPerStudent');
        if (barPerStudent) barPerStudent.textContent = `₹${perStudent.toLocaleString('en-IN')} / student`;

        const barStudentCount = document.getElementById('psBarStudentCount');
        if (barStudentCount) barStudentCount.textContent = state.students;

        const barItemCount = document.getElementById('psBarItemCount');
        if (barItemCount) barItemCount.textContent = `${count} Products Selected`;

        // Update Hidden form payload for proposal modal
        const selectedList = [];
        for (const id in selectedProducts) {
          const p = selectedProducts[id];
          selectedList.push({
            id: id,
            title: p.item.title,
            pkg: p.item.pkgLabel,
            category: p.item.catLabel,
            unit_price: p.item.price,
            qty: p.qty,
            subtotal: p.item.price * p.qty
          });
        }

        const cpFormQuotation = document.getElementById('cpFormQuotation');
        if (cpFormQuotation) {
          const qData = {
            budget_lakhs: state.budget,
            target_budget_rs: targetBudgetRs,
            selected_total_rs: total,
            budget_variance_rs: diff,
            student_count: state.students,
            cost_per_student: perStudent,
            cbse_compliance: {
              form1_life_forms_qty: form1Count,
              form2_machines_iot_qty: form2Count,
              form3_digital_ai_qty: form3Count,
              infra_qty: infraCount,
              is_100_percent_compliant: form1Count > 0 && form2Count > 0 && form3Count > 0 && infraCount > 0
            },
            selected_components: selectedList
          };
          cpFormQuotation.value = JSON.stringify(qData);
        }

        const cpFormMessage = document.getElementById('cpFormMessage');
        if (cpFormMessage) {
          let msgText = `Custom Composite Lab Quotation Request:
          Target Budget: ₹${state.budget.toFixed(2)} Lakh
          Selected Items Total: ₹${(total / 100000).toFixed(2)} Lakh (${count} Products)
          Student Cohort: ${state.students} (₹${perStudent}/student/yr)
          CBSE Coverage: Form 1 (${form1Count} items), Form 2 (${form2Count} items), Form 3 (${form3Count} items), Infra (${infraCount} items)

          Selected Bill of Materials:
          `;
          selectedList.forEach((it, idx) => {
            msgText += `${idx + 1}. [${it.pkg}] ${it.title} × ${it.qty} = ₹${it.subtotal.toLocaleString('en-IN')}\n`;
          });
          cpFormMessage.value = msgText;
        }

        const modalSummaryTitle = document.getElementById('modalSummaryTitle');
        if (modalSummaryTitle) {
          modalSummaryTitle.textContent = `Custom Package (${count} Products · ₹${(total/100000).toFixed(2)}L · ${state.students} Students)`;
        }

        const modalSummaryCost = document.getElementById('modalSummaryCost');
        if (modalSummaryCost) {
          modalSummaryCost.textContent = `₹${perStudent.toLocaleString('en-IN')} / student`;
        }

        // Dynamically update smart suggestion shelf
        renderSmartSuggestions();
      }

      // Live shop background hydration
            // Live shop background sync (verifies pricing / status without adding out-of-scope items)
      function hydrateFromLiveShop() {
        const BASE = '<?= rtrim(env("APP_URL"), "/") ?>/club-shop';
        Promise.all([
          fetch(BASE + '/api/products/slug/skillvation-lab').then(r => r.ok ? r.json() : null).catch(() => null)
        ]).then(([basicRes, advRes, premRes]) => {
          let updated = 0;
          const syncPrices = (res) => {
            if (!res || !res.data || !Array.isArray(res.data.bundle_components)) return;
            res.data.bundle_components.forEach(c => {
              const matched = masterCatalog.find(m => m.title.trim().toLowerCase() === (c.title || '').trim().toLowerCase());
              if (matched && c.unit_price) {
                matched.price = Number(c.unit_price);
                if (c.required_quantity) matched.defaultQty = Number(c.required_quantity);
                updated++;
              }
            });
          };
          syncPrices(basicRes);
          syncPrices(advRes);
          syncPrices(premRes);
          if (updated > 0) {
            renderProductCatalog();
            updateProductSelectionIntelligence();
          }
        }).catch(() => {});
      }

      @if(($setting->lab_customization_status ?? 'active') == 'active')
      // Build the product catalog from the live shop, THEN initialize the advisor.
      buildCatalogFromShop().then(function () {
        initDefaultProducts();
        updateRecommendation();
      });
      @endif
    }());

</script>
@endpush
