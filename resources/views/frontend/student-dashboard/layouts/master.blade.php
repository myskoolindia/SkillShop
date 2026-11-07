@php
    $hideFlashOffer = true;
    view()->share('hideFlashOffer', true);
@endphp
@extends('frontend.layouts.master')

<!-- meta -->
@section('meta_title', __('Student Dashboard'))
<!-- end meta -->

@section('contents')
    <!-- breadcrumb-area -->
    <x-frontend.breadcrumb
        :title="__('')"
        :links="[]"
    />
    <!-- breadcrumb-area-end -->

    <!-- dashboard-area -->
    <section class="dashboard__area section-pb-120">
        <div class="container">
            
            <div class="row">
                <div class="col-lg-3">
                    @include('frontend.student-dashboard.layouts.sidebar')
                </div>
                <div class="col-lg-9">
                    @yield('dashboard-contents')
                </div>
            </div>
        </div>
    </section>
    <!-- dashboard-area-end -->
@endsection
