<?php
$bundleModel = new \App\Models\BundleModel();
$bundleComponents = $bundleModel->getBundleComponents($product->id, false);
$bundleMetrics = $bundleModel->calculateBundleMetrics($product->id);
$isBundle = !empty($product->is_bundle) ? 1 : 0;
$pricingType = !empty($product->bundle_pricing_type) ? $product->bundle_pricing_type : 'fixed';
$discountRate = !empty($product->bundle_discount_rate) ? $product->bundle_discount_rate : 0;
?>

<div class="section-product-details section-bundle-builder m-b-30">
    <div class="form-box">
        <div class="form-box-head d-flex justify-content-between align-items-center" style="display:flex; justify-content:space-between; align-items:center;">
            <h4 class="title" style="margin:0;">
                <i class="fa fa-cubes text-primary"></i>&nbsp;<?= trans("bundle_package_builder") ?? "Bundle / Package Package (100+ Items)"; ?>
            </h4>
            <div class="custom-control custom-switch custom-control-inline">
                <input type="checkbox" class="custom-control-input" id="is_bundle_toggle" name="is_bundle" value="1" <?= $isBundle ? 'checked' : ''; ?> onchange="toggleBundleSection(this.checked);">
                <label class="custom-control-label font-weight-bold" for="is_bundle_toggle">Enable as Bundle Package</label>
            </div>
        </div>

        <div class="form-box-body" id="bundle_builder_container" style="<?= $isBundle ? '' : 'display:none;'; ?> padding-top:15px;">
            <p class="text-muted m-b-20">
                Create combo kits, hampers, or packages containing up to 100+ simple and variant products. Inventory is automatically tracked and synced across all child items.
            </p>

            <!-- Metrics Bar -->
            <div class="row m-b-20">
                <div class="col-md-3 col-sm-6 m-b-10">
                    <div class="card p-3 bg-light border-0 text-center rounded">
                        <small class="text-muted text-uppercase">Total Components</small>
                        <h3 class="m-0 font-weight-bold text-primary" id="badge_total_components"><?= count($bundleComponents); ?></h3>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 m-b-10">
                    <div class="card p-3 bg-light border-0 text-center rounded">
                        <small class="text-muted text-uppercase">Total Package Units</small>
                        <h3 class="m-0 font-weight-bold text-info" id="badge_total_units"><?= $bundleMetrics['total_units']; ?></h3>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 m-b-10">
                    <div class="card p-3 bg-light border-0 text-center rounded">
                        <small class="text-muted text-uppercase">Sum of Items Value</small>
                        <h3 class="m-0 font-weight-bold text-dark" id="badge_sum_price"><?= priceFormatted($bundleMetrics['sum_price'], $defaultCurrency->code, true); ?></h3>
                        <button type="button" class="btn btn-xs btn-outline-primary mt-1" onclick="applyBundleSumToPrice();" title="Copy sum of items to product price"><i class="fa fa-copy"></i> Apply to Price</button>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 m-b-10">
                    <div class="card p-3 bg-light border-0 text-center rounded">
                        <small class="text-muted text-uppercase">Calculated Stock</small>
                        <h3 class="m-0 font-weight-bold <?= $bundleMetrics['max_stock'] > 0 ? 'text-success' : 'text-danger'; ?>" id="badge_max_stock">
                            <?= $bundleMetrics['max_stock']; ?> pkgs
                        </h3>
                    </div>
                </div>
            </div>

            <!-- Action Toolbar: Bulk CSV Upload & Search Add -->
            <div class="row align-items-center m-b-20">
                <div class="col-md-6 col-sm-12 m-b-10">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalAddBundleItem">
                        <i class="fa fa-plus-circle"></i>&nbsp;&nbsp;Add Product / Variant
                    </button>
                    <button type="button" class="btn btn-outline-success m-l-10" data-toggle="modal" data-target="#modalBulkCsvBundle">
                        <i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;Bulk CSV Import (100+ Items)
                    </button>
                    <button type="button" class="btn btn-outline-secondary m-l-10" onclick="sortBundleTableByCategory();" title="Sort and group items by Category">
                        <i class="fa fa-sort-alpha-asc"></i>&nbsp;&nbsp;Sort by Category
                    </button>
                </div>
                <div class="col-md-6 col-sm-12 text-right m-b-10">
                    <input type="text" id="bundle_filter_input" class="form-control" style="max-width:280px; display:inline-block;" placeholder="Filter components in table..." onkeyup="filterBundleTable();">
                </div>
            </div>

            <!-- Components Table -->
            <div class="table-responsive" style="max-height: 520px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 6px;">
                <table class="table table-hover table-striped mb-0" id="table_bundle_components">
                    <thead class="thead-light" style="position: sticky; top: 0; z-index: 2; background:#f8f9fa;">
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th style="width: 70px;">Image</th>
                            <th>Product & Variation</th>
                            <th>SKU</th>
                            <th style="width: 90px;" class="text-center">Optional</th>
                            <th style="width: 110px;">Qty in Pkg</th>
                            <th style="width: 110px;">Unit Price</th>
                            <th style="width: 110px;">Total</th>
                            <th style="width: 110px;">Stock</th>
                            <th style="width: 70px;" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="bundle_components_tbody">
                        <?php if (!empty($bundleComponents)): ?>
                            <?php 
                            $currCatName = null;
                            foreach ($bundleComponents as $idx => $comp): 
                                $compCat = !empty($comp->category_name) ? $comp->category_name : 'General';
                                if ($compCat !== $currCatName):
                                    $currCatName = $compCat;
                            ?>
                                <tr class="bundle-category-header" style="background:#f1f5f9; border-top:2px solid #cbd5e1; border-bottom:1px solid #cbd5e1;">
                                    <td colspan="10" style="padding:6px 12px; font-weight:700; color:#1e3a8a; font-size:12px; text-transform:uppercase; letter-spacing:0.5px;">
                                        <i class="fa fa-folder-open-o text-primary"></i>&nbsp;&nbsp;<?= esc($currCatName); ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                                <tr class="bundle-row" data-id="<?= $comp->id; ?>" data-title="<?= esc(strtolower($comp->title)); ?>" data-sku="<?= esc(strtolower($comp->sku)); ?>" data-category="<?= esc(strtolower($comp->category_name ?? '')); ?>">
                                    <td><?= $idx + 1; ?></td>
                                    <td>
                                        <?php if (!empty($comp->image_small)):
                                            $compImgUrl = (str_starts_with($comp->image_small, 'http://') || str_starts_with($comp->image_small, 'https://')) ? $comp->image_small : (str_starts_with($comp->image_small, 'uploads/') ? base_url($comp->image_small) : base_url('uploads/images/' . $comp->image_small));
                                        ?>
                                            <img src="<?= $compImgUrl; ?>" style="width:45px; height:45px; object-fit:cover; border-radius:4px;" alt="">
                                        <?php else: ?>
                                            <div style="width:45px; height:45px; background:#eee; border-radius:4px; display:flex; align-items:center; justify-content:center; color:#999;"><i class="fa fa-image"></i></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold"><?= esc($comp->title); ?></div>
                                        <div style="display:flex; gap:4px; flex-wrap:wrap; margin-top:2px;">
                                            <?php if (!empty($comp->category_name)): ?>
                                                <span class="badge badge-light border text-secondary" style="font-size:10.5px;"><i class="fa fa-folder-o"></i> <?= esc($comp->category_name); ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($comp->variant_name)): ?>
                                                <span class="badge badge-info" style="font-size:10.5px;"><i class="fa fa-tag"></i> <?= esc($comp->variant_name); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <input type="hidden" name="bundle_items[<?= $idx; ?>][component_product_id]" value="<?= $comp->component_product_id; ?>">
                                        <input type="hidden" name="bundle_items[<?= $idx; ?>][variant_id]" value="<?= $comp->variant_id; ?>">
                                    </td>
                                    <td><code><?= esc($comp->sku); ?></code></td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input bundle-optional-checkbox" id="bundle_opt_<?= $idx; ?>" name="bundle_items[<?= $idx; ?>][is_optional]" value="1" <?= !empty($comp->is_optional) ? 'checked' : ''; ?> onchange="onBundleOptionalChange(this);">
                                            <label class="custom-control-label small" for="bundle_opt_<?= $idx; ?>" title="Optional item starts with 0 qty for buyer"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" min="<?= !empty($comp->is_optional) ? '0' : '1'; ?>" max="9999" name="bundle_items[<?= $idx; ?>][quantity]" class="form-control form-control-sm bundle-qty-input" value="<?= $comp->required_quantity; ?>" onchange="recalculateBundleTotals();">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" name="bundle_items[<?= $idx; ?>][price_override]" class="form-control form-control-sm bundle-price-input" value="<?= $comp->price_override !== null ? $comp->price_override : $comp->unit_price; ?>" onchange="recalculateBundleTotals();">
                                    </td>
                                    <td class="font-weight-bold text-muted item-row-total">
                                        <?= priceFormatted($comp->total_price, $defaultCurrency->code, true); ?>
                                    </td>
                                    <td>
                                        <span class="badge <?= $comp->can_fulfill ? 'badge-success' : 'badge-danger'; ?>">
                                            <?= $comp->available_stock; ?> in stock
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeBundleRow(this);" title="Remove">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr id="empty_bundle_row">
                                <td colspan="10" class="text-center p-4 text-muted">
                                    <i class="fa fa-inbox fa-2x mb-2"></i><br>
                                    No component items added yet. Click <strong>Add Product / Variant</strong> or use <strong>Bulk CSV Import</strong> to add 100+ items.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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
