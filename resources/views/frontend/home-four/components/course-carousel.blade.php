@php
  $carouselTitle = $title ?? 'Explore Our Courses';
  $carouselSubtitle = $subtitle ?? 'Because great learning never stops';
  $tagline = $tagline ?? 'Skill Learning Programs';
  $courseType = $type ?? null;
  $courseUpskill = $upskill ?? null;
  $courseLmsOnly = $lms_only ?? null;
@endphp

<!-- BEGIN: Dynamic Courses Carousel Component -->
<section class="py-16 bg-white relative overflow-hidden" id="courses-section">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- Section Header -->
    <div class="text-center mb-12 space-y-3">
      <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-50 text-primary text-xs font-bold uppercase tracking-wider">
        {{ $tagline }}
      </div>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900">
        {{ $carouselTitle }}
      </h2>
      <p class="text-sm sm:text-base text-slate-600 max-w-2xl mx-auto">
        {{ $carouselSubtitle }}
      </p>
    </div>

    <!-- Carousel Wrapper -->
    <div class="relative">
      
      <!-- Loading State -->
      <div id="courses-loading" class="text-center py-16">
        <div class="inline-flex items-center gap-3 text-primary font-medium text-sm">
          <i class="fa-solid fa-circle-notch fa-spin text-xl"></i>
          <span>Loading courses...</span>
        </div>
      </div>

      <!-- Error State -->
      <div id="courses-error" class="hidden max-w-xl mx-auto bg-red-50 border border-red-200 text-red-700 rounded-2xl p-6 text-center shadow-sm">
        <i class="fa-solid fa-triangle-exclamation text-2xl mb-2 text-red-500"></i>
        <p class="text-sm font-medium" id="courses-error-msg">Unable to load courses at the moment.</p>
      </div>

      <!-- Empty State -->
      <div id="courses-empty" class="hidden text-center py-16 text-slate-500">
        <i class="fa-solid fa-folder-open text-3xl mb-2 text-slate-400"></i>
        <p class="text-sm">No courses currently available for this category.</p>
      </div>

      <!-- Courses Container -->
      <div id="courses-carousel-wrapper" class="hidden relative">
        <div id="courses-container" class="flex gap-6 overflow-x-auto scroll-smooth snap-x snap-mandatory pb-6 scrollbar-hide">
          <!-- Dynamically populated via JS -->
        </div>

        <!-- Navigation Buttons -->
        <div id="courses-navigation" class="flex justify-center items-center mt-6 gap-4">
          <button type="button" id="coursePrev" aria-label="Previous courses" class="w-11 h-11 rounded-full border border-slate-300 bg-white hover:bg-slate-50 hover:border-primary text-slate-600 hover:text-primary flex items-center justify-center shadow-sm transition-all active:scale-95">
            <i class="fa-solid fa-chevron-left text-sm"></i>
          </button>
          <button type="button" id="courseNext" aria-label="Next courses" class="w-11 h-11 rounded-full border border-slate-300 bg-white hover:bg-slate-50 hover:border-primary text-slate-600 hover:text-primary flex items-center justify-center shadow-sm transition-all active:scale-95">
            <i class="fa-solid fa-chevron-right text-sm"></i>
          </button>
        </div>
      </div>

      <!-- See All Link -->
      <!-- <div id="courses-see-all" class="text-center mt-8">
        <a href="{{ route('courses') }}" class="inline-flex items-center gap-2 px-7 py-3 rounded-full border border-primary text-primary font-bold text-sm hover:bg-primary hover:text-white transition-all shadow-sm">
          <span>View All Courses</span>
          <i class="fa-solid fa-arrow-right text-xs"></i>
        </a>
      </div> -->

    </div>

  </div>
