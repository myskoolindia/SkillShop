<!DOCTYPE html>
<html lang="<?= $activeLang->short_form; ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <title><?= esc($title); ?> - <?= esc($baseSettings->site_title); ?></title>
    <meta name="description" content="<?= esc($description); ?>"/>
    <meta name="keywords" content="<?= esc($keywords); ?>"/>
    <meta name="author" content="<?= esc($generalSettings->application_name); ?>"/>
    <link rel="shortcut icon" type="image/png" href="<?= getFavicon(); ?>"/>
    <meta property="og:locale" content="en-US"/>
    <meta property="og:site_name" content="<?= esc($generalSettings->application_name); ?>"/>
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css'); ?>"/>
</head>
<body>
<div class="container" style="width: 898px; max-width: 898px;min-width: 898px;">
    <div class="row">
        <div class="col-12">
            <div class="container-invoice">
                <div id="content" class="card">
                    <div class="card-body invoice p-0">
                        <div class="row">
                            <div class="col-12">
                                <h1 style="text-align: center; font-size: 36px;font-weight: 400;margin-top: 20px;"><?= trans("invoice"); ?></h1>
                            </div>
                        </div>
                        <div class="row" style="padding: 45px 30px;">
                            <div class="col-6">
                                <div class="logo">
                                    <img src="<?= getLogo(); ?>" alt="logo">
                                </div>
                                <div>
                                    <p style="margin-bottom: 5px;"><?= esc($baseSettings->contact_address); ?></p>
                                    <p style="margin-bottom: 5px;"><?= esc($baseSettings->contact_email); ?></p>
                                    <p style="margin-bottom: 5px;"><?= esc($baseSettings->contact_phone); ?></p>
                                    <div>
                                        <?= getAdditionalInvoiceInfo(selectedLangId()); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="float-right">
                                    <p class="font-weight-bold mb-1"><span style="display: inline-block;width: 100px;"><?= trans("invoice"); ?>:</span>#<?= 'VN' . $order->order_number; ?></p>
                                    <p class="font-weight-bold"><span style="display: inline-block;width: 100px;"><?= trans("date"); ?>:</span><?= formatDate($order->created_at); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="padding: 45px 30px;">
                            <div class="col-6">
                                <p class="font-weight-bold mb-3"><?= trans("client_information"); ?></p>
                                <p class="mb-1"><?= esc($invoice->client_first_name); ?>&nbsp;<?= esc($invoice->client_last_name); ?>&nbsp;(<?= $invoice->client_username; ?>)</p>
                                <?php if (!empty($invoice->client_address)): ?>
                                    <p class="mb-1"><?= esc($invoice->client_address); ?></p>
                                <?php endif;
                                if (!empty($invoice->client_state)): ?>
                                    <p class="mb-1"><?= !empty($invoice->client_city) ? $invoice->client_city . ', ' : '' ?><?= esc($invoice->client_state); ?></p>
                                <?php endif;
                                if (!empty($invoice->client_country)): ?>
                                    <p class="mb-1"><?= esc($invoice->client_country); ?></p>
                                <?php endif;
                                if ($generalSettings->show_customer_phone_seller == 1):
                                    if (!empty($invoice->client_phone_number)): ?>
                                        <p class="mb-1"><?= esc($invoice->client_phone_number); ?></p>
                                    <?php endif;
                                endif;
                                if (!empty($invoice->client_tax_number)): ?>
                                    <p class="mb-1"><?= trans("tax_registration_number"); ?>:&nbsp;<?= esc($invoice->client_tax_number); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="col-6">
                                <div class="float-right">
                                    <p class="font-weight-bold mb-3"><?= trans("payment_details"); ?></p>
                                    <p class="mb-1"><span style="display: inline-block;min-width: 158px;"><?= trans("payment_status"); ?>:</span><?= getPaymentStatus($order->payment_status); ?></p>
                                    <p class="mb-1"><span style="display: inline-block;min-width: 158px;"><?= trans("payment_method"); ?>:</span><?= getPaymentMethod($order->payment_method); ?></p>
                                    <p class="mb-1"><span style="display: inline-block;min-width: 158px;"><?= trans("currency"); ?>:</span><?= $order->price_currency; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="row p-4">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th class="border-0 font-weight-bold" style="width: 12%;"><?= trans("seller"); ?></th>
                                            <th class="border-0 font-weight-bold" style="width: 10%;"><?= trans("product_id"); ?></th>
                                            <th class="border-0 font-weight-bold" style="width: 30%;"><?= trans("description"); ?></th>
                                            <th class="border-0 font-weight-bold text-center" style="width: 8%; text-align: center;"><?= trans("quantity"); ?></th>
                                            <th class="border-0 font-weight-bold text-right" style="width: 13%; text-align: right;"><?= trans("unit_price"); ?></th>
                                            <?php if ($paymentSettings->vat_status): ?>
                                                <th class="border-0 font-weight-bold text-right" style="width: 13%; text-align: right;"><?= trans("vat"); ?></th>
                                            <?php endif; ?>
                                            <th class="border-0 font-weight-bold text-right" style="width: 14%; text-align: right;"><?= trans("total"); ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $saleSubtotal = 0;
                                        $saleVat = 0;
                                        $saleTotal = 0;
                                        $saleShipping = 0;
                                        $shipping = false;
                                        $affiliateDiscount = 0;
                                        $affiliateDiscountRate = 0;
                                        if ($affiliateSettings->status == 1 && $affiliateSettings->type == 'seller_based') {
                                            $affiliate = unserializeData($order->affiliate_data);
                                            if (!empty($affiliate) && !empty($affiliate['discount']) && !empty($affiliate['sellerId']) && user()->id == $affiliate['sellerId']) {
                                                $affiliateDiscount = $affiliate['discount'];
                                                $affiliateDiscountRate = $affiliate['discountRate'];
                                            }
                                        }
                                        if (!empty($invoiceItems) && is_array($invoiceItems)):
                                            foreach ($invoiceItems as $item):
                                                if (!empty($item['id'])):
                                                    $orderProduct = getOrderProduct($item['id']);
                                                    if (!empty($orderProduct)):
                                                        if ($orderProduct->product_type == 'physical') {
                                                            $shipping = true;
                                                        }
                                                        $showItem = false;
                                                        if ($orderProduct->seller_id == user()->id) {
                                                            $showItem = true;
                                                        }
                                                        if ($showItem == true):
                                                            $product = getProduct($orderProduct->product_id);
                                                            $itemSku = !empty($item['sku']) ? $item['sku'] : '';
                                                            if (empty($itemSku) && !empty($product)) {
                                                                $itemSku = $product->sku;
                                                            }
                                                            $saleSubtotal += $orderProduct->product_unit_price * $orderProduct->product_quantity;
                                                            $saleVat += $orderProduct->product_vat;
                                                            $saleShipping = $orderProduct->seller_shipping_cost;
                                                            $saleTotal += $orderProduct->product_total_price; ?>
                                                            <tr style="font-size: 15px;">
                                                                <td style="vertical-align: top;"><?= !empty($item['seller']) ? esc($item['seller']) : ''; ?></td>
                                                                <td style="vertical-align: top;"><?= $orderProduct->product_id; ?></td>
                                                                <td style="vertical-align: top;">
                                                                    <span class="font-weight-600"><?= $orderProduct->product_title; ?></span>
                                                                    <?php if (!empty($itemSku)): ?>
                                                                        <div><small class="text-muted"><?= trans("sku"); ?>:&nbsp;<?= esc($itemSku); ?></small></div>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td class="text-center" style="vertical-align: top; text-align: center;"><?= $orderProduct->product_quantity; ?></td>
                                                                <td class="text-right" style="vertical-align: top; text-align: right; white-space: nowrap;"><?= priceFormatted($orderProduct->product_unit_price, $orderProduct->product_currency); ?></td>
                                                                <?php if ($paymentSettings->vat_status): ?>
                                                                    <td class="text-right" style="vertical-align: top; text-align: right; white-space: nowrap;">
                                                                        <?php if (!empty($orderProduct->product_vat) && $orderProduct->product_vat > 0):
                                                                            $itemGst = calculateGstDetails($orderProduct->product_vat_rate, $orderProduct->product_vat, $orderProduct->seller_id, $invoice->client_state ?? null);
                                                                            if ($itemGst['is_interstate']): ?>
                                                                                <?= priceFormatted($itemGst['igst_amount'], $orderProduct->product_currency); ?><br><small class="text-muted">(<?= $itemGst['igst_rate']; ?>% <?= trans('igst'); ?>)</small>
                                                                            <?php else: ?>
                                                                                <?= priceFormatted($itemGst['cgst_amount'], $orderProduct->product_currency); ?>&nbsp;<small class="text-muted">(<?= $itemGst['cgst_rate']; ?>% <?= trans('cgst'); ?>)</small><br>
                                                                                <?= priceFormatted($itemGst['sgst_amount'], $orderProduct->product_currency); ?>&nbsp;<small class="text-muted">(<?= $itemGst['sgst_rate']; ?>% <?= trans('sgst'); ?>)</small>
                                                                            <?php endif;
                                                                        endif; ?>
                                                                    </td>
                                                                <?php endif; ?>
                                                                <td class="text-right" style="vertical-align: top; text-align: right; white-space: nowrap; font-weight: 600;"><?= priceFormatted($orderProduct->product_total_price, $orderProduct->product_currency); ?></td>
                                                            </tr>
                                                            <?php
                                                            /* ── Bundle / Package Contents Full-Width Row ────────── */
                                                            if (!empty($orderProduct->is_bundle) && !empty($orderProduct->bundle_items)):
                                                                $_biRaw = is_string($orderProduct->bundle_items)
                                                                    ? json_decode($orderProduct->bundle_items, true)
                                                                    : $orderProduct->bundle_items;
                                                                if (!empty($_biRaw) && is_array($_biRaw)):
                                                                    $_biIds = [];
                                                                    foreach ($_biRaw as $_bi) {
                                                                        if ((int)($_bi['qty'] ?? 0) > 0 && !empty($_bi['product_id'])) {
                                                                            $_biIds[] = (int)$_bi['product_id'];
                                                                        }
                                                                    }
                                                                    $_biMap = [];
                                                                    if (!empty($_biIds)) {
                                                                        $_biIdsStr = implode(',', $_biIds);
                                                                        $_db = \Config\Database::connect();
                                                                        $_biRows = $_db->query("
                                                                            SELECT p.id, pd.title, p.sku, cl.name AS category_name
                                                                            FROM products p
                                                                            LEFT JOIN product_details pd
                                                                                   ON pd.product_id = p.id AND pd.lang_id = 1
                                                                            LEFT JOIN categories cat ON cat.id = p.category_id
                                                                            LEFT JOIN category_lang cl
                                                                                   ON cl.category_id = cat.id AND cl.lang_id = 1
                                                                            WHERE p.id IN ({$_biIdsStr})
                                                                        ")->getResult();
                                                                        foreach ($_biRows as $_r) {
                                                                            $_biMap[(int)$_r->id] = $_r;
                                                                        }
                                                                    }
                                                                    $_biByCategory = [];
                                                                    foreach ($_biRaw as $_bi) {
                                                                        $_qty = (int)($_bi['qty'] ?? 0);
                                                                        if ($_qty <= 0) continue;
                                                                        $_pid  = (int)($_bi['product_id'] ?? 0);
                                                                        $_pRow = $_biMap[$_pid] ?? null;
                                                                        $_cat  = $_pRow ? (string)($_pRow->category_name ?? 'General') : 'General';
                                                                        $_biByCategory[$_cat][] = [
                                                                            'title'  => $_pRow ? (string)($_pRow->title ?? '—') : '—',
                                                                            'sku'    => $_pRow ? (string)($_pRow->sku   ?? '')  : '',
                                                                            'qty'    => $_qty,
                                                                            'unit'   => (float)($_bi['unit_price'] ?? 0),
                                                                            'cur'    => $orderProduct->product_currency ?? 'INR',
                                                                        ];
                                                                    }
                                                                    if (!empty($_biByCategory)): ?>
                                                                        <tr style="background-color: #f8fafc;">
                                                                            <td colspan="<?= $paymentSettings->vat_status ? 7 : 6; ?>" style="padding: 14px 20px 18px 20px; border-top: none; border-bottom: 2px solid #e2e8f0;">
                                                                                <div style="font-size: 13px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">
                                                                                    &#128230; Package Contents (Included Components)
                                                                                </div>
                                                                                <?php foreach ($_biByCategory as $_catName => $_catItems): ?>
                                                                                    <div style="margin-bottom: 12px;">
                                                                                        <div style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 4px;">
                                                                                            <?= esc($_catName); ?>
                                                                                        </div>
                                                                                        <table style="width: 100%; font-size: 13px; border-collapse: collapse; background: #ffffff; border: 1px solid #e2e8f0;">
                                                                                            <thead>
                                                                                                <tr style="background: #f1f5f9; color: #334155;">
                                                                                                    <th style="padding: 6px 12px; text-align: left; border: 1px solid #e2e8f0; font-weight: 600; width: 44%;">Item</th>
                                                                                                    <th style="padding: 6px 12px; text-align: left; border: 1px solid #e2e8f0; font-weight: 600; width: 22%;">SKU</th>
                                                                                                    <th style="padding: 6px 12px; text-align: center; border: 1px solid #e2e8f0; font-weight: 600; width: 10%;">Qty</th>
                                                                                                    <th style="padding: 6px 12px; text-align: right; border: 1px solid #e2e8f0; font-weight: 600; width: 12%;">Unit Price</th>
                                                                                                    <th style="padding: 6px 12px; text-align: right; border: 1px solid #e2e8f0; font-weight: 600; width: 12%;">Total</th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody>
                                                                                            <?php foreach ($_catItems as $_ci): ?>
                                                                                                <tr>
                                                                                                    <td style="padding: 6px 12px; border: 1px solid #e2e8f0; font-weight: 500; color: #0f172a;"><?= esc($_ci['title']); ?></td>
                                                                                                    <td style="padding: 6px 12px; border: 1px solid #e2e8f0; font-family: monospace; font-size: 12px; color: #64748b;"><?= esc($_ci['sku']); ?></td>
                                                                                                    <td style="padding: 6px 12px; border: 1px solid #e2e8f0; text-align: center; color: #334155;"><?= $_ci['qty']; ?></td>
                                                                                                    <td style="padding: 6px 12px; border: 1px solid #e2e8f0; text-align: right; white-space: nowrap; color: #475569;"><?= priceFormatted($_ci['unit'], $_ci['cur']); ?></td>
                                                                                                    <td style="padding: 6px 12px; border: 1px solid #e2e8f0; text-align: right; white-space: nowrap; font-weight: 600; color: #0f172a;"><?= priceFormatted($_ci['unit'] * $_ci['qty'], $_ci['cur']); ?></td>
                                                                                                </tr>
                                                                                            <?php endforeach; ?>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>
                                                                                <?php endforeach; ?>
                                                                            </td>
                                                                        </tr>
                                                                    <?php endif;
                                                                endif;
                                                            endif;
                                                        endif;
                                                    endif;
                                                endif;
                                            endforeach;
                                        endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="order-total float-right">
                                    <div class="row mb-2">
                                        <div class="col-7 col-left">
                                            <?= trans("subtotal"); ?>
                                        </div>
                                        <div class="col-5 col-right">
                                            <strong class="font-600"><?= priceFormatted($saleSubtotal, $order->price_currency); ?></strong>
                                        </div>
                                    </div>
                                    <?php if (!empty($affiliateDiscount)):
                                        $affiliateDiscount = numToDecimal($affiliateDiscount);
                                        $saleTotal = $saleTotal - $affiliateDiscount; ?>
                                        <div class="row">
                                            <div class="col-sm-6 col-xs-6 col-left">
                                                <?= trans("referral_discount"); ?>&nbsp;(<?= $affiliateDiscountRate; ?>%)
                                            </div>
                                            <div class="col-sm-6 col-xs-6 col-right">
                                                <strong>- <?= priceFormatted($affiliateDiscount, $order->price_currency); ?></strong>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($saleVat) && $saleVat > 0):
                                        $orderGst = calculateGstDetails(0, $saleVat, user()->id ?? null, $invoice->client_state ?? null);
                                        if ($orderGst['is_interstate']): ?>
                                            <div class="row mb-2">
                                                <div class="col-7 col-left">
                                                    <?= trans("igst"); ?>
                                                </div>
                                                <div class="col-5 col-right">
                                                    <strong class="font-600"><?= priceFormatted($orderGst['igst_amount'], $order->price_currency); ?></strong>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="row mb-2">
                                                <div class="col-7 col-left">
                                                    <?= trans("cgst"); ?>
                                                </div>
                                                <div class="col-5 col-right">
                                                    <strong class="font-600"><?= priceFormatted($orderGst['cgst_amount'], $order->price_currency); ?></strong>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-7 col-left">
                                                    <?= trans("sgst"); ?>
                                                </div>
                                                <div class="col-5 col-right">
                                                    <strong class="font-600"><?= priceFormatted($orderGst['sgst_amount'], $order->price_currency); ?></strong>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <div class="row mb-2">
                                            <div class="col-7 col-left">
                                                <?= trans("total_gst"); ?>
                                            </div>
                                            <div class="col-5 col-right">
                                                <strong class="font-600"><?= priceFormatted($saleVat, $order->price_currency); ?></strong>
                                            </div>
                                        </div>
                                    <?php endif;
                                    if ($shipping): ?>
                                        <div class="row mb-2">
                                            <div class="col-7 col-left">
                                                <?= trans("shipping"); ?>
                                            </div>
                                            <div class="col-5 col-right">
                                                <strong class="font-600"><?= priceFormatted($saleShipping, $order->price_currency); ?></strong>
                                            </div>
                                        </div>
                                    <?php endif;
                                    if ($order->coupon_discount > 0):
                                        if ($order->coupon_seller_id == user()->id) {
                                            $saleTotal = $saleTotal - $order->coupon_discount;
                                        }
                                        $showDiscount = false;
                                        if ($order->coupon_seller_id == user()->id) {
                                            $showDiscount = true;
                                        }
                                        if ($showDiscount):?>
                                            <div class="row mb-2">
                                                <div class="col-7 col-left">
                                                    <?= trans("discount"); ?>
                                                </div>
                                                <div class="col-5 col-right">
                                                    <strong class="font-600">-<?= priceFormatted($order->coupon_discount, $order->price_currency); ?></strong>
                                                </div>
                                            </div>
                                        <?php endif;
                                    endif; ?>
                                    <div class="row mb-2 pt-2" style="border-top: 2px solid #dee2e6; margin-top: 6px;">
                                        <div class="col-7 col-left">
                                            <strong style="font-size: 16px;"><?= trans("total"); ?></strong>
                                        </div>
                                        <div class="col-5 col-right">
                                            <?php $priceSecondCurrency = '';
                                            $transaction = getTransactionByOrderId($order->id);
                                            if (!empty($transaction) && $transaction->currency != $order->price_currency):
                                                $priceSecondCurrency = priceCurrencyFormat($transaction->payment_amount, $transaction->currency);
                                            endif; ?>
                                            <strong class="font-600" style="font-size: 16px;">
                                                <?php $saleTotal = $saleTotal + $saleShipping; ?>
                                                <?= priceFormatted($saleTotal, $order->price_currency);
                                                if (!empty($priceSecondCurrency)):?>
                                                    <br><span style="font-weight: 400;font-size: 13px;white-space: nowrap;">(<?= trans("paid"); ?>:&nbsp;<?= $priceSecondCurrency; ?>&nbsp;<?= $transaction->currency; ?>)</span>
                                                <?php endif; ?>
                                            </strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <style>
                        body {
                            font-size: 15px !important;
                        }

                        .logo img {
                            width: 160px;
                            height: auto;
                        }

                        .container-invoice {
                            max-width: 900px;
                            margin: 0 auto;
                        }

                        table {
                            border-bottom: 1px solid #dee2e6;
                        }

                        table th {
                            font-size: 14px;
                            white-space: nowrap;
                        }

                        .order-total {
                            width: 400px;
                            max-width: 100%;
                            float: right;
                            padding: 20px;
                        }

                        .order-total .col-left {
                            font-weight: 600;
                        }

                        .order-total .col-right {
                            text-align: right;
                        }

                        #btn_print {
                            min-width: 180px;
                        }

                        @media print {
                            .hidden-print {
                                display: none !important;
                            }
                        }
                    </style>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container" style="margin-bottom: 100px;">
    <div class="row">
        <div class="col-12 text-center mt-3">
            <button id="btn_print" class="btn btn-secondary btn-md hidden-print">
                <svg id="i-print" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="16" height="16" fill="none" stroke="currentcolor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" style="margin-top: -4px;">
                    <path d="M7 25 L2 25 2 9 30 9 30 25 25 25 M7 19 L7 30 25 30 25 19 Z M25 9 L25 2 7 2 7 9 M22 14 L25 14"/>
                </svg>
                &nbsp;&nbsp;<?= trans("print"); ?></button>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/js/jquery-3.5.1.min.js'); ?>"></script>
<script>
    $(document).on('click', '#btn_print', function () {
        window.print();
    });
</script>
</body>
</html>