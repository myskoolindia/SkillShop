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
        $normalizedName = preg_replace('/[^a-zA-Z0-9_]/', '_', strtolower($catName));
        $catKey = 'cat_' . $normalizedName;
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
        $pkgQty = $isOpt ? 0 : max(1, (int)$comp->required_quantity);
        $savedConfig = $editItemMap['c_' . $comp->id] ?? ($editItemMap['p_' . $comp->component_product_id] ?? null);
        if (!empty($savedConfig) && isset($savedConfig['qty']) && (int)$savedConfig['qty'] >= ($isOpt ? 0 : 1)) {
            $pkgQty = (int)$savedConfig['qty'];
        }
        $unitPrice = !empty($comp->unit_price) ? (float)$comp->unit_price : 0.00;
        $groupedComponents[$catKey]['total_units'] += $pkgQty;
        $groupedComponents[$catKey]['total_price'] += ($unitPrice * $pkgQty);
    }

    // 1. Sort Categories Alphabetically
    uasort($groupedComponents, function($a, $b) {
        return strcasecmp($a['name'] ?? '', $b['name'] ?? '');
    });

    // 2. Sort Items Inside Each Category by Checked (Mandatory/Selected first) then Alphabetically
    foreach ($groupedComponents as $catKey => &$catGroup) {
        usort($catGroup['items'], function($a, $b) use ($editItemMap) {
            $aOpt = !empty($a->is_optional);
            $bOpt = !empty($b->is_optional);

            $aQty = $aOpt ? 0 : max(1, (int)$a->required_quantity);
            $aSaved = $editItemMap['c_' . $a->id] ?? ($editItemMap['p_' . $a->component_product_id] ?? null);
            if (!empty($aSaved) && isset($aSaved['qty'])) {
                $aQty = (int)$aSaved['qty'];
            }
            $aChecked = (!$aOpt || $aQty > 0) ? 1 : 0;

            $bQty = $bOpt ? 0 : max(1, (int)$b->required_quantity);
            $bSaved = $editItemMap['c_' . $b->id] ?? ($editItemMap['p_' . $b->component_product_id] ?? null);
            if (!empty($bSaved) && isset($bSaved['qty'])) {
                $bQty = (int)$bSaved['qty'];
            }
            $bChecked = (!$bOpt || $bQty > 0) ? 1 : 0;

            if ($aChecked !== $bChecked) {
                return $bChecked - $aChecked; // Checked (1) comes before Unchecked (0)
            }

            return strcasecmp($a->title ?? '', $b->title ?? '');
        });
    }
    unset($catGroup);
    $initialGrandTotalUnits = 0;
    $initialGrandTotalPrice = 0.00;
    foreach ($groupedComponents as $catGroup) {
        $initialGrandTotalUnits += (int)($catGroup['total_units'] ?? 0);
        $initialGrandTotalPrice += (float)($catGroup['total_price'] ?? 0.0);
    }
}
$totalCategoriesCount = count($groupedComponents);
?>

<style>
.bundle-category-header-row {
    cursor: pointer;
    user-select: none;
    transition: background-color 0.15s ease;
}
.bundle-category-header-row:hover {
    background: #e2e8f0 !important;
}
.bundle-cat-chevron {
    transition: transform 0.25s ease;
    display: inline-block;
}
.bundle-cat-chevron.collapsed {
    transform: rotate(-90deg);
}
.bundle-row-disabled {
    background: #fafafa !important;
    opacity: 0.65;
    transition: opacity 0.2s ease, background-color 0.2s ease;
}
.bundle-row-disabled:hover {
    opacity: 0.9;
}
.bundle-item-checkbox {
    cursor: pointer;
    width: 18px !important;
    height: 18px !important;
    accent-color: #2563eb;
    display: inline-block !important;
    opacity: 1 !important;
    visibility: visible !important;
    vertical-align: middle;
    margin: 0 auto;
}
.bundle-item-checkbox[disabled] {
    cursor: not-allowed;
    accent-color: #16a34a;
    opacity: 0.95 !important;
}
</style>

