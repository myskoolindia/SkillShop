<?php
$bundleModel = new \App\Models\BundleModel();
$bundleComponents = $bundleModel->getBundleComponents($product->id, true);
$bundleMetrics = $bundleModel->calculateBundleMetrics($product->id);

$currencyCode = !empty($product->currency) ? $product->currency : 'INR';
$currencyObj = !empty(getContextValue('currencies')[$currencyCode]) ? getContextValue('currencies')[$currencyCode] : null;
$currencySymbol = !empty($currencyObj) ? $currencyObj->symbol : '₹';
$symbolDirection = !empty($currencyObj) ? $currencyObj->symbol_direction : 'left';
$spaceSymbol = (!empty($currencyObj) && $currencyObj->space_money_symbol == 1) ? ' ' : '';

// Group components by Category
$groupedComponents = [];
$editItemMap = [];
if (!empty($editingCartItem) && !empty($editingCartItem->bundle_items)) {
    $parsedEditingBundle = is_string($editingCartItem->bundle_items) ? safeJsonDecode($editingCartItem->bundle_items, true) : $editingCartItem->bundle_items;
    if (is_array($parsedEditingBundle)) {
        foreach ($parsedEditingBundle as $eb) {
            $cId = (int)($eb['comp_id'] ?? 0);
            $pId = (int)($eb['product_id'] ?? 0);
            if ($cId > 0) {
                $editItemMap['c_' . $cId] = $eb;
            }
            if ($pId > 0) {
                $editItemMap['p_' . $pId] = $eb;
            }
        }
    }
}

if (!empty($bundleComponents)) {
    foreach ($bundleComponents as $comp) {
        $catId = !empty($comp->category_id) ? (int)$comp->category_id : 0;
        $catName = !empty($comp->category_name) ? trim($comp->category_name) : 'General';
        $catKey = 'cat_' . $catId;
        if (!isset($groupedComponents[$catKey])) {
            $groupedComponents[$catKey] = [
                'id' => $catId,
                'name' => $catName,
                'items' => [],
                'total_units' => 0,
                'total_price' => 0.00
            ];
        }
        $groupedComponents[$catKey]['items'][] = $comp;
        $isOpt = !empty($comp->is_optional);
        $pkgQty = $isOpt ? max(0, (int)$comp->required_quantity) : max(1, (int)$comp->required_quantity);
        $savedConfig = $editItemMap['c_' . $comp->id] ?? ($editItemMap['p_' . $comp->component_product_id] ?? null);
        if (!empty($savedConfig) && isset($savedConfig['qty']) && (int)$savedConfig['qty'] >= ($isOpt ? 0 : 1)) {
            $pkgQty = (int)$savedConfig['qty'];
        }
        $unitPrice = !empty($comp->unit_price) ? (float)$comp->unit_price : 0.00;
        $groupedComponents[$catKey]['total_units'] += $pkgQty;
        $groupedComponents[$catKey]['total_price'] += ($unitPrice * $pkgQty);
    }
}
$totalCategoriesCount = count($groupedComponents);
?>