#modalAddBundleItem .modal-footer button {
    margin: 0 !important;
}
.bundle-search-table {
    width: 100% !important;
    margin-bottom: 0 !important;
    table-layout: auto;
}
.bundle-search-table th, .bundle-search-table td {
    vertical-align: middle !important;
}
</style>
<div class="modal fade" id="modalAddBundleItem" tabindex="-1" role="dialog" aria-labelledby="modalAddBundleItemLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" style="width: 95% !important; max-width: 1400px !important; margin: 20px auto !important;">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title" id="modalAddBundleItemLabel">
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
                            <label class="small mb-0 font-weight-bold text-muted" style="margin-bottom:0;">Default Qty:</label>
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

                <!-- Feedback Alert Container -->
                <div id="bundle_modal_feedback" style="display:none; margin-top:8px;"></div>

                <!-- Search Results List with Single Row Table (Wide & No Scrollbar) -->
                <div id="bundle_search_results">
                    <div class="text-muted text-center py-3"><i class="fa fa-spinner fa-spin"></i> Loading products...</div>
                </div>
            </div>
            <div class="modal-footer" style="display:flex; justify-content:flex-end; align-items:center; gap:10px;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success font-weight-bold" id="btn_modal_footer_batch_add" style="display:none;" onclick="addSelectedBundleProducts();">
                    <i class="fa fa-plus-circle"></i> Add Selected Items
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Bulk CSV Import -->
<div class="modal fade" id="modalBulkCsvBundle" tabindex="-1" role="dialog" aria-labelledby="modalBulkCsvBundleLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalBulkCsvBundleLabel"><i class="fa fa-file-excel-o"></i> Bulk CSV Import (100+ Items)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">
                    Upload a CSV file to instantly add 100+ products and variants to this package.
                </p>
                <div class="alert alert-info py-2 small">
                    <strong>Required Columns:</strong> <code>sku</code>, <code>variant_sku</code> (optional), <code>quantity</code>, <code>price_override</code> (optional).<br>
                    <a href="data:text/csv;charset=utf-8,sku,variant_sku,quantity,price_override%0APROD-001,,1,%0APROD-002,VAR-BLUE-L,2,15.50" download="bundle_sample_100_items.csv" class="font-weight-bold text-primary">Download Sample CSV Template</a>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Choose CSV File</label>
                    <input type="file" id="bundle_csv_file_input" class="form-control-file" accept=".csv">
                </div>
                <div id="bundle_csv_status"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="uploadBundleCsv(<?= $product->id; ?>);">Upload & Populate Package</button>
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
});

