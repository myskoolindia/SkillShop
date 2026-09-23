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
                            <?php foreach ($bundleComponents as $idx => $comp): ?>
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

<!-- Modal: Add Product / Variant Live Search -->
<div class="modal fade" id="modalAddBundleItem" tabindex="-1" role="dialog" aria-labelledby="modalAddBundleItemLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddBundleItemLabel"><i class="fa fa-search"></i> Select Product & Variation for Package</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
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
                <div id="bundle_search_results" style="max-height: 280px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 4px; padding: 10px; margin-top: 10px;">
                    <div class="text-muted text-center py-3"><i class="fa fa-spinner fa-spin"></i> Loading products...</div>
                </div>

                <div id="bundle_selected_item_preview" class="mt-3 p-3 bg-light rounded" style="display:none; margin-top:15px; padding:12px; background:#f8f9fa; border-radius:6px;">
                    <h6 class="font-weight-bold mb-2">Configure Selection</h6>
                    <div class="row align-items-center">
                        <div class="col-md-5" id="bundle_variant_select_container">
                            <label class="small font-weight-bold">Variant (if applicable)</label>
                            <select id="bundle_item_variant_id" class="form-control form-control-sm custom-select"></select>
                        </div>
                        <div class="col-md-3">
                            <label class="small font-weight-bold">Quantity in Package</label>
                            <input type="number" id="bundle_item_qty" class="form-control form-control-sm" min="1" value="1">
                        </div>
                        <div class="col-md-4">
                            <label class="small font-weight-bold">Price Override (Optional)</label>
                            <input type="number" step="0.01" id="bundle_item_price" class="form-control form-control-sm" placeholder="Default">
                        </div>
                        <div class="col-12 mt-2 pt-2" style="border-top: 1px dashed #dee2e6;">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="bundle_item_is_optional" onchange="toggleModalBundleOptional(this.checked);">
                                <label class="custom-control-label small font-weight-bold text-dark" for="bundle_item_is_optional" style="cursor:pointer;">
                                    <i class="fa fa-plus-circle text-primary"></i> Mark as Optional Item (Item starts with 0 qty; buyer can add when purchasing)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btn_confirm_add_bundle_item" style="display:none;" onclick="confirmAddBundleItem();"><i class="fa fa-plus"></i> Add to Package</button>
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

function removeBundleRow(btn) {
    $(btn).closest('tr').remove();
    recalculateBundleTotals();
    if ($('#table_bundle_components tbody tr.bundle-row').length === 0) {
        $('#bundle_components_tbody').html('<tr id="empty_bundle_row"><td colspan="9" class="text-center p-4 text-muted"><i class="fa fa-inbox fa-2x mb-2"></i><br>No component items added yet.</td></tr>');
    }
}

function triggerBundleSearch() {
    var query = ($('#bundle_product_search_query').val() || '').trim();
    searchProductsForBundle(query);
}

