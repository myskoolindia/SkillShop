@extends('frontend.home-four.layouts.master')

@section('meta_title', 'Composite Skill Lab for CBSE Schools | Setup from ₹3L | Skillvation')
@section('meta_description', 'Set up a CBSE-compliant Composite Skill Lab in just 3–4 weeks. Everything you need for CBSE Skill-75/2024 — equipment, installation, training & documentation. Starting at ₹3 Lakh.')

@push('styles')
<style>
  .dot-bg {
    background-image: radial-gradient(#e2e8f0 1px, transparent 1px);
    background-size: 22px 22px;
  }
  details summary::-webkit-details-marker { display: none; }
  details[open] .faq-chevron { transform: rotate(180deg); }
  .skill-card { transition: transform 0.25s ease, box-shadow 0.25s ease; }
  .skill-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(0,0,0,0.10); }
</style>
@endpush

@section('contents')

{{-- =====================================================================
     1. HERO
     ===================================================================== --}}
<section class="dot-bg pt-14 pb-16 lg:pt-20 lg:pb-24 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

      {{-- Left --}}
      <div class="space-y-6">
        <div class="flex items-center gap-2 text-xs text-slate-400 font-semibold">
          <a href="{{ route('labs') }}" class="hover:text-brand-orange transition-colors">Labs</a>
          <i class="fa-solid fa-chevron-right text-[9px]"></i>
          <span class="text-slate-600">Composite Skill Lab</span>
        </div>

        <h1 class="text-3xl sm:text-4xl lg:text-[42px] font-extrabold text-brand-navy leading-[1.15]">
          Set Up a CBSE-Compliant<br>
          <span class="text-brand-orange">Composite Skill Lab</span> in Just<br>
          3–4 Weeks
        </h1>

        <p class="text-base sm:text-lg text-slate-600">
          Everything You Need for CBSE Skill Education
          <strong class="text-brand-orange">Starting at ₹3 Lakh</strong>
        </p>

        <ul class="space-y-2.5">
          @foreach([
            'CBSE Skill-75/2024 Compliant',
            'Complete Equipment + Installation + Training',
            'Documentation for Affiliation Inspections',
            'Delivered Across India',
          ] as $pt)
          <li class="flex items-center gap-2.5 text-sm text-slate-700">
            <span class="w-2 h-2 rounded-full bg-brand-orange flex-shrink-0"></span>{{ $pt }}
          </li>
          @endforeach
        </ul>

        <div class="flex flex-wrap gap-4 pt-2">
          <a href="#demo"
             class="inline-flex items-center justify-center px-6 py-3 rounded-lg bg-brand-navy hover:bg-brand-darknavy text-white text-sm font-bold shadow transition-all hover:-translate-y-0.5">
            Book a Free Lab Consultation
          </a>
          <a href="#brochure"
             class="inline-flex items-center justify-center px-6 py-3 rounded-lg border-2 border-brand-navy text-brand-navy text-sm font-bold hover:bg-slate-50 transition-all">
            Download Brochure
          </a>
        </div>
      </div>

      {{-- Right --}}
      <div class="relative">
        <div class="absolute -inset-3 bg-brand-orange/10 rounded-3xl rotate-1 blur-sm"></div>
        <div class="relative rounded-2xl overflow-hidden shadow-2xl border border-slate-200">
          <img src="https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=900&q=85"
               alt="Students working on composite skill lab robotics projects"
               class="w-full aspect-[4/3] object-cover" />
        </div>
      </div>
    </div>

    {{-- Stats row --}}
    <div class="mt-14 grid grid-cols-2 sm:grid-cols-4 gap-4">
      @foreach([
        ['icon'=>'fa-school',           'stat'=>'500+',          'label'=>'Schools Served'],
        ['icon'=>'fa-calendar-check',   'stat'=>'3–4 Weeks',     'label'=>'Setup Timeline'],
        ['icon'=>'fa-screwdriver-wrench','stat'=>'100%',         'label'=>'Inspection-Ready Docs'],
        ['icon'=>'fa-graduation-cap',   'stat'=>'Classes VI–XII','label'=>'Full Grade Coverage'],
      ] as $s)
      <div class="bg-[#f0f4ff] rounded-2xl p-5 flex items-center gap-4 border border-brand-navy/10">
        <div class="w-11 h-11 rounded-xl bg-brand-navy/10 flex items-center justify-center text-brand-orange text-xl flex-shrink-0">
          <i class="fa-solid {{ $s['icon'] }}"></i>
        </div>
        <div>
          <div class="text-xl font-extrabold text-brand-navy leading-tight">{{ $s['stat'] }}</div>
          <div class="text-xs text-slate-500 font-semibold">{{ $s['label'] }}</div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- =====================================================================
     2. WHAT IS A COMPOSITE SKILL LAB
     ===================================================================== --}}
