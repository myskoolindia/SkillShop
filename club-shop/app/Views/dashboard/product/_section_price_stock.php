<div class="section-product-details">
    <div class="form-box">
        <div class="row">
            <?php if ($product->product_type != 'digital' && $product->listing_type != 'ordinary_listing'): ?>
                <div class="col-sm-12 col-lg-6">
                    <div class="form-box-head">
                        <h4 class="title"><?= trans('stock'); ?></h4>
                    </div>
                    <div class="form-box-body">
                        <div class="form-group">
                            <input type="number" name="stock" class="form-control form-input" min="0" max="999999999" value="<?= $product->stock; ?>" placeholder="<?= trans("stock"); ?>" required>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <input type="hidden" name="stock" value="<?= $product->stock; ?>">
            <?php endif;
            if ($product->listing_type == 'ordinary_listing' || $productSettings->marketplace_sku == 1): ?>
                <div class="col-sm-12 col-lg-6">
                    <div class="form-box-head">
                        <h4 class="title">
                            <?= trans('sku'); ?>&nbsp;<small style="width: auto;display: inline-block;margin-bottom: 0;margin-top:0;">(<?= trans("product_code"); ?>)</small>
                        </h4>
                    </div>
                    <div class="form-box-body">
                        <div class="form-group">
                            <div class="position-relative">
                                <input type="text" name="sku" id="input_sku" class="form-control form-input" value="<?= $product->sku; ?>" placeholder="<?= trans("sku"); ?>&nbsp;(<?= trans("optional"); ?>)" maxlength="90">
                                <button type="button" class="btn btn-default btn-generate-sku" onclick="$('#input_sku').val(generateUniqueString()).trigger('input');"><?= trans("generate"); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <input type="hidden" name="sku" value="">
            <?php endif; ?>
        </div>
    </div>

    <?php if ($product->listing_type == 'sell_on_site' || $product->listing_type == 'license_key'): ?>
        <div class="form-box form-box-price form-box-last">
            <div class="form-box-head">
                <h4 class="title"><?= trans("product_price"); ?></h4>
            </div>
            <div class="form-box-body">

                <?php
                $thousandsSep = ($defaultCurrency->currency_format == 'european') ? '.' : ',';
                $decimalSep   = ($defaultCurrency->currency_format == 'european') ? ',' : '.';
                $spaceMoneySymbol = $defaultCurrency->space_money_symbol;
                $productDiscountAmount = isset($product->discount_amount) && (float)$product->discount_amount > 0 
                    ? (float)$product->discount_amount 
                    : (((float)$product->price > (float)$product->price_discounted) ? ((float)$product->price - (float)$product->price_discounted) : 0);
                ?>

                <div id="price_input_container" class="form-group"
                     data-commission-rate="<?= $commissionRate; ?>"
                     data-currency-symbol="<?= $defaultCurrency->symbol; ?>"
                     data-symbol-direction="<?= $defaultCurrency->symbol_direction; ?>"
                     data-thousands-separator="<?= $thousandsSep; ?>"
                     data-decimal-separator="<?= $decimalSep; ?>"
                     data-space-symbol="<?= $spaceMoneySymbol; ?>">

                    <div class="row">
                        <div class="col-xs-12 col-sm-4 m-b-sm-15">
                            <label class="font-600"><?= trans("price"); ?></label>
                            <?= renderPriceInput('price', $product->price, ['class' => 'form-control price-input', 'id' => 'product_price_input', 'placeholder' => $paymentSettings->default_currency, 'required' => ($product->is_free_product == 1 ? false : true)]); ?>
                        </div>

                        <div class="col-xs-12 col-sm-4 m-b-sm-15">
                            <label class="font-600">Flat Discount Amount (<?= $defaultCurrency->symbol; ?>)</label>
                            <div id="discount_amount_input_container">
                                <?= renderPriceInput('discount_amount', $productDiscountAmount, ['class' => 'form-control price-input', 'id' => 'product_discount_amount_input', 'placeholder' => '0.00']); ?>
                            </div>
                            <div id="container_discount_rate_pill" class="m-t-5" style="<?= ($product->discount_rate == 0 && empty($productDiscountAmount)) ? 'display:none;' : ''; ?>">
                                <span class="badge" style="background:#fee2e2; color:#b91c1c; border:1px solid #fca5a5; font-size:11px; font-weight:700; padding:2px 8px; border-radius:10px;">
                                    <i class="fa fa-tag"></i> <span id="badge_discount_rate_val"><?= ($product->discount_rate == 0 && !empty($productDiscountAmount)) ? '<1' : $product->discount_rate; ?></span>% OFF
                                </span>
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-4 m-b-sm-15">
                            <div class="row align-items-center">
                                <div class="col-sm-12">
                                    <label class="font-600"><?= trans("discounted_price"); ?></label>
                                    <div id="discount_input_container">
                                        <?= renderPriceInput('price_discounted', $product->price_discounted, ['class' => 'form-control price-input', 'id' => 'product_discounted_price_input', 'placeholder' => $paymentSettings->default_currency]); ?>
                                    </div>
                                </div>
                                <div class="col-sm-12 m-t-10">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="checkbox_has_discount" id="checkbox_discount_rate" <?= ($product->discount_rate == 0 && empty($productDiscountAmount)) ? 'checked' : ''; ?>>
                                        <label for="checkbox_discount_rate" class="custom-control-label"><?= trans("no_discount"); ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($paymentSettings->vat_status == 1): ?>
                        <div class="row m-t-15">
                            <div class="col-xs-12 col-sm-4">
                                <div class="row align-items-center">
                                    <div class="col-sm-12">
                                        <label class="font-600"><?= trans("product_based_vat"); ?><small>&nbsp;(<?= trans("vat_exp"); ?>)</small></label>
                                        <div id="vat_input_container" class="<?= $product->vat_rate == 0 ? 'display-none' : ''; ?>">
                                            <div class="input-group">
                                                <span class="input-group-addon">%</span>
                                                <input type="hidden" name="currency" value="<?= $paymentSettings->default_currency; ?>">
                                                <input type="number" name="vat_rate" id="input_vat_rate" aria-describedby="basic-addon-vat" class="form-control form-input" value="<?= $product->vat_rate; ?>" min="0" max="100" step="0.01">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 m-t-10">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="no_vat" id="checkbox_no_vat" <?= $product->vat_rate == 0 ? 'checked' : ''; ?>>
                                            <label for="checkbox_no_vat" class="custom-control-label"><?= trans("no_vat"); ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                        <div class="col-sm-12 m-t-30" id="calculated_amount_container">
                            <p class="calculated-price">
                                <strong><?= trans("discount_rate"); ?>:&nbsp;&nbsp;</strong>
                                <b id="span_discount_rate" class="earned-price"><?= $product->discount_rate; ?>%</b>
                            </p>

                            <p class="calculated-price">
                                <strong><?= trans("commission_rate"); ?>:&nbsp;&nbsp;</strong>
                                <b class="earned-price"><?= $commissionRate; ?>%</b>
                            </p>
                            <p class="calculated-price">
                                <strong><?= trans("you_will_earn"); ?> (<span id="currency_span"><?= $defaultCurrency->code; ?></span>):&nbsp;&nbsp;</strong>
                                <b id="span_earned_amount" class="earned-price">
                                    <?php
                                    $earnedAmount = 0;
                                    if (!empty($product->price)) {
                                        $price = $product->price;
                                        $discountedPrice = $product->price_discounted;

                                        if ($product->discount_rate == 0) {
                                            $finalPrice = $price;
                                        } else {
                                            $finalPrice = (!empty($discountedPrice) && $discountedPrice > 0) ? $discountedPrice : $price;
                                        }

                                        $earnedAmount = $finalPrice - (($finalPrice * $commissionRate) / 100);
                                    }
                                    echo esc(priceFormatted($earnedAmount, $defaultCurrency->code, true));
                                    ?>
                                </b>
                                &nbsp;&nbsp;<b>+&nbsp;&nbsp;&nbsp;<?= trans("vat"); ?></b>
                                <?php if ($product->product_type != 'digital'): ?>
                                    &nbsp;&nbsp;<b>+&nbsp;&nbsp;&nbsp;<?= trans("shipping_cost"); ?></b>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php if ($product->product_type == 'digital'): ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="is_free_product" id="checkbox_free_product" <?= $product->is_free_product == 1 ? 'checked' : ''; ?>>
                                <label for="checkbox_free_product" class="custom-control-label text-danger"><?= trans("free_product"); ?></label>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php elseif ($product->listing_type == 'ordinary_listing'):
        if ($productSettings->classified_price == 1): ?>
            <div class="form-box form-box-last">
                <div class="form-box-head">
                    <h4 class="title"><?= trans('price'); ?></h4>
                </div>
                <div class="form-box-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-xs-12 col-md-4 col-lg-3 m-b-sm-15">
                                <label class="font-600"><?= trans("currency"); ?></label>
                                <select name="currency" class="form-control custom-select select2" required>
                                    <?php if (!empty($currencies)):
                                        $allowAllCurrencies = $paymentSettings->allow_all_currencies_for_classied == 1;
                                        foreach ($currencies as $key => $value):
                                            if ($allowAllCurrencies || ($key == $defaultCurrency->code)): ?>
                                                <option value="<?= $key; ?>" <?= $key == $product->currency ? 'selected' : ''; ?>>
                                                    <?= esc($value->name) . ' (' . $value->symbol . ')'; ?>
                                                </option>
                                            <?php endif;
                                        endforeach;
                                    endif; ?>
                                </select>
                            </div>
                            <div class="col-xs-12 col-md-3 m-b-sm-15">
                                <label class="font-600"><?= trans("price"); ?></label>
                                <?= renderPriceInput('price', $product->price, ['id' => 'product_price_input', 'required' => $product->is_free_product == 1 ? false : true], false); ?>
                            </div>
                            <div class="col-xs-12 col-md-3 m-b-sm-15">
                                <label class="font-600">Flat Discount Amount</label>
                                <div id="discount_amount_input_container_ord" class="<?= $product->discount_rate == 0 ? 'display-none' : ''; ?>">
                                    <?= renderPriceInput('discount_amount', $productDiscountAmount, ['id' => 'product_discount_amount_input'], false); ?>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-3">
                                <div class="row align-items-center">
                                    <div class="col-sm-12">
                                        <label class="font-600"><?= trans("discounted_price"); ?></label>
                                        <div id="discount_input_container" class="<?= $product->discount_rate == 0 ? 'display-none' : ''; ?>">
                                            <?= renderPriceInput('price_discounted', $product->price_discounted, ['id' => 'product_discounted_price_input'], false); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 m-t-10">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="checkbox_has_discount" id="checkbox_discount_rate" <?= $product->discount_rate == 0 ? 'checked' : ''; ?>>
                                            <label for="checkbox_discount_rate" class="custom-control-label"><?= trans("no_discount"); ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif;
    elseif ($product->listing_type == 'bidding'): ?>
        <input type="hidden" name="currency" value="<?= $paymentSettings->default_currency; ?>">
    <?php endif; ?>

</div>

<script>
    $(document).on('click', '#checkbox_free_product', function () {
        if ($(this).is(':checked')) {
            $('#price_input_container').hide();
            $(".price-input").prop('required', false);
        } else {
            $('#price_input_container').show();
            $(".price-input").prop('required', true);
        }
    });
</script>
<?php if ($product->is_free_product == 1): ?>
    <style>
        #price_input_container {
            display: none;
        }
    </style>
<?php endif;
if ($product->listing_type == 'sell_on_site' || $product->listing_type == 'license_key'): ?>
    <script>
        $(document).ready(function() {
            const $container = $('#price_input_container');
            const commissionRate = parseFloat($container.attr('data-commission-rate')) || 0;
            const currencySymbol = $container.attr('data-currency-symbol') || '$';
            const symbolDirection = $container.attr('data-symbol-direction') || 'left';
            const thousandsSep = $container.attr('data-thousands-separator') || ',';
            const decimalSep = $container.attr('data-decimal-separator') || '.';
            const useSpace = $container.attr('data-space-symbol') == '1';

            var isSyncingInputs = false;

            // Recalculate whenever Price changes
            $(document).on('input change keyup', '#product_price_input', function() {
                if (isSyncingInputs) return;
                var price = getNumericValue($(this).val());
                var isNoDiscount = $('#checkbox_discount_rate').is(':checked');

                if (!isNoDiscount && price > 0) {
                    var discountAmount = getNumericValue($('#product_discount_amount_input').val());
                    if (discountAmount > 0) {
                        if (discountAmount > price) {
                            discountAmount = price;
                            isSyncingInputs = true;
                            $('#product_discount_amount_input').val(formatNumber(discountAmount));
                            isSyncingInputs = false;
                        }
                        var discountedPrice = Math.max(0, price - discountAmount);
                        isSyncingInputs = true;
                        $('#product_discounted_price_input').val(formatNumber(discountedPrice));
                        isSyncingInputs = false;
                    } else {
                        var discountedPrice = getNumericValue($('#product_discounted_price_input').val());
                        if (discountedPrice > 0 && discountedPrice < price) {
                            var discAmt = price - discountedPrice;
                            isSyncingInputs = true;
                            $('#product_discount_amount_input').val(formatNumber(discAmt));
                            isSyncingInputs = false;
                        }
                    }
                }
                calculateEarnings();
            });

            // Recalculate whenever Flat Discount Amount changes
            $(document).on('input change keyup', '#product_discount_amount_input', function() {
                if (isSyncingInputs) return;
                var price = getNumericValue($('#product_price_input').val());
                var discountAmount = getNumericValue($(this).val());

                if (price > 0 && discountAmount > 0) {
                    $('#checkbox_discount_rate').prop('checked', false);
                    if (discountAmount > price) {
                        discountAmount = price;
                    }
                    var discountedPrice = Math.max(0, price - discountAmount);
                    isSyncingInputs = true;
                    $('#product_discounted_price_input').val(formatNumber(discountedPrice));
                    isSyncingInputs = false;

                    var discountRate = Math.round((discountAmount / price) * 100);
                    var rateLabel = discountRate;
                    if (discountRate === 0 && discountAmount > 0) {
                        rateLabel = '<1';
                    }
                    $('#badge_discount_rate_val').text(rateLabel);
                    $('#container_discount_rate_pill').show();
                } else {
                    isSyncingInputs = true;
                    if (price > 0 && discountAmount === 0) {
                        $('#product_discounted_price_input').val(formatNumber(price));
                    }
                    isSyncingInputs = false;
                    $('#badge_discount_rate_val').text('0');
                    $('#container_discount_rate_pill').hide();
                }
                calculateEarnings();
            });

            // Recalculate whenever Discounted Price changes directly
            $(document).on('input change keyup', '#product_discounted_price_input', function() {
                if (isSyncingInputs) return;
                var price = getNumericValue($('#product_price_input').val());
                var discountedPrice = getNumericValue($(this).val());

                if (price > 0 && discountedPrice > 0 && discountedPrice < price) {
                    var discountAmount = Math.max(0, price - discountedPrice);
                    isSyncingInputs = true;
                    $('#product_discount_amount_input').val(formatNumber(discountAmount));
                    isSyncingInputs = false;

                    var discountRate = Math.round((discountAmount / price) * 100);
                    var rateLabel = discountRate;
                    if (discountRate === 0 && discountAmount > 0) {
                        rateLabel = '<1';
                    }
                    $('#badge_discount_rate_val').text(rateLabel);
                    $('#container_discount_rate_pill').show();
                } else {
                    isSyncingInputs = true;
                    if (discountedPrice >= price) {
                        $('#product_discount_amount_input').val('');
                    }
                    isSyncingInputs = false;
                    $('#badge_discount_rate_val').text('0');
                    $('#container_discount_rate_pill').hide();
                }
                calculateEarnings();
            });

            // Toggle discount based on checkbox state
            $(document).on('change', '#checkbox_discount_rate', function() {
                if ($(this).is(':checked')) {
                    $('#product_discount_amount_input').val('');
                    $('#container_discount_rate_pill').hide();
                    var price = getNumericValue($('#product_price_input').val());
                    $('#product_discounted_price_input').val(price > 0 ? formatNumber(price) : '');
                } else {
                    var price = getNumericValue($('#product_price_input').val());
                    var discPrice = getNumericValue($('#product_discounted_price_input').val());
                    if (price > 0 && (!discPrice || discPrice >= price)) {
                        $('#product_discounted_price_input').val(formatNumber(price));
                    }
                }
                calculateEarnings();
            });

            // Main Calculation Logic
            function calculateEarnings() {
                let price = getNumericValue($('#product_price_input').val());
                let discountedPrice = getNumericValue($('#product_discounted_price_input').val());
                let isNoDiscount = $('#checkbox_discount_rate').is(':checked');

                let finalPrice = price;
                let discountRate = 0;

                // Apply discount logic
                if (!isNoDiscount && discountedPrice > 0 && discountedPrice < price) {
                    finalPrice = discountedPrice;
                    discountRate = Math.round(((price - discountedPrice) / price) * 100);
                }

                // Calculate Earnings
                let commissionAmount = (finalPrice * commissionRate) / 100;
                let earnedAmount = finalPrice - commissionAmount;

                // Prevent negative earnings
                if (earnedAmount < 0) earnedAmount = 0;

                var discAmount = price - finalPrice;
                var rateLabel = discountRate;
                if (discountRate === 0 && discAmount > 0) {
                    rateLabel = '<1';
                }

                $('#span_discount_rate').text(rateLabel + '%');
                $('#badge_discount_rate_val').text(rateLabel);
                if ((discountRate > 0 || discAmount > 0) && !isNoDiscount) {
                    $('#container_discount_rate_pill').show();
                } else {
                    $('#container_discount_rate_pill').hide();
                }

                $('#span_earned_amount').text(formatMoney(earnedAmount));
            }

            // Custom Number Formatter (Numeric without Currency Symbol for inputs)
            function formatNumber(amount) {
                if (amount === undefined || amount === null || isNaN(amount)) return '';
                let decimals = (amount % 1 === 0) ? 0 : 2;
                let fixedAmount = Number(amount).toFixed(decimals).toString();
                let parts = fixedAmount.split('.');
                let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSep);
                let decimalPart = parts.length > 1 ? parts[1] : '';
                return decimals > 0 && decimalPart ? (integerPart + decimalSep + decimalPart) : integerPart;
            }

            // Custom Money Formatter (Manual Implementation)
            function formatMoney(amount) {
                let decimals = (amount % 1 === 0) ? 0 : 2;
                let fixedAmount = amount.toFixed(decimals).toString();

                let parts = fixedAmount.split('.');
                let integerPart = parts[0];
                let decimalPart = parts.length > 1 ? parts[1] : '';

                integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSep);

                let result = integerPart;
                if (decimals > 0) {
                    result += decimalSep + decimalPart;
                }

                let spaceStr = useSpace ? ' ' : '';

                if (symbolDirection === 'left') {
                    return currencySymbol + spaceStr + result;
                } else {
                    return result + spaceStr + currencySymbol;
                }
            }

            // Smart Input Parser
            function getNumericValue(value) {
                if (value === undefined || value === null) return 0;
                let clean = value.toString().trim();
                if (clean === '') return 0;

                clean = clean.replace(/[^0-9.,-]/g, '');
                if (clean === '') return 0;

                // Handle known currency format
                if (thousandsSep === ',' && decimalSep === '.') {
                    clean = clean.replace(/,/g, '');
                } else if (thousandsSep === '.' && decimalSep === ',') {
                    clean = clean.replace(/\./g, '').replace(',', '.');
                } else {
                    let lastDot = clean.lastIndexOf('.');
                    let lastComma = clean.lastIndexOf(',');
                    if (lastDot > -1 && lastComma > -1) {
                        if (lastComma > lastDot) {
                            clean = clean.replace(/\./g, '').replace(',', '.');
                        } else {
                            clean = clean.replace(/,/g, '');
                        }
                    } else if (lastComma > -1 && lastDot === -1) {
                        if (clean.length - lastComma - 1 === 3) {
                            clean = clean.replace(/,/g, '');
                        } else {
                            clean = clean.replace(',', '.');
                        }
                    }
                }

                let num = parseFloat(clean);
                return isNaN(num) ? 0 : num;
            }

            // Global function to auto-apply bundle sum (mandatory + optional selected) to price input
            window.applyCalculatedBundlePrice = function(targetAmount) {
                if (!targetAmount || targetAmount <= 0) return;
                targetAmount = Math.round(targetAmount);
                var currentPrice = getNumericValue($('#product_price_input').val());
                if (currentPrice === targetAmount) return;

                isSyncingInputs = true;
                $('#product_price_input').val(formatNumber(targetAmount));
                isSyncingInputs = false;

                var discountAmount = getNumericValue($('#product_discount_amount_input').val());
                var isNoDiscount = $('#checkbox_discount_rate').is(':checked');
                if (!isNoDiscount && discountAmount > 0) {
                    if (discountAmount > targetAmount) {
                        discountAmount = targetAmount;
                        isSyncingInputs = true;
                        $('#product_discount_amount_input').val(formatNumber(discountAmount));
                        isSyncingInputs = false;
                    }
                    var discountedPrice = Math.max(0, targetAmount - discountAmount);
                    isSyncingInputs = true;
                    $('#product_discounted_price_input').val(formatNumber(discountedPrice));
                    isSyncingInputs = false;
                } else if (isNoDiscount) {
                    isSyncingInputs = true;
                    $('#product_discounted_price_input').val(formatNumber(targetAmount));
                    isSyncingInputs = false;
                }
                calculateEarnings();
            };
        });
    </script>
<?php endif; ?>
<script>
    $('#checkbox_no_vat').change(function () {
        if (!this.checked) {
            $("#vat_input_container").show();
        } else {
            $('#input_vat_rate').val("0");
            $("#vat_input_container").hide();
        }
    });
</script>