function searchProductsForBundle(query) {
    query = (query || '').trim();
    var categoryId = parseInt($('#bundle_product_search_category_id').val()) || 0;

    var postData = {
        query: query,
        category_id: categoryId,
        exclude_id: <?= $product->id; ?>
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
            if (response && response.status === 'success' && response.data && response.data.length > 0) {
                window.bundleSearchResultsList = response.data;
                var html = '<ul class="list-group mb-0">';
                $.each(response.data, function(idx, item) {
                    var varBadge = item.variants && item.variants.length > 0 ? '<span class="badge badge-info ml-1">' + item.variants.length + ' variants</span>' : '';
                    var catBadge = item.category_name ? '<span class="badge badge-light border text-secondary ml-1" style="font-size:10.5px;"><i class="fa fa-folder-o"></i> ' + $('<div>').text(item.category_name).html() + '</span>' : '';
                    var imgTag = item.image_small ? '<img src="' + item.image_small + '" style="width:36px; height:36px; object-fit:cover; border-radius:4px; margin-right:10px;">' : '<div style="width:36px; height:36px; background:#eee; border-radius:4px; display:inline-flex; align-items:center; justify-content:center; margin-right:10px; color:#aaa;"><i class="fa fa-cube"></i></div>';
                    html += '<li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-2" style="cursor:pointer;" onclick="selectProductForBundleByIndex(' + idx + ');">';
                    html += '<div class="d-flex align-items-center" style="max-width:80%;">' + imgTag + '<div><strong>' + $('<div>').text(item.title).html() + '</strong>' + catBadge + varBadge + '<br><small class="text-muted">SKU: <code>' + (item.sku || 'N/A') + '</code> | Stock: ' + item.stock + ' | Price: ' + item.price + '</small></div></div>';
                    html += '<button type="button" class="btn btn-sm btn-outline-primary"><i class="fa fa-check"></i> Select</button>';
                    html += '</li>';
                });
                html += '</ul>';
                $('#bundle_search_results').html(html);
            } else {
                $('#bundle_search_results').html('<div class="text-muted text-center py-3"><i class="fa fa-info-circle"></i> No matching products found' + (query ? ' for "' + $('<div>').text(query).html() + '"' : '') + '</div>');
            }
        },
        error: function(xhr, status, error) {
            $('#bundle_search_results').html('<div class="text-danger text-center py-3"><i class="fa fa-warning"></i> Search error: ' + error + '</div>');
        }
    });
}

