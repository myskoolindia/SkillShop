<div id="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <?php if (!empty($cart) && !empty($cart->items)): ?>
                    <div class="shopping-cart">
                        <div class="row">
                            <div class="col-sm-12 col-lg-8">
                                <div class="left">
                                    <h1 class="cart-section-title"><?= trans("my_cart"); ?> (<?= esc($cart->num_items); ?>)</h1>
<style>
.shopping-cart .cart-item-card {
    display: block !important;
    width: 100% !important;
    float: none !important;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 22px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.shopping-cart .cart-item-card .cart-item-image {
    display: block !important;
    width: 80px !important;
    flex-shrink: 0;
}
.shopping-cart .cart-item-card .cart-item-details {
    display: block !important;
    flex: 1 1 auto;
    min-width: 0;
    padding: 0 !important;
}
.shopping-cart .cart-item-card .cart-item-quantity {
    display: flex !important;
    width: auto !important;
    padding-left: 0 !important;
}
.shopping-cart .cart-item-card .number-spinner {
    width: 116px;
    height: 38px;
}
.shopping-cart .cart-item-card .number-spinner .input-group {
    height: 38px;
}
.shopping-cart .cart-item-card .number-spinner button {
    height: 38px;
    width: 34px;
    padding: 0;
    line-height: 36px;
    border: 1px solid #cbd5e1;
    background: #f8fafc !important;
    color: #334155;
    font-weight: 700;
}
.shopping-cart .cart-item-card .number-spinner button:hover {
    background: #e2e8f0 !important;
}
.shopping-cart .cart-item-card .number-spinner input {
    height: 38px;
    font-size: 14px;
    font-weight: 700;
    border-top: 1px solid #cbd5e1;
    border-bottom: 1px solid #cbd5e1;
    border-left: none;
    border-right: none;
    padding: 0 4px !important;
    color: #1e293b;
}
@media (max-width: 767.98px) {
    .shopping-cart .cart-item-card {
        padding: 14px;
    }
    .shopping-cart .cart-item-top-row {
        flex-direction: column;
        align-items: flex-start !important;
    }
    .shopping-cart .cart-item-card .cart-item-actions-col {
        width: 100% !important;
        justify-content: space-between;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px dashed #e2e8f0;
    }
}
</style>
                                    <?php 
                                    // echo '<pre>';
                                    // print_r($cart);
                                    // exit();
                                    if (!empty($cart->items)):
                                        foreach ($cart->items as $cartItem): ?>
                                            <div class="item cart-item-card">
                                                <!-- Top Row: Product Details & Controls -->
                                                <div class="cart-item-top-row d-flex flex-wrap align-items-center justify-content-between" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; padding-bottom: <?= (!empty($cartItem->is_bundle) && (!empty($cartItem->bundle_categories) || !empty($cartItem->bundle_products))) ? '14px' : '0' ?>; <?= (!empty($cartItem->is_bundle) && (!empty($cartItem->bundle_categories) || !empty($cartItem->bundle_products))) ? 'border-bottom: 1px solid #f1f5f9;' : '' ?>">
                                                    
                                                    <!-- Left Side: Image + Metadata -->
                                                    <div class="d-flex align-items-center" style="display: flex; align-items: center; gap: 16px; flex: 1 1 320px; min-width: 0;">
                                                        <div class="cart-item-image">
                                                            <a href="<?= esc($cartItem->product_url); ?>">
                                                                <div class="product-image-box product-image-box-sm" style="width: 80px; height: 80px; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0;">
                                                                    <img src="<?= getOrderImageUrl($cartItem->product_image_data, $cartItem->product_id); ?>" data-src="<?= getOrderImageUrl($cartItem->product_image_data, $cartItem->product_id); ?>" alt="<?= esc($cartItem->product_title); ?>" class="lazyload img-fluid img-product" style="width: 100%; height: 100%; object-fit: cover;">
                                                                </div>
                                                            </a>
                                                        </div>

                                                        <div class="cart-item-details">
                                                            <?php if ($cartItem->product_type == 'digital'): ?>
                                                                <div class="m-b-4">
                                                                    <label class="badge badge-success-light badge-instant-download">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
                                                                            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                                                            <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/>
                                                                        </svg>&nbsp;&nbsp;<?= trans("instant_download"); ?>
                                                                    </label>
                                                                </div>
                                                            <?php endif; ?>
                                                            <div class="product-title" style="margin-bottom: 4px;">
                                                                <a href="<?= esc($cartItem->product_url); ?>" style="font-size: 16px; font-weight: 700; color: #1e293b; text-decoration: none;">
                                                                    <?= esc($cartItem->product_title); ?>
                                                                </a>
                                                            </div>
                                                            <?php if (!empty($cartItem->product_options_summary)): ?>
                                                                <div class="product-variant-info m-b-4">
                                                                    <?= $cartItem->product_options_summary; ?>
                                                                </div>
                                                            <?php endif; ?>

                                                            <div class="d-flex flex-wrap align-items-center" style="display: flex; flex-wrap: wrap; align-items: center; gap: 8px 14px; margin-bottom: 6px;">
                                                                <div class="badge badge-info-light" style="font-size: 12px;">
                                                                    <?= trans("seller"); ?>:&nbsp;<a href="<?= generateProfileUrl($cartItem->seller_slug); ?>"><?= esc($cartItem->seller_username); ?></a>
                                                                </div>
                                                                <?php if (empty($cartItem->is_stock_available)): ?>
                                                                    <span class="badge badge-danger-light"><?= trans("out_of_stock"); ?></span>
                                                                <?php endif; ?>
                                                            </div>

                                                            <div class="cart-item-pricing-row d-flex flex-wrap align-items-center" style="display: flex; flex-wrap: wrap; align-items: center; gap: 8px 16px;">
                                                                <?php if ($cartItem->purchase_type != 'bidding'): ?>
                                                                    <div class="d-inline-flex align-items-center" style="display: inline-flex; align-items: center; gap: 6px;">
                                                                        <label style="font-size: 14px; font-weight: 600; color: #64748b; margin-bottom: 0; white-space: nowrap;"><?= trans("unit_price"); ?>:</label>
                                                                        <span style="white-space: nowrap;">
                                                                            <?php if (!empty($cartItem->has_discount)): ?>
                                                                                <del style="font-size: 14px; color: #94a3b8; font-weight: 500; margin-right: 4px; text-decoration: line-through;"><?= priceDecimal($cartItem->original_unit_price, $cart->currency_code); ?></del>
                                                                            <?php endif; ?>
                                                                            <strong class="lbl-price" style="font-size: 16px; font-weight: 700; color: #1e293b;">
                                                                                <?= priceDecimal($cartItem->unit_price, $cart->currency_code); ?>
                                                                            </strong>
                                                                        </span>
                                                                    </div>
                                                                    <span class="d-none d-sm-inline" style="color: #cbd5e1; font-weight: 300; font-size: 16px; line-height: 1;">|</span>
                                                                <?php endif; ?>
                                                                <div class="d-inline-flex align-items-center" style="display: inline-flex; align-items: center; gap: 6px;">
                                                                    <label style="font-size: 14px; font-weight: 700; color: #334155; margin-bottom: 0; white-space: nowrap;"><?= trans("total"); ?>:</label>
                                                                    <span style="white-space: nowrap;">
                                                                        <?php if (!empty($cartItem->has_discount)): ?>
                                                                            <del style="font-size: 15px; color: #94a3b8; font-weight: 500; margin-right: 6px; text-decoration: line-through;"><?= priceDecimal($cartItem->original_total_price, $cart->currency_code); ?></del>
                                                                        <?php endif; ?>
                                                                        <strong class="lbl-price" style="font-size: 19px; font-weight: 800; color: #1d4ed8;"><?= priceDecimal($cartItem->total_price, $cart->currency_code); ?></strong>
                                                                    </span>
                                                                    <?php if (!empty($cartItem->has_discount) && !empty($cartItem->discount_amount) && $cartItem->discount_amount > 0): ?>
                                                                        <span class="badge badge-success" style="background: #dcfce7; color: #15803d; font-size: 11px; font-weight: 700; border: 1px solid #bbf7d0; padding: 2px 6px; border-radius: 4px; margin-left: 4px;">Save <?= priceDecimal($cartItem->discount_amount, $cart->currency_code); ?></span>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Right Side: Quantity & Action Buttons -->
                                                    <div class="cart-item-actions-col d-flex align-items-center" style="display: flex; align-items: center; gap: 10px; flex-shrink: 0;">
                                                        <div class="cart-item-quantity" style="display: flex; align-items: center;">
                                                            <?php if ($cartItem->purchase_type == 'bidding'): ?>
                                                                <span style="font-size: 14px; font-weight: 600; color: #334155;"><?= trans("quantity") . ': ' . esc($cartItem->quantity); ?></span>
                                                            <?php else:
                                                                if ($cartItem->product_type != 'digital'):?>
                                                                    <div class="number-spinner">
                                                                        <div class="input-group">
                                                                            <span class="input-group-btn">
                                                                                <button type="button" class="btn btn-default btn-spinner-minus" data-cart-item-id="<?= $cartItem->id; ?>" data-dir="dwn" style="border-radius: 6px 0 0 6px;">-</button>
                                                                            </span>
                                                                            <input type="text" id="q-<?= $cartItem->id; ?>" class="form-control text-center" value="<?= $cartItem->quantity; ?>" data-product-id="<?= $cartItem->product_id; ?>" data-cart-item-id="<?= $cartItem->id; ?>">
                                                                            <span class="input-group-btn">
                                                                                <button type="button" class="btn btn-default btn-spinner-plus" data-cart-item-id="<?= $cartItem->id; ?>" data-dir="up" style="border-radius: 0 6px 6px 0;">+</button>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                <?php endif;
                                                            endif; ?>
                                                        </div>

                                                        <a href="javascript:void(0)" class="btn btn-sm btn-outline-danger" style="border-radius: 6px; padding: 7px 12px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;" onclick="removeFromCart('<?= $cartItem->id; ?>');" title="<?= trans("remove"); ?>">
                                                            <i class="icon-close"></i><span class="d-none d-sm-inline">&nbsp;<?= trans("remove"); ?></span>
                                                        </a>
                                                        
                                                        <?php if(!empty($cartItem->is_bundle)): ?>
                                                            <a href="<?= esc($cartItem->product_url); ?>?cart_item_id=<?= $cartItem->id; ?>#tab_bundle_contents" class="btn btn-sm btn-outline-primary" style="border-radius: 6px; padding: 7px 12px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                                                <i class="fa fa-sliders"></i><span class="d-none d-sm-inline">&nbsp;Customize</span>
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <!-- FULL-WIDTH Bundle Package Breakdown (100% of card width) -->
                                                <?php if(!empty($cartItem->is_bundle) && (!empty($cartItem->bundle_categories) || !empty($cartItem->bundle_products))): ?>
                                                    <div class="bundle-items-container m-t-15 p-3" style="width: 100%; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
                                                        <div class="d-flex justify-content-between align-items-center m-b-12 pb-2" style="border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                                                            <div>
                                                                <strong style="color: #1e293b; font-size: 14px; font-weight: 700;">
                                                                    <i class="fa fa-cubes text-primary m-r-1"></i> <?= esc($cartItem->product_title); ?> Contents (<?= count($cartItem->bundle_products ?? []); ?>)
                                                                </strong>
                                                            </div>
                                                            <a href="<?= esc($cartItem->product_url); ?>?cart_item_id=<?= $cartItem->id; ?>#tab_bundle_contents" class="btn btn-sm btn-outline-primary" style="font-size: 12px; padding: 3px 10px; border-radius: 4px; font-weight: 600;">
                                                                <i class="fa fa-sliders m-r-1"></i> Customize
                                                            </a>
                                                        </div>

                                                        <?php if(!empty($cartItem->bundle_categories)): ?>
                                                            <?php foreach($cartItem->bundle_categories as $bCategory): ?>
                                                                <div class="bundle-category-section m-b-12" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.03);">
                                                                    <div class="d-flex justify-content-between align-items-center px-3 py-2" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                                                                        <div style="font-weight: 700; font-size: 13.5px; color: #1e293b; display: inline-flex; align-items: center; gap: 8px;">
                                                                            <span><i class="fa fa-folder-open" style="color: #4f46e5;"></i> <?= esc($bCategory['name']); ?></span>
                                                                            <span class="badge badge-secondary" style="font-size: 11px; background: #e2e8f0; color: #475569; font-weight: 600; padding: 3px 8px; border-radius: 12px;"><?= count($bCategory['items']); ?> items / <?= $bCategory['total_units']; ?> units</span>
                                                                        </div>
                                                                        <div class="category-subtotal-badge" style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 3px 10px; display: inline-flex; align-items: center; gap: 6px;">
                                                                            <span style="font-size: 11px; font-weight: 700; color: #2563eb; text-transform: uppercase; letter-spacing: 0.3px;">Category Subtotal:</span>
                                                                            <strong style="font-size: 14px; font-weight: 800; color: #1d4ed8;"><?= priceDecimal($bCategory['subtotal'], $cart->currency_code); ?></strong>
                                                                        </div>
                                                                    </div>
                                                                    <div class="table-responsive">
                                                                        <table class="table table-sm table-borderless m-b-0" style="font-size: 13px; width: 100%; margin-bottom: 0;">
                                                                            <thead>
                                                                                <tr style="color: #64748b; border-bottom: 1px dashed #e2e8f0; font-size: 12px; background: #fafafa;">
                                                                                    <th style="font-weight: 600; padding: 8px 12px;">Item</th>
                                                                                    <th class="text-center" style="font-weight: 600; padding: 8px 12px; width: 130px;">Unit Price</th>
                                                                                    <th class="text-center" style="font-weight: 600; padding: 8px 12px; width: 70px;">Qty</th>
                                                                                    <th class="text-right" style="font-weight: 600; padding: 8px 12px; width: 130px;">Total</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <?php foreach($bCategory['items'] as $bProd): ?>
                                                                                    <tr style="border-bottom: 1px solid #f8fafc;">
                                                                                        <td style="padding: 8px 12px; vertical-align: middle;">
                                                                                            <div class="d-flex align-items-center" style="display: flex; align-items: center;">
                                                                                                <img src="<?= getProductMainImage($bProd->product_id, 'image_small'); ?>" alt="<?= esc($bProd->title); ?>" style="width: 36px; height: 36px; object-fit: cover; border-radius: 4px; margin-right: 10px; border: 1px solid #e2e8f0; flex-shrink: 0;">
                                                                                                <div>
                                                                                                    <a href="<?= esc($bProd->product_url); ?>" style="color: #1e293b; font-weight: 500; text-decoration: none;">
                                                                                                        <?= esc($bProd->title); ?>
                                                                                                    </a>
                                                                                                    <?php if(!empty($bProd->sku)): ?>
                                                                                                        <br><small class="text-muted" style="font-size: 11px;">SKU: <?= esc($bProd->sku); ?></small>
                                                                                                    <?php endif; ?>
                                                                                                </div>
                                                                                            </div>
                                                                                        </td>
                                                                                        <td class="text-center" style="padding: 8px 12px; vertical-align: middle; color: #475569;">
                                                                                            <?php if (!empty($bProd->has_discount)): ?>
                                                                                                <del style="font-size: 11px; color: #94a3b8; margin-right: 3px;"><?= priceDecimal($bProd->original_price, $cart->currency_code); ?></del>
                                                                                            <?php endif; ?>
                                                                                            <span style="font-weight: 600; color: #334155;"><?= priceDecimal($bProd->unit_price, $cart->currency_code); ?></span>
                                                                                        </td>
                                                                                        <td class="text-center" style="padding: 8px 12px; vertical-align: middle; font-weight: 600; color: #1e293b;">
                                                                                            <?= $bProd->quantity; ?>
                                                                                        </td>
                                                                                        <td class="text-right font-weight-bold" style="padding: 8px 12px; vertical-align: middle; color: #2563eb;">
                                                                                            <?php if (!empty($bProd->has_discount)): ?>
                                                                                                <del style="font-size: 11px; color: #94a3b8; font-weight: 400; margin-right: 3px;"><?= priceDecimal($bProd->original_total_price, $cart->currency_code); ?></del>
                                                                                            <?php endif; ?>
                                                                                            <span><?= priceDecimal($bProd->total_price, $cart->currency_code); ?></span>
                                                                                        </td>
                                                                                    </tr>
                                                                                <?php endforeach; ?>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-borderless m-b-0" style="font-size: 13px; width: 100%;">
                                                                    <thead>
                                                                        <tr style="color: #718096; border-bottom: 1px dashed #cbd5e0; font-size: 12px;">
                                                                            <th style="font-weight: 600; padding: 6px 10px;">Item</th>
                                                                            <th class="text-center" style="font-weight: 600; padding: 6px 10px; width: 130px;">Unit Price</th>
                                                                            <th class="text-center" style="font-weight: 600; padding: 6px 10px; width: 70px;">Qty</th>
                                                                            <th class="text-right" style="font-weight: 600; padding: 6px 10px; width: 130px;">Subtotal</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php foreach($cartItem->bundle_products as $bProd): ?>
                                                                            <tr style="border-bottom: 1px solid #edf2f7;">
                                                                                <td style="padding: 8px 10px; vertical-align: middle;">
                                                                                    <div class="d-flex align-items-center" style="display: flex; align-items: center;">
                                                                                        <img src="<?= getProductMainImage($bProd->product_id, 'image_small'); ?>" alt="<?= esc($bProd->title); ?>" style="width: 36px; height: 36px; object-fit: cover; border-radius: 4px; margin-right: 10px; border: 1px solid #e2e8f0; flex-shrink: 0;">
                                                                                        <div>
                                                                                            <a href="<?= esc($bProd->product_url); ?>" style="color: #2d3748; font-weight: 500; text-decoration: none;">
                                                                                                <?= esc($bProd->title); ?>
                                                                                            </a>
                                                                                            <?php if(!empty($bProd->sku)): ?>
                                                                                                <br><small class="text-muted" style="font-size: 11px;">SKU: <?= esc($bProd->sku); ?></small>
                                                                                            <?php endif; ?>
                                                                                        </div>
                                                                                    </div>
                                                                                </td>
                                                                                <td class="text-center" style="padding: 8px 10px; vertical-align: middle; color: #4a5568;">
                                                                                    <?php if (!empty($bProd->has_discount)): ?>
                                                                                        <del style="font-size: 11px; color: #94a3b8; margin-right: 3px;"><?= priceDecimal($bProd->original_price, $cart->currency_code); ?></del>
                                                                                    <?php endif; ?>
                                                                                    <span style="font-weight: 600;"><?= priceDecimal($bProd->unit_price, $cart->currency_code); ?></span>
                                                                                </td>
                                                                                <td class="text-center" style="padding: 8px 10px; vertical-align: middle; font-weight: 600; color: #2d3748;">
                                                                                    <?= $bProd->quantity; ?>
                                                                                </td>
                                                                                <td class="text-right font-weight-bold" style="padding: 8px 10px; vertical-align: middle; color: #2b6cb0;">
                                                                                    <?php if (!empty($bProd->has_discount)): ?>
                                                                                        <del style="font-size: 11px; color: #94a3b8; font-weight: 400; margin-right: 3px;"><?= priceDecimal($bProd->original_total_price, $cart->currency_code); ?></del>
                                                                                    <?php endif; ?>
                                                                                    <span><?= priceDecimal($bProd->total_price, $cart->currency_code); ?></span>
                                                                                </td>
                                                                            </tr>
                                                                        <?php endforeach; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php elseif(!empty($cartItem->is_bundle) && !empty($cartItem->bundle_summary)): ?>
                                                    <div class="bundle-breakdown m-t-10 m-b-10">
                                                        <div class="d-flex justify-content-between align-items-center m-b-5">
                                                            <strong style="font-size: 12px; color: #4a5568;"><i class="fa fa-cubes text-primary"></i> <?= esc($cartItem->product_title); ?> Items:</strong>
                                                            <a href="<?= esc($cartItem->product_url); ?>?cart_item_id=<?= $cartItem->id; ?>#tab_bundle_contents" class="btn btn-xs btn-outline-primary" style="font-size: 11px; padding: 2px 8px;">Customize</a>
                                                        </div>
                                                        <?php foreach($cartItem->bundle_summary as $b): ?>
                                                            <small class="text-muted"><i class="icon-arrow-right"></i> <?= esc($b) ?></small><br>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach;
                                    endif; ?>
                                </div>
                                <a href="<?= langBaseUrl(); ?>" class="btn btn-md btn-custom m-t-30"><i class="icon-arrow-left m-r-2"></i><?= trans("keep_shopping") ?></a>
                            </div>
                            <div class="col-sm-12 col-lg-4">
                                <div class="right">
                                    <div class="row-custom m-b-15">
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
                                    <?php endif; ?>
                                    <?php if (!empty($cart->coupon_code)): ?>
                                        <div class="row-custom">
                                            <strong><?= trans("coupon"); ?>&nbsp;&nbsp;[<?= esc($cart->coupon_code); ?>]&nbsp;&nbsp;<a href="javascript:void(0)" class="font-weight-normal" onclick="removeCartDiscountCoupon();">[<?= trans("remove"); ?>]</a><span class="float-right">-&nbsp;<?= priceDecimal($cart->totals->coupon_discount, $cart->currency_code); ?></span></strong>
                                        </div>
                                    <?php endif; ?>
                                    <div class="row-custom">
                                        <p class="line-seperator"></p>
                                    </div>
                                    <div class="row-custom m-b-10">
                                        <strong><?= trans("total"); ?><span class="float-right"><?= priceDecimal($cart->totals->total_before_shipping, $cart->currency_code); ?></span></strong>
                                    </div>
                                    <div class="row-custom m-t-30 m-b-15">
                                        <?php if (empty($cart->is_valid)): ?>
                                            <a href="javascript:void(0)" class="btn btn-block"><?= trans("continue_to_checkout"); ?></a>
                                        <?php else:
                                            $showCheckoutBtn = false;
                                            if (authCheck()) {
                                                $showCheckoutBtn = true;
                                            } else {
                                                if ($generalSettings->guest_checkout == 1) {
                                                    $showCheckoutBtn = true;
                                                }
                                                if ($cart->has_digital_product) {
                                                    $showCheckoutBtn = false;
                                                }
                                            }
                                            if ($showCheckoutBtn):
                                                if ($cart->has_physical_product == true && $productSettings->marketplace_shipping == 1): ?>
                                                    <a href="<?= generateUrl('cart', 'shipping'); ?>" class="btn btn-block"><?= trans("continue_to_checkout"); ?></a>
                                                <?php else: ?>
                                                    <a href="<?= generateUrl('cart', 'payment_method'); ?>" class="btn btn-block"><?= trans("continue_to_checkout"); ?></a>
                                                <?php endif;
                                            else:?>
                                                <a href="#" class="btn btn-block" data-toggle="modal" data-target="#loginModal"><?= trans("continue_to_checkout"); ?></a>
                                            <?php endif;
                                        endif; ?>
                                    </div>
                                    <div class="clearfix"></div>
                                    <hr class="m-t-30 m-b-30">
                                    <form action="<?= base_url('cart/coupon-code-post'); ?>" method="post" id="form_validate" class="m-0">
                                        <?= csrf_field(); ?>
                                        <label class="font-600"><?= trans("discount_coupon") ?></label>
                                        <div class="cart-discount-coupon">
                                            <input type="text" name="coupon_code" class="form-control form-input" value="<?= esc(old('coupon_code')); ?>" maxlength="254" placeholder="<?= trans("coupon_code") ?>" required>
                                            <button type="submit" class="btn btn-custom m-l-5"><?= trans("apply") ?></button>
                                        </div>
                                    </form>
                                    <div class="cart-coupon-error">
                                        <?php if (!empty(helperGetSession('error_coupon_code'))): ?>
                                            <div class="text-danger">
                                                <?= helperGetSession('error_coupon_code'); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="shopping-cart-empty">
                        <p><strong class="font-600"><?= trans("your_cart_is_empty"); ?></strong></p>
                        <a href="<?= langBaseUrl(); ?>" class="btn btn-lg btn-custom"><i class="icon-arrow-left"></i>&nbsp;<?= trans("shop_now"); ?></a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>