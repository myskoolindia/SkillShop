<?php if (!empty($method)): ?>
    <div id="modalEditShippingMethod<?= $method->id; ?>" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="<?= base_url('Dashboard/editShippingMethodPost'); ?>" method="post">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="method_id" value="<?= esc($method->id); ?>">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><i class="fa-solid fa-xmark"></i></button>
                        <h4 class="modal-title"><?= trans("shipping_method"); ?></h4>
                    </div>
                    <div class="modal-body">

                        <div class="form-group">
                            <?= formSwitch('status', trans('status'), $method->status); ?>
                        </div>


                        <div class="form-group m-b-10">
                            <label class="control-label"><?= trans("method_name"); ?></label>
                            <?php foreach ($activeLanguages as $language): ?>
                                <input type="text" name="method_name_<?= esc($method->id); ?>_lang_<?= esc($language->id); ?>" class="form-control form-input m-b-5" value="<?= esc(@parseSerializedNameArray($method->name_array, $language->id)); ?>" placeholder="<?= esc($language->name); ?>" maxlength="255">
                            <?php endforeach; ?>
                        </div>

                        <?php if ($method->method_type == 'free_shipping'): ?>

                            <div class="form-group">
                                <label class="control-label"><?= trans("minimum_order_amount"); ?></label>
                                <?= renderPriceInput('free_shipping_min_amount', $method->free_shipping_min_amount); ?>
                            </div>

                        <?php elseif ($method->method_type == 'local_pickup'): ?>

                            <div class="form-group">
                                <label class="control-label"><?= trans("local_pickup_cost"); ?></label>
                                <?= renderPriceInput('local_pickup_cost', $method->local_pickup_cost); ?>
                            </div>

                        <?php elseif ($method->method_type == 'flat_rate'):
                            $flatRates = [];
                            if (!empty($method->flat_rate_costs)) {
                                $decodedRates = safeJsonDecode($method->flat_rate_costs);
                                if (!empty($decodedRates) && countItems($decodedRates) > 0) {
                                    $flatRates = $decodedRates;
                                }
                            }
                            ?>

                            <div class="form-group">
                                <label for="shipping_calculation_type"><?= trans("shipping_calculation_type"); ?></label>
                                <select id="shipping_calculation_type" name="cost_calculation_type" class="form-control">
                                    <option value="total_weight" <?= $method->cost_calculation_type == 'total_weight' ? 'selected' : ''; ?>><?= trans("shipping_by_total_weight"); ?></option>
                                    <option value="per_order" <?= $method->cost_calculation_type == 'per_order' ? 'selected' : ''; ?>><?= trans("shipping_by_per_order"); ?></option>
                                    <option value="per_item" <?= $method->cost_calculation_type == 'per_item' ? 'selected' : ''; ?>><?= trans("shipping_by_per_item"); ?></option>
                                </select>
                            </div>

                            <div id="weight_rules_container" style="display:<?= $method->cost_calculation_type == 'total_weight' ? 'block' : 'none'; ?>">
                                <div class="form-group">
                                    <label><?= trans("flat_rate_rules") ?> (<?= trans("min_weight") ?>/<?= trans("max_weight") ?>/<?= trans("cost") ?>)</label>
                                    <div id="shipping-rates-container">
                                        <?php
                                        $flatRates = !empty($flatRates) ? $flatRates : [(object)['min_weight' => '', 'max_weight' => '', 'cost' => '']];
                                        foreach ($flatRates as $i => $rate): ?>
                                            <div class="rate-row">
                                                <div class="rate-row-inner">
                                                    <div class="rate-input">
                                                        <div class="input-group">
                                                            <span class="input-group-addon"><?= trans("kg"); ?></span>
                                                            <input type="number" name="rates[<?= $i ?>][min_weight]" value="<?= esc($rate->min_weight) ?>" class="form-control" placeholder="<?= trans("min_weight") ?>" min="0" max="999999" step="0.001">
                                                        </div>
                                                    </div>
                                                    <div class="rate-input">
                                                        <div class="input-group">
                                                            <span class="input-group-addon"><?= trans("kg"); ?></span>
                                                            <input type="number" name="rates[<?= $i ?>][max_weight]" value="<?= esc($rate->max_weight) ?>" class="form-control" placeholder="<?= trans("max_weight") ?>" min="0" max="999999" step="0.001">
                                                        </div>
                                                    </div>
                                                    <div class="rate-input">
                                                        <div class="input-group">
                                                            <span class="input-group-addon"><?= esc($defaultCurrency->symbol) ?></span>
                                                            <input type="text" name="rates[<?= $i ?>][cost]" value="<?= esc($rate->cost) ?>" class="form-control input-price" maxlength="13" placeholder="<?= trans("cost") ?>" inputmode="decimal">
                                                        </div>
                                                    </div>
                                                    <div style="display: flex; align-items: center">
                                                        <button type="button" class="btn btn-sm btn-danger btn-remove-rate"><i class="fa fa-times"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <button type="button" id="btn-add-rate" class="btn btn-success mt-2">
                                        <span class="glyphicon glyphicon-plus"></span>&nbsp;<?= trans("add_rate"); ?>
                                    </button>
                                </div>
                            </div>

                            <div id="single_cost_container" style="display:<?= $method->cost_calculation_type == 'total_weight' ? 'none' : 'block'; ?>">
                                <div class="form-group">
                                    <label class="control-label"><?= trans("shipping_cost"); ?></label>
                                    <?= renderPriceInput('shipping_flat_cost', $method->shipping_flat_cost, ['id' => 'shipping_flat_cost']); ?>
                                    <small class="help-block"><?= trans("shipping_cost_calculation_exp"); ?></small>
                                </div>
                            </div>

                            <?php elseif ($method->method_type == 'shipdelight'):
                                $meta = [];
                                if (!empty($method->meta)) {
                                    $meta = safeJsonDecode($method->meta, true);
                                    $meta = is_array($meta) ? $meta : [];
                                }
                                $costType = $meta['cost_type'] ?? 'fixed';
                                $fixedCost = $meta['fixed_cost'] ?? 0;
                                $payType = $meta['pay_type'] ?? 'BOTH';
                                $serviceType = $meta['service_type'] ?? 'F';
                                $courierServiceType = $meta['courier_service_type'] ?? 'sdd';
                                $useRecommendation = $meta['use_recommendation'] ?? 1;
                            ?>
                                <div class="form-group">
                                    <label class="control-label">ShipDelight Pricing</label>
                                    <select name="sd_cost_type" class="form-control">
                                        <option value="fixed" <?= $costType === 'fixed' ? 'selected' : ''; ?>>Fixed Cost</option>
                                        <option value="from_flat_rate" <?= $costType === 'from_flat_rate' ? 'selected' : ''; ?>>Use Flat-Rate Rules</option>
                                    </select>
                                    <small class="help-block">
                                        Fixed = always same shipping charge.
                                        Flat-Rate = use your weight/per-item rules (like your existing flat_rate).
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Fixed Shipping Cost</label>
                                    <?= renderPriceInput('sd_fixed_cost', $fixedCost); ?>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Pay Type</label>
                                    <select name="sd_pay_type" class="form-control">
                                        <option value="BOTH" <?= $payType === 'BOTH' ? 'selected' : ''; ?>>Both (COD + PPD)</option>
                                        <option value="PPD" <?= $payType === 'PPD' ? 'selected' : ''; ?>>Prepaid Only</option>
                                        <option value="COD" <?= $payType === 'COD' ? 'selected' : ''; ?>>COD Only</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Service Type</label>
                                    <select name="sd_service_type" class="form-control">
                                        <option value="F" <?= $serviceType === 'F' ? 'selected' : ''; ?>>Forward</option>
                                        <option value="R" <?= $serviceType === 'R' ? 'selected' : ''; ?>>Reverse</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Courier Service Type</label>
                                    <input type="text" name="sd_courier_service_type" class="form-control"
                                        value="<?= esc($courierServiceType); ?>" placeholder="sdd">
                                    <small class="help-block">Example: sdd (same-day) depending on provider setup.</small>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Auto Courier Recommendation</label>
                                    <select name="sd_use_recommendation" class="form-control">
                                        <option value="1" <?= (int)$useRecommendation === 1 ? 'selected' : ''; ?>>Yes</option>
                                        <option value="0" <?= (int)$useRecommendation === 0 ? 'selected' : ''; ?>>No</option>
                                    </select>
                                    <small class="help-block">Uses priority_rule API to select courier automatically (optional).</small>
                                </div>
                            


                        <?php endif; ?>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success"><?= trans("save_changes"); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>