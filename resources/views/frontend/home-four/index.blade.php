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
    background: linear-gradient(rgba(11, 37, 69, 0.85), rgba(0, 86, 179, 0.8)), url("{{asset('frontend/img/banner/homeban01.jpeg')}}");
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    color: #ffffff;
    padding: 75px 0 70px;
  }
  .skillvation-hero-banner h1 {
    color: #ffffff;
    font-size: clamp(32px, 4.2vw, 48px);
    line-height: 1.25;
    margin-bottom: 14px;
    font-weight: 800;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.6);
  }
  .sv-hero-tagline {
    font-size: clamp(17px, 2vw, 22px);
    font-weight: 700;
    color: #60a5fa;
    letter-spacing: 0.3px;
    margin-bottom: 16px;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.6);
  }
  .sv-hero-desc {
    font-size: 17.5px;
    line-height: 1.65;
    color: #f1f5f9;
    margin: 0 0 22px 0;
    max-width: 920px;
    font-weight: 400;
    text-shadow: 0 1px 6px rgba(0, 0, 0, 0.6);
  }
  /* ── Hero Powered By Highlight Badge ──────────────── */
  @keyframes lataaPulse {
    0%, 100% {
      box-shadow: 0 0 25px rgba(56, 189, 248, 0.4), 0 8px 30px rgba(11, 37, 69, 0.6);
      border-color: rgba(56, 189, 248, 0.7);
    }
    50% {
      box-shadow: 0 0 42px rgba(56, 189, 248, 0.8), 0 10px 36px rgba(11, 37, 69, 0.85);
      border-color: #38bdf8;
    }
  }
  .sv-hero-powered-wrap {
    margin-top: 26px;
  }
  .sv-hero-powered {
    display: inline-flex;
    align-items: center;
    gap: 14px;
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.94) 0%, rgba(30, 58, 138, 0.95) 50%, rgba(15, 23, 42, 0.94) 100%);
    border: 2px solid #38bdf8;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    padding: 10px 22px 10px 12px;
    border-radius: 9999px;
    font-size: 15px;
    color: #ffffff !important;
    text-decoration: none;
    transition: all 0.35s cubic-bezier(0.2, 0.8, 0.2, 1);
    box-shadow: 0 0 25px rgba(56, 189, 248, 0.4), 0 8px 30px rgba(11, 37, 69, 0.6);
    animation: lataaPulse 3.5s infinite ease-in-out;
    position: relative;
    max-width: 100%;
    box-sizing: border-box;
  }
  .sv-hero-powered:hover {
    background: linear-gradient(135deg, rgba(30, 58, 138, 0.98) 0%, rgba(2, 132, 199, 0.98) 100%);
    border-color: #7dd3fc;
    color: #ffffff !important;
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 0 45px rgba(56, 189, 248, 0.85), 0 14px 40px rgba(0, 0, 0, 0.5);
  }
  .sv-hero-powered .badge-chip {
    background: linear-gradient(135deg, #06b6d4 0%, #2563eb 50%, #38bdf8 100%);
    color: #ffffff;
    padding: 6px 14px;
    border-radius: 9999px;
    font-weight: 800;
    font-size: 13px;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    box-shadow: 0 0 16px rgba(6, 182, 212, 0.65);
    display: inline-flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
  }
  .sv-hero-powered .sv-powered-label {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
    color: #f1f5f9;
    line-height: 1.4;
  }
  .sv-hero-powered .sv-powered-highlight {
    color: #38bdf8;
    font-weight: 800;
    letter-spacing: 0.3px;
    text-shadow: 0 0 10px rgba(56, 189, 248, 0.6);
  }
  .sv-hero-powered .sv-arrow-btn {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: rgba(56, 189, 248, 0.2);
    border: 1px solid rgba(56, 189, 248, 0.5);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #38bdf8;
    font-size: 12px;
    margin-left: 2px;
    flex-shrink: 0;
    transition: all 0.3s ease;
  }
  .sv-hero-powered:hover .sv-arrow-btn {
    background: #38bdf8;
    color: #0f172a;
    transform: translateX(4px);
    box-shadow: 0 0 14px rgba(56, 189, 248, 0.8);
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
    .skillvation-container {
      padding: 0 16px;
    }
    .skillvation-section {
      padding: 48px 0 !important;
    }
    .skillvation-stats-grid,
    .skillvation-figures-grid,
    .skillvation-news-grid {
      grid-template-columns: 1fr;
      gap: 14px;
      margin: 24px 0;
    }
    .skillvation-stat-card {
      padding: 22px 18px;
      border-radius: 12px;
    }
    .skillvation-stat-card .stat-title {
      font-size: 18px;
      margin-bottom: 8px;
    }
    .skillvation-stat-card .stat-desc {
      font-size: 13.5px;
      line-height: 1.55;
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
  .sv-section-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 12.5px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #0284c7;
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    padding: 6px 14px;
    border-radius: 9999px;
    margin-bottom: 18px;
  }
  .sv-pillars-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-top: 36px;
  }
  @media (max-width: 991px) {
    .sv-pillars-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }
  @media (max-width: 576px) {
    .sv-pillars-grid {
      grid-template-columns: 1fr;
    }
  }
  .sv-pillar-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 22px 20px;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
  }
  .sv-pillar-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--pillar-accent, #2563eb);
  }
  .sv-pillar-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
    background: #ffffff;
    border-color: #cbd5e1;
  }
  .sv-pillar-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    margin-bottom: 14px;
    background: var(--pillar-bg, #eff6ff);
    color: var(--pillar-accent, #2563eb);
  }
  .sv-pillar-card h3 {
    font-size: 17px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 8px;
    line-height: 1.3;
  }
  .sv-pillar-card p {
    font-size: 13.5px;
    color: #64748b;
    line-height: 1.55;
    margin: 0;
  }
  .sv-pillar-card.curriculum { --pillar-accent: #2563eb; --pillar-bg: #eff6ff; }
  .sv-pillar-card.teacher { --pillar-accent: #059669; --pillar-bg: #ecfdf5; }
  .sv-pillar-card.infrastructure { --pillar-accent: #7c3aed; --pillar-bg: #f5f3ff; }
  .sv-pillar-card.intelligence { --pillar-accent: #0284c7; --pillar-bg: #f0f9ff; }

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
        <h1>Building Future-Ready Schools Through Skill Education and AI</h1>
        <div class="sv-hero-tagline">One Integrated Ecosystem. Four Powerful Solutions. One Intelligent AI Backbone.</div>
        <p class="sv-hero-desc">Skillvation brings together structured skill education, teacher development, experiential learning infrastructure and intelligent AI technology to transform how schools teach, learn and prepare students for the future.</p>
        <div class="sv-hero-powered-wrap">
          <a href="{{ route('lataa') }}" class="sv-hero-powered">
            <span class="badge-chip"><i class="fa-solid fa-microchip"></i> LATAA OS</span>
            <span class="sv-powered-label">
              <span>Powered by</span>
              <strong class="sv-powered-highlight">LATAA OS</strong>
              <span>&ndash; Learning and Teaching AI Assistant Operating System</span>
            </span>
            <span class="sv-arrow-btn"><i class="fa-solid fa-arrow-right"></i></span>
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- 2. Section 2 – Our integrated education ecosystem -->
  <section class="skillvation-section sv-section-intro" data-reveal="fade-up">
    <div class="skillvation-container">
      <div class="skillvation-grid-2col">
        <div>
          <div class="sv-section-eyebrow">
            <i class="fa-solid fa-shapes"></i> Our Integrated Education Ecosystem
          </div>
          <h2 class="mb-4" style="font-size: clamp(26px, 3.2vw, 38px); font-weight: 800; color: #0f172a; line-height: 1.25;">
            Everything a School Needs to Transform Teaching and Learning
          </h2>

          <p style="font-size: 16px; color: #334155; line-height: 1.75; margin-bottom: 16px;">
            Skill education cannot happen through curriculum alone. It requires capable teachers, meaningful learning experiences, the right infrastructure and intelligent technology.
          </p>

          <p style="font-size: 16px; color: #334155; line-height: 1.75; margin-bottom: 0;">
            Skillvation brings all these elements together through an integrated ecosystem designed to support schools, teachers and students at every stage of the learning journey.
          </p>
        </div>
        
        <div>
          <div class="skillvation-media-card" style="height: auto !important; aspect-ratio: 16/9; overflow: hidden; border-radius: 12px;">
            <iframe
              src="https://www.youtube.com/embed/nBfEYkylnXU"
              title="Skillvation Education Video"
              style="width: 100%; height: 100%; min-height: 280px; border: none; display: block;"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen>
            </iframe>
          </div>
        </div>
      </div>

      <!-- 4 Pillars Grid -->
      <div class="sv-pillars-grid">
        <div class="sv-pillar-card curriculum">
          <div class="sv-pillar-icon"><i class="fa-solid fa-book-bookmark"></i></div>
          <h3>Curriculum</h3>
          <p>Structured skill education programmes and learning pathways.</p>
        </div>
        <div class="sv-pillar-card teacher">
          <div class="sv-pillar-icon"><i class="fa-solid fa-chalkboard-user"></i></div>
          <h3>Teacher Capability</h3>
          <p>Training, upskilling and AI-assisted teaching support.</p>
        </div>
        <div class="sv-pillar-card infrastructure">
          <div class="sv-pillar-icon"><i class="fa-solid fa-microchip"></i></div>
          <h3>Infrastructure</h3>
          <p>Purpose-built skill labs and practical learning environments.</p>
        </div>
        <div class="sv-pillar-card intelligence">
          <div class="sv-pillar-icon"><i class="fa-solid fa-brain"></i></div>
          <h3>Intelligence</h3>
          <p><a href="{{ route('lataa') }}" style="color: inherit; text-decoration: none;"><strong>LATAA OS</strong> powering personalised learning and intelligent teaching.</a></p>
        </div>
      </div>
    </div>
  </section>

  <!-- 3. LATAA OS – The Intelligence Behind Skillvation -->
  <section class="skillvation-section bg-light sv-section-skills" data-reveal="slide-right">
    <div class="skillvation-container">
      <div class="skillvation-grid-2col equal" style="align-items: center; gap: 40px;">
        <div>
          <div class="sv-section-eyebrow" style="display: inline-flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #0284c7; background: #e0f2fe; padding: 4px 14px; border-radius: 999px; margin-bottom: 14px;">
            <i class="fa-solid fa-microchip"></i> Education-First AI Operating System
          </div>
          <h2 class="text-3xl font-bold mb-4" style="font-size: clamp(24px, 2.8vw, 34px); line-height: 1.25; color: #0f172a;">
            LATAA OS &ndash; <span style="color: #2563eb;">The Intelligence Behind Skillvation</span>
          </h2>
          <p style="color: #475569; font-size: 14.5px; line-height: 1.75; margin-bottom: 12px;">
            <strong>LATAA OS</strong> (Learning and Teaching AI Assistant Operating System) is a purpose-built, AI-powered education platform developed to enhance the way educational institutions plan, deliver and evaluate learning. Designed to address the evolving needs of schools, teachers and students, it brings together curriculum intelligence, AI-assisted content creation, instructional support and assessment capabilities within an integrated learning ecosystem.
          </p>
          <p style="color: #475569; font-size: 14.5px; line-height: 1.75; margin-bottom: 12px;">
            By translating curriculum requirements into structured lesson plans, teaching scripts, learning resources, classroom activities and assessments, LATAA OS enables teachers to improve instructional efficiency, deliver more engaging learning experiences and address diverse learner needs. It reduces the effort involved in academic preparation while strengthening consistency, relevance and effectiveness in classroom delivery.
          </p>
          <p style="color: #475569; font-size: 14.5px; line-height: 1.75; margin-bottom: 12px;">
            As the intelligence layer powering Skillvation, LATAA OS connects our programmes, laboratories and teacher development initiatives with technology-driven academic support—helping schools move beyond access to educational resources towards more structured, effective and outcome-oriented learning.
          </p>
          <p style="color: #1e1b4b; font-size: 15px; font-weight: 700; line-height: 1.6; margin-top: 14px; margin-bottom: 20px;">
            LATAA OS powers our vision of making skill education smarter, more connected and future-ready.
          </p>
          <a href="{{ route('lataa') }}" class="skillvation-pill-btn" style="display: inline-flex; align-items: center; gap: 10px; background: linear-gradient(135deg, #1d4ed8, #0284c7); color: #ffffff !important; padding: 11px 26px; border-radius: 999px; font-weight: 700; font-size: 13.5px; text-decoration: none; box-shadow: 0 4px 14px rgba(2, 132, 199, 0.35); transition: transform .25s ease, box-shadow .25s ease;">
            <span>Explore LATAA OS Platform</span>
            <i class="fa-solid fa-arrow-right text-xs"></i>
          </a>
        </div>

        <div style="display: flex; justify-content: center; align-items: center;">
          <a href="{{ route('lataa') }}" title="Explore LATAA OS Ecosystem" style="display: block; max-width: 490px; width: 100%; text-decoration: none; transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;">
            <div style="position: relative; border-radius: 24px; overflow: hidden; box-shadow: 0 16px 36px -8px rgba(15, 23, 42, 0.16); border: 1px solid rgba(226, 232, 240, 0.8); background: #ffffff;">
              <img src="{{ asset('frontend/img/skillbox/lataa_ecosystem_diagram.jpg') }}" alt="LATAA OS Platform - Intelligent Skill Ecosystem" style="width: 100%; height: auto; display: block; object-fit: contain;">
            </div>
          </a>
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
      .sv-eco-intro{text-align:center;max-width:1200px;margin:0 auto 72px;}
      .sv-eco-intro-header{max-width:920px;margin:0 auto;}
      .sv-eco-intro h2{font-size:clamp(28px,3.8vw,34px);font-weight:800;color:#1e1b4b;line-height:1.2;margin-bottom:22px;}
      .sv-eco-intro p{font-size:17px;color:#475569;line-height:1.8;margin-bottom:14px;}
      .sv-lataa-core-hub{display:inline-flex;flex-direction:column;align-items:center;background:linear-gradient(135deg,#1e1b4b 0%,#1e3a8a 100%);color:#ffffff;padding:18px 36px;border-radius:18px;box-shadow:0 12px 28px rgba(30,27,75,0.18);margin:16px auto 28px;border:1px solid rgba(96,165,250,0.35);text-decoration:none;transition:transform .3s ease;}
      .sv-lataa-core-hub:hover{transform:translateY(-2px);color:#ffffff;}
      .sv-lataa-core-badge{display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#2563eb,#38bdf8);color:#ffffff;font-size:13px;font-weight:800;letter-spacing:0.08em;padding:4px 14px;border-radius:999px;margin-bottom:8px;text-transform:uppercase;}
      .sv-lataa-core-title{font-size:17px;font-weight:700;color:#f1f5f9;letter-spacing:0.02em;}
      
      /* Big & Colorful Transform Grid */
      .sv-lataa-transform-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:22px;margin:32px auto 36px;text-align:left;width:100%;}
      @media(max-width:1024px){.sv-lataa-transform-grid{grid-template-columns:repeat(2,1fr);gap:18px;}}
      @media(max-width:640px){.sv-lataa-transform-grid{grid-template-columns:1fr;gap:16px;}}
      
      .sv-transform-card{position:relative;border-radius:22px;padding:28px 24px;transition:all .35s cubic-bezier(.34,1.56,.64,1);display:flex;flex-direction:column;justify-content:space-between;min-height:245px;text-decoration:none;overflow:hidden;color:#ffffff;}
      .sv-transform-card::before{content:'';position:absolute;top:-40px;right:-40px;width:140px;height:140px;border-radius:50%;background:radial-gradient(circle,rgba(255,255,255,.24) 0%,transparent 70%);pointer-events:none;}
      .sv-transform-card:hover{transform:translateY(-8px) scale(1.02);color:#ffffff;}
      
      .sv-transform-card.card-camps{background:linear-gradient(140deg,#f59e0b 0%,#ea580c 50%,#c2410c 100%);box-shadow:0 14px 30px -6px rgba(234,88,12,.42),0 6px 14px rgba(217,119,6,.2);border:1px solid rgba(254,215,170,.45);}
      .sv-transform-card.card-camps:hover{box-shadow:0 22px 42px -6px rgba(234,88,12,.55),0 8px 18px rgba(217,119,6,.3);}
      
      .sv-transform-card.card-ttt{background:linear-gradient(140deg,#10b981 0%,#059669 50%,#047857 100%);box-shadow:0 14px 30px -6px rgba(5,150,105,.42),0 6px 14px rgba(5,150,105,.2);border:1px solid rgba(167,243,208,.45);}
      .sv-transform-card.card-ttt:hover{box-shadow:0 22px 42px -6px rgba(5,150,105,.55),0 8px 18px rgba(5,150,105,.3);}
      
      .sv-transform-card.card-u4t{background:linear-gradient(140deg,#f43f5e 0%,#e11d48 50%,#be123c 100%);box-shadow:0 14px 30px -6px rgba(225,29,72,.42),0 6px 14px rgba(225,29,72,.2);border:1px solid rgba(254,205,211,.45);}
      .sv-transform-card.card-u4t:hover{box-shadow:0 22px 42px -6px rgba(225,29,72,.55),0 8px 18px rgba(225,29,72,.3);}
      
      .sv-transform-card.card-labs{background:linear-gradient(140deg,#3b82f6 0%,#2563eb 50%,#1d4ed8 100%);box-shadow:0 14px 30px -6px rgba(37,99,235,.42),0 6px 14px rgba(37,99,235,.2);border:1px solid rgba(191,219,254,.45);}
      .sv-transform-card.card-labs:hover{box-shadow:0 22px 42px -6px rgba(37,99,235,.55),0 8px 18px rgba(37,99,235,.3);}
      
      .sv-transform-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;}
      .sv-transform-icon-box{width:48px;height:48px;border-radius:14px;background:rgba(255,255,255,.22);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.35);display:flex;align-items:center;justify-content:center;font-size:20px;color:#ffffff;}
      .sv-transform-badge{font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;background:rgba(255,255,255,.22);backdrop-filter:blur(6px);border:1px solid rgba(255,255,255,.35);padding:4px 12px;border-radius:999px;color:#ffffff;}
      .sv-transform-card h3{font-size:24px;font-weight:900;color:#ffffff;margin:0 0 6px;letter-spacing:.02em;line-height:1.15;}
      .sv-transform-card .sv-transform-subtitle{font-size:14.5px;font-weight:700;color:rgba(255,255,255,.95);margin-bottom:10px;line-height:1.35;}
      .sv-transform-card p{font-size:13.5px;color:rgba(255,255,255,.9);line-height:1.55;margin:0 0 16px;font-weight:500;}
      .sv-transform-link{display:inline-flex;align-items:center;gap:6px;font-size:12.5px;font-weight:800;color:#ffffff;text-transform:uppercase;letter-spacing:.06em;opacity:.92;transition:gap .25s ease,opacity .25s ease;}
      .sv-transform-card:hover .sv-transform-link{gap:10px;opacity:1;}
      .sv-eco-connected-banner{background:#f1f5f9;border:1px dashed #cbd5e1;border-radius:12px;padding:12px 24px;display:inline-flex;align-items:center;gap:18px;margin:0 auto 24px;flex-wrap:wrap;justify-content:center;}
      .sv-eco-connected-title{font-size:13px;font-weight:700;color:#334155;text-transform:uppercase;letter-spacing:0.06em;display:inline-flex;align-items:center;gap:8px;}
      .sv-eco-connected-nodes{display:inline-flex;align-items:center;gap:10px;font-size:14px;font-weight:600;color:#1e40af;}
      .sv-eco-connected-nodes .sep{color:#94a3b8;}
      .sv-lataa-conclusion{font-size:16.5px;color:#475569;line-height:1.8;max-width:840px;margin:0 auto;}
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
        .sv-eco-intro{margin:0 auto 48px;}
        .sv-eco-row,.sv-eco-row.rev-layout{grid-template-columns:1fr;direction:ltr;margin-bottom:20px;}
        .sv-eco-img-pane{min-height:240px;height:260px;}
        .sv-eco-img-pane img{transform:none !important;}
        .sv-eco-content{padding:28px 22px;}
        .sv-eco-bullets{grid-template-columns:1fr;}
        .sv-journey-grid{grid-template-columns:1fr;}
        .sv-commit{grid-template-columns:1fr;}
        .sv-tagline{min-width:0;}
        .sv-eco-pipeline{padding:36px 20px;}
        .sv-eco-stripe{gap:12px;margin:0 auto 36px;}
        .sv-eco-stripe h2{font-size:22px;white-space:normal;text-align:center;}
        .sv-eco-stripe-line{display:none;}
      }

      @media(max-width:640px){
        #sv-ecosystem{padding-top:48px !important;padding-bottom:48px !important;}
        .sv-eco-eyebrow{font-size:11px;padding:4px 12px;margin-bottom:14px;white-space:normal;text-align:center;}
        .sv-eco-intro h2{font-size:24px;line-height:1.25;margin-bottom:14px;}
        .sv-eco-intro p{font-size:14.5px;line-height:1.65;}
        
        .sv-lataa-core-hub{width:100%;max-width:360px;padding:14px 16px;margin:12px auto 18px;box-sizing:border-box;}
        .sv-lataa-core-badge{font-size:11px;padding:3px 10px;}
        .sv-lataa-core-title{font-size:14px;text-align:center;line-height:1.35;}

        .sv-lataa-transform-grid{grid-template-columns:1fr;gap:14px;margin:18px auto 26px;}
        .sv-transform-card{min-height:auto;padding:22px 18px;border-radius:18px;}
        .sv-transform-card:hover{transform:translateY(-4px) scale(1.01);}
        .sv-transform-top{margin-bottom:14px;}
        .sv-transform-icon-box{width:42px;height:42px;font-size:18px;border-radius:12px;}
        .sv-transform-badge{font-size:10px;padding:3px 10px;}
        .sv-transform-card h3{font-size:21px;margin-bottom:3px;}
        .sv-transform-subtitle{font-size:13.5px;margin-bottom:6px;}
        .sv-transform-card p{font-size:13px;line-height:1.5;margin-bottom:14px;}
        .sv-transform-link{font-size:12px;}

        .sv-eco-connected-banner{display:flex;flex-direction:column;gap:8px;padding:12px 14px;width:100%;border-radius:12px;margin:0 auto 20px;}
        .sv-eco-connected-title{font-size:11.5px;}
        .sv-eco-connected-nodes{font-size:12.5px;gap:8px;flex-wrap:wrap;justify-content:center;}
        .sv-lataa-conclusion{font-size:14px;line-height:1.65;}

        .sv-eco-row{border-radius:16px;margin-bottom:18px;}
        .sv-eco-img-pane{height:220px;min-height:220px;}
        .sv-eco-badge{bottom:12px;left:12px;padding:6px 12px 6px 6px;max-width:calc(100% - 24px);box-sizing:border-box;}
        .sv-eco-badge-icon{width:28px;height:28px;}
        .sv-eco-badge-icon i{font-size:12px !important;}
        .sv-eco-badge span{font-size:11.5px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .sv-eco-num{top:12px;right:12px;width:34px;height:34px;font-size:14px;}

        .sv-eco-content{padding:22px 18px;}
        .sv-eco-tag{font-size:10px;padding:3px 10px;margin-bottom:12px;}
        .sv-eco-content h3{font-size:20px;line-height:1.3;margin-bottom:6px;}
        .sv-eco-sub-label{font-size:12px;margin-bottom:10px;}
        .sv-eco-content p.body{font-size:13.5px;line-height:1.65;margin-bottom:16px;}
        .sv-eco-bullets{gap:8px;margin-bottom:16px;}
        .sv-eco-bullets li{font-size:13px;line-height:1.5;}
        .sv-eco-quote{font-size:12.5px;padding-top:12px;line-height:1.55;}

        .sv-eco-pipeline{padding:28px 16px;border-radius:16px;margin-top:36px;}
        .sv-eco-pipeline h2{font-size:21px;line-height:1.25;}
        .sv-pipe-sub{font-size:11px;margin-bottom:20px;}
        .sv-journey-card{padding:18px 14px;}
        .sv-commit{padding-top:20px;gap:18px;}
        .sv-tagline{padding:18px 14px;}
        .sv-tagline p{font-size:19px;}
        .sv-tagline em{font-size:12px;}

        .sv-advantage-section{margin-top:44px;}
        .sv-advantage-section h2{font-size:21px;margin-bottom:20px;}
        .sv-advantage-card{padding:18px 16px;}
      }

      /* ── The Skillvation Advantage ── */
      .sv-advantage-section {
        margin-top: 72px;
        text-align: center;
      }
      .sv-advantage-section h2 {
        font-size: clamp(24px, 3.2vw, 38px);
        font-weight: 800;
        color: #1e1b4b;
        margin-bottom: 36px;
      }
      .sv-advantage-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px;
        text-align: left;
      }
      @media(max-width: 920px) {
        .sv-advantage-grid { grid-template-columns: repeat(2, 1fr); }
      }
      @media(max-width: 580px) {
        .sv-advantage-grid { grid-template-columns: 1fr; }
      }
      .sv-advantage-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 22px 20px;
        box-shadow: 0 4px 16px rgba(30, 27, 75, 0.05);
        display: flex;
        align-items: flex-start;
        gap: 14px;
        transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
      }
      .sv-advantage-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(37, 99, 235, 0.12);
        border-color: #93c5fd;
      }
      .sv-adv-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 16px;
        background: #eff6ff;
        color: #2563eb;
      }
      .sv-advantage-card p {
        font-size: 14.5px;
        color: #334155;
        line-height: 1.55;
        margin: 0;
        font-weight: 600;
      }
      .sv-advantage-card.full-span {
        grid-column: 1 / -1;
        background: linear-gradient(135deg, #eff6ff 0%, #f5f3ff 100%);
        border: 1px solid #c7d2fe;
        justify-content: center;
        text-align: center;
      }
      .sv-advantage-card.full-span .sv-adv-icon {
        background: #3b82f6;
        color: #fff;
      }

      /* ── Closing Banner ── */
      .sv-closing-banner {
        margin-top: 56px;
        border-radius: 22px;
        overflow: hidden;
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #1e3a8a 100%);
        padding: 56px 48px;
        color: #ffffff;
        text-align: center;
        position: relative;
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.22);
      }
      .sv-closing-banner::before {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 280px;
        height: 280px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(56, 189, 248, 0.25) 0%, transparent 70%);
        pointer-events: none;
      }
      .sv-closing-banner::after {
        content: '';
        position: absolute;
        bottom: -50px;
        left: -50px;
        width: 240px;
        height: 240px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(168, 85, 247, 0.2) 0%, transparent 70%);
        pointer-events: none;
      }
      .sv-closing-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: #38bdf8;
        background: rgba(56, 189, 248, 0.12);
        border: 1px solid rgba(56, 189, 248, 0.3);
        padding: 5px 16px;
        border-radius: 999px;
        margin-bottom: 20px;
      }
      .sv-closing-banner h2 {
        font-size: clamp(24px, 3.5vw, 40px);
        font-weight: 800;
        color: #ffffff;
        line-height: 1.25;
        margin-bottom: 20px;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
      }
      .sv-closing-banner p {
        font-size: 17px;
        color: #e2e8f0;
        line-height: 1.75;
        max-width: 820px;
        margin: 0 auto 14px;
      }
      .sv-closing-powered {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        margin-top: 24px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.22);
        backdrop-filter: blur(8px);
        padding: 10px 24px;
        border-radius: 999px;
        font-size: 15.5px;
        font-weight: 700;
        color: #ffffff !important;
        text-decoration: none;
        transition: all .3s ease;
      }
      .sv-closing-powered:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: #38bdf8;
        transform: translateY(-2px);
      }
      .sv-closing-powered .badge {
        background: linear-gradient(135deg, #2563eb, #38bdf8);
        color: #fff;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .05em;
      }
    </style>

    <div class="skillvation-container">

      {{-- How LATAA OS transforms the Skillvation ecosystem --}}
      <div class="sv-eco-intro" data-reveal="fade-up">
        <div class="sv-eco-eyebrow"><i class="fa-solid fa-robot" style="font-size:11px;"></i> How LATAA OS Transforms the Skillvation Ecosystem</div>
        <h2>From Planning to Practice. <span style="color:#2563eb;">From Learning to Mastery.</span></h2>
        
        <a href="{{ route('lataa') }}" class="sv-lataa-core-hub">
          <span class="sv-lataa-core-badge"><i class="fa-solid fa-microchip"></i> LATAA OS</span>
          <span class="sv-lataa-core-title">The AI Intelligence Layer Across Skillvation</span>
        </a>

        <div class="sv-lataa-transform-grid">
          <!-- 1. CAMPS -->
          <a href="{{ route('skill2school') }}" class="sv-transform-card card-camps" title="Explore CAMPS Programme">
            <div>
              <div class="sv-transform-top">
                <div class="sv-transform-icon-box">
                  <i class="fa-solid fa-shapes"></i>
                </div>
                <!-- <span class="sv-transform-badge">Grades 1–5</span> -->
              </div>
              <h3>CAMPS</h3>
              <div class="sv-transform-subtitle">AI-Assisted Student Learning</div>
              <!-- <p>Personalised foundational learning, experiential activities &amp; competency development.</p> -->
            </div>
            <!-- <div class="sv-transform-link">
              <span>Explore CAMPS</span>
              <i class="fa-solid fa-arrow-right text-xs"></i>
            </div> -->
          </a>

          <!-- 2. TTT -->
          <a href="{{ route('upskill4teacher') }}" class="sv-transform-card card-ttt" title="Explore TTT Programme">
            <div>
              <div class="sv-transform-top">
                <div class="sv-transform-icon-box">
                  <i class="fa-solid fa-chalkboard-user"></i>
                </div>
                <!-- <span class="sv-transform-badge">Educator Prep</span> -->
              </div>
              <h3>TTT</h3>
              <div class="sv-transform-subtitle">AI-Assisted Teaching Preparation</div>
              <!-- <p>Lesson plans, teaching scripts, worksheets, question banks &amp; classroom delivery resources.</p> -->
            </div>
            <!-- <div class="sv-transform-link">
              <span>Explore TTT</span>
              <i class="fa-solid fa-arrow-right text-xs"></i>
            </div> -->
          </a>

          <!-- 3. U4S -->
          <a href="{{ route('upskill4school') }}" class="sv-transform-card card-u4t" title="Explore U4S Programme">
            <div>
              <div class="sv-transform-top">
                <div class="sv-transform-icon-box">
                  <i class="fa-solid fa-award"></i>
                </div>
                <!-- <span class="sv-transform-badge">Institutional Growth</span> -->
              </div>
              <h3>U4S</h3>
              <div class="sv-transform-subtitle">AI-Enabled School Development</div>
              <!-- <p>Continuous institutional upskilling, modern pedagogy, ed-tech awareness &amp; leadership.</p> -->
            </div>
            <!-- <div class="sv-transform-link">
              <span>Explore U4T</span>
              <i class="fa-solid fa-arrow-right text-xs"></i>
            </div> -->
          </a>

          <!-- 4. Skill Labs -->
          <a href="{{ route('labs.composite-skill-basic') }}" class="sv-transform-card card-labs" title="Explore Skill Labs">
            <div>
              <div class="sv-transform-top">
                <div class="sv-transform-icon-box">
                  <i class="fa-solid fa-flask-vial"></i>
                </div>
                <!-- <span class="sv-transform-badge">Hands-On Hub</span> -->
              </div>
              <h3>Skill Labs</h3>
              <div class="sv-transform-subtitle">AI-Supported Practical Learning</div>
              <!-- <p>Composite Skill Labs &amp; Skillvation Labs with DIY kits, experiment guides &amp; project learning.</p> -->
            </div>
            <!-- <div class="sv-transform-link">
              <span>Explore Labs</span>
              <i class="fa-solid fa-arrow-right text-xs"></i>
            </div> -->
          </a>
        </div>

        <div class="sv-eco-connected-banner">
          <span class="sv-eco-connected-title"><i class="fa-solid fa-network-wired"></i> Connected Education Ecosystem</span>
          <div class="sv-eco-connected-nodes">
            <span><i class="fa-solid fa-school"></i> Schools</span>
            <span class="sep">&bull;</span>
            <span><i class="fa-solid fa-chalkboard-user"></i> Teachers</span>
            <span class="sep">&bull;</span>
            <span><i class="fa-solid fa-user-graduate"></i> Students</span>
          </div>
        </div>

        <p class="sv-lataa-conclusion">
          LATAA OS ensures that technology is not an isolated addition to education. It becomes a working part of the teaching and learning process, connecting academic content, teacher capabilities and student development.
        </p>
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
          'rev'   => false,
          'num'   => '01',
          'img'   => asset('frontend/img/skillbox/camps_ecosystem_new.jpg'),
          'alt'   => 'CAMPS - Foundational Skill Education for Primary School',
          'grad'  => 'linear-gradient(135deg,rgba(180,83,9,.25),rgba(245,158,11,.1))',
          'bBg'   => '#b45309','bIcon'=>'fa-building-columns','bLabel'=>'CAMPS',
          'tagBg' => '#fffbeb','tagClr'=>'#b45309','tagIcon'=>'fa-building-columns','tagLabel'=>'Learning Environments',
          'clr'   => '#b45309','title'=>'Making Skill Education an Integral Part of School Learning',
          'sub'   => 'What CAMPS offers:',
          'body'  => 'CAMPS enables schools to implement structured, curriculum-aligned skill education that goes beyond textbooks and examinations.<br> Through practical activities, project-based learning, competency development and AI-supported learning experiences, students develop the confidence and capabilities needed to apply knowledge in real-life situations.',
          'pts'   => ['Structured skill education curriculum and learning pathways','Practical and experiential learning activities','Curriculum-aligned projects, DIY kits and learning resources','Dedicated experiential spaces','Competency-based student development and assessment','AI-assisted explanations, practice and personalised learning through LATAA OS', 'student progress insights and learning support'],
          'qt'    => 'Empowered by LATAA OS: Students receive intelligent learning assistance, while teachers can use AI to prepare lessons, activities, assessments and learning resources',
        ],
        [
          'rev'   => true,
          'num'   => '02',
          'img'   => asset('frontend/img/skillbox/teacher_upskilling.jpg'),
          'alt'   => 'Train the Teacher Programme',
          'grad'  => 'linear-gradient(135deg,rgba(4,120,87,.6),rgba(16,185,129,.35))',
          'bBg'   => '#047857','bIcon'=>'fa-chalkboard-user','bLabel'=>'TTT – Train the Teacher',
          'tagBg' => '#ecfdf5','tagClr'=>'#047857','tagIcon'=>'fa-chalkboard-user','tagLabel'=>'Teacher Development',
          'clr'   => '#047857','title'=>'Transforming Teachers into Confident Learning Facilitators',
          'sub'   => 'What TTT offers:',
          'body'  => 'Great education begins with empowered teachers. Our Train the Teacher programme equips educators with the knowledge, teaching methodologies and practical skills required to deliver engaging, effective and competency-based learning.',
          'pts'   => ['Structured teacher training programmes','Pedagogy and experiential learning methodologies','Subject-specific teaching strategies','Classroom management and student engagement','Activity-based and project-based teaching','Practical training for skill education implementation','AI-enabled lesson preparation and teaching assistance'],
          'qt'    => 'Empowered by LATAA OS: Teachers gain an intelligent teaching assistant to support lesson planning, teaching scripts, worksheets, question banks, assessments and classroom resources.',
        ],
        [
          'rev'   => false,
          'num'   => '03',
          'img'   => asset('frontend/img/skillbox/teacher_upskilling.jpg'),
          'alt'   => 'Upskill For School Programme',
          'grad'  => 'linear-gradient(135deg,rgba(223, 63, 63, 0.6),rgba(226, 127, 88, 0.35))',
          'bBg'   => '#882b00','bIcon'=>'fa-school','bLabel'=>'U4S – Upskill for Schools',
          'tagBg' => '#ecfdf5','tagClr'=>'#882b00','tagIcon'=>'fa-school','tagLabel'=>'School Development',
          'clr'   => '#882b00','title'=>'Continuous Learning for the Schools of Tomorrow',
          'sub'   => 'What U4S offers:',
          'body'  => 'Education is an evolving landscape. U4S provides schools and educators with opportunities to continuously upgrade institutional capabilities, modern pedagogy, and classroom practices through structured learning programmes and digital experiences.',
          'pts'   => ['Institutional professional development courses','Subject and domain-specific school upskilling','Modern teaching methodologies and leadership','Educational technology and AI awareness','Classroom management and academic excellence','Communication, leadership and school development','Continuous institutional growth pathways'],
          'qt'    => 'Empowered by LATAA OS: Schools can access AI-assisted learning, explore new concepts, strengthen institutional knowledge and receive support in applying modern pedagogical practices.',
        ],
        [
          'rev'   => true,
          'num'   => '04',
          'img'   => asset('frontend/img/skillbox/Workonlifeforms.jpeg'),
          'alt'   => 'Composite Skill Lab & Skillvation Lab',
          'grad'  => 'linear-gradient(135deg,rgba(190,24,93,.6),rgba(236,72,153,.35))',
          'bBg'   => '#be185d','bIcon'=>'fa-screwdriver-wrench','bLabel'=>'Composite Skill Lab & Skillvation Lab',
          'tagBg' => '#fdf2f8','tagClr'=>'#be185d','tagIcon'=>'fa-screwdriver-wrench','tagLabel'=>'Project-Based Learning',
          'clr'   => '#be185d','title'=>'Where Knowledge Transforms into Practical Skills',
          'sub'   => 'What our labs offer:',
          'body'  => 'Skillvation designs and develops practical learning environments that enable students to explore, experiment, create and apply their knowledge.<br> From CBSE-aligned Composite Skill Labs to customised Skillvation Labs for State Board and ICSE schools, we provide integrated infrastructure, curriculum, learning resources and implementation support.',
          'pts'   => ['CBSE-aligned Composite Skill Lab solutions','Customised Skillvation Labs for State and ICSE schools','Purpose-built infrastructure and equipment','Curriculum and session-wise lesson plans','Teacher demonstration resources and student DIY project kits','Teacher training and implementation support','AI-assisted curriculum preparation and teaching resources'],
          'qt'    => 'Empowered by LATAA OS: Teachers can plan practical sessions, access instructional resources and create supporting learning materials. Students can receive AI-guided conceptual support to complement their hands-on lab experiences.',
        ],
        [
          'rev'   => false,
          'num'   => '05',
          'img'   => asset('frontend/img/skillbox/lataa_ecosystem_new.jpg'),
          'alt'   => 'LATAA OS - Education-First AI Operating System',
          'grad'  => 'linear-gradient(135deg,rgba(3,105,161,.25),rgba(14,165,233,.1))',
          'bBg'   => '#0369a1','bIcon'=>'fa-robot','bLabel'=>'LATAA OS',
          'tagBg' => '#e0f2fe','tagClr'=>'#0369a1','tagIcon'=>'fa-robot','tagLabel'=>'Learning and Teaching AI Assistant Operating System',
          'clr'   => '#0369a1','title'=>'One Intelligent AI. Every Learning Journey',
          'sub'   => 'LATAA OS empowers every Skillvation solution:',
          'body'  => 'At the heart of Skillvation is LATAA OS, our purpose-built AI operating system designed exclusively for educational institutions.<br> LATAA OS brings intelligent assistance to students, teachers and academic institutions. It supports personalised learning, lesson preparation, teaching resources, assessments and academic development – making everyday teaching and learning more effective, accessible and engaging.<br> Unlike general-purpose AI tools, LATAA OS is designed around the needs of schools, educators and learners.',
          'pts'   => ['CAMPS: Supports personalised student learning, conceptual understanding, practice and competency development','TTT: Assists teachers with lesson plans, teaching scripts, classroom resources and assessments','U4T: Supports continuous professional learning, subject enrichment and teacher development','Composite Skill Labs & Skillvation Labs: Supports curriculum planning, practical session preparation, instructional resources and project-based learning'],
          'qt'    => 'One AI ecosystem. Multiple educational applications. One connected learning experience.',
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

      {{-- Section – The Skillvation advantage --}}
      <div class="sv-advantage-section" data-reveal="fade-up">
        <div class="sv-eco-eyebrow"><i class="fa-solid fa-award" style="font-size:11px;"></i> The Skillvation Advantage</div>
        <h2>Why Schools Choose Skillvation</h2>

        <div class="sv-advantage-grid">
          <div class="sv-advantage-card">
            <div class="sv-adv-icon"><i class="fa-solid fa-layer-group"></i></div>
            <p>One integrated partner for skill education, teacher development and learning infrastructure.</p>
          </div>
          <div class="sv-advantage-card">
            <div class="sv-adv-icon"><i class="fa-solid fa-book-bookmark"></i></div>
            <p>Curriculum-driven programmes supported by practical implementation.</p>
          </div>
          <div class="sv-advantage-card">
            <div class="sv-adv-icon"><i class="fa-solid fa-chalkboard-user"></i></div>
            <p>Continuous teacher training and professional development.</p>
          </div>
          <div class="sv-advantage-card">
            <div class="sv-adv-icon"><i class="fa-solid fa-brain"></i></div>
            <p>AI-powered assistance for both teaching and learning.</p>
          </div>
          <div class="sv-advantage-card">
            <div class="sv-adv-icon"><i class="fa-solid fa-flask-vial"></i></div>
            <p>Experiential learning through structured activities and skill labs.</p>
          </div>
          <div class="sv-advantage-card">
            <div class="sv-adv-icon"><i class="fa-solid fa-sliders"></i></div>
            <p>Personalised and scalable solutions based on institutional requirements.</p>
          </div>
          <div class="sv-advantage-card full-span">
            <div class="sv-adv-icon"><i class="fa-solid fa-network-wired"></i></div>
            <p>A connected ecosystem that supports the school, teacher and student.</p>
          </div>
        </div>
      </div>

      {{-- Section - Closing banner --}}
      <div class="sv-closing-banner" data-reveal="fade-up">
        <div class="sv-closing-eyebrow"><i class="fa-solid fa-sparkles"></i> The Future of Education</div>
        <h2>The Future of Education Is Integrated.<br>The Future Is Intelligent.</h2>
        <p>Give your school more than a curriculum, more than a lab and more than a digital tool.</p>
        <p>Give your teachers the right support, your students the right learning experiences and your institution the intelligence to move forward.</p>
        <div>
          <a href="{{ route('lataa') }}" class="sv-closing-powered">
            <span class="badge">LATAA OS</span>
            <span>Build a future-ready school with <strong>Skillvation</strong>, powered by <strong>LATAA OS</strong></span>
            <i class="fa-solid fa-arrow-right" style="font-size:12px;"></i>
          </a>
        </div>
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