<section class="py-16 lg:py-24 bg-white border-t border-slate-100">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

      <div class="rounded-2xl overflow-hidden shadow-xl border border-slate-200 aspect-[4/3]">
        <img src="https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=900&q=85"
             alt="Students at composite skill lab workstation"
             class="w-full h-full object-cover" />
      </div>

      <div class="space-y-5">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-orange/10 text-brand-orange text-xs font-bold uppercase tracking-wider">
          CBSE Mandated
        </div>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-navy leading-tight">
          What Is a Composite Skill Lab?
        </h2>
        <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
          A Composite Skill Lab is a multidisciplinary, hands-on learning space mandated by CBSE under Circular No. Skill-75/2024, aligned with NEP 2020 and NCF-SE 2023. It brings practical skill-based education — covering AI, robotics, coding, electronics, healthcare, agriculture, and vocational trades — into mainstream schooling for Classes VI–XII.
        </p>
        <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
          The lab delivers skill education across three CBSE-defined forms of work:
          <strong class="text-brand-navy">Work with Life Forms</strong>,
          <strong class="text-brand-navy">Work with Machines and Materials</strong>, and
          <strong class="text-brand-navy">Work on Providing Human Services</strong> — preparing students with real-world, industry-relevant competencies.
        </p>
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-800 font-semibold leading-relaxed">
          <i class="fa-solid fa-triangle-exclamation mr-2 text-amber-500"></i>
          <strong>CBSE Deadline:</strong> All existing affiliated schools must set up a Composite Skill Lab by
          <strong class="text-red-600">22 August 2027</strong>. New affiliation applicants must have it before affiliation is granted.
        </div>
      </div>
    </div>
  </div>
</section>

{{-- =====================================================================
     3. THREE FORMS OF WORK
     ===================================================================== --}}
<section class="py-16 lg:py-24 bg-[#f8f9ff]">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="text-center max-w-3xl mx-auto mb-14 space-y-3">
      <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-navy/10 text-brand-navy text-xs font-bold uppercase tracking-wider">
        NCF-SE 2023 Framework
      </div>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-navy">
        Multi-Sector Skill Education Across<br class="hidden sm:block"> Three Forms of Work
      </h2>
      <p class="text-slate-600 text-sm sm:text-base">
        CBSE mandates coverage across all three forms of work. Schools choose sectors within each form based on their infrastructure, student needs, and local relevance.
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

      <div class="skill-card bg-white rounded-2xl border border-green-200 shadow-sm overflow-hidden">
        <div class="bg-green-600 px-6 py-5">
          <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-white text-xl mb-3">
            <i class="fa-solid fa-seedling"></i>
          </div>
          <h3 class="text-lg font-extrabold text-white">Work with Life Forms</h3>
          <p class="text-green-100 text-xs mt-1">Biology, agriculture & sustainability</p>
        </div>
        <div class="p-6 space-y-2.5">
          @foreach(['Agriculture & Gardening','Horticulture & Nursery Management','Food Production','Animal Husbandry','Sustainability Projects'] as $item)
          <div class="flex items-center gap-2 text-sm text-slate-700">
            <i class="fa-solid fa-check text-green-500 text-xs flex-shrink-0"></i> {{ $item }}
          </div>
          @endforeach
        </div>
      </div>

      <div class="skill-card bg-white rounded-2xl border border-blue-200 shadow-sm overflow-hidden">
        <div class="bg-brand-navy px-6 py-5">
          <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-white text-xl mb-3">
            <i class="fa-solid fa-gears"></i>
          </div>
          <h3 class="text-lg font-extrabold text-white">Work with Machines & Materials</h3>
          <p class="text-blue-100 text-xs mt-1">Engineering, technology & fabrication</p>
        </div>
        <div class="p-6 space-y-2.5">
          @foreach(['Coding, Robotics & AI','Electronics & Mechatronics','IT / ITeS','Carpentry & Woodwork','Apparel & Fashion Design','AVGC & Media Content Creation'] as $item)
          <div class="flex items-center gap-2 text-sm text-slate-700">
            <i class="fa-solid fa-check text-brand-navy text-xs flex-shrink-0"></i> {{ $item }}
          </div>
          @endforeach
        </div>
      </div>

      <div class="skill-card bg-white rounded-2xl border border-orange-200 shadow-sm overflow-hidden">
        <div class="bg-brand-orange px-6 py-5">
          <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-white text-xl mb-3">
            <i class="fa-solid fa-hand-holding-heart"></i>
          </div>
          <h3 class="text-lg font-extrabold text-white">Work on Providing Human Services</h3>
          <p class="text-orange-100 text-xs mt-1">Healthcare, finance & community services</p>
        </div>
        <div class="p-6 space-y-2.5">
          @foreach(['Healthcare','Finance & Banking','Tourism & Hospitality','Retail','Beauty & Wellness'] as $item)
          <div class="flex items-center gap-2 text-sm text-slate-700">
            <i class="fa-solid fa-check text-brand-orange text-xs flex-shrink-0"></i> {{ $item }}
          </div>
          @endforeach
        </div>
      </div>

    </div>
  </div>
