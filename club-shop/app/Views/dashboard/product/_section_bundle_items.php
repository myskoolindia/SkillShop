<?php
$bundleModel = new \App\Models\BundleModel();
$bundleComponents = $bundleModel->getBundleComponents($product->id, false);
$bundleMetrics = $bundleModel->calculateBundleMetrics($product->id);
$isBundle = !empty($product->is_bundle) ? 1 : 0;
$pricingType = !empty($product->bundle_pricing_type) ? $product->bundle_pricing_type : 'fixed';
$discountRate = !empty($product->bundle_discount_rate) ? $product->bundle_discount_rate : 0;

// Gather category statistics for quick-filters and category totals
$categoryStats = [];
$mandatoryCount = 0;
$optionalCount = 0;
$mandatorySum = 0.0;
$allSum = 0.0;

foreach ($bundleComponents as $c) {
    $cCat = !empty($c->category_name) ? trim($c->category_name) : 'General';
    if (!isset($categoryStats[$cCat])) {
        $categoryStats[$cCat] = [
            'count' => 0,
            'units' => 0,
            'mand_count' => 0,
            'opt_count' => 0,
            'total' => 0.0,
            'mand_total' => 0.0,
        ];
    }
    $effectiveUnitPrice = ($c->price_override !== null && $c->price_override !== '') 
        ? (float)$c->price_override 
        : (float)$c->unit_price;
    $lineTotal = $effectiveUnitPrice * (int)$c->required_quantity;

    $categoryStats[$cCat]['count']++;
    $categoryStats[$cCat]['units'] += (int)$c->required_quantity;
    $categoryStats[$cCat]['total'] += $lineTotal;
    $allSum += $lineTotal;

    if (!empty($c->is_optional)) {
        $optionalCount++;
        $categoryStats[$cCat]['opt_count']++;
    } else {
        $mandatoryCount++;
        $mandatorySum += $lineTotal;
        $categoryStats[$cCat]['mand_count']++;
        $categoryStats[$cCat]['mand_total'] += $lineTotal;
    }
}
$categoriesList = [];
foreach ($categoryStats as $cName => $cData) {
    $categoriesList[$cName] = $cData['count'];
}
ksort($categoriesList);
?>

<style>
/* Modern UI Styles for Bundle Builder */
.section-bundle-builder {
    background: #ffffff;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}

/* Metric Cards Equal Height & SaaS Layout */
.bundle-metrics-row {
    display: flex;
    flex-wrap: wrap;
    margin-right: -10px;
    margin-left: -10px;
}
.bundle-metrics-row > [class*='col-'] {
    padding-right: 10px;
    padding-left: 10px;
    display: flex;
}
.bundle-metric-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 14px 16px;
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    width: 100%;
    min-height: 110px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}
.bundle-metric-card:hover {
    border-color: #cbd5e1;
    background: #f8fafc;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    transform: translateY(-1px);
}
.bundle-metric-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}
.bundle-metric-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: #64748b;
    margin-bottom: 0;
}
.bundle-metric-body {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 8px;
    margin-top: auto;
}
.bundle-metric-value {
    font-size: 24px;
    font-weight: 800;
    line-height: 1.1;
    font-family: 'Segoe UI', -apple-system, sans-serif;
}

/* Modern iOS-style Switch */
.switch-modern {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
    margin-bottom: 0;
    vertical-align: middle;
}
.switch-modern input {
    opacity: 0;
    width: 0;
    height: 0;
    position: absolute;
}
.switch-slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #cbd5e1;
    transition: .25s ease;
    border-radius: 24px;
}
.switch-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: #ffffff;
    transition: .25s ease;
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0,0,0,0.25);
}
.switch-modern input:checked + .switch-slider {
    background-color: #10b981;
}
.switch-modern input:checked + .switch-slider:before {
    transform: translateX(20px);
}

/* Hide native number spinners to prevent digit truncation */
.bundle-price-input::-webkit-outer-spin-button,
.bundle-price-input::-webkit-inner-spin-button,
.bundle-qty-input::-webkit-outer-spin-button,
.bundle-qty-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
.bundle-price-input[type=number],
.bundle-qty-input[type=number] {
    -moz-appearance: textfield;
}

.bundle-price-input {
    font-family: 'Segoe UI', 'Consolas', monospace !important;
    font-size: 13.5px !important;
    font-weight: 700 !important;
    letter-spacing: 0.3px;
    color: #0f172a;
    background: #ffffff;
    width: 130px;
    margin-left: auto;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 4px 8px;
}
.bundle-price-input:focus {
    background: #fffbeb;
    border-color: #f59e0b;
    box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2);
}

/* Table styling with clean sticky header and NO scroll trap */
.bundle-table-wrapper {
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    background: #ffffff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}
.bundle-table-wrapper.windowed-scroll {
    max-height: 650px;
    overflow-y: auto;
}
.bundle-table-wrapper.full-view {
    max-height: none !important;
    overflow-y: visible !important;
}

#table_bundle_components thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    background: #f1f5f9;
    color: #334155;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 10px 12px;
    border-top: none;
    border-bottom: 2px solid #cbd5e1;
    box-shadow: 0 2px 4px rgba(0,0,0,0.04);
}
#table_bundle_components tbody td {
    vertical-align: middle;
    padding: 8px 12px;
    font-size: 13px;
    border-bottom: 1px solid #e2e8f0;
}
.bundle-row:hover {
    background-color: #f8fafc !important;
}
.bundle-row.row-optional {
    background-color: #fcfcfc;
}
.bundle-row.row-mandatory {
    background-color: #ffffff;
}
.bundle-category-header td {
    background: #f8fafc !important;
    border-top: 2px solid #cbd5e1 !important;
    border-bottom: 1px solid #cbd5e1 !important;
    padding: 9px 14px !important;
}

/* Quick Filter Pills with Crisp High-Contrast Badges */
.bundle-filter-pill {
    cursor: pointer;
    border-radius: 20px;
    padding: 6px 14px;
    font-size: 12.5px;
    font-weight: 700;
    border: 1.5px solid #cbd5e1;
    background: #ffffff;
    color: #1e293b;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    user-select: none;
}
.bundle-filter-pill:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
    color: #0f172a;
}
.bundle-filter-pill.active {
    background: #1e3a8a !important;
    border-color: #1e3a8a !important;
    color: #ffffff !important;
    box-shadow: 0 2px 5px rgba(30, 58, 138, 0.3);
}
.bundle-filter-pill .badge-count {
    display: inline-block;
    font-size: 11.5px;
    font-weight: 800;
    padding: 2px 8px;
    border-radius: 12px;
    background: #e2e8f0;
    color: #0f172a;
}
.bundle-filter-pill.active .badge-count {
    background: #ffffff !important;
    color: #1e3a8a !important;
}

/* Floating Sticky Footer Bar */
.bundle-sticky-bottom-bar {
    position: sticky;
    bottom: 15px;
    z-index: 20;
    background: #1e293b;
    color: #ffffff;
    border-radius: 8px;
    padding: 10px 20px;
    margin-top: 15px;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.25);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}
</style>