</section>

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
  const targetType = "{{ $courseType }}";
  const targetUpskill = "{{ $courseUpskill }}";
  const targetLmsOnly = "{{ $courseLmsOnly }}";
  let apiUrl = window.APP_CONFIG?.COURSES_API_URL || '/api/collab-courses';
  
  const queryParams = [];
  if (targetType) {
    queryParams.push(`type=${encodeURIComponent(targetType)}`);
  }
  if (targetUpskill !== '') {
    queryParams.push(`upskill=${encodeURIComponent(targetUpskill)}`);
  }
  if (targetLmsOnly !== '') {
    queryParams.push(`lms_only=${encodeURIComponent(targetLmsOnly)}`);
  }
  if (queryParams.length > 0) {
    const sep = apiUrl.includes('?') ? '&' : '?';
    apiUrl = `${apiUrl}${sep}${queryParams.join('&')}`;
  }

  const loadingEl = document.getElementById('courses-loading');
  const errorEl = document.getElementById('courses-error');
  const emptyEl = document.getElementById('courses-empty');
  const wrapperEl = document.getElementById('courses-carousel-wrapper');
  const containerEl = document.getElementById('courses-container');
  const prevBtn = document.getElementById('coursePrev');
  const nextBtn = document.getElementById('courseNext');

  function escapeHtml(str) {
    return String(str ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  function renderCards(courses) {
    containerEl.innerHTML = '';
    
    // Filter by type if provided and courses have type property
    const targetTypes = targetType ? targetType.split(',').map(s => s.trim()).filter(Boolean) : [];
    let filteredCourses = targetTypes.length > 0 
      ? courses.filter(c => c.type === undefined || c.type === null || targetTypes.includes(String(c.type)) || targetTypes.includes(String(c.course_type)))
      : courses;

    if (targetLmsOnly === '1') {
      filteredCourses = filteredCourses.filter(c => c.has_lms === true || c.lms_id || c.has_lms === 1);
    }

    if (filteredCourses.length === 0) {
      emptyEl.classList.remove('hidden');
      return;
    }

    filteredCourses.forEach(course => {
      const title = course.title || 'Untitled Course';
      const desc = course.short_description || course.description || '';
      const price = course.formatted_price || ('₹ ' + (course.price || 0));
      const image = course.thumbnail || course.cover_image || course.image || window.APP_CONFIG?.IMAGE_FALLBACK || '';
      const cat = course.category_name || course.category || 'Skill Course';
      const courseId = course.lms_id || course.id || '';
      const courseUrl = courseId
        ? `{{ url('/course-detail') }}/${encodeURIComponent(courseId)}`
        : '#';

      const card = document.createElement('div');
      card.className = "w-full sm:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)] xl:w-[calc(25%-18px)] flex-shrink-0 snap-start";
      card.innerHTML = `
        <div class="h-full border border-slate-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 bg-white flex flex-col justify-between group">
          <div class="h-48 bg-slate-100 overflow-hidden relative">
            <img src="${escapeHtml(image)}" alt="${escapeHtml(title)}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" onerror="this.onerror=null;this.src='${window.APP_CONFIG?.IMAGE_FALLBACK || ''}';" />
            <div class="absolute top-3 left-3">
              <span class="px-2.5 py-1 bg-white/90 backdrop-blur-sm text-primary text-[11px] font-bold rounded-full shadow-sm border border-slate-100">
                ${escapeHtml(cat)}
              </span>
            </div>
          </div>
          <div class="p-5 flex flex-col flex-grow justify-between space-y-3">
            <div>
              <h3 class="text-base font-bold text-slate-900 group-hover:text-primary transition-colors line-clamp-2 leading-snug">
                ${escapeHtml(title)}
              </h3>
              <p class="text-xs text-slate-500 mt-2 line-clamp-2 leading-relaxed">
                ${escapeHtml(desc)}
              </p>
            </div>
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between mt-auto">
              <span class="text-base font-extrabold text-slate-900">${escapeHtml(price)}</span>
              <a href="${courseUrl}" class="inline-flex items-center gap-1.5 text-xs font-bold text-primary group-hover:translate-x-1 transition-transform">
                Explore <i class="fa-solid fa-arrow-right text-[10px]"></i>
              </a>
            </div>
          </div>
        </div>
      `;
      containerEl.appendChild(card);
    });

    wrapperEl.classList.remove('hidden');
  }

  fetch(apiUrl)
    .then(res => {
      if (!res.ok) throw new Error('HTTP ' + res.status);
      return res.json();
    })
    .then(res => {
      loadingEl.classList.add('hidden');
      const courses = res.courses?.data || res.data || (Array.isArray(res) ? res : []);
      if (!courses || courses.length === 0) {
        emptyEl.classList.remove('hidden');
        return;
      }
      renderCards(courses);
    })
    .catch(err => {
      console.error('Course API load error:', err);
      loadingEl.classList.add('hidden');
      errorEl.classList.remove('hidden');
    });

  if (prevBtn && nextBtn) {
    prevBtn.addEventListener('click', () => {
      containerEl.scrollBy({ left: -320, behavior: 'smooth' });
    });
    nextBtn.addEventListener('click', () => {
      containerEl.scrollBy({ left: 320, behavior: 'smooth' });
    });
  }
});
</script>
@endpush