</section>

{{-- =====================================================================
     4. LAB TYPES (4 cards)
     ===================================================================== --}}
<section class="py-16 lg:py-24 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="text-center max-w-3xl mx-auto mb-14 space-y-3">
      <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-orange/10 text-brand-orange text-xs font-bold uppercase tracking-wider">
        Lab Models
      </div>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-navy">
        Composite Skill Labs Designed for a Holistic Learning Experience
      </h2>
      <p class="text-slate-600 text-sm sm:text-base">
        Our labs foster creativity, innovation, and practical learning — from AI and Robotics to Art & Media, Financial Literacy, and Automobile Engineering.
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

      @php
      $labTypes = [
        [
          'badge'   => 'Future Tech Lab',
          'badgecls'=> 'bg-blue-50 text-blue-700',
          'title'   => 'Robotics, AI, 3D Printing & Electronics',
          'img'     => 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=900&q=80',
          'alt'     => 'Future Tech Lab — AI & Robotics',
          'desc'    => 'Empowers schools with advanced tech resources — smart development boards, 3D printers, sensors, and AI/coding software. Builds critical thinking, problem-solving, and 21st-century skills through experiential, hands-on technology and engineering learning.',
          'tags'    => ['AI & ML','Robotics','3D Printing','Coding','Electronics','IoT'],
        ],
        [
          'badge'   => 'Art, Design & Media Lab',
          'badgecls'=> 'bg-pink-50 text-pink-700',
          'title'   => 'Design, Marketing & Media Literacy',
          'img'     => 'https://images.unsplash.com/photo-1561070791-2526d30994b5?auto=format&fit=crop&w=900&q=80',
          'alt'     => 'Art Design and Media Lab',
          'desc'    => 'Covers skills in marketing, mass media, multimedia design, and graphic work. Includes sketching tools, craft supplies, sculpting materials, fashion design tools, graphic tablets, and software such as Adobe Creative Suite and Canva. Teaches digital art, animation, blogging, and vlogging.',
          'tags'    => ['Graphic Design','Digital Art','Animation','Media','Fashion','Craft'],
        ],
        [
          'badge'   => 'Entrepreneurship & Finance Lab',
          'badgecls'=> 'bg-emerald-50 text-emerald-700',
          'title'   => 'Financial Literacy, Banking & Entrepreneurship',
          'img'     => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=900&q=80',
          'alt'     => 'Entrepreneurship and Finance Lab',
          'desc'    => 'Offers skills in financial markets, financial literacy, and banking. Covers taxation, business administration, cost accounting, and office procedures. Includes financial software, budgeting apps, and business simulation programs. Prepares students for careers in finance and entrepreneurship.',
          'tags'    => ['Finance','Banking','Taxation','Business','Accounting','Entrepreneurship'],
        ],
        [
          'badge'   => 'Woodworking & Automobile Lab',
          'badgecls'=> 'bg-amber-50 text-amber-700',
          'title'   => 'Automobile, Woodworking & Craftsmanship',
          'img'     => 'https://images.unsplash.com/photo-1504328345606-18bbc8c9d7d1?auto=format&fit=crop&w=900&q=80',
          'alt'     => 'Woodworking and Automobile Lab',
          'desc'    => 'Hands-on work with woodworking and automotive mechanics. Includes power tools, machinery, materials, and safety equipment. Teaches 3D design, laser cutting, engraving, craftsmanship, and basic automotive work. Prepares students for engineering and manufacturing career pathways.',
          'tags'    => ['Woodwork','Automotive','Carpentry','Laser Cutting','3D Design','Safety'],
        ],
      ];
      @endphp

      @foreach($labTypes as $lab)
      <div class="group skill-card rounded-2xl border border-slate-200 overflow-hidden bg-white">
        <div class="aspect-[16/7] overflow-hidden bg-slate-100">
          <img src="{{ $lab['img'] }}" alt="{{ $lab['alt'] }}"
               class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
        </div>
        <div class="p-6 space-y-3">
          <span class="inline-block px-3 py-1 rounded-full {{ $lab['badgecls'] }} text-xs font-bold">{{ $lab['badge'] }}</span>
          <h3 class="text-xl font-extrabold text-brand-navy">{{ $lab['title'] }}</h3>
          <p class="text-sm text-slate-600 leading-relaxed">{{ $lab['desc'] }}</p>
          <div class="flex flex-wrap gap-2 pt-1">
            @foreach($lab['tags'] as $tag)
            <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">{{ $tag }}</span>
            @endforeach
          </div>
        </div>
      </div>
      @endforeach

    </div>
  </div>