<div class="section-product-details section-bundle-builder m-b-30">
    <div class="form-box">
        <?php
        $bundleSectionTitle = trans("bundle_package_builder");
        if (empty($bundleSectionTitle) || $bundleSectionTitle === 'bundle_package_builder') {
            $bundleSectionTitle = "Bundle / Package Builder";
        }
        ?>
        <div class="form-box-head d-flex justify-content-between align-items-center" style="display:flex; justify-content:space-between; align-items:center; padding:16px 20px; border-bottom:1px solid #e2e8f0;">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="width:40px; height:40px; background:#eff6ff; border:1px solid #dbeafe; border-radius:10px; display:flex; align-items:center; justify-content:center; color:#2563eb;">
                    <i class="fa fa-cubes" style="font-size:20px;"></i>
                </div>
                <div>
                    <h4 class="title" style="margin:0; font-size:16px; font-weight:700; color:#0f172a; letter-spacing:-0.2px;">
                        <?= esc($bundleSectionTitle); ?>
                    </h4>
                    <span class="text-muted" style="font-size:12px;">Component pricing, inventory tracking, and package curation</span>
                </div>
            </div>
            <div>
                <label style="display:inline-flex; align-items:center; gap:10px; margin-bottom:0; cursor:pointer;" for="is_bundle_toggle">
                    <span class="switch-modern">
                        <input type="checkbox" id="is_bundle_toggle" name="is_bundle" value="1" <?= $isBundle ? 'checked' : ''; ?> onchange="toggleBundleSection(this.checked);">
                        <span class="switch-slider"></span>
                    </span>
                    <span style="font-size:13.5px; font-weight:700; color:#1e293b;">Enable as Bundle Package</span>
                </label>
            </div>
        </div>

        <div class="form-box-body" id="bundle_builder_container" style="<?= $isBundle ? '' : 'display:none;'; ?> padding:20px;">
            
            <!-- Metrics Bar (Equal Height SaaS Cards) -->
            <div class="row bundle-metrics-row m-b-20">
                <div class="col-md-3 col-sm-6 m-b-10">
                    <div class="bundle-metric-card">
                        <div class="bundle-metric-header">
                            <span class="bundle-metric-label">Component Products</span>
                            <span class="badge" style="background:#eff6ff; color:#2563eb; font-size:10.5px; font-weight:700; padding:2px 6px; border-radius:5px; border:1px solid #dbeafe;">SKUs</span>
                        </div>
                        <div class="bundle-metric-body" style="margin:4px 0 8px 0;">
                            <span class="bundle-metric-value text-primary font-weight-bold" id="badge_total_components"><?= count($bundleComponents); ?></span>
                            <span style="font-size:11.5px; font-weight:700; color:#334155; background:#f1f5f9; border:1px solid #e2e8f0; padding:3px 8px; border-radius:6px; white-space:nowrap;">
                                <span id="badge_mand_count" class="text-success font-weight-bold"><?= $mandatoryCount; ?></span> Mand &bull; <span id="badge_opt_count" class="text-secondary font-weight-bold"><?= $optionalCount; ?></span> Opt
                            </span>
                        </div>
                        <div style="font-size:11px; color:#64748b; padding-top:5px; border-top:1px solid #f1f5f9; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            Included in package curation
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 m-b-10">
                    <div class="bundle-metric-card">
                        <div class="bundle-metric-header">
                            <span class="bundle-metric-label">Total Package Units</span>
                            <span class="badge" style="background:#f0fdf4; color:#16a34a; font-size:10.5px; font-weight:700; padding:2px 6px; border-radius:5px; border:1px solid #dcfce7;">QTY</span>
                        </div>
                        <div class="bundle-metric-body" style="margin:4px 0 8px 0;">
                            <span class="bundle-metric-value text-info font-weight-bold" id="badge_total_units"><?= $bundleMetrics['total_units']; ?></span>
                            <span style="font-size:11.5px; font-weight:700; color:#334155; background:#f1f5f9; border:1px solid #e2e8f0; padding:3px 8px; border-radius:6px; white-space:nowrap;">
                                <i class="fa fa-cubes text-info"></i> Pieces in 1 Box
                            </span>
                        </div>
                        <div style="font-size:11px; color:#64748b; padding-top:5px; border-top:1px solid #f1f5f9; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            Physical component count
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 m-b-10">
                    <div class="bundle-metric-card" style="border-color:#cbd5e1; background:linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);">
                        <div class="bundle-metric-header">
                            <span class="bundle-metric-label">Total Package Value</span>
                            <span class="badge" style="background:#eff6ff; color:#1e40af; font-size:10.5px; font-weight:700; padding:2px 6px; border-radius:5px; border:1px solid #bfdbfe;">VAL</span>
                        </div>
                        <div class="bundle-metric-body" style="margin:4px 0 8px 0; display:flex; align-items:center; justify-content:space-between; gap:6px;">
                            <span class="bundle-metric-value text-dark font-weight-bold" id="badge_sum_price" style="font-size:19px; font-family:'Segoe UI', 'Consolas', monospace; white-space:nowrap; line-height:1.2;">₹ <?= number_format($allSum, 2, '.', ','); ?></span>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-primary font-weight-bold" onclick="applyBundleSumToPrice('total');" style="font-size:10.5px; padding:3px 8px; border-radius:4px 0 0 4px; white-space:nowrap;" title="Apply total package value (₹ <?= number_format($allSum, 2, '.', ','); ?>) to product price">
                                    <i class="fa fa-arrow-up"></i> Apply
                                </button>
                                <?php if ($optionalCount > 0): ?>
                                    <button type="button" class="btn btn-sm btn-outline-primary font-weight-bold" onclick="applyBundleSumToPrice('mandatory');" style="font-size:10.5px; padding:3px 7px; border-radius:0 4px 4px 0; border-left:none; white-space:nowrap; background:#eff6ff;" title="Apply mandatory base sum (₹ <?= number_format($mandatorySum, 2, '.', ','); ?>) to product price">
                                        Base
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div style="font-size:11px; color:#64748b; padding-top:5px; border-top:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center;">
                            <span id="card_mand_box" <?= ($optionalCount > 0) ? '' : 'style="display:none;"'; ?> style="white-space:nowrap;">
                                Base: <strong class="text-dark font-weight-bold" id="card_mand_sum_disp">₹ <?= number_format($mandatorySum, 2, '.', ','); ?></strong>
                            </span>
                            <span id="card_all_mand_note" style="<?= ($optionalCount > 0) ? 'display:none;' : ''; ?> white-space:nowrap; color:#64748b;">
                                All items mandatory
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 m-b-10">
                    <div class="bundle-metric-card">
                        <div class="bundle-metric-header">
                            <span class="bundle-metric-label">Calculated Stock</span>
                            <span class="badge" style="background:#fef2f2; color:#dc2626; font-size:10.5px; font-weight:700; padding:2px 6px; border-radius:5px; border:1px solid #fee2e2;">LIMIT</span>
                        </div>
                        <div class="bundle-metric-body" style="margin:4px 0 8px 0;">
                            <span class="bundle-metric-value <?= $bundleMetrics['max_stock'] > 0 ? 'text-success' : 'text-danger'; ?> font-weight-bold" id="badge_max_stock">
                                <?= $bundleMetrics['max_stock']; ?> <span style="font-size:13px; font-weight:600; color:#64748b;">pkgs</span>
                            </span>
                            <span style="font-size:11.5px; font-weight:600; color:#64748b; background:#f1f5f9; border:1px solid #e2e8f0; padding:3px 8px; border-radius:6px; white-space:nowrap;">
                                <i class="fa fa-shield text-muted"></i> By component stock
                            </span>
                        </div>
                        <div style="font-size:11px; color:#64748b; padding-top:5px; border-top:1px solid #f1f5f9; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            Max fulfillable from inventory
                        </div>
                    </div>
                </div>
            </div>

            <!-- Toolbar 1: Primary Actions, Search & View Controls -->
            <div class="row align-items-center m-b-15">
                <div class="col-lg-6 col-md-12 m-b-10" style="display:flex; flex-wrap:wrap; gap:8px;">
                    <button type="button" class="btn btn-primary font-weight-bold" data-toggle="modal" data-target="#modalAddBundleItem">
                        <i class="fa fa-plus-circle"></i>&nbsp;&nbsp;Add Products / Variations
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="sortBundleTableByCategory();" title="Sort and group items alphabetically by Category">
                        <i class="fa fa-sort-alpha-asc"></i>&nbsp;&nbsp;Sort by Category
                    </button>
                </div>
                <div class="col-lg-6 col-md-12 text-lg-right m-b-10" style="display:flex; justify-content:flex-end; align-items:center; flex-wrap:wrap; gap:10px;">
                    <!-- View Mode Toggle -->
                    <div class="btn-group btn-group-sm" role="group" aria-label="View Mode">
                        <button type="button" class="btn btn-outline-secondary active font-weight-bold" id="btn_view_full" onclick="setBundleTableViewMode('full');" title="Smooth full page flow (No nested scrollbars)">
                            <i class="fa fa-arrows-v"></i> Full View
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="btn_view_window" onclick="setBundleTableViewMode('window');" title="Contained scroll window">
                            <i class="fa fa-bars"></i> Compact Window
                        </button>
                    </div>

                    <!-- Filter Search Input -->
                    <div style="position:relative; width:260px;">
                        <input type="text" id="bundle_filter_input" class="form-control form-control-sm" style="padding-left:30px; border-radius:20px;" placeholder="Filter by Name, SKU, Category..." onkeyup="filterBundleTable();">
                        <i class="fa fa-search text-muted" style="position:absolute; left:10px; top:8px; font-size:12px;"></i>
                    </div>
                </div>
            </div>

            <!-- Toolbar 2: Quick Filter Pills & Category Dropdown -->
            <div class="p-2 mb-3" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
                <div style="display:flex; align-items:center; flex-wrap:wrap; gap:8px;">
                    <span class="text-muted small font-weight-bold text-uppercase" style="margin-right:4px;">Filter:</span>
                    <div class="bundle-filter-pill active" id="pill_all" onclick="setBundleFilterType('all');">
                        All Products <span class="badge-count" id="pill_count_all"><?= count($bundleComponents); ?></span>
                    </div>
                    <div class="bundle-filter-pill" id="pill_mandatory" onclick="setBundleFilterType('mandatory');">
                        <i class="fa fa-check-circle text-success"></i> Mandatory <span class="badge-count" id="pill_count_mand"><?= $mandatoryCount; ?></span>
                    </div>
                    <div class="bundle-filter-pill" id="pill_optional" onclick="setBundleFilterType('optional');">
                        <i class="fa fa-tag text-muted"></i> Optional <span class="badge-count" id="pill_count_opt"><?= $optionalCount; ?></span>
                    </div>
                </div>

                <div style="display:flex; align-items:center; gap:8px;">
                    <label class="small text-muted mb-0 font-weight-bold">Jump to Category:</label>
                    <select id="bundle_category_filter_select" class="form-control form-control-sm custom-select" style="max-width:240px; height:32px; font-size:12px; font-weight:600;" onchange="filterBundleTable();">
                        <option value="">-- All Categories (<?= count($categoriesList); ?>) --</option>
                        <?php foreach ($categoriesList as $catName => $count): ?>
                            <option value="<?= esc(strtolower($catName)); ?>"><?= esc($catName); ?> (<?= $count; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Components Table Wrapper (Default: Smooth Full Flow, NO scroll trap) -->
            <div class="table-responsive bundle-table-wrapper full-view" id="bundle_table_container">
                <table class="table table-hover table-striped mb-0" id="table_bundle_components" style="width:100%;">
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align:center;">#</th>
                            <th style="width: 60px; text-align:center;">Image</th>
                            <th style="min-width: 250px;">Component & Variation</th>
                            <th style="width: 120px;">SKU</th>
                            <th style="width: 75px;" class="text-center">Optional</th>
                            <th style="width: 85px;" class="text-center">Qty</th>
                            <th style="width: 140px;" class="text-right">Unit Price (₹)</th>
                            <th style="width: 130px;" class="text-right">Total (₹)</th>
                            <th style="width: 95px;" class="text-center">Stock</th>
                            <th style="width: 55px;" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="bundle_components_tbody">
                        <?php if (!empty($bundleComponents)): ?>
                            <?php 
                            $currCatName = null;
                            foreach ($bundleComponents as $idx => $comp): 
                                $compCat = !empty($comp->category_name) ? $comp->category_name : 'General';
                                $isOpt = !empty($comp->is_optional) ? 1 : 0;
                                
                                // Price override formatting: ALWAYS guarantee 2 decimal places with .00 visible
                                $effectiveUnitPrice = ($comp->price_override !== null && $comp->price_override !== '') 
                                    ? (float)$comp->price_override 
                                    : (float)$comp->unit_price;
                                $formattedUnitPrice = number_format($effectiveUnitPrice, 2, '.', '');
                                $lineTotal = $effectiveUnitPrice * (int)$comp->required_quantity;
                                $formattedLineTotal = number_format($lineTotal, 2, '.', ',');

                                if ($compCat !== $currCatName):
                                    $currCatName = $compCat;
                                    $cStat = $categoryStats[$currCatName] ?? ['count' => 0, 'mand_count' => 0, 'opt_count' => 0, 'total' => 0.0, 'mand_total' => 0.0];
                            ?>
                                <tr class="bundle-category-header" data-category="<?= esc(strtolower($currCatName)); ?>">
                                    <td colspan="10">
                                        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
                                            <div style="display:flex; align-items:center; gap:8px;">
                                                <span style="font-weight:700; color:#0f172a; font-size:12.5px; text-transform:uppercase; letter-spacing:0.5px;">
                                                    <i class="fa fa-folder-open text-primary"></i>&nbsp;&nbsp;<?= esc($currCatName); ?>
                                                </span>
                                                <span style="font-size:11.5px; font-weight:700; color:#1e293b; background:#e2e8f0; border:1px solid #cbd5e1; padding:3px 9px; border-radius:12px;">
                                                    <span class="cat-item-count"><?= $cStat['count']; ?></span> <span class="cat-item-count-label"><?= $cStat['count'] == 1 ? 'Product' : 'Products'; ?></span> (<span class="cat-unit-count"><?= $cStat['units']; ?></span> <span class="cat-unit-count-label"><?= $cStat['units'] == 1 ? 'Unit' : 'Units'; ?></span>)
                                                </span>
                                            </div>
                                            <div style="display:flex; align-items:center; gap:8px;">
                                                <span class="cat-mand-wrapper text-muted small" style="font-size:11.5px; <?= ($cStat['opt_count'] > 0) ? '' : 'display:none;'; ?>">
                                                    Base/Mandatory: <strong class="cat-mand-val text-dark" style="font-family:'Segoe UI', 'Consolas', monospace;">₹ <?= number_format($cStat['mand_total'], 2, '.', ','); ?></strong>
                                                    <span class="cat-mand-sep text-muted mx-1">|</span>
                                                </span>
                                                <span style="font-size:12px; font-weight:700; color:#1e3a8a; background:#e0e7ff; border:1px solid #c7d2fe; padding:3px 10px; border-radius:6px; display:inline-flex; align-items:center; gap:5px;">
                                                    <span>Category Total:</span>
                                                    <span class="cat-total-val" style="font-family:'Segoe UI', 'Consolas', monospace; font-size:13px; color:#1e1b4b;">₹ <?= number_format($cStat['total'], 2, '.', ','); ?></span>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                                <tr class="bundle-row <?= $isOpt ? 'row-optional' : 'row-mandatory'; ?>" 
                                    data-id="<?= $comp->id; ?>" 
                                    data-title="<?= esc(strtolower($comp->title)); ?>" 
                                    data-sku="<?= esc(strtolower($comp->sku)); ?>" 
                                    data-category="<?= esc(strtolower($compCat)); ?>"
                                    data-is-optional="<?= $isOpt; ?>">
                                    
                                    <!-- Index -->
                                    <td class="text-center text-muted font-weight-bold bundle-row-index" style="font-size:12px;"><?= $idx + 1; ?></td>
                                    
                                    <!-- Image -->
                                    <td class="text-center" style="padding:4px;">
                                        <?php if (!empty($comp->image_small)):
                                            $compImgUrl = (str_starts_with($comp->image_small, 'http://') || str_starts_with($comp->image_small, 'https://')) ? $comp->image_small : (str_starts_with($comp->image_small, 'uploads/') ? base_url($comp->image_small) : base_url('uploads/images/' . $comp->image_small));
                                        ?>
                                            <img src="<?= $compImgUrl; ?>" style="width:40px; height:40px; object-fit:cover; border-radius:6px; border:1px solid #e2e8f0;" alt="">
                                        <?php else: ?>
                                            <div style="width:40px; height:40px; background:#f1f5f9; border-radius:6px; display:inline-flex; align-items:center; justify-content:center; color:#94a3b8; border:1px solid #e2e8f0;"><i class="fa fa-cube"></i></div>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <!-- Title & Variation (Category is in subheading so omitted here) -->
                                    <td>
                                        <div class="font-weight-bold text-dark" style="font-size:13px; line-height:1.3;"><?= esc($comp->title); ?></div>
                                        <?php if (!empty($comp->variant_name)): ?>
                                            <div style="margin-top:3px;">
                                                <span class="badge badge-info" style="font-size:10px;"><i class="fa fa-tag"></i> <?= esc($comp->variant_name); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        <input type="hidden" name="bundle_items[<?= $idx; ?>][component_product_id]" value="<?= $comp->component_product_id; ?>">
                                        <input type="hidden" name="bundle_items[<?= $idx; ?>][variant_id]" value="<?= $comp->variant_id; ?>">
                                    </td>
                                    
                                    <!-- SKU -->
                                    <td><code style="font-size:11.5px; background:#f8fafc; border:1px solid #e2e8f0; padding:2px 5px; border-radius:4px;"><?= esc($comp->sku ?: 'N/A'); ?></code></td>
                                    
                                    <!-- Optional Checkbox (Clean checkbox, no redundant text) -->
                                    <td class="text-center">
                                        <div class="custom-control custom-checkbox" style="display:inline-block;">
                                            <input type="checkbox" class="custom-control-input bundle-optional-checkbox" id="bundle_opt_<?= $idx; ?>" name="bundle_items[<?= $idx; ?>][is_optional]" value="1" <?= $isOpt ? 'checked' : ''; ?> onchange="onBundleOptionalChange(this);">
                                            <label class="custom-control-label" for="bundle_opt_<?= $idx; ?>" title="Check to mark item as optional"></label>
                                        </div>
                                    </td>
                                    
                                    <!-- Qty in Pkg -->
                                    <td class="text-center">
                                        <input type="number" min="<?= $isOpt ? '0' : '1'; ?>" max="9999" name="bundle_items[<?= $idx; ?>][quantity]" class="form-control form-control-sm text-center font-weight-bold bundle-qty-input" value="<?= $comp->required_quantity; ?>" onchange="recalculateBundleTotals();" style="width:65px; margin:0 auto; font-size:13px;">
                                    </td>
                                    
                                    <!-- Unit Price (Clean full amount with guaranteed .00, no rupee symbol) -->
                                    <td class="text-right">
                                        <input type="number" step="0.01" min="0" 
                                               name="bundle_items[<?= $idx; ?>][price_override]" 
                                               class="form-control form-control-sm bundle-price-input text-right" 
                                               value="<?= $formattedUnitPrice; ?>" 
                                               placeholder="0.00"
                                               onchange="normalizePriceInput(this); recalculateBundleTotals();"
                                               onblur="normalizePriceInput(this);"
                                               title="Unit price amount (full amount with .00)">
                                    </td>
                                    
                                    <!-- Line Total (Clean number, no rupee symbol) -->
                                    <td class="text-right font-weight-bold text-dark item-row-total" style="font-family:'Segoe UI', 'Consolas', monospace; font-size:13px; white-space:nowrap;">
                                        <?= $formattedLineTotal; ?>
                                    </td>
                                    
                                    <!-- Stock -->
                                    <td class="text-center">
                                        <span class="badge <?= $comp->can_fulfill ? 'badge-success' : 'badge-danger'; ?>" style="font-size:10.5px; padding:3px 6px;">
                                            <?= $comp->available_stock; ?> in stock
                                        </span>
                                    </td>
                                    
                                    <!-- Action -->
                                    <td class="text-center">
                                        <button type="button" class="btn btn-xs btn-outline-danger" onclick="removeBundleRow(this);" title="Remove" style="padding:3px 7px;">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr id="empty_bundle_row">
                                <td colspan="10" class="text-center p-4 text-muted">
                                    <i class="fa fa-inbox fa-3x mb-2 text-muted"></i><br>
                                    <strong>No component items added yet.</strong><br>
                                    Click <button type="button" class="btn btn-sm btn-primary m-1" data-toggle="modal" data-target="#modalAddBundleItem"><i class="fa fa-plus-circle"></i> Add Products / Variations</button> to populate this package.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Sticky Bottom Quick-Bar -->
            <div class="bundle-sticky-bottom-bar" id="bundle_sticky_bar">
                <div style="display:flex; align-items:center; gap:16px; flex-wrap:wrap;">
                    <div>
                        <span style="color:#94a3b8; font-size:12px;">Visible Products:</span>
                        <strong style="font-size:14px; margin-left:4px; color:#ffffff;" id="sticky_visible_count"><?= count($bundleComponents); ?></strong>
                        <span style="color:#64748b; font-size:12px;">of <?= count($bundleComponents); ?></span>
                    </div>
                    <div style="width:1px; height:18px; background:#475569;"></div>
                    <div>
                        <span style="color:#94a3b8; font-size:12px;">Visible Units:</span>
                        <strong style="font-size:14px; margin-left:4px; color:#ffffff;" id="sticky_visible_units"><?= $bundleMetrics['total_units']; ?></strong>
                        <span style="color:#64748b; font-size:12px;">of <?= $bundleMetrics['total_units']; ?></span>
                    </div>
                    <div style="width:1px; height:18px; background:#475569;"></div>
                    <div>
                        <span style="color:#94a3b8; font-size:12px;">Mandatory Base Price:</span>
                        <strong style="color:#38bdf8; font-size:15px; margin-left:4px; font-family:'Segoe UI', 'Consolas', monospace;" id="sticky_mand_sum">₹ <?= number_format($mandatorySum, 2, '.', ','); ?></strong>
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                    <?php if ($optionalCount > 0): ?>
                        <button type="button" class="btn btn-sm font-weight-bold" style="background:#0284c7; color:#ffffff !important; border:1px solid #0369a1; padding:6px 12px; border-radius:6px;" onclick="applyBundleSumToPrice('mandatory');" title="Set product price equal to mandatory components base sum only">
                            <i class="fa fa-check-circle"></i> Apply Base to Price
                        </button>
                    <?php endif; ?>
                    <button type="button" class="btn btn-sm font-weight-bold" style="background:#f59e0b; color:#0f172a !important; font-weight:800; border:1px solid #d97706; padding:6px 14px; border-radius:6px;" onclick="applyBundleSumToPrice('total');" title="Set product price equal to sum of all items value">
                        <i class="fa fa-arrow-up"></i> Apply Total to Product Price
                    </button>
                    <button type="button" class="btn btn-sm font-weight-bold" style="background:#334155; color:#ffffff !important; border:1px solid #475569; padding:6px 14px; border-radius:6px;" onclick="$('html, body').animate({scrollTop: $('#bundle_builder_container').offset().top - 80}, 200);" title="Scroll to top of bundle builder">
                        <i class="fa fa-arrow-up"></i> Top
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal: Add Product / Variant Live Search & Multi-Select -->
<style>
#modalAddBundleItem .modal-dialog {
    width: 95% !important;
    max-width: 1400px !important;
    margin: 20px auto !important;
}
#modalAddBundleItem .modal-body {
    max-height: none !important;
    overflow: visible !important;
}
#bundle_search_results {
    max-height: none !important;
    overflow: visible !important;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 0;
    margin-top: 10px;
    background: #fff;
}
#modalAddBundleItem .modal-footer {
    display: flex !important;
    justify-content: flex-end !important;
    align-items: center !important;
    gap: 10px !important;
    padding: 12px 20px !important;
}
.bundle-search-table {
    width: 100% !important;
    margin-bottom: 0 !important;
}
.bundle-search-table th, .bundle-search-table td {
    vertical-align: middle !important;
}
</style>
<div class="modal fade" id="modalAddBundleItem" tabindex="-1" role="dialog" aria-labelledby="modalAddBundleItemLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title font-weight-bold" id="modalAddBundleItemLabel">
                    <i class="fa fa-cubes text-primary"></i> Add Products to Package
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Search & Category Filters -->
                <div class="row">
                    <div class="col-md-5 col-12 mb-2">
                        <label class="font-weight-bold"><i class="fa fa-folder-open text-primary"></i> Category</label>
                        <select id="bundle_product_search_category_id" class="form-control custom-select" onchange="triggerBundleSearch();">
                            <option value="0">-- All Categories (Browse All) --</option>
                            <?php
                            $categoryModel = new \App\Models\CategoryModel();
                            $allParentCats = $categoryModel->getParentCategories(true);
                            if (!empty($allParentCats)) {
                                foreach ($allParentCats as $pCat) {
                                    $pCatName = !empty($pCat->cat_name) ? $pCat->cat_name : (!empty($pCat->name) ? $pCat->name : ('Category #' . $pCat->id));
                                    echo '<option value="' . $pCat->id . '">' . esc($pCatName) . '</option>';
                                    $subCats = $categoryModel->getSubCategoriesByParentId($pCat->id);
                                    if (!empty($subCats)) {
                                        foreach ($subCats as $sCat) {
                                            $sCatName = !empty($sCat->cat_name) ? $sCat->cat_name : (!empty($sCat->name) ? $sCat->name : ('Subcategory #' . $sCat->id));
                                            echo '<option value="' . $sCat->id . '">&nbsp;&nbsp;&nbsp;↳ ' . esc($sCatName) . '</option>';
                                        }
                                    }
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-7 col-12 mb-2">
                        <label class="font-weight-bold"><i class="fa fa-search text-muted"></i> Search Title or SKU</label>
                        <div class="input-group" style="display:flex;">
                            <input type="text" id="bundle_product_search_query" class="form-control" placeholder="Type product name or SKU..." onkeyup="debouncedSearchBundle(this.value);" oninput="debouncedSearchBundle(this.value);" autocomplete="off">
                            <div class="input-group-append" style="margin-left:5px;">
                                <button class="btn btn-primary" type="button" onclick="triggerBundleSearch();"><i class="fa fa-search"></i> Search</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Multi-Select Action Bar -->
                <div id="bundle_multi_select_bar" style="background:#f8fafc; border:1px solid #cbd5e1; border-radius:6px; padding:8px 12px; margin-top:8px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
                    <div style="display:inline-flex; align-items:center; gap:8px;">
                        <label style="cursor:pointer; font-size:13px; font-weight:bold; margin-bottom:0; display:inline-flex; align-items:center; gap:6px; color:#1e293b;">
                            <input type="checkbox" id="bundle_check_all_products" onchange="toggleSelectAllBundleProducts(this.checked);" style="width:17px; height:17px; margin:0; cursor:pointer;">
                            Select All
                        </label>
                        <span class="badge" id="bundle_selected_count_badge" style="display:none; background:#0284c7; color:#fff; font-size:11px; padding:3px 8px; border-radius:10px;">0 selected</span>
                    </div>
                    <div style="display:inline-flex; align-items:center; gap:12px; flex-wrap:wrap;">
                        <div style="display:inline-flex; align-items:center; gap:6px;">
                            <label class="small mb-0 font-weight-bold text-muted">Default Qty:</label>
                            <input type="number" id="batch_bundle_item_qty" class="form-control input-sm" min="0" value="1" style="width:65px; height:28px; padding:2px 6px; font-weight:600; display:inline-block;">
                        </div>
                        <label style="cursor:pointer; font-size:12px; font-weight:600; margin-bottom:0; display:inline-flex; align-items:center; gap:4px; color:#475569;" title="Optional items start with 0 qty for buyers">
                            <input type="checkbox" id="batch_bundle_item_is_optional" onchange="onBatchOptionalChange(this.checked);" style="width:16px; height:16px; margin:0; cursor:pointer;">
                            Optional
                        </label>
                        <button type="button" class="btn btn-sm btn-success font-weight-bold" id="btn_batch_add_bundle_items" style="display:none; padding:4px 12px; font-size:12px;" onclick="addSelectedBundleProducts();">
                            <i class="fa fa-plus-circle"></i> Add Selected (<span id="batch_selected_btn_count">0</span>)
                        </button>
                    </div>
                </div>

                <div id="bundle_modal_feedback" style="display:none; margin-top:8px;"></div>
                <div id="bundle_search_results">
                    <div class="text-muted text-center py-3"><i class="fa fa-spinner fa-spin"></i> Loading products...</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success font-weight-bold" id="btn_modal_footer_batch_add" style="display:none;" onclick="addSelectedBundleProducts();">
                    <i class="fa fa-plus-circle"></i> Add Selected Items
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var selectedProductForBundle = null;
var bundleRowIndex = <?= count($bundleComponents); ?>;
window.bundleSearchResultsList = [];
window.bundleSelectedMap = {};
var bundleSearchTimer = null;
window.currentBundleFilterType = 'all';

// Normalize price input to always have 2 decimal places (.00)
function normalizePriceInput(input) {
    var rawVal = $(input).val();
    if (rawVal === '' || rawVal === null || rawVal === undefined) return;
    var num = parseFloat(rawVal);
    if (!isNaN(num) && num >= 0) {
        $(input).val(num.toFixed(2));
    }
}

function debouncedSearchBundle(query) {
    if (bundleSearchTimer) clearTimeout(bundleSearchTimer);
    bundleSearchTimer = setTimeout(function() {
        searchProductsForBundle(query);
    }, 180);
}

$(document).ready(function() {
    $('#modalAddBundleItem').on('shown.bs.modal', function() {
        $('#bundle_product_search_query').focus();
        $('#bundle_modal_feedback').hide().empty();
        window.bundleSelectedMap = {};
        $('#bundle_check_all_products').prop('checked', false);
        updateBatchSelectionUI();
        searchProductsForBundle($('#bundle_product_search_query').val() || '');
    });

    recalculateBundleTotals();
});

function toggleBundleSection(checked) {
    if (checked) {
        $('#bundle_builder_container').slideDown(200);
    } else {
        $('#bundle_builder_container').slideUp(200);
    }
}

// View Mode: Full Flow vs Compact Window
function setBundleTableViewMode(mode) {
    var $container = $('#bundle_table_container');
    if (mode === 'window') {
        $container.removeClass('full-view').addClass('windowed-scroll');
        $('#btn_view_window').addClass('active font-weight-bold');
        $('#btn_view_full').removeClass('active font-weight-bold');
    } else {
        $container.removeClass('windowed-scroll').addClass('full-view');
        $('#btn_view_full').addClass('active font-weight-bold');
        $('#btn_view_window').removeClass('active font-weight-bold');
    }
}

// Quick Filter: All vs Mandatory vs Optional
function setBundleFilterType(type) {
    window.currentBundleFilterType = type;
    $('.bundle-filter-pill').removeClass('active');
    if (type === 'mandatory') {
        $('#pill_mandatory').addClass('active');
    } else if (type === 'optional') {
        $('#pill_optional').addClass('active');
    } else {
        $('#pill_all').addClass('active');
    }
    filterBundleTable();
}

function filterBundleTable() {
    var textFilter = ($('#bundle_filter_input').val() || '').toLowerCase().trim();
    var catFilter = ($('#bundle_category_filter_select').val() || '').toLowerCase().trim();
    var statusFilter = window.currentBundleFilterType;

    var visibleCount = 0;
    var visibleUnits = 0;
    var visibleMandatorySum = 0;

    $('#table_bundle_components tbody tr.bundle-row').each(function() {
        var title = $(this).attr('data-title') || '';
        var sku = $(this).attr('data-sku') || '';
        var cat = $(this).attr('data-category') || '';
        var isOpt = parseInt($(this).attr('data-is-optional')) || 0;

        var matchText = (!textFilter || title.indexOf(textFilter) > -1 || sku.indexOf(textFilter) > -1 || cat.indexOf(textFilter) > -1);
        var matchCat = (!catFilter || cat.indexOf(catFilter) > -1);

        var matchStatus = true;
        if (statusFilter === 'mandatory') {
            matchStatus = (isOpt === 0);
        } else if (statusFilter === 'optional') {
            matchStatus = (isOpt === 1);
        }

        if (matchText && matchCat && matchStatus) {
            $(this).show();
            visibleCount++;
            $(this).find('.bundle-row-index').text(visibleCount); // Dynamically renumbers visible rows sequentially (1, 2, 3...)
            var qty = parseInt($(this).find('.bundle-qty-input').val()) || 0;
            var price = parseFloat($(this).find('.bundle-price-input').val()) || 0;
            visibleUnits += qty;
            if (isOpt === 0) {
                visibleMandatorySum += (qty * price);
            }
        } else {
            $(this).hide();
        }
    });

    $('#table_bundle_components tbody tr.bundle-category-header').each(function() {
        var headerCat = $(this).attr('data-category') || '';
        if (catFilter && headerCat.indexOf(catFilter) === -1) {
            $(this).hide();
        } else {
            var hasVisibleRows = false;
            var $next = $(this).next();
            while ($next.length && !$next.hasClass('bundle-category-header')) {
                if ($next.is(':visible')) {
                    hasVisibleRows = true;
                    break;
                }
                $next = $next.next();
            }
            if (hasVisibleRows) {
                $(this).show();
            } else {
                $(this).hide();
            }
        }
    });

    $('#sticky_visible_count').text(visibleCount);
    $('#sticky_visible_units').text(visibleUnits);
    $('#sticky_mand_sum').text('₹ ' + visibleMandatorySum.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
}

function getExistingBundleProductIds() {
    var pids = [];
    $('#table_bundle_components tbody tr.bundle-row').each(function() {
        var pid = parseInt($(this).find('input[name*="[component_product_id]"]').val()) || 0;
        if (pid > 0 && pids.indexOf(pid) === -1) {
            pids.push(pid);
        }
    });
    return pids;
}

function removeBundleRow(btn) {
    var $row = $(btn).closest('tr');
    var cat = $row.attr('data-category');
    $row.remove();
    if (cat) {
        var remainingInCat = $('#table_bundle_components tbody tr.bundle-row[data-category="' + cat + '"]').length;
        if (remainingInCat === 0) {
            $('#table_bundle_components tbody tr.bundle-category-header[data-category="' + cat + '"]').remove();
        }
    }
    recalculateBundleTotals();
    if ($('#table_bundle_components tbody tr.bundle-row').length === 0) {
        $('#bundle_components_tbody').html('<tr id="empty_bundle_row"><td colspan="10" class="text-center p-4 text-muted"><i class="fa fa-inbox fa-3x mb-2 text-muted"></i><br><strong>No component items added yet.</strong></td></tr>');
    }
}

function triggerBundleSearch() {
    var query = ($('#bundle_product_search_query').val() || '').trim();
    searchProductsForBundle(query);
}

function searchProductsForBundle(query) {
    query = (query || '').trim();
    var categoryId = parseInt($('#bundle_product_search_category_id').val()) || 0;
    var existingIds = getExistingBundleProductIds();

    var postData = {
        query: query,
        category_id: categoryId,
        exclude_id: <?= $product->id; ?>,
        exclude_product_ids: existingIds.join(',')
    };
    if (typeof MdsConfig !== 'undefined' && MdsConfig.csrfTokenName) {
        postData[MdsConfig.csrfTokenName] = $('meta[name="X-CSRF-TOKEN"]').attr('content') || '<?= csrf_hash(); ?>';
    } else {
        postData['<?= csrf_token(); ?>'] = '<?= csrf_hash(); ?>';
    }

    $.ajax({
        url: typeof generateUrl === 'function' ? generateUrl('ajax/search-bundle-products') : '<?= base_url("ajax/search-bundle-products"); ?>',
        type: 'POST',
        data: postData,
        dataType: 'json',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            if (response && response.status === 'success' && response.data) {
                var currentExistingIds = getExistingBundleProductIds();
                window.bundleSearchResultsList = response.data.filter(function(item) {
                    return currentExistingIds.indexOf(item.id) === -1;
                });
                window.bundleSelectedMap = {};
                $('#bundle_check_all_products').prop('checked', false);
                updateBatchSelectionUI();
                renderBundleSearchResultsList(query);
            } else {
                $('#bundle_search_results').html('<div class="text-muted text-center py-3"><i class="fa fa-info-circle"></i> No products found' + (query ? ' for "' + $('<div>').text(query).html() + '"' : '') + '</div>');
            }
        },
        error: function(xhr, status, error) {
            $('#bundle_search_results').html('<div class="text-danger text-center py-3"><i class="fa fa-warning"></i> Search error: ' + error + '</div>');
        }
    });
}

function renderBundleSearchResultsList(query) {
    if (!window.bundleSearchResultsList || window.bundleSearchResultsList.length === 0) {
        var msg = 'No matching products available to add.';
        var existingCount = getExistingBundleProductIds().length;
        if (existingCount > 0) {
            msg = 'All matching products from this category / search have already been added to the package.';
        }
        $('#bundle_search_results').html('<div class="text-muted text-center py-4"><i class="fa fa-check-circle text-success fa-2x mb-2" style="display:block;"></i>' + msg + '</div>');
        return;
    }

    var defaultBatchQty = parseInt($('#batch_bundle_item_qty').val());
    if (isNaN(defaultBatchQty) || defaultBatchQty < 0) defaultBatchQty = 1;
    var defaultBatchOpt = $('#batch_bundle_item_is_optional').is(':checked');

    var html = '<div style="margin-bottom:0; overflow:visible;">';
    html += '<table class="table table-hover table-bordered table-striped bundle-search-table" style="margin-bottom:0; background:#fff; font-size:12px; width:100%;">';
    html += '<thead>';
    html += '<tr class="active" style="color:#475569; font-weight:bold; font-size:11.5px; text-transform:uppercase; letter-spacing:0.4px;">';
    html += '<th style="width:36px; text-align:center; vertical-align:middle;">#</th>';
    html += '<th style="width:48px; text-align:center; vertical-align:middle;">Img</th>';
    html += '<th style="vertical-align:middle; min-width:240px;">Product &amp; Variant Details</th>';
    html += '<th style="width:85px; text-align:center; vertical-align:middle;">Qty</th>';
    html += '<th style="width:130px; text-align:center; vertical-align:middle;">Price (₹)</th>';
    html += '<th style="width:75px; text-align:center; vertical-align:middle;">Optional</th>';
    html += '<th style="width:80px; text-align:center; vertical-align:middle;">Action</th>';
    html += '</tr>';
    html += '</thead>';
    html += '<tbody>';

    $.each(window.bundleSearchResultsList, function(idx, item) {
        var isChecked = window.bundleSelectedMap[item.id] ? 'checked' : '';
        var imgTag = item.image_small ? '<img src="' + item.image_small + '" style="width:40px; height:40px; object-fit:cover; border-radius:4px; display:inline-block;">' : '<div style="width:40px; height:40px; background:#f1f5f9; border-radius:4px; display:inline-block; line-height:40px; text-align:center; color:#94a3b8;"><i class="fa fa-cube"></i></div>';
        
        var formattedItemPrice = parseFloat(item.price || 0).toFixed(2);

        var variantSelectorHtml = '';
        if (item.variants && item.variants.length > 0) {
            variantSelectorHtml = '<div style="margin-top:4px;" onclick="event.stopPropagation();">' +
                '<select class="form-control input-sm bundle-item-variant-select" id="bundle_item_var_' + idx + '" style="font-size:11px; height:26px; padding:2px 6px; max-width:320px; display:inline-block;" onchange="onVariantChangeInSearch(' + idx + ', this);">' +
                '<option value="" data-sku="' + (item.sku || '') + '" data-price="' + formattedItemPrice + '" data-stock="' + item.stock + '">-- Base Product (SKU: ' + (item.sku || 'N/A') + ') --</option>';
            $.each(item.variants, function(vIdx, v) {
                var vPrice = parseFloat(v.price || 0).toFixed(2);
                variantSelectorHtml += '<option value="' + v.id + '" data-sku="' + (v.sku || '') + '" data-price="' + vPrice + '" data-stock="' + v.quantity + '">' + $('<div>').text(v.name).html() + ' (SKU: ' + (v.sku || 'N/A') + ' - Stock: ' + v.quantity + ')</option>';
            });
            variantSelectorHtml += '</select></div>';
        }

        var rowQty = defaultBatchOpt ? 0 : defaultBatchQty;

        html += '<tr class="bundle-search-item-row" data-prod-id="' + item.id + '" data-idx="' + idx + '" style="cursor:pointer;" onclick="onBundleRowClick(event, ' + idx + ');">';
        
        html += '<td style="width:36px; text-align:center; vertical-align:middle;" onclick="event.stopPropagation();">';
        html += '<input type="checkbox" class="bundle-product-chk" id="bundle_chk_' + idx + '" data-idx="' + idx + '" ' + isChecked + ' onchange="onBundleItemCheckChange(' + idx + ', this.checked);" style="width:17px; height:17px; margin:0 auto; cursor:pointer; display:block;">';
        html += '</td>';

        html += '<td style="width:48px; text-align:center; vertical-align:middle; padding:4px;">';
        html += imgTag;
        html += '</td>';

        html += '<td style="vertical-align:middle;">';
        html += '<div><strong class="text-dark" style="font-size:13px;">' + $('<div>').text(item.title).html() + '</strong></div>';
        html += '<div class="text-muted" style="font-size:11px; margin-top:2px;">SKU: <code id="bundle_item_sku_disp_' + idx + '">' + (item.sku || 'N/A') + '</code> | Stock: <span id="bundle_item_stock_disp_' + idx + '">' + item.stock + '</span> | Price: <strong style="color:#0284c7;" id="bundle_item_price_disp_' + idx + '">' + formattedItemPrice + '</strong></div>';
        html += variantSelectorHtml;
        html += '</td>';

        html += '<td style="width:85px; text-align:center; vertical-align:middle;" onclick="event.stopPropagation();">';
        html += '<input type="number" id="bundle_row_qty_' + idx + '" class="form-control input-sm text-center font-weight-bold bundle-row-qty" min="' + (defaultBatchOpt ? 0 : 1) + '" value="' + rowQty + '" style="width:68px; margin:0 auto; height:30px; padding:2px 4px; font-size:13px; font-weight:bold; display:inline-block;" title="Quantity in package" onchange="onRowQtyChange(' + idx + ');">';
        html += '</td>';

        html += '<td style="width:130px; text-align:center; vertical-align:middle;" onclick="event.stopPropagation();">';
        html += '<input type="number" step="0.01" min="0" id="bundle_row_price_' + idx + '" class="form-control input-sm text-right font-weight-bold bundle-row-price" placeholder="' + formattedItemPrice + '" onblur="normalizePriceInput(this);" style="width:115px; margin:0 auto; height:30px; padding:2px 6px; font-family:monospace; font-size:12px; display:inline-block;" title="Price override with .00">';
        html += '</td>';

        html += '<td style="width:75px; text-align:center; vertical-align:middle;" onclick="event.stopPropagation();">';
        html += '<div class="custom-control custom-checkbox" style="display:inline-block;">';
        html += '<input type="checkbox" class="custom-control-input bundle-row-opt" id="bundle_row_opt_' + idx + '" ' + (defaultBatchOpt ? 'checked' : '') + ' onchange="onRowOptionalChange(' + idx + ', this.checked);">';
        html += '<label class="custom-control-label" for="bundle_row_opt_' + idx + '" title="Optional"></label>';
        html += '</div>';
        html += '</td>';

        html += '<td style="width:80px; text-align:center; vertical-align:middle;" onclick="event.stopPropagation();">';
        html += '<button type="button" class="btn btn-sm btn-primary font-weight-bold" onclick="quickAddSingleBundleItem(' + idx + ');" style="padding:4px 10px; font-size:12px;" title="Add"><i class="fa fa-plus"></i></button>';
        html += '</td>';

        html += '</tr>';
    });
    html += '</tbody></table></div>';
    $('#bundle_search_results').html(html);
}

function onRowQtyChange(idx) {
    var $qtyInput = $('#bundle_row_qty_' + idx);
    var val = parseInt($qtyInput.val());
    var isOpt = $('#bundle_row_opt_' + idx).is(':checked');
    if (isNaN(val) || val < 0) {
        $qtyInput.val(isOpt ? 0 : 1);
    } else if (!isOpt && val < 1) {
        $qtyInput.val(1);
    }
}

function onRowOptionalChange(idx, checked) {
    var $qtyInput = $('#bundle_row_qty_' + idx);
    if (checked) {
        $qtyInput.attr('min', 0);
        if (parseInt($qtyInput.val()) <= 0) {
            $qtyInput.val(0);
        }
    } else {
        $qtyInput.attr('min', 1);
        if (parseInt($qtyInput.val()) < 1) {
            $qtyInput.val(1);
        }
    }
}

function onBundleRowClick(e, idx) {
    if ($(e.target).is('input, select, button, a, label, .custom-control-label')) return;
    var $chk = $('#bundle_chk_' + idx);
    var newChecked = !$chk.is(':checked');
    $chk.prop('checked', newChecked);
    onBundleItemCheckChange(idx, newChecked);
}

function onVariantChangeInSearch(idx, selectEl) {
    var $opt = $(selectEl).find('option:selected');
    var sku = $opt.data('sku');
    var price = parseFloat($opt.data('price') || 0).toFixed(2);
    var stock = $opt.data('stock');
    if (sku !== undefined) $('#bundle_item_sku_disp_' + idx).text(sku || 'N/A');
    if (price !== undefined) {
        $('#bundle_item_price_disp_' + idx).text(price);
        $('#bundle_row_price_' + idx).attr('placeholder', price);
    }
    if (stock !== undefined) $('#bundle_item_stock_disp_' + idx).text(stock);
}

function toggleSelectAllBundleProducts(checked) {
    $('.bundle-product-chk').prop('checked', checked);
    if (checked) {
        if (window.bundleSearchResultsList && window.bundleSearchResultsList.length) {
            window.bundleSearchResultsList.forEach(function(item, idx) {
                window.bundleSelectedMap[item.id] = {
                    idx: idx,
                    item: item,
                    variantSelectId: '#bundle_item_var_' + idx,
                    qtyInputId: '#bundle_row_qty_' + idx,
                    priceInputId: '#bundle_row_price_' + idx,
                    optCheckId: '#bundle_row_opt_' + idx
                };
            });
        }
    } else {
        window.bundleSelectedMap = {};
    }
    updateBatchSelectionUI();
}

function onBundleItemCheckChange(idx, checked) {
    if (!window.bundleSearchResultsList || !window.bundleSearchResultsList[idx]) return;
    var item = window.bundleSearchResultsList[idx];
    if (checked) {
        window.bundleSelectedMap[item.id] = {
            idx: idx,
            item: item,
            variantSelectId: '#bundle_item_var_' + idx,
            qtyInputId: '#bundle_row_qty_' + idx,
            priceInputId: '#bundle_row_price_' + idx,
            optCheckId: '#bundle_row_opt_' + idx
        };
    } else {
        delete window.bundleSelectedMap[item.id];
    }
    
    var totalVisible = $('.bundle-product-chk').length;
    var totalChecked = $('.bundle-product-chk:checked').length;
    $('#bundle_check_all_products').prop('checked', totalVisible > 0 && totalVisible === totalChecked);
    updateBatchSelectionUI();
}

function updateBatchSelectionUI() {
    var count = Object.keys(window.bundleSelectedMap).length;
    if (count > 0) {
        $('#bundle_selected_count_badge').text(count + ' selected').show();
        $('#batch_selected_btn_count').text(count);
        $('#btn_batch_add_bundle_items').show();
        $('#btn_modal_footer_batch_add').html('<i class="fa fa-plus-circle"></i> Add ' + count + ' Selected Items to Package').show();
    } else {
        $('#bundle_selected_count_badge').hide();
        $('#btn_batch_add_bundle_items').hide();
        $('#btn_modal_footer_batch_add').hide();
    }
}

function onBatchOptionalChange(checked) {
    if (checked) {
        $('#batch_bundle_item_qty').val(0).attr('min', 0);
    } else {
        if (parseInt($('#batch_bundle_item_qty').val()) <= 0) {
            $('#batch_bundle_item_qty').val(1);
        }
        $('#batch_bundle_item_qty').attr('min', 1);
    }
    $('.bundle-row-opt').each(function() {
        var idx = $(this).attr('id').replace('bundle_row_opt_', '');
        var isRowChecked = $('#bundle_chk_' + idx).is(':checked');
        if (isRowChecked || $('.bundle-product-chk:checked').length === 0) {
            $(this).prop('checked', checked);
            onRowOptionalChange(idx, checked);
        }
    });
}

function onBundleOptionalChange(checkbox) {
    var $row = $(checkbox).closest('tr');
    var $qtyInput = $row.find('.bundle-qty-input');
    var isChecked = $(checkbox).is(':checked');
    
    if (isChecked) {
        $row.removeClass('row-mandatory').addClass('row-optional');
        $row.attr('data-is-optional', 1);
        $qtyInput.attr('min', 0);
        if (parseInt($qtyInput.val()) <= 0) {
            $qtyInput.val(0);
        }
    } else {
        $row.removeClass('row-optional').addClass('row-mandatory');
        $row.attr('data-is-optional', 0);
        $qtyInput.attr('min', 1);
        if (parseInt($qtyInput.val()) < 1) {
            $qtyInput.val(1);
        }
    }
    recalculateBundleTotals();
}

function renderBundleComponentRowHtml(item, variantId, variantName, sku, price, stock, qty, isOptional, priceOverride) {
    var rawPrice = (priceOverride !== '' && priceOverride !== null && priceOverride !== undefined) ? parseFloat(priceOverride) : parseFloat(price);
    var formattedUnitPrice = isNaN(rawPrice) ? '0.00' : rawPrice.toFixed(2);
    var totalPriceNum = rawPrice * qty;
    var formattedTotal = totalPriceNum.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    
    var imgHtml = item.image_small ? '<img src="' + item.image_small + '" style="width:40px; height:40px; object-fit:cover; border-radius:6px; border:1px solid #e2e8f0;">' : '<div style="width:40px; height:40px; background:#f1f5f9; border-radius:6px; display:inline-flex; align-items:center; justify-content:center; color:#94a3b8; border:1px solid #e2e8f0;"><i class="fa fa-cube"></i></div>';
    var catName = item.category_name || 'General';
    var varBadge = variantName ? '<div style="margin-top:3px;"><span class="badge badge-info" style="font-size:10px;"><i class="fa fa-tag"></i> ' + $('<div>').text(variantName).html() + '</span></div>' : '';

    var rowIdx = bundleRowIndex++;
    return '<tr class="bundle-row ' + (isOptional ? 'row-optional' : 'row-mandatory') + '" data-id="0" data-title="' + (item.title || '').toLowerCase() + '" data-sku="' + (sku || '').toLowerCase() + '" data-category="' + catName.toLowerCase() + '" data-is-optional="' + (isOptional ? 1 : 0) + '">' +
        '<td class="text-center text-muted font-weight-bold" style="font-size:12px;">' + ($('#table_bundle_components tbody tr.bundle-row').length + 1) + '</td>' +
        '<td class="text-center" style="padding:4px;">' + imgHtml + '</td>' +
        '<td>' +
        '<div class="font-weight-bold text-dark" style="font-size:13px; line-height:1.3;">' + $('<div>').text(item.title).html() + '</div>' +
        varBadge +
        '<input type="hidden" name="bundle_items[' + rowIdx + '][component_product_id]" value="' + item.id + '">' +
        '<input type="hidden" name="bundle_items[' + rowIdx + '][variant_id]" value="' + (variantId || '') + '">' +
        '</td>' +
        '<td><code style="font-size:11.5px; background:#f8fafc; border:1px solid #e2e8f0; padding:2px 5px; border-radius:4px;">' + (sku || 'N/A') + '</code></td>' +
        '<td class="text-center">' +
        '<div class="custom-control custom-checkbox" style="display:inline-block;">' +
        '<input type="checkbox" class="custom-control-input bundle-optional-checkbox" id="bundle_opt_' + rowIdx + '" name="bundle_items[' + rowIdx + '][is_optional]" value="1" ' + (isOptional ? 'checked' : '') + ' onchange="onBundleOptionalChange(this);">' +
        '<label class="custom-control-label" for="bundle_opt_' + rowIdx + '" title="Optional"></label>' +
        '</div>' +
        '</td>' +
        '<td class="text-center"><input type="number" min="' + (isOptional ? '0' : '1') + '" max="9999" name="bundle_items[' + rowIdx + '][quantity]" class="form-control form-control-sm text-center font-weight-bold bundle-qty-input" value="' + qty + '" onchange="recalculateBundleTotals();" style="width:65px; margin:0 auto; font-size:13px;"></td>' +
        '<td class="text-right">' +
        '<input type="number" step="0.01" min="0" name="bundle_items[' + rowIdx + '][price_override]" class="form-control form-control-sm bundle-price-input text-right font-weight-bold" value="' + formattedUnitPrice + '" placeholder="0.00" onchange="normalizePriceInput(this); recalculateBundleTotals();" onblur="normalizePriceInput(this);" title="Unit price amount">' +
        '</td>' +
        '<td class="text-right font-weight-bold text-dark item-row-total" style="font-family:\'Segoe UI\', \'Consolas\', monospace; font-size:13px; white-space:nowrap;">' + formattedTotal + '</td>' +
        '<td class="text-center"><span class="badge ' + (stock >= qty ? 'badge-success' : 'badge-danger') + '" style="font-size:10.5px; padding:3px 6px;">' + stock + ' in stock</span></td>' +
        '<td class="text-center"><button type="button" class="btn btn-xs btn-outline-danger" onclick="removeBundleRow(this);" title="Remove"><i class="fa fa-trash"></i></button></td>' +
        '</tr>';
}

function addSelectedBundleProducts() {
    var selectedKeys = Object.keys(window.bundleSelectedMap);
    if (!selectedKeys.length) {
        alert('Please select at least one product using the checkboxes.');
        return;
    }
    
    $('#empty_bundle_row').remove();
    var addedCount = 0;
    var newlyAddedIds = [];
    
    selectedKeys.forEach(function(pid) {
        var entry = window.bundleSelectedMap[pid];
        if (!entry || !entry.item) return;
        var item = entry.item;
        var idx = entry.idx;
        
        var variantId = null;
        var variantName = '';
        var sku = item.sku || '';
        var price = item.price || 0;
        var stock = item.stock || 0;
        
        var $varSelect = $(entry.variantSelectId);
        if ($varSelect.length && $varSelect.val()) {
            variantId = $varSelect.val();
            var $opt = $varSelect.find('option:selected');
            variantName = $opt.text();
            sku = $opt.data('sku') || sku;
            price = $opt.data('price') !== undefined ? $opt.data('price') : price;
            stock = $opt.data('stock') !== undefined ? $opt.data('stock') : stock;
        }

        var $optCheck = $(entry.optCheckId);
        var isOptional = $optCheck.length && $optCheck.is(':checked') ? 1 : 0;

        var $qtyInput = $(entry.qtyInputId);
        var qty = $qtyInput.length ? parseInt($qtyInput.val()) : 1;
        if (isNaN(qty) || qty < 0) {
            qty = isOptional ? 0 : 1;
        }
        if (!isOptional && qty < 1) {
            qty = 1;
        }

        var $priceInput = $(entry.priceInputId);
        var priceOverride = $priceInput.length ? ($priceInput.val() || '').trim() : '';
        
        var rowHtml = renderBundleComponentRowHtml(item, variantId, variantName, sku, price, stock, qty, isOptional, priceOverride);
        insertBundleRowIntoTable(rowHtml, item.category_name);
        addedCount++;
        newlyAddedIds.push(item.id);
    });
    
    recalculateBundleTotals();
    
    window.bundleSelectedMap = {};
    $('#bundle_check_all_products').prop('checked', false);
    updateBatchSelectionUI();
    
    if (window.bundleSearchResultsList && window.bundleSearchResultsList.length) {
        window.bundleSearchResultsList = window.bundleSearchResultsList.filter(function(it) {
            return newlyAddedIds.indexOf(it.id) === -1;
        });
        renderBundleSearchResultsList();
    }
    
    $('#bundle_modal_feedback').html(
        '<div class="alert alert-success alert-dismissible fade show py-2 px-3 small font-weight-bold" role="alert">' +
        '<i class="fa fa-check-circle"></i> Successfully added ' + addedCount + ' product(s) to package! ' +
        '<button type="button" class="close py-2" data-dismiss="alert" aria-label="Close">' +
        '<span aria-hidden="true">&times;</span>' +
        '</button>' +
        '</div>'
    ).show();
}

function quickAddSingleBundleItem(idx) {
    if (!window.bundleSearchResultsList || !window.bundleSearchResultsList[idx]) return;
    var item = window.bundleSearchResultsList[idx];
    
    var variantId = null;
    var variantName = '';
    var sku = item.sku || '';
    var price = item.price || 0;
    var stock = item.stock || 0;
    
    var $varSelect = $('#bundle_item_var_' + idx);
    if ($varSelect.length && $varSelect.val()) {
        variantId = $varSelect.val();
        var $opt = $varSelect.find('option:selected');
        variantName = $opt.text();
        sku = $opt.data('sku') || sku;
        price = $opt.data('price') !== undefined ? $opt.data('price') : price;
        stock = $opt.data('stock') !== undefined ? $opt.data('stock') : stock;
    }
    
    var isOptional = $('#bundle_row_opt_' + idx).is(':checked') ? 1 : 0;
    var qty = parseInt($('#bundle_row_qty_' + idx).val());
    if (isNaN(qty) || qty < 0) {
        qty = isOptional ? 0 : 1;
    }
    if (!isOptional && qty < 1) {
        qty = 1;
    }
    
    var priceOverride = ($('#bundle_row_price_' + idx).val() || '').trim();
    
    $('#empty_bundle_row').remove();
    var rowHtml = renderBundleComponentRowHtml(item, variantId, variantName, sku, price, stock, qty, isOptional, priceOverride);
    insertBundleRowIntoTable(rowHtml, item.category_name);
    recalculateBundleTotals();
    
    delete window.bundleSelectedMap[item.id];
    window.bundleSearchResultsList = window.bundleSearchResultsList.filter(function(it) {
        return it.id !== item.id;
    });
    renderBundleSearchResultsList();
    updateBatchSelectionUI();
    
    $('#bundle_modal_feedback').html(
        '<div class="alert alert-success alert-dismissible fade show py-2 px-3 small font-weight-bold" role="alert">' +
        '<i class="fa fa-check-circle"></i> Added "' + $('<div>').text(item.title).html() + '" (Qty: ' + qty + ') to package!' +
        '<button type="button" class="close py-2" data-dismiss="alert" aria-label="Close">' +
        '<span aria-hidden="true">&times;</span>' +
        '</button>' +
        '</div>'
    ).show();
}

function insertBundleRowIntoTable(rowHtml, catName) {
    catName = catName || 'General';
    var catKey = catName.toLowerCase().trim();
    var $existingHeader = $('#table_bundle_components tbody tr.bundle-category-header[data-category="' + catKey + '"]');

    if ($existingHeader.length === 0) {
        var headerHtml = '<tr class="bundle-category-header" data-category="' + catKey + '">' +
            '<td colspan="10">' +
            '<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">' +
            '<div style="display:flex; align-items:center; gap:8px;">' +
            '<span style="font-weight:700; color:#0f172a; font-size:12.5px; text-transform:uppercase; letter-spacing:0.5px;">' +
            '<i class="fa fa-folder-open text-primary"></i>&nbsp;&nbsp;' + $('<div>').text(catName).html() +
            '</span>' +
            '<span style="font-size:11.5px; font-weight:700; color:#1e293b; background:#e2e8f0; border:1px solid #cbd5e1; padding:3px 9px; border-radius:12px;">' +
            '<span class="cat-item-count">1</span> <span class="cat-item-count-label">Product</span> (<span class="cat-unit-count">1</span> <span class="cat-unit-count-label">Unit</span>)' +
            '</span>' +
            '</div>' +
            '<div style="display:flex; align-items:center; gap:8px;">' +
            '<span class="cat-mand-wrapper text-muted small" style="font-size:11.5px; display:none;">' +
            'Base/Mandatory: <strong class="cat-mand-val text-dark" style="font-family:\'Segoe UI\', \'Consolas\', monospace;">₹ 0.00</strong>' +
            '<span class="cat-mand-sep text-muted mx-1">|</span>' +
            '</span>' +
            '<span style="font-size:12px; font-weight:700; color:#1e3a8a; background:#e0e7ff; border:1px solid #c7d2fe; padding:3px 10px; border-radius:6px; display:inline-flex; align-items:center; gap:5px;">' +
            '<span>Category Total:</span>' +
            '<span class="cat-total-val" style="font-family:\'Segoe UI\', \'Consolas\', monospace; font-size:13px; color:#1e1b4b;">₹ 0.00</span>' +
            '</span>' +
            '</div>' +
            '</div>' +
            '</td>' +
            '</tr>';
        $('#bundle_components_tbody').append(headerHtml);
        $('#bundle_components_tbody').append(rowHtml);
    } else {
        var $lastRow = $existingHeader;
        var $scan = $existingHeader.next();
        while ($scan.length && !$scan.hasClass('bundle-category-header')) {
            $lastRow = $scan;
            $scan = $scan.next();
        }
        $lastRow.after(rowHtml);
    }
}

function recalculateBundleTotals() {
    var count = 0;
    var totalUnits = 0;
    var sumPrice = 0;
    var mandCount = 0;
    var optCount = 0;
    var mandSum = 0;
    
    var catTotals = {};
    var catMandTotals = {};
    var catCounts = {};
    var catUnits = {};
    var catOptCounts = {};
    
    $('#table_bundle_components tbody tr.bundle-row').each(function() {
        count++;
        var isOpt = parseInt($(this).attr('data-is-optional')) || 0;
        var qty = parseInt($(this).find('.bundle-qty-input').val()) || 0;
        var priceInput = $(this).find('.bundle-price-input');
        var price = parseFloat(priceInput.val()) || 0;
        var cat = ($(this).attr('data-category') || 'general').toLowerCase().trim();
        
        var rowTotal = qty * price;
        $(this).find('.item-row-total').text(rowTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        
        totalUnits += qty;
        sumPrice += rowTotal;
        
        if (typeof catCounts[cat] === 'undefined') {
            catTotals[cat] = 0;
            catMandTotals[cat] = 0;
            catCounts[cat] = 0;
            catUnits[cat] = 0;
            catOptCounts[cat] = 0;
        }
        catTotals[cat] += rowTotal;
        catCounts[cat]++;
        catUnits[cat] += qty;
        
        if (isOpt === 1) {
            optCount++;
            catOptCounts[cat]++;
        } else {
            mandCount++;
            mandSum += rowTotal;
            catMandTotals[cat] += rowTotal;
        }
    });

    // Update live category totals & unit counts in category section headers
    $('#table_bundle_components tbody tr.bundle-category-header').each(function() {
        var cat = ($(this).attr('data-category') || '').toLowerCase().trim();
        var catTotal = catTotals[cat] || 0;
        var catMand = catMandTotals[cat] || 0;
        var catCount = catCounts[cat] || 0;
        var catUnit = catUnits[cat] || 0;
        var catOpt = catOptCounts[cat] || 0;

        $(this).find('.cat-total-val').text('₹ ' + catTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $(this).find('.cat-item-count').text(catCount);
        $(this).find('.cat-item-count-label').text(catCount === 1 ? 'Product' : 'Products');
        $(this).find('.cat-unit-count').text(catUnit);
        $(this).find('.cat-unit-count-label').text(catUnit === 1 ? 'Unit' : 'Units');
        $(this).find('.cat-mand-val').text('₹ ' + catMand.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

        if (catOpt > 0) {
            $(this).find('.cat-mand-wrapper').show();
        } else {
            $(this).find('.cat-mand-wrapper').hide();
        }
    });

    // Update Filter Pill counts
    $('#pill_count_all').text(count);
    $('#pill_count_mand').text(mandCount);
    $('#pill_count_opt').text(optCount);

    // Update Top Metric Cards
    $('#badge_total_components').text(count);
    $('#badge_mand_count').text(mandCount);
    $('#badge_opt_count').text(optCount);
    $('#badge_total_units').text(totalUnits);
    $('#badge_sum_price').text('₹ ' + sumPrice.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    $('#card_mand_sum_disp').text('₹ ' + mandSum.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    if (optCount > 0) {
        $('#card_mand_box').show();
    } else {
        $('#card_mand_box').hide();
    }
    
    // Update Bottom Sticky Bar
    $('#sticky_visible_count').text(count);
    $('#sticky_visible_units').text(totalUnits);
    $('#sticky_mand_sum').text('₹ ' + mandSum.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    
    // If a filter is currently active, re-filter so visible counts match
    filterBundleTable();
}

function applyBundleSumToPrice(mode) {
    if (!mode) mode = 'total';
    var sumPrice = 0;
    var mandPrice = 0;

    $('#table_bundle_components tbody tr.bundle-row').each(function() {
        var isOpt = parseInt($(this).attr('data-is-optional')) || 0;
        var qty = parseInt($(this).find('.bundle-qty-input').val()) || 1;
        var price = parseFloat($(this).find('.bundle-price-input').val()) || 0;
        var line = qty * price;
        sumPrice += line;
        if (isOpt === 0) {
            mandPrice += line;
        }
    });

    var targetAmount = (mode === 'mandatory') ? mandPrice : sumPrice;
    var label = (mode === 'mandatory') ? 'Mandatory Package Base' : 'Total Items Value';

    var formattedVal = targetAmount.toFixed(2);
    if ($('input[name="price"]').length) {
        $('input[name="price"]').val(formattedVal).trigger('input').trigger('change');
    }
    if ($('#product_price_input').length) {
        $('#product_price_input').val(formattedVal).trigger('input').trigger('change');
    }
    if ($('input[name="price_discounted"]').length && $('#checkbox_discount_rate').is(':checked')) {
        $('input[name="price_discounted"]').val(formattedVal).trigger('input').trigger('change');
    }
    if (typeof reFormatPriceInputs === 'function') {
        reFormatPriceInputs();
    }

    if (typeof alert !== 'undefined') {
        alert('Applied ' + label + ' (₹ ' + targetAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ') to Product Price input above!');
    }
}

function sortBundleTableByCategory() {
    var tbody = $('#bundle_components_tbody');
    var rows = tbody.find('tr.bundle-row').get();
    if (!rows.length) return;

    rows.sort(function(a, b) {
        var catA = ($(a).attr('data-category') || 'general').trim();
        var catB = ($(b).attr('data-category') || 'general').trim();
        var cmp = catA.localeCompare(catB);
        if (cmp !== 0) return cmp;
        var titleA = ($(a).attr('data-title') || '').trim();
        var titleB = ($(b).attr('data-title') || '').trim();
        return titleA.localeCompare(titleB);
    });

    tbody.empty();
    var currentCat = null;
    rows.forEach(function(row, idx) {
        var catName = $(row).attr('data-category') || 'general';
        if (catName !== currentCat) {
            currentCat = catName;
            var headerHtml = '<tr class="bundle-category-header" data-category="' + catName.toLowerCase() + '">' +
                '<td colspan="10">' +
                '<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">' +
                '<div style="display:flex; align-items:center; gap:8px;">' +
                '<span style="font-weight:700; color:#0f172a; font-size:12.5px; text-transform:uppercase; letter-spacing:0.5px;">' +
                '<i class="fa fa-folder-open text-primary"></i>&nbsp;&nbsp;' + (catName.charAt(0).toUpperCase() + catName.slice(1)) +
                '</span>' +
                '<span style="font-size:11.5px; font-weight:700; color:#1e293b; background:#e2e8f0; border:1px solid #cbd5e1; padding:3px 9px; border-radius:12px;">' +
                '<span class="cat-item-count">0</span> <span class="cat-item-count-label">Products</span> (<span class="cat-unit-count">0</span> <span class="cat-unit-count-label">Units</span>)' +
                '</span>' +
                '</div>' +
                '<div style="display:flex; align-items:center; gap:8px;">' +
                '<span class="cat-mand-wrapper text-muted small" style="font-size:11.5px; display:none;">' +
                'Base/Mandatory: <strong class="cat-mand-val text-dark" style="font-family:\'Segoe UI\', \'Consolas\', monospace;">₹ 0.00</strong>' +
                '<span class="cat-mand-sep text-muted mx-1">|</span>' +
                '</span>' +
                '<span style="font-size:12px; font-weight:700; color:#1e3a8a; background:#e0e7ff; border:1px solid #c7d2fe; padding:3px 10px; border-radius:6px; display:inline-flex; align-items:center; gap:5px;">' +
                '<span>Category Total:</span>' +
                '<span class="cat-total-val" style="font-family:\'Segoe UI\', \'Consolas\', monospace; font-size:13px; color:#1e1b4b;">₹ 0.00</span>' +
                '</span>' +
                '</div>' +
                '</div>' +
                '</td></tr>';
            tbody.append(headerHtml);
        }
        $(row).find('td:first').text(idx + 1);
        tbody.append(row);
    });
    recalculateBundleTotals();
    filterBundleTable();
}
</script>
