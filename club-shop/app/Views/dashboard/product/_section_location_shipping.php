<?php $isLocationEnabled = true;
if ($product->listing_type == 'ordinary_listing') {
    if ($productSettings->classified_product_location != 1) {
        $isLocationEnabled = false;
    }
} else {
    if ($productSettings->marketplace_product_location != 1) {
        $isLocationEnabled = false;
    }
}
if ($product->product_type != 'digital' && $isLocationEnabled == true):?>
    <div class="section-product-details">
        <div class="form-box form-box-last">
            <div class="form-box-head">
                <h4 class="title">
                    <?= trans('location'); ?>
                    <small><?= trans("product_location_exp"); ?></small>
                </h4>
            </div>
            <div class="form-box-body">
                <div class="row">
                    <?php $countries = getCountries();
                    $countryId = $product->country_id;
                    $states = !empty($countryId) ? getStatesByCountry($countryId) : array();
                    $cities = !empty($product->state_id) ? getCitiesByState($product->state_id) : array(); ?>
                    <?php if ($generalSettings->single_country_mode != 1): ?>
                        <div class="col-md-12 col-lg-2 m-b-15">
                            <select id="select_countries" name="country_id" class="select2 form-control" onchange="getStates(this.value);">
                                <option value=""><?= trans('country'); ?></option>
                                <?php if (!empty($countries)):
                                    foreach ($countries as $item):
                                        if ($item->status == 1):?>
                                            <option value="<?= $item->id; ?>" <?= !empty($countryId) && $item->id == $countryId ? 'selected' : ''; ?>><?= esc($item->name); ?></option>
                                        <?php endif;
                                    endforeach;
                                endif; ?>
                            </select>
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="country_id" value="<?= $generalSettings->single_country_id; ?>">
                        <?php $countryId = $generalSettings->single_country_id;
                        $states = getStatesByCountry($countryId);
                    endif; ?>
                    <div id="get_states_container" class="col-md-12 col-lg-2 m-b-15 <?= !empty($countryId) ? '' : 'display-none'; ?>">
                        <select id="select_states" name="state_id" class="select2 form-control" onchange="getCities(this.value);">
                            <option value=""><?= trans('state'); ?></option>
                            <?php if (!empty($states)):
                                foreach ($states as $item): ?>
                                    <option value="<?= $item->id; ?>" <?= !empty($product->state_id) && $item->id == $product->state_id ? 'selected' : ''; ?>><?= esc($item->name); ?></option>
                                <?php endforeach;
                            endif; ?>
                        </select>
                    </div>
                    <div id="get_cities_container" class="col-md-12 col-lg-2 m-b-15 <?= empty($cities) ? 'display-none' : ''; ?>">
                        <select id="select_cities" name="city_id" class="select2 form-control">
                            <option value=""><?= trans('city'); ?></option>
                            <?php if (!empty($cities)):
                                foreach ($cities as $item):?>
                                    <option value="<?= $item->id; ?>" <?= !empty($product->city_id) && $item->id == $product->city_id ? 'selected' : ''; ?>><?= esc($item->name); ?></option>
                                <?php endforeach;
                            endif; ?>
                        </select>
                    </div>
                    <div class="col-md-12 col-lg-4 m-b-15">
                        <input type="text" name="address" id="address_input" class="form-control form-input" value="<?= !empty($product->address) ? esc($product->address) : ''; ?>" placeholder="<?= trans("address") ?>" maxlength="499">
                    </div>
                    <div class="col-md-12 col-lg-2 m-b-15">
                        <input type="text" name="zip_code" id="zip_code_input" class="form-control form-input" value="<?= !empty($product->zip_code) ? esc($product->zip_code) : ''; ?>" placeholder="<?= trans("zip_code") ?>" maxlength="49">
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>


<?php if ($shippingStatus == 1):
    $shippingDimensions = json_decode($product->shipping_dimensions ?? ''); ?>
    <div class="section-product-details">
        <div class="form-box form-box-last">
            <div class="form-box-head">
                <h4 class="title"><?= trans('shipping'); ?>&nbsp;(<?= trans("optional"); ?>)</h4>
            </div>


            <div class="row">
                <div class="col-sm-12 col-md-4 col-lg-2">
                    <div class="form-group">
                        <label><?= trans("weight"); ?>&nbsp;(<?= trans("kg"); ?>)</label>
                        <input type="number" class="form-control" name="product_weight" value="<?= !empty($shippingDimensions->weight) ? esc($shippingDimensions->weight) : ''; ?>" placeholder="e.g., 1.5" min="0" max="999.99" step="0.01">
                    </div>
                </div>

                <div class="col-sm-12 col-md-8 col-lg-6">
                    <div class="form-group m-b-0">
                        <label for="product_length" style="word-break: break-word; white-space: pre-wrap"><?= trans("dimensions"); ?>&nbsp;(<?= trans("length"); ?>&nbsp;/&nbsp;<?= trans("width"); ?>&nbsp;/&nbsp;<?= trans("height"); ?>)&nbsp;(<?= trans("cm"); ?>)</label>
                        <div style="display: flex; gap: 5px; max-width: 500px;">
                            <input type="number" class="form-control" name="product_length" value="<?= !empty($shippingDimensions->length) ? esc($shippingDimensions->length) : ''; ?>" placeholder="<?= esc(trans("length")); ?>" min="0" max="500" step="0.01">
                            <input type="number" class="form-control" name="product_width" value="<?= !empty($shippingDimensions->width) ? esc($shippingDimensions->width) : ''; ?>" placeholder="<?= esc(trans("width")); ?>" min="0" max="500" step="0.01">
                            <input type="number" class="form-control" name="product_height" value="<?= !empty($shippingDimensions->height) ? esc($shippingDimensions->height) : ''; ?>" placeholder="<?= esc(trans("height")); ?>" min="0" max="500" step="0.01">
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12">
                    <?php if (!empty($product->chargeable_weight)): ?>
                        <label class="label label-default" style="font-size: 13px; !important; margin-bottom: 25px; margin-top: 5px; display: inline-block">
                            <?= trans("calculated_weight") ?>(<?= trans("kg"); ?>):&nbsp;<strong><?= $product->chargeable_weight; ?></strong>
                        </label>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <label><?= trans('delivery_time'); ?></label>
                    <select name="shipping_delivery_time_id" class="select2 form-control custom-select">
                        <option value=""><?= trans("select"); ?></option>
                        <?php if (!empty($shippingDeliveryTimes)): ?>
                            <?php foreach ($shippingDeliveryTimes as $deliveryTime): ?>
                                <option value="<?= $deliveryTime->id; ?>" <?= $product->shipping_delivery_time_id == $deliveryTime->id ? 'selected' : ''; ?>><?= @parseSerializedOptionArray($deliveryTime->option_array, selectedLangId()); ?></option>
                            <?php endforeach;
                        endif; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