</section>

{{-- =====================================================================
     5. 21ST CENTURY SKILLS (dark navy band)
     ===================================================================== --}}
<section class="py-16 lg:py-20 bg-brand-navy">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="text-center max-w-3xl mx-auto mb-12 space-y-3">
      <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 text-white text-xs font-bold uppercase tracking-wider">
        Skills for the Future
      </div>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-white">
        Imparting 21st-Century Skills Through the Composite Skill Lab
      </h2>
      <p class="text-blue-100 text-sm sm:text-base">
        Elevate education — bridging technology and experiential learning to empower students and teachers with essential skills for the modern world.
      </p>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
      @foreach([
        ['fa-lightbulb',   'Critical Thinking & Problem Solving'],
        ['fa-pen-ruler',   'Design Thinking & Innovation'],
        ['fa-people-group','Communication & Collaboration'],
        ['fa-terminal',    'Algorithmic Literacy'],
        ['fa-coins',       'Financial Literacy'],
        ['fa-rocket',      'Entrepreneurship'],
      ] as [$icon, $label])
      <div class="bg-white/10 border border-white/10 rounded-2xl p-5 text-center space-y-3 hover:bg-white/15 transition-colors">
        <div class="w-11 h-11 mx-auto rounded-xl bg-brand-orange/20 text-brand-orange flex items-center justify-center text-lg">
          <i class="fa-solid {{ $icon }}"></i>
        </div>
        <p class="text-xs font-bold text-white leading-tight">{{ $label }}</p>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- =====================================================================
     6. COMPLETE PACKAGE — WHAT WE INCLUDE
     ===================================================================== --}}
<section class="py-16 lg:py-24 bg-[#f8f9ff]">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="text-center max-w-3xl mx-auto mb-14 space-y-3">
      <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-orange/10 text-brand-orange text-xs font-bold uppercase tracking-wider">
        Complete Package
      </div>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-navy">
        What All We Include in the CBSE Composite Skill Lab
      </h2>
      <p class="text-slate-600 text-sm sm:text-base">
        A complete, future-focused solution — hardware kits, software, curriculum, teacher training, and an LMS, all in one package designed for day-one deployment.
      </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      @php
      $includes = [
        ['icon'=>'fa-cube',             'color'=>'bg-blue-100 text-blue-600',   'title'=>'Lab Layout & Infrastructure',    'desc'=>'Modular space design for a 400 sq ft (split) or 600 sq ft (combined) lab with workstations, storage, interactive screens, and safe collaborative zones.'],
        ['icon'=>'fa-robot',            'color'=>'bg-orange-100 text-brand-orange','title'=>'Hardware Kits & Equipment',  'desc'=>'Robotics kits, 3D printers, electronics components, sensors, microcontrollers, science instruments, and full sector-specific tool sets for all three forms of work.'],
        ['icon'=>'fa-laptop-code',      'color'=>'bg-purple-100 text-purple-600','title'=>'Technology & Software',        'desc'=>'Computers with pre-installed coding, AI, and design software. AR/VR tools, an integrated LMS platform, and digital collaboration systems — all ready to go.'],
        ['icon'=>'fa-book-open',        'color'=>'bg-green-100 text-green-600',  'title'=>'Curriculum & Lesson Plans',    'desc'=>'CBSE and NEP 2020-aligned interdisciplinary lesson plans, student activity books, project briefs, and assessment rubrics for every grade and every sector.'],
        ['icon'=>'fa-chalkboard-user',  'color'=>'bg-rose-100 text-rose-600',    'title'=>'Teacher Training Programme',   'desc'=>'Comprehensive upskilling in AI, ML, coding, AR-VR, and robotics. Includes project-based assessment frameworks and peer review structures for teachers.'],
        ['icon'=>'fa-file-shield',      'color'=>'bg-amber-100 text-amber-600',  'title'=>'Affiliation Documentation',    'desc'=>'Complete compliance documentation aligned with CBSE Circular Skill-75/2024 for smooth affiliation inspections, renewals, and audit readiness.'],
      ];
      @endphp
      @foreach($includes as $inc)
      <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4 hover:shadow-md transition-shadow">
        <div class="w-12 h-12 rounded-xl {{ $inc['color'] }} flex items-center justify-center text-xl">
          <i class="fa-solid {{ $inc['icon'] }}"></i>
        </div>
        <h3 class="text-base font-extrabold text-brand-navy">{{ $inc['title'] }}</h3>
        <p class="text-sm text-slate-600 leading-relaxed">{{ $inc['desc'] }}</p>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- =====================================================================
     7. LAB SIZE OPTIONS
     ===================================================================== --}}