<div class="bundle-contents-wrapper p-3">
    <!-- Top Summary Banner -->
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3 p-3 bg-light rounded" style="border: 1px solid #e2e8f0; gap: 10px;">
        <div>
            <h5 class="font-weight-bold mb-1"><i class="fa fa-cubes text-primary"></i> Package Details</h5>
            <span class="text-muted small">
                This package contains <strong><?= count($bundleComponents); ?></strong> unique items across <strong><?= $totalCategoriesCount; ?></strong> <?= $totalCategoriesCount == 1 ? 'category' : 'categories'; ?> (Total <strong id="storefront_bundle_total_units"><?= $bundleMetrics['total_units']; ?></strong> units).
            </span>
        </div>
        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
            <span class="badge badge-primary p-2" style="font-size: 13px; font-weight: 600;">
                Items Value: <strong id="storefront_bundle_total_price"><?= priceFormatted($bundleMetrics['sum_price'], $currencyCode, true); ?></strong>
            </span>
            <span class="badge badge-success p-2" style="font-size: 13px;">
                <i class="fa fa-check-circle"></i> Package Complete & In Stock
            </span>
        </div>
    </div>

    <!-- Category Filter Pills & Search Bar -->
    <div class="bundle-filter-controls mb-3">
        <div class="row align-items-center">
            <div class="col-md-6 col-12 mb-2 mb-md-0">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white" style="border-right: none;"><i class="fa fa-search text-muted"></i></span>
                    </div>
                    <input type="text" id="bundle_storefront_filter" class="form-control" placeholder="Search products, SKU or category in this package..." onkeyup="filterStorefrontBundle();" style="border-left: none;">
                    <div class="input-group-append" id="bundle_search_clear_btn" style="display: none;">
                        <button class="btn btn-outline-secondary" type="button" onclick="clearBundleSearch();" title="Clear search">&times;</button>
                    </div>
                </div>
            </div>
            <?php if ($totalCategoriesCount > 1): ?>
                <div class="col-md-6 col-12 text-md-right">
                    <div class="d-flex align-items-center justify-content-md-end flex-wrap" style="gap: 6px;">
                        <span class="text-muted small mr-1 d-none d-lg-inline"><i class="fa fa-filter"></i> Filter:</span>
                        <button type="button" class="btn btn-sm btn-primary bundle-cat-pill active" data-cat-key="all" onclick="filterBundleCategory('all', this);">
                            All <span class="badge badge-light ml-1"><?= count($bundleComponents); ?></span>
                        </button>
                        <?php foreach ($groupedComponents as $catKey => $catGroup): ?>
                            <button type="button" class="btn btn-sm btn-outline-secondary bundle-cat-pill" data-cat-key="<?= esc($catKey); ?>" onclick="filterBundleCategory('<?= esc($catKey); ?>', this);">
                                <?= esc($catGroup['name']); ?> <span class="badge badge-secondary ml-1"><?= count($catGroup['items']); ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Category-Wise Items Table -->
    <div class="bundle-items-list table-responsive" style="max-height: 620px; overflow-y: auto; border: 1px solid #edf2f7; border-radius: 8px;">
        <table class="table table-hover mb-0" id="table_storefront_bundle">
            <thead class="thead-light" style="position: sticky; top:0; z-index:3; background:#f8f9fa;">
                <tr>
                    <th style="width: 45px;" class="text-center">#</th>
                    <th style="width: 65px;">Item</th>
                    <th>Product & Details</th>
                    <th style="width: 120px;">SKU</th>
                    <th style="width: 110px;" class="text-center">Unit Price</th>
                    <th style="width: 155px;" class="text-center">Quantity</th>
                    <th style="width: 120px;" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($groupedComponents)): ?>
                    <?php $globalCounter = 1; ?>
                    <?php foreach ($groupedComponents as $catKey => $catGroup): ?>
                        <!-- Category Header Row -->
                        <tr class="bundle-category-header-row" data-cat-key="<?= esc($catKey); ?>" data-cat-name="<?= esc(strtolower($catGroup['name'])); ?>" style="background: #f1f5f9; border-top: 2px solid #e2e8f0; border-bottom: 1px solid #cbd5e0;">
                            <td colspan="7" class="py-2 px-3">
                                <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
                                    <div class="d-flex align-items-center">
                                        <i class="fa fa-folder-open text-primary mr-2" style="font-size: 15px;"></i>
                                        <span class="font-weight-bold text-dark" style="font-size: 13.5px;"><?= esc($catGroup['name']); ?></span>
                                        <span class="badge badge-primary ml-2 px-2 py-1" style="font-size: 11px; font-weight: 500;">
                                            <?= count($catGroup['items']); ?> <?= count($catGroup['items']) == 1 ? 'item' : 'items'; ?>
                                        </span>
                                    </div>
                                    <div class="text-muted small">
                                        Category Value: <strong class="text-dark category-subtotal-price" data-cat-key="<?= esc($catKey); ?>"><?= priceFormatted($catGroup['total_price'], $currencyCode, true); ?></strong>
                                        (<span class="category-subtotal-units font-weight-bold" data-cat-key="<?= esc($catKey); ?>"><?= $catGroup['total_units']; ?></span> units)
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <!-- Category Items Rows -->
                        <?php foreach ($catGroup['items'] as $comp):
                            $isOptional = !empty($comp->is_optional);
                            $pkgMinQty = $isOptional ? 0 : max(1, (int)$comp->required_quantity);
                            $availableStock = isset($comp->available_stock) ? (int)$comp->available_stock : 9999;
                            $unitPrice = !empty($comp->unit_price) ? (float)$comp->unit_price : 0.00;
                            $displaySku = $comp->sku;
                            // If product has selectable variants, default to first variant's values
                            $firstVariantId = 0;
                            if (empty($comp->variant_id) && !empty($comp->available_variants)) {
                                $firstAv = $comp->available_variants[0];
                                $firstAvPrice = $firstAv->price_discounted > 0 ? (float)$firstAv->price_discounted : (float)$firstAv->price;
                                if ($firstAvPrice > 0) {
                                    $unitPrice = $firstAvPrice;
                                }
                                $firstVariantId = (int)$firstAv->id;
                                $displaySku = $firstAv->sku ?: $comp->sku;
                                $availableStock = ($firstAv->quantity === null || $firstAv->quantity === '') ? 9999 : (int)$firstAv->quantity;
                            }

                            $savedConfig = $editItemMap['c_' . $comp->id] ?? ($editItemMap['p_' . $comp->component_product_id] ?? null);
                            $currentQty = $isOptional ? (int)$comp->required_quantity : $pkgMinQty;
                            $selectedVarId = !empty($comp->variant_id) ? (int)$comp->variant_id : (int)$firstVariantId;

                            if (!empty($savedConfig)) {
                                if (isset($savedConfig['qty']) && (int)$savedConfig['qty'] >= $pkgMinQty) {
                                    $currentQty = (int)$savedConfig['qty'];
                                }
                                if (!empty($savedConfig['variant_id'])) {
                                    $selectedVarId = (int)$savedConfig['variant_id'];
                                }
                            }

                            if (!empty($comp->available_variants) && !empty($selectedVarId)) {
                                foreach ($comp->available_variants as $av) {
                                    if ((int)$av->id === $selectedVarId) {
                                        $avPrice = $av->price_discounted > 0 ? (float)$av->price_discounted : (float)$av->price;
                                        if ($avPrice > 0) {
                                            $unitPrice = $avPrice;
                                        }
                                        $displaySku = $av->sku ?: $comp->sku;
                                        $availableStock = ($av->quantity === null || $av->quantity === '') ? 9999 : (int)$av->quantity;
                                        break;
                                    }
                                }
                            }

                            $lineTotal = $unitPrice * $currentQty;
                        ?>
                            <tr class="storefront-bundle-row"
                                data-cat-key="<?= esc($catKey); ?>"
                                data-cat-name="<?= esc(strtolower($catGroup['name'])); ?>"
                                data-title="<?= esc(strtolower($comp->title)); ?>"
                                data-sku="<?= esc(strtolower($comp->sku)); ?>"
                                data-comp-id="<?= $comp->id; ?>"
                                data-unit-price="<?= $unitPrice; ?>">
                                <td class="text-muted text-center" style="vertical-align: middle; font-size: 12px;"><?= $globalCounter++; ?></td>
                                <td style="vertical-align: middle;">
                                    <?php if (!empty($comp->image_small)):
                                        $compImgUrl = (str_starts_with($comp->image_small, 'http://') || str_starts_with($comp->image_small, 'https://')) ? $comp->image_small : (str_starts_with($comp->image_small, 'uploads/') ? base_url($comp->image_small) : base_url('uploads/images/' . $comp->image_small));
                                    ?>
                                        <img src="<?= $compImgUrl; ?>" alt="<?= esc($comp->title); ?>" style="width: 45px; height: 45px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0;">
                                    <?php else: ?>
                                        <div style="width: 45px; height: 45px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; display:flex; align-items:center; justify-content:center; color:#a0aec0;">
                                            <i class="fa fa-cube" style="font-size: 18px;"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td style="vertical-align: middle;">
                                    <div class="font-weight-bold" style="font-size: 13.5px;">
                                        <a href="<?= generateProductUrl($comp); ?>" target="_blank" class="text-dark hover-primary" style="text-decoration:none;"><?= esc($comp->title); ?></a>
                                    </div>
                                    <div class="d-flex align-items-center flex-wrap mt-1" style="gap: 5px;">
                                        <span class="badge badge-light text-secondary border px-2 py-1" style="font-size: 10.5px;">
                                            <i class="fa fa-folder-o mr-1"></i><?= esc($catGroup['name']); ?>
                                        </span>
                                        <?php if (!empty($comp->is_optional)): ?>
                                            <span class="badge badge-warning text-dark border px-2 py-1" style="background:#fef3c7; color:#92400e !important; border-color:#fde68a !important; font-size: 10.5px;" title="Optional item (starts with 0 qty)">
                                                <i class="fa fa-plus-circle mr-1"></i>Optional
                                            </span>
                                        <?php endif; ?>
                                        <?php if (!empty($comp->variant_name)): ?>
                                            <!-- Pre-assigned specific variant -->
                                            <span class="badge badge-secondary px-2 py-1" style="font-size: 10.5px;">
                                                <i class="fa fa-tag mr-1"></i><?= esc($comp->variant_name); ?>
                                            </span>
                                        <?php elseif (!empty($comp->available_variants)): ?>
                                            <!-- Show all available variants as selectable pills -->
                                            <div class="mt-1 w-100">
                                                <span class="text-muted" style="font-size: 10px; display: block; margin-bottom: 3px;">
                                                    <i class="fa fa-tags mr-1"></i>Available variants:
                                                </span>
                                                <div class="d-flex flex-wrap" style="gap: 4px;">
                                                    <?php foreach ($comp->available_variants as $avIdx => $av):
                                                        $avPrice = $av->price_discounted > 0 ? (float)$av->price_discounted : (float)$av->price;
                                                        $avStock = ($av->quantity === null || $av->quantity === '') ? 9999 : (int)$av->quantity;
                                                        $isSelected = ((int)$av->id === (int)$selectedVarId) || (empty($selectedVarId) && $avIdx === 0);
                                                    ?>
                                                        <span class="bundle-variant-pill <?= $isSelected ? 'selected' : ''; ?>"
                                                              data-comp-id="<?= $comp->id; ?>"
                                                              data-variant-id="<?= (int)$av->id; ?>"
                                                              data-sku="<?= esc($av->sku); ?>"
                                                              data-price="<?= $avPrice; ?>"
                                                              data-stock="<?= $avStock; ?>"
                                                              onclick="selectBundleVariant(this)"
                                                              title="SKU: <?= esc($av->sku); ?> | Stock: <?= $avStock; ?>"
                                                              style="font-size: 10px; padding: 3px 8px; border-radius: 4px; white-space: nowrap; cursor: pointer; border: 1px solid <?= $isSelected ? '#6366f1' : '#c7d2fe'; ?>; background: <?= $isSelected ? '#6366f1' : '#eef2ff'; ?>; color: <?= $isSelected ? '#fff' : '#3730a3'; ?>; user-select: none;">
                                                            <?= esc($av->variant_name); ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td style="vertical-align: middle;"><code class="bundle-row-sku"><?= esc($displaySku); ?></code></td>
                                <td class="text-center font-weight-bold text-nowrap bundle-unit-price-cell" style="color: #4a5568; vertical-align: middle;">
                                    <?= priceFormatted($unitPrice, $currencyCode, true); ?>
                                </td>
                                <td class="text-center" style="vertical-align: middle;">
                                    <div class="bundle-qty-spinner" style="display:inline-flex; align-items:center; justify-content:center;">
                                        <div class="input-group input-group-sm" style="width: 125px;">
                                            <div class="input-group-prepend">
                                                <button type="button" class="btn btn-outline-secondary btn-bundle-minus" onclick="changeBundleStorefrontQty(this, -1);" style="border-top-right-radius:0; border-bottom-right-radius:0; padding: 2px 8px;" title="<?= !empty($comp->is_optional) ? 'Minimum: 0' : ('Cannot be lower than package minimum (' . $pkgMinQty . ')'); ?>">
                                                    <i class="fa fa-minus" style="font-size:10px;"></i>
                                                </button>
                                            </div>
                                            <input type="number"
                                                   class="form-control form-control-sm text-center font-weight-bold bundle-storefront-qty-input"
                                                   value="<?= $currentQty; ?>"
                                                   min="<?= $pkgMinQty; ?>"
                                                   max="<?= $availableStock > 0 ? $availableStock : 9999; ?>"
                                                   data-min="<?= $pkgMinQty; ?>"
                                                   data-pkg-min="<?= $pkgMinQty; ?>"
                                                   data-is-optional="<?= !empty($comp->is_optional) ? '1' : '0'; ?>"
                                                   data-unit-price="<?= $unitPrice; ?>"
                                                   data-comp-id="<?= $comp->id; ?>"
                                                   data-cat-key="<?= esc($catKey); ?>"
                                                   data-product-id="<?= $comp->component_product_id; ?>"
                                                   data-variant-id="<?= !empty($comp->variant_id) ? $comp->variant_id : $selectedVarId; ?>"
                                                   oninput="validateBundleStorefrontQty(this);"
                                                   onchange="validateBundleStorefrontQty(this);"
                                                   style="font-size:13px; font-weight:bold; padding:2px 4px;">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary btn-bundle-plus" onclick="changeBundleStorefrontQty(this, 1);" style="border-top-left-radius:0; border-bottom-left-radius:0; padding: 2px 8px;">
                                                    <i class="fa fa-plus" style="font-size:10px;"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-1">
                                        <small class="text-muted d-block" style="font-size: 11px;">
                                            <?php if (!empty($comp->is_optional)): ?>
                                                <i class="fa fa-info-circle" style="font-size: 9px; color: #f59e0b;"></i> Optional (0 qty by default)
                                            <?php else: ?>
                                                <i class="fa fa-lock" style="font-size: 9px;"></i> Min in pkg: <strong><?= $pkgMinQty; ?></strong>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </td>
                                <td class="text-right font-weight-bold text-primary text-nowrap bundle-item-total" id="bundle_item_total_<?= $comp->id; ?>" style="font-size: 14px; vertical-align: middle;">
                                    <?= priceFormatted($lineTotal, $currencyCode, true); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center p-4 text-muted">No component items found in this package.</td>
                    </tr>
                <?php endif; ?>
                <tr id="bundle_storefront_no_match" style="display: none;">
                    <td colspan="7" class="text-center p-4 text-muted">
                        <i class="fa fa-search fa-2x mb-2 text-muted"></i><br>
                        No package items matching your search.
                    </td>
                </tr>
            </tbody>
            <?php if (!empty($bundleComponents)): ?>
                <tfoot style="background: #f8fafc; border-top: 2px solid #e2e8f0; position: sticky; bottom: 0; z-index: 2;">
                    <tr>
                        <th colspan="4" class="text-right font-weight-bold">Package Total:</th>
                        <th class="text-center font-weight-bold text-muted small">—</th>
                        <th class="text-center font-weight-bold">
                            <span id="storefront_footer_total_units" class="badge badge-info px-2 py-1" style="font-size:12px;"><?= $bundleMetrics['total_units']; ?> units</span>
                        </th>
                        <th class="text-right font-weight-bold text-primary" style="font-size:15px;" id="storefront_footer_total_price">
                            <?= priceFormatted($bundleMetrics['sum_price'], $currencyCode, true); ?>
                        </th>
                    </tr>
                </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>

