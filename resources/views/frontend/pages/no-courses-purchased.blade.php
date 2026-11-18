@extends('frontend.home-four.layouts.master')
@section('meta_title', __('No Courses Purchased') . ' || ' . config('app.name', 'Skillvation'))

@section('contents')
    <!-- breadcrumb-area -->
    <x-frontend.breadcrumb :title="__('Dashboard Access')" :links="[
        ['url' => route('home'), 'text' => __('Home')],
        ['url' => url()->current(), 'text' => __('No Courses Purchased')],
    ]" />
    <!-- breadcrumb-area-end -->

    <section class="py-16 md:py-24 bg-slate-50 min-h-[65vh] flex items-center justify-center">
        <div class="max-w-xl mx-auto px-4 w-full">
            <div class="bg-white rounded-3xl shadow-xl border border-slate-200/90 p-8 md:p-12 text-center relative overflow-hidden">
                <!-- Top Accent Gradient -->
                <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-amber-400 via-orange-500 to-primary"></div>
                
                <!-- Icon Badge -->
                <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center shadow-sm">
                    <i class="fa-solid fa-graduation-cap text-3xl text-amber-500"></i>
                </div>

                <!-- Alert Badge -->
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-100 text-amber-800 mb-4">
                    <i class="fa-solid fa-circle-exclamation text-xs text-amber-600"></i> {{ __('No Courses Purchased') }}
                </span>

                <!-- Title -->
                <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 mb-3 tracking-tight">
                    {{ __('You haven’t purchased any courses yet') }}
                </h2>

                <!-- Description -->
                <p class="text-slate-600 text-sm md:text-base leading-relaxed mb-6 max-w-md mx-auto">
                    {{ __('Your learning dashboard, DigiLab access, and RoboQuest tools unlock automatically once you enroll in a course or purchase a kit.') }}
                </p>

                <!-- Persistent On-Screen Alert Box (Does NOT vanish) -->
                <div class="bg-amber-50 border border-amber-200/90 rounded-2xl p-4 text-left text-xs sm:text-sm text-amber-950 mb-8 flex items-start gap-3 shadow-xs">
                    <div class="w-8 h-8 rounded-lg bg-amber-200/70 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fa-solid fa-bell text-amber-800 text-sm"></i>
                    </div>
                    <div>
                        <strong class="block text-amber-900 font-semibold mb-0.5">{{ __('Notice') }}</strong>
                        <span class="text-amber-800/90 leading-normal">{{ __('Dashboard access is reserved for active learners and enrolled institutions. Please continue to our home page or explore our shop.') }}</span>
                    </div>
                </div>

                <!-- Action Buttons: Continue button redirects to Home Page -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="{{ route('home') }}" id="btn-continue-home" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl bg-primary hover:bg-primary-dark text-white font-bold text-sm sm:text-base transition-all shadow-md hover:shadow-lg focus:ring-4 focus:ring-primary/20">
                        <i class="fa-solid fa-house text-sm"></i>
                        <span>{{ __('Continue') }}</span>
                    </a>

                    <a href="{{ route('shop') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-sm sm:text-base transition-all border border-slate-200">
                        <i class="fa-solid fa-basket-shopping text-sm text-primary"></i>
                        <span>{{ __('Explore Shop & Courses') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
