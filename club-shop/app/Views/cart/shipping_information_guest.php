<div id="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="shopping-cart shopping-cart-shipping">
                    <div class="row">
                        <div class="col-sm-12 col-lg-8">
                            <div class="left">
                                <h1 class="cart-section-title"><?= trans("checkout"); ?></h1>
                                <div class="tab-checkout tab-checkout-open m-t-0">
                                    <p class="font-600 text-center m-b-30">
                                        <?= trans("checking_out_as_guest"); ?>.&nbsp;<?= trans("have_account"); ?>&nbsp;
                                        <a href="javascript:void(0)" class="link" data-toggle="modal" data-target="#loginModal">
                                            <strong class="link-underlined"><?= trans("login"); ?></strong>
                                        </a>
                                    </p>

                                    <h2 class="title">1.&nbsp;&nbsp;<?= trans("shipping_information"); ?></h2>
                                    <form action="<?= base_url('Cart/shippingPost'); ?>" method="post" id="form_express_checkout" class="validate-form">
                                        <?= csrf_field(); ?>
                                        <div class="row">
                                            <div class="col-12 cart-form-shipping-address">
                                                <p class="text-shipping-address"><?= trans("shipping_address") ?></p>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-12 col-md-6 m-b-sm-15">
                                                            <label class="control-label"><?= trans("first_name"); ?></label>
                                                            <input type="text" name="shipping_first_name" class="form-control form-input" value="<?= !empty($cartShippingData->sFirstName) ? esc($cartShippingData->sFirstName) : ''; ?>" maxlength="250" placeholder="<?= trans("first_name"); ?>" required>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <label class="control-label"><?= trans("last_name"); ?></label>
                                                            <input type="text" name="shipping_last_name" class="form-control form-input" value="<?= !empty($cartShippingData->sLastName) ? esc($cartShippingData->sLastName) : ''; ?>" maxlength="250" placeholder="<?= trans("last_name"); ?>" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-12 col-md-6 m-b-sm-15">
                                                            <label class="control-label"><?= trans("email"); ?></label>
                                                            <input type="email" name="shipping_email" class="form-control form-input" value="<?= !empty($cartShippingData->sEmail) ? esc($cartShippingData->sEmail) : ''; ?>" maxlength="250" placeholder="<?= trans("email"); ?>" required>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <label class="control-label"><?= trans("phone_number"); ?></label>
                                                            <input type="text" name="shipping_phone_number" class="form-control form-input" value="<?= !empty($cartShippingData->sPhoneNumber) ? esc($cartShippingData->sPhoneNumber) : ''; ?>" maxlength="100" placeholder="<?= trans("phone_number"); ?>" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <?php if ($generalSettings->single_country_mode != 1): ?>
                                                            <div class="col-12 col-md-6 m-b-sm-15">
                                                                <label class="control-label"><?= trans("country"); ?></label>
                                                                <select id="select_countries_guest_address" name="shipping_country_id" class="select2 select2-req form-control form-input" data-placeholder="<?= trans("country"); ?>" onchange="getStates(this.value,'guest_address'); $('.cart-seller-shipping-options').empty();" required>
                                                                    <option></option>
                                                                    <?php if (!empty($activeCountries)):
                                                                        foreach ($activeCountries as $item): ?>
                                                                            <option value="<?= $item->id; ?>" <?= !empty($cartShippingData->sCountryId) && $cartShippingData->sCountryId == $item->id ? 'selected' : ''; ?>><?= esc($item->name); ?></option>
                                                                        <?php endforeach;
                                                                    endif; ?>
                                                                </select>
                                                            </div>
                                                            <?php if (!empty($cartShippingData->sCountryId)):
                                                                $states = getStatesByCountry($cartShippingData->sCountryId);
                                                            endif;
                                                        else: ?>
                                                            <input type="hidden" name="shipping_country_id" value="<?= $generalSettings->single_country_id; ?>">
                                                            <?php $states = getStatesByCountry($generalSettings->single_country_id);
                                                        endif; ?>
                                                        <div id="get_states_container_guest_address" class="col-12 <?= $generalSettings->single_country_mode == 1 ? 'col-md-12' : 'col-md-6'; ?>">
                                                            <label class="control-label"><?= trans("state"); ?></label>
                                                            <select id="select_states_guest_address" name="shipping_state_id" class="select2 select2-req form-control" data-placeholder="<?= trans("state"); ?>" onchange="getShippingMethodsByLocation(this.value);" required>
                                                                <option></option>
                                                                <?php if (!empty($states)):
                                                                    foreach ($states as $item): ?>
                                                                        <option value="<?= $item->id; ?>" <?= !empty($cartShippingData->sStateId) && $cartShippingData->sStateId == $item->id ? 'selected' : ''; ?>><?= esc($item->name); ?></option>
                                                                    <?php endforeach;
                                                                endif; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-12 col-md-6 m-b-sm-15">
                                                            <label class="control-label"><?= trans("city"); ?></label>
                                                            <input type="text" name="shipping_city" class="form-control form-input" value="<?= !empty($cartShippingData->sCity) ? esc($cartShippingData->sCity) : ''; ?>" maxlength="250" placeholder="<?= trans("city"); ?>" required>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <label class="control-label"><?= trans("zip_code"); ?></label>
                                                            <input type="text" name="shipping_zip_code" class="form-control form-input" value="<?= !empty($cartShippingData->sZipCode) ? esc($cartShippingData->sZipCode) : ''; ?>" maxlength="90" placeholder="<?= trans("zip_code"); ?>" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label"><?= trans("address"); ?></label>
                                                    <input type="text" name="shipping_address" class="form-control form-input" value="<?= !empty($cartShippingData->sAddress) ? esc($cartShippingData->sAddress) : ''; ?>" maxlength="250" placeholder="<?= trans("address"); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-12 cart-form-billing-address" <?= empty($selectedSameAddressForBilling) ? 'style="display: block;"' : ''; ?>>
                                                <p class="text-shipping-address"><?= trans("billing_address") ?></p>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-12 col-md-6 m-b-sm-15">
                                                            <label class="control-label"><?= trans("first_name"); ?></label>
                                                            <input type="text" name="billing_first_name" class="form-control form-input" value="<?= !empty($cartShippingData->bFirstName) ? esc($cartShippingData->bFirstName) : ''; ?>" maxlength="250" placeholder="<?= trans("first_name"); ?>" required>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <label class="control-label"><?= trans("last_name"); ?></label>
                                                            <input type="text" name="billing_last_name" class="form-control form-input" value="<?= !empty($cartShippingData->bLastName) ? esc($cartShippingData->bLastName) : ''; ?>" maxlength="250" placeholder="<?= trans("last_name"); ?>" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-12 col-md-6 m-b-sm-15">
                                                            <label class="control-label"><?= trans("email"); ?></label>
                                                            <input type="email" name="billing_email" class="form-control form-input" value="<?= !empty($cartShippingData->bEmail) ? esc($cartShippingData->bEmail) : ''; ?>" maxlength="250" placeholder="<?= trans("email"); ?>" required>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <label class="control-label"><?= trans("phone_number"); ?></label>
                                                            <input type="text" name="billing_phone_number" class="form-control form-input" value="<?= !empty($cartShippingData->bPhoneNumber) ? esc($cartShippingData->bPhoneNumber) : ''; ?>" maxlength="100" placeholder="<?= trans("phone_number"); ?>" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <?php if ($generalSettings->single_country_mode != 1): ?>
                                                            <div class="col-12 col-md-6 m-b-sm-15">
                                                                <label class="control-label"><?= trans("country"); ?></label>
                                                                <select id="select_countries_guest_billing" name="billing_country_id" class="select2 form-control <?= empty($selectedSameAddressForBilling) ? 'select2-req' : ''; ?>" data-placeholder="<?= trans("country"); ?>" onchange="getStates(this.value,'guest_billing');" required>
                                                                    <option></option>
                                                                    <?php if (!empty($activeCountries)):
                                                                        foreach ($activeCountries as $item): ?>
                                                                            <option value="<?= $item->id; ?>" <?= !empty($cartShippingData->bCountryId) && $cartShippingData->bCountryId == $item->id ? 'selected' : ''; ?>><?= esc($item->name); ?></option>
                                                                        <?php endforeach;
                                                                    endif; ?>
                                                                </select>
                                                            </div>
                                                            <?php if (!empty($cartShippingData->bCountryId)):
                                                                $states = getStatesByCountry($cartShippingData->bCountryId);
                                                            endif;
                                                        else: ?>
                                                            <input type="hidden" name="billing_country_id" value="<?= $generalSettings->single_country_id; ?>">
                                                            <?php $states = getStatesByCountry($generalSettings->single_country_id);
                                                        endif; ?>
                                                        <div class="col-12 <?= $generalSettings->single_country_mode == 1 ? 'col-md-12' : 'col-md-6'; ?>">
                                                            <label class="control-label"><?= trans("state"); ?></label>
                                                            <div id="get_states_container_guest_billing">
                                                                <select id="select_states_guest_billing" name="billing_state_id" class="select2 form-control <?= empty($selectedSameAddressForBilling) == 1 ? 'select2-req' : ''; ?>" data-placeholder="<?= trans("state"); ?>" required>
                                                                    <option></option>
                                                                    <?php if (!empty($states)):
                                                                        foreach ($states as $item): ?>
                                                                            <option value="<?= $item->id; ?>" <?= !empty($cartShippingData->bStateId) && $cartShippingData->bStateId == $item->id ? 'selected' : ''; ?>><?= esc($item->name); ?></option>
                                                                        <?php endforeach;
                                                                    endif; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-12 col-md-6 m-b-sm-15">
                                                            <label class="control-label"><?= trans("city"); ?></label>
                                                            <input type="text" name="billing_city" class="form-control form-input" value="<?= !empty($cartShippingData->bCity) ? esc($cartShippingData->bCity) : ''; ?>" maxlength="250" placeholder="<?= trans("city"); ?>" required>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <label class="control-label"><?= trans("zip_code"); ?></label>
                                                            <input type="text" name="billing_zip_code" class="form-control form-input" value="<?= !empty($cartShippingData->bZipCode) ? esc($cartShippingData->bZipCode) : ''; ?>" maxlength="90" placeholder="<?= trans("zip_code"); ?>" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label"><?= trans("address"); ?></label>
                                                    <input type="text" name="billing_address" class="form-control form-input" value="<?= !empty($cartShippingData->bAddress) ? esc($cartShippingData->bAddress) : ''; ?>" maxlength="250" placeholder="<?= trans("address"); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" name="use_same_address_for_billing" value="1" id="use_same_address_for_billing" <?= $selectedSameAddressForBilling == 1 ? 'checked' : ''; ?>>
                                                        <label for="use_same_address_for_billing" class="custom-control-label"><?= trans("use_same_address_for_billing"); ?></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div id="cart_shipping_methods_container" class="shipping-methods-container">
                                                    <?= view("cart/_shipping_methods"); ?>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="cart-shipping-loader">
                                                            <div class="spinner">
                                                                <div class="bounce1"></div>
                                                                <div class="bounce2"></div>
                                                                <div class="bounce3"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    
                                    <!-- SECTION 3: Payment Method (Hidden for Razorpay default; shown only if alternatives exist) -->
                                    <?php
                                    $hasOtherPaymentMethods = false;
                                    if (!empty($paymentSettings->bank_transfer_enabled)) {
                                        $hasOtherPaymentMethods = true;
                                    }
                                    if (!empty($gateways)) {
                                        foreach ($gateways as $g) {
                                            if ($g->name_key !== 'razorpay') {
                                                $hasOtherPaymentMethods = true;
                                                break;
                                            }
                                        }
                                    }
                                    ?>

                                    <?php if ($hasOtherPaymentMethods): ?>
                                        <div class="tab-checkout tab-checkout-open" style="background:#fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-top: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                                            <h2 class="title m-b-15" style="font-size: 17px; font-weight: 700; color: #1e293b;">
                                                <span class="badge badge-primary mr-2" style="font-size: 13px; border-radius: 50%; width: 24px; height: 24px; line-height: 18px; display: inline-flex; align-items: center; justify-content: center; background-color: #2563eb;">2</span>
                                                <?= trans("payment_method"); ?>
                                            </h2>

                                            <div class="cart-options-list">
                                                <?php 
                                                $idx = 0;
                                                if (!empty($gateways)):
                                                    foreach ($gateways as $gateway): ?>
                                                        <div class="option-card <?= $gateway->name_key == 'razorpay' || $idx == 0 ? 'is-selected' : ''; ?>" style="border-radius: 8px; margin-bottom: 10px; cursor: pointer;">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" name="payment_option" value="<?= esc($gateway->name_key); ?>" id="option_gateway_<?= $gateway->id; ?>" class="custom-control-input option-radio" <?= $gateway->name_key == 'razorpay' || $idx == 0 ? 'checked' : ''; ?> required>
                                                                <label class="custom-control-label" for="option_gateway_<?= $gateway->id; ?>"></label>
                                                            </div>
                                                            <div class="option-details">
                                                                <div class="method-name" style="font-weight: 700; font-size: 15px; color: #0f172a;"><?= esc($gateway->name); ?></div>
                                                                <div class="method-desc" style="font-size: 12px; color: #64748b; margin-top: 2px;">
                                                                    <?= $gateway->name_key == 'razorpay' ? 'Cards (Visa, Mastercard, RuPay), UPI (Google Pay, PhonePe, Paytm), Netbanking' : ''; ?>
                                                                </div>
                                                            </div>
                                                            <div class="payment-logos">
                                                                <?php $logos = @explode(',', $gateway->logos);
                                                                if (!empty($logos) && countItems($logos) > 0):
                                                                    foreach ($logos as $logo): ?>
                                                                        <img src="<?= base_url('assets/img/payment/' . esc(trim($logo ?? '')) . '.svg'); ?>" alt="<?= esc(trim($logo ?? '')); ?>" height="22" style="max-height: 22px; margin-left: 5px;">
                                                                    <?php endforeach;
                                                                endif; ?>
                                                            </div>
                                                        </div>
                                                        <?php $idx++;
                                                    endforeach;
                                                endif; ?>

                                                <?php if (!empty($paymentSettings->bank_transfer_enabled)): ?>
                                                    <div class="option-card" style="border-radius: 8px; margin-bottom: 10px; cursor: pointer;">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="payment_option" value="bank_transfer" id="option_bank_transfer" class="custom-control-input option-radio" required>
                                                            <label class="custom-control-label" for="option_bank_transfer"></label>
                                                        </div>
                                                        <div class="option-details">
                                                            <div class="method-name" style="font-weight: 700; font-size: 15px; color: #0f172a;"><?= trans("bank_transfer"); ?></div>
                                                            <div class="method-desc" style="font-size: 12px; color: #64748b; margin-top: 2px;"><?= trans("bank_transfer_exp"); ?></div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <!-- Razorpay is Default: payment section hidden -->
                                        <input type="hidden" name="payment_option" value="razorpay">
                                    <?php endif; ?>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <?= view('cart/_order_summary'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-express-confirm-trigger');
        if (!btn) return;
        e.preventDefault();
        runGuestExpressCheckout(btn);
    });

    function showGuestAlert(title, message, icon) {
        if (typeof Swal !== 'undefined' && Swal.fire) {
            Swal.fire({
                title: title,
                text: message,
                icon: icon || 'warning',
                confirmButtonText: 'OK',
                confirmButtonColor: '#2563eb'
            });
        } else {
            alert(title + ': ' + message);
        }
    }

    function runGuestExpressCheckout(btn) {
        var termsCheckbox = document.getElementById('checkbox_terms');
        if (termsCheckbox && !termsCheckbox.checked) {
            showGuestAlert('Terms & Conditions', 'Please accept the Terms and Conditions to proceed.', 'warning');
            return;
        }

        var form = document.getElementById('form_express_checkout');
        if (!form) form = document.querySelector('form');

        var allBtns = document.querySelectorAll('.btn-express-confirm-trigger');
        var origHtml = btn.innerHTML;
        allBtns.forEach(function (b) {
            b.disabled = true;
            b.innerHTML = '<i class="fa fa-spinner fa-spin mr-2"></i> Initializing Payment...';
        });

        function resetAllBtns() {
            allBtns.forEach(function (b) {
                b.disabled = false;
                b.innerHTML = origHtml;
            });
        }

        var formData = form ? new FormData(form) : new FormData();
        var selectedPayment = 'razorpay';
        var paymentRadio = form ? form.querySelector('input[name="payment_option"]:checked') : null;
        if (paymentRadio) {
            selectedPayment = paymentRadio.value;
        } else {
            var hiddenPayment = form ? form.querySelector('input[name="payment_option"]') : null;
            if (hiddenPayment) selectedPayment = hiddenPayment.value;
        }
        if (!formData.has('payment_method')) {
            formData.append('payment_method', selectedPayment);
        }

        fetch('<?= base_url("cart/express-checkout-confirm"); ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function (res) { return res.json(); })
        .then(function (res) {
            if (!res) {
                resetAllBtns();
                showGuestAlert('Error', 'Unexpected response from server. Please try again.', 'error');
                return;
            }

            if (res.status === 'error') {
                resetAllBtns();
                showGuestAlert('Checkout Notice', res.message || 'An error occurred.', 'warning');
                return;
            }

            if (res.status === 'success') {
                allBtns.forEach(function (b) {
                    b.innerHTML = '<i class="fa fa-check mr-2"></i> Order Placed! Redirecting...';
                });
                window.location.href = res.redirectUrl;
                return;
            }

            if (res.status === 'razorpay') {
                allBtns.forEach(function (b) {
                    b.innerHTML = '<i class="fa fa-credit-card mr-2"></i> Processing in Razorpay...';
                });

                if (typeof Razorpay === 'undefined') {
                    resetAllBtns();
                    showGuestAlert('Payment Error', 'Razorpay checkout is loading. Please check your connection and try again.', 'error');
                    return;
                }

                var rzpOptions = {
                    key: res.key,
                    amount: res.amount,
                    currency: res.currency,
                    name: res.name,
                    description: res.description,
                    image: res.image,
                    order_id: res.orderId,
                    prefill: {
                        name: res.customer.name,
                        email: res.customer.email,
                        contact: res.customer.contact
                    },
                    notes: { checkout_token: res.checkoutToken },
                    theme: { color: "#2563eb" },
                    handler: function (razorpayResponse) {
                        allBtns.forEach(function (b) {
                            b.innerHTML = '<i class="fa fa-spinner fa-spin mr-2"></i> Finalizing Order...';
                        });

                        var verifyData = new FormData();
                        verifyData.append('razorpay_payment_id', razorpayResponse.razorpay_payment_id);
                        verifyData.append('razorpay_order_id', razorpayResponse.razorpay_order_id);
                        verifyData.append('razorpay_signature', razorpayResponse.razorpay_signature);
                        verifyData.append('checkout_token', res.checkoutToken);
                        verifyData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

                        fetch(res.paymentPostUrl, {
                            method: 'POST',
                            body: verifyData,
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        })
                        .then(function (vRes) { return vRes.json(); })
                        .then(function (verifyRes) {
                            if (verifyRes && verifyRes.status == 1 && verifyRes.redirectUrl) {
                                allBtns.forEach(function (b) {
                                    b.innerHTML = '<i class="fa fa-check mr-2"></i> Payment Successful! Redirecting...';
                                });
                                window.location.href = verifyRes.redirectUrl;
                            } else if (verifyRes && verifyRes.redirectUrl) {
                                window.location.href = verifyRes.redirectUrl;
                            } else {
                                window.location.href = '<?= generateUrl("order-completed"); ?>/' + res.checkoutToken;
                            }
                        })
                        .catch(function () {
                            resetAllBtns();
                            showGuestAlert('Order Status', 'Payment was processed. Redirecting to your order confirmation...', 'info');
                            setTimeout(function () {
                                window.location.href = '<?= generateUrl("order-completed"); ?>/' + res.checkoutToken;
                            }, 2000);
                        });
                    },
                    modal: {
                        ondismiss: function () { resetAllBtns(); }
                    }
                };

                var rzp = new Razorpay(rzpOptions);
                rzp.on('payment.failed', function (failRes) {
                    resetAllBtns();
                    var msg = failRes.error ? (failRes.error.description || failRes.error.reason) : 'Payment was cancelled or failed. Please try again.';
                    showGuestAlert('Payment Cancelled', msg, 'error');
                });
                rzp.open();
            }
        })
        .catch(function () {
            resetAllBtns();
            showGuestAlert('Communication Error', 'A communication error occurred. Please check your internet connection and try again.', 'error');
        });
    }
</script>