<div class="bundle-contents-wrapper p-3">
    <!-- Category Filter Pills & Search Bar -->
    <div class="bundle-filter-controls mb-3">
        <div class="row align-items-center" style="row-gap: 10px;">
            <div class="col-lg-4 col-md-5 col-12">
                <div class="input-group" style="box-shadow: 0 1px 2px rgba(0,0,0,0.04); border-radius: 6px; overflow: hidden;">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white" style="border-right: none; border-color: #cbd5e1;"><i class="fa fa-search text-muted"></i></span>
                    </div>
                    <input type="text" id="bundle_storefront_filter" class="form-control" placeholder="Search products, SKU or category..." onkeyup="filterStorefrontBundle();" style="border-left: none; border-color: #cbd5e1; font-size: 13.5px;">
                    <div class="input-group-append" id="bundle_search_clear_btn" style="display: none;">
                        <button class="btn btn-outline-secondary" type="button" onclick="clearBundleSearch();" title="Clear search" style="border-color: #cbd5e1;">&times;</button>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-8 col-md-7 col-12">
                <div class="d-flex align-items-center justify-content-md-end flex-wrap" style="gap: 6px;">
                    <?php if ($totalCategoriesCount > 1): ?>
                        <button type="button" class="btn btn-sm btn-primary bundle-cat-pill active" data-cat-key="all" onclick="filterBundleCategory('all', this);" style="border-radius: 6px; font-weight: 600; padding: 5px 12px; font-size: 12.5px;">
                            All <span class="badge badge-light ml-1" style="color: #2563eb; background: #ffffff;"><?= count($bundleComponents); ?></span>
                        </button>
                        <?php foreach ($groupedComponents as $catKey => $catGroup): ?>
                            <button type="button" class="btn btn-sm btn-outline-secondary bundle-cat-pill" data-cat-key="<?= esc($catKey); ?>" onclick="filterBundleCategory('<?= esc($catKey); ?>', this);" style="border-radius: 6px; font-weight: 500; padding: 5px 12px; font-size: 12.5px; background: #ffffff; border-color: #cbd5e1; color: #334155;">
                                <?= esc($catGroup['name']); ?> <span class="badge badge-secondary ml-1" style="background: #e2e8f0; color: #475569;"><?= count($catGroup['items']); ?></span>
                            </button>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Category-Wise Items Table -->
    <div class="bundle-items-list table-responsive" style="max-height: 620px; overflow-y: auto; border: 1px solid #edf2f7; border-radius: 8px;">
        <table class="table table-hover mb-0" id="table_storefront_bundle">
            <thead class="thead-light" style="position: sticky; top:0; z-index:3; background:#f8f9fa;">
                <tr>
                    <th style="width: 40px;" class="text-center">#</th>
                    <th style="width: 65px;" class="text-center">Select</th>
                    <th style="width: 60px;">Item</th>
                    <th>Product & Details</th>
                    <th style="width: 110px;">SKU</th>
                    <th style="width: 105px;" class="text-center">Unit Price</th>
                    <th style="width: 145px;" class="text-center">Quantity</th>
                    <th style="width: 115px;" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($groupedComponents)): ?>
                    <?php $globalCounter = 1; ?>
                    <?php foreach ($groupedComponents as $catKey => $catGroup): ?>
                        <!-- Category Header Row (Collapsible) -->
                        <tr class="bundle-category-header-row" id="bundle_cat_row_<?= esc($catKey); ?>" data-cat-key="<?= esc($catKey); ?>" data-cat-id="<?= esc($catGroup['id'] ?? ''); ?>" data-cat-name="<?= esc(strtolower($catGroup['name'])); ?>" onclick="toggleBundleCategory('<?= esc($catKey); ?>');" style="background: #f1f5f9; border-top: 2px solid #e2e8f0; border-bottom: 1px solid #cbd5e0; cursor: pointer;" title="Click to collapse / expand this category">
                            <td colspan="8" class="py-2 px-3">
                                <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
                                    <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
                                        <i class="fa fa-chevron-down bundle-cat-chevron text-muted mr-1 collapsed" data-cat-key="<?= esc($catKey); ?>" style="font-size: 11px;"></i>
                                        <i class="fa fa-folder bundle-cat-folder text-primary mr-1" data-cat-key="<?= esc($catKey); ?>" style="font-size: 15px;"></i>
                                        <span class="font-weight-bold text-dark bundle-cat-title" style="font-size: 14px;"><?= esc($catGroup['name']); ?></span>
                                        <span class="font-weight-bold text-primary ml-1 bundle-cat-price-wrapper" style="font-size: 13.5px;">
                                            (<span class="category-subtotal-price" data-cat-key="<?= esc($catKey); ?>"><?= priceFormatted($catGroup['total_price'], $currencyCode, true); ?></span>)
                                        </span>
                                        <span class="badge badge-secondary ml-2 px-2 py-1" style="font-size: 11px; font-weight: 500;">
                                            <span class="category-subtotal-units" data-cat-key="<?= esc($catKey); ?>"><?= $catGroup['total_units']; ?></span> units / <?= count($catGroup['items']); ?> items
                                        </span>
                                        <span class="text-muted small ml-2 d-none d-sm-inline" style="font-size: 11px; opacity: 0.65;">
                                            (Click to collapse/expand)
                                        </span>
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
                            $currentQty = $isOptional ? 0 : $pkgMinQty;
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
                            $isItemChecked = !$isOptional || ($currentQty > 0);
                        ?>
                            <tr class="storefront-bundle-row <?= (!$isItemChecked) ? 'bundle-row-disabled' : ''; ?>"
                                data-cat-key="<?= esc($catKey); ?>"
                                data-cat-name="<?= esc(strtolower($catGroup['name'])); ?>"
                                data-title="<?= esc(strtolower($comp->title)); ?>"
                                data-sku="<?= esc(strtolower($comp->sku)); ?>"
                                data-comp-id="<?= $comp->id; ?>"
                                data-unit-price="<?= $unitPrice; ?>">
                                <td class="text-muted text-center" style="vertical-align: middle; font-size: 12px;"><?= $globalCounter++; ?></td>
                                
                                <!-- Checkbox Column (Mandatory vs Optional) -->
                                <td class="text-center" style="padding: 8px 6px; vertical-align: middle;">
                                    <?php if (!$isOptional): ?>
                                        <input type="checkbox" class="bundle-item-checkbox" id="chk_comp_<?= $comp->id; ?>" checked disabled data-mandatory="1" data-comp-id="<?= $comp->id; ?>" title="Mandatory item (included by default)">
                                    <?php else: ?>
                                        <input type="checkbox" class="bundle-item-checkbox" id="chk_comp_<?= $comp->id; ?>" <?= $isItemChecked ? 'checked' : ''; ?> data-mandatory="0" data-comp-id="<?= $comp->id; ?>" onchange="toggleBundleOptionalItem(this);" title="Toggle optional item (0 qty when unchecked, 1 qty when checked)">
                                    <?php endif; ?>
                                </td>

                                <td style="vertical-align: middle;">
                                    <a href="javascript:void(0);" onclick="openBundleProductQuickView(<?= $comp->id; ?>);" style="cursor: pointer; display: block;" title="View Details: <?= esc($comp->title); ?>">
                                        <?php if (!empty($comp->image_small)):
                                            $compImgUrl = (str_starts_with($comp->image_small, 'http://') || str_starts_with($comp->image_small, 'https://')) ? $comp->image_small : (str_starts_with($comp->image_small, 'uploads/') ? base_url($comp->image_small) : base_url('uploads/images/' . $comp->image_small));
                                        ?>
                                            <img src="<?= $compImgUrl; ?>" alt="<?= esc($comp->title); ?>" style="width: 45px; height: 45px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0; transition: transform 0.15s ease;">
                                        <?php else: ?>
                                            <div style="width: 45px; height: 45px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; display:flex; align-items:center; justify-content:center; color:#a0aec0;">
                                                <i class="fa fa-cube" style="font-size: 18px;"></i>
                                            </div>
                                        <?php endif; ?>
                                    </a>
                                </td>
                                <td style="vertical-align: middle;">
                                    <div class="font-weight-bold" style="font-size: 13.5px;">
                                        <a href="javascript:void(0);" onclick="openBundleProductQuickView(<?= $comp->id; ?>);" class="text-dark hover-primary" style="text-decoration:none;" title="View Details: <?= esc($comp->title); ?>">
                                            <?= esc($comp->title); ?>
                                            <i class="fa fa-info-circle text-muted ml-1" style="font-size: 11px; opacity: 0.65;"></i>
                                        </a>
                                    </div>
                                    <div class="d-flex align-items-center flex-wrap mt-1" style="gap: 5px;">
                                        <span class="badge badge-light text-secondary border px-2 py-1" style="font-size: 10.5px;">
                                            <i class="fa fa-folder-o mr-1"></i><?= esc($catGroup['name']); ?>
                                        </span>
                                        <?php if (!empty($comp->is_optional)): ?>
                                            <span class="badge badge-warning text-dark border px-2 py-1" style="background:#fef3c7; color:#92400e !important; border-color:#fde68a !important; font-size: 10.5px;" title="Optional item (0 qty by default, check to select)">
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
                                        <div class="input-group input-group-sm" style="width: 120px;">
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
                                                <i class="fa fa-info-circle" style="font-size: 9px; color: #f59e0b;"></i> Optional (0 qty default)
                                            <?php else: ?>
                                                <i class="fa fa-lock" style="font-size: 9px; color: #16a34a;"></i> Min in pkg: <strong><?= $pkgMinQty; ?></strong>
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
                        <td colspan="8" class="text-center p-4 text-muted">No component items found in this package.</td>
                    </tr>
                <?php endif; ?>
                <tr id="bundle_storefront_no_match" style="display: none;">
                    <td colspan="8" class="text-center p-4 text-muted">
                        <i class="fa fa-search fa-2x mb-2 text-muted"></i><br>
                        No package items matching your search.
                    </td>
                </tr>
            </tbody>
            <?php if (!empty($bundleComponents)): ?>
                <tfoot style="background: #f8fafc; border-top: 2px solid #e2e8f0; position: sticky; bottom: 0; z-index: 2;">
                    <!-- Active Category Subtotal row (shown when filtering by category) -->
                    <tr id="storefront_footer_cat_row" style="display: none; background: #f0f9ff; border-bottom: 1px dashed #cbd5e1;">
                        <th colspan="5" class="text-right font-weight-bold text-primary" style="font-size: 13px;">
                            <i class="fa fa-folder-open-o mr-1"></i><span id="storefront_footer_cat_name">Category</span> Subtotal:
                        </th>
                        <th class="text-center font-weight-bold text-muted small">—</th>
                        <th class="text-center font-weight-bold">
                            <span id="storefront_footer_cat_units" class="badge badge-primary px-2 py-1" style="font-size:11.5px;">0 units</span>
                        </th>
                        <th class="text-right font-weight-bold text-primary" style="font-size:14px;" id="storefront_footer_cat_price">
                            <?= priceFormatted(0, $currencyCode, true); ?>
                        </th>
                    </tr>
                    <!-- Package Grand Total row -->
                    <tr>
                        <th colspan="5" class="text-right font-weight-bold" style="font-size: 13.5px;">
                            <span id="storefront_footer_pkg_label">Package Total<?= $totalCategoriesCount > 1 ? ' (' . $totalCategoriesCount . ' Categories)' : ''; ?>:</span>
                        </th>
                        <th class="text-center font-weight-bold text-muted small">—</th>
                        <th class="text-center font-weight-bold">
                            <span id="storefront_footer_total_units" class="badge badge-info px-2 py-1" style="font-size:12px;"><?= $initialGrandTotalUnits ?? ($bundleMetrics['total_units'] ?? 0); ?> units</span>
                        </th>
                        <th class="text-right font-weight-bold text-primary" style="font-size:15px;" id="storefront_footer_total_price">
                            <?= priceFormatted($initialGrandTotalPrice ?? ($bundleMetrics['sum_price'] ?? 0), $currencyCode, true); ?>
                        </th>
                    </tr>
                </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>

