<style>
.bundle-toggle-btn:not(.collapsed) .fa-chevron-down {
    transform: rotate(180deg);
}
.bundle-toggle-btn:hover {
    background: #ede9fe !important;
}
.order-summary-container {
    box-sizing: border-box !important;
}
.order-summary-container .right {
    background-color: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px !important;
    padding: 20px !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04) !important;
    width: 100% !important;
    box-sizing: border-box !important;
    float: none !important;
    display: block !important;
    clear: both !important;
    overflow: hidden !important;
    margin-top: 0 !important;
}
.order-summary-container .cart-order-details {
    float: none !important;
    display: block !important;
    width: 100% !important;
    clear: both !important;
}
.order-summary-container .cart-order-details .item {
    float: none !important;
    display: block !important;
    width: 100% !important;
    clear: both !important;
    box-sizing: border-box !important;
}
.order-summary-container .cart-totals-summary {
    float: none !important;
    display: block !important;
    width: 100% !important;
    clear: both !important;
    box-sizing: border-box !important;
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
                    <?php
                    $productEditUrl = esc($cartItem->product_url) . (!empty($cartItem->id) ? '?cart_item_id=' . $cartItem->id : '') . (!empty($cartItem->is_bundle) ? '#tab_bundle_contents' : '');
                    ?>
                    <div class="item" style="display: block !important; width: 100% !important; float: none !important; margin-bottom: 16px; padding-bottom: 16px; <?= $itemIndex < $totalItemCount ? 'border-bottom: 1px solid #f1f5f9;' : ''; ?>">
                        <!-- Top Header: Image + Product Info -->
                        <div style="display: flex; align-items: flex-start; gap: 12px; width: 100%;">
                            <div style="flex-shrink: 0;">
                                <a href="<?= $productEditUrl; ?>">
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
                                    <a href="<?= $productEditUrl; ?>" style="font-weight: 600; font-size: 14px; color: #1e293b; text-decoration: none;">
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

                        <!-- VAT / GST (Item Level) -->
                        <?php if (!empty($cartItem->product_vat) && $cartItem->product_vat > 0):
                            $itemGst = calculateGstDetails($cartItem->product_vat_rate, $cartItem->product_vat, $cartItem->seller_id, $cart->location_state_id ?? null);
                        ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 6px; font-size: 11.5px; color: #64748b; background: #f8fafc; padding: 5px 10px; border-radius: 5px; border: 1px dashed #e2e8f0;">
                                <span>
                                    <i class="fa fa-percent text-muted" style="font-size: 10px; margin-right: 3px;"></i>
                                    <?= trans("gst"); ?> (<?= $cartItem->product_vat_rate; ?>%<?= $itemGst['is_interstate'] ? ' ' . trans('igst') : ''; ?>):
                                    <?php if (!$itemGst['is_interstate']): ?>
                                        <span style="color: #94a3b8; font-size: 10.5px;">(CGST <?= $itemGst['cgst_rate']; ?>% + SGST <?= $itemGst['sgst_rate']; ?>%)</span>
                                    <?php endif; ?>
                                </span>
                                <strong style="color: #334155; font-size: 12px;"><?= priceDecimal($cartItem->product_vat, $cart->currency_code); ?></strong>
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
                                                    <div style="background: #f1f5f9; font-weight: 700; font-size: 11px; color: #334155; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; padding: 5px 8px; flex-wrap: wrap; gap: 4px;">
                                                        <span><i class="fa fa-folder-open" style="color: #4f46e5;"></i> <?= esc($bCategory['name']); ?> <strong style="color: #1d4ed8;">(<?= priceDecimal($bCategory['subtotal'], $cart->currency_code); ?>)</strong></span>
                                                        <span style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 1px 6px; border-radius: 4px; font-weight: 700; font-size: 10.5px;">
                                                            <span style="font-size: 9.5px; color: #2563eb; text-transform: uppercase;">Total: </span><?= priceDecimal($bCategory['subtotal'], $cart->currency_code); ?>
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
        <div class="cart-totals-summary" style="margin-top: 22px;">
            <!-- Subtotal Row -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; font-size: 14px; color: #334155;">
                <span style="font-weight: 600;"><?= trans("subtotal"); ?></span>
                <strong style="font-size: 15px; color: #1e293b; text-align: right; min-width: 100px;"><?= priceDecimal($cart->totals->subtotal, $cart->currency_code); ?></strong>
            </div>

            <!-- Referral / Affiliate Discount -->
            <?php if ($cart->totals->affiliate_discount > 0): ?>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; font-size: 13.5px; color: #15803d;">
                    <span><?= trans("referral_discount"); ?> (<?= $cart->totals->affiliate_discount_rate; ?>%)</span>
                    <strong style="text-align: right; min-width: 100px;">- <?= priceDecimal($cart->totals->affiliate_discount, $cart->currency_code); ?></strong>
                </div>
            <?php endif; ?>

            <!-- GST Breakdown Section -->
            <?php if (!empty($cart->totals->vat) && $cart->totals->vat > 0):
                $cartSellerId = !empty($cart->items[0]->seller_id) ? $cart->items[0]->seller_id : null;
                $gstRate = !empty($cart->items[0]->product_vat_rate) ? $cart->items[0]->product_vat_rate : 18;
                $cartGst = calculateGstDetails($gstRate, $cart->totals->vat, $cartSellerId, $cart->location_state_id ?? null);
            ?>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; font-size: 14px; color: #334155;">
                    <span style="font-weight: 600;">
                        <?= trans("total_gst"); ?> <span style="font-size: 12px; font-weight: normal; color: #64748b;">(<?= $cartGst['is_interstate'] ? $cartGst['igst_rate'] : ($cartGst['cgst_rate'] + $cartGst['sgst_rate']); ?>%)</span>
                    </span>
                    <strong style="font-size: 14.5px; color: #1e293b; text-align: right; min-width: 100px;"><?= priceDecimal($cart->totals->vat, $cart->currency_code); ?></strong>
                </div>
                <?php if ($cartGst['is_interstate']): ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; font-size: 12.5px; color: #64748b; padding-left: 12px;">
                        <span>&bull; <?= trans("igst"); ?> (<?= $cartGst['igst_rate']; ?>%)</span>
                        <span style="text-align: right; min-width: 100px;"><?= priceDecimal($cartGst['igst_amount'], $cart->currency_code); ?></span>
                    </div>
                <?php else: ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; font-size: 12.5px; color: #64748b; padding-left: 12px;">
                        <span>&bull; <?= trans("cgst"); ?> (<?= $cartGst['cgst_rate']; ?>%)</span>
                        <span style="text-align: right; min-width: 100px;"><?= priceDecimal($cartGst['cgst_amount'], $cart->currency_code); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; font-size: 12.5px; color: #64748b; padding-left: 12px;">
                        <span>&bull; <?= trans("sgst"); ?> (<?= $cartGst['sgst_rate']; ?>%)</span>
                        <span style="text-align: right; min-width: 100px;"><?= priceDecimal($cartGst['sgst_amount'], $cart->currency_code); ?></span>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Shipping Cost -->
            <?php if (!empty($cart->totals->shipping_cost) && $cart->totals->shipping_cost > 0): ?>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; font-size: 14px; color: #334155;">
                    <span style="font-weight: 600;"><?= trans("shipping"); ?></span>
                    <strong style="font-size: 14.5px; color: #1e293b; text-align: right; min-width: 100px;"><?= priceDecimal($cart->totals->shipping_cost, $cart->currency_code); ?></strong>
                </div>
            <?php endif; ?>

            <!-- Coupon Code Discount -->
            <?php if (!empty($cart->coupon_code)): ?>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; font-size: 13.5px; color: #15803d;">
                    <span><?= trans("coupon"); ?> [<?= esc($cart->coupon_code); ?>] <a href="javascript:void(0)" class="font-weight-normal text-danger" onclick="removeCartDiscountCoupon();">[<?= trans("remove"); ?>]</a></span>
                    <strong style="text-align: right; min-width: 100px;">- <?= priceDecimal($cart->totals->coupon_discount, $cart->currency_code); ?></strong>
                </div>
            <?php endif; ?>

            <!-- Global Taxes -->
            <?php if (!empty($cart->totals->global_taxes_array)):
                foreach ($cart->totals->global_taxes_array as $taxItem):?>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; font-size: 13.5px; color: #64748b;">
                        <span><?= esc(getTaxName($taxItem['taxNameArray'], selectedLangId())); ?>&nbsp;(<?= $taxItem['taxRate']; ?>%)</span>
                        <strong style="text-align: right; min-width: 100px;"><?= priceDecimal($taxItem['taxTotal'], $cart->currency_code); ?></strong>
                    </div>
                <?php endforeach;
            endif; ?>

            <!-- Transaction Fee -->
            <?php if (!empty($cart->totals->transaction_fee)): ?>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; font-size: 13.5px; color: #64748b;">
                    <span><?= trans("transaction_fee"); ?><?= $cart->totals->transaction_fee_rate ? ' (' . numToDecimal($cart->totals->transaction_fee_rate) . '%)' : ''; ?></span>
                    <strong style="text-align: right; min-width: 100px;"><?= priceDecimal($cart->totals->transaction_fee, $cart->currency_code); ?></strong>
                </div>
            <?php endif; ?>

            <!-- Divider Line -->
            <hr style="margin: 14px 0 12px 0; border: 0; border-top: 1.5px solid #e2e8f0;">

            <!-- Grand Total Row -->
            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 16px;">
                <strong style="font-size: 16px; color: #0f172a; font-weight: 700;"><?= trans("total"); ?></strong>
                <strong style="font-size: 20px; font-weight: 800; color: #1d4ed8; text-align: right; min-width: 100px;"><?= priceDecimal(!empty($cart->totals->shipping_cost) ? $cart->totals->total : $cart->totals->total_before_shipping, $cart->currency_code); ?></strong>
            </div>
            <div style="text-align: right; font-size: 11px; color: #94a3b8; margin-top: 2px;">
                (Inclusive of all taxes & charges)
            </div>

            <!-- Savings Banner at Bottom -->
            <?php if (!empty($cart->totals->total_savings) && $cart->totals->total_savings > 0): ?>
                <div style="margin-top: 16px; background: #f0fdf4; border: 1px dashed #86efac; border-radius: 8px; padding: 10px 14px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <strong style="color: #166534; font-size: 13px;">
                            <i class="fa fa-tag text-success m-r-1"></i> Total Savings
                        </strong>
                        <strong class="text-success" style="font-size: 14px; font-weight: 800; color: #15803d !important; text-align: right;">
                            <?= priceDecimal($cart->totals->total_savings, $cart->currency_code); ?>
                        </strong>
                    </div>
                    <?php if (!empty($cart->totals->savings_percentage) && $cart->totals->savings_percentage > 0): ?>
                        <div style="font-size: 11px; color: #16a34a; font-weight: 600; margin-top: 4px;">
                            🎉 You are saving <?= $cart->totals->savings_percentage; ?>% on this order!
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>