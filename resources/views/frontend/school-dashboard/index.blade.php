@extends('frontend.school-dashboard.layouts.master')

@section('dashboard-contents')
    <div class="dashboard__content-wrap dashboard__content-wrap-two mb-60">
        <div class="dashboard__content-title">
            <h4 class="title">{{ __('School Dashboard') }}</h4>
        </div>

        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-6">
                <div class="dashboard__counter-item">
                    <div class="icon">
                        <i class="flaticon-mortarboard"></i>
                    </div>
                    <div class="content">
                        <span class="count odometer" data-count="{{ $totalTeachers }}"></span>
                        <p>{{ __('TEACHERS') }}</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-6">
                <div class="dashboard__counter-item">
                    <div class="icon">
                        <i class="flaticon-mortarboard"></i>
                    </div>
                    <div class="content">
                        <span class="count odometer" data-count="{{ $totalStudents }}"></span>
                        <p>{{ __('STUDENTS') }}</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-6">
                <div class="dashboard__counter-item">
                    <div class="icon">
                        <i class="flaticon-mortarboard"></i>
                    </div>
                    <div class="content">
                        <span class="count odometer" data-count="{{ $totalAssignments }}"></span>
                        <p>{{ __('COURSE ASSIGNMENTS') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard__content-wrap">
        <div class="dashboard__content-title">
            <h4 class="title">{{ __('Recent Orders') }}</h4>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="dashboard__review-table table-responsive">
                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <th>{{ __('No') }}</th>
                                <th>{{ __('Invoice') }}</th>
                                <th>{{ __('Paid') }}</th>
                                <th>{{ __('Gateway') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Payment') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $index => $order)
                                <tr>
                                    <td>{{ ++$index }}</td>
                                    <td>#{{ $order->invoice_id }}</td>
                                    <td>{{ $order->paid_amount }} {{ $order->payable_currency }}</td>
                                    <td>{{ $order->payment_method }}</td>
                                    <td>
                                        @if ($order->status == 'completed')
                                            <div class="badge bg-success">{{ __('Completed') }}</div>
                                        @elseif($order->status == 'processing')
                                            <div class="badge bg-warning">{{ __('Processing') }}</div>
                                        @elseif($order->status == 'declined')
                                            <div class="badge bg-danger">{{ __('Declined') }}</div>
                                        @else
                                            <div class="badge bg-warning">{{ __('Pending') }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($order->payment_status == 'paid')
                                            <div class="badge bg-success">{{ __('Paid') }}</div>
                                        @elseif ($order->payment_status == 'cancelled')
                                            <div class="badge bg-danger">{{ __('Cancelled') }}</div>
                                        @else
                                            <div class="badge bg-danger">{{ __('Pending') }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('school.orders.show', $order->id) }}" class="btn btn-sm btn-icon-only btn-outline-primary" title="{{ __('View Invoice') }}">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">{{ __('No orders found!') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
