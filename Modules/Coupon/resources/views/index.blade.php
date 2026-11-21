@extends('admin.master_layout')
@section('title')
    <title>{{ __('Coupon List') }}</title>
@endsection
@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Coupon List') }}</h1>
            </div>

            <div class="section-body">
                <div class="text-right">
                    <a href="javascript:;" data-toggle="modal" data-target="#create_coupon_id" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('Add New Coupon') }}
                    </a>
                </div>

                <div class="row mt-sm-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive table-invoice">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('SN') }}</th>
                                                <th>{{ __('Coupon Code') }}</th>
                                                <th>{{ __('Discount') }}</th>
                                                <th>{{ __('Min Spend') }}</th>
                                                <th>{{ __('Scope') }}</th>
                                                <th>{{ __('Validity') }}</th>
                                                <th>{{ __('Usage') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($coupons as $index => $coupon)
                                                <tr>
                                                    <td>{{ ++$index }}</td>
                                                    <td>
                                                        <strong class="text-primary font-weight-bold">{{ $coupon->coupon_code }}</strong>
                                                    </td>
                                                    <td>
                                                        @if (($coupon->discount_type ?? 'percentage') === 'fixed')
                                                            <span class="badge badge-info">{{ currency($coupon->discount_amount ?: $coupon->offer_percentage) }} {{ __('Flat') }}</span>
                                                        @else
                                                            <span class="badge badge-success">{{ $coupon->offer_percentage }}%</span>
                                                            @if (!empty($coupon->max_discount_amount) && $coupon->max_discount_amount > 0)
                                                                <br><small class="text-muted">{{ __('Cap: ') }}{{ currency($coupon->max_discount_amount) }}</small>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (!empty($coupon->min_price) && $coupon->min_price > 0)
                                                            {{ currency($coupon->min_price) }}
                                                        @else
                                                            <span class="badge badge-light">{{ __('No Minimum') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            $cIds = is_array($coupon->course_ids) ? $coupon->course_ids : (json_decode($coupon->course_ids ?? '', true) ?: []);
                                                        @endphp
                                                        @if (empty($cIds))
                                                            <span class="badge badge-secondary">{{ __('All Courses') }}</span>
                                                        @else
                                                            <span class="badge badge-warning" title="{{ implode(', ', $cIds) }}">
                                                                {{ count($cIds) }} {{ __('Course(s)') }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (empty($coupon->expired_date))
                                                            <span class="badge badge-success">{{ __('Lifetime') }}</span>
                                                        @else
                                                            @php
                                                                $isExpired = strtotime($coupon->expired_date) < strtotime(date('Y-m-d'));
                                                            @endphp
                                                            <span class="{{ $isExpired ? 'text-danger font-weight-bold' : '' }}">
                                                                {{ date('d M Y', strtotime($coupon->expired_date)) }}
                                                            </span>
                                                            @if ($isExpired)
                                                                <br><small class="badge badge-danger">{{ __('Expired') }}</small>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $coupon->times_used ?? 0 }}
                                                        @if (!empty($coupon->usage_limit_total))
                                                            / {{ $coupon->usage_limit_total }}
                                                        @else
                                                            <small class="text-muted">({{ __('Unlimited') }})</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($coupon->status == 'active')
                                                            <span class="badge badge-success">{{ __('Active') }}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{ __('Inactive') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="javascript:;" data-toggle="modal"
                                                            data-target="#edit_coupon_id_{{ $coupon->id }}"
                                                            class="btn btn-primary btn-sm" title="{{ __('Edit') }}">
                                                            <i class="fa fa-edit" aria-hidden="true"></i>
                                                        </a>
                                                        <a href="javascript:;" data-toggle="modal"
                                                            data-target="#deleteModal" class="btn btn-danger btn-sm"
                                                            onclick="deleteData({{ $coupon->id }})" title="{{ __('Delete') }}">
                                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <x-empty-table :name="__('Coupon')" route="" create="no"
                                                    :message="__('No data found!')" colspan="9"></x-empty-table>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- Edit Modals --}}
    @foreach ($coupons as $coupon)
        @php
            $editCourseIds = is_array($coupon->course_ids) ? $coupon->course_ids : (json_decode($coupon->course_ids ?? '', true) ?: []);
            $editType = $coupon->discount_type ?? 'percentage';
        @endphp
        <div class="modal fade" id="edit_coupon_id_{{ $coupon->id }}" tabindex="-1" role="dialog"
            aria-labelledby="modelTitleId_{{ $coupon->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold">{{ __('Edit Coupon') }}: {{ $coupon->coupon_code }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('admin.coupon.update', $coupon->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>{{ __('Coupon Code') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="coupon_code" autocomplete="off" class="form-control text-uppercase"
                                        value="{{ $coupon->coupon_code }}" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>{{ __('Discount Type') }} <span class="text-danger">*</span></label>
                                    <select name="discount_type" class="form-control discount-type-select" data-id="edit_{{ $coupon->id }}">
                                        <option value="percentage" {{ $editType === 'percentage' ? 'selected' : '' }}>{{ __('Percentage Discount (%)') }}</option>
                                        <option value="fixed" {{ $editType === 'fixed' ? 'selected' : '' }}>{{ __('Fixed Cash Discount (₹)') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 form-group discount-percent-box" id="percent_box_edit_{{ $coupon->id }}">
                                    <label>{{ __('Discount Percentage (%)') }}</label>
                                    <input type="number" step="0.01" name="offer_percentage" min="0" max="100" class="form-control"
                                        value="{{ $coupon->offer_percentage }}">
                                </div>
                                <div class="col-md-4 form-group discount-amount-box" id="amount_box_edit_{{ $coupon->id }}">
                                    <label>{{ __('Fixed Discount Amount (₹)') }}</label>
                                    <input type="number" step="0.01" name="discount_amount" min="0" class="form-control"
                                        value="{{ $coupon->discount_amount ?: ($editType === 'fixed' ? $coupon->offer_percentage : '') }}">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>{{ __('Max Discount Cap (₹)') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                                    <input type="number" step="0.01" name="max_discount_amount" min="0" class="form-control"
                                        value="{{ $coupon->max_discount_amount }}" placeholder="{{ __('No limit') }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>{{ __('Minimum Cart Spend (₹)') }} <small class="text-muted">({{ __('0 for no min') }})</small></label>
                                    <input type="number" step="0.01" name="min_price" min="0" class="form-control"
                                        value="{{ $coupon->min_price ?? 0 }}">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>{{ __('Status') }} <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control">
                                        <option {{ $coupon->status == 'active' ? 'selected' : '' }} value="active">{{ __('Active') }}</option>
                                        <option {{ $coupon->status == 'inactive' ? 'selected' : '' }} value="inactive">{{ __('Inactive') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>{{ __('Applicable Courses') }} <small class="text-muted">({{ __('Leave unselected to apply to all courses') }})</small></label>
                                <select name="course_ids[]" class="form-control select2" multiple style="width: 100%;">
                                    @foreach ($courses as $c)
                                        <option value="{{ $c->id }}" {{ in_array((string)$c->id, array_map('strval', $editCourseIds), true) || in_array((string)$c->api_course_id, array_map('strval', $editCourseIds), true) ? 'selected' : '' }}>
                                            {{ $c->title }} (ID: {{ $c->id }}{{ $c->api_course_id ? ', API: ' . $c->api_course_id : '' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>{{ __('Start Date') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                                    <input type="date" name="start_date" class="form-control"
                                        value="{{ $coupon->start_date }}">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>{{ __('Expiry Date') }} <small class="text-muted">({{ __('Leave empty for Lifetime') }})</small></label>
                                    <input type="date" name="expired_date" class="form-control"
                                        value="{{ $coupon->expired_date }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>{{ __('Total Usage Limit') }} <small class="text-muted">({{ __('Empty for Unlimited') }})</small></label>
                                    <input type="number" name="usage_limit_total" min="1" class="form-control"
                                        value="{{ $coupon->usage_limit_total }}" placeholder="{{ __('Unlimited') }}">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>{{ __('Usage Limit Per User') }}</label>
                                    <input type="number" name="usage_limit_per_user" min="1" class="form-control"
                                        value="{{ $coupon->usage_limit_per_user ?? 1 }}">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Create Modal --}}
    <div class="modal fade" id="create_coupon_id" tabindex="-1" role="dialog" aria-labelledby="createModelTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold">{{ __('Create New Coupon') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.coupon.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>{{ __('Coupon Code') }} <span class="text-danger">*</span></label>
                                <input type="text" name="coupon_code" autocomplete="off" class="form-control text-uppercase"
                                    placeholder="e.g. CAMPS50, PRO001, WELCOME10" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('Discount Type') }} <span class="text-danger">*</span></label>
                                <select name="discount_type" class="form-control discount-type-select" data-id="create">
                                    <option value="percentage" selected>{{ __('Percentage Discount (%)') }}</option>
                                    <option value="fixed">{{ __('Fixed Cash Discount (₹)') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 form-group discount-percent-box" id="percent_box_create">
                                <label>{{ __('Discount Percentage (%)') }}</label>
                                <input type="number" step="0.01" name="offer_percentage" min="0" max="100" class="form-control"
                                    placeholder="e.g. 10, 25, 50" value="10">
                            </div>
                            <div class="col-md-4 form-group discount-amount-box" id="amount_box_create">
                                <label>{{ __('Fixed Discount Amount (₹)') }}</label>
                                <input type="number" step="0.01" name="discount_amount" min="0" class="form-control"
                                    placeholder="e.g. 500, 1000">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>{{ __('Max Discount Cap (₹)') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                                <input type="number" step="0.01" name="max_discount_amount" min="0" class="form-control"
                                    placeholder="{{ __('No limit') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>{{ __('Minimum Cart Spend (₹)') }} <small class="text-muted">({{ __('0 for no min') }})</small></label>
                                <input type="number" step="0.01" name="min_price" min="0" class="form-control"
                                    value="0" placeholder="0">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('Status') }} <span class="text-danger">*</span></label>
                                <select name="status" class="form-control">
                                    <option value="active" selected>{{ __('Active') }}</option>
                                    <option value="inactive">{{ __('Inactive') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ __('Applicable Courses') }} <small class="text-muted">({{ __('Leave unselected to apply to all courses') }})</small></label>
                            <select name="course_ids[]" class="form-control select2" multiple style="width: 100%;">
                                @foreach ($courses as $c)
                                    <option value="{{ $c->id }}">
                                        {{ $c->title }} (ID: {{ $c->id }}{{ $c->api_course_id ? ', API: ' . $c->api_course_id : '' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>{{ __('Start Date') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                                <input type="date" name="start_date" class="form-control"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('Expiry Date') }} <small class="text-muted">({{ __('Leave empty for Lifetime') }})</small></label>
                                <input type="date" name="expired_date" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>{{ __('Total Usage Limit') }} <small class="text-muted">({{ __('Empty for Unlimited') }})</small></label>
                                <input type="number" name="usage_limit_total" min="1" class="form-control"
                                    placeholder="{{ __('Unlimited') }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('Usage Limit Per User') }}</label>
                                <input type="number" name="usage_limit_per_user" min="1" class="form-control"
                                    value="1">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Create Coupon') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-admin.delete-modal />
    <script>
        "use strict";

        function deleteData(id) {
            $("#deleteForm").attr("action", '{{ url('admin/coupon/') }}' + "/" + id);
        }

        $(document).ready(function() {
            if ($.fn.select2) {
                $('.select2').select2();
            }
        });
    </script>
@endsection
