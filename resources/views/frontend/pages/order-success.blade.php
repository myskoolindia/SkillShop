@extends('frontend.layouts.master')
@section('meta_title', 'Order Completed' . ' || ' . $setting->app_name)

@section('contents')
    <!-- breadcrumb-area -->
    <x-frontend.breadcrumb :title="__('Order Completed')" :links="[
        ['url' => route('home'), 'text' => __('Home')],
        ['url' => route('checkout.index'), 'text' => __('Order Completed')],
    ]" />
    <!-- breadcrumb-area-end -->

    <!-- checkout-area -->
    <div class="checkout__area section-py-120">
        <div class="container">
            <div class="row">
                <div class="text-center">
                    <img src="{{ asset('uploads/website-images/success.png') }}" alt="">
                    <h6 class="mt-2">{{ __('Your order has been placed') }}</h6>
                    @php
                        $userRole = userAuth()?->role;
                        $dashboardUrl = match($userRole) {
                            'school'     => route('school.dashboard'),
                            'instructor' => route('instructor.dashboard'),
                            default      => route('student.dashboard'),
                        };
                        $buttonText = match($userRole) {
                            'school'     => __('Go to School Dashboard & Assign Courses'),
                            'teacher'    => __('Go to Teacher Portal'),
                            default      => __('Start Learning / My Courses'),
                        };
                        $subMessage = match($userRole) {
                            'school'     => __('Your course licenses have been activated. You can now assign them to your enrolled teachers and students.'),
                            'teacher'    => __('Your educator access is confirmed. Curriculum and course resources are now accessible in your account.'),
                            default      => __('Your enrollment is active! You can start watching lessons and learning immediately.'),
                        };
                    @endphp
                    <p class="text-muted">{{ $subMessage }}</p>
                    <a href="{{ $dashboardUrl }}" class="btn btn-primary px-4 py-2 mt-2">
                        <i class="fas fa-chalkboard-teacher me-2"></i> {{ $buttonText }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
@if (session('enrollSuccess') && $setting->google_tagmanager_status == 'active' && $marketing_setting?->order_success)
    @php
        $enrollSuccess = session('enrollSuccess');
        session()->forget('enrollSuccess');
    @endphp
    @push('scripts')
        <script>
            $(function() {
                dataLayer.push({
                    'event': 'enrollSuccess',
                    'order_details': @json($enrollSuccess)
                });
            });
        </script>
    @endpush
@endif
