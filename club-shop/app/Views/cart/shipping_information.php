<div id="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="shopping-cart shopping-cart-shipping">
                    <div class="row">
                        <div class="col-sm-12 col-lg-8">
                            <div class="left">
                                <div class="d-flex align-items-center justify-content-between m-b-20">
                                    <h1 class="cart-section-title m-0" style="font-size: 22px; font-weight: 700; color: #0f172a;">Express Checkout</h1>
                                    <span class="badge p-2" style="font-size: 12px; font-weight: 600; color: #166534; background: #dcfce7; border: 1px solid #bbf7d0; border-radius: 6px;">
                                        <i class="fa fa-shield text-success mr-1"></i> 100% Safe & Secure
                                    </span>
                                </div>

                                <?= view('partials/_messages'); ?>

                                <form id="form_express_checkout" onsubmit="return false;">
                                    <?= csrf_field(); ?>
                                    <input type="hidden" name="checkout_type" value="product">

                                    <!-- SECTION 1: Delivery Address -->
                                    <div class="tab-checkout tab-checkout-open m-t-0" style="background:#fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                                        <div class="d-flex align-items-center justify-content-between m-b-15">
                                            <h2 class="title m-0" style="font-size: 17px; font-weight: 700; color: #1e293b;">
                                                <span class="badge badge-primary mr-2" style="font-size: 13px; border-radius: 50%; width: 24px; height: 24px; line-height: 18px; display: inline-flex; align-items: center; justify-content: center; background-color: #2563eb;">1</span>
                                                <?= trans("shipping_address"); ?>
                                            </h2>
                                            <a href="javascript:void(0)" class="btn btn-sm btn-outline-primary font-600 display-inline-flex align-items-center link-add-new-shipping-option" data-toggle="modal" data-target="#modalAddAddress" style="border-radius: 6px; padding: 4px 10px; font-size: 12.5px;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-plus-circle mr-1" viewBox="0 0 16 16">
                                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                                </svg>
                                                <?= trans("add_new_address"); ?>
                                            </a>
                                        </div>

                                        <?php $selectedStateId = 0;
                                        if (empty($shippingAddresses)): ?>
                                            <div class="alert alert-light border" style="background: #f8fafc; border-color: #cbd5e1 !important; border-radius: 8px; padding: 15px; text-align: center;">
                                                <p class="text-muted m-b-10" style="font-size: 13.5px;"><?= trans("not_added_shipping_address"); ?></p>
                                                <button type="button" class="btn btn-primary btn-sm font-600" data-toggle="modal" data-target="#modalAddAddress" style="border-radius: 6px; padding: 5px 14px;">
                                                    <i class="fa fa-plus-circle mr-1"></i> <?= trans("add_new_address"); ?>
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <div class="row">
                                                <?php foreach ($shippingAddresses as $address):
                                                    if ($address->address_type == 'shipping'):
                                                        if (empty($selectedShippingAddressId) || $selectedShippingAddressId == $address->id) {
                                                            $selectedStateId = $address->state_id;
                                                        }
                                                        $country = getCountry($address->country_id);
                                                        $state = getState($address->state_id); ?>
                                                        <div class="col-12 m-b-10">
                                                            <div class="shipping-address-box shipping-address-box-cart <?= $selectedShippingAddressId == $address->id ? 'border-primary' : ''; ?>" style="border-radius: 8px; transition: border-color 0.2s ease;">
                                                                <div class="custom-control custom-radio">
                                                                    <input type="radio" class="custom-control-input" id="option_shipping_address_<?= $address->id; ?>" name="shipping_address_id" value="<?= $address->id; ?>"
                                                                        <?= $selectedShippingAddressId == $address->id ? 'checked' : ''; ?> onchange="getShippingMethodsByLocation('<?= $address->state_id; ?>');" required>
                                                                    <label class="custom-control-label" for="option_shipping_address_<?= $address->id; ?>" style="cursor: pointer;">
                                                                        <strong class="m-b-5" style="color: #0f172a; font-size: 14.5px;"><?= esc($address->title); ?></strong>
                                                                        <p style="color: #475569; font-size: 13px; margin-bottom: 2px;">
                                                                            <?= esc($address->first_name . ' ' . $address->last_name); ?> &bull; <?= esc($address->phone_number); ?>
                                                                        </p>
                                                                        <p style="color: #64748b; font-size: 12.5px; margin-bottom: 0;">
                                                                            <?= esc($address->address); ?>, <?= esc($address->city); ?>, <?= !empty($state->name) ? esc($state->name) . ' - ' : ''; ?><?= esc($address->zip_code); ?><?= !empty($country->name) ? ', ' . esc($country->name) : ''; ?>
                                                                        </p>
                                                                    </label>
                                                                </div>
                                                                <div class="profile-actions-shipping profile-actions-shipping-cart">
                                                                    <a href="javascript:void(0)" data-toggle="modal" data-target="#modalAddress<?= $address->id; ?>" title="<?= trans('edit'); ?>">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#64748b" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                                                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                                                        </svg>
                                                                    </a>
                                                                    <a href="javascript:void(0)" onclick='deleteShippingAddress("<?= $address->id; ?>","<?= trans("confirm_delete", true); ?>");' title="<?= trans('delete'); ?>">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#ef4444" class="bi bi-trash3" viewBox="0 0 16 16">
                                                                            <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
                                                                        </svg>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endif;
                                                endforeach; ?>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 m-t-10">
                                                    <div class="form-group m-b-0">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" name="use_same_address_for_billing" value="1" id="use_same_address_for_billing" <?= $selectedSameAddressForBilling == 1 ? 'checked' : ''; ?>>
                                                            <label for="use_same_address_for_billing" class="custom-control-label" style="font-weight: 500; font-size: 13.5px;"><?= trans("use_same_address_for_billing"); ?></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="cart-form-billing-address" <?= empty($selectedSameAddressForBilling) ? "style='display:block; margin-top: 15px;'" : "style='display:none; margin-top: 15px;'"; ?>>
                                                <p class="text-shipping-address" style="font-weight: 600; font-size: 14px; color: #1e293b; margin-bottom: 10px;"><?= trans("billing_address"); ?></p>
                                                <div class="row">
                                                    <?php foreach ($shippingAddresses as $address):
                                                        if ($address->address_type == 'billing'):
                                                            $country = getCountry($address->country_id);
                                                            $state = getState($address->state_id); ?>
                                                            <div class="col-12 m-b-10">
                                                                <div class="shipping-address-box shipping-address-box-cart">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" class="custom-control-input" id="option_billing_address_<?= $address->id; ?>" name="billing_address_id" value="<?= $address->id; ?>" <?= $selectedBillingAddressId == $address->id ? 'checked' : ''; ?>>
                                                                        <label class="custom-control-label" for="option_billing_address_<?= $address->id; ?>">
                                                                            <strong class="m-b-5"><?= esc($address->title); ?></strong>
                                                                            <p style="color: #64748b; font-size: 12.5px;">
                                                                                <?= esc($address->address); ?>&nbsp;<?= esc($address->zip_code); ?><?= !empty($address->city) ? ' / ' . esc($address->city) : ''; ?><?= !empty($state->name) ? ' / ' . esc($state->name) : ''; ?>
                                                                            </p>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endif;
                                                    endforeach; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- SECTION 2: Delivery Option -->
                                    <div class="tab-checkout tab-checkout-open" style="background:#fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                                        <h2 class="title m-b-15" style="font-size: 17px; font-weight: 700; color: #1e293b;">
                                            <span class="badge badge-primary mr-2" style="font-size: 13px; border-radius: 50%; width: 24px; height: 24px; line-height: 18px; display: inline-flex; align-items: center; justify-content: center; background-color: #2563eb;">2</span>
                                            <?= trans("shipping_method"); ?>
                                        </h2>
                                        <div id="cart_shipping_methods_container">
                                            <?= view('cart/_shipping_methods'); ?>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="cart-shipping-loader" style="display: none;">
                                                    <div class="spinner">
                                                        <div class="bounce1"></div>
                                                        <div class="bounce2"></div>
                                                        <div class="bounce3"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- SECTION 3: Payment Method -->
                                    <div class="tab-checkout tab-checkout-open" style="background:#fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                                        <h2 class="title m-b-15" style="font-size: 17px; font-weight: 700; color: #1e293b;">
                                            <span class="badge badge-primary mr-2" style="font-size: 13px; border-radius: 50%; width: 24px; height: 24px; line-height: 18px; display: inline-flex; align-items: center; justify-content: center; background-color: #2563eb;">3</span>
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

                                            <?php if (authCheck() && !empty($paymentSettings->cash_on_delivery_enabled) && empty($cart->has_digital_product) && !empty($vendorCashOnDelivery)): ?>
                                                <div class="option-card" style="border-radius: 8px; margin-bottom: 10px; cursor: pointer;">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="payment_option" value="cash_on_delivery" id="option_cash_on_delivery" class="custom-control-input option-radio" <?= $idx == 0 ? 'checked' : ''; ?> required>
                                                        <label class="custom-control-label" for="option_cash_on_delivery"></label>
                                                    </div>
                                                    <div class="option-details">
                                                        <div class="method-name" style="font-weight: 700; font-size: 15px; color: #0f172a;"><?= trans("cash_on_delivery"); ?></div>
                                                        <div class="method-desc" style="font-size: 12px; color: #64748b; margin-top: 2px;"><?= trans("cash_on_delivery_exp"); ?></div>
                                                    </div>
                                                </div>
                                                <?php $idx++;
                                            endif; ?>

                                            <?php if (!empty($paymentSettings->wallet_status) && authCheck() && !empty(user()->balance) && user()->balance >= ($cart->totals->total ?? 0)): ?>
                                                <div class="option-card" style="border-radius: 8px; margin-bottom: 10px; cursor: pointer;">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="payment_option" value="wallet_balance" id="option_wallet_balance" class="custom-control-input option-radio" required>
                                                        <label class="custom-control-label" for="option_wallet_balance"></label>
                                                    </div>
                                                    <div class="option-details">
                                                        <div class="method-name" style="font-weight: 700; font-size: 15px; color: #0f172a;"><?= trans("wallet_balance"); ?>&nbsp;(<b><?= priceFormatted(user()->balance, $cart->currency_code, true); ?></b>)</div>
                                                        <div class="method-desc" style="font-size: 12px; color: #64748b; margin-top: 2px;"><?= trans("pay_wallet_balance_exp"); ?></div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
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

                        <?= view('cart/_order_summary'); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modalAddAddress" class="modal fade modal-custom" role="dialog">
    <div class="modal-dialog modal-dialog-shipping-address">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                <h4 class="modal-title"><?= trans("add_new_address"); ?></h4>
            </div>
            <form action="<?= base_url('add-shipping-address-post'); ?>" method="post" id="form_add_shipping_address" class="validate-form">
                <?= csrf_field(); ?>
                <input type="hidden" name="back_url" value="<?= getCurrentUrl(); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label"><?= trans("address_type"); ?></label>
                        <select name="address_type" class="select2 form-control" data-minimum-results-for-search="-1" required>
                            <option value="shipping"><?= trans("shipping_address"); ?></option>
                            <option value="billing"><?= trans("billing_address"); ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans("address_title"); ?></label>
                        <input type="text" name="title" class="form-control form-input" placeholder="<?= trans("address_title"); ?>" maxlength="250" required>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-12 col-md-6 m-b-sm-15">
                                <label class="control-label"><?= trans("first_name"); ?></label>
                                <input type="text" name="first_name" class="form-control form-input" placeholder="<?= trans("first_name"); ?>" maxlength="250" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="control-label"><?= trans("last_name"); ?></label>
                                <input type="text" name="last_name" class="form-control form-input" placeholder="<?= trans("last_name"); ?>" maxlength="250" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-12 col-md-6 m-b-sm-15">
                                <label class="control-label"><?= trans("email"); ?></label>
                                <input type="email" name="email" class="form-control form-input" placeholder="<?= trans("email"); ?>" maxlength="250" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="control-label"><?= trans("phone_number"); ?></label>
                                <input type="text" name="phone_number" class="form-control form-input" placeholder="<?= trans("phone_number"); ?>" maxlength="100" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <?php if ($generalSettings->single_country_mode != 1): ?>
                                <div class="col-12 col-md-6 m-b-sm-15">
                                    <label class="control-label"><?= trans("country"); ?></label>
                                    <select id="select_countries_new_address" name="country_id" class="select2 select2-req form-control" data-placeholder="<?= trans("country"); ?>" onchange="getStates(this.value,'new_address');" required>
                                        <option></option>
                                        <?php if (!empty($activeCountries)):
                                            foreach ($activeCountries as $item): ?>
                                                <option value="<?= $item->id; ?>"><?= esc($item->name); ?></option>
                                            <?php endforeach;
                                        endif; ?>
                                    </select>
                                </div>
                            <?php else: ?>
                                <input type="hidden" name="country_id" value="<?= $generalSettings->single_country_id; ?>">
                                <?php $states = getStatesByCountry($generalSettings->single_country_id);
                            endif; ?>
                            <div class="col-12 <?= $generalSettings->single_country_mode == 1 ? 'col-md-12' : 'col-md-6'; ?>">
                                <label class="control-label"><?= trans("state"); ?></label>
                                <div id="get_states_container_new_address">
                                    <select id="select_states_new_address" name="state_id" class="select2 select2-req form-control" data-placeholder="<?= trans("state"); ?>" data-id="select_states_new_address" required>
                                        <option></option>
                                        <?php if (!empty($states)):
                                            foreach ($states as $item): ?>
                                                <option value="<?= $item->id; ?>"><?= esc($item->name); ?></option>
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
                                <input type="text" name="city" class="form-control form-input" placeholder="<?= trans("city"); ?>" maxlength="250" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="control-label"><?= trans("zip_code"); ?></label>
                                <input type="text" name="zip_code" class="form-control form-input" placeholder="<?= trans("zip_code"); ?>" maxlength="90" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans("address"); ?></label>
                        <input type="text" name="address" class="form-control form-input" placeholder="<?= trans("address"); ?>" maxlength="490" required>
                    </div>
                </div>
                <div class="modal-footer text-right">
                    <button type="submit" class="btn btn-md btn-custom"><?= trans("submit"); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if (!empty($shippingAddresses)):
    foreach ($shippingAddresses as $address):?>
        <div id="modalAddress<?= $address->id; ?>" class="modal fade modal-custom" role="dialog">
            <div class="modal-dialog modal-dialog-shipping-address">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                        <h4 class="modal-title"><?= trans("edit_address"); ?></h4>
                    </div>
                    <form action="<?= base_url('edit-shipping-address-post'); ?>" method="post" id="form_edit_shipping_address_<?= $address->id; ?>" class="validate-form">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="id" value="<?= $address->id; ?>">
                        <input type="hidden" name="back_url" value="<?= getCurrentUrl(); ?>">
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="control-label"><?= trans("address_type"); ?></label>
                                <select name="address_type" class="select2 form-control" data-minimum-results-for-search="-1" required>
                                    <option value="shipping" <?= $address->address_type != 'billing' ? 'selected' : ''; ?>><?= trans("shipping_address"); ?></option>
                                    <option value="billing" <?= $address->address_type == 'billing' ? 'selected' : ''; ?>><?= trans("billing_address"); ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?= trans("address_title"); ?></label>
                                <input type="text" name="title" class="form-control form-input" value="<?= esc($address->title); ?>" placeholder="<?= trans("address_title"); ?>" maxlength="250" required>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-12 col-md-6 m-b-sm-15">
                                        <label class="control-label"><?= trans("first_name"); ?></label>
                                        <input type="text" name="first_name" class="form-control form-input" value="<?= esc($address->first_name); ?>" placeholder="<?= trans("first_name"); ?>" maxlength="250" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="control-label"><?= trans("last_name"); ?></label>
                                        <input type="text" name="last_name" class="form-control form-input" value="<?= esc($address->last_name); ?>" placeholder="<?= trans("last_name"); ?>" maxlength="250" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-12 col-md-6 m-b-sm-15">
                                        <label class="control-label"><?= trans("email"); ?></label>
                                        <input type="email" name="email" class="form-control form-input" value="<?= esc($address->email); ?>" placeholder="<?= trans("email"); ?>" maxlength="250" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="control-label"><?= trans("phone_number"); ?></label>
                                        <input type="text" name="phone_number" class="form-control form-input" value="<?= esc($address->phone_number); ?>" placeholder="<?= trans("phone_number"); ?>" maxlength="100" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <?php if ($generalSettings->single_country_mode != 1): ?>
                                        <div class="col-12 col-md-6 m-b-sm-15">
                                            <label class="control-label"><?= trans("country"); ?></label>
                                            <select id="select_countries_address_<?= $address->id; ?>" name="country_id" class="select2 select2-req form-control" onchange="getStates(this.value, 'address_<?= $address->id; ?>');" required>
                                                <?php if (!empty($activeCountries)):
                                                    foreach ($activeCountries as $item): ?>
                                                        <option value="<?= $item->id; ?>" <?= $item->id == $address->country_id ? 'selected' : ''; ?>><?= esc($item->name); ?></option>
                                                    <?php endforeach;
                                                endif; ?>
                                            </select>
                                        </div>
                                        <?php $states = getStatesByCountry($address->country_id);
                                    else: ?>
                                        <input type="hidden" name="country_id" value="<?= $generalSettings->single_country_id; ?>">
                                        <?php $states = getStatesByCountry($generalSettings->single_country_id);
                                    endif; ?>
                                    <div class="col-12 <?= $generalSettings->single_country_mode == 1 ? 'col-md-12' : 'col-md-6'; ?>">
                                        <label class="control-label"><?= trans("state"); ?></label>
                                        <div id="get_states_container_address_<?= $address->id; ?>">
                                            <select id="select_states_address_<?= $address->id; ?>" name="state_id" class="select2 select2-req form-control" required>
                                                <?php if (!empty($states)):
                                                    foreach ($states as $item): ?>
                                                        <option value="<?= $item->id; ?>" <?= $item->id == $address->state_id ? 'selected' : ''; ?>><?= esc($item->name); ?></option>
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
                                        <input type="text" name="city" class="form-control form-input" value="<?= esc($address->city); ?>" placeholder="<?= trans("city"); ?>" maxlength="250" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="control-label"><?= trans("zip_code"); ?></label>
                                        <input type="text" name="zip_code" class="form-control form-input" value="<?= esc($address->zip_code); ?>" placeholder="<?= trans("zip_code"); ?>" maxlength="90" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?= trans("address"); ?></label>
                                <input type="text" name="address" class="form-control form-input" value="<?= esc($address->address); ?>" placeholder="<?= trans("address"); ?>" maxlength="490" required>
                            </div>
                        </div>
                        <div class="modal-footer text-right">
                            <button type="submit" class="btn btn-md btn-custom"><?= trans("submit"); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach;
