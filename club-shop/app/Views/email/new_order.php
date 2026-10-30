<?= view('email/_header', ['title' => trans("email_text_thank_for_order")]); ?>
<?php $emailData = unserializeData($emailRow->email_data); ?>
    <table role="presentation" class="main">
        <?php if (!empty($emailData['orderId'])):
            $order = getOrder($emailData['orderId']);
            if (!empty($order)):
                $orderProducts = getOrderItems($order->id); ?>
                <tr>
                    <td class="wrapper">
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>
                                    <h1 style="text-decoration: none; font-size: 24px;line-height: 28px;font-weight: bold"><?= trans("email_text_thank_for_order"); ?></h1>
                                    <div class="mailcontent" style="line-height: 26px;font-size: 14px;">
                                        <p style='text-align: left;color: #555;'><?= trans("email_text_new_order"); ?></p><br>
                                        <h2 style="margin-bottom: 10px; font-size: 16px;font-weight: 600;"><?= trans("order_information"); ?></h2>
                                        <p style="color: #555;">
                                            <?= trans("order"); ?>:&nbsp;#<?= $order->order_number; ?><br>
                                            <?= trans("payment_status"); ?>:&nbsp;<?= trans($order->payment_status); ?><br>
                                            <?= trans("payment_method"); ?>:&nbsp;<?= getPaymentMethod($order->payment_method); ?>
                                            <br>
                                            <?= trans("date"); ?>:&nbsp;<?= formatDate($order->created_at); ?><br>
                                        </p>
                                    </div>
                                    <?php $shipping = unserializeData($order->shipping);
                                    if (!empty($shipping)):?>
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="margin-top: 30px;">
                                            <tr>
                                                <td>
                                                    <h3 style="margin-bottom: 10px; font-size: 16px;font-weight: 600;"><?= trans("shipping_address"); ?></h3>
                                                    <p style="color: #555; padding-right: 10px;">
                                                        <?= trans("first_name"); ?>:&nbsp;<?= !empty($shipping->sFirstName) ? esc($shipping->sFirstName) : ''; ?><br>
                                                        <?= trans("last_name"); ?>:&nbsp;<?= !empty($shipping->sLastName) ? esc($shipping->sLastName) : ''; ?><br>
                                                        <?= trans("email"); ?>:&nbsp;<?= !empty($shipping->sEmail) ? esc($shipping->sEmail) : ''; ?><br>
                                                        <?= trans("phone_number"); ?>:&nbsp;<?= !empty($shipping->sPhoneNumber) ? esc($shipping->sPhoneNumber) : ''; ?><br>
                                                        <?= trans("address"); ?>:&nbsp;<?= !empty($shipping->sAddress) ? esc($shipping->sAddress) : ''; ?><br>
                                                        <?= trans("country"); ?>:&nbsp;<?= !empty($shipping->sCountry) ? esc($shipping->sCountry) : ''; ?><br>
                                                        <?= trans("state"); ?>:&nbsp;<?= !empty($shipping->sState) ? esc($shipping->sState) : ''; ?><br>
                                                        <?= trans("city"); ?>:&nbsp;<?= !empty($shipping->sCity) ? esc($shipping->sCity) : ''; ?><br>
                                                        <?= trans("zip_code"); ?>:&nbsp;<?= !empty($shipping->sZipCode) ? esc($shipping->sZipCode) : ''; ?><br>
                                                    </p>
                                                </td>
                                                <td>
                                                    <h3 style="margin-bottom: 10px; font-size: 16px;font-weight: 600;"><?= trans("billing_address"); ?></h3>
                                                    <p style="color: #555; padding-right: 10px;">
                                                        <?= trans("first_name"); ?>:&nbsp;<?= !empty($shipping->bFirstName) ? esc($shipping->bFirstName) : ''; ?><br>
                                                        <?= trans("last_name"); ?>:&nbsp;<?= !empty($shipping->bLastName) ? esc($shipping->bLastName) : ''; ?><br>
                                                        <?= trans("email"); ?>:&nbsp;<?= !empty($shipping->bEmail) ? esc($shipping->bEmail) : ''; ?><br>
                                                        <?= trans("phone_number"); ?>:&nbsp;<?= !empty($shipping->bPhoneNumber) ? esc($shipping->bPhoneNumber) : ''; ?><br>
                                                        <?= trans("address"); ?>:&nbsp;<?= !empty($shipping->bAddress) ? esc($shipping->bAddress) : ''; ?><br>
                                                        <?= trans("country"); ?>:&nbsp;<?= !empty($shipping->bCountry) ? esc($shipping->bCountry) : ''; ?><br>
                                                        <?= trans("state"); ?>:&nbsp;<?= !empty($shipping->bState) ? esc($shipping->bState) : ''; ?><br>
                                                        <?= trans("city"); ?>:&nbsp;<?= !empty($shipping->bCity) ? esc($shipping->bCity) : ''; ?><br>
                                                        <?= trans("zip_code"); ?>:&nbsp;<?= !empty($shipping->bZipCode) ? esc($shipping->bZipCode) : ''; ?><br>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    <?php endif; ?>
                                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%; border-collapse: collapse; margin-top: 25px;" class="table-products">
                                        <thead>
                                            <tr>
                                                <th style="padding: 10px 8px; border-bottom: 2px solid #ddd; text-align: left; font-size: 13px; font-weight: 600; width: 42%;"><?= trans("product"); ?></th>
                                                <th style="padding: 10px 8px; border-bottom: 2px solid #ddd; text-align: right; font-size: 13px; font-weight: 600; width: 15%; white-space: nowrap;"><?= trans("unit_price"); ?></th>
                                                <th style="padding: 10px 8px; border-bottom: 2px solid #ddd; text-align: center; font-size: 13px; font-weight: 600; width: 10%; white-space: nowrap;"><?= trans("quantity"); ?></th>
                                                <th style="padding: 10px 8px; border-bottom: 2px solid #ddd; text-align: right; font-size: 13px; font-weight: 600; width: 18%; white-space: nowrap;"><?= trans("vat"); ?></th>
                                                <th style="padding: 10px 8px; border-bottom: 2px solid #ddd; text-align: right; font-size: 13px; font-weight: 600; width: 15%; white-space: nowrap;"><?= trans("total"); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (!empty($orderProducts)):
                                            foreach ($orderProducts as $item): ?>
                                                <tr>
                                                    <td style="width: 42%; padding: 12px 8px; border-bottom: 1px solid #ddd; vertical-align: top; text-align: left;">
                                                        <span style="font-weight: 600; color: #333;"><?= esc($item->product_title); ?></span>
                                                    </td>
                                                    <td style="width: 15%; padding: 12px 8px; border-bottom: 1px solid #ddd; vertical-align: top; text-align: right; white-space: nowrap;"><?= priceFormatted($item->product_unit_price, $item->product_currency); ?></td>
                                                    <td style="width: 10%; padding: 12px 8px; border-bottom: 1px solid #ddd; vertical-align: top; text-align: center; white-space: nowrap;"><?= $item->product_quantity; ?></td>
                                                    <?php if (!empty($order->price_vat)): ?>
                                                        <td style="width: 18%; padding: 12px 8px; border-bottom: 1px solid #ddd; vertical-align: top; text-align: right; white-space: nowrap;">
                                                            <?php if (!empty($item->product_vat) && $item->product_vat > 0):
                                                                $delivState = !empty($shipping->sState) ? $shipping->sState : (!empty($shipping->bState) ? $shipping->bState : null);
                                                                $itemGst = calculateGstDetails($item->product_vat_rate, $item->product_vat, $item->seller_id, $delivState);
                                                                if ($itemGst['is_interstate']): ?>
                                                                    <?= priceFormatted($itemGst['igst_amount'], $item->product_currency); ?><br><small style="color: #888;">(<?= $itemGst['igst_rate']; ?>% <?= trans('igst'); ?>)</small>
                                                                <?php else: ?>
                                                                    <?= priceFormatted($itemGst['cgst_amount'], $item->product_currency); ?>&nbsp;<small style="color: #888;">(<?= $itemGst['cgst_rate']; ?>% <?= trans('cgst'); ?>)</small><br>
                                                                    <?= priceFormatted($itemGst['sgst_amount'], $item->product_currency); ?>&nbsp;<small style="color: #888;">(<?= $itemGst['sgst_rate']; ?>% <?= trans('sgst'); ?>)</small>
                                                                <?php endif;
                                                            else: ?>
                                                                -
                                                            <?php endif; ?>
                                                        </td>
                                                    <?php else: ?>
                                                        <td style="width: 18%; padding: 12px 8px; border-bottom: 1px solid #ddd; vertical-align: top; text-align: right;">-</td>
                                                    <?php endif; ?>
                                                    <td style="width: 15%; padding: 12px 8px; border-bottom: 1px solid #ddd; vertical-align: top; text-align: right; white-space: nowrap; font-weight: 600;"><?= priceFormatted($item->product_total_price, $item->product_currency); ?></td>
                                                </tr>
                                                <?php
                                                /* ── Bundle / Package Contents Full-Width Row ────────── */
                                                if (!empty($item->is_bundle) && !empty($item->bundle_items)):
                                                    $_biRaw = is_string($item->bundle_items)
                                                        ? json_decode($item->bundle_items, true)
                                                        : $item->bundle_items;
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
                                                            foreach ($_biRows as $_r) { $_biMap[(int)$_r->id] = $_r; }
                                                        }
                                                        $_biByCategory = [];
                                                        foreach ($_biRaw as $_bi) {
                                                            $_qty = (int)($_bi['qty'] ?? 0);
                                                            if ($_qty <= 0) continue;
                                                            $_pid  = (int)($_bi['product_id'] ?? 0);
                                                            $_pRow = $_biMap[$_pid] ?? null;
                                                            $_cat  = $_pRow ? (string)($_pRow->category_name ?? 'General') : 'General';
                                                            $_biByCategory[$_cat][] = [
                                                                'title' => $_pRow ? (string)($_pRow->title ?? '—') : '—',
                                                                'sku'   => $_pRow ? (string)($_pRow->sku   ?? '')  : '',
                                                                'qty'   => $_qty,
                                                                'unit'  => (float)($_bi['unit_price'] ?? 0),
                                                                'cur'   => $item->product_currency ?? 'INR',
                                                            ];
                                                        }
                                                        if (!empty($_biByCategory)): ?>
                                                            <tr style="background-color: #f9fafb;">
                                                                <td colspan="<?= !empty($order->price_vat) ? 5 : 4; ?>" style="padding: 12px 16px 16px 16px; border-top: none; border-bottom: 2px solid #e5e7eb;">
                                                                    <div style="font-size: 12px; font-weight: 700; color: #374151; margin-bottom: 8px;">
                                                                        &#128230; Package Contents (Included Components)
                                                                    </div>
                                                                    <?php foreach ($_biByCategory as $_catName => $_catItems): ?>
                                                                        <div style="margin-bottom: 10px;">
                                                                            <div style="font-size: 10px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 4px;">
                                                                                <?= esc($_catName); ?>
                                                                            </div>
                                                                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%; font-size: 12px; border-collapse: collapse; background: #ffffff; border: 1px solid #e5e7eb;">
                                                                                <thead>
                                                                                    <tr style="background: #f3f4f6; color: #374151;">
                                                                                        <th style="padding: 5px 8px; text-align: left; border: 1px solid #e5e7eb; font-weight: 600; width: 44%;">Item</th>
                                                                                        <th style="padding: 5px 8px; text-align: left; border: 1px solid #e5e7eb; font-weight: 600; width: 22%;">SKU</th>
                                                                                        <th style="padding: 5px 8px; text-align: center; border: 1px solid #e5e7eb; font-weight: 600; width: 10%;">Qty</th>
                                                                                        <th style="padding: 5px 8px; text-align: right; border: 1px solid #e5e7eb; font-weight: 600; width: 12%;">Unit Price</th>
                                                                                        <th style="padding: 5px 8px; text-align: right; border: 1px solid #e5e7eb; font-weight: 600; width: 12%;">Total</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                <?php foreach ($_catItems as $_ci): ?>
                                                                                    <tr>
                                                                                        <td style="padding: 5px 8px; border: 1px solid #e5e7eb; font-weight: 500; color: #111827;"><?= esc($_ci['title']); ?></td>
                                                                                        <td style="padding: 5px 8px; border: 1px solid #e5e7eb; font-family: monospace; font-size: 11px; color: #6b7280;"><?= esc($_ci['sku']); ?></td>
                                                                                        <td style="padding: 5px 8px; border: 1px solid #e5e7eb; text-align: center; color: #374151;"><?= $_ci['qty']; ?></td>
                                                                                        <td style="padding: 5px 8px; border: 1px solid #e5e7eb; text-align: right; white-space: nowrap; color: #4b5563;"><?= priceFormatted($_ci['unit'], $_ci['cur']); ?></td>
                                                                                        <td style="padding: 5px 8px; border: 1px solid #e5e7eb; text-align: right; white-space: nowrap; font-weight: 600; color: #111827;"><?= priceFormatted($_ci['unit'] * $_ci['qty'], $_ci['cur']); ?></td>
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
                                                endif; ?>
                                            <?php endforeach;
                                        endif; ?>
                                        </tbody>
                                    </table>
                                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" align="right" style="width: 100%; max-width: 360px; margin-left: auto; margin-right: 0; margin-top: 30px; border-collapse: collapse;">
                                        <tr>
                                            <td style="width: 60%; padding: 6px 10px; text-align: right; color: #555;"><?= trans("subtotal"); ?></td>
                                            <td style="width: 40%; padding: 6px 0; text-align: right; font-weight: 600; white-space: nowrap;"><?= priceFormatted($order->price_subtotal, $order->price_currency); ?></td>
                                        </tr>
                                        <?php $affiliate = unserializeData($order->affiliate_data);
                                        if (!empty($affiliate) && !empty($affiliate['discount'])): ?>
                                            <tr>
                                                <td style="width: 60%; padding: 6px 10px; text-align: right; color: #555;"><?= trans("referral_discount"); ?>&nbsp;(<?= $affiliate['discountRate']; ?>%)</td>
                                                <td style="width: 40%; padding: 6px 0; text-align: right; font-weight: 600; white-space: nowrap;">-<?= priceFormatted($affiliate['discount'], $order->price_currency); ?></td>
                                            </tr>
                                        <?php endif;
                                        if (!empty($order->price_vat) && $order->price_vat > 0):
                                            $delivState = !empty($shipping->sState) ? $shipping->sState : (!empty($shipping->bState) ? $shipping->bState : null);
                                            $firstSellerId = !empty($orderProducts[0]->seller_id) ? $orderProducts[0]->seller_id : null;
                                            $orderGst = calculateGstDetails(0, $order->price_vat, $firstSellerId, $delivState);
                                            if ($orderGst['is_interstate']): ?>
                                                <tr>
                                                    <td style="width: 60%; padding: 6px 10px; text-align: right; color: #555;"><?= trans("igst"); ?></td>
                                                    <td style="width: 40%; padding: 6px 0; text-align: right; font-weight: 600; white-space: nowrap;"><?= priceFormatted($orderGst['igst_amount'], $order->price_currency); ?></td>
                                                </tr>
                                            <?php else: ?>
                                                <tr>
                                                    <td style="width: 60%; padding: 6px 10px; text-align: right; color: #555;"><?= trans("cgst"); ?></td>
                                                    <td style="width: 40%; padding: 6px 0; text-align: right; font-weight: 600; white-space: nowrap;"><?= priceFormatted($orderGst['cgst_amount'], $order->price_currency); ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 60%; padding: 6px 10px; text-align: right; color: #555;"><?= trans("sgst"); ?></td>
                                                    <td style="width: 40%; padding: 6px 0; text-align: right; font-weight: 600; white-space: nowrap;"><?= priceFormatted($orderGst['sgst_amount'], $order->price_currency); ?></td>
                                                </tr>
                                            <?php endif; ?>
                                            <tr>
                                                <td style="width: 60%; padding: 6px 10px; text-align: right; color: #555;"><?= trans("total_gst"); ?></td>
                                                <td style="width: 40%; padding: 6px 0; text-align: right; font-weight: 600; white-space: nowrap;"><?= priceFormatted($order->price_vat, $order->price_currency); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <tr>
                                            <td style="width: 60%; padding: 6px 10px; text-align: right; color: #555;"><?= trans("shipping"); ?></td>
                                            <td style="width: 40%; padding: 6px 0; text-align: right; font-weight: 600; white-space: nowrap;"><?= priceFormatted($order->price_shipping, $order->price_currency); ?></td>
                                        </tr>
                                        <?php if ($order->coupon_discount > 0): ?>
                                            <tr>
                                                <td style="width: 60%; padding: 6px 10px; text-align: right; color: #555;"><?= trans("coupon"); ?>&nbsp;[<?= esc($order->coupon_code); ?>]</td>
                                                <td style="width: 40%; padding: 6px 0; text-align: right; font-weight: 600; white-space: nowrap;">-<?= priceFormatted($order->coupon_discount, $order->price_currency); ?></td>
                                            </tr>
                                        <?php endif;
                                        if (!empty($order->global_taxes_data)):
                                            $globalTaxesArray = unserializeData($order->global_taxes_data);
                                            if (!empty($globalTaxesArray)):
                                                foreach ($globalTaxesArray as $taxItem):
                                                    if (!empty($taxItem['taxNameArray']) && !empty($taxItem['taxTotal']) && !empty($taxItem['taxRate'])):?>
                                                        <tr>
                                                            <td style="width: 60%; padding: 6px 10px; text-align: right; color: #555;"><?= esc(getTaxName($taxItem['taxNameArray'], selectedLangId())); ?>&nbsp;(<?= $taxItem['taxRate']; ?>%)</td>
                                                            <td style="width: 40%; padding: 6px 0; text-align: right; font-weight: 600; white-space: nowrap;"><?= priceDecimal($taxItem['taxTotal'], $order->price_currency); ?></td>
                                                        </tr>
                                                    <?php endif;
                                                endforeach;
                                            endif;
                                        endif;
                                        if (!empty($order->transaction_fee)): ?>
                                            <tr>
                                                <td style="width: 60%; padding: 6px 10px; text-align: right; color: #555;"><?= trans("transaction_fee"); ?><?= $order->transaction_fee_rate ? ' (' . $order->transaction_fee_rate . '%)' : ''; ?></td>
                                                <td style="width: 40%; padding: 6px 0; text-align: right; font-weight: 600; white-space: nowrap;"><?= priceFormatted($order->transaction_fee, $order->price_currency); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <tr>
                                            <?php $priceSecondCurrency = '';
                                            $transaction = getTransactionByOrderId($order->id);
                                            if (!empty($transaction) && $transaction->currency != $order->price_currency):
                                                $priceSecondCurrency = priceCurrencyFormat($transaction->payment_amount, $transaction->currency);
                                            endif; ?>
                                            <td style="width: 60%; padding: 10px 10px 6px 10px; border-top: 2px solid #ddd; text-align: right; font-size: 15px; font-weight: bold; color: #333;"><?= trans("total"); ?></td>
                                            <td style="width: 40%; padding: 10px 0 6px 0; border-top: 2px solid #ddd; text-align: right; font-size: 15px; font-weight: bold; white-space: nowrap; color: #333;">
                                                <?= priceFormatted($order->price_total, $order->price_currency);
                                                if (!empty($priceSecondCurrency)):?>
                                                    <br><span style="font-weight: 400; font-size: 12px; white-space: nowrap; color: #777;">(<?= trans("paid"); ?>:&nbsp;<?= $priceSecondCurrency; ?>&nbsp;<?= $transaction->currency; ?>)</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </table>
                                    <?php if ($order->buyer_type != 'guest'): ?>
                                        <p style='text-align: center;margin-top: 40px;'>
                                            <a href="<?= generateUrl('order_details') . '/' . $order->order_number; ?>" style='font-size: 14px;text-decoration: none;padding: 14px 40px;background-color: <?= $generalSettings->site_color; ?>;color: #ffffff !important; border-radius: 3px;'>
                                                <?= trans("see_order_details"); ?>
                                            </a>
                                        </p>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            <?php endif;
        endif; ?>
    </table>
<?= view('email/_footer'); ?>