<!-- Bundle Product Quick View Modal (NO Add-to-Cart button) -->
<div class="modal fade" id="bundleProductQuickViewModal" tabindex="-1" role="dialog" aria-labelledby="bundleProductQuickViewModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="max-width: 720px;">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.05);">
            <div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 14px 20px;">
                <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                    <span class="badge badge-primary px-2.5 py-1" id="bp_qv_category" style="font-size: 11.5px; font-weight: 600;">Category</span>
                    <span class="badge badge-light border text-muted px-2 py-1" id="bp_qv_sku" style="font-size: 11.5px;">SKU: -</span>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="outline: none; font-size: 24px; font-weight: 400; opacity: 0.6; line-height: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4" style="background: #ffffff;">
                <div class="row">
                    <!-- Left: Product Image Box -->
                    <div class="col-md-5 col-12 mb-3 mb-md-0">
                        <div style="width: 100%; aspect-ratio: 1/1; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; display: flex; align-items: center; justify-content: center; position: relative;">
                            <img id="bp_qv_image" src="" alt="" style="width: 100%; height: 100%; object-fit: contain; padding: 8px;">
                            <div id="bp_qv_no_image" class="text-muted" style="display:none; text-align: center;">
                                <i class="fa fa-cube fa-3x text-muted mb-2"></i>
                                <div style="font-size: 12px;">No image available</div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Product Info & Description -->
                    <div class="col-md-7 col-12 d-flex flex-column justify-content-between">
                        <div>
                            <h4 id="bp_qv_title" class="font-weight-bold text-dark mb-2" style="font-size: 18px; line-height: 1.35;"></h4>
                            
                            <div class="d-flex align-items-center flex-wrap mb-3" style="gap: 12px;">
                                <div class="font-weight-bold text-primary" id="bp_qv_price" style="font-size: 18px;"></div>
                                <span class="badge badge-success" id="bp_qv_stock" style="font-size: 11px; padding: 4px 8px; background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0;">In Stock</span>
                            </div>

                            <div class="border-top pt-3 mt-2">
                                <h6 class="text-muted font-weight-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px; margin-bottom: 6px;">Product Overview & Specifications</h6>
                                <div id="bp_qv_description" class="text-secondary" style="font-size: 13.5px; line-height: 1.6; max-height: 200px; overflow-y: auto; padding-right: 4px;"></div>
                            </div>
                        </div>

                        <div class="mt-3 pt-3 border-top d-flex align-items-center justify-content-between">
                            <span class="text-muted small" style="font-size: 11.5px;">
                                <i class="fa fa-cubes mr-1 text-primary"></i> Bundle Package Component Item
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 10px 20px;">
                <button type="button" class="btn btn-secondary px-4 py-2" data-dismiss="modal" style="border-radius: 6px; font-weight: 600; font-size: 13px;">
                    <i class="fa fa-times mr-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var activeBundleCategoryKey = 'all';