<section class="py-16 lg:py-24 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="text-center max-w-3xl mx-auto mb-14 space-y-3">
      <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-navy/10 text-brand-navy text-xs font-bold uppercase tracking-wider">
        CBSE Lab Size Requirements
      </div>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-navy">Layout Plan for Composite Skill Lab</h2>
      <p class="text-slate-600 text-sm sm:text-base">
        CBSE permits two configurations. Choose the option that suits your school's space and grade structure.
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
      <div class="rounded-2xl border-2 border-brand-navy/20 bg-[#f0f4ff] p-8 space-y-4 text-center hover:border-brand-navy transition-colors">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-brand-navy text-white flex items-center justify-center text-2xl">
          <i class="fa-solid fa-school"></i>
        </div>
        <h3 class="text-2xl font-extrabold text-brand-navy">Option A</h3>
        <div class="text-4xl font-black text-brand-orange">600 sq ft</div>
        <p class="text-sm font-bold text-brand-navy">Single Lab — Classes VI to XII</p>
        <p class="text-sm text-slate-600 leading-relaxed">One combined lab serving all grades from Class 6 through Class 12 in a single, flexible multi-sector space.</p>
        <div class="inline-block px-4 py-1.5 rounded-full bg-brand-navy text-white text-xs font-bold">Ideal for Mid-Size to Large Schools</div>
      </div>

      <div class="rounded-2xl border-2 border-brand-orange/30 bg-[#fff7f0] p-8 space-y-4 text-center hover:border-brand-orange transition-colors">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-brand-orange text-white flex items-center justify-center text-2xl">
          <i class="fa-solid fa-door-open"></i>
        </div>
        <h3 class="text-2xl font-extrabold text-brand-navy">Option B</h3>
        <div class="text-4xl font-black text-brand-navy">2 × 400 sq ft</div>
        <p class="text-sm font-bold text-brand-navy">Two Separate Labs</p>
        <p class="text-sm text-slate-600 leading-relaxed"><strong>Lab 1:</strong> Classes VI–X &nbsp;|&nbsp; <strong>Lab 2:</strong> Classes XI–XII. Each 400 sq ft, tailored to the grade level and skill depth required.</p>
        <div class="inline-block px-4 py-1.5 rounded-full bg-brand-orange text-white text-xs font-bold">Ideal for Larger Schools</div>
      </div>
    </div>

    <p class="text-center text-sm text-slate-500 mt-8">
      <i class="fa-solid fa-circle-info mr-1.5 text-brand-navy"></i>
      Both configurations are fully compliant with CBSE Circular No. Skill-75/2024.
    </p>
  </div>
</section>

{{-- =====================================================================
     8. MOST CHOSEN SKILL SUBJECTS (with progress bars)
     ===================================================================== --}}
<section class="py-16 lg:py-24 bg-[#f8f9ff]">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

      <div class="space-y-6">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-orange/10 text-brand-orange text-xs font-bold uppercase tracking-wider">
          Student Preference
        </div>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-navy leading-tight">
          Most Chosen CBSE Skill Subjects Among Students
        </h2>
        <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
          CBSE Circular No. Skill-75/2024 mandates all affiliated schools to equip a Composite Skill Lab. The Board offers 22 skill subjects for Class 10 and 43 for Class 12. Information Technology and Artificial Intelligence are consistently the top choice, followed by Physical Activity Trainer, Tourism, and Beauty & Wellness.
        </p>
        <div class="space-y-4 pt-2">
          @foreach([
            ['Information Technology',    92, 'bg-brand-navy'],
            ['Artificial Intelligence',   87, 'bg-brand-orange'],
            ['Physical Activity Trainer', 61, 'bg-green-500'],
            ['Tourism',                   48, 'bg-purple-500'],
            ['Beauty & Wellness',         39, 'bg-pink-500'],
          ] as [$name, $pct, $bar])
          <div>
            <div class="flex justify-between text-xs font-bold text-slate-700 mb-1.5">
              <span>{{ $name }}</span><span>{{ $pct }}%</span>
            </div>
            <div class="w-full h-2.5 bg-slate-200 rounded-full overflow-hidden">
              <div class="{{ $bar }} h-full rounded-full" style="width:{{ $pct }}%"></div>
            </div>
          </div>
          @endforeach
        </div>
      </div>

      <div class="rounded-2xl overflow-hidden shadow-xl border border-slate-200 aspect-[4/3]">
        <img src="https://images.unsplash.com/photo-1516339901601-2e1b62dc0c45?auto=format&fit=crop&w=900&q=85"
             alt="Students engaged in skill-based learning"
             class="w-full h-full object-cover" />
      </div>

    </div>
  </div>
</section>

