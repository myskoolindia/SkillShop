@php
  $carouselTitle = $title ?? 'Explore Our Courses';
  $carouselSubtitle = $subtitle ?? 'Because great learning never stops';
  $tagline = $tagline ?? 'Skill Learning Programs';
  $courseType = $type ?? null;
  $courseUpskill = $upskill ?? null;
  $isBundle = !empty($is_bundle) || ($courseType === '8' && (string)$courseUpskill === '0');

  $preloadedCourses = collect();
  if ($isBundle && class_exists('\App\Models\Course')) {
      $gradeOrder = [27 => 1, 2 => 2, 52 => 3, 53 => 4, 54 => 5, 10 => 1, 1 => 2, 5 => 3, 13 => 4, 14 => 5];
      $preloadedCourses = \App\Models\Course::active()
          ->where(function($q) {
              $q->whereIn('api_course_id', [27, 2, 52, 53, 54])
                ->orWhereIn('id', [10, 1, 5, 13, 14]);
          })
          ->get()
          ->sortBy(function($c) use ($gradeOrder) {
              return $gradeOrder[$c->api_course_id] ?? ($gradeOrder[$c->id] ?? 99);
          })->values();
  }
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
      <div id="courses-loading" class="{{ $preloadedCourses->isNotEmpty() ? 'hidden ' : '' }}text-center py-16">
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
      <div id="courses-carousel-wrapper" class="{{ $preloadedCourses->isEmpty() ? 'hidden ' : '' }}relative">
        <div id="courses-container" class="flex gap-6 overflow-x-auto scroll-smooth snap-x snap-mandatory pb-6 scrollbar-hide">
          @if($preloadedCourses->isNotEmpty())
            @foreach($preloadedCourses as $pCourse)
              @php
                $pId = $pCourse->id;
                $pApiId = $pCourse->api_course_id ?? $pCourse->id;
                $pTitle = $pCourse->title ?? 'Untitled Course';
                $pDesc = $pCourse->short_description ?? ($pCourse->description ?? '');
                $pDesc = Str::limit(strip_tags($pDesc), 130);
                $pPrice = $pCourse->price > 0 ? '₹ ' . number_format($pCourse->price, 0) : '₹ 5,000';
                $pThumb = $pCourse->thumbnail ? (str_starts_with($pCourse->thumbnail, 'http') ? $pCourse->thumbnail : asset($pCourse->thumbnail)) : asset('designs/img/TTT-1.png');
                $pDetailUrl = url('/course-detail/' . $pId);
              @endphp
              <div class="w-full sm:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)] xl:w-[calc(25%-18px)] flex-shrink-0 snap-start">
                <div class="h-full border border-slate-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 bg-white flex flex-col justify-between group">
                  <a href="{{ $pDetailUrl }}">  
                    <div class="h-48 bg-slate-100 overflow-hidden relative">
                      <img src="{{ $pThumb }}" alt="{{ $pTitle }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" onerror="this.onerror=null;this.src='{{ asset('designs/img/TTT-1.png') }}';" />
                      <div class="absolute top-3 left-3 flex flex-wrap items-center gap-1.5 z-10">
                        <span class="px-2.5 py-1 bg-cyan-700/95 backdrop-blur-sm text-white text-[10px] font-extrabold uppercase tracking-wider rounded-full shadow-sm">Part of 5-Grade Package</span>
                        <span class="px-2.5 py-1 bg-amber-500/95 backdrop-blur-sm text-white text-[10px] font-extrabold uppercase tracking-wider rounded-full shadow-sm">CAMPS</span>
                      </div>
                    </div>
                    <div class="p-3 flex flex-col flex-grow justify-between space-y-3">
                      <div>
                        <h3 class="text-base font-bold text-slate-900 group-hover:text-primary transition-colors line-clamp-2 leading-snug">
                          {{ $pTitle }}
                        </h3>
                        <p class="text-xs text-slate-500 mt-2 line-clamp-2 leading-relaxed">
                          {{ $pDesc }}
                        </p>
                      </div>
                      <div class="pt-4 border-t border-slate-100 flex items-center justify-between mt-auto gap-2">
                        <div>
                          <span class="text-base font-extrabold text-slate-900">{{ $pPrice }}</span>
                          <div class="text-[11px] text-cyan-700 font-bold leading-tight">Package: ₹25,000 (All 5 Grades)</div>
                        </div>
                        @if(!$isBundle)
                          <div class="flex items-center gap-1.5">
                            <button type="button" onclick="carouselBuyNow(event, '{{ $pId }}', '{{ $pApiId }}', false)" class="px-3.5 py-1.5 bg-primary hover:bg-primary-dark text-white text-xs font-bold rounded-full transition-all shadow-sm active:scale-95 flex items-center gap-1">
                              <i class="fa-solid fa-bolt text-[10px]"></i> Buy Now
                            </button>
                          </div>
                        @endif
                      </div>
                    </div>
                  </a>
                </div>
              </div>
            @endforeach
          @endif
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

        <!-- Single Bottom Button for Complete Bundle (When in Bundle Mode) -->
        @if($isBundle)
          <div class="text-center mt-8 pt-4">
            <button type="button" onclick="carouselBuyNow(event, '', '', true)" id="carousel-bottom-bundle-btn" class="inline-flex items-center gap-2.5 px-8 py-3.5 rounded-full bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-extrabold text-sm sm:text-base shadow-lg shadow-cyan-500/25 transition-all duration-300 active:scale-95">
              <i class="fa-solid fa-bolt"></i>
              <span>Buy Now</span>
              <i class="fa-solid fa-arrow-right text-xs"></i>
            </button>
            <div class="text-xs text-slate-500 mt-2 font-medium">( CAMPS Grade 1 to 5)</div>
          </div>
        @endif

      </div>

    </div>

  </div>