var collapsedBundleCategories = {};
<?php if (!empty($groupedComponents)): ?>
    <?php foreach ($groupedComponents as $cK => $cG): ?>
        collapsedBundleCategories['<?= escJs($cK); ?>'] = true;
    <?php endforeach; ?>
<?php endif; ?>

var bundleCategoryNames = {
<?php if (!empty($groupedComponents)): ?>
    <?php foreach ($groupedComponents as $cK => $cG): ?>
        '<?= escJs($cK); ?>': <?= json_encode($cG['name']); ?>,
    <?php endforeach; ?>
<?php endif; ?>
};

var bundleProductsMap = {};
<?php if (!empty($bundleComponents)): ?>
    <?php foreach ($bundleComponents as $comp): 
        $compImg = (str_starts_with($comp->image_default ?: ($comp->image_small ?? ''), 'http://') || str_starts_with($comp->image_default ?: ($comp->image_small ?? ''), 'https://')) ? ($comp->image_default ?: $comp->image_small) : (str_starts_with($comp->image_default ?: ($comp->image_small ?? ''), 'uploads/') ? base_url($comp->image_default ?: $comp->image_small) : (!empty($comp->image_default ?: $comp->image_small) ? base_url('uploads/images/' . ($comp->image_default ?: $comp->image_small)) : ''));
    ?>
    bundleProductsMap['<?= (int)$comp->id; ?>'] = {
        id: <?= (int)$comp->component_product_id; ?>,
        title: <?= json_encode($comp->title); ?>,
        sku: <?= json_encode($comp->sku); ?>,
        category: <?= json_encode($comp->category_name); ?>,
        price: <?= json_encode(priceFormatted($comp->unit_price, $currencyCode, true)); ?>,
        image: <?= json_encode($compImg); ?>,
        stock: <?= (int)($comp->available_stock ?? 1); ?>,
        short_description: <?= json_encode($comp->short_description ?? ''); ?>,
        description: <?= json_encode($comp->description ?? ''); ?>
    };
    <?php endforeach; ?>