endif; ?>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        <?php if (!empty($selectedStateId)): ?>
            getShippingMethodsByLocation(<?= esc($selectedStateId); ?>);
        <?php endif; ?>
    });

    $(document).on('click', '.btn-express-confirm-trigger', function (e) {
        e.preventDefault();

        var $btn = $(this);
        var $allBtns = $('.btn-express-confirm-trigger');

        // Check terms
        if ($('#checkbox_terms').length && !$('#checkbox_terms').is(':checked')) {
            Swal.fire({
                text: 'Please accept the Terms and Conditions to proceed.',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return false;
        }

        // Check address
        var shippingAddrId = $('input[name="shipping_address_id"]:checked').val();
        if (!shippingAddrId && $('input[name="shipping_address_id"]').length > 0) {
            Swal.fire({
                text: 'Please select a delivery address or click "+ Add New Address" to proceed.',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return false;
        }

        // Gather form data
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
                        Swal.fire({
                            text: 'Razorpay SDK could not be loaded. Please check your internet connection and try again.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
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
                        notes: {
                            checkout_token: res.checkoutToken
                        },
                        theme: {
                            color: "#2563eb"
                        },
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
                                        $allBtns.html('<i class="fa fa-check mr-2"></i> Payment Successful! Redirecting...');
                                        window.location.href = verifyRes.redirectUrl;
                                    } else if (verifyRes && verifyRes.redirectUrl) {
                                        window.location.href = verifyRes.redirectUrl;
                                    } else {
                                        window.location.href = '<?= generateUrl("order-completed"); ?>/' + res.checkoutToken;
                                    }
                                },
                                error: function (xhr) {
                                    resetButtons();
                                    Swal.fire({
                                        text: 'A communication error occurred while finalizing payment. Please check your orders.',
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            });
                        },
                        modal: {
                            ondismiss: function () {
                                resetButtons();
                            }
                        }
                    };

                    var rzpInstance = new Razorpay(rzpOptions);
                    rzpInstance.on('payment.failed', function (failRes) {
                        resetButtons();
                        Swal.fire({
                            text: failRes.error ? (failRes.error.description || failRes.error.reason) : 'Payment could not be completed. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
                    rzpInstance.open();
                }
            },
            error: function (xhr) {
                resetButtons();
                var errMsg = 'A communication error occurred. Please check your internet connection and try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                }
                Swal.fire({
                    text: errMsg,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
</script>
