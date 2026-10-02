@extends('frontend.home-four.layouts.master')

@section('meta_title', 'Skillvation Lab — Building Schools That Prepare Students for the Future')
@section('meta_description', 'Skillvation Lab helps schools create structured, practical learning environments covering Robotics, AI, STEM, Electronics, Design Thinking, and more. Complete turnkey lab solution for ICSE and State Board schools.')

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
@endpush
