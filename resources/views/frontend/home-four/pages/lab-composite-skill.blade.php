@extends('frontend.home-four.layouts.master')

@section('meta_title', 'Composite — Skill Labs for CBSE Schools | ' . config('app.name', 'Skillvation'))
@section('meta_description', 'Turnkey Robotics, AI, and coding labs built to CBSE specifications — installed, mapped to your syllabus, and staffed with trained teachers before the term starts.')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">

<style>
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
    align-items: center;
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
    grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
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

  /* ==================== LEAD FORM ==================== */
  .vl-form-panel {
    display: grid;
    grid-template-columns: 1fr 1.15fr;
    gap: 52px;
    align-items: center;
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
</style>
@endpush

@section('contents')
<div class="vidyalab-page">

  <!-- ==================== HERO SECTION ==================== -->
  <section class="vl-hero-vibrant">
    <div class="vidyalab-wrap vl-hero-grid">
      <div>
        <div class="vl-badge-pill hero">
          <span>✨</span> NEP 2020 &amp; CBSE COMPLIANT SKILL LABS
        </div>
        
        <h1>The composite skill lab your <span class="vl-grad-gold">NEP&nbsp;2020 committee</span> will celebrate &amp; approve.</h1>
        
        <p class="lead">A complete, CBSE-aligned skill lab solution—from lab setup and curriculum mapping to certified faculty—everything ready before your academic session begins.</p>
        
        <div class="vl-hero-ctas">
          <a href="{{ url('/register')}}" class="vl-btn-vibrant-cta">
            To Visit Our Experience Center
            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
          </a>
          <a href="#checklist" class="vl-btn-glass-cta">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            Download Brochure
          </a>
        </div>
        
        <div class="vl-hero-stats">
          <div class="vl-stat-box">
            <span class="num">212+</span>
            <span class="label">CBSE Schools Fitted</span>
          </div>
          <div class="vl-stat-box">
            <span class="num">18</span>
            <span class="label">States Covered</span>
          </div>
          <div class="vl-stat-box">
            <span class="num">46,000+</span>
            <span class="label">Students Learning</span>
          </div>
          <div class="vl-stat-box">
            <span class="num">100%</span>
            <span class="label">Syllabus Mapped</span>
          </div>
        </div>
      </div>

      <!-- Floor Plan Schematic Card -->
      <img src="{{ asset('/frontend/img/skillbox/workonhumanservices.jpeg') }}" alt="Leaders of Learning - School with Children">
     
    </div>
  </section>

  <!-- ==================== OUTCOMES / COMPARISON ==================== -->
  <section class="vl-section bg-soft" id="outcomes">
    <div class="vidyalab-wrap">
      <div class="vl-section-head">
        <div class="vl-eyebrow">
          <span>★</span> The Real-World Difference
        </div>
        <h2>Most skill labs get installed once and <span class="vl-grad-text">inspected forever after.</span></h2>
        <p>We've walked into dozens of school storerooms to see dusty equipment boxes. Here is the critical difference between a lab that sits idle and one teachers and students love using every single week.</p>
      </div>

      <div class="vl-outcomes-grid">
        <!-- The Problem -->
        <div class="vl-outcome-card problem">
          <div class="vl-outcome-img">
            <img
              src="https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&w=900&q=80"
              alt="Unused lab equipment locked in school cupboard"
            />
          </div>
          <div class="vl-outcome-content">
            <span class="vl-outcome-tag warn">✕ What Usually Happens</span>
            <h3>A kit arrives, nobody is certified to run it</h3>
            <p>Vendors dump hardware against a purchase order, run one hasty orientation, and leave. Six months later the kits are locked in a cupboard and the "lab" is merely a brochure bullet point, not an active learning hub.</p>
            
            <ul class="vl-outcome-features">
              <li>✕ Zero alignment with weekly school timetable</li>
              <li>✕ Teachers left anxious about operating complex kits</li>
              <li>✕ Missing replacement parts cause permanent shutdowns</li>
            </ul>
          </div>
        </div>

        <!-- The Solution -->
        <div class="vl-outcome-card solution">
          <div class="vl-outcome-img">
            <img
              src="https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=900&q=80"
              alt="Students actively engaged in composite skill lab"
            />
          </div>
          <div class="vl-outcome-content">
            <span class="vl-outcome-tag good">✓ The Skillvation Standard</span>
            <h3>A complete lab mapped directly to your curriculum</h3>
            <p>Every single activity is mapped to CBSE syllabus units before installation. Your existing science and computer teachers receive supportive certification, and we perform ongoing term health audits.</p>
            
            <ul class="vl-outcome-features">
              <li>✓ 100% matched to CBSE Grade VI–XII chapters</li>
              <li>✓ Faculty trained for full operational independence</li>
              <li>✓ Guaranteed AMC, quick spares &amp; software updates</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ==================== WHAT'S INCLUDED ==================== -->
  <section class="vl-section bg-white" id="program">
    <div class="vidyalab-wrap">
      <div class="vl-section-head">
        <div class="vl-eyebrow coral">
          <span>★</span> Turnkey Implementation
        </div>
        <h2>What's included in the <span class="vl-grad-sunset">complete setup</span></h2>
        <p>One dedicated partner, one transparent contract, and complete accountability for continuous learning.</p>
      </div>

      <div class="vl-features-grid">
        <!-- Feature 1 -->
        <div class="vl-feature-card">
          <div class="vl-feature-card-img">
            <img src="https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=600&q=80"
                 alt="Robotics kits and hardware workstations" loading="lazy" />
          </div>
          <div class="vl-feature-card-body">
            <div class="vl-feature-pill f1">Hardware &amp; Furniture</div>
            <h3>Lab Hardware &amp; Workstations</h3>
            <p>Robotics kits, AI/data stations, electronics benches, and coding pods ergonomically sized to your classroom footprint.</p>
          </div>
        </div>

        <!-- Feature 2 -->
        <div class="vl-feature-card">
          <div class="vl-feature-card-img">
            <img src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=600&q=80"
                 alt="Curriculum mapping aligned to CBSE syllabus" loading="lazy" />
          </div>
          <div class="vl-feature-card-body">
            <div class="vl-feature-pill f2">Academics &amp; Pedagogy</div>
            <h3>CBSE Curriculum Mapping</h3>
            <p>Every project is mapped to specific CBSE syllabus units (Grades VI–XII) so lab sessions reinforce regular classroom theory.</p>
          </div>
        </div>

        <!-- Feature 3 -->
        <div class="vl-feature-card">
          <div class="vl-feature-card-img">
            <img src="https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=600&q=80"
                 alt="Teacher training and certification session" loading="lazy" />
          </div>
          <div class="vl-feature-card-body">
            <div class="vl-feature-pill f3">Faculty Empowerment</div>
            <h3>Teacher Training &amp; Certification</h3>
            <p>Your existing faculty are empowered and certified to facilitate practical sessions independently with no recurring vendor reliance.</p>
          </div>
        </div>

        <!-- Feature 4 -->
        <div class="vl-feature-card">
          <div class="vl-feature-card-img">
            <img src="https://images.unsplash.com/photo-1621905251918-48416bd8575a?auto=format&fit=crop&w=600&q=80"
                 alt="Annual maintenance and technical support" loading="lazy" />
          </div>
          <div class="vl-feature-card-body">
            <div class="vl-feature-pill f4">Care &amp; Spares</div>
            <h3>Annual Maintenance &amp; Support</h3>
            <p>Routine hardware servicing, continuous software updates, replacement spares, and a dedicated support helpline.</p>
          </div>
        </div>

        <!-- Feature 5 -->
        <div class="vl-feature-card">
          <div class="vl-feature-card-img">
            <img src="{{ asset('frontend/img/skillbox/compliance_documentation.jpg') }}"
                 alt="CBSE compliance documentation and inspection readiness" loading="lazy" />
          </div>
          <div class="vl-feature-card-body">
            <div class="vl-feature-pill f5">Regulatory Readiness</div>
            <h3>Compliance Documentation</h3>
            <p>Ready-to-present NEP 2020 and CBSE skill-lab audit dossiers prepared for your school inspection committee and trustees.</p>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ==================== PRICING PLANS ==================== -->
  <section class="vl-section bg-peach" id="plans">
    <div class="vidyalab-wrap">
      <div class="vl-section-head center">
        <div class="vl-eyebrow purple">
          <span>★</span> Transparent Packages
        </div>
        <h2>Choose the right lab package for <span class="vl-grad-violet">your campus</span></h2>
        <p>Every tier includes complete hardware setup, curriculum integration, and teacher certification. Select the plan tailored to your school's size.</p>
      </div>

      <div class="vl-plans-grid">

        <!-- Basic Plan -->
        <div class="vl-plan-card">
          <div class="vl-plan-tier">Basic Package</div>
          <div class="vl-plan-price">₹3 <span>Lakh</span></div>
          <div class="vl-plan-desc">One-time turnkey setup</div>
          <div class="vl-plan-summary">For schools up to 500 students — single classroom lab, core CBSE compliance ready.</div>
          
          <ul class="vl-plan-list">
            <li>
              <div class="vl-plan-check-icon">✓</div>
              <span>Lab hardware for one 400 sq ft room</span>
            </li>
            <li>
              <div class="vl-plan-check-icon">✓</div>
              <span>Robotics &amp; coding kits (Grades VI–X)</span>
            </li>
            <li>
              <div class="vl-plan-check-icon">✓</div>
              <span>Teacher training for 2 faculty members</span>
            </li>
            <li>
              <div class="vl-plan-check-icon">✓</div>
              <span>CBSE inspection readiness dossier</span>
            </li>
          </ul>
          
          <a href="{{ url('/labs/composite-skill-details') }}" target="_blank" class="vl-plan-btn-outline">
            View Basic Plan →
          </a>
        </div>

        <!-- Advance Plan (Featured) -->
        <div class="vl-plan-card featured">
          <div class="vl-plan-badge-top">★ Most Popular Choice</div>
          <div class="vl-plan-tier">Advance Package</div>
          <div class="vl-plan-price">₹6 <span>Lakh</span></div>
          <div class="vl-plan-desc">One-time turnkey setup</div>
          <div class="vl-plan-summary">For schools up to 1,000 students — full composite lab with AI modules &amp; ongoing curriculum refresh.</div>
          
          <ul class="vl-plan-list">
            <li>
              <div class="vl-plan-check-icon">✓</div>
              <span>Everything included in Basic</span>
            </li>
            <li>
              <div class="vl-plan-check-icon">✓</div>
              <span>600 sq ft expanded composite layout</span>
            </li>
            <li>
              <div class="vl-plan-check-icon">✓</div>
              <span>Dedicated AI, IoT &amp; Electronics stations</span>
            </li>
            <li>
              <div class="vl-plan-check-icon">✓</div>
              <span>Full certification for up to 5 teachers</span>
            </li>
            <li>
              <div class="vl-plan-check-icon">✓</div>
              <span>Quarterly on-site faculty refresher sessions</span>
            </li>
          </ul>
          
          <a href="#checklist" class="vl-plan-btn-featured">
            Get Advance Plan Quote →
          </a>
        </div>

        <!-- Premium Plan -->
        <div class="vl-plan-card">
          <div class="vl-plan-tier">Premium Package</div>
          <div class="vl-plan-price">₹10 <span>Lakh</span></div>
          <div class="vl-plan-desc">One-time turnkey setup</div>
          <div class="vl-plan-summary">For large institutions &amp; group schools — dual lab setup, digital infrastructure &amp; priority support.</div>
          
          <ul class="vl-plan-list">
            <li>
              <div class="vl-plan-check-icon">✓</div>
              <span>Everything included in Advance</span>
            </li>
            <li>
              <div class="vl-plan-check-icon">✓</div>
              <span>Two separate labs (400 sq ft each)</span>
            </li>
            <li>
              <div class="vl-plan-check-icon">✓</div>
              <span>High-grade 3D printers, drones &amp; robotics</span>
            </li>
            <li>
              <div class="vl-plan-check-icon">✓</div>
              <span>Dedicated relationship manager &amp; quick spares</span>
            </li>
          </ul>
          
          <a href="#checklist" class="vl-plan-btn-outline">
            Request Premium Plan →
          </a>
        </div>

      </div>

      <p style="margin-top:30px; font-size:0.92rem; color:var(--vl-muted); text-align:center; font-weight:500;">
        All package prices are indicative. Exact proposal provided after a free spatial assessment. &nbsp;·&nbsp;
        <a href="#checklist" style="color:#db2777; font-weight:800; text-decoration:underline;">Contact our advisors</a> for trust &amp; multi-branch concessions.
      </p>
    </div>
  </section>

  <!-- ==================== LEAD FORM ==================== -->
  <section class="vl-section bg-mint" id="checklist">
    <div class="vidyalab-wrap vl-form-panel">
      <div class="vl-form-copy">
        <div class="vl-eyebrow teal">
          <span>★</span> Free Compliance Guide
        </div>
        <h2>Get the CBSE Skill Lab <span class="vl-grad-sunset">Compliance Checklist 2026</span></h2>
        <p style="margin-top:14px; color:var(--vl-body); font-size:1.08rem;">A comprehensive handbook you can hand directly to your inspection committee or trustees — before making any vendor commitments.</p>
        
        <ul class="vl-checklist-items">
          <li>
            <div class="vl-checklist-badge">✓</div>
            <span>Exact CBSE &amp; NEP 2020 regulatory compliance criteria</span>
          </li>
          <li>
            <div class="vl-checklist-badge">✓</div>
            <span>Floor space, electrical safety &amp; student seating benchmarks</span>
          </li>
          <li>
            <div class="vl-checklist-badge">✓</div>
            <span>Key questions to evaluate any equipment supplier</span>
          </li>
          <li>
            <div class="vl-checklist-badge">✓</div>
            <span>Budget estimators mapped by student batch size</span>
          </li>
        </ul>
      </div>

      <div class="vl-form-card">
        <form class="vl-form" id="leadForm">
          <input type="hidden" name="course_title" value="CBSE Composite Skill Lab – Checklist Enquiry">
          <input type="hidden" name="source" value="composite-skill-lab">
          
          <div class="vl-form-grid-2">
            <div class="vl-field">
              <label for="lead_name">Your full name</label>
              <input id="lead_name" name="name" type="text" placeholder="e.g. Dr. Sunita Rao" required>
            </div>
            <div class="vl-field">
              <label for="lead_designation">Designation</label>
              <input id="lead_designation" name="designation" type="text" placeholder="e.g. Principal / Academic Director" required>
            </div>
          </div>

          <div class="vl-form-grid-2">
            <div class="vl-field">
              <label for="lead_school">School name</label>
              <input id="lead_school" name="school" type="text" placeholder="e.g. Blossom International School" required>
            </div>
            <div class="vl-field">
              <label for="lead_city">City</label>
              <input id="lead_city" name="city" type="text" placeholder="e.g. Bengaluru / Pune" required>
            </div>
          </div>

          <div class="vl-form-grid-2">
            <div class="vl-field">
              <label for="lead_phone">Phone number</label>
              <input id="lead_phone" name="phone" type="tel" placeholder="e.g. +91 98765 43210" required>
            </div>
            <div class="vl-field">
              <label for="lead_email">Email address</label>
              <input id="lead_email" name="email" type="email" placeholder="e.g. principal@school.edu.in" required>
            </div>
          </div>

          <div class="vl-field">
            <label for="lead_address">School campus address (Optional)</label>
            <textarea id="lead_address" name="address" placeholder="Campus location, road or landmark"></textarea>
          </div>

          <button type="submit" id="leadSubmitBtn" class="vl-form-submit-vibrant">
            Send me the compliance checklist
          </button>
          
          <div class="vl-form-note">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2" style="display:inline; vertical-align:-2px;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            Your information is confidential. No unsolicited spam.
          </div>
        </form>

        <div class="vl-confirm" id="confirmMsg">
          <span class="vl-confirm-icon">🎉</span>
          <h3 style="font-size:1.4rem; margin-bottom:8px; color:#db2777;">Details received successfully!</h3>
          <p style="margin:0; font-size:0.98rem; color:var(--vl-body);">The checklist PDF has been sent to your email. Our academic coordinator will reach out shortly to support you.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ==================== COMPARISON TABLE ==================== -->
  <section class="vl-section bg-white" id="compliance">
    <div class="vidyalab-wrap">
      <div class="vl-section-head center">
        <div class="vl-eyebrow purple">
          <span>★</span> Benchmark Comparison
        </div>
        <h2>How Skillvation compares to <span class="vl-grad-text">generic suppliers</span></h2>
        <p>Why leading CBSE schools trust Skillvation for end-to-end skill lab implementation.</p>
      </div>

      <div class="vl-table-card">
        <div class="vl-table-wrap">
          <table class="vl-table">
            <thead>
              <tr>
                <th>Key Criterion</th>
                <th>Generic / DIY Vendor</th>
                <th>Skillvation Complete Solution</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Curriculum Alignment</td>
                <td class="no">✕ Left entirely to teachers</td>
                <td class="yes">✓ Pre-mapped to CBSE syllabus units</td>
              </tr>
              <tr>
                <td>Deployment Timeline</td>
                <td class="no">✕ 8–14 weeks, unpredictable</td>
                <td class="yes">✓ 4–6 weeks guaranteed fixed timeline</td>
              </tr>
              <tr>
                <td>Teacher Certification</td>
                <td class="no">✕ 1 brief handover demo</td>
                <td class="yes">✓ Multi-day certification + term refreshers</td>
              </tr>
              <tr>
                <td>Maintenance &amp; Spares</td>
                <td class="no">✕ High cost per visit, slow spares</td>
                <td class="yes">✓ Included AMC + express replacement parts</td>
              </tr>
              <tr>
                <td>Compliance Dossier</td>
                <td class="no">✕ Not provided</td>
                <td class="yes">✓ Full NEP 2020 inspection documentation</td>
              </tr>
              <tr>
                <td>Price Transparency</td>
                <td class="no">✕ Hidden hardware &amp; software surcharges</td>
                <td class="yes">✓ Transparent all-inclusive turnkey quote</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>

  <!-- ==================== FAQ CAROUSEL SECTION ==================== -->
  <section class="vl-section bg-lavender" id="faq">
    <div class="vidyalab-wrap">
      <div class="vl-section-head center">
        <div class="vl-eyebrow">
          <span>★</span> Frequently Asked Questions
        </div>
        <h2>Questions school management <span class="vl-grad-sunset">usually ask</span></h2>
        <p>Everything you need to know about setup, CBSE affiliation guidelines, and ongoing lab operations.</p>
      </div>

      {{-- ── FAQ Card Carousel ────────────────────────────────── --}}
      <div class="vl-faq-carousel" id="faqCarousel">

        {{-- Slide 1 --}}
        <div class="vl-faq-slide active">
          <div class="vl-faq-grid">

            <div class="vl-faq-card">
              <div class="vl-faq-card__q">What is a Composite Skill Lab?</div>
              <div class="vl-faq-card__divider"></div>
              <div class="vl-faq-card__a">A Composite Skill Lab is an experiential multidisciplinary learning space where students gain hands-on proficiency in Robotics, Coding, AI, Electronics, and Design Thinking through structured curriculum projects.</div>
            </div>

            <div class="vl-faq-card">
              <div class="vl-faq-card__q">Is it mandatory for CBSE affiliated schools?</div>
              <div class="vl-faq-card__divider"></div>
              <div class="vl-faq-card__a">CBSE and NEP 2020 guidelines strongly emphasize setting up Composite Skill Labs for 21st-century skill education. Skillvation ensures your lab satisfies all affiliation inspection norms.</div>
            </div>

            <div class="vl-faq-card">
              <div class="vl-faq-card__q">What lab tiers does Skillvation offer?</div>
              <div class="vl-faq-card__divider"></div>
              <div class="vl-faq-card__a">
                <ul>
                  <li><strong>Basic:</strong> Core infrastructure, robotics &amp; coding tools.</li>
                  <li><strong>Advance:</strong> Expanded composite layout with AI &amp; IoT stations.</li>
                  <li><strong>Premium:</strong> Dual labs with advanced robotics, 3D printing &amp; dedicated support.</li>
                </ul>
              </div>
            </div>

          </div>
        </div>

        {{-- Slide 2 --}}
        <div class="vl-faq-slide">
          <div class="vl-faq-grid">

            <div class="vl-faq-card">
              <div class="vl-faq-card__q">How do we select the right package for our campus?</div>
              <div class="vl-faq-card__divider"></div>
              <div class="vl-faq-card__a">The package depends on your student strength, room dimensions, budget, and timetable structure. Our academic advisors offer a free on-site spatial survey to guide your selection.</div>
            </div>

            <div class="vl-faq-card">
              <div class="vl-faq-card__q">Can the lab layout and equipment be customized?</div>
              <div class="vl-faq-card__divider"></div>
              <div class="vl-faq-card__a">Yes, absolutely. We customize furniture layout, computer stations, safety provisions, and project kit quantities according to your exact room size and student batch counts.</div>
            </div>

            <div class="vl-faq-card">
              <div class="vl-faq-card__q">What deliverables are provided in the kit?</div>
              <div class="vl-faq-card__divider"></div>
              <div class="vl-faq-card__a">We supply student DIY project kits, demonstration hardware, teacher manuals, safety gear, software licenses, student workbooks, and digital curriculum portals.</div>
            </div>

          </div>
        </div>

        {{-- Slide 3 --}}
        <div class="vl-faq-slide">
          <div class="vl-faq-grid">

            <div class="vl-faq-card">
              <div class="vl-faq-card__q">Are student DIY project kits provided?</div>
              <div class="vl-faq-card__divider"></div>
              <div class="vl-faq-card__a">Yes. We supply modular, reusable STEM &amp; robotics kits with step-by-step guides so students can build tangible working models across physics, computing, and mechanics.</div>
            </div>

            <div class="vl-faq-card">
              <div class="vl-faq-card__q">How is teacher training conducted?</div>
              <div class="vl-faq-card__divider"></div>
              <div class="vl-faq-card__a">We conduct an intensive 3 to 5-day on-site training certification for your science and computer faculty, backed by video tutorials, lesson plans, and scheduled term refreshers.</div>
            </div>

            <div class="vl-faq-card">
              <div class="vl-faq-card__q">Can we upgrade our tier in future years?</div>
              <div class="vl-faq-card__divider"></div>
              <div class="vl-faq-card__a">Yes. Our modular design allows you to start with the Basic package and seamlessly expand with AI stations, 3D printers, or additional robotics pods as student enrolment increases.</div>
            </div>

          </div>
        </div>

        {{-- Slide 4 --}}
        <div class="vl-faq-slide">
          <div class="vl-faq-grid">

            <div class="vl-faq-card">
              <div class="vl-faq-card__q">What happens after installation — are we supported?</div>
              <div class="vl-faq-card__divider"></div>
              <div class="vl-faq-card__a">No school is left on their own. Annual preventive maintenance, software patches, fast spare parts replacement, and ongoing pedagogy support are bundled for the full agreement period.</div>
            </div>

            <div class="vl-faq-card">
              <div class="vl-faq-card__q">Why choose Skillvation over open-market vendors?</div>
              <div class="vl-faq-card__divider"></div>
              <div class="vl-faq-card__a">Skillvation combines Infrastructure + Certified Pedagogy + CBSE Syllabus Integration + Teacher Enablement into one unified, guaranteed ecosystem.</div>
            </div>

            <div class="vl-faq-card">
              <div class="vl-faq-card__q">How quickly can the lab be installed and ready?</div>
              <div class="vl-faq-card__divider"></div>
              <div class="vl-faq-card__a">Our turnkey rollout takes between 4 to 6 weeks from agreement signing to complete teacher handover, ensuring zero disruption to your school term.</div>
            </div>

          </div>
        </div>

        {{-- Navigation Controls --}}
        <div class="vl-faq-nav">
          <button class="vl-faq-btn" id="faqPrev" aria-label="Previous questions" disabled>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
          </button>
          
          <div class="vl-faq-dots" id="faqDots">
            <button class="vl-faq-dot active" data-slide="0" aria-label="Slide 1"></button>
            <button class="vl-faq-dot" data-slide="1" aria-label="Slide 2"></button>
            <button class="vl-faq-dot" data-slide="2" aria-label="Slide 3"></button>
            <button class="vl-faq-dot" data-slide="3" aria-label="Slide 4"></button>
          </div>
          
          <span class="vl-faq-counter" id="faqCounter">1 / 4</span>
          
          <button class="vl-faq-btn" id="faqNext" aria-label="Next questions">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
          </button>
        </div>

      </div>
    </div>
  </section>

  <!-- ==================== FINAL CALL TO ACTION ==================== -->
  <section class="vl-final-cta-vibrant" id="book">
    <div class="vidyalab-wrap">
      <div class="vl-badge-pill hero" style="margin-bottom:18px;">READY TO GET STARTED?</div>
      <h2>Ready to experience a vibrant skill lab at your school?</h2>
      <p>Schedule a friendly 15-minute consultation with our academic specialists — transparent advice on room setup, curriculum mapping, and turnkey costs.</p>
      
      <div class="vl-hero-ctas" style="margin-top:34px;">
        <a href="#checklist" class="vl-btn-vibrant-cta">
          Schedule School Consultation
          <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
        </a>
      </div>
      
      <p style="margin-top:22px; font-size:1rem; color:#fef08a; font-weight:700;">
        ✨ Direct callback within 1 working day &nbsp;·&nbsp; 🌸 Free on-campus spatial assessment
      </p>
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
      if (idx < 0 || idx >= total) return;
      
      slides[current].classList.remove('active');
      dots[current].classList.remove('active');

      current = idx;

      slides[current].classList.add('active');
      dots[current].classList.add('active');

      if (counter) counter.textContent = (current + 1) + ' / ' + total;
      if (prevBtn) prevBtn.disabled = current === 0;
      if (nextBtn) nextBtn.disabled = current === total - 1;
    }

    if (prevBtn) {
      prevBtn.disabled = true;
      prevBtn.addEventListener('click', () => goTo(current - 1));
    }
    
    if (nextBtn) {
      nextBtn.disabled = total <= 1;
      nextBtn.addEventListener('click', () => goTo(current + 1));
    }
    
    dots.forEach((dot, i) => dot.addEventListener('click', () => goTo(i)));

    // ── Lead Form Ajax Submission ─────────────────────────────
    const leadForm = document.getElementById('leadForm');
    if (leadForm) {
      leadForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const btn        = document.getElementById('leadSubmitBtn');
        const confirmMsg = document.getElementById('confirmMsg');
        const formData   = new FormData(this);

        btn.disabled    = true;
        const originalText = btn.textContent;
        btn.textContent = 'Submitting…';

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
          btn.textContent = originalText;
        });
      });
    }
  });
</script>
@endpush