<script>
var activeBundleCategoryKey = 'all';

function formatBundleCurrency(amount) {
    var formatted = parseFloat(amount).toFixed(2);
    if (formatted.endsWith('.00')) {
        formatted = formatted.substring(0, formatted.length - 3);
    }
    var parts = formatted.split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    formatted = parts.join('.');

    var symbol = '<?= escJs($currencySymbol); ?>';
    var dir = '<?= escJs($symbolDirection); ?>';
    var space = '<?= $spaceSymbol ? " " : ""; ?>';

    return dir === 'left' ? (symbol + space + formatted) : (formatted + space + symbol);
}

function filterBundleCategory(catKey, btn) {
    activeBundleCategoryKey = catKey;
    $('.bundle-cat-pill').removeClass('active btn-primary').addClass('btn-outline-secondary');
    $(btn).removeClass('btn-outline-secondary').addClass('active btn-primary');
    applyBundleStorefrontFilters();
}

function clearBundleSearch() {
    $('#bundle_storefront_filter').val('');
    applyBundleStorefrontFilters();
}

function filterStorefrontBundle() {
    applyBundleStorefrontFilters();
}

function applyBundleStorefrontFilters() {
    var query = ($('#bundle_storefront_filter').val() || '').toLowerCase().trim();
    if (query.length > 0) {
        $('#bundle_search_clear_btn').show();
    } else {
        $('#bundle_search_clear_btn').hide();
    }

    var totalVisibleRows = 0;
    var categoryVisibleCount = {};

    // Filter each item row
    $('#table_storefront_bundle tbody tr.storefront-bundle-row').each(function() {
        var $row = $(this);
        var rowCatKey = $row.attr('data-cat-key') || '';
        var title = $row.attr('data-title') || '';
        var sku = $row.attr('data-sku') || '';
        var catName = $row.attr('data-cat-name') || '';

        var categoryMatch = (activeBundleCategoryKey === 'all' || rowCatKey === activeBundleCategoryKey);
        var searchMatch = (query === '' || title.indexOf(query) > -1 || sku.indexOf(query) > -1 || catName.indexOf(query) > -1);

        if (categoryMatch && searchMatch) {
            $row.show();
            totalVisibleRows++;
            categoryVisibleCount[rowCatKey] = (categoryVisibleCount[rowCatKey] || 0) + 1;
        } else {
            $row.hide();
        }
    });

    // Update Category Header rows visibility
    $('#table_storefront_bundle tbody tr.bundle-category-header-row').each(function() {
        var $catHeader = $(this);
        var headerCatKey = $catHeader.attr('data-cat-key') || '';
        if (categoryVisibleCount[headerCatKey] && categoryVisibleCount[headerCatKey] > 0) {
            $catHeader.show();
        } else {
            $catHeader.hide();
        }
    });

    // Show empty state if no rows match
    if (totalVisibleRows === 0 && $('#table_storefront_bundle tbody tr.storefront-bundle-row').length > 0) {
        $('#bundle_storefront_no_match').show();
    } else {
        $('#bundle_storefront_no_match').hide();
    }
}

