@php
    $hideFlashOffer = true;
    view()->share('hideFlashOffer', true);
@endphp
@extends('frontend.layouts.master')

<!-- meta -->
@section('meta_title', __('School Dashboard'))
<!-- end meta -->

@push('styles')
<style>
  /* Reset and beautify dashboard tables & action buttons */
  .dashboard__review-table .table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
  }
  .dashboard__review-table thead th {
    background-color: #f8fafc;
    color: #475569;
    font-weight: 700;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 14px 16px;
    border-bottom: 2px solid #e2e8f0;
  }
  .dashboard__review-table tbody tr td {
    padding: 14px 16px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    font-size: 14px;
    color: #334155;
  }
  .dashboard__review-table tbody tr:hover td {
    background-color: #f8fafc;
  }

  /* Override the legacy 30px round circle distortion and remove hard theme box-shadows */
  .dashboard__content-wrap .btn,
  .dashboard__content-wrap button.btn,
  .dashboard__review-table .btn,
  .dashboard__review-table a.btn,
  .dashboard__review-table button.btn,
  .dashboard-action-btn,
  .dashboard__review-table tbody tr td a,
  .dashboard__review-table tbody tr td > a,
  .dashboard__review-table tbody tr td > a:first-child,
  .dashboard__review-table tbody tr td > a:last-child {
    box-shadow: none !important;
    text-transform: none !important;
    letter-spacing: normal !important;
  }

  .dashboard__review-table tbody tr td a:not(.btn),
  .dashboard__review-table tbody tr td > a:not(.btn) {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    color: inherit;
    background: transparent !important;
    width: auto !important;
    height: auto !important;
    line-height: inherit !important;
    border-radius: 0 !important;
  }

  /* Dedicated Action Buttons */
  .dashboard-action-btn,
  .dashboard__content-wrap .btn,
  .dashboard__review-table .btn,
  .dashboard__review-table a.btn,
  .dashboard__review-table button.btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    padding: 7px 15px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    border-radius: 6px !important;
    line-height: 1.4 !important;
    width: auto !important;
    height: auto !important;
    text-decoration: none !important;
    transition: all 0.2s ease !important;
    white-space: nowrap !important;
    cursor: pointer !important;
    box-shadow: none !important;
  }

  /* Button Color Variants */
  .dashboard__content-wrap .btn-primary,
  .dashboard__review-table .btn-primary,
  .dashboard__review-table a.btn-primary {
    background-color: #1976d2 !important;
    border: 1px solid #1976d2 !important;
    color: #ffffff !important;
  }
  .dashboard__content-wrap .btn-primary:hover,
  .dashboard__review-table .btn-primary:hover,
  .dashboard__review-table a.btn-primary:hover {
    background-color: #115293 !important;
    border-color: #115293 !important;
    color: #ffffff !important;
    box-shadow: 0 3px 8px rgba(25, 118, 210, 0.25) !important;
  }

  .dashboard__content-wrap .btn-outline-primary,
  .dashboard__review-table .btn-outline-primary,
  .dashboard__review-table a.btn-outline-primary,
  .dashboard__review-table button.btn-outline-primary {
    background-color: #eff6ff !important;
    border: 1px solid #bfdbfe !important;
    color: #1d4ed8 !important;
  }
  .dashboard__content-wrap .btn-outline-primary:hover,
  .dashboard__review-table .btn-outline-primary:hover,
  .dashboard__review-table a.btn-outline-primary:hover,
  .dashboard__review-table button.btn-outline-primary:hover {
    background-color: #1d4ed8 !important;
    border-color: #1d4ed8 !important;
    color: #ffffff !important;
    box-shadow: 0 3px 8px rgba(29, 78, 216, 0.25) !important;
  }

  .dashboard__content-wrap .btn-outline-secondary,
  .dashboard__review-table .btn-outline-secondary,
  .dashboard__review-table a.btn-outline-secondary,
  .dashboard__review-table button.btn-outline-secondary {
    background-color: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    color: #475569 !important;
  }
  .dashboard__content-wrap .btn-outline-secondary:hover,
  .dashboard__review-table .btn-outline-secondary:hover,
  .dashboard__review-table a.btn-outline-secondary:hover,
  .dashboard__review-table button.btn-outline-secondary:hover {
    background-color: #f1f5f9 !important;
    border-color: #94a3b8 !important;
    color: #1e293b !important;
  }

  .dashboard__content-wrap .btn-outline-info,
  .dashboard__review-table .btn-outline-info,
  .dashboard__review-table a.btn-outline-info,
  .dashboard__review-table button.btn-outline-info {
    background-color: #f0f9ff !important;
    border: 1px solid #bae6fd !important;
    color: #0284c7 !important;
  }
  .dashboard__content-wrap .btn-outline-info:hover,
  .dashboard__review-table .btn-outline-info:hover,
  .dashboard__review-table a.btn-outline-info:hover,
  .dashboard__review-table button.btn-outline-info:hover {
    background-color: #0284c7 !important;
    border-color: #0284c7 !important;
    color: #ffffff !important;
    box-shadow: 0 3px 8px rgba(2, 132, 199, 0.25) !important;
  }

  .dashboard__content-wrap .btn-outline-warning,
  .dashboard__review-table .btn-outline-warning,
  .dashboard__review-table a.btn-outline-warning,
  .dashboard__review-table button.btn-outline-warning {
    background-color: #fffbeb !important;
    border: 1px solid #fde68a !important;
    color: #d97706 !important;
  }
  .dashboard__content-wrap .btn-outline-warning:hover,
  .dashboard__review-table .btn-outline-warning:hover,
  .dashboard__review-table a.btn-outline-warning:hover,
  .dashboard__review-table button.btn-outline-warning:hover {
    background-color: #d97706 !important;
    border-color: #d97706 !important;
    color: #ffffff !important;
    box-shadow: 0 3px 8px rgba(217, 119, 6, 0.25) !important;
  }

  .dashboard__content-wrap .btn-outline-danger,
  .dashboard__review-table .btn-outline-danger,
  .dashboard__review-table a.btn-outline-danger,
  .dashboard__review-table button.btn-outline-danger {
    background-color: #fef2f2 !important;
    border: 1px solid #fecaca !important;
    color: #dc2626 !important;
  }
  .dashboard__content-wrap .btn-outline-danger:hover,
  .dashboard__review-table .btn-outline-danger:hover,
  .dashboard__review-table a.btn-outline-danger:hover,
  .dashboard__review-table button.btn-outline-danger:hover {
    background-color: #dc2626 !important;
    border-color: #dc2626 !important;
    color: #ffffff !important;
    box-shadow: 0 3px 8px rgba(220, 38, 38, 0.25) !important;
  }

  /* Icon only action buttons (compact square/rounded badges) */
  .dashboard__review-table .btn-icon-only,
  .dashboard__review-table tbody tr td > a.btn-sm:not(:has(span)),
  .dashboard__review-table tbody tr td > form button.btn-sm:not(:has(span)) {
    width: 32px !important;
    height: 32px !important;
    min-width: 32px !important;
    padding: 0 !important;
    border-radius: 6px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 13px !important;
  }

  .actions-cell {
    white-space: nowrap;
  }
  .actions-cell .d-flex {
    gap: 8px;
    align-items: center;
  }
</style>
@endpush

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
                    @include('frontend.school-dashboard.layouts.sidebar')
                </div>
                <div class="col-lg-9">
                    @yield('dashboard-contents')
                </div>
            </div>
        </div>
    </section>
    <!-- dashboard-area-end -->
    @stack('modals')
@endsection