<?php endif; ?>

function openBundleProductQuickView(compId) {
    var p = bundleProductsMap[compId];
    if (!p) return;

    $('#bp_qv_title').text(p.title || '');
    $('#bp_qv_sku').text('SKU: ' + (p.sku || 'N/A'));
    $('#bp_qv_category').text(p.category || 'General');
    $('#bp_qv_price').text(p.price || '');

    if (p.image && p.image.length > 0) {
        $('#bp_qv_image').attr('src', p.image).show();
        $('#bp_qv_no_image').hide();
    } else {
        $('#bp_qv_image').hide();
        $('#bp_qv_no_image').show();
    }

    var desc = p.description || p.short_description || 'No additional details provided for this component item.';
    $('#bp_qv_description').html(desc);

    if (p.stock > 0) {
        $('#bp_qv_stock').text('In Stock').removeClass('badge-danger').addClass('badge-success').css({background: '#dcfce7', color: '#15803d', border: '1px solid #bbf7d0'});
    } else {
        $('#bp_qv_stock').text('Out of Stock').removeClass('badge-success').addClass('badge-danger').css({background: '#fee2e2', color: '#991b1b', border: '1px solid #fecaca'});
    }

    $('#bundleProductQuickViewModal').modal('show');
}

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

function updateStorefrontFooterSummary() {
    var catTotals = window._latestCategoryTotals || {};
    var numCats = <?= (int)$totalCategoriesCount; ?>;

    if (activeBundleCategoryKey !== 'all' && catTotals[activeBundleCategoryKey]) {
        var catName = (typeof bundleCategoryNames !== 'undefined' && bundleCategoryNames[activeBundleCategoryKey]) ? bundleCategoryNames[activeBundleCategoryKey] : 'Category';
        $('#storefront_footer_cat_name').text(catName);
        $('#storefront_footer_cat_units').text((catTotals[activeBundleCategoryKey].units || 0) + ' units');
        $('#storefront_footer_cat_price').text(formatBundleCurrency(catTotals[activeBundleCategoryKey].price || 0));
        $('#storefront_footer_cat_row').show();
        $('#storefront_footer_pkg_label').text('Package Grand Total (All Categories):');
    } else {
        $('#storefront_footer_cat_row').hide();
        if (numCats > 1) {
            $('#storefront_footer_pkg_label').text('Package Total (' + numCats + ' Categories):');
        } else {
            $('#storefront_footer_pkg_label').text('Package Total:');
        }
    }
}

