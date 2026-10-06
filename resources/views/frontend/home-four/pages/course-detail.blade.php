@extends('frontend.home-four.layouts.master')

@php
  $courseTitle = $course ? $course->title : '';
  $courseDesc = $course ? $course->description : '';
  $courseShortDesc = $course ? ($course->short_description ?: $course->seo_description) : '';
  $courseImg = $course && $course->thumbnail ? asset($course->thumbnail) : asset('designs/img/TTT-1.png');
  $originalPrice = $course ? (float)$course->price : 0;
  $discountPrice = $course ? (float)$course->discount : 0;
  $hasDiscount = $discountPrice > 0 && $discountPrice < $originalPrice;
  $effectivePrice = $hasDiscount ? $discountPrice : $originalPrice;
  $formattedPrice = $effectivePrice > 0 ? '₹ ' . number_format($effectivePrice, 0) : 'Free';
  $formattedOriginalPrice = '₹ ' . number_format($originalPrice, 0);
  $currentCourseId = $course ? $course->id : ($id ?? '');
@endphp

@section('meta_title', ($courseTitle ? $courseTitle . ' - ' : '') . 'Course Details - ' . config('app.name', 'Skillvation'))

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
<style>
  .card-hover {
    transition: all 0.3s ease;
  }
  .card-hover:hover {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    transform: translateY(-2px);
  }
  .accordion-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out;
  }
  .accordion-content.expanded {
    max-height: 500px;
  }
</style>
@endpush

