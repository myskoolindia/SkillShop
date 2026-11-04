<div class="product-price-container">
    <?php
    // Accept both naming conventions:
    // - controller-prepared: $productPrice / $productPriceDiscounted / $productDiscountRate
    // - direct inline calls:  $price / $priceDiscounted / $discountRate
    $productPrice          = $productPrice          ?? $price          ?? '';
    $productPriceDiscounted = $productPriceDiscounted ?? $priceDiscounted ?? $productPrice;
    $productDiscountRate   = $productDiscountRate   ?? $discountRate   ?? 0;

    $productDiscountAmount = 0.0;
    if (isset($product->discount_amount) && (float)$product->discount_amount > 0) {
        $productDiscountAmount = (float)$product->discount_amount;
    } elseif (!empty($product->price) && !empty($product->price_discounted) && (float)$product->price > (float)$product->price_discounted) {
        $productDiscountAmount = (float)$product->price - (float)$product->price_discounted;
    }
    ?>
    <?php if ($product->is_free_product): ?>
        <div id="div-product-price" class="text-product-discounted">
            <span class="final-price final-price-free"><?= trans("free"); ?></span>
        </div>
    <?php elseif ($product->listing_type == 'ordinary_listing' && $product->is_sold): ?>
        <div id="div-product-price">
            <span class="final-price text-muted"><?= trans("sold"); ?></span>
        </div>
    <?php else:
        if (!empty($productPrice)):?>
            <div id="div-product-price" style="<?= $productDiscountRate > 0 ? 'display:inline-block; margin-right:8px;' : 'display:none;'; ?>">
                <del class="original-price" style="font-size:18px; color:#868e96; text-decoration:line-through;"><?= $productPrice; ?></del>
            </div>
            <div id="div-product-discounted-price" class="<?= $productDiscountRate > 0 ? 'text-product-discounted' : ''; ?>" style="display:inline-block;">
                <span class="final-price"><?= $productPriceDiscounted; ?></span>
            </div>
            <div id="div-product-discount-rate" style="<?= $productDiscountRate > 0 ? 'display:inline-block; margin-left:8px;' : 'display:none;'; ?>">
                <span class="discount-rate">-<?= discountRateFormat($productDiscountRate); ?></span>
            </div>
            <div id="div-product-discount-amount" style="<?= ($productDiscountRate > 0 && $productDiscountAmount > 0) ? 'display:inline-block; margin-left:8px;' : 'display:none;'; ?>">
                <span class="badge badge-success" style="background:#dcfce7; color:#15803d; font-size:12px; font-weight:700; border:1px solid #bbf7d0; padding:3px 8px; border-radius:4px; vertical-align:middle;">
                    Save <span class="discount-amount-val"><?= priceFormatted($productDiscountAmount, $product->currency); ?></span>
                </span>
            </div>
        <?php endif;
    endif; ?>
</div>