{{-- =====================================================================
     9. FUTURE SKILLS GRID
     ===================================================================== --}}
<section class="py-16 lg:py-24 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="text-center max-w-3xl mx-auto mb-14 space-y-3">
      <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-navy/10 text-brand-navy text-xs font-bold uppercase tracking-wider">
        Empowering Students
      </div>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-navy">
        Empowering Kids with the Right Future Skills
      </h2>
      <p class="text-slate-600 text-sm sm:text-base">
        With a hands-on approach to AI, Coding, Robotics, and STEM, we cultivate innovators by building the 21st-century skills that unleash the true potential of every young learner.
      </p>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
      @foreach([
        ['fa-code',        'Coding — Graphical & Python', 'bg-blue-50',   'text-blue-600'],
        ['fa-brain',       'Artificial Intelligence',     'bg-purple-50', 'text-purple-600'],
        ['fa-microchip',   'Machine Learning',            'bg-indigo-50', 'text-indigo-600'],
        ['fa-robot',       'Robotics',                    'bg-orange-50', 'text-brand-orange'],
        ['fa-vr-cardboard','AI and VR Tech',              'bg-pink-50',   'text-pink-600'],
        ['fa-wifi',        'Internet of Things (IoT)',    'bg-cyan-50',   'text-cyan-600'],
        ['fa-fingerprint', 'Biometric & Robotics',        'bg-green-50',  'text-green-600'],
        ['fa-gears',       'Advanced Robotics',           'bg-amber-50',  'text-amber-600'],
      ] as [$icon, $label, $bg, $ic])
      <div class="{{ $bg }} rounded-2xl p-5 text-center space-y-3 border border-slate-100 hover:shadow-md transition-shadow">
        <div class="w-12 h-12 mx-auto rounded-xl bg-white shadow-sm flex items-center justify-center text-xl {{ $ic }}">
          <i class="fa-solid {{ $icon }}"></i>
        </div>
        <p class="text-xs font-bold text-brand-navy leading-tight">{{ $label }}</p>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- =====================================================================
     10. CBSE MANDATE & COMPLIANCE
     ===================================================================== --}}
<section class="py-16 lg:py-24 bg-[#fff7f0] border-y border-brand-orange/10">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">

      <div class="space-y-6">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-red-50 text-red-600 text-xs font-bold uppercase tracking-wider border border-red-100">
          <i class="fa-solid fa-circle-exclamation"></i> Mandatory for All CBSE Schools
        </div>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-navy leading-tight">
          Mandatory Implementation of CBSE Composite Skill Lab
        </h2>
        <div class="space-y-4 text-sm text-slate-700 leading-relaxed">
          <div class="flex items-start gap-3 bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <i class="fa-solid fa-building-columns text-brand-navy mt-0.5 flex-shrink-0"></i>
            <p><strong class="text-brand-navy">For New Affiliations:</strong> Schools seeking fresh CBSE affiliation must establish a fully equipped Composite Skill Lab as a mandatory prerequisite before affiliation is granted.</p>
          </div>
          <div class="flex items-start gap-3 bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <i class="fa-solid fa-calendar-xmark text-brand-orange mt-0.5 flex-shrink-0"></i>
            <p><strong class="text-brand-navy">For Existing Affiliations:</strong> All currently affiliated schools must set up a Composite Skill Lab by <strong class="text-red-600">22 August 2027</strong> to remain compliant with CBSE requirements.</p>
          </div>
          <div class="flex items-start gap-3 bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <i class="fa-solid fa-indian-rupee-sign text-green-600 mt-0.5 flex-shrink-0"></i>
            <p><strong class="text-brand-navy">Funding:</strong> Government schools can use central/state funds, World Bank project grants, and social impact funding. Private schools can explore CSR initiatives, government schemes, and public-private partnerships.</p>
          </div>
          <div class="flex items-start gap-3 bg-red-50 rounded-xl p-4 border border-red-200">
            <i class="fa-solid fa-triangle-exclamation text-red-500 mt-0.5 flex-shrink-0"></i>
            <p><strong class="text-red-700">Non-compliance may affect CBSE affiliation status.</strong> Contact us today to begin your setup well ahead of the deadline.</p>
          </div>
        </div>
        <div class="flex flex-wrap gap-3 pt-2">
          <a href="#demo" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-brand-navy text-white text-sm font-bold hover:bg-brand-darknavy transition-all shadow">
            <i class="fa-solid fa-file-arrow-down text-xs"></i> Download CBSE Circular
          </a>
          <a href="#demo" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg border-2 border-brand-navy text-brand-navy text-sm font-bold hover:bg-brand-navy/5 transition-all">
            Model Layout Plan
          </a>
        </div>
      </div>

      <div class="space-y-5">
        <div class="rounded-2xl overflow-hidden shadow-xl border border-slate-200 aspect-[4/3]">
          <img src="https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=900&q=85"
               alt="CBSE composite skill lab setup"
               class="w-full h-full object-cover" />
        </div>
        <div class="bg-brand-navy rounded-2xl p-5 text-white text-center space-y-1">
          <p class="text-xs font-bold uppercase tracking-wider text-blue-200">CBSE Circular Reference</p>
          <p class="text-lg font-extrabold">Circular No. Skill-75/2024</p>
          <p class="text-xs text-blue-100">Issued 23 August 2024 &nbsp;·&nbsp; Deadline 22 August 2027</p>
        </div>
      </div>

    </div>
  </div>
