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
                                    
                                    <!-- SECTION 3: Payment Method -->
                                    <div class="tab-checkout tab-checkout-open" style="background:#fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-top: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                                        <h2 class="title m-b-15" style="font-size: 17px; font-weight: 700; color: #1e293b;">
                                            <span class="badge badge-primary mr-2" style="font-size: 13px; border-radius: 50%; width: 24px; height: 24px; line-height: 18px; display: inline-flex; align-items: center; justify-content: center; background-color: #2563eb;">2</span>
                                            <?= trans("payment_method"); ?>
                                        </h2>

                                        <div class="cart-options-list">
                                            <?php 
                                            $gateways = getActivePaymentGateways();
                                            $idx = 0;
                                            if (!empty($gateways)):
                                                foreach ($gateways as $gateway): ?>
                                                    <div class="option-card <?= $idx == 0 ? 'is-selected' : ''; ?>" style="border-radius: 8px; margin-bottom: 10px; cursor: pointer;">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="payment_option" value="<?= esc($gateway->name_key); ?>" id="option_gateway_<?= $gateway->id; ?>" class="custom-control-input option-radio" <?= $idx == 0 ? 'checked' : ''; ?> required>
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
                                                        <input type="radio" name="payment_option" value="bank_transfer" id="option_bank_transfer" class="custom-control-input option-radio" <?= $idx == 0 ? 'checked' : ''; ?> required>
                                                        <label class="custom-control-label" for="option_bank_transfer"></label>
                                                    </div>
                                                    <div class="option-details">
                                                        <div class="method-name" style="font-weight: 700; font-size: 15px; color: #0f172a;"><?= trans("bank_transfer"); ?></div>
                                                        <div class="method-desc" style="font-size: 12px; color: #64748b; margin-top: 2px;"><?= trans("bank_transfer_exp"); ?></div>
                                                    </div>
                                                </div>
                                                <?php $idx++;
                                            endif; ?>
                                        </div>

                                        <div class="form-group m-t-20 m-b-0">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" name="terms" id="checkbox_terms" checked required>
                                                <label for="checkbox_terms" class="custom-control-label" style="font-size: 13px; color: #475569; cursor: pointer;">
                                                    <?= trans("terms_conditions_exp"); ?>&nbsp;
                                                    <?php $pageTerms = getPageByDefaultName('terms_conditions', selectedLangId());
                                                    if (!empty($pageTerms)): ?>
                                                        <a href="<?= generateUrl($pageTerms->page_default_name); ?>" class="link-terms" target="_blank" style="color: #2563eb; font-weight: 600; text-decoration: underline;"><?= esc($pageTerms->title); ?></a>
                                                    <?php endif; ?>
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Mobile Confirm Button -->
                                        <div class="form-group m-t-25 m-b-0 d-block d-lg-none">
                                            <button type="button" class="btn btn-lg btn-block btn-express-confirm-trigger" style="background: linear-gradient(135deg, #2563eb, #1d4ed8); color: #fff; font-weight: 700; font-size: 16px; border-radius: 8px; padding: 14px 20px; border: none; box-shadow: 0 4px 14px rgba(37,99,235,0.3);">
                                                <i class="fa fa-lock mr-2"></i> Confirm and Pay &mdash; <?= priceDecimal(!empty($cart->totals->shipping_cost) ? $cart->totals->total : $cart->totals->total_before_shipping, $cart->currency_code); ?>
                                            </button>
                                        </div>
                                    </div>

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

<?php if (!empty($cartShippingData->sStateId)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            getShippingMethodsByLocation(<?= esc($cartShippingData->sStateId); ?>);
        });
    </script>
<?php endif; ?>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    $(document).on('click', '.btn-express-confirm-trigger', function (e) {
        e.preventDefault();

        var $btn = $(this);
        var $allBtns = $('.btn-express-confirm-trigger');

        if ($('#checkbox_terms').length && !$('#checkbox_terms').is(':checked')) {
            Swal.fire({ text: 'Please accept the Terms and Conditions to proceed.', icon: 'warning', confirmButtonText: 'OK' });
            return false;
        }

        var formData = $('#form_express_checkout').serialize();
        var selectedPayment = $('input[name="payment_option"]:checked').val() || 'razorpay';
        formData += '&payment_method=' + encodeURIComponent(selectedPayment);

        var origHtml = $btn.html();
        $allBtns.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-2"></i> Initializing Payment...');

        function resetButtons() {
            $allBtns.prop('disabled', false).html(origHtml);
        }

        $.ajax({
            type: 'POST',
            url: '<?= base_url("cart/express-checkout-confirm"); ?>',
            data: formData,
            dataType: 'json',
            success: function (res) {
                if (!res) {
                    resetButtons();
                    Swal.fire({ text: 'Unexpected server response. Please try again.', icon: 'error', confirmButtonText: 'OK' });
                    return;
                }

                if (res.status === 'error') {
                    resetButtons();
                    Swal.fire({ text: res.message || 'An error occurred.', icon: 'error', confirmButtonText: 'OK' });
                    return;
                }

                if (res.status === 'success') {
                    $allBtns.html('<i class="fa fa-check mr-2"></i> Order Placed! Redirecting...');
                    window.location.href = res.redirectUrl;
                    return;
                }

                if (res.status === 'razorpay') {
                    $allBtns.html('<i class="fa fa-credit-card mr-2"></i> Processing in Razorpay...');

                    if (typeof Razorpay === 'undefined') {
                        resetButtons();
                        Swal.fire({ text: 'Razorpay SDK could not be loaded.', icon: 'error', confirmButtonText: 'OK' });
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
                            $allBtns.html('<i class="fa fa-spinner fa-spin mr-2"></i> Finalizing Order...');

                            var verifyData = {
                                razorpay_payment_id: razorpayResponse.razorpay_payment_id,
                                razorpay_order_id: razorpayResponse.razorpay_order_id,
                                razorpay_signature: razorpayResponse.razorpay_signature,
                                checkout_token: res.checkoutToken,
                                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                            };

                            $.ajax({
                                type: 'POST',
                                url: res.paymentPostUrl,
                                data: verifyData,
                                dataType: 'json',
                                success: function (verifyRes) {
                                    if (verifyRes && verifyRes.status == 1 && verifyRes.redirectUrl) {
                                        window.location.href = verifyRes.redirectUrl;
                                    } else {
                                        window.location.href = '<?= generateUrl("order-completed"); ?>/' + res.checkoutToken;
                                    }
                                },
                                error: function () {
                                    resetButtons();
                                    Swal.fire({ text: 'Payment verification failed. Please contact support.', icon: 'error', confirmButtonText: 'OK' });
                                }
                            });
                        },
                        modal: {
                            ondismiss: function () { resetButtons(); }
                        }
                    };

                    var rzp = new Razorpay(rzpOptions);
                    rzp.on('payment.failed', function (failRes) {
                        resetButtons();
                        Swal.fire({ text: failRes.error ? (failRes.error.description || failRes.error.reason) : 'Payment failed.', icon: 'error', confirmButtonText: 'OK' });
                    });
                    rzp.open();
                }
            },
            error: function (xhr) {
                resetButtons();
                var errMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'A communication error occurred.';
                Swal.fire({ text: errMsg, icon: 'error', confirmButtonText: 'OK' });
            }
        });
    });
</script>