function changeBundleStorefrontQty(btn, delta) {
    var $input = $(btn).closest('.bundle-qty-spinner').find('.bundle-storefront-qty-input');
    var current = parseInt($input.val());
    if (isNaN(current)) current = 0;
    var min = parseInt($input.data('min'));
    if (isNaN(min)) min = 0;
    var max = parseInt($input.attr('max')) || 9999;
    var nextVal = current + delta;

    if (nextVal < min) {
        nextVal = min;
    }
    if (nextVal > max) {
        nextVal = max;
    }

    $input.val(nextVal);
    validateBundleStorefrontQty($input[0]);
}

function validateBundleStorefrontQty(input) {
    var $input = $(input);
    var min = parseInt($input.data('min'));
    if (isNaN(min)) min = 0;
    var max = parseInt($input.attr('max')) || 9999;
    var val = parseInt($input.val());

    if (isNaN(val) || val < min) {
        val = min;
        $input.val(min);
    } else if (val > max) {
        val = max;
        $input.val(max);
    }

    // Update minus button disabled state
    var $minusBtn = $input.closest('.bundle-qty-spinner').find('.btn-bundle-minus');
    if (val <= min) {
        $minusBtn.attr('disabled', true).addClass('disabled').css('opacity', '0.5');
    } else {
        $minusBtn.removeAttr('disabled').removeClass('disabled').css('opacity', '1');
    }

    // Update row total
    var unitPrice = parseFloat($input.data('unit-price')) || 0;
    var rowTotal = unitPrice * val;
    var $row = $input.closest('tr.storefront-bundle-row');
    $row.find('.bundle-item-total').text(formatBundleCurrency(rowTotal));

    // Recalculate category subtotal & overall totals
    recalculateStorefrontBundleTotals();
}