function toggleModalBundleOptional(checked) {
    if (checked) {
        $('#bundle_item_qty').val(0).attr('min', 0);
    } else {
        if (parseInt($('#bundle_item_qty').val()) <= 0) {
            $('#bundle_item_qty').val(1);
        }
        $('#bundle_item_qty').attr('min', 1);
    }
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

function selectProductForBundleByIndex(idx) {
    if (!window.bundleSearchResultsList || !window.bundleSearchResultsList[idx]) return;
    var item = window.bundleSearchResultsList[idx];
    selectedProductForBundle = item;
    $('#bundle_selected_item_preview').slideDown(150);
    $('#btn_confirm_add_bundle_item').show();
    $('#bundle_item_is_optional').prop('checked', false);
    $('#bundle_item_qty').val(1).attr('min', 1);
    $('#bundle_item_price').val('');
    
    var variantSelect = $('#bundle_item_variant_id');
    variantSelect.empty();
    if (item.variants && item.variants.length > 0) {
        $('#bundle_variant_select_container').show();
        variantSelect.append('<option value="">-- Simple / Base Product --</option>');
        $.each(item.variants, function(vIdx, v) {
            variantSelect.append('<option value="' + v.id + '" data-sku="' + (v.sku || '') + '" data-price="' + v.price + '" data-stock="' + v.quantity + '">' + $('<div>').text(v.name).html() + ' (SKU: ' + (v.sku || 'N/A') + ' - Stock: ' + v.quantity + ')</option>');
        });
    } else {
        $('#bundle_variant_select_container').hide();
    }
}

function confirmAddBundleItem() {
    if (!selectedProductForBundle) return;
    $('#empty_bundle_row').remove();
    
    var variantId = $('#bundle_item_variant_id').val() || null;
    var variantName = '';
    var sku = selectedProductForBundle.sku || '';
    var price = selectedProductForBundle.price || 0;
    var stock = selectedProductForBundle.stock || 0;
    
    if (variantId) {
        var selectedOpt = $('#bundle_item_variant_id option:selected');
        variantName = selectedOpt.text();
        sku = selectedOpt.data('sku') || sku;
        stock = selectedOpt.data('stock') !== undefined ? selectedOpt.data('stock') : stock;
    }
    
    var isOptional = $('#bundle_item_is_optional').is(':checked') ? 1 : 0;
    var qty = parseInt($('#bundle_item_qty').val());
    if (isNaN(qty)) {
        qty = isOptional ? 0 : 1;
    }
    if (!isOptional && qty < 1) {
        qty = 1;
    }

    var priceOverride = $('#bundle_item_price').val();
    var unitPrice = priceOverride !== '' ? parseFloat(priceOverride) : parseFloat(price);
    var totalPrice = (unitPrice * qty).toFixed(2);
    
    var imgHtml = selectedProductForBundle.image_small ? '<img src="' + selectedProductForBundle.image_small + '" style="width:45px; height:45px; object-fit:cover; border-radius:4px;">' : '<div style="width:45px; height:45px; background:#eee; border-radius:4px; display:flex; align-items:center; justify-content:center; color:#999;"><i class="fa fa-image"></i></div>';
    var catName = selectedProductForBundle.category_name || '';
    var catBadge = catName ? '<span class="badge badge-light border text-secondary" style="font-size:10.5px;"><i class="fa fa-folder-o"></i> ' + $('<div>').text(catName).html() + '</span>' : '';
    var varBadge = variantName ? '<span class="badge badge-info" style="font-size:10.5px;"><i class="fa fa-tag"></i> ' + $('<div>').text(variantName).html() + '</span>' : '';

    var rowIdx = bundleRowIndex++;
    var rowHtml = '<tr class="bundle-row" data-title="' + selectedProductForBundle.title.toLowerCase() + '" data-sku="' + sku.toLowerCase() + '" data-category="' + catName.toLowerCase() + '">' +
        '<td>' + ($('#table_bundle_components tbody tr.bundle-row').length + 1) + '</td>' +
        '<td>' + imgHtml + '</td>' +
        '<td><div class="font-weight-bold">' + $('<div>').text(selectedProductForBundle.title).html() + '</div>' +
        '<div style="display:flex; gap:4px; flex-wrap:wrap; margin-top:2px;">' + catBadge + varBadge + '</div>' +
        '<input type="hidden" name="bundle_items[' + rowIdx + '][component_product_id]" value="' + selectedProductForBundle.id + '">' +
        '<input type="hidden" name="bundle_items[' + rowIdx + '][variant_id]" value="' + (variantId || '') + '">' +
        '</td>' +
        '<td><code>' + sku + '</code></td>' +
        '<td class="text-center" style="vertical-align: middle;">' +
        '<div class="custom-control custom-checkbox">' +
        '<input type="checkbox" class="custom-control-input bundle-optional-checkbox" id="bundle_opt_' + rowIdx + '" name="bundle_items[' + rowIdx + '][is_optional]" value="1" ' + (isOptional ? 'checked' : '') + ' onchange="onBundleOptionalChange(this);">' +
        '<label class="custom-control-label small" for="bundle_opt_' + rowIdx + '" title="Optional item starts with 0 qty for buyer"></label>' +
        '</div>' +
        '</td>' +
        '<td><input type="number" min="' + (isOptional ? '0' : '1') + '" max="9999" name="bundle_items[' + rowIdx + '][quantity]" class="form-control form-control-sm bundle-qty-input" value="' + qty + '" onchange="recalculateBundleTotals();"></td>' +
        '<td><input type="number" step="0.01" min="0" name="bundle_items[' + rowIdx + '][price_override]" class="form-control form-control-sm bundle-price-input" value="' + (priceOverride !== '' ? priceOverride : unitPrice) + '" onchange="recalculateBundleTotals();"></td>' +
        '<td class="font-weight-bold text-muted item-row-total">' + totalPrice + '</td>' +
        '<td><span class="badge ' + (stock >= qty ? 'badge-success' : 'badge-danger') + '">' + stock + ' in stock</span></td>' +
        '<td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeBundleRow(this);"><i class="fa fa-trash"></i></button></td>' +
        '</tr>';
        
    $('#bundle_components_tbody').append(rowHtml);
    recalculateBundleTotals();
    $('#modalAddBundleItem').modal('hide');
    $('#bundle_product_search_query').val('');
    $('#bundle_search_results').html('<div class="text-muted text-center py-3">Type above or click Search to find products...</div>');
    $('#bundle_selected_item_preview').hide();
    $('#btn_confirm_add_bundle_item').hide();
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
</script>