</section>

{{-- =====================================================================
     11. FAQ
     ===================================================================== --}}
<section class="py-16 lg:py-24 bg-white">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="text-center mb-14 space-y-3">
      <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-navy/10 text-brand-navy text-xs font-bold uppercase tracking-wider">
        FAQs
      </div>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-navy">
        Frequently Asked Questions on Setting Up a Composite Skill Lab
      </h2>
    </div>

    @php
    $faqs = [
      ['What is a Composite Skill Lab?',
       'A Composite Skill Lab is an interactive, multidisciplinary learning space where students gain hands-on experience across disciplines including STEM, Robotics, IoT, Coding, AI, 3D Printing, Arts & Design, Woodworking, Automobile, Entrepreneurship, and Financial Literacy. It bridges the gap between theory and real-world application.'],
      ['Why is the Composite Skill Lab important?',
       'The lab aligns with NEP 2020 and NCF-SE 2023, which emphasise skill education alongside academic subjects. It provides practical, application-based learning, prepares students for emerging career opportunities, and fosters creativity, critical thinking, and problem-solving skills.'],
      ['Which schools must set up a Composite Skill Lab?',
       'New Schools: Schools applying for CBSE affiliation must have a fully equipped Composite Skill Lab in place before affiliation is granted. Existing Schools: All currently CBSE-affiliated schools must set up the lab by 22 August 2027.'],
      ['What are the CBSE lab size options?',
       'Schools can choose between: (A) One 600 sq ft lab for Classes VI–XII, or (B) Two separate 400 sq ft labs — one for Classes VI–X and one for Classes XI–XII. Both configurations are fully CBSE-compliant.'],
      ['What infrastructure and equipment is required?',
       'Essential infrastructure includes power outlets, stable internet, ventilation, and storage. Equipment requirements depend on selected skill sectors and typically include robotics kits, 3D printers, computers with coding/AI software, electronics components, science instruments, safety equipment, and sector-specific tools.'],
      ['How much does a Composite Skill Lab cost?',
       'Setup starts from ₹3 Lakh. The final investment depends on lab size, number of skill sectors, equipment depth, and inclusion of AI/robotics modules. All our packages include teacher training — it is not an add-on.'],
      ['Can schools reuse existing labs and spaces?',
       'Yes. Existing spaces such as ATL, IT labs, Home Science labs, or makerspaces can contribute to CSL requirements if they fully satisfy the infrastructure, safety, storage, and multi-sector conditions specified by CBSE. However, a single-sector lab does not automatically qualify as a complete Composite Skill Lab.'],
      ['How can schools fund a Composite Skill Lab?',
       'Schools can use existing infrastructure and resources to minimise costs. External funding options include CSR initiatives, government schemes, and public-private partnerships. Government schools can access central and state funds, World Bank project grants, and similar programmes. Contact us for school-specific funding guidance.'],
    ];
    @endphp

    <div class="space-y-3">
      @foreach($faqs as [$q, $a])
      <details class="group bg-white border border-slate-200 rounded-2xl overflow-hidden">
        <summary class="flex items-center justify-between gap-4 px-6 py-4 cursor-pointer select-none list-none">
          <span class="text-sm sm:text-base font-bold text-brand-navy">{{ $q }}</span>
          <i class="fa-solid fa-chevron-down faq-chevron text-slate-400 text-xs flex-shrink-0 transition-transform duration-200"></i>
        </summary>
        <div class="px-6 pb-5 text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-4">
          {{ $a }}
        </div>
      </details>
      @endforeach
    </div>

  </div>
</section>

{{-- =====================================================================
     12. DEMO / CTA FORM
     ===================================================================== --}}