function recalculateStorefrontBundleTotals() {
    var totalUnits = 0;
    var totalPrice = 0;
    var categoryTotals = {};
    var bundleItemsData = [];

    $('.bundle-storefront-qty-input').each(function() {
        var $this = $(this);
        var minVal = parseInt($this.data('min'));
        if (isNaN(minVal)) minVal = 0;
        var q = parseInt($this.val());
        if (isNaN(q) || q < minVal) {
            q = minVal;
        }
        var unitPrice = parseFloat($this.data('unit-price')) || 0;
        var catKey = $this.data('cat-key');
        var itemTotal = unitPrice * q;

        totalUnits += q;
        totalPrice += itemTotal;

        if (catKey) {
            if (!categoryTotals[catKey]) {
                categoryTotals[catKey] = { units: 0, price: 0 };
            }
            categoryTotals[catKey].units += q;
            categoryTotals[catKey].price += itemTotal;
        }

        bundleItemsData.push({
            comp_id: $this.data('comp-id'),
            product_id: $this.data('product-id'),
            variant_id: $this.data('variant-id') || 0,
            qty: q,
            unit_price: unitPrice
        });
    });

    // Update category-level subtotal headers
    for (var key in categoryTotals) {
        $('.category-subtotal-price[data-cat-key="' + key + '"]').text(formatBundleCurrency(categoryTotals[key].price));
        $('.category-subtotal-units[data-cat-key="' + key + '"]').text(categoryTotals[key].units);
    }

    // Update overall package details totals
    $('#storefront_bundle_total_units').text(totalUnits);
    $('#storefront_footer_total_units').text(totalUnits + ' units');

    var formattedPrice = formatBundleCurrency(totalPrice);
    $('#storefront_bundle_total_price').text(formattedPrice);
    $('#storefront_footer_total_price').text(formattedPrice);

    // Update main product price container on the page
    var mainQty = parseInt($('#input_product_quantity').val()) || 1;
    var totalActualPrice = totalPrice * mainQty;
    var bundleDiscountRate = <?= !empty($product->bundle_discount_rate) ? (float)$product->bundle_discount_rate : (!empty($product->discount_rate) ? (float)$product->discount_rate : 0); ?>;

    if (bundleDiscountRate > 0) {
        var discountedActualPrice = totalActualPrice * (1 - (bundleDiscountRate / 100));
        var formattedDiscountedPrice = formatBundleCurrency(discountedActualPrice);
        var formattedOrigPrice = formatBundleCurrency(totalActualPrice);

        $('#div-product-discounted-price .final-price, .product-price-container .final-price').text(formattedDiscountedPrice);
        $('#div-product-discounted-price').addClass('text-product-discounted').show();

        $('#div-product-price .original-price').text(formattedOrigPrice);
        $('#div-product-price').show();

        $('#div-product-discount-rate .discount-rate').text('-' + Math.round(bundleDiscountRate) + '%');
        $('#div-product-discount-rate').show();
    } else {
        var formattedMainPrice = formatBundleCurrency(totalActualPrice);
        $('#div-product-discounted-price .final-price, .product-price-container .final-price').text(formattedMainPrice);
        $('#div-product-discounted-price').removeClass('text-product-discounted').show();
        $('#div-product-price').hide();
        $('#div-product-discount-rate').hide();
    }

    // Sync hidden inputs in the Add-to-Cart form with actual modified quantities and price
    var bundleJson = JSON.stringify(bundleItemsData);
    if ($('#hidden_bundle_components_data').length === 0) {
        $('#form-add-to-cart').append('<input type="hidden" name="bundle_components_data" id="hidden_bundle_components_data">');
    }
    if ($('#hidden_bundle_total_price').length === 0) {
        $('#form-add-to-cart').append('<input type="hidden" name="bundle_total_price" id="hidden_bundle_total_price">');
    }
    $('#hidden_bundle_components_data').val(bundleJson);
    $('#hidden_bundle_total_price').val(totalPrice.toFixed(2));
}

