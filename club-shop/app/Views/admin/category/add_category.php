<div class="row">
    <div class="col-lg-10 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans('add_category'); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('categories'); ?>" class="btn btn-success btn-add-new">
                        <i class="fa fa-list-ul"></i>&nbsp;&nbsp;<?= trans('categories'); ?>
                    </a>
                </div>
            </div>
            <form action="<?= base_url('Category/addCategoryPost'); ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <input type="hidden" name="parent_id" value="0">
                <div class="box-body">

                    <div class="form-group">
                        <label><?= trans("category_name"); ?></label>
                        <?php foreach ($activeLanguages as $language): ?>
                            <input type="text" class="form-control m-b-5" name="name_<?= $language->id; ?>" placeholder="<?= countItems($activeLanguages) > 1 ? esc($language->name) : esc(trans("category_name")); ?>" maxlength="255" required>
                        <?php endforeach; ?>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans("slug"); ?>
                            <small>(<?= trans("slug_exp"); ?>)</small>
                        </label>
                        <input type="text" class="form-control" name="slug" placeholder="<?= trans("slug"); ?>">
                    </div>

                    <div class="form-group">
                        <label><?= trans('order'); ?></label>
                        <input type="number" class="form-control" name="category_order" placeholder="<?= trans('order'); ?>" value="1" min="1" max="99999" required>
                    </div>

                    <div class="form-group">
                        <label><?= trans('parent_category'); ?></label>
                        <select class="form-control select2" name="category_id[]" onchange="getSubCategories(this.value, 1);" required>
                            <option value="0"><?= trans('none'); ?></option>
                            <?php if (!empty($parentCategories)):
                                foreach ($parentCategories as $parentCategory): ?>
                                    <option value="<?= $parentCategory->id; ?>"><?= esc($parentCategory->cat_name); ?></option>
                                <?php endforeach;
                            endif; ?>
                        </select>
                        <div id="category_select_container"></div>
                    </div>

                    <div class="form-group">
                        <label for="commission_mode"><?= trans("commission"); ?></label>
                        <select name="commission_mode" id="commission_mode" class="form-control">
                            <option value="default"><?= trans("default"); ?></option>
                            <option value="custom"><?= trans("custom"); ?></option>
                            <option value="none"><?= trans("none"); ?> (0%)</option>
                        </select>
                    </div>

                    <div class="form-group" id="custom_commission_input" style="display: none">
                        <label><?= trans('commission_rate'); ?>&nbsp;(%)</label>
                        <input type="number" name="commission_rate" id="commission_rate" class="form-control" min="0" max="99.99" step="0.01" placeholder="E.g. 5">
                    </div>

                    <div class="form-group">
                        <label for="markup_mode"><?= trans("seller_markup"); ?> (Markup Exception)</label>
                        <select name="markup_mode" id="markup_mode" class="form-control">
                            <option value="default"><?= trans("default"); ?> (Inherit Global/Parent)</option>
                            <option value="custom"><?= trans("custom"); ?></option>
                            <option value="none"><?= trans("none"); ?> (0% / Exempt)</option>
                        </select>
                    </div>

                    <div class="form-group" id="custom_markup_input" style="display: none">
                        <label><?= trans('markup_rate'); ?>&nbsp;(%)</label>
                        <input type="number" name="markup_rate" id="markup_rate" class="form-control" min="0" max="999.99" step="0.01" placeholder="E.g. 15">
                        <small class="text-muted">Custom markup rate for products under this category (overrides global markup).</small>
                    </div>

                    <div class="form-group">
                        <label for="discount_mode">Store Discount (Discount Exception)</label>
                        <select name="discount_mode" id="discount_mode" class="form-control">
                            <option value="default"><?= trans("default"); ?> (Inherit Global/Parent)</option>
                            <option value="custom"><?= trans("custom"); ?></option>
                            <option value="none"><?= trans("none"); ?> (0% / Exempt)</option>
                        </select>
                    </div>

                    <div class="form-group" id="custom_discount_input" style="display: none">
                        <label>Discount Rate&nbsp;(%)</label>
                        <input type="number" name="discount_rate" id="discount_rate" class="form-control" min="0" max="99.99" step="0.01" placeholder="E.g. 10">
                        <small class="text-muted">Custom discount rate for products under this category (overrides global store discount).</small>
                    </div>

                    <div class="form-group">
                        <?= formSwitch('status', trans('status'), 1); ?>
                    </div>

                    <div class="form-group">
                        <?= formSwitch('show_on_main_menu', trans('show_on_main_menu'), 1); ?>
                    </div>

                    <div class="form-group">
                        <?= formSwitch('show_image_on_main_menu', trans('show_image_on_main_menu'), 0); ?>
                    </div>

                    <div class="form-group">
                        <?= formSwitch('show_description', trans('show_description_category_page'), 0); ?>
                    </div>

                    <div class="form-group m-b-30">
                        <label class="control-label display-block"><?= trans('image'); ?></label>
                        <div class='btn btn-success btn-sm btn-file-upload'>
                            <?= trans('select_image'); ?>
                            <input type="file" name="file" class="image-input" accept=".jpg, .jpeg, .webp, .png, .gif" data-preview="#preview1">
                        </div>
                        <img src="" id="preview1" class="img-thumbnail img-preview">
                    </div>

                    <div class="nav-tabs-custom tab-default">
                        <p class="font-600"><?= trans("seo_metadata"); ?></p>
                        <ul class="nav nav-tabs">
                            <?php foreach ($activeLanguages as $language): ?>
                                <li class="<?= $language->id == selectedLangId() ? 'active' : ''; ?>"><a href="#tabLang<?= $language->id; ?>" data-toggle="tab" aria-expanded="true"><?= esc($language->name); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="tab-content">
                            <?php foreach ($activeLanguages as $language): ?>
                                <div class="tab-pane <?= $language->id == selectedLangId() ? 'active' : ''; ?>" id="tabLang<?= $language->id; ?>">
                                    <div class="form-group m-b-10">
                                        <input type="text" name="meta_title_<?= $language->id; ?>" class="form-control" placeholder="<?= esc(trans("meta_title")); ?>">
                                    </div>
                                    <div class="form-group m-b-10">
                                        <textarea class="form-control form-textarea" name="meta_description_<?= $language->id; ?>" placeholder="<?= esc(trans("meta_description")); ?>"></textarea>
                                    </div>
                                    <div class="form-group m-b-10">
                                        <input type="text" class="form-control" name="meta_keywords_<?= $language->id; ?>" placeholder="<?= esc(trans('meta_keywords')); ?>">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('add_category'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