</section>

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
  const targetType = "{{ $courseType }}";
  const targetUpskill = "{{ $courseUpskill }}";
  const isBundleMode = {{ $isBundle ? 'true' : 'false' }};
  const hasPreloaded = {{ $preloadedCourses->isNotEmpty() ? 'true' : 'false' }};
  let apiUrl = window.APP_CONFIG?.COURSES_API_URL || '{{ url("/api/collab-courses") }}';
  
  const queryParams = [];
  if (targetType) {
    queryParams.push(`type=${encodeURIComponent(targetType)}`);
  }
  if (targetUpskill !== '') {
    queryParams.push(`upskill=${encodeURIComponent(targetUpskill)}`);
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
    let filteredCourses = courses;

    if (isBundleMode) {
      // For bundle mode, sort sequentially by Grade 1 -> Grade 5
      const gradeOrder = { '27': 1, '1': 1, '2': 2, '10': 2, '52': 3, '5': 3, '53': 4, '13': 4, '54': 5, '14': 5 };
      filteredCourses = courses.filter(c => {
        const id = String(c.api_course_id || c.id || '');
        const title = String(c.title || '');
        return gradeOrder[id] !== undefined || /Grade\s*[1-5]/i.test(title);
      });
      filteredCourses.sort((a, b) => {
        const getOrder = c => {
          const m = String(c.title || '').match(/Grade\s*([1-5])/i);
          if (m) return parseInt(m[1], 10);
          return gradeOrder[String(c.api_course_id)] ?? (gradeOrder[String(c.id)] ?? (gradeOrder[String(c.lms_id)] ?? 99));
        };
        return getOrder(a) - getOrder(b);
      });
    } else {
      const targetTypes = targetType ? targetType.split(',').map(s => s.trim()).filter(Boolean) : [];
      if (targetTypes.length > 0) {
        filteredCourses = filteredCourses.filter(c => c.type === undefined || c.type === null || targetTypes.includes(String(c.type)) || targetTypes.includes(String(c.course_type)));
      }
    }

    if (filteredCourses.length === 0) {
      if (!hasPreloaded) {
        emptyEl.classList.remove('hidden');
        wrapperEl.classList.add('hidden');
      }
      return;
    }

    // Replace container HTML with rendered cards
    containerEl.innerHTML = '';

    filteredCourses.forEach(course => {
      const title = course.title || 'Untitled Course';
      const desc = course.short_description || course.description || '';
      const price = course.formatted_price || ('₹ ' + (course.price || 0));
      const image = course.thumbnail || course.cover_image || course.image || window.APP_CONFIG?.IMAGE_FALLBACK || '{{ asset("designs/img/TTT-1.png") }}';
      const cat = course.category_name || course.category || 'Skill Course';
      const courseId = course.lms_id || course.id || '';
      const courseUrl = courseId
        ? `{{ url('/course-detail') }}/${encodeURIComponent(courseId)}`
        : '#';

      const card = document.createElement('div');
      card.className = "w-full sm:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)] xl:w-[calc(25%-18px)] flex-shrink-0 snap-start";
      
      const badgeHtml = isBundleMode
        ? `<div class="flex flex-wrap items-center gap-1.5">
             <span class="px-2.5 py-1 bg-cyan-700/95 backdrop-blur-sm text-white text-[10px] font-extrabold uppercase tracking-wider rounded-full shadow-sm">Part of 5-Grade Package</span>
             <span class="px-2.5 py-1 bg-amber-500/95 backdrop-blur-sm text-white text-[10px] font-extrabold uppercase tracking-wider rounded-full shadow-sm">CAMPS</span>
           </div>`
        : `<span class="px-2.5 py-1 bg-white/90 backdrop-blur-sm text-primary text-[11px] font-bold rounded-full shadow-sm border border-slate-100">${escapeHtml(cat)}</span>`;

      const priceHtml = isBundleMode
        ? `<div>
            <span class="text-base font-extrabold text-slate-900">${escapeHtml(price)}</span>
            <div class="text-[11px] text-cyan-700 font-bold leading-tight">Package: ₹25,000 (All 5 Grades)</div>
           </div>`
        : `<span class="text-base font-extrabold text-slate-900">${escapeHtml(price)}</span>`;

      const buyButtonHtml = isBundleMode
        ? ''
        : `<button type="button" onclick="carouselBuyNow(event, '${escapeHtml(courseId)}', '${escapeHtml(course.api_course_id || '')}', false)" class="px-3 py-1.5 bg-primary hover:bg-primary-dark text-white text-xs font-bold rounded-full transition-all shadow-sm active:scale-95 flex items-center gap-1">
             <i class="fa-solid fa-bolt text-[10px]"></i> Buy Now
           </button>`;

      card.innerHTML = `
        <div class="h-full border border-slate-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 bg-white flex flex-col justify-between group">
        <a href="${courseUrl}">  
          <div class="h-48 bg-slate-100 overflow-hidden relative">
            <img src="${escapeHtml(image)}" alt="${escapeHtml(title)}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" onerror="this.onerror=null;this.src='${window.APP_CONFIG?.IMAGE_FALLBACK || '{{ asset("designs/img/TTT-1.png") }}'}';" />
            <div class="absolute top-3 left-3 z-10">
              ${badgeHtml}
            </div>
          </div>
          <div class="p-3 flex flex-col flex-grow justify-between space-y-3">
            <div>
              <h3 class="text-base font-bold text-slate-900 group-hover:text-primary transition-colors line-clamp-2 leading-snug">
                ${escapeHtml(title)}
              </h3>
              <p class="text-xs text-slate-500 mt-2 line-clamp-2 leading-relaxed">
                ${escapeHtml(desc)}
              </p>
            </div>
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between mt-auto gap-2">
              ${priceHtml}
              ${buyButtonHtml ? `<div class="flex items-center gap-1.5">${buyButtonHtml}</div>` : ''}
            </div>
          </div>
          </a>
        </div>
      `;
      containerEl.appendChild(card);
    });

    emptyEl.classList.add('hidden');
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
        if (!hasPreloaded) {
          emptyEl.classList.remove('hidden');
        }
        return;
      }
      renderCards(courses);
    })
    .catch(err => {
      console.warn('Course API load warning:', err);
      loadingEl.classList.add('hidden');
      if (!hasPreloaded) {
        errorEl.classList.remove('hidden');
      }
    });

  if (prevBtn && nextBtn) {
    prevBtn.addEventListener('click', () => {
      containerEl.scrollBy({ left: -320, behavior: 'smooth' });
    });
    nextBtn.addEventListener('click', () => {
      containerEl.scrollBy({ left: 320, behavior: 'smooth' });
    });
  }

  window.carouselBuyNow = async function(e, courseId, apiId, forceBundle) {
    if (e) {
      e.preventDefault();
      e.stopPropagation();
    }
    const btn = e ? e.currentTarget : null;
    const oldHtml = btn ? btn.innerHTML : '';
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-[10px]"></i>';
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const shouldBundle = forceBundle || isBundleMode;

    try {
      if (shouldBundle) {
        const bundleRes = await fetch('{{ route("cart.add-skill2school-bundle") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
          },
          body: JSON.stringify({ _token: csrfToken })
        });
        const bundleData = await bundleRes.json();
        if (bundleData.status === 'success' || bundleData.status === 'already') {
          window.location.href = bundleData.checkout_url || '{{ route("checkout.index") }}';
          return;
        }
      }

      const targetId = courseId || apiId;
      const res = await fetch('{{ url("/add-to-cart") }}/' + encodeURIComponent(targetId), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        },
        body: JSON.stringify({ _token: csrfToken })
      });

      const data = await res.json();
      if (data.status === 'success' || data.status === 'already') {
        window.location.href = data.checkout_url || '{{ route("checkout.index") }}';
      } else {
        if (apiId && String(apiId) !== String(targetId)) {
          const fallback = await fetch('{{ url("/add-to-cart-by-api-id") }}/' + encodeURIComponent(apiId), {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken,
              'Accept': 'application/json'
            },
            body: JSON.stringify({ _token: csrfToken })
          });
          const fbData = await fallback.json();
          if (fbData.status === 'success' || fbData.status === 'already') {
            window.location.href = fbData.checkout_url || '{{ route("checkout.index") }}';
            return;
          }
        }
        if (typeof toastr !== 'undefined') toastr.error(data.message || 'Error adding to cart');
        else alert(data.message || 'Error adding to cart');
        if (btn) { btn.disabled = false; btn.innerHTML = oldHtml; }
      }
    } catch (err) {
      console.error(err);
      if (btn) { btn.disabled = false; btn.innerHTML = oldHtml; }
      window.location.href = '{{ url("/course-detail") }}/' + encodeURIComponent(courseId);
    }
  };
});
</script>
@endpush