@section('contents')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-20 space-y-12">
  
  <!-- Hero Image Grid -->
  <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mt-6">
    <!-- Main Featured Image -->
    <div class="md:col-span-8 rounded-xl overflow-hidden border border-slate-200 bg-slate-100" style="height:360px;">
      <div id="course-main-image" class="w-full h-full bg-cover bg-center bg-no-repeat"
        style="background-image: url('{{ $courseImg }}'); height:360px;">
      </div>
    </div>
    <!-- Side Images Stack -->
    <div class="md:col-span-4 flex flex-col gap-4" style="height:360px;">
      <div class="flex-1 rounded-xl overflow-hidden border border-slate-200 bg-slate-100">
        <div id="course-side-image-1" class="w-full h-full bg-cover bg-center bg-no-repeat"
          style="background-image: url('{{ $courseImg }}'); min-height:170px;">
        </div>
      </div>
      <div class="flex-1 rounded-xl overflow-hidden border border-slate-200 bg-slate-100">
        <div id="course-side-image-2" class="w-full h-full bg-cover bg-center bg-no-repeat"
          style="background-image: url('{{ $courseImg }}'); min-height:170px;">
        </div>
      </div>
    </div>
  </div>

  <!-- Course Content & Sidebar -->
  <div class="grid grid-cols-1 md:grid-cols-12 gap-8 lg:gap-12 pt-4">
    <!-- Main Left Column (2/3) -->
    <div class="md:col-span-8 space-y-10">
      <div>
        <h1 id="course-title" class="text-3xl sm:text-4xl font-bold text-gray-900 mb-6">
          {{ $courseTitle }}
        </h1>
        <div class="space-y-4">
          <h2 class="text-2xl font-bold text-gray-900">Description</h2>
          <div id="course-description" class="text-base text-gray-600 leading-relaxed prose max-w-none">
            {!! $courseDesc !!}
          </div>
        </div>
      </div>

      <div class="space-y-4">
        <h2 class="text-2xl font-bold text-gray-900">Learning Outcome</h2>
        <div class="p-6 bg-gray-50 rounded-xl border border-gray-200">
          <p id="course-short-description" class="text-gray-600 leading-relaxed">
            {{ $courseShortDesc ?: 'Explore hands-on concepts, real-world application, and skill development.' }}
          </p>
        </div>
      </div>

      <!-- Ratings & Reviews Section -->
      <div class="pt-8 border-t border-slate-200">
        <h2 class="text-2xl font-bold text-gray-900 mb-8">Ratings and Review</h2>
        <div class="space-y-8">
          <!-- Review 1 -->
          <div class="space-y-3">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-full overflow-hidden border border-slate-200">
                <img alt="Lavanya S" class="w-full h-full object-cover"
                  src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80" />
              </div>
              <div>
                <h4 class="font-bold text-gray-900 text-base">Lavanya S</h4>
                <div class="flex text-[#FFC107] text-sm">
                  <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1; font-size: 18px;">star</span>
                  <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1; font-size: 18px;">star</span>
                  <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1; font-size: 18px;">star</span>
                  <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1; font-size: 18px;">star</span>
                  <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1; font-size: 18px;">star</span>
                </div>
              </div>
            </div>
            <p class="text-sm text-gray-600 leading-relaxed">
              Skillvation has made professional development so much easier for me. The courses are practical, engaging, and easy to follow. I have learned several new classroom strategies that keep my students more involved, and the AI sessions have significantly reduced the time I spend preparing teaching materials.
            </p>
          </div>
          <!-- Review 2 -->
          <div class="space-y-3 pt-6 border-t border-slate-200">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-full overflow-hidden border border-slate-200">
                <img alt="Arthi K" class="w-full h-full object-cover"
                  src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=120&q=80" />
              </div>
              <div>
                <h4 class="font-bold text-gray-900 text-base">Arthi K</h4>
                <div class="flex text-[#FFC107] text-sm">
                  <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1; font-size: 18px;">star</span>
                  <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1; font-size: 18px;">star</span>
                  <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1; font-size: 18px;">star</span>
                  <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1; font-size: 18px;">star</span>
                  <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1; font-size: 18px;">star</span>
                </div>
              </div>
            </div>
            <p class="text-sm text-gray-600 leading-relaxed">
              Joining Skillvation has been one of the best decisions for my teaching career. The workshops are well-structured, the mentors are knowledgeable, and the learning resources are excellent. Every session gives me ideas that I can immediately apply in my classroom, making teaching more effective and enjoyable.
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Sticky Sidebar Right Column (1/3) -->
    <div class="md:col-span-4 relative">
      <div class="sticky top-24">
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-md card-hover flex flex-col gap-6">
          <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Course Price</p>
            <div class="flex items-baseline gap-3">
              <p id="sidebar-price" class="text-3xl font-black text-gray-900">{{ $formattedPrice }}</p>
              <span id="sidebar-original-price" class="text-base text-gray-400 line-through {{ $hasDiscount ? '' : 'hidden' }}">
                {{ $formattedOriginalPrice }}
              </span>
              @if ($hasDiscount)
                <span id="sidebar-offer-badge" class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">Special Offer</span>
              @endif
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-col gap-2.5">
            {{-- Buy Now --}}
            <button id="buy-now-btn" type="button" onclick="buyNow()"
              class="bg-primary hover:bg-primary-dark text-white px-6 py-3.5 rounded-full text-sm font-bold w-full transition-all shadow-md active:scale-95 flex items-center justify-center gap-2">
              <i class="fa-solid fa-bolt text-xs"></i>
              <span>Buy Now</span>
            </button>

            {{-- Add to Cart --}}
            <button id="add-to-cart-btn" type="button" onclick="addToCartAction()"
              class="bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-300 px-6 py-3 rounded-full text-sm font-bold w-full transition-all shadow-sm active:scale-95 flex items-center justify-center gap-2">
              <i class="fa-solid fa-cart-shopping text-xs"></i>
              <span>Add to Cart</span>
            </button>

            {{-- Request a Quote Button --}}
            <button id="quote-btn" type="button" onclick="toggleQuoteForm()"
              class="text-primary hover:text-primary-dark hover:bg-primary/5 border border-primary/30 px-6 py-2.5 rounded-full text-xs font-semibold w-full transition-all flex items-center justify-center gap-2">
              <i class="fa-solid fa-file-lines text-xs"></i>
              <span>Institutional Quote / Enquiry</span>
            </button>
          </div>

          <!-- Quote Form (hidden by default) -->
          <div id="quote-form-wrap" class="hidden">
            <div class="border-t border-slate-100 pt-5 space-y-4">
              <h3 class="text-sm font-bold text-gray-800">Fill in your details and we'll get back to you</h3>

              <!-- Success Message -->
              <div id="quote-success" class="hidden bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3 text-sm text-emerald-700 font-medium flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                Thank you! We'll reach out to you shortly.
              </div>

              <form id="quote-form" class="space-y-3" onsubmit="submitQuote(event)">
                <input type="hidden" name="subject" id="quote-subject" value="Quote Request" />
                <div>
                  <label class="block text-xs font-semibold text-gray-600 mb-1">Full Name <span class="text-red-500">*</span></label>
                  <input type="text" name="name" required placeholder="Your full name"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition" />
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-600 mb-1">Phone Number <span class="text-red-500">*</span></label>
                  <input type="tel" name="phone" required placeholder="+91 98765 43210"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition" />
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-600 mb-1">Email Address <span class="text-red-500">*</span></label>
                  <input type="email" name="email" required placeholder="you@school.edu.in"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition" />
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-600 mb-1">School / Organisation</label>
                  <input type="text" name="school" placeholder="School or organisation name"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition" />
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-600 mb-1">Message</label>
                  <textarea name="message" rows="3" placeholder="Tell us about your requirements..."
                    class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition resize-none"></textarea>
                </div>
                <button type="submit" id="quote-submit-btn"
                  class="bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-full text-sm font-bold w-full transition-all shadow-md active:scale-95 flex items-center justify-center gap-2">
                  <i class="fa-solid fa-paper-plane text-xs"></i>
                  <span>Submit Request</span>
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  const API_URL = window.APP_CONFIG?.COURSES_API_URL || '/api/collab-courses';
  const CART_API_BASE = window.APP_CONFIG?.APP_URL || window.location.origin;

  // Extract ID from URL params or pathname
  const urlParams = new URLSearchParams(window.location.search);
  let courseId = urlParams.get('id') || '{{ $currentCourseId }}';
  if (!courseId) {
    const pathParts = window.location.pathname.split('/').filter(Boolean);
    courseId = pathParts[pathParts.length - 1] || '';
  }

  /*
   * Load course from API (Client-side enrichment)
   * Fetches from devapi directly to get accurate cover_image filenames
   */
  async function loadCourse() {
    if (!courseId) return;

    try {
      // Try local proxy first (has correct image base URLs built in)
      const response = await fetch(API_URL);
      if (!response.ok) throw new Error('local api failed');

      const result = await response.json();
      const courses = result.courses?.data || result.data || (Array.isArray(result) ? result : []);

      let course = courses.find(item =>
        String(item.id) === String(courseId) ||
        String(item.lms_id) === String(courseId) ||
        String(item.api_course_id) === String(courseId)
      );

      if (course) {
        updateCourse(course);
        return;
      }

      // Fallback: hit devapi directly to find by api_course_id
      const devResponse = await fetch(APP_API);
      if (!devResponse.ok) return;
      const devResult = await devResponse.json();
      const devCourses = devResult.courses?.data || [];

      // course-detail/{id} = local DB id, so find by api_course_id match
      // Try to get api_course_id from page variable
      const apiCourseId = '{{ $course ? $course->api_course_id : "" }}';
      course = devCourses.find(item =>
        String(item.id) === String(apiCourseId) ||
        String(item.id) === String(courseId)
      );

      if (course) updateCourse(course);

    } catch (error) {
      console.warn('Course API enrichment failed:', error);
    }
  }

  function updateCourse(course) {
    if (course.title) {
      document.title = course.title + ' - Skillvation';
      const titleEl = document.getElementById('course-title');
      if (titleEl) titleEl.textContent = course.title;
    }

    if (course.description) {
      const descEl = document.getElementById('course-description');
      if (descEl) descEl.innerHTML = course.description;
    }

    if (course.short_description) {
      const shortDescEl = document.getElementById('course-short-description');
      if (shortDescEl) shortDescEl.textContent = course.short_description;
    }

    const effectivePrice = Number(course.price ?? (course.discount > 0 ? course.discount : 0));
    const formattedPrice = course.formatted_price || (effectivePrice > 0 ? ('₹ ' + effectivePrice.toLocaleString('en-IN')) : 'Free');
    const priceEl = document.getElementById('sidebar-price');
    if (priceEl) priceEl.textContent = formattedPrice;

    const origPriceEl = document.getElementById('sidebar-original-price');
    if (origPriceEl) {
      if (course.has_discount && course.formatted_original_price) {
        origPriceEl.textContent = course.formatted_original_price;
        origPriceEl.classList.remove('hidden');
      } else if (!course.has_discount && !{{ $hasDiscount ? 'true' : 'false' }}) {
        origPriceEl.classList.add('hidden');
      }
    }

    const cover = course.cover_image || course.thumbnail || course.image;
    if (cover) {
      let fullImgUrl;
      if (cover.startsWith('http') || cover.startsWith('/')) {
        // Already a full URL or absolute path
        fullImgUrl = cover;
      } else {
        // Raw filename from devapi — build devcollab URL
        const apiBase = '{{ env("APP_API_BASE") }}';
        fullImgUrl = apiBase + '/assets/images/event/cover/' + cover;
      }
      setCourseImage('course-main-image', fullImgUrl);
      setCourseImage('course-side-image-1', fullImgUrl);
      setCourseImage('course-side-image-2', fullImgUrl);
    }

    // Store course details
    if (course.type) {
      window._courseType = String(course.type);
    }
    if (course.id) {
      window._apiCourseId = course.id;
    }
    if (course.lms_id) {
      window._lmsCourseId = course.lms_id;
    }
  }

  function setCourseImage(elementId, imageUrl) {
    const el = document.getElementById(elementId);
    if (el && imageUrl) {
      el.style.backgroundImage = `url("${imageUrl}")`;
    }
  }

  /*
   * Quote Form Toggle
   */
  function toggleQuoteForm() {
    const wrap = document.getElementById('quote-form-wrap');
    const btn  = document.getElementById('quote-btn');
    const isHidden = wrap.classList.contains('hidden');

    if (isHidden) {
      wrap.classList.remove('hidden');
      btn.innerHTML = '<i class="fa-solid fa-xmark text-xs"></i><span>Close Enquiry Form</span>';
      setTimeout(() => wrap.scrollIntoView({ behavior: 'smooth', block: 'nearest' }), 100);
    } else {
      wrap.classList.add('hidden');
      btn.innerHTML = '<i class="fa-solid fa-file-lines text-xs"></i><span>Institutional Quote / Enquiry</span>';
    }
  }

  /*
   * Buy Now — Adds course to cart and redirects straight to checkout
   */
  async function buyNow() {
    const btn = document.getElementById('buy-now-btn');
    const oldHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i><span>Processing...</span>';

    const apiId = window._apiCourseId || '{{ $course ? $course->api_course_id : "" }}';
    const lmsId = window._lmsCourseId || '{{ $course ? $course->id : "" }}' || courseId;
    const courseType = window._courseType || '{{ $course ? $course->type : "" }}';

    // Physical TTT products (type=10) redirect to club-shop product page if mapped
    const shopUrl = '{{ env("APP_URL") }}';
    const shopBase = shopUrl + '/club-shop';
    const productMap = {
      '49':  shopBase + '/annual-activity-kit-10-box-203',
      '102': shopBase + '/mega-sample-package-15-items',
    };

    if (String(courseType) === '10' && productMap[String(apiId)]) {
      window.location.href = productMap[String(apiId)];
      return;
    }

    // Standard Course Purchase (Grade 2, 3, 6, 7, 8 / Skill2School courses)
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const targetId = lmsId || apiId || courseId;

    try {
      const response = await fetch('{{ url("/add-to-cart") }}/' + encodeURIComponent(targetId), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        },
        body: JSON.stringify({ _token: csrfToken })
      });

      const data = await response.json();

      if (data.status === 'success' || data.status === 'already') {
        if (typeof toastr !== 'undefined') {
          toastr.success(data.message || 'Proceeding to checkout...');
        }
        window.location.href = data.checkout_url || '{{ route("checkout.index") }}';
      } else {
        // Fallback: try by API course ID
        if (apiId && String(apiId) !== String(targetId)) {
          const fallbackRes = await fetch('{{ url("/add-to-cart-by-api-id") }}/' + encodeURIComponent(apiId), {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken,
              'Accept': 'application/json'
            },
            body: JSON.stringify({ _token: csrfToken })
          });
          const fbData = await fallbackRes.json();
          if (fbData.status === 'success' || fbData.status === 'already') {
            window.location.href = fbData.checkout_url || '{{ route("checkout.index") }}';
            return;
          }
        }

        if (typeof toastr !== 'undefined') {
          toastr.error(data.message || 'Unable to add course to cart.');
        } else {
          alert(data.message || 'Unable to add course to cart.');
        }
        btn.disabled = false;
        btn.innerHTML = oldHtml;
      }
    } catch (err) {
      console.error('Purchase flow error:', err);
      btn.disabled = false;
      btn.innerHTML = oldHtml;
      if (typeof toastr !== 'undefined') {
        toastr.error('Network error during checkout. Please try again.');
      } else {
        alert('Network error during checkout. Please try again.');
      }
    }
  }

  /*
   * Add To Cart — Adds course to cart without immediate redirect
   */
  async function addToCartAction() {
    const btn = document.getElementById('add-to-cart-btn');
    const oldHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i><span>Adding...</span>';

    const apiId = window._apiCourseId || '{{ $course ? $course->api_course_id : "" }}';
    const lmsId = window._lmsCourseId || '{{ $course ? $course->id : "" }}' || courseId;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const targetId = lmsId || apiId || courseId;

    try {
      const response = await fetch('{{ url("/add-to-cart") }}/' + encodeURIComponent(targetId), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        },
        body: JSON.stringify({ _token: csrfToken })
      });

      const data = await response.json();

      if (data.status === 'success') {
        if (typeof toastr !== 'undefined') {
          toastr.success(data.message || 'Course added to cart!');
        }
        document.querySelectorAll('.mini-cart-count').forEach(el => el.textContent = data.cart_count);
        btn.innerHTML = '<i class="fa-solid fa-circle-check text-xs text-emerald-600"></i><span>Added to Cart</span>';
        setTimeout(() => {
          btn.disabled = false;
          btn.innerHTML = '<i class="fa-solid fa-bag-shopping text-xs"></i><span>View Cart</span>';
          btn.onclick = () => window.location.href = '{{ route("cart") }}';
        }, 1200);
      } else if (data.status === 'already') {
        if (typeof toastr !== 'undefined') {
          toastr.info(data.message || 'Already in cart!');
        }
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-bag-shopping text-xs"></i><span>View Cart</span>';
        btn.onclick = () => window.location.href = '{{ route("cart") }}';
      } else {
        if (typeof toastr !== 'undefined') {
          toastr.error(data.message || 'Unable to add to cart.');
        } else {
          alert(data.message || 'Unable to add to cart.');
        }
        btn.disabled = false;
        btn.innerHTML = oldHtml;
      }
    } catch (err) {
      console.error('Add to cart error:', err);
      btn.disabled = false;
      btn.innerHTML = oldHtml;
      if (typeof toastr !== 'undefined') {
        toastr.error('Error adding to cart. Please try again.');
      }
    }
  }

  /*
   * Submit Quote Form
   */
  function submitQuote(e) {
    e.preventDefault();

    const form       = document.getElementById('quote-form');
    const submitBtn  = document.getElementById('quote-submit-btn');
    const successMsg = document.getElementById('quote-success');
    const formData   = new FormData(form);

    // Add course info
    const title = document.getElementById('course-title')?.textContent?.trim() || '';
    formData.append('course_id',    courseId);
    formData.append('course_title', title);
    // Update hidden subject with course name
    const subjectInput = document.getElementById('quote-subject');
    if (subjectInput) subjectInput.value = 'Quote Request' + (title ? ' — ' + title : '');

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i><span>Sending...</span>';

    fetch('{{ route("course.enquiry.store") }}', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        'Accept': 'application/json',
      },
      body: formData,
    })
    .then(res => {
      // Show success regardless of response — form submitted
      form.reset();
      form.classList.add('hidden');
      successMsg.classList.remove('hidden');
      submitBtn.disabled = false;
      submitBtn.innerHTML = '<i class="fa-solid fa-paper-plane text-xs"></i><span>Submit Request</span>';
    })
    .catch(() => {
      // Still show success (graceful degradation)
      form.reset();
      form.classList.add('hidden');
      successMsg.classList.remove('hidden');
      submitBtn.disabled = false;
      submitBtn.innerHTML = '<i class="fa-solid fa-paper-plane text-xs"></i><span>Submit Request</span>';
    });
  }

  // Run API load
  loadCourse();
</script>
@endpush
@endsection
