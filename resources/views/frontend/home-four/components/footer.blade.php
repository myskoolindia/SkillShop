<!-- BEGIN: Footer -->
<footer class="bg-surface-container py-section-gap border-t border-outline/20">
  <div class="max-w-container-max mx-auto px-margin-mobile md:px-gutter grid grid-cols-1 md:grid-cols-4 gap-gutter">

    <!-- Column 1: About -->
    <div>
      <a href="{{ route('home') }}" class="inline-block mb-4">
        <img alt="{{ config('app.name', 'Skillvation') }}" class="h-8 sm:h-9 w-auto object-contain" src="{{ asset('designs/img/logo.png') }}" onerror="this.onerror=null;this.src='{{ asset(Cache::get('setting')?->logo ?? 'frontend/img/logo/logo.svg') }}';" />
      </a>
      <p class="text-sm font-medium mb-4 text-on-surface-variant">About Us</p>
      <p class="text-sm text-on-surface-variant">
        Our mission is to inspire students and educators to think creatively,
        solve problems, and excel in a rapidly changing world.
      </p>
      <!-- Social Icons -->
      <div class="flex gap-4 mt-6">
        <a href="#" aria-label="Facebook" class="text-on-surface-variant hover:text-primary transition-colors">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987H7.898V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
          </svg>
        </a>
        <a href="#" aria-label="Twitter / X" class="text-on-surface-variant hover:text-primary transition-colors">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
          </svg>
        </a>
        <a href="#" aria-label="Instagram" class="text-on-surface-variant hover:text-primary transition-colors">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
          </svg>
        </a>
        <a href="#" aria-label="LinkedIn" class="text-on-surface-variant hover:text-primary transition-colors">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
          </svg>
        </a>
        <a href="#" aria-label="YouTube" class="text-on-surface-variant hover:text-primary transition-colors">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
          </svg>
        </a>
      </div>
    </div>

    <!-- Column 2: Quick Links & Get Involved -->
    <div>
      <h4 class="font-bold text-on-surface mb-4">Quick Links</h4>
      <ul class="space-y-2 text-sm text-on-surface-variant">
        <li><a class="hover:text-primary transition-colors" href="{{ route('skill2school') }}">Skill 2 Skool</a></li>
        <li><a class="hover:text-primary transition-colors" href="{{ route('upskill4teacher') }}">Upskill 4 Teacher</a></li>
        <li><a class="hover:text-primary transition-colors" href="{{ route('ttt') }}">Train the Trainer</a></li>
        <li><a class="hover:text-primary transition-colors" href="{{ route('labs') }}">STEMbot Labs</a></li>
      </ul>
      <h4 class="font-bold text-on-surface mt-6 mb-4">Get Involved</h4>
      <ul class="space-y-2 text-sm text-on-surface-variant">
        <li><a class="hover:text-primary transition-colors" href="{{ route('become-instructor') }}">Partner with us</a></li>
        <li><a class="hover:text-primary transition-colors" href="{{ route('become-instructor') }}">Teach with us</a></li>
        <li><a class="hover:text-primary transition-colors" href="{{ url('/sso/shop') }}?target=affiliate-program">Affiliate Program</a></li>
      </ul>
    </div>

    <!-- Column 3: Community & Others -->
    <div>
      <h4 class="font-bold text-on-surface mb-4">Community</h4>
      <ul class="space-y-2 text-sm text-on-surface-variant">
        <li><a class="hover:text-primary transition-colors" href="{{ route('blogs') }}">Blog &amp; Articles</a></li>
      </ul>
      <h4 class="font-bold text-on-surface mt-6 mb-4">Others</h4>
      <ul class="space-y-2 text-sm text-on-surface-variant">
        <li><a class="hover:text-primary transition-colors" href="{{ route('courses') }}">All Courses</a></li>
        <li><a class="hover:text-primary transition-colors" href="{{ route('contact.index') }}">Contact Us</a></li>
      </ul>
    </div>

    <!-- Column 4: Legal -->
    <div>
      <h4 class="font-bold text-on-surface mb-4">Legal</h4>
      <ul class="space-y-2 text-sm text-on-surface-variant">
        <li><a class="hover:text-primary transition-colors" href="{{ route('custom-page', 'privacy-policy') }}">Privacy Policy</a></li>
        <li><a class="hover:text-primary transition-colors" href="{{ route('custom-page', 'refund-policy') }}">Refund Policy</a></li>
        <li><a class="hover:text-primary transition-colors" href="{{ route('custom-page', 'terms-and-conditions') }}">Terms &amp; Conditions</a></li>
      </ul>
    </div>

  </div>

  <!-- Bottom Bar -->
  <div class="text-center mt-12 pt-8 border-t border-outline/10 text-sm text-on-surface-variant">
    <p>&copy; {{ date('Y') }} {{ config('app.name', 'Skillvation') }}. All rights reserved.</p>
  </div>
</footer>
<!-- END: Footer -->
