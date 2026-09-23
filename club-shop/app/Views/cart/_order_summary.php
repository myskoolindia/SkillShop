<style>
.bundle-toggle-btn:not(.collapsed) .fa-chevron-down {
    transform: rotate(180deg);
}
.bundle-toggle-btn:hover {
    background: #ede9fe !important;
}
</style>
<div class="col-sm-12 col-lg-4 order-summary-container">
    <h2 class="cart-section-title"><?= trans("order_summary"); ?> (<?= esc($cart->num_items); ?>)</h2>
    <div class="right">
        <div class="cart-order-details">
            <?php 
            if (!empty($cart->items)):
                $itemIndex = 0;
                $totalItemCount = count($cart->items);
                foreach ($cart->items as $cartItem): 
                    $itemIndex++;
                ?>
                    <div class="item" style="display: block; width: 100%; margin-bottom: 16px; padding-bottom: 16px; <?= $itemIndex < $totalItemCount ? 'border-bottom: 1px solid #f1f5f9;' : ''; ?>">
                        <!-- Top Header: Image + Product Info -->
                        <div style="display: flex; align-items: flex-start; gap: 12px; width: 100%;">
                            <div style="flex-shrink: 0;">
                                <a href="<?= esc($cartItem->product_url); ?>">
                                    <div class="product-image-box product-image-box-xs" style="width: 60px; height: 60px; border-radius: 6px; overflow: hidden; border: 1px solid #e2e8f0;">
                                        <img src="<?= getOrderImageUrl($cartItem->product_image_data, $cartItem->product_id); ?>" data-src="<?= getOrderImageUrl($cartItem->product_image_data, $cartItem->product_id); ?>" alt="<?= esc($cartItem->product_title); ?>" class="lazyload img-fluid img-product" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                </a>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <?php if ($cartItem->product_type == 'digital'): ?>
                                    <div style="margin-bottom: 4px;">
                                        <label class="badge badge-success-light badge-instant-download" style="font-size: 11px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
                                                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                                <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/>
                                            </svg>&nbsp;&nbsp;<?= trans("instant_download"); ?>
                                        </label>
                                    </div>
                                <?php endif; ?>
                                <div style="margin-bottom: 4px; line-height: 1.35;">
                                    <a href="<?= esc($cartItem->product_url); ?>" style="font-weight: 600; font-size: 14px; color: #1e293b; text-decoration: none;">
                                        <?= esc($cartItem->product_title); ?>
                                    </a>
                                </div>
                                <?php if (!empty($cartItem->product_options_summary)): ?>
                                    <div class="product-variant-info" style="font-size: 12px; color: #64748b; margin-bottom: 4px;">
                                        <?= $cartItem->product_options_summary; ?>
                                    </div>
                                <?php endif; ?>
                                <div class="seller" style="margin-top: 2px;">
                                    <div class="badge badge-info-light" style="font-size: 11px; padding: 2px 6px;">
                                        <?= trans("seller"); ?>:&nbsp;<a href="<?= generateProfileUrl($cartItem->seller_slug); ?>"><?= esc($cartItem->seller_username); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Full Width Row: Quantity & Price Box -->
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; flex-wrap: wrap; margin-top: 10px; padding: 7px 10px; background: #f8fafc; border-radius: 6px; border: 1px solid #e2e8f0; width: 100%;">
                            <div style="display: inline-flex; align-items: center; gap: 6px;">
                                <span style="font-size: 13px; font-weight: 600; color: #64748b;"><?= trans("quantity"); ?>:</span>
                                <strong class="lbl-price" style="font-size: 14px; font-weight: 700; color: #1e293b;"><?= $cartItem->quantity; ?></strong>
                            </div>
                            <div style="display: inline-flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                <span style="font-size: 13px; font-weight: 600; color: #64748b;"><?= trans("price"); ?>:</span>
                                <?php if (!empty($cartItem->has_discount)): ?>
                                    <del style="font-size: 12px; color: #94a3b8; font-weight: 500; text-decoration: line-through;"><?= priceDecimal($cartItem->original_total_price, $cart->currency_code); ?></del>
                                <?php endif; ?>
                                <strong class="lbl-price" style="font-size: 15px; font-weight: 800; color: #1d4ed8; white-space: nowrap;"><?= priceDecimal($cartItem->total_price, $cart->currency_code); ?></strong>
                                <?php if (!empty($cartItem->has_discount) && !empty($cartItem->discount_amount) && $cartItem->discount_amount > 0): ?>
                                    <span class="badge badge-success" style="background: #dcfce7; color: #15803d; font-size: 10px; font-weight: 700; border: 1px solid #bbf7d0; padding: 2px 6px; border-radius: 4px; white-space: nowrap;">Save <?= priceDecimal($cartItem->discount_amount, $cart->currency_code); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- VAT / GST (Full Width) -->
                        <?php if (!empty($cartItem->product_vat) && $cartItem->product_vat > 0):
                            if (!empty($cartItem->product_vat_rate) && $cartItem->product_vat_rate > 0): 
                                $gstRate = $cartItem->product_vat_rate;
                                $halfRate = $gstRate / 2;
                                $totalVatAmount = $cartItem->product_vat;
                                $halfAmount = $totalVatAmount / 2;
                            ?>
                                <div class="list-item" style="font-size: 12px; margin-top: 6px; color: #64748b; display: flex; justify-content: space-between;">
                                    <span><?= trans("cgst"); ?> (<?= $halfRate; ?>%): <strong><?= priceDecimal($halfAmount, $cart->currency_code); ?></strong></span>
                                    <span><?= trans("sgst"); ?> (<?= $halfRate; ?>%): <strong><?= priceDecimal($halfAmount, $cart->currency_code); ?></strong></span>
                                </div>
                            <?php endif; ?>
                            <div class="list-item" style="font-size: 12px; margin-top: 4px; color: #64748b; display: flex; justify-content: space-between;">
                                <span><?= trans("total"); ?> <?= trans("vat"); ?> (<?= $cartItem->product_vat_rate; ?>%):</span>
                                <strong><?= priceDecimal($cartItem->product_vat, $cart->currency_code); ?></strong>
                            </div>
                        <?php endif; ?>

                        <!-- Collapsible Full Width Bundle Items Breakdown -->
                        <?php if(!empty($cartItem->is_bundle) && (!empty($cartItem->bundle_categories) || !empty($cartItem->bundle_products) || !empty($cartItem->bundle_summary))): 
                            $bundleCollapseId = 'collapse_bundle_' . $cartItem->product_id . '_' . $itemIndex;
                            $bundleItemCount = !empty($cartItem->bundle_products) ? count($cartItem->bundle_products) : (!empty($cartItem->bundle_summary) ? count($cartItem->bundle_summary) : 0);
                        ?>
                            <div class="bundle-collapse-wrapper" style="margin-top: 8px;">
                                <a class="bundle-toggle-btn collapsed" 
                                   data-toggle="collapse" 
                                   href="#<?= $bundleCollapseId; ?>" 
                                   role="button" 
                                   aria-expanded="false" 
                                   aria-controls="<?= $bundleCollapseId; ?>"
                                   style="display: flex; align-items: center; justify-content: space-between; font-size: 11.5px; font-weight: 600; color: #3730a3; background: #f5f3ff; border: 1px solid #ddd6fe; border-radius: 6px; padding: 6px 10px; text-decoration: none; transition: background 0.2s ease;">
                                    <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 70%;">
                                        <i class="fa fa-cubes text-primary" style="margin-right: 4px;"></i> <?= esc($cartItem->product_title); ?> Items <?= $bundleItemCount > 0 ? '(' . $bundleItemCount . ')' : ''; ?>
                                    </span>
                                    <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 11px; color: #6366f1; white-space: nowrap;">
                                        <span>View details</span>
                                        <i class="fa fa-chevron-down" style="font-size: 10px; transition: transform 0.2s ease;"></i>
                                    </span>
                                </a>
                                
                                <div class="collapse" id="<?= $bundleCollapseId; ?>">
                                    <?php if(!empty($cartItem->bundle_categories)): ?>
                                        <div class="bundle-items-summary" style="margin-top: 6px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 12px; width: 100%; padding: 6px;">
                                            <?php foreach($cartItem->bundle_categories as $bCategory): ?>
                                                <div style="margin-top: 4px; margin-bottom: 6px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden;">
                                                    <div style="background: #f1f5f9; font-weight: 700; font-size: 11px; color: #334155; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; padding: 5px 8px;">
                                                        <span><i class="fa fa-folder-open" style="color: #4f46e5;"></i> <?= esc($bCategory['name']); ?></span>
                                                        <span style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 1px 6px; border-radius: 4px; font-weight: 700; font-size: 10.5px;">
                                                            <span style="font-size: 9.5px; color: #2563eb; text-transform: uppercase;">Subtotal: </span><?= priceDecimal($bCategory['subtotal'], $cart->currency_code); ?>
                                                        </span>
                                                    </div>
                                                    <div style="padding: 4px 6px;">
                                                        <?php foreach($bCategory['items'] as $bProd): ?>
                                                            <div style="border-bottom: 1px dashed #f1f5f9; display: flex; justify-content: space-between; align-items: center; font-size: 11px; padding: 4px 2px;">
                                                                <span style="max-width: 58%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: #475569;" title="<?= esc($bProd->title); ?>">
                                                                    &bull; <?= esc($bProd->title); ?>
                                                                </span>
                                                                <span class="text-muted" style="white-space: nowrap; font-size: 11px;">
                                                                    <?= $bProd->quantity; ?> × <?php if (!empty($bProd->has_discount)): ?><del style="font-size: 10px; color: #94a3b8; margin-right: 4px;"><?= priceDecimal($bProd->original_price, $cart->currency_code); ?></del><?php endif; ?><strong style="color: #1e293b;"><?= priceDecimal($bProd->unit_price, $cart->currency_code); ?></strong>
                                                                </span>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php elseif(!empty($cartItem->bundle_products)): ?>
                                        <div class="bundle-items-summary" style="margin-top: 6px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 4px 8px;">
                                            <?php foreach($cartItem->bundle_products as $bProd): ?>
                                                <div style="border-bottom: 1px dashed #e2e8f0; display: flex; justify-content: space-between; align-items: center; font-size: 11px; padding: 4px 0;">
                                                    <span style="max-width: 58%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= esc($bProd->title); ?>">
                                                        &bull; <?= esc($bProd->title); ?>
                                                    </span>
                                                    <span class="text-muted" style="font-size: 11px; white-space: nowrap;">
                                                        <?= $bProd->quantity; ?> × <?php if (!empty($bProd->has_discount)): ?><del style="font-size: 10px; color: #94a3b8; margin-right: 4px;"><?= priceDecimal($bProd->original_price, $cart->currency_code); ?></del><?php endif; ?><strong style="color: #1e293b;"><?= priceDecimal($bProd->unit_price, $cart->currency_code); ?></strong>
                                                    </span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php elseif(!empty($cartItem->bundle_summary)): ?>
                                        <div class="bundle-breakdown" style="margin-top: 6px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 6px 10px;">
                                            <?php foreach($cartItem->bundle_summary as $b): ?>
                                                <small class="text-muted" style="display: block; font-size: 11px; margin-bottom: 2px;"><i class="icon-arrow-right"></i> <?= esc($b) ?></small>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
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