<section class="py-16 lg:py-24 bg-[#fedecf] border-y border-brand-orange/20" id="demo">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

      <div class="space-y-5">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white text-brand-orange text-xs font-bold uppercase tracking-wider shadow-sm">
          Free Consultation
        </div>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-navy">
          Book Your FREE Demo Today
        </h2>
        <p class="text-slate-700 text-sm sm:text-base leading-relaxed">
          Ready to experience the power of a Composite Skill Lab? Our education specialists will assess your school, help you choose the right sectors, and plan a compliant setup within your budget — in as little as one hour.
        </p>
        <div class="space-y-3 pt-1">
          @foreach([
            'CBSE Circular Skill-75/2024 Compliance Review',
            'Lab Size & Layout Recommendation',
            'Sector Selection Guidance',
            'Equipment & Budget Planning',
            'Teacher Training Roadmap',
          ] as $b)
          <div class="flex items-center gap-3 text-sm text-slate-700 font-semibold">
            <i class="fa-solid fa-circle-check text-brand-orange text-base flex-shrink-0"></i>
            {{ $b }}
          </div>
          @endforeach
        </div>
      </div>

      <div class="bg-white/95 rounded-3xl p-6 sm:p-8 shadow-xl border border-white/80">
        <h3 class="text-lg font-extrabold text-brand-navy mb-5">Request your Demo Today</h3>
        <form class="space-y-4" onsubmit="event.preventDefault(); alert('Thank you! Our specialists will reach out within 24 hours to schedule your free consultation.');">
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">School Name *</label>
            <input type="text" required placeholder="e.g. National Public School" class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm focus:border-brand-navy focus:outline-none focus:ring-2 focus:ring-brand-navy/20 transition-all" />
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Contact Person *</label>
              <input type="text" required placeholder="Your full name" class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm focus:border-brand-navy focus:outline-none focus:ring-2 focus:ring-brand-navy/20 transition-all" />
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Phone Number *</label>
              <input type="tel" required placeholder="+91 98765 43210" class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm focus:border-brand-navy focus:outline-none focus:ring-2 focus:ring-brand-navy/20 transition-all" />
            </div>
          </div>
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Email Address *</label>
            <input type="email" required placeholder="principal@school.edu.in" class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm focus:border-brand-navy focus:outline-none focus:ring-2 focus:ring-brand-navy/20 transition-all" />
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">City *</label>
              <input type="text" required placeholder="e.g. Bengaluru, Delhi" class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm focus:border-brand-navy focus:outline-none focus:ring-2 focus:ring-brand-navy/20 transition-all" />
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">School Board *</label>
              <select required class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm focus:border-brand-navy focus:outline-none focus:ring-2 focus:ring-brand-navy/20 transition-all">
                <option value="">Select Board</option>
                <option value="cbse">CBSE</option>
                <option value="icse">ICSE</option>
                <option value="cambridge">Cambridge / IGCSE</option>
                <option value="ib">IB</option>
                <option value="state">State Board</option>
              </select>
            </div>
          </div>
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Message</label>
            <textarea rows="3" placeholder="Tell us about your space, grade levels, or existing infrastructure..." class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-3 text-sm focus:border-brand-navy focus:outline-none focus:ring-2 focus:ring-brand-navy/20 transition-all"></textarea>
          </div>
          <button type="submit" class="w-full py-3.5 rounded-xl bg-brand-navy hover:bg-brand-darknavy text-white text-sm font-bold shadow-md hover:shadow-xl transition-all duration-200">
            Book a Free Lab Consultation
          </button>
        </form>
      </div>

    </div>
  </div>
</section>

{{-- =====================================================================
     13. OTHER LABS
     ===================================================================== --}}
<section class="py-16 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-10">
      <h2 class="text-2xl sm:text-3xl font-extrabold text-brand-navy mb-3">Explore Our Other Labs</h2>
      <p class="text-slate-500 text-sm">Each lab complements and expands your school's innovation ecosystem.</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
      @foreach([
        [route('labs.ai-robotics'), 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=500&q=80', 'AI & Robotics Lab',    'Hands-on robotics, coding & AI experiments'],
        [route('labs.stem'),        'https://images.unsplash.com/photo-1516339901601-2e1b62dc0c45?auto=format&fit=crop&w=500&q=80', 'STEM Lab',             'Electronics, IoT & project-based learning'],
        [route('labs.ecec'),        'https://images.unsplash.com/photo-1581092335397-9583fe92d232?auto=format&fit=crop&w=500&q=80', 'ECEC Lab',             'Early childhood exploration & creativity'],
      ] as [$url, $img, $name, $desc])
      <a href="{{ $url }}" class="group flex flex-col gap-3 p-4 rounded-2xl border border-slate-200 hover:border-brand-orange hover:shadow-lg transition-all bg-white">
        <div class="aspect-[4/3] rounded-xl overflow-hidden bg-slate-100">
          <img src="{{ $img }}" alt="{{ $name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
        </div>
        <div>
          <p class="font-bold text-brand-navy group-hover:text-brand-orange transition-colors text-sm">{{ $name }}</p>
          <p class="text-xs text-slate-500 mt-0.5">{{ $desc }}</p>
        </div>
      </a>
      @endforeach
    </div>
  </div>
</section>

@endsection