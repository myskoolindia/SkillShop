<div class="col-sm-12 col-lg-4 order-summary-container">
    <h2 class="cart-section-title"><?= trans("order_summary"); ?> (<?= esc($cart->num_items); ?>)</h2>
    <div class="right">
        <div class="cart-order-details">
            <?php 
        //      echo '<pre>';
        // print_r($cart->items);
        // exit();
            if (!empty($cart->items)):
                foreach ($cart->items as $cartItem): ?>
                    <div class="item">
                        <div class="item-left">
                            <a href="<?= esc($cartItem->product_url); ?>">
                                <div class="product-image-box product-image-box-xs">
                                    <img src="<?= getOrderImageUrl($cartItem->product_image_data, $cartItem->product_id); ?>" data-src="<?= getOrderImageUrl($cartItem->product_image_data, $cartItem->product_id); ?>" alt="<?= esc($cartItem->product_title); ?>" class="lazyload img-fluid img-product">
                                </div>
                            </a>
                        </div>
                        <div class="item-right">
                            <?php if ($cartItem->product_type == 'digital'): ?>
                                <div class="list-item">
                                    <label class="badge badge-success-light badge-instant-download">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
                                            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                            <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/>
                                        </svg>&nbsp;&nbsp;<?= trans("instant_download"); ?>
                                    </label>
                                </div>
                            <?php endif; ?>
                            <div class="list-item">
                                <a href="<?= esc($cartItem->product_url); ?>"><?= esc($cartItem->product_title); ?></a>
                            </div>
                            <?php if (!empty($cartItem->product_options_summary)): ?>
                                <div class="product-variant-info">
                                    <?= $cartItem->product_options_summary; ?>
                                </div>
                            <?php endif; ?>
                            <div class="list-item seller">
                                <div class="badge badge-info-light">
                                    <?= trans("seller"); ?>:&nbsp;<a href="<?= generateProfileUrl($cartItem->seller_slug); ?>"><?= esc($cartItem->seller_username); ?></a>
                                </div>
                            </div>
                            <div class="list-item m-t-10 d-flex flex-wrap align-items-center" style="display: flex; flex-wrap: wrap; align-items: baseline; gap: 8px 14px; margin-bottom: 8px;">
                                <div style="display: inline-flex; align-items: baseline; gap: 6px;">
                                    <label style="font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 0; white-space: nowrap;"><?= trans("quantity"); ?>:</label>
                                    <strong class="lbl-price" style="font-size: 15px; font-weight: 700; color: #1e293b;"><?= $cartItem->quantity; ?></strong>
                                </div>
                                <span class="d-none d-sm-inline" style="color: #cbd5e1; font-size: 14px;">|</span>
                                <div style="display: inline-flex; align-items: baseline; gap: 6px;">
                                    <label style="font-size: 14px; font-weight: 700; color: #334155; margin-bottom: 0; white-space: nowrap;"><?= trans("price"); ?>:</label>
                                    <span style="white-space: nowrap;">
                                        <?php if (!empty($cartItem->has_discount)): ?>
                                            <del style="font-size: 13px; color: #94a3b8; font-weight: 500; margin-right: 4px; text-decoration: line-through;"><?= priceDecimal($cartItem->original_total_price, $cart->currency_code); ?></del>
                                        <?php endif; ?>
                                        <strong class="lbl-price" style="font-size: 17px; font-weight: 800; color: #1d4ed8; white-space: nowrap;"><?= priceDecimal($cartItem->total_price, $cart->currency_code); ?></strong>
                                    </span>
                                    <?php if (!empty($cartItem->has_discount) && !empty($cartItem->discount_amount) && $cartItem->discount_amount > 0): ?>
                                        <span class="badge badge-success" style="background: #dcfce7; color: #15803d; font-size: 10px; font-weight: 700; border: 1px solid #bbf7d0; padding: 2px 5px; border-radius: 4px; margin-left: 2px;">Save <?= priceDecimal($cartItem->discount_amount, $cart->currency_code); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if (!empty($cartItem->product_vat) && $cartItem->product_vat > 0):

                                if (!empty($cartItem->product_vat_rate) && $cartItem->product_vat_rate > 0): ?>

                                <?php
                                    $gstRate = $cartItem->product_vat_rate;
                                    $halfRate = $gstRate / 2;

                                    $totalVatAmount = $cartItem->product_vat;
                                    $halfAmount = $totalVatAmount / 2;
                                ?>

                                <div class="list-item">
                                    <label><?= trans("cgst"); ?>&nbsp;(<?= $halfRate; ?>%):</label>
                                    <strong><?= priceDecimal($halfAmount, $cart->currency_code); ?></strong>

                                    <label><?= trans("sgst"); ?>&nbsp;(<?= $halfRate; ?>%):</label>
                                    <strong><?= priceDecimal($halfAmount, $cart->currency_code); ?></strong>
                                </div>

                                <?php endif;?>

                                <div class="list-item">
                                    <label><?= trans("total"); ?> <?= trans("vat"); ?>&nbsp;(<?= $cartItem->product_vat_rate; ?>%) </label>
                                    <strong><?= priceDecimal($cartItem->product_vat, $cart->currency_code); ?></strong>
                            <?php endif; ?>
                            
                            <?php if(!empty($cartItem->is_bundle) && (!empty($cartItem->bundle_categories) || !empty($cartItem->bundle_products))): ?>
                                <div class="bundle-items-summary m-t-10 m-b-10 p-2" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 12px;">
                                    <div class="font-weight-bold text-muted m-b-5" style="font-size: 11px; text-transform: uppercase;">
                                        <i class="fa fa-cubes text-primary"></i> <?= esc($cartItem->product_title); ?> Items (<?= count($cartItem->bundle_products ?? []); ?>)
                                    </div>
                                    <?php if(!empty($cartItem->bundle_categories)): ?>
                                        <?php foreach($cartItem->bundle_categories as $bCategory): ?>
                                            <div class="m-t-5 m-b-5 p-1" style="background: #ffffff; border: 1px solid #edf2f7; border-radius: 4px;">
                                                <div class="d-flex justify-content-between align-items-center px-2 py-1" style="background: #f8fafc; font-weight: 700; font-size: 11px; color: #334155; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; border-radius: 4px 4px 0 0;">
                                                    <span><i class="fa fa-folder-open" style="color: #4f46e5;"></i> <?= esc($bCategory['name']); ?></span>
                                                    <span style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 1px 6px; border-radius: 4px; font-weight: 700;">
                                                        <span style="font-size: 9.5px; color: #2563eb; text-transform: uppercase;">Subtotal: </span><?= priceDecimal($bCategory['subtotal'], $cart->currency_code); ?>
                                                    </span>
                                                </div>
                                                <?php foreach($bCategory['items'] as $bProd): ?>
                                                    <div class="d-flex justify-content-between align-items-center py-1 px-1" style="border-bottom: 1px dashed #f1f5f9; display: flex; justify-content: space-between; font-size: 11px;">
                                                        <span style="max-width: 55%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: #475569;" title="<?= esc($bProd->title); ?>">
                                                            &bull; <?= esc($bProd->title); ?>
                                                        </span>
                                                        <span class="text-muted" style="white-space: nowrap;">
                                                            <?= $bProd->quantity; ?> × <?php if (!empty($bProd->has_discount)): ?><del style="font-size: 10px; color: #94a3b8;"><?= priceDecimal($bProd->original_price, $cart->currency_code); ?></del> <?php endif; ?><?= priceDecimal($bProd->unit_price, $cart->currency_code); ?>
                                                        </span>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <?php foreach($cartItem->bundle_products as $bProd): ?>
                                            <div class="d-flex justify-content-between align-items-center py-1" style="border-bottom: 1px dashed #e2e8f0; display: flex; justify-content: space-between;">
                                                <span style="max-width: 55%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= esc($bProd->title); ?>">
                                                    &bull; <?= esc($bProd->title); ?>
                                                </span>
                                                <span class="text-muted" style="font-size: 11px; white-space: nowrap;">
                                                    <?= $bProd->quantity; ?> × <?php if (!empty($bProd->has_discount)): ?><del style="font-size: 10px; color: #94a3b8;"><?= priceDecimal($bProd->original_price, $cart->currency_code); ?></del> <?php endif; ?><?= priceDecimal($bProd->unit_price, $cart->currency_code); ?>
                                                </span>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            <?php elseif(!empty($cartItem->is_bundle) && !empty($cartItem->bundle_summary)): ?>
                                <div class="bundle-breakdown m-t-5 m-b-5">
                                    <?php foreach($cartItem->bundle_summary as $b): ?>
                                        <small class="text-muted"><i class="icon-arrow-right"></i> <?= esc($b) ?></small><br>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach;
            endif; ?>
        </div>
        <div class="row-custom m-t-30 m-b-10">
            <strong><?= trans("subtotal"); ?><span class="float-right"><?= priceDecimal($cart->totals->subtotal, $cart->currency_code); ?></span></strong>
        </div>
        <?php if (!empty($cart->totals->total_savings) && $cart->totals->total_savings > 0): ?>
            <div class="row-custom m-b-15" style="background: #f0fdf4; border: 1px dashed #86efac; border-radius: 8px; padding: 10px 12px;">
                <div class="d-flex justify-content-between align-items-center" style="display: flex; justify-content: space-between; align-items: center;">
                    <strong style="color: #166534; font-size: 14px;">
                        <i class="fa fa-tag text-success m-r-1"></i> Total Savings
                    </strong>
                    <strong class="text-success" style="font-size: 15px; font-weight: 800; color: #15803d !important;">
                        - <?= priceDecimal($cart->totals->total_savings, $cart->currency_code); ?>
                    </strong>
                </div>
                <?php if (!empty($cart->totals->savings_percentage) && $cart->totals->savings_percentage > 0): ?>
                    <div style="font-size: 11px; color: #16a34a; font-weight: 600; margin-top: 3px;">
                        You are saving <?= $cart->totals->savings_percentage; ?>% on this order!
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <?php if ($cart->totals->affiliate_discount > 0): ?>
            <div class="row-custom m-b-10">
                <strong><?= trans("referral_discount"); ?>&nbsp;(<?= $cart->totals->affiliate_discount_rate; ?>%)<span class="float-right">-&nbsp;<?= priceDecimal($cart->totals->affiliate_discount, $cart->currency_code); ?></span></strong>
            </div>
        <?php endif;
        if (!empty($cart->totals->vat) && $cart->totals->vat > 0):
            
            $totalVatAmount = $cart->totals->vat;
            $halfAmount = $totalVatAmount / 2;

        ?>
            
            <div class="row-custom m-b-10">
                <strong><?= trans("cgst"); ?><span class="float-right"><?= priceDecimal($halfAmount, $cart->currency_code); ?></span></strong>
            </div>
            <div class="row-custom m-b-10">
                <strong><?= trans("sgst"); ?><span class="float-right"><?= priceDecimal($halfAmount, $cart->currency_code); ?></span></strong>
            </div>
            <div class="row-custom">
                <p class="line-seperator"></p>
            </div>
            <div class="row-custom m-b-10">
                <strong><?= trans("total"); ?> <?= trans("vat"); ?><span class="float-right"><?= priceDecimal($cart->totals->vat, $cart->currency_code); ?></span></strong>
            </div>
        <?php endif;
        if (!empty($cart->totals->shipping_cost) && $cart->totals->shipping_cost > 0): ?>
            <div class="row-custom m-b-10">
                <strong><?= trans("shipping"); ?><span class="float-right"><?= priceDecimal($cart->totals->shipping_cost, $cart->currency_code); ?></span></strong>
            </div>
        <?php endif;
        if (!empty($cart->coupon_code)): ?>
            <div class="row-custom m-b-10">
                <strong><?= trans("coupon"); ?>&nbsp;&nbsp;[<?= esc($cart->coupon_code); ?>]&nbsp;&nbsp;<a href="javascript:void(0)" class="font-weight-normal" onclick="removeCartDiscountCoupon();">[<?= trans("remove"); ?>]</a><span class="float-right">-&nbsp;<?= priceDecimal($cart->totals->coupon_discount, $cart->currency_code); ?></span></strong>
            </div>
        <?php endif;
        if (!empty($cart->totals->global_taxes_array)):
            foreach ($cart->totals->global_taxes_array as $taxItem):?>
                <div class="row-custom m-b-10">
                    <strong><?= esc(getTaxName($taxItem['taxNameArray'], selectedLangId())); ?>&nbsp;(<?= $taxItem['taxRate']; ?>%)<span class="float-right"><?= priceDecimal($taxItem['taxTotal'], $cart->currency_code); ?></span></strong>
                </div>
            <?php endforeach;
        endif;
        if (!empty($cart->totals->transaction_fee)): ?>
            <div class="row-custom m-b-15">
                <strong><?= trans("transaction_fee"); ?><?= $cart->totals->transaction_fee_rate ? ' (' . numToDecimal($cart->totals->transaction_fee_rate) . '%)' : ''; ?><span class="float-right"><?= priceDecimal($cart->totals->transaction_fee, $cart->currency_code); ?></span></strong>
            </div>
        <?php endif; ?>
        <div class="row-custom">
            <p class="line-seperator"></p>
        </div>
        <?php if (!empty($cart->totals->shipping_cost)): ?>
            <div class="row-custom">
                <strong><?= trans("total"); ?><span class="float-right"><?= priceDecimal($cart->totals->total, $cart->currency_code); ?></span></strong>
            </div>
        <?php else: ?>
            <div class="row-custom">
                <strong><?= trans("total"); ?><span class="float-right"><?= priceDecimal($cart->totals->total_before_shipping, $cart->currency_code); ?></span></strong>
            </div>
        <?php endif; ?>
    </div>
</div>