function toggleBundleCategory(catKey) {
    if (collapsedBundleCategories[catKey]) {
        delete collapsedBundleCategories[catKey];
    } else {
        collapsedBundleCategories[catKey] = true;
    }
    updateCategoryCollapseUI(catKey);
}

function expandAllBundleCategories() {
    collapsedBundleCategories = {};
    $('.bundle-category-header-row').each(function() {
        var catKey = $(this).attr('data-cat-key');
        if (catKey) {
            updateCategoryCollapseUI(catKey);
        }
    });
}

function collapseAllBundleCategories() {
    $('.bundle-category-header-row').each(function() {
        var catKey = $(this).attr('data-cat-key');
        if (catKey) {
            collapsedBundleCategories[catKey] = true;
            updateCategoryCollapseUI(catKey);
        }
    });
}

function updateCategoryCollapseUI(catKey) {
    var isCollapsed = !!collapsedBundleCategories[catKey];
    var $chevron = $('.bundle-cat-chevron[data-cat-key="' + catKey + '"]');
    var $folder = $('.bundle-cat-folder[data-cat-key="' + catKey + '"]');

    if (isCollapsed) {
        $chevron.addClass('collapsed');
        $folder.removeClass('fa-folder-open').addClass('fa-folder');
    } else {
        $chevron.removeClass('collapsed');
        $folder.removeClass('fa-folder').addClass('fa-folder-open');
    }

    applyBundleStorefrontFilters();
}

function filterBundleCategory(catKey, btn) {
    activeBundleCategoryKey = catKey;
    $('.bundle-cat-pill').removeClass('active btn-primary').addClass('btn-outline-secondary');
    $(btn).removeClass('btn-outline-secondary').addClass('active btn-primary');

    // Auto-expand the selected category if it was collapsed
    if (catKey !== 'all' && collapsedBundleCategories[catKey]) {
        delete collapsedBundleCategories[catKey];
        var $chevron = $('.bundle-cat-chevron[data-cat-key="' + catKey + '"]');
        var $folder = $('.bundle-cat-folder[data-cat-key="' + catKey + '"]');
        $chevron.removeClass('collapsed');
        $folder.removeClass('fa-folder').addClass('fa-folder-open');
    }

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
            // Count matching items
            categoryVisibleCount[rowCatKey] = (categoryVisibleCount[rowCatKey] || 0) + 1;
            totalVisibleRows++;

            // If category is collapsed by user, and not searching, keep hidden
            if (collapsedBundleCategories[rowCatKey] && query === '') {
                $row.hide();
            } else {
                $row.show();
            }
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

    // Sync footer category subtotal row with current filter state
    if (typeof updateStorefrontFooterSummary === 'function') {
        updateStorefrontFooterSummary();
    }
}

