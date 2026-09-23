<div id="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="nav-breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-products breadcrumb-mobile-scroll">
                        <li class="breadcrumb-item"><a href="<?= langBaseUrl(); ?>"><?= trans("home"); ?></a></li>
                        <?php if (!empty($parentCategoriesTree)):
                            foreach ($parentCategoriesTree as $item):?>
                                <li class="breadcrumb-item"><a href="<?= generateCategoryUrl($item); ?>"><?= esc($item->cat_name); ?></a></li>
                            <?php endforeach;
                        endif; ?>
                        <li class="breadcrumb-item active"><?= esc($title); ?></li>
                    </ol>
                </nav>
            </div>
            <div class="col-12">
                <div class="product-details-container <?= (!empty($video) || !empty($audio)) && countItems($productImages) < 2 ? 'product-details-container-digital' : ''; ?> mb-0">
                    <div class="row">
                        <div class="col-12 col-sm-12 col-lg-6 col-product-details-left">
                            <div id="product_slider_container">
                                <?php
                                $imageCount = countItems($productImages);
                                $hasVideo = !empty($video);
                                $hasAudio = !empty($audio);

                                $showSlider = $imageCount > 1 || (!$hasVideo && !$hasAudio);
                                $showVideo = !$showSlider && $hasVideo;
                                $showAudio = !$showSlider && !$hasVideo && $hasAudio;

                                if (empty($productSliderImages)) {
                                    $noImg = base_url('assets/img/no-image.jpg');
                                    $productSliderImages = [
                                        [
                                            'id' => 0,
                                            'url_main' => $noImg,
                                            'url_thumb' => $noImg,
                                            'url_full' => $noImg,
                                        ]
                                    ];
                                }
                                ?>

                                <?php if ($showSlider): ?>
                                    <div class="product-slider-wrapper">
                                        <div class="thumb-slider-wrapper">
                                            <div id="product-thumb-slider" class="swiper thumb-slider">
                                                <div class="swiper-wrapper">

                                                    <?php $i = 1;
                                                    if (!empty($productSliderImages)):
                                                        foreach ($productSliderImages as $image): ?>
                                                            <div class="swiper-slide">
                                                                <img src="<?= !empty($image['url_thumb']) ? esc($image['url_thumb']) : ''; ?>" alt="<?= esc($title) . ' ' . $i; ?>">
                                                            </div>
                                                            <?php $i++;
                                                        endforeach;
                                                    endif; ?>

                                                    <?php if ($hasVideo): ?>
                                                        <div class="swiper-slide no-thumb-sync">
                                                            <button type="button" data-toggle="modal" data-target="#productVideoModal" aria-label="button video modal">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-play-circle" viewBox="0 0 16 16">
                                                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                                                    <path d="M6.271 5.055a.5.5 0 0 1 .52.038l3.5 2.5a.5.5 0 0 1 0 .814l-3.5 2.5A.5.5 0 0 1 6 10.5v-5a.5.5 0 0 1 .271-.445"/>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if ($hasAudio): ?>
                                                        <div class="swiper-slide no-thumb-sync">
                                                            <button type="button" data-toggle="modal" data-target="#productAudioModal" aria-label="button audio modal">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-music-note" viewBox="0 0 16 16">
                                                                    <path d="M9 13c0 1.105-1.12 2-2.5 2S4 14.105 4 13s1.12-2 2.5-2 2.5.895 2.5 2"/>
                                                                    <path fill-rule="evenodd" d="M9 3v10H8V3z"/>
                                                                    <path d="M8 2.82a1 1 0 0 1 .804-.98l3-.6A1 1 0 0 1 13 2.22V4L8 5z"/>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    <?php endif; ?>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="slider-for-container">
                                            <div id="product-slider" class="swiper product-slider">
                                                <div class="swiper-wrapper">
                                                    <?php if (!empty($productSliderImages)):
                                                        foreach ($productSliderImages as $image):?>
                                                            <div class="swiper-slide">
                                                                <a href="<?= !empty($image['url_full']) ? esc($image['url_full']) : ''; ?>" class="glightbox-product" data-gallery="product-gallery">
                                                                    <img src="<?= !empty($image['url_main']) ? esc($image['url_main']) : ''; ?>" alt="<?= esc($title); ?>">
                                                                </a>
                                                            </div>
                                                        <?php endforeach;
                                                    endif; ?>
                                                </div>

                                                <div class="swiper-button-next"></div>
                                                <div class="swiper-button-prev"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="product-slider-mobile-media-buttons">
                                        <?php if ($hasVideo): ?>
                                            <div class="media-item">
                                                <button type="button" data-toggle="modal" data-target="#productVideoModal" aria-label="button video modal">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-play-circle" viewBox="0 0 16 16">
                                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                                        <path d="M6.271 5.055a.5.5 0 0 1 .52.038l3.5 2.5a.5.5 0 0 1 0 .814l-3.5 2.5A.5.5 0 0 1 6 10.5v-5a.5.5 0 0 1 .271-.445"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($hasAudio): ?>
                                            <div class="media-item">
                                                <button type="button" data-toggle="modal" data-target="#productAudioModal" aria-label="button audio modal">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-music-note" viewBox="0 0 16 16">
                                                        <path d="M9 13c0 1.105-1.12 2-2.5 2S4 14.105 4 13s1.12-2 2.5-2 2.5.895 2.5 2"/>
                                                        <path fill-rule="evenodd" d="M9 3v10H8V3z"/>
                                                        <path d="M8 2.82a1 1 0 0 1 .804-.98l3-.6A1 1 0 0 1 13 2.22V4L8 5z"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </div>


                                <?php elseif ($hasVideo): ?>
                                    <div class="product-video-preview">
                                        <video id="player" playsinline controls>
                                            <source src="<?= getProductVideoUrl($video); ?>" type="video/mp4">
                                        </video>
                                    </div>
                                <?php elseif ($hasAudio):
                                    echo view('product/details/_audio_player');
                                endif; ?>


                                <?php if ($hasVideo && $showSlider): ?>
                                    <div class="modal fade" id="productVideoModal" role="dialog">
                                        <div class="modal-dialog modal-dialog-centered modal-product-video" role="document">
                                            <div class="modal-content">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="close-video-modal"><i class="icon-close"></i></button>
                                                <div class="product-video-preview m-0">
                                                    <video id="player" playsinline controls>
                                                        <source src="<?= getProductVideoUrl($video); ?>" type="video/mp4">
                                                    </video>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif;
                                if ($hasAudio && $showSlider): ?>
                                    <div class="modal fade" id="productAudioModal" role="dialog">
                                        <div class="modal-dialog modal-dialog-centered modal-product-video" role="document">
                                            <div class="modal-content">
                                                <div class="row-custom" style="width: auto !important;">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="close-audio-modal"><i class="icon-close"></i></button>
                                                    <?= view('product/details/_audio_player'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>



                                <?//= view("product/details/_preview"); ?>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-lg-6 col-product-details-right">
                            <div id="response_product_details" class="product-content-details">
                                <div class="row">
                                    <div class="col-12">
                                        <?php if ($product->product_type == 'digital'): ?>
                                            <label class="badge badge-success-light badge-instant-download">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
                                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                                    <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/>
                                                </svg>&nbsp;&nbsp;<?= trans("instant_download"); ?>
                                            </label>
                                        <?php endif; ?>
                                        <h1 class="product-title">
                                            <?= esc($title); ?>
                                            <?php if (authCheck() && user()->id == $product->user_id): ?>
                                                <a href="<?= generateDashUrl('edit_product') . '/' . $product->id; ?>" class="btn btn-default btn-edit-product"><i class="icon-edit m-0"></i></a>
                                            <?php endif; ?>
                                        </h1>
                                        <?php if ($product->status == 0): ?>
                                            <label class="badge badge-warning badge-product-status"><?= trans("pending"); ?></label>
                                        <?php elseif ($product->visibility == 0): ?>
                                            <label class="badge badge-danger badge-product-status"><?= trans("hidden"); ?></label>
                                        <?php endif; ?>

                                        <div class="row-custom meta">
                                            <div class="d-flex justify-content-between align-items-center flex-wrap product-meta-info">
                                                <div class="d-flex align-items-center flex-wrap">
                                                    <span><?= trans("seller"); ?>:&nbsp;<a href="<?= generateProfileUrl($product->user_slug); ?>"><?= characterLimiter(esc($product->user_username), 30, '..'); ?></a></span>
                                                    <span class="mx-2">|</span>
                                                    <?php if ($generalSettings->reviews == 1): ?>
                                                        <div class="product-details-review">
                                                            <?= view('partials/_review_stars', ['rating' => $product->rating]); ?>
                                                            <?php if ($product->rating > 0): ?>
                                                                <button type="button" id="btnGoToReviews" class="button-link review-text" aria-label="go-to-reviews"><?= trans("reviews"); ?>&nbsp;(<?= numberFormatShort($reviewsCount); ?>)</button>
                                                            <?php else: ?>
                                                                <span class="review-text"><?= trans("reviews"); ?>&nbsp;(<?= numberFormatShort($reviewsCount); ?>)</span>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="d-flex align-items-center product-analytics">
                                                    <?php if ($generalSettings->product_comments == 1): ?>
                                                        <span><i class="icon-comment"></i>&nbsp;<?= esc($commentsCount); ?></span>
                                                    <?php endif; ?>
                                                    <span><i class="icon-heart"></i>&nbsp;<?= numberFormatShort($wishlistCount); ?></span>
                                                    <span><i class="icon-eye"></i>&nbsp;<?= numberFormatShort($product->pageviews); ?></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row-custom">
                                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                                                <div class="flex-item">
                                                    <?= view('product/details/_price', ['product' => $product, 'price' => $product->price, 'priceDiscounted' => $product->price_discounted, 'discountRate' => $product->discount_rate]); ?>
                                                </div>

                                                <div class="flex-item">
                                                    <?php $showVendorContactInfo = false;
                                                    if (authCheck() || !empty($generalSettings->show_vendor_contact_info_guests)) {
                                                        $showVendorContactInfo = true;
                                                    }
                                                    $showAsk = true;
                                                    if ($product->listing_type == 'ordinary_listing' && empty($product->external_link)):
                                                        $showAsk = false;
                                                    endif;
                                                    if ($showAsk == true): ?>
                                                        <?php if ($showVendorContactInfo): ?>
                                                            <button class="btn btn-contact-seller" data-toggle="modal" data-target="#messageModal"><i class="icon-envelope"></i> <?= trans("ask_question") ?></button>
                                                        <?php else: ?>
                                                            <button class="btn btn-contact-seller" data-toggle="modal" data-target="#loginModal"><i class="icon-envelope"></i> <?= trans("ask_question") ?></button>
                                                        <?php endif;
                                                    endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <?php
                                        $bundleCheck = !empty($product->is_bundle);
                                        $bMetrics = (new \App\Models\BundleModel())->calculateBundleMetrics($product->id);
                                        if (!$bundleCheck && $bMetrics['total_components'] > 0) {
                                            $bundleCheck = true;
                                        }
                                        ?>
                                        <?php if ($bundleCheck && $bMetrics['total_components'] > 0): ?>
                                            <div class="alert alert-info py-2 px-3 m-b-15 d-flex align-items-center justify-content-between" style="background:#ebf8ff; border:1px solid #bee3f8; border-radius:6px; display:flex; justify-content:space-between; align-items:center;">
                                                <div>
                                                    <strong class="text-primary"><i class="fa fa-cubes"></i> Bundle Package Deal</strong><br>
                                                    <small class="text-muted">Includes <strong><?= $bMetrics['total_components']; ?></strong> products & variations (Total <strong><?= $bMetrics['total_units']; ?></strong> units) in one complete set.</small>
                                                </div>
                                                <a href="#tab_bundle_contents" onclick="$('#tab_bundle_contents').tab('show'); if($('#product_description_content').length){$('html, body').animate({scrollTop: $('#product_description_content').offset().top - 80}, 300);}" class="btn btn-sm btn-primary font-weight-bold" style="white-space:nowrap; margin-left:10px;">
                                                    View Package Contents &darr;
                                                </a>
                                            </div>
                                        <?php endif; ?>

                                        <div class="row-custom details">
                                            <div class="item-details<?= $product->listing_type == 'ordinary_listing' || $product->product_type == 'digital' ? ' hidden' : ''; ?>">
                                                <div class="left">
                                                    <label><?= trans("status"); ?></label>
                                                </div>
                                                <div class="right">
                                                    <span id="span-product-stock-status" class="status-in-stock <?= $productStock > 0 ? 'text-success' : 'text-danger'; ?>"><?= $productStock > 0 ? trans("in_stock") : trans("out_of_stock"); ?></span>
                                                </div>
                                            </div>
                                            <?php if ($productSettings->marketplace_sku == 1 && !empty($product->sku)): ?>
                                                <div class="item-details">
                                                    <div class="left">
                                                        <label><?= trans("sku"); ?></label>
                                                    </div>
                                                    <div class="right">
                                                        <span id="product-sku"><?= esc($productSku); ?></span>
                                                    </div>
                                                </div>
                                            <?php endif;
                                            if ($product->product_type == 'digital' && !empty($product->files_included)): ?>
                                                <div class="item-details">
                                                    <div class="left">
                                                        <label><?= trans("files_included"); ?></label>
                                                    </div>
                                                    <div class="right">
                                                        <span><?= esc($product->files_included); ?></span>
                                                    </div>
                                                </div>
                                            <?php endif;
                                            if ($product->listing_type == 'ordinary_listing'): ?>
                                                <div class="item-details">
                                                    <div class="left">
                                                        <label><?= trans("uploaded"); ?></label>
                                                    </div>
                                                    <div class="right">
                                                        <span><?= timeAgo($product->created_at); ?></span>
                                                    </div>
                                                </div>
                                            <?php endif;
                                            if (!empty($productCustomFieldsValues) && !empty($productCustomFieldsValues['top']) && countItems($productCustomFieldsValues['top']) > 0):
                                                foreach ($productCustomFieldsValues['top'] as $item):?>
                                                    <div class="item-details">
                                                        <div class="left">
                                                            <label><?= esc($item['name']); ?></label>
                                                        </div>
                                                        <div class="right">
                                                            <span><?= esc($item['value']); ?></span>
                                                        </div>
                                                    </div>
                                                <?php endforeach;
                                            endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <?php echo view('product/details/_product_details_form', [
                                    'product' => $product,
                                    'productStock' => $productStock,
                                    'initialProductData_json' => $initialProductData_json,
                                    'isProductInWishlist' => $isProductInWishlist,
                                    'showVendorContactInfo' => $showVendorContactInfo,
                                    'editingCartItem' => $editingCartItem ?? null
                                ]); ?>

                                <?php if (!empty($digitalSale) && $product->is_free_product != 1): ?>
                                    <div class="row">
                                        <div class="col-12 product-already-purchased text-success">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bag-check-fill" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd" d="M10.5 3.5a2.5 2.5 0 0 0-5 0V4h5v-.5zm1 0V4H15v10a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V4h3.5v-.5a3.5 3.5 0 1 1 7 0zm-.646 5.354a.5.5 0 0 0-.708-.708L7.5 10.793 6.354 9.646a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0l3-3z"/>
                                            </svg>&nbsp;<?= trans("msg_product_already_purchased") ?>&nbsp;
                                            <?php if (!empty($product->digital_file_download_link)): ?>
                                                <a href="<?= esc($product->digital_file_download_link); ?>" class="text-success" target="_blank">
                                                    <?= trans("download"); ?>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download">
                                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                        <polyline points="7 10 12 15 17 10"></polyline>
                                                        <line x1="12" y1="15" x2="12" y2="3"></line>
                                                    </svg>
                                                </a>
                                            <?php else: ?>
                                                <form action="<?= base_url('download-purchased-digital-file-post'); ?>" method="post" class="d-inline-block">
                                                    <?= csrf_field(); ?>
                                                    <input type="hidden" name="sale_id" value="<?= $digitalSale->id; ?>">
                                                    <button type="submit" name="submit" value="<?= $product->listing_type == 'license_key' ? 'license_certificate' : 'main_files'; ?>" class="btn-product-download">
                                                        <?= trans("download"); ?>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download">
                                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                            <polyline points="7 10 12 15 17 10"></polyline>
                                                            <line x1="12" y1="15" x2="12" y2="3"></line>
                                                        </svg>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="product-delivery-est">
                                    <?php if ($shippingStatus == 1):
                                        if (!empty($deliveryTime)): ?>
                                            <div class="item">
                                                <div class="title">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 32 32">
                                                        <path fill="#7c818b" d="M0 6v2h19v15h-6.156c-.446-1.719-1.992-3-3.844-3s-3.398 1.281-3.844 3H4v-5H2v7h3.156c.446 1.719 1.992 3 3.844 3s3.398-1.281 3.844-3h8.312c.446 1.719 1.992 3 3.844 3s3.398-1.281 3.844-3H32v-8.156l-.063-.157l-2-6L29.72 10H21V6zm1 4v2h9v-2zm20 2h7.281L30 17.125V23h-1.156c-.446-1.719-1.992-3-3.844-3s-3.398 1.281-3.844 3H21zM2 14v2h6v-2zm7 8c1.117 0 2 .883 2 2s-.883 2-2 2s-2-.883-2-2s.883-2 2-2m16 0c1.117 0 2 .883 2 2s-.883 2-2 2s-2-.883-2-2s.883-2 2-2"/>
                                                    </svg>&nbsp;&nbsp;<span><?= @parseSerializedOptionArray($deliveryTime->option_array, selectedLangId()); ?></span>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <div class="item">
                                            <div class="title">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 32 32">
                                                    <path fill="#7c818b" d="M16 4C9.383 4 4 9.383 4 16s5.383 12 12 12s12-5.383 12-12S22.617 4 16 4m0 2c5.535 0 10 4.465 10 10s-4.465 10-10 10S6 21.535 6 16S10.465 6 16 6m-1 2v9h7v-2h-5V8z"/>
                                                </svg>&nbsp;&nbsp;<span><?= trans("estimated_delivery"); ?>:</span>
                                            </div>&nbsp;
                                            <?php $estLocation = getEstimatedDeliveryLocation();
                                            if (!empty($estLocation)): ?>
                                                <div class="display-flex align-items-center flex-wrap">
                                                    <?= $estimatedDelivery; ?>
                                                    <button type="button" data-toggle="modal" data-target="#locationModal" class="nav-link btn-modal-location button-link btn-modal-location-product" aria-label="location-modal">
                                                        <div class="badge badge-info-light">
                                                            <?= esc($estLocation); ?>&nbsp;<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="#15a0b6" viewBox="0 0 256 256">
                                                                <path d="M181.66,133.66l-80,80a8,8,0,0,1-11.32-11.32L164.69,128,90.34,53.66a8,8,0,0,1,11.32-11.32l80,80A8,8,0,0,1,181.66,133.66Z"></path>
                                                            </svg>
                                                        </div>
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                <button type="button" data-toggle="modal" data-target="#locationModal" class="nav-link btn-modal-location button-link link-underlined btn-modal-location-product" aria-label="location-modal"><?= trans("select_location") ?></button>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="item">
                                        <strong><?= trans("share"); ?>:</strong>&nbsp;<?= view("product/details/_product_share"); ?>
                                    </div>
                                </div>


                                <?//= view("product/details/_product_details"); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <!-- BUNDLE BUILDER -->
                        <div id="wrapper">
                            <div class="container">
                            <div class="row">

                            <!-- LEFT PRODUCTS -->
                            <div class="col-lg-8">
                                <div class="shopping-cart mt-0">
                                    <h1 class="cart-section-title">Build Your Bundle</h1>
                                                <?php
                                                // echo '<pre>';
                                                // print_r($relatedProducts);
                                                // exit();
                                                ?>
                                    <?php foreach($relatedProducts as $item): ?>
                                        <?php
                                        $cnvtcry = $item->listing_type == 'ordinary_listing' ? false : true;
                                        $prdtPrice = !empty($item->price) && $item->price > 0 ? priceFormatted($item->price, $item->currency, $cnvtcry) : '';
                                        $prdtPriceDiscounted = priceFormatted($item->price_discounted, $item->currency, $cnvtcry);
                                        $prdtDiscountRate = calculateDiscount($item->price, $item->price_discounted);

                                        ?>
                                    <div class="item bundle-item" data-id="<?= $item->id ?>" data-price="<?= $item->price_discounted ?>">

                                        <div class="cart-item-image">
                                            <div class="product-image-box product-image-box-md">
                                                <img src="<?= getProductItemImage($item); ?>" class="img-fluid">
                                            </div>
                                        </div>

                                        <div class="cart-item-details">
                                            <h5 class="product-title"><?= esc($item->title); ?></h5>
                                                <!-- <div><strong>₹<?//= $item->price ?></strong></div> -->
                                                 
                                            <div class="item-details<?= $item->listing_type == 'ordinary_listing' || $item->product_type == 'digital' ? ' hidden' : ''; ?>">
                                                <div class="">
                                                    <label><?= trans("status"); ?></label>
                                                    <span id="span-product-stock-status" class="status-in-stock <?= $item->stock > 0 ? 'text-success' : 'text-danger'; ?>"><?= $item->stock > 0 ? trans("in_stock") : trans("out_of_stock"); ?></span>
                                                </div>
                                            </div>
                                    
                                            <div class="product-price-container">
                                                <?php if ($item->is_free_product): ?>
                                                    <div id="div-product-price" class="text-product-discounted">
                                                        <span class="final-price final-price-free"><?= trans("free"); ?></span>
                                                    </div>
                                                <?php elseif ($item->listing_type == 'ordinary_listing' && $item->is_sold): ?>
                                                    <div id="div-product-price">
                                                        <span class="final-price text-muted"><?= trans("sold"); ?></span>
                                                    </div>
                                                <?php else:
                                                    if (!empty($prdtPrice)):?>
                                                        <div id="div-product-discounted-price" class="<?= $prdtDiscountRate > 0 ? 'text-product-discounted' : ''; ?>">
                                                            <span class="final-price"><?= $prdtPriceDiscounted; ?></span>
                                                        </div>
                                                        <div id="div-product-price">
                                                            <?php if ($prdtDiscountRate > 0): ?>
                                                                <span class="original-price"><?= $prdtPrice; ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div id="div-product-discount-rate">
                                                            <?php if ($prdtDiscountRate > 0): ?>
                                                                <span class="discount-rate">-<?= discountRateFormat($prdtDiscountRate); ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endif;
                                                endif; ?>
                                            </div>
                                        </div>
                                    
                                        <div class="cart-item-quantity">
                                            <div class="number-spinner">
                                                <div class="input-group">
                                                    <span class="input-group-btn">
                                                        <button class="btn btn-default minus">-</button>
                                                    </span>
                                                    <input type="text" class="form-control qty text-center" value="0">
                                                    <span class="input-group-btn">
                                                        <button class="btn btn-default plus">+</button>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- RIGHT TOTAL PANEL -->
                            <div class="col-lg-4">
                                <div class="right">
                                    <div class="row-custom m-b-15">
                                        <strong>Total Bundle
                                            <span class="float-right">₹<span id="bundleTotal"><?= $product->price_discounted > 0 ? $product->price_discounted : $product->price ?></span></span>
                                        </strong>
                                    </div>

                                    <div class="row-custom m-t-30">
                                        <button id="btnAddBundle" class="btn btn-block btn-custom btn-lg">
                                            PROCEED TO CART
                                        </button>
                                    </div>
                                </div>
                            </div>

                            </div>
                            </div>
                        </div>

                        <div id="product_description_content" class="product-description post-text-responsive">
                            <?php $session = session();
                            $isReviewTabActive = false;
                            if (!empty($session->getFlashdata('review_added'))) {
                                $isReviewTabActive = true;
                            }
                            $bundleCheck = !empty($product->is_bundle) ? true : false;
                            if (!$bundleCheck) {
                                $bMetrics = (new \App\Models\BundleModel())->calculateBundleMetrics($product->id);
                                if (!empty($bMetrics['total_components']) && $bMetrics['total_components'] > 0) {
                                    $bundleCheck = true;
                                }
                            }
                            $isPackageTabActive = false;
                            $isDescriptionTabActive = false;
                            if ($isReviewTabActive) {
                                // Review tab active
                            } elseif ($bundleCheck) {
                                $isPackageTabActive = true;
                            } else {
                                $isDescriptionTabActive = true;
                            }
                            $packageContentsTitle = (function_exists('trans') && trans("package_details") !== "package_details" && !empty(trans("package_details"))) ? trans("package_details") : ((function_exists('trans') && trans("package_contents") !== "package_contents" && !empty(trans("package_contents"))) ? trans("package_contents") : "Package Details");
                            ?>
                            <ul class="nav nav-tabs nav-tabs-horizontal">
                                <?php if ($bundleCheck): ?>
                                    <li class="nav-item">
                                        <a class="nav-link <?= $isPackageTabActive ? 'active' : ''; ?>" id="tab_bundle_contents" data-toggle="tab" href="#tab_bundle_contents_content"><i class="fa fa-cubes text-primary"></i> <?= $packageContentsTitle; ?></a>
                                    </li>
                                <?php endif; ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $isDescriptionTabActive ? 'active' : ''; ?>" id="tab_description" data-toggle="tab" href="#tab_description_content"><?= trans("description"); ?></a>
                                </li>
                                <?php if (!empty($productCustomFieldsValues) && !empty($productCustomFieldsValues['bottom']) && countItems($productCustomFieldsValues['bottom']) > 0): ?>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab_additional_information" data-toggle="tab" href="#tab_additional_information_content"><?= trans("additional_information"); ?></a>
                                    </li>
                                <?php endif; ?>
                                <?php if ($shippingStatus == 1 || $productLocationStatus == 1): ?>
                                    <li class="nav-item">
                                        <?php if ($shippingStatus == 1 && $productLocationStatus != 1): ?>
                                            <a class="nav-link" id="tab_shipping" data-toggle="tab" href="#tab_shipping_content"><?= trans("shipping"); ?></a>
                                        <?php elseif ($shippingStatus != 1 && $productLocationStatus == 1): ?>
                                            <a class="nav-link" id="tab_shipping" data-toggle="tab" href="#tab_shipping_content" onclick="loadProductShopLocationMap();"><?= trans("location"); ?></a>
                                        <?php else: ?>
                                            <a class="nav-link" id="tab_shipping" data-toggle="tab" href="#tab_shipping_content" onclick="loadProductShopLocationMap();"><?= trans("shipping_location"); ?></a>
                                        <?php endif; ?>
                                    </li>
                                <?php endif;
                                if ($generalSettings->reviews == 1): ?>
                                    <li class="nav-item">
                                        <a class="nav-link <?= $isReviewTabActive == true ? 'active' : ''; ?>" id="tab_reviews" data-toggle="tab" href="#tab_reviews_content"><?= trans("reviews"); ?>&nbsp;(<?= $reviewsCount; ?>)</a>
                                    </li>
                                <?php endif;
                                if ($generalSettings->product_comments == 1): ?>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab_comments" data-toggle="tab" href="#tab_comments_content"><?= trans("comments"); ?>&nbsp;(<?= $commentsCount; ?>)</a>
                                    </li>
                                <?php endif;
                                if ($generalSettings->facebook_comment_status == 1): ?>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab_facebook_comments" data-toggle="tab" href="#tab_facebook_comments_content"><?= trans("facebook_comments"); ?></a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                            <div id="accordion" class="tab-content">
                                <?php if ($bundleCheck): ?>
                                    <div class="tab-pane fade <?= $isPackageTabActive ? 'show active' : ''; ?>" id="tab_bundle_contents_content">
                                        <div class="card">
                                            <div class="card-header">
                                                <a class="card-link <?= $isPackageTabActive ? '' : 'collapsed'; ?>" data-toggle="collapse" href="#collapse_bundle_contents_content">
                                                    <?= $packageContentsTitle; ?><i class="icon-arrow-down"></i><i class="icon-arrow-up"></i>
                                                </a>
                                            </div>
                                            <div id="collapse_bundle_contents_content" class="collapse-description-content collapse <?= $isPackageTabActive ? 'show' : ''; ?>">
                                                <div class="tab-content-inner">
                                                    <?= view('product/details/_bundle_contents', ['editingCartItem' => $editingCartItem ?? null]); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div class="tab-pane fade <?= $isDescriptionTabActive ? 'show active' : ''; ?>" id="tab_description_content">
                                    <div class="card">
                                        <div class="card-header">
                                            <a class="card-link <?= $isDescriptionTabActive ? '' : 'collapsed'; ?>" data-toggle="collapse" href="#collapse_description_content">
                                                <?= trans("description"); ?><i class="icon-arrow-down"></i><i class="icon-arrow-up"></i>
                                            </a>
                                        </div>
                                        <div id="collapse_description_content" class="collapse-description-content collapse <?= $isDescriptionTabActive ? 'show' : ''; ?>">
                                            <div class="tab-content-inner">
                                                <div class="description">
                                                    <?= !empty($productDetails->description) ? $productDetails->description : ''; ?>
                                                </div>

                                                <div class="row-custom text-right m-b-10">
                                                    <?php if (authCheck()):
                                                        if (isActiveAffiliateProduct($product, $user)): ?>
                                                            <button type="button" id="btnCreateAffiliateLink" class="button-link text-muted link-abuse-report link-abuse-report-button display-inline-flex align-items-center" data-id="<?= $product->id; ?>">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 640 512" fill="currentColor">
                                                                    <path d="M579.8 267.7c56.5-56.5 56.5-148 0-204.5c-50-50-128.8-56.5-186.3-15.4l-1.6 1.1c-14.4 10.3-17.7 30.3-7.4 44.6s30.3 17.7 44.6 7.4l1.6-1.1c32.1-22.9 76-19.3 103.8 8.6c31.5 31.5 31.5 82.5 0 114L422.3 334.8c-31.5 31.5-82.5 31.5-114 0c-27.9-27.9-31.5-71.8-8.6-103.8l1.1-1.6c10.3-14.4 6.9-34.4-7.4-44.6s-34.4-6.9-44.6 7.4l-1.1 1.6C206.5 251.2 213 330 263 380c56.5 56.5 148 56.5 204.5 0L579.8 267.7zM60.2 244.3c-56.5 56.5-56.5 148 0 204.5c50 50 128.8 56.5 186.3 15.4l1.6-1.1c14.4-10.3 17.7-30.3 7.4-44.6s-30.3-17.7-44.6-7.4l-1.6 1.1c-32.1 22.9-76 19.3-103.8-8.6C74 372 74 321 105.5 289.5L217.7 177.2c31.5-31.5 82.5-31.5 114 0c27.9 27.9 31.5 71.8 8.6 103.9l-1.1 1.6c-10.3 14.4-6.9 34.4 7.4 44.6s34.4 6.9 44.6-7.4l1.1-1.6C433.5 260.8 427 182 377 132c-56.5-56.5-148-56.5-204.5 0L60.2 244.3z"/>
                                                                </svg>&nbsp;<?= trans("create_affiliate_link"); ?>
                                                            </button>&nbsp;&nbsp;&nbsp;&nbsp;
                                                        <?php endif;
                                                        if ($product->user_id != user()->id): ?>
                                                            <button type="button" class="button-link text-muted link-abuse-report link-abuse-report-button display-inline-flex align-items-center" data-toggle="modal" data-target="#reportProductModal">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 512 512" fill="currentColor">
                                                                    <path d="M256 32c14.2 0 27.3 7.5 34.5 19.8l216 368c7.3 12.4 7.3 27.7 .2 40.1S486.3 480 472 480H40c-14.3 0-27.6-7.7-34.7-20.1s-7-27.8 .2-40.1l216-368C228.7 39.5 241.8 32 256 32zm0 128c-13.3 0-24 10.7-24 24V296c0 13.3 10.7 24 24 24s24-10.7 24-24V184c0-13.3-10.7-24-24-24zm32 224a32 32 0 1 0 -64 0 32 32 0 1 0 64 0z"/>
                                                                </svg>&nbsp;<?= trans("report_this_product"); ?>
                                                            </button>
                                                        <?php endif;
                                                    else: ?>
                                                        <button type="button" class="button-link text-muted link-abuse-report link-abuse-report-product display-inline-flex align-items-center" data-toggle="modal" data-target="#loginModal">
                                                            <?= trans("report_this_product"); ?>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if (!empty($productCustomFieldsValues) && !empty($productCustomFieldsValues['bottom']) && countItems($productCustomFieldsValues['bottom']) > 0): ?>
                                    <div class="tab-pane fade" id="tab_additional_information_content">
                                        <div class="card">
                                            <div class="card-header">
                                                <a class="card-link collapsed" data-toggle="collapse" href="#collapse_additional_information_content">
                                                    <?= trans("additional_information"); ?><i class="icon-arrow-down"></i><i class="icon-arrow-up"></i>
                                                </a>
                                            </div>
                                            <div id="collapse_additional_information_content" class="collapse-description-content collapse">
                                                <div class="tab-content-inner">
                                                    <table class="table table-striped table-product-additional-information">
                                                        <tbody>
                                                        <?php foreach ($productCustomFieldsValues['bottom'] as $item): ?>
                                                            <tr>
                                                                <td class="td-left"><?= esc($item['name']); ?></td>
                                                                <td class="td-right"><?= esc($item['value']); ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif;
                                if ($shippingStatus == 1 || $productLocationStatus == 1): ?>
                                    <div class="tab-pane fade" id="tab_shipping_content">
                                        <div class="card">
                                            <div class="card-header">
                                                <?php if ($shippingStatus == 1 && $productLocationStatus != 1): ?>
                                                    <a class="card-link collapsed" data-toggle="collapse" href="#collapse_shipping_content"><?= trans("shipping"); ?><i class="icon-arrow-down"></i><i class="icon-arrow-up"></i></a>
                                                <?php elseif ($shippingStatus != 1 && $productLocationStatus == 1): ?>
                                                    <a class="card-link collapsed" data-toggle="collapse" href="#collapse_shipping_content" onclick="loadProductShopLocationMap();"><?= trans("location"); ?><i class="icon-arrow-down"></i><i class="icon-arrow-up"></i></a>
                                                <?php else: ?>
                                                    <a class="card-link collapsed" data-toggle="collapse" href="#collapse_shipping_content" onclick="loadProductShopLocationMap();"><?= trans("shipping_location"); ?><i class="icon-arrow-down"></i><i class="icon-arrow-up"></i></a>
                                                <?php endif; ?>
                                            </div>
                                            <div id="collapse_shipping_content" class="collapse-description-content collapse">
                                                <div class="tab-content-inner">
                                                    <table class="table table-product-shipping">
                                                        <tbody>
                                                        <?php if ($shippingStatus == 1): ?>
                                                            <tr>
                                                                <td class="td-left"><?= trans("shipping_cost"); ?></td>
                                                                <td class="td-right">
                                                                    <div class="form-group">
                                                                        <div class="row">
                                                                            <div class="col-12">
                                                                                <label class="control-label"><?= trans("select_your_location"); ?></label>
                                                                            </div>
                                                                            <?php $defaultCountryId = $generalSettings->single_country_mode == 1 ? $generalSettings->single_country_id : $baseVars->defaultLocation->country_id;
                                                                            $shippingStates = !empty($defaultCountryId) ? getStatesByCountry($defaultCountryId) : array(); ?>
                                                                            <?php if ($generalSettings->single_country_mode != 1): ?>
                                                                                <div class="col-12 col-md-4 m-b-sm-15">
                                                                                    <select id="select_countries_product" name="country_id" class="select2 form-control" data-placeholder="<?= trans("country"); ?>" onchange="getStates(this.value, 'product'); $('#product_shipping_cost_container').empty();">
                                                                                        <option></option>
                                                                                        <?php if (!empty($activeCountries)):
                                                                                            foreach ($activeCountries as $item): ?>
                                                                                                <option value="<?= $item->id; ?>"><?= esc($item->name); ?></option>
                                                                                            <?php endforeach;
                                                                                        endif; ?>
                                                                                    </select>
                                                                                </div>
                                                                            <?php else: ?>
                                                                                <input type="hidden" name="country_id" value="<?= $generalSettings->single_country_id; ?>">
                                                                            <?php endif; ?>
                                                                            <div class="col-12 col-md-4 m-b-sm-15">
                                                                                <div id="get_states_container_product">
                                                                                    <select id="select_states_product" name="state_id" class="select2 form-control" data-placeholder="<?= trans("state"); ?>" onchange="getProductShippingCost(this.value, '<?= $product->id; ?>');">
                                                                                        <option></option>
                                                                                        <?php if (!empty($shippingStates)):
                                                                                            foreach ($shippingStates as $item): ?>
                                                                                                <option value="<?= $item->id; ?>" <?= $item->id == $baseVars->defaultLocation->state_id ? 'selected' : ''; ?>><?= esc($item->name); ?></option>
                                                                                            <?php endforeach;
                                                                                        endif; ?>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div id="product_shipping_cost_container" class="product-shipping-methods"></div>
                                                                    <div class="row-custom">
                                                                        <div class="product-shipping-loader">
                                                                            <div class="spinner">
                                                                                <div class="bounce1"></div>
                                                                                <div class="bounce2"></div>
                                                                                <div class="bounce3"></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <?php if (!empty($deliveryTime)): ?>
                                                                <tr>
                                                                    <td class="td-left"><?= trans("shipping"); ?></td>
                                                                    <td class="td-right"><span><?= @parseSerializedOptionArray($deliveryTime->option_array, selectedLangId()); ?></span></td>
                                                                </tr>
                                                            <?php endif;
                                                        endif;
                                                        if ($productLocationStatus == 1):
                                                            if (!empty($product->country_id)):?>
                                                                <tr>
                                                                    <td class="td-left"><?= trans("product_location"); ?></td>
                                                                    <td class="td-right"><span id="span_shop_location_address"><?= getLocation($product); ?></span></td>
                                                                </tr>
                                                            <?php else: ?>
                                                                <tr>
                                                                    <td class="td-left"><?= trans("shop_location"); ?></td>
                                                                    <td class="td-right"><span id="span_shop_location_address"><?= getLocation($user); ?></span></td>
                                                                </tr>
                                                            <?php endif;
                                                        endif; ?>
                                                        </tbody>
                                                    </table>
                                                    <?php if ($productLocationStatus == 1): ?>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="product-location-map">
                                                                    <iframe id="iframe_shop_location_address" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif;
                                if ($generalSettings->reviews == 1): ?>
                                    <div class="tab-pane fade <?= $isReviewTabActive == true ? 'show active' : ''; ?>" id="tab_reviews_content">
                                        <div class="card">
                                            <div class="card-header">
                                                <a class="card-link collapsed" data-toggle="collapse" href="#collapse_reviews_content">
                                                    <?= trans("reviews"); ?><i class="icon-arrow-down"></i><i class="icon-arrow-up"></i>
                                                </a>
                                            </div>
                                            <div id="collapse_reviews_content" class="collapse-description-content collapse">
                                                <div id="review-result">
                                                    <?= view('product/details/_reviews'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif;
                                if ($generalSettings->product_comments == 1): ?>
                                    <div class="tab-pane fade" id="tab_comments_content">
                                        <div class="card">
                                            <div class="card-header">
                                                <a class="card-link collapsed" data-toggle="collapse" href="#collapse_comments_content">
                                                    <?= trans("comments"); ?><i class="icon-arrow-down"></i><i class="icon-arrow-up"></i>
                                                </a>
                                            </div>
                                            <div id="collapse_comments_content" class="collapse-description-content collapse">
                                                <?= view('product/details/_comments', ['commentsArray' => $commentsArray]); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif;
                                if ($generalSettings->facebook_comment_status == 1): ?>
                                    <div class="tab-pane fade" id="tab_facebook_comments_content">
                                        <div class="card">
                                            <div class="card-header">
                                                <a class="card-link collapsed" data-toggle="collapse" href="#collapse_facebook_comments_content">
                                                    <?= trans("facebook_comments"); ?><i class="icon-arrow-down"></i><i class="icon-arrow-up"></i>
                                                </a>
                                            </div>
                                            <div id="collapse_facebook_comments_content" class="collapse-description-content collapse">
                                                <div class="fb-comments" data-href="<?= current_url(); ?>" data-width="100%" data-numposts="5" data-colorscheme="light"></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?= view('partials/_ad_spaces', ['adSpace' => 'product_1', 'class' => 'mb-4']); ?>
            <?php if (!empty($userProducts) && $generalSettings->multi_vendor_system == 1): ?>
                <div class="col-12 section section-related-products m-t-30">
                    <strong class="title"><?= trans("more_from"); ?>&nbsp;<a href="<?= generateProfileUrl($user->slug); ?>"><?= esc(getUsername($user)); ?></a></strong>
                    <div class="row row-product">
                        <?php $count = 0;
                        $maxCount = $generalSettings->index_products_per_row == 6 ? 6 : 5;
                        foreach ($userProducts as $item):
                            if ($count < $maxCount):?>
                                <div class="col-6 col-sm-4 col-md-3 col-product <?= $generalSettings->index_products_per_row == 5 ? 'col-product-5' : 'col-product-6'; ?>">
                                    <?= view('product/_product_item', ['product' => $item]); ?>
                                </div>
                            <?php endif;
                            $count++;
                        endforeach; ?>
                    </div>
                    <?php if (countItems($userProducts) > 5): ?>
                        <div class="row-custom text-center">
                            <a href="<?= generateProfileUrl($product->user_slug); ?>" class="link-see-more"><span><?= trans("view_all"); ?>&nbsp;</span><i class="icon-arrow-right"></i></a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif;
            if (!empty($relatedProducts) && countItems($relatedProducts) > 0):
                shuffle($relatedProducts); ?>
                <div class="col-12 section section-related-products">
                    <strong class="title"><?= trans("you_may_also_like"); ?></strong>
                    <div class="row row-product">
                        <?php $i = 0;
                        $maxCount = $generalSettings->index_products_per_row == 6 ? 12 : 10;
                        foreach ($relatedProducts as $item):
                            if ($i < $maxCount):?>
                                <div class="col-6 col-sm-4 col-md-3 col-product <?= $generalSettings->index_products_per_row == 5 ? 'col-product-5' : 'col-product-6'; ?>">
                                    <?= view('product/_product_item', ['product' => $item]); ?>
                                </div>
                            <?php endif;
                            $i++;
                        endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            <?= view('partials/_ad_spaces', ['adSpace' => 'product_2', 'class' => 'mb-4']); ?>
        </div>
    </div>
</div>

<?= view('partials/_modal_send_message', ['subject' => esc($title), 'productId' => $product->id]); ?>

<?php if (isActiveAffiliateProduct($product, $user)): ?>
    <div class="modal fade" id="affliateLinkModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content modal-custom modal-affiliate-link">
                <div class="modal-header">
                    <h5 class="modal-title m-b-15"><?= trans("affiliate_link"); ?></h5>
                    <div class="affiliate-link-exp"><?= trans("affiliate_link_exp"); ?></div>
                    <button type="button" class="close" data-dismiss="modal">
                        <span><i class="icon-close"></i> </span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <?php
                            $affCommissionRate = 0;
                            $affDiscountRate = 0;
                            if ($affiliateSettings->type == 'site_based') {
                                $affCommissionRate = !empty($affiliateSettings->commission_rate) ? $affiliateSettings->commission_rate : 0;
                                $affDiscountRate = !empty($affiliateSettings->discount_rate) ? $affiliateSettings->discount_rate : 0;
                            } else {
                                $affCommissionRate = !empty($user->affiliate_commission_rate) ? $user->affiliate_commission_rate : 0;
                                $affDiscountRate = !empty($user->affiliate_discount_rate) ? $user->affiliate_discount_rate : 0;
                            } ?>

                            <?php if (!empty($affCommissionRate)): ?>
                                <div class="m-b-15"><?= trans("referrer_commission_rate"); ?>:&nbsp;<strong><?= esc($affCommissionRate); ?>%</strong></div>
                            <?php endif; ?>
                            <?php if (!empty($affDiscountRate)): ?>
                                <div class="m-b-15"><?= trans("buyer_discount_rate"); ?>:&nbsp;<strong><?= esc($affDiscountRate); ?>%</strong></div>
                            <?php endif; ?>
                            <div class="copy-code-container copy-code-container-link">
                                <span class="code" id="spanAffLink"></span>
                            </div>
                        </div>
                        <div class="col-12 text-center">
                            <button type="button" id="btnCopyAffLink" class="btn btn-block"><span><?= trans("copy_link"); ?></span></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (authCheck() && $product->user_id != user()->id): ?>
    <div class="modal fade" id="reportProductModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content modal-custom modal-report-abuse">
                <form id="form_report_product" method="post">
                    <input type="hidden" name="id" value="<?= $product->id; ?>">
                    <div class="modal-header">
                        <h5 class="modal-title"><?= trans("report_this_product"); ?></h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span><i class="icon-close"></i> </span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div id="response_form_report_product" class="col-12"></div>
                            <div class="col-12">
                                <div class="form-group m-0">
                                    <label class="control-label"><?= trans("description"); ?></label>
                                    <textarea name="description" class="form-control form-textarea" placeholder="<?= trans("abuse_report_exp"); ?>" minlength="5" maxlength="10000" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer text-right">
                        <button type="submit" class="btn btn-md btn-custom"><?= trans("submit"); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="modal fade" id="reportCommentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content modal-custom">
            <form id="form_report_comment" method="post">
                <div class="modal-header">
                    <h5 class="modal-title"><?= trans("report_comment"); ?></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span><i class="icon-close"></i> </span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div id="response_form_report_comment" class="col-12"></div>
                        <div class="col-12">
                            <input type="hidden" id="report_comment_id" name="id" value="">
                            <div class="form-group m-0">
                                <label class="control-label"><?= trans("description"); ?></label>
                                <textarea name="description" class="form-control form-textarea" placeholder="<?= trans("abuse_report_exp"); ?>" minlength="5" maxlength="10000" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer text-right">
                    <button type="submit" class="btn btn-md btn-custom"><?= trans("submit"); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if ($generalSettings->facebook_comment_status == 1):
    echo $generalSettings->facebook_comment;
endif; ?>


<style>.product-location-map .embed-responsive {
        overflow: visible;
    }</style>
    <style>
        .bundle-card{
            border:1px solid #ddd;
            padding:10px;
            cursor:pointer;
            border-radius:8px;
            transition:0.2s;
            text-align:center;
        }
        .bundle-card.selected{
            border:3px solid #000;
        }
        .bundle-card.selected h6,
        .bundle-card.selected p{
            font-weight:700;
            color:#000;
        }
    </style>

<script>
// let basePrice = <?//= $product->price ?>;
// let total = basePrice;

// document.querySelectorAll('.bundle-card').forEach(card=>{
//     card.addEventListener('click',function(){
//         let price = parseFloat(this.dataset.price);
//         if(this.classList.contains('selected')){
//             this.classList.remove('selected');
//             total -= price;
//         }else{
//             this.classList.add('selected');
//             total += price;
//         }
//         document.getElementById('bundleTotal').innerText = total;
//     });
// });

// document.getElementById('btnAddBundle').addEventListener('click',function(){

//     let items = [];
//     items.push({product_id: <?= $product->id ?>, qty:1, variant_id:null, extra_options:null});

//     document.querySelectorAll('.bundle-card.selected').forEach(card=>{
//         items.push({product_id:card.dataset.id, qty:1, variant_id:null, extra_options:null});
//     });

//     var data = {
//         'cart_items': JSON.stringify({items:items})
//     };
//     $.ajax({
//         type: 'POST',
//         url: generateUrl('cart/add-to-cart-bundle'),
//         data: data,
//         success: function (response) {
//         console.log(response);
//             // location.reload();
//         }
//     });
// });
</script>
<script>
// let basePrice = <?//= $basePrice ?>;
// let total = basePrice;

// document.querySelectorAll('.bundle-item').forEach(item=>{

//     let qtyInput = item.querySelector('.qty');
//     let price = parseFloat(item.dataset.price);

//     item.querySelector('.plus').onclick = ()=>{
//         let q = parseInt(qtyInput.value) + 1;
//         qtyInput.value = q;
//         total += price;
//         updateTotal();
//     };

//     item.querySelector('.minus').onclick = ()=>{
//         let q = parseInt(qtyInput.value);
//         if(q>0){
//             qtyInput.value = q-1;
//             total -= price;
//             updateTotal();
//         }
//     };
// });

// function updateTotal(){
//     document.getElementById('bundleTotal').innerText = total;
// }

// document.getElementById('btnAddBundle').onclick = ()=>{
// alert('dsfds');
//     let items = [];
//     items.push({product_id:<?= $product->id ?>, qty:1, variant_id:null, extra_options:null});

//     document.querySelectorAll('.bundle-item').forEach(i=>{
//         let q = parseInt(i.querySelector('.qty').value);
//         if(q>0){
//             items.push({
//                 product_id:i.dataset.id,
//                 qty:q,
//                 variant_id:null,
//                 extra_options:null
//             });
//         }
//     });

    
//     var data = {
//         'cart_items': JSON.stringify({items:items})
//     };
//     $.ajax({
//         type: 'POST',
//         url: generateUrl('cart/add-to-cart-bundle'),
//         data: data,
//         success: function (response) {
//         console.log(response);
//             // location.reload();
//         }
//     });

// };
</script>
<script>
let mainPrice = parseFloat(<?= $product->price_discounted ?>);
let mainQtyInput = document.getElementById('input_product_quantity');

let total = mainPrice * parseInt(mainQtyInput.value);
// let total = <?//= $product->price_discounted ?>;

document.querySelectorAll('.bundle-item').forEach(item=>{
    let price = parseFloat(item.dataset.price);
    let qtyInput = item.querySelector('.qty');

    item.querySelector('.plus').onclick=()=>{
        qtyInput.value = parseInt(qtyInput.value)+1;
        total += price;
        document.getElementById('bundleTotal').innerText = total;
    };

    item.querySelector('.minus').onclick=()=>{
        let q=parseInt(qtyInput.value);
        if(q>0){
            qtyInput.value = q-1;
            total -= price;
            document.getElementById('bundleTotal').innerText = total;
        }
    };
});

document.getElementById('btnAddBundle').onclick=()=>{

    let items=[];
    let mainQty = parseInt(document.getElementById('input_product_quantity').value) || 1;
    items.push({main_product_id:<?= $product->id ?>,product_id:<?= $product->id ?>, qty:mainQty});

    document.querySelectorAll('.bundle-item').forEach(i=>{
        let q=parseInt(i.querySelector('.qty').value);
        if(q>0) items.push({product_id:i.dataset.id, qty:q});
    });

    var data = {
        'cart_items': JSON.stringify({items:items})
    };
    $.ajax({
        type: 'POST',
        url: generateUrl('cart/add-to-cart-bundle'),
        data: data,
        success: function (response) {

        // console.log(response);
            location.reload();
            if (response.result == 1) {
                location.href = generateUrl('cart'); // or modal open
            } else {
                alert("Failed to add bundle to cart");
            }
            
        }
    });
};
</script>


