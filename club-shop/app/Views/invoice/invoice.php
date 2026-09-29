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
                                    <p class="font-weight-bold mb-1"><span style="display: inline-block;width: 100px;"><?= trans("invoice"); ?>:</span>#<?= esc($order->order_number); ?></p>
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
                                    <p class="mb-1"><?= !empty($invoice->client_city) ? $invoice->client_city . ", " : '' ?><?= esc($invoice->client_state); ?></p>
                                <?php endif;
                                if (!empty($invoice->client_country)): ?>
                                    <p class="mb-1"><?= esc($invoice->client_country); ?></p>
                                <?php endif;
                                if (!empty($invoice->client_phone_number)): ?>
                                    <p class="mb-1"><?= esc($invoice->client_phone_number); ?></p>
                                <?php endif;
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
                                            <th class="border-0 font-weight-bold"><?= trans("seller"); ?></th>
                                            <th class="border-0 font-weight-bold"><?= trans("product_id"); ?></th>
                                            <th class="border-0 font-weight-bold"><?= trans("description"); ?></th>
                                            <th class="border-0 font-weight-bold"><?= trans("quantity"); ?></th>
                                            <th class="border-0 font-weight-bold"><?= trans("unit_price"); ?></th>
                                            <?php if ($paymentSettings->vat_status): ?>
                                                <th class="border-0 font-weight-bold"><?= trans("vat"); ?></th>
                                            <?php endif; ?>
                                            <th class="border-0 font-weight-bold"><?= trans("total"); ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $saleSubtotal = $order->price_subtotal;
                                        $saleVat = $order->price_vat;
                                        $saleShipping = $order->price_shipping;
                                        $saleTotal = $order->price_total;
                                        $shipping = false;
                                        if (!empty($invoiceItems) && is_array($invoiceItems)):
                                            foreach ($invoiceItems as $item):
                                                if (!empty($item['id'])):
                                                    // echo '<pre>';
                                                    // print_r(getOrderProduct($item['id']));
                                                    // exit();
                                                    $orderProduct = getOrderProduct($item['id']);
                                                    if (!empty($orderProduct)):
                                                        $product = getProduct($orderProduct->product_id);
                                                        $itemSku = !empty($item['sku']) ? $item['sku'] : '';
                                                        if (empty($itemSku) && !empty($product)) {
                                                            $itemSku = $product->sku;
                                                        }
                                                        if ($orderProduct->product_type == 'physical') {
                                                            $shipping = true;
                                                        } ?>
                                                        <tr style="font-size: 15px;">
                                                            <td><?= !empty($item['seller']) ? esc($item['seller']) : ''; ?></td>
                                                            <td><?= $orderProduct->product_id; ?></td>
                                                            <td>
                                                                <?= $orderProduct->product_title; ?>
                                                                <?php if (!empty($itemSku)): ?>
                                                                    <div><?= trans("sku"); ?>:&nbsp;<?= esc($itemSku); ?></div>
                                                                <?php endif; ?>


                                                                <?php
                                                                /* ── Bundle / Package Contents ─────────────────────────────
                                                                   bundle_items JSON has: product_id, qty, unit_price
                                                                   We bulk-fetch title/sku/category from DB.
                                                                ─────────────────────────────────────────────────────────── */
                                                                if (!empty($orderProduct->is_bundle) && !empty($orderProduct->bundle_items)):
                                                                    $_biRaw = is_string($orderProduct->bundle_items)
                                                                        ? json_decode($orderProduct->bundle_items, true)
                                                                        : $orderProduct->bundle_items;
                                                                    if (!empty($_biRaw) && is_array($_biRaw)):
                                                                        /* 1. collect product IDs (qty > 0 only) */
                                                                        $_biIds = [];
                                                                        foreach ($_biRaw as $_bi) {
                                                                            if ((int)($_bi['qty'] ?? 0) > 0 && !empty($_bi['product_id'])) {
                                                                                $_biIds[] = (int)$_bi['product_id'];
                                                                            }
                                                                        }
                                                                        /* 2. bulk-fetch title / sku / category in one query */
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
                                                                        /* 3. group by category */
                                                                        $_biByCategory = [];
                                                                        foreach ($_biRaw as $_bi) {
                                                                            $_qty = (int)($_bi['qty'] ?? 0);
                                                                            if ($_qty <= 0) continue;
                                                                            $_pid  = (int)($_bi['product_id'] ?? 0);
                                                                            $_pRow = $_biMap[$_pid] ?? null;
                                                                            $_cat  = $_pRow ? (string)($_pRow->category_name ?? 'Items') : 'Items';
                                                                            $_biByCategory[$_cat][] = [
                                                                                'title'  => $_pRow ? (string)($_pRow->title ?? '—') : '—',
                                                                                'sku'    => $_pRow ? (string)($_pRow->sku   ?? '')  : '',
                                                                                'qty'    => $_qty,
                                                                                'unit'   => (float)($_bi['unit_price'] ?? 0),
                                                                                'cur'    => $orderProduct->product_currency ?? 'INR',
                                                                            ];
                                                                        }
                                                                ?>
                                                                <div style="margin-top:10px;border-top:1px dashed #dee2e6;padding-top:8px;">
                                                                    <div style="font-size:12px;font-weight:700;color:#495057;margin-bottom:6px;">
                                                                        &#128230; Package Contents
                                                                    </div>
                                                                    <?php foreach ($_biByCategory as $_catName => $_catItems): ?>
                                                                        <div style="margin-bottom:8px;">
                                                                            <div style="font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:.04em;margin-bottom:3px;">
                                                                                <?= esc($_catName); ?>
                                                                            </div>
                                                                            <table style="width:100%;font-size:12px;border-collapse:collapse;">
                                                                                <thead>
                                                                                    <tr style="background:#f8f9fa;">
                                                                                        <th style="padding:4px 8px;text-align:left;border:1px solid #dee2e6;font-weight:600;">Item</th>
                                                                                        <th style="padding:4px 8px;text-align:left;border:1px solid #dee2e6;font-weight:600;">SKU</th>
                                                                                        <th style="padding:4px 8px;text-align:center;border:1px solid #dee2e6;font-weight:600;">Qty</th>
                                                                                        <th style="padding:4px 8px;text-align:right;border:1px solid #dee2e6;font-weight:600;">Unit</th>
                                                                                        <th style="padding:4px 8px;text-align:right;border:1px solid #dee2e6;font-weight:600;">Total</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                <?php foreach ($_catItems as $_ci): ?>
                                                                                    <tr>
                                                                                        <td style="padding:4px 8px;border:1px solid #dee2e6;"><?= esc($_ci['title']); ?></td>
                                                                                        <td style="padding:4px 8px;border:1px solid #dee2e6;font-family:monospace;font-size:11px;color:#6c757d;"><?= esc($_ci['sku']); ?></td>
                                                                                        <td style="padding:4px 8px;border:1px solid #dee2e6;text-align:center;"><?= $_ci['qty']; ?></td>
                                                                                        <td style="padding:4px 8px;border:1px solid #dee2e6;text-align:right;white-space:nowrap;"><?= priceFormatted($_ci['unit'], $_ci['cur']); ?></td>
                                                                                        <td style="padding:4px 8px;border:1px solid #dee2e6;text-align:right;white-space:nowrap;font-weight:600;"><?= priceFormatted($_ci['unit'] * $_ci['qty'], $_ci['cur']); ?></td>
                                                                                    </tr>
                                                                                <?php endforeach; ?>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                                <?php endif; endif; ?>

                                                            </td>
                                                            <td><?= $orderProduct->product_quantity; ?></td>
                                                            <td style="white-space: nowrap"><?= priceFormatted($orderProduct->product_unit_price, $orderProduct->product_currency); ?></td>
                                                            <?php if ($paymentSettings->vat_status): ?>
                                                                <td style="white-space: nowrap">
                                                                    <?php if (!empty($orderProduct->product_vat) && $orderProduct->product_vat > 0):
                                                                        $itemGst = calculateGstDetails($orderProduct->product_vat_rate, $orderProduct->product_vat, $orderProduct->seller_id, $invoice->client_state ?? null);
                                                                        if ($itemGst['is_interstate']): ?>
                                                                            <?= priceFormatted($itemGst['igst_amount'], $orderProduct->product_currency); ?>&nbsp;(<?= $itemGst['igst_rate']; ?>% <?= trans('igst'); ?>)
                                                                        <?php else: ?>
                                                                            <?= priceFormatted($itemGst['cgst_amount'], $orderProduct->product_currency); ?>&nbsp;(<?= $itemGst['cgst_rate']; ?>% <?= trans('cgst'); ?>)<br>
                                                                            <?= priceFormatted($itemGst['sgst_amount'], $orderProduct->product_currency); ?>&nbsp;(<?= $itemGst['sgst_rate']; ?>% <?= trans('sgst'); ?>)
                                                                        <?php endif;
                                                                    endif; ?>
                                                                </td>
                                                            <?php endif; ?>
                                                            <td style="white-space: nowrap"><?= priceFormatted($orderProduct->product_total_price, $orderProduct->product_currency); ?></td>
                                                        </tr>
                                                    <?php endif;
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
                                    <?php $affiliate = unserializeData($order->affiliate_data);
                                    if (!empty($affiliate) && !empty($affiliate['discount'])): ?>
                                        <div class="row">
                                            <div class="col-6 col-left">
                                                <?= trans("referral_discount"); ?>&nbsp;(<?= $affiliate['discountRate']; ?>%)
                                            </div>
                                            <div class="col-6 col-right">
                                                <strong>-&nbsp;<?= priceCurrencyFormat($affiliate['discount'], $order->price_currency); ?></strong>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($saleVat) && $saleVat > 0):
                                        $firstSellerId = !empty($orderProducts[0]->seller_id) ? $orderProducts[0]->seller_id : null;
                                        $orderGst = calculateGstDetails(0, $saleVat, $firstSellerId, $invoice->client_state ?? null);
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
                                    if ($order->coupon_discount > 0): ?>
                                        <div class="row mb-2">
                                            <div class="col-7 col-left">
                                                <?= trans("discount"); ?>
                                            </div>
                                            <div class="col-5 col-right">
                                                <strong class="font-600">-<?= priceFormatted($order->coupon_discount, $order->price_currency); ?></strong>
                                            </div>
                                        </div>
                                    <?php endif;
                                    if (!empty($order->global_taxes_data)):
                                        $globalTaxesArray = unserializeData($order->global_taxes_data);
                                        if (!empty($globalTaxesArray)):
                                            foreach ($globalTaxesArray as $taxItem):?>
                                                <div class="row mb-2">
                                                    <div class="col-7 col-left">
                                                        <?= esc(getTaxName($taxItem['taxNameArray'], selectedLangId())); ?>&nbsp;(<?= $taxItem['taxRate']; ?>%)
                                                    </div>
                                                    <div class="col-5 col-right">
                                                        <strong class="font-600"><?= priceDecimal($taxItem['taxTotal'], $order->price_currency); ?></strong>
                                                    </div>
                                                </div>
                                            <?php endforeach;
                                        endif;
                                    endif;
                                    if (!empty($order->transaction_fee) && $order->transaction_fee > 0): ?>
                                        <div class="row mb-2">
                                            <div class="col-7 col-left">
                                                <?= trans("transaction_fee"); ?><?= $order->transaction_fee_rate ? ' (' . $order->transaction_fee_rate . '%)' : ''; ?>
                                            </div>
                                            <div class="col-5 col-right">
                                                <strong class="font-600"><?= priceFormatted($order->transaction_fee, $order->price_currency); ?></strong>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <div class="row mb-2">
                                        <div class="col-7 col-left">
                                            <?= trans("total"); ?>
                                        </div>
                                        <div class="col-5 col-right">
                                            <?php $priceSecondCurrency = '';
                                            $transaction = getTransactionByOrderId($order->id);
                                            if (!empty($transaction) && $transaction->currency != $order->price_currency):
                                                $priceSecondCurrency = priceCurrencyFormat($transaction->payment_amount, $transaction->currency);
                                            endif; ?>
                                            <strong class="font-600">
                                                <?= priceFormatted($saleTotal, $order->price_currency);
                                                if (!empty($priceSecondCurrency)):?>
                                                    <br><span style="font-weight: 400;white-space: nowrap;">(<?= trans("paid"); ?>:&nbsp;<?= $priceSecondCurrency; ?>&nbsp;<?= $transaction->currency; ?>)</span>
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