function toggleBundleOptionalItem(chk) {
    var $chk = $(chk);
    var $row = $chk.closest('tr.storefront-bundle-row');
    var $input = $row.find('.bundle-storefront-qty-input');
    var isChecked = $chk.is(':checked');

    if (isChecked) {
        var currentVal = parseInt($input.val()) || 0;
        if (currentVal < 1) {
            $input.val(1);
        }
        $row.removeClass('bundle-row-disabled');
    } else {
        $input.val(0);
        $row.addClass('bundle-row-disabled');
    }

    validateBundleStorefrontQty($input[0]);
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
    var isOptional = $input.data('is-optional') == '1';
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

    // Sync checkbox state for optional items
    var $row = $input.closest('tr.storefront-bundle-row');
    var $chk = $row.find('.bundle-item-checkbox');
    if (isOptional && $chk.length) {
        if (val > 0) {
            $chk.prop('checked', true);
            $row.removeClass('bundle-row-disabled');
        } else {
            $chk.prop('checked', false);
            $row.addClass('bundle-row-disabled');
        }
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

    window._latestCategoryTotals = categoryTotals;
    window._latestTotalUnits = totalUnits;
    window._latestTotalPrice = totalPrice;

    // Update active category subtotal in table footer (if a specific category is filtered/selected)
    updateStorefrontFooterSummary();

    // Update overall package details totals
    $('#storefront_bundle_total_units').text(totalUnits);
    $('#storefront_footer_total_units').text(totalUnits + ' units');

    var formattedPrice = formatBundleCurrency(totalPrice);
    $('#storefront_bundle_total_price').text(formattedPrice);
    $('#storefront_footer_total_price').text(formattedPrice);

    // Update main product price container on the page
    var mainQty = parseInt($('#input_product_quantity').val()) || 1;
    var totalActualPrice = totalPrice * mainQty;
    var bundleDiscountRate = <?= !empty($product->bundle_discount_rate) ? (float)$product->bundle_discount_rate : 0; ?>;

    if (bundleDiscountRate > 0) {
        var discountedActualPrice = totalActualPrice * (1 - (bundleDiscountRate / 100));
        var formattedDiscountedPrice = formatBundleCurrency(discountedActualPrice);
        var formattedOrigPrice = formatBundleCurrency(totalActualPrice);

        if ($('#div-product-discounted-price .final-price').length) {
            $('#div-product-discounted-price .final-price, .product-price-container .final-price').text(formattedDiscountedPrice);
        } else if ($('#div-product-discounted-price').length) {
            $('#div-product-discounted-price').html('<span class="final-price">' + formattedDiscountedPrice + '</span>');
        }
        $('#div-product-discounted-price').addClass('text-product-discounted').show();

        if ($('#div-product-price .original-price').length) {
            $('#div-product-price .original-price').text(formattedOrigPrice);
        } else if ($('#div-product-price').length) {
            $('#div-product-price').html('<span class="original-price">' + formattedOrigPrice + '</span>');
        }
        $('#div-product-price').show();

        if ($('#div-product-discount-rate .discount-rate').length) {
            $('#div-product-discount-rate .discount-rate').text('-' + Math.round(bundleDiscountRate) + '%');
        } else if ($('#div-product-discount-rate').length) {
            $('#div-product-discount-rate').html('<span class="discount-rate">-' + Math.round(bundleDiscountRate) + '%</span>');
        }
        $('#div-product-discount-rate').show();
    } else {
        var formattedMainPrice = formatBundleCurrency(totalActualPrice);
        if ($('#div-product-discounted-price .final-price').length) {
            $('#div-product-discounted-price .final-price, .product-price-container .final-price').text(formattedMainPrice);
        } else if ($('#div-product-discounted-price').length) {
            $('#div-product-discounted-price').html('<span class="final-price">' + formattedMainPrice + '</span>');
        }
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

window.recalculateStorefrontBundleTotals = recalculateStorefrontBundleTotals;

$(document).ready(function() {
    applyBundleStorefrontFilters();

    $('.bundle-storefront-qty-input').each(function() {
        validateBundleStorefrontQty(this);
    });

    recalculateStorefrontBundleTotals();
    setTimeout(recalculateStorefrontBundleTotals, 100);
    setTimeout(recalculateStorefrontBundleTotals, 300);
    setTimeout(recalculateStorefrontBundleTotals, 800);

    // Also update main price when main quantity spinner changes
    $(document).on('input keyup paste change', '#input_product_quantity', function() {
        recalculateStorefrontBundleTotals();
    });

    $(document).on('click', '.product-add-to-cart-container .number-spinner button, .btn-spinner-minus, .btn-spinner-plus', function() {
        setTimeout(function() {
            recalculateStorefrontBundleTotals();
        }, 50);
    });

    // Handle direct navigation to bundle customization tab and category
    var urlParams = new URLSearchParams(window.location.search);
    var targetBundleCat = urlParams.get('bundle_cat');
    var isBundleTabHash = (window.location.hash === '#tab_bundle_contents' || window.location.hash === '#tab_bundle_contents_content' || targetBundleCat);

    if (isBundleTabHash) {
        if ($('#tab_bundle_contents').length) {
            $('#tab_bundle_contents').tab('show');
        }
        if ($('#collapse_bundle_contents_content').length) {
            $('#collapse_bundle_contents_content').addClass('show');
        }

        if (targetBundleCat && targetBundleCat !== 'all') {
            // 1. Specific Category Customize Clicked: Open ONLY this category, hide other categories
            var resolvedCatKey = targetBundleCat;
            if (!resolvedCatKey.startsWith('cat_') && $('#bundle_cat_row_cat_' + resolvedCatKey).length) {
                resolvedCatKey = 'cat_' + resolvedCatKey;
            } else if (!$('#bundle_cat_row_' + resolvedCatKey).length) {
                var cleanParam = targetBundleCat.toLowerCase().replace(/^cat_/, '').trim();
                $('.bundle-category-header-row').each(function() {
                    var rowKey = $(this).attr('data-cat-key') || '';
                    var rowName = ($(this).attr('data-cat-name') || '').toLowerCase().trim();
                    var rowId = ($(this).attr('data-cat-id') || '').toString();
                    if (rowKey.toLowerCase() === cleanParam || rowKey.toLowerCase() === ('cat_' + cleanParam) || rowName === cleanParam || rowName.indexOf(cleanParam) > -1 || rowId === cleanParam) {
                        resolvedCatKey = rowKey;
                        return false;
                    }
                });
            }

            // Set active category filter to ONLY this category
            activeBundleCategoryKey = resolvedCatKey;

            // Expand this category and collapse all others
            collapsedBundleCategories = {};
            $('.bundle-category-header-row').each(function() {
                var ck = $(this).attr('data-cat-key');
                if (ck) {
                    if (ck === resolvedCatKey) {
                        $('.bundle-cat-chevron[data-cat-key="' + ck + '"]').removeClass('collapsed');
                        $('.bundle-cat-folder[data-cat-key="' + ck + '"]').removeClass('fa-folder').addClass('fa-folder-open');
                    } else {
                        collapsedBundleCategories[ck] = true;
                        $('.bundle-cat-chevron[data-cat-key="' + ck + '"]').addClass('collapsed');
                        $('.bundle-cat-folder[data-cat-key="' + ck + '"]').removeClass('fa-folder-open').addClass('fa-folder');
                    }
                }
            });

            // Update category pills styling
            $('.bundle-cat-pill').removeClass('active btn-primary').addClass('btn-outline-secondary');
            if ($('.bundle-cat-pill[data-cat-key="' + resolvedCatKey + '"]').length) {
                $('.bundle-cat-pill[data-cat-key="' + resolvedCatKey + '"]').removeClass('btn-outline-secondary').addClass('active btn-primary');
            }

            // Apply filters to display ONLY this category's items and header
            applyBundleStorefrontFilters();

            // Smooth scroll to the target category with visual highlight
            setTimeout(function() {
                var $targetHeader = $('#bundle_cat_row_' + resolvedCatKey);
                if ($targetHeader.length) {
                    var offsetTop = $targetHeader.offset().top - 110;
                    $('html, body').animate({scrollTop: offsetTop}, 450);
                    $targetHeader.css({ 'background-color': '#dbeafe', 'transition': 'background-color 0.4s ease' });
                    setTimeout(function() {
                        $targetHeader.css({ 'background-color': '#f1f5f9' });
                    }, 1800);
                } else if ($('#tab_bundle_contents_content').length) {
                    $('html, body').animate({scrollTop: $('#tab_bundle_contents_content').offset().top - 110}, 400);
                }
            }, 300);
        } else {
            // 2. Main Item Customize Clicked (bundle_cat=all or not set): Show ALL categories expanded
            activeBundleCategoryKey = 'all';
            $('.bundle-cat-pill').removeClass('active btn-primary').addClass('btn-outline-secondary');
            $('.bundle-cat-pill[data-cat-key="all"]').removeClass('btn-outline-secondary').addClass('active btn-primary');

            // Un-collapse / expand all categories so all are open and visible
            collapsedBundleCategories = {};
            $('.bundle-category-header-row').each(function() {
                var ck = $(this).attr('data-cat-key');
                if (ck) {
                    $('.bundle-cat-chevron[data-cat-key="' + ck + '"]').removeClass('collapsed');
                    $('.bundle-cat-folder[data-cat-key="' + ck + '"]').removeClass('fa-folder').addClass('fa-folder-open');
                }
            });

            applyBundleStorefrontFilters();

            setTimeout(function() {
                if ($('#tab_bundle_contents_content').length) {
                    $('html, body').animate({scrollTop: $('#tab_bundle_contents_content').offset().top - 110}, 400);
                }
            }, 250);
        }
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