function toggleBundleSection(checked) {
    if (checked) {
        $('#bundle_builder_container').slideDown(200);
    } else {
        $('#bundle_builder_container').slideUp(200);
    }
}

function filterBundleTable() {
    var filter = ($('#bundle_filter_input').val() || '').toLowerCase().trim();
    $('#table_bundle_components tbody tr.bundle-row').each(function() {
        var title = $(this).attr('data-title') || '';
        var sku = $(this).attr('data-sku') || '';
        var cat = $(this).attr('data-category') || '';
        if (title.indexOf(filter) > -1 || sku.indexOf(filter) > -1 || cat.indexOf(filter) > -1) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
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
    $(btn).closest('tr').remove();
    recalculateBundleTotals();
    if ($('#table_bundle_components tbody tr.bundle-row').length === 0) {
        $('#bundle_components_tbody').html('<tr id="empty_bundle_row"><td colspan="10" class="text-center p-4 text-muted"><i class="fa fa-inbox fa-2x mb-2"></i><br>No component items added yet. Click <strong>Add Product / Variant</strong> or use <strong>Bulk CSV Import</strong> to add 100+ items.</td></tr>');
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
                // Client-side safeguard to ensure already added items are strictly excluded
                var currentExistingIds = getExistingBundleProductIds();
                window.bundleSearchResultsList = response.data.filter(function(item) {
                    return currentExistingIds.indexOf(item.id) === -1;
                });
                // Reset batch selections that are no longer in results
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
    html += '<th style="width:130px; text-align:center; vertical-align:middle;">Price Override</th>';
    html += '<th style="width:90px; text-align:center; vertical-align:middle;">Optional</th>';
    html += '<th style="width:85px; text-align:center; vertical-align:middle;">Action</th>';
    html += '</tr>';
    html += '</thead>';
    html += '<tbody>';

    $.each(window.bundleSearchResultsList, function(idx, item) {
        var isChecked = window.bundleSelectedMap[item.id] ? 'checked' : '';
        var catBadge = item.category_name ? '<span class="badge" style="background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; font-size:10px; font-weight:500; margin-left:4px;"><i class="fa fa-folder-o"></i> ' + $('<div>').text(item.category_name).html() + '</span>' : '';
        var imgTag = item.image_small ? '<img src="' + item.image_small + '" style="width:40px; height:40px; object-fit:cover; border-radius:4px; display:inline-block;">' : '<div style="width:40px; height:40px; background:#f1f5f9; border-radius:4px; display:inline-block; line-height:40px; text-align:center; color:#94a3b8;"><i class="fa fa-cube"></i></div>';
        
        var variantSelectorHtml = '';
        if (item.variants && item.variants.length > 0) {
            variantSelectorHtml = '<div style="margin-top:4px;" onclick="event.stopPropagation();">' +
                '<select class="form-control input-sm bundle-item-variant-select" id="bundle_item_var_' + idx + '" style="font-size:11px; height:26px; padding:2px 6px; max-width:320px; display:inline-block;" onchange="onVariantChangeInSearch(' + idx + ', this);">' +
                '<option value="" data-sku="' + (item.sku || '') + '" data-price="' + item.price + '" data-stock="' + item.stock + '">-- Base Product (SKU: ' + (item.sku || 'N/A') + ') --</option>';
            $.each(item.variants, function(vIdx, v) {
                variantSelectorHtml += '<option value="' + v.id + '" data-sku="' + (v.sku || '') + '" data-price="' + v.price + '" data-stock="' + v.quantity + '">' + $('<div>').text(v.name).html() + ' (SKU: ' + (v.sku || 'N/A') + ' - Stock: ' + v.quantity + ')</option>';
            });
            variantSelectorHtml += '</select></div>';
        }

        var rowQty = defaultBatchOpt ? 0 : defaultBatchQty;

        html += '<tr class="bundle-search-item-row" data-prod-id="' + item.id + '" data-idx="' + idx + '" style="cursor:pointer;" onclick="onBundleRowClick(event, ' + idx + ');">';
        
        // 1. Checkbox
        html += '<td style="width:36px; text-align:center; vertical-align:middle;" onclick="event.stopPropagation();">';
        html += '<input type="checkbox" class="bundle-product-chk" id="bundle_chk_' + idx + '" data-idx="' + idx + '" ' + isChecked + ' onchange="onBundleItemCheckChange(' + idx + ', this.checked);" style="width:17px; height:17px; margin:0 auto; cursor:pointer; display:block;">';
        html += '</td>';

        // 2. Image
        html += '<td style="width:48px; text-align:center; vertical-align:middle; padding:4px;">';
        html += imgTag;
        html += '</td>';

        // 3. Name & Details (Title, SKU, Stock, Base Price, Variant dropdown)
        html += '<td style="vertical-align:middle;">';
        html += '<div><strong class="text-dark" style="font-size:13px;">' + $('<div>').text(item.title).html() + '</strong>' + catBadge + '</div>';
        html += '<div class="text-muted" style="font-size:11px; margin-top:2px;">SKU: <code id="bundle_item_sku_disp_' + idx + '">' + (item.sku || 'N/A') + '</code> | Stock: <span id="bundle_item_stock_disp_' + idx + '">' + item.stock + '</span> | Price: <strong style="color:#0284c7;" id="bundle_item_price_disp_' + idx + '">' + item.price + '</strong></div>';
        html += variantSelectorHtml;
        html += '</td>';

        // 4. Qty Field
        html += '<td style="width:85px; text-align:center; vertical-align:middle;" onclick="event.stopPropagation();">';
        html += '<input type="number" id="bundle_row_qty_' + idx + '" class="form-control input-sm text-center font-weight-bold bundle-row-qty" min="' + (defaultBatchOpt ? 0 : 1) + '" value="' + rowQty + '" style="width:68px; margin:0 auto; height:30px; padding:2px 4px; font-size:13px; font-weight:bold; display:inline-block;" title="Quantity in package" onchange="onRowQtyChange(' + idx + ');">';
        html += '</td>';

        // 5. Price Override Field
        html += '<td style="width:130px; text-align:center; vertical-align:middle;" onclick="event.stopPropagation();">';
        html += '<input type="number" step="0.01" min="0" id="bundle_row_price_' + idx + '" class="form-control input-sm text-center bundle-row-price" placeholder="Default (' + item.price + ')" style="width:115px; margin:0 auto; height:30px; padding:2px 4px; font-size:12px; display:inline-block;" title="Price override (optional)">';
        html += '</td>';

        // 6. Optional Checkbox
        html += '<td style="width:90px; text-align:center; vertical-align:middle;" onclick="event.stopPropagation();">';
        html += '<label style="cursor:pointer; margin-bottom:0; font-weight:600; font-size:11.5px; display:inline-flex; align-items:center; gap:4px; color:#475569;" title="Optional items start with 0 qty for buyers">';
        html += '<input type="checkbox" class="bundle-row-opt" id="bundle_row_opt_' + idx + '" ' + (defaultBatchOpt ? 'checked' : '') + ' onchange="onRowOptionalChange(' + idx + ', this.checked);" style="width:16px; height:16px; margin:0; cursor:pointer;"> Optional';
        html += '</label>';
        html += '</td>';

        // 7. Add Button
        html += '<td style="width:85px; text-align:center; vertical-align:middle;" onclick="event.stopPropagation();">';
        html += '<button type="button" class="btn btn-sm btn-primary font-weight-bold" onclick="quickAddSingleBundleItem(' + idx + ');" style="padding:4px 10px; font-size:12px;" title="Add this item to package"><i class="fa fa-plus"></i> Add</button>';
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
    var price = $opt.data('price');
    var stock = $opt.data('stock');
    if (sku !== undefined) $('#bundle_item_sku_disp_' + idx).text(sku || 'N/A');
    if (price !== undefined) {
        $('#bundle_item_price_disp_' + idx).text(price);
        $('#bundle_row_price_' + idx).attr('placeholder', 'Default (' + price + ')');
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
    if ($(checkbox).is(':checked')) {
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
    recalculateBundleTotals();
}

function renderBundleComponentRowHtml(item, variantId, variantName, sku, price, stock, qty, isOptional, priceOverride) {
    var unitPrice = (priceOverride !== '' && priceOverride !== null && priceOverride !== undefined) ? parseFloat(priceOverride) : parseFloat(price);
    var totalPrice = (unitPrice * qty).toFixed(2);
    
    var imgHtml = item.image_small ? '<img src="' + item.image_small + '" style="width:45px; height:45px; object-fit:cover; border-radius:4px;">' : '<div style="width:45px; height:45px; background:#eee; border-radius:4px; display:flex; align-items:center; justify-content:center; color:#999;"><i class="fa fa-image"></i></div>';
    var catName = item.category_name || '';
    var catBadge = catName ? '<span class="badge badge-light border text-secondary" style="font-size:10.5px;"><i class="fa fa-folder-o"></i> ' + $('<div>').text(catName).html() + '</span>' : '';
    var varBadge = variantName ? '<span class="badge badge-info" style="font-size:10.5px;"><i class="fa fa-tag"></i> ' + $('<div>').text(variantName).html() + '</span>' : '';

    var rowIdx = bundleRowIndex++;
    return '<tr class="bundle-row" data-id="0" data-title="' + (item.title || '').toLowerCase() + '" data-sku="' + (sku || '').toLowerCase() + '" data-category="' + catName.toLowerCase() + '">' +
        '<td>' + ($('#table_bundle_components tbody tr.bundle-row').length + 1) + '</td>' +
        '<td>' + imgHtml + '</td>' +
        '<td><div class="font-weight-bold">' + $('<div>').text(item.title).html() + '</div>' +
        '<div style="display:flex; gap:4px; flex-wrap:wrap; margin-top:2px;">' + catBadge + varBadge + '</div>' +
        '<input type="hidden" name="bundle_items[' + rowIdx + '][component_product_id]" value="' + item.id + '">' +
        '<input type="hidden" name="bundle_items[' + rowIdx + '][variant_id]" value="' + (variantId || '') + '">' +
        '</td>' +
        '<td><code>' + (sku || 'N/A') + '</code></td>' +
        '<td class="text-center" style="vertical-align: middle;">' +
        '<div class="custom-control custom-checkbox">' +
        '<input type="checkbox" class="custom-control-input bundle-optional-checkbox" id="bundle_opt_' + rowIdx + '" name="bundle_items[' + rowIdx + '][is_optional]" value="1" ' + (isOptional ? 'checked' : '') + ' onchange="onBundleOptionalChange(this);">' +
        '<label class="custom-control-label small" for="bundle_opt_' + rowIdx + '" title="Optional item starts with 0 qty for buyer"></label>' +
        '</div>' +
        '</td>' +
        '<td><input type="number" min="' + (isOptional ? '0' : '1') + '" max="9999" name="bundle_items[' + rowIdx + '][quantity]" class="form-control form-control-sm bundle-qty-input" value="' + qty + '" onchange="recalculateBundleTotals();"></td>' +
        '<td><input type="number" step="0.01" min="0" name="bundle_items[' + rowIdx + '][price_override]" class="form-control form-control-sm bundle-price-input" value="' + (priceOverride !== '' && priceOverride !== null && priceOverride !== undefined ? priceOverride : unitPrice) + '" onchange="recalculateBundleTotals();"></td>' +
        '<td class="font-weight-bold text-muted item-row-total">' + totalPrice + '</td>' +
        '<td><span class="badge ' + (stock >= qty ? 'badge-success' : 'badge-danger') + '">' + stock + ' in stock</span></td>' +
        '<td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeBundleRow(this);"><i class="fa fa-trash"></i></button></td>' +
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
        $('#bundle_components_tbody').append(rowHtml);
        addedCount++;
        newlyAddedIds.push(item.id);
    });
    
    recalculateBundleTotals();
    
    // Clear batch selection
    window.bundleSelectedMap = {};
    $('#bundle_check_all_products').prop('checked', false);
    updateBatchSelectionUI();
    
    // Remove newly added products from current search results list
    if (window.bundleSearchResultsList && window.bundleSearchResultsList.length) {
        window.bundleSearchResultsList = window.bundleSearchResultsList.filter(function(it) {
            return newlyAddedIds.indexOf(it.id) === -1;
        });
        renderBundleSearchResultsList();
    }
    
    $('#bundle_modal_feedback').html(
        '<div class="alert alert-success alert-dismissible fade show py-2 px-3 small font-weight-bold" role="alert">' +
        '<i class="fa fa-check-circle"></i> Successfully added ' + addedCount + ' product(s) to package! ' +
        'You can select more products or close this window when done.' +
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
    $('#bundle_components_tbody').append(rowHtml);
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

function recalculateBundleTotals() {
    var count = 0;
    var totalUnits = 0;
    var sumPrice = 0;
    
    $('#table_bundle_components tbody tr.bundle-row').each(function() {
        count++;
        var qty = parseInt($(this).find('.bundle-qty-input').val()) || 0;
        var price = parseFloat($(this).find('.bundle-price-input').val()) || 0;
        var rowTotal = qty * price;
        $(this).find('.item-row-total').text(rowTotal.toFixed(2));
        totalUnits += qty;
        sumPrice += rowTotal;
    });
    
    $('#badge_total_components').text(count);
    $('#badge_total_units').text(totalUnits);
    $('#badge_sum_price').text(sumPrice.toFixed(2));
}

function applyBundleSumToPrice() {
    var sumPrice = 0;
    $('#table_bundle_components tbody tr.bundle-row').each(function() {
        var qty = parseInt($(this).find('.bundle-qty-input').val()) || 1;
        var price = parseFloat($(this).find('.bundle-price-input').val()) || 0;
        sumPrice += (qty * price);
    });
    var formattedVal = sumPrice.toFixed(2);
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
}

function uploadBundleCsv(productId) {
    var fileInput = document.getElementById('bundle_csv_file_input');
    if (!fileInput.files || !fileInput.files[0]) {
        alert('Please select a CSV file to upload.');
        return;
    }
    
    var formData = new FormData();
    formData.append('bundle_csv', fileInput.files[0]);
    formData.append('product_id', productId);
    formData.append('<?= csrf_token(); ?>', '<?= csrf_hash(); ?>');
    
    $('#bundle_csv_status').html('<div class="alert alert-info py-2">Processing CSV for 100+ items...</div>');
    
    $.ajax({
        url: '<?= base_url("ajax/upload-bundle-csv"); ?>',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                $('#bundle_csv_status').html('<div class="alert alert-success py-2">Successfully imported ' + res.imported_count + ' items! Reloading...</div>');
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                var errStr = res.errors ? res.errors.join('<br>') : res.message;
                $('#bundle_csv_status').html('<div class="alert alert-danger py-2">' + errStr + '</div>');
            }
        },
        error: function() {
            $('#bundle_csv_status').html('<div class="alert alert-danger py-2">Error uploading CSV file.</div>');
        }
    });
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
            var headerHtml = '<tr class="bundle-category-header" style="background:#f1f5f9; border-top:2px solid #cbd5e1; border-bottom:1px solid #cbd5e1;">' +
                '<td colspan="10" style="padding:6px 12px; font-weight:700; color:#1e3a8a; font-size:12px; text-transform:uppercase; letter-spacing:0.5px;">' +
                '<i class="fa fa-folder-open-o text-primary"></i>&nbsp;&nbsp;' + (catName.charAt(0).toUpperCase() + catName.slice(1)) +
                '</td></tr>';
            tbody.append(headerHtml);
        }
        $(row).find('td:first').text(idx + 1);
        tbody.append(row);
    });
}
</script>
