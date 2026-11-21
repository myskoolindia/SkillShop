@extends('frontend.layouts.master')
@section('meta_title', 'Cart' . ' || ' . $setting->app_name)

@section('contents')
    <!-- breadcrumb-area -->
    <x-frontend.breadcrumb :title="__('Cart')" :links="[['url' => route('home'), 'text' => __('Home')], ['url' => route('cart'), 'text' => __('Cart')]]" />
    <!-- breadcrumb-area-end -->

    <!-- cart-area -->
    <div class="cart__area section-py-120">
        <div class="container">
            @foreach ($products as $item)
                @if (in_array($item->id, session()->get('instructor_courses') ?? []))
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                            class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img"
                            aria-label="Warning:">
                            <path
                                d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                        </svg>
                        <div>
                            {{ __('You have your own courses in your cart. before proceed please remove those from cart ') }}
                        </div>
                    </div>
                @endif
            @endforeach

            @if (Cart::content()->count() > 0)
                <div class="row">
                    <div class="col-lg-8">
                        <table class="table cart__table">
                            <thead>
                                <tr>
                                    <th class="product__thumb">&nbsp;</th>
                                    <th class="product__name">{{ __('Course') }}</th>
                                    <th class="product__price">{{ __('Price') }}</th>
                                    <th class="product__remove">&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                    <tr>
                                        <td class="product__thumb">
                                            <a href="{{ route('course.show', $product->options['slug']) }}"><img
                                                    src="{{ asset($product->options['image']) }}"
                                                    onerror="this.onerror=null;this.src='{{ asset('frontend/img/courses/course_thumb01.jpg') }}';"
                                                    alt=""></a>
                                        </td>
                                        <td class="product__name">
                                            <a
                                                href="{{ route('course.show', $product->options['slug']) }}">{{ $product->name }}</a>
                                            <br>
                                            @if (in_array($product->id, session()->get('enrollments') ?? []))
                                                <div class="mt-2">
                                                    <span class="badge" style="background:#fef3c7; color:#92400e; border:1px solid #fcd34d; font-size:12px; font-weight:700; padding:5px 10px; border-radius:6px; display:inline-flex; align-items:center; gap:5px; box-shadow:0 1px 2px rgba(0,0,0,0.05);">
                                                        <i class="fa fa-info-circle text-warning"></i> {{ __('Previously Purchased') }} &bull; <span style="font-weight:600; color:#b45309;">{{ __('Repurchasing will extend / renew access') }}</span>
                                                    </span>
                                                </div>
                                            @elseif (in_array($product->id, session()->get('instructor_courses') ?? []))
                                                <span class="badge bg-warning mt-2">{{ __('Own course') }}</span>
                                            @endif
                                        </td>
                                        <td class="product__price">{{ currency($product->price) }}</td>
                                        <td class="product__remove">
                                            <a href="{{ route('remove-cart-item', $product->rowId) }}">×</a>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="6" class="cart__actions">
                                        <form action="{{ route('apply-coupon') }}" class="cart__actions-form coupon-form"
                                            method="POST">
                                            @csrf
                                            <input type="text" name="coupon" placeholder="{{ __('Coupon code') }}" value="{{ old('coupon', $coupon) }}" autocomplete="off" style="text-transform:uppercase;">
                                            <button type="submit" class="btn" id="btn-apply-coupon">{{ __('Apply coupon') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-4">
                        <div class="cart__collaterals-wrap">
                            <h2 class="title">{{ __('Cart totals') }}</h2>
                            <ul class="list-wrap">
                                <li>{{ __('Total Items') }}<span>{{ count(Cart::content()) }}</span></li>
                                <li>
                                    @if (Session::has('coupon_code'))
                                        <p class="coupon-discount m-0">
                                            <span>{{ __('Discount') }}</span>
                                            <br>
                                            <small><strong class="text-primary">{{ $coupon }}</strong> 
                                                @if (Session::get('discount_type') === 'fixed')
                                                    ({{ __('Flat') }})
                                                @elseif ($discountPercent > 0)
                                                    ({{ $discountPercent }}%)
                                                @endif
                                                <a class="ms-2 text-danger font-weight-bold" href="/remove-coupon" title="{{ __('Remove Coupon') }}">×</a>
                                            </small>
                                        </p>
                                        <span class="discount-amount text-success font-weight-bold">- {{ currency($discountAmount) }}</span>
                                    @else
                                        <p class="coupon-discount m-0">
                                            <span>{{ __('Discount') }}</span>
                                        </p>
                                        <span class="discount-amount">{{ currency(0) }}</span>
                                    @endif
                                </li>
                                <li>{{ __('Total') }} <span class="amount">{{ $total }}</span></li>
                            </ul>
                            <a href="{{ route('checkout.index') }}" class="btn">{{ __('Proceed to checkout') }}</a>
                        </div>
                    </div>
                </div>
            @else
                <div class="w-100 text-center">
                    <img class="mb-4" src="{{ asset('uploads/website-images/empty-cart.png') }}" alt="">
                    <h4 class="text-center">{{ __('Cart is empty!') }}</h4>
                    <p class="text-center">
                        {{ __('Please add some courses in your cart.') }}
                    </p>
                </div>
            @endif
        </div>
    </div>
    <!-- cart-area-end -->
@endsection
@push('scripts')
<script>
    $(document).on('submit', '.coupon-form', function(e) {
        e.preventDefault();
        var form = $(this);
        var btn = form.find('button[type="submit"]');
        var input = form.find('input[name="coupon"]');
        var code = input.val().trim();

        if (!code) {
            if (window.toastr) {
                toastr.error("{{ __('Please enter a coupon code') }}");
            } else {
                alert("{{ __('Please enter a coupon code') }}");
            }
            return;
        }

        btn.prop('disabled', true).text("{{ __('Applying...') }}");

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (window.toastr) {
                    toastr.success(response.message || "{{ __('Coupon applied successfully!') }}");
                }
                setTimeout(function() {
                    window.location.reload();
                }, 600);
            },
            error: function(xhr) {
                btn.prop('disabled', false).text("{{ __('Apply coupon') }}");
                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : "{{ __('Invalid coupon code') }}";
                if (window.toastr) {
                    toastr.error(msg);
                } else {
                    alert(msg);
                }
            }
        });
    });
</script>
@endpush

@if (session('removeFromCart') && $setting->google_tagmanager_status == 'active' && $marketing_setting?->remove_from_cart)
    @php
        $removeFromCart = session('removeFromCart');
        session()->forget('removeFromCart');
    @endphp
    @push('scripts')
        <script>
            $(function() {
                dataLayer.push({
                    'event': 'removeFromCart',
                    'cart_details': @json($removeFromCart)
                });
            });
        </script>
    @endpush
@endif