$(document).ready(function() {
    $('.bundle-storefront-qty-input').each(function() {
        validateBundleStorefrontQty(this);
    });

    recalculateStorefrontBundleTotals();
    setTimeout(recalculateStorefrontBundleTotals, 100);
    setTimeout(recalculateStorefrontBundleTotals, 500);

    // Also update main price when main quantity spinner changes
    $(document).on('input keyup paste change', '#input_product_quantity', function() {
        recalculateStorefrontBundleTotals();
    });

    $(document).on('click', '.product-add-to-cart-container .number-spinner button, .btn-spinner-minus, .btn-spinner-plus', function() {
        setTimeout(function() {
            recalculateStorefrontBundleTotals();
        }, 50);
    });

    if (window.location.hash === '#tab_bundle_contents' || window.location.hash === '#tab_bundle_contents_content') {
        $('#tab_bundle_contents').tab('show');
        setTimeout(function() {
            if ($('#tab_bundle_contents_content').length) {
                $('html, body').animate({scrollTop: $('#tab_bundle_contents_content').offset().top - 120}, 400);
            }
        }, 200);
    }
});

function selectBundleVariant(pill) {
    var $pill    = $(pill);
    var newPrice = parseFloat($pill.data('price'))      || 0;
    var newStock = parseInt($pill.data('stock'))        || 9999;
    var newSku   = $pill.data('sku')                    || '';
    var newVarId = parseInt($pill.data('variant-id'))   || 0;

    // Swap active pill styling for this row only
    $pill.closest('.d-flex.flex-wrap').find('.bundle-variant-pill').each(function() {
        $(this).css({ background: '#eef2ff', color: '#3730a3', 'border-color': '#c7d2fe' })
               .removeClass('selected');
    });
    $pill.css({ background: '#6366f1', color: '#fff', 'border-color': '#6366f1' })
         .addClass('selected');

    // Locate the qty input for this row
    var $row   = $pill.closest('tr.storefront-bundle-row');
    var $input = $row.find('.bundle-storefront-qty-input');
    var minQty = parseInt($input.data('pkg-min')) || 1;
    var maxQty = newStock > 0 ? newStock : 9999;

    // Update qty input meta
    $input.attr('max', maxQty)
          .data('unit-price', newPrice)
          .data('variant-id', newVarId);

    // Clamp qty to new stock limit
    if (parseInt($input.val()) > maxQty) {
        $input.val(maxQty);
    }

    // Update SKU cell
    $row.find('.bundle-row-sku').text(newSku);

    // Update unit price cell
    $row.find('td.bundle-unit-price-cell').text(formatBundleCurrency(newPrice));

    // Trigger row total + footer recalc
    validateBundleStorefrontQty($input[0]);
}
</script>
