<?= view('email/_header', ['title' => trans("your_order_shipped")]); ?>
<?php $emailData = unserializeData($emailRow->email_data); ?>
<table role="presentation" class="main">
    <?php if (!empty($emailData['orderProductId'])):
        $orderProduct = getOrderProduct($emailData['orderProductId']);
        if (!empty($orderProduct)):
            $order = getOrder($orderProduct->order_id);
            if (!empty($order)): ?>
                <tr>
                    <td class="wrapper">
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>
                                    <h1 style="text-decoration: none; font-size: 24px;line-height: 28px;font-weight: bold"><?= trans("your_order_shipped"); ?></h1>
                                    <div class="mailcontent" style="line-height: 26px;font-size: 14px;">
                                        <h2 style="margin-bottom: 10px; font-size: 16px;font-weight: 600;"><?= trans("order_information"); ?></h2>
                                        <p style="color: #555;">
                                            <?= trans("order"); ?>:&nbsp;#<?= esc($order->order_number); ?><br>
                                            <?= trans("payment_status"); ?>:&nbsp;<?= trans($order->payment_status); ?><br>
                                            <?= trans("payment_method"); ?>:&nbsp;<?= getPaymentMethod($order->payment_method); ?><br>
                                            <?= trans("date"); ?>:&nbsp;<?= formatDate($order->created_at); ?><br>
                                        </p>
                                    </div>
                                    <?php if (!empty($orderProduct)): ?>
                                        <br>
                                        <p>
                                        <h2 style="margin-bottom: 10px; font-size: 16px;font-weight: 600;"><?= trans("shipping"); ?></h2>
                                        <?= trans("tracking_code"); ?>:&nbsp;<?= $orderProduct->shipping_tracking_number; ?><br>
                                        <?= trans("tracking_url"); ?>:&nbsp;<?= $orderProduct->shipping_tracking_url; ?><br>
                                        </p>
                                    <?php endif; ?>
                                    <h3 style="margin-bottom: 10px;font-size: 16px;font-weight: 600;border-bottom: 1px solid #d1d1d1;padding-bottom: 5px; margin-top: 45px;"><?= trans("shipped_product"); ?></h3>
                                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%; border-collapse: collapse; margin-top: 15px;" class="table-products">
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
                                        <?php if (!empty($orderProduct)): ?>
                                            <tr>
                                                <td style="width: 42%; padding: 12px 8px; border-bottom: 1px solid #ddd; vertical-align: top; text-align: left; font-weight: 600; color: #333;"><?= esc($orderProduct->product_title); ?></td>
                                                <td style="width: 15%; padding: 12px 8px; border-bottom: 1px solid #ddd; vertical-align: top; text-align: right; white-space: nowrap;"><?= priceFormatted($orderProduct->product_unit_price, $orderProduct->product_currency); ?></td>
                                                <td style="width: 10%; padding: 12px 8px; border-bottom: 1px solid #ddd; vertical-align: top; text-align: center; white-space: nowrap;"><?= $orderProduct->product_quantity; ?></td>
                                                <?php if (!empty($order->price_vat)): ?>
                                                    <td style="width: 18%; padding: 12px 8px; border-bottom: 1px solid #ddd; vertical-align: top; text-align: right; white-space: nowrap;">
                                                        <?php if (!empty($orderProduct->product_vat)): 
                                                            $shipping = !empty($order->shipping) ? unserializeData($order->shipping) : null;
                                                            $delivState = $shipping->sStateId ?? $shipping->sState ?? $shipping->bState ?? null;
                                                            $itemGst = calculateGstDetails($orderProduct->product_vat_rate, $orderProduct->product_vat, $orderProduct->seller_id, $delivState);
                                                            if ($itemGst['is_intra']): ?>
                                                                <small><?= trans("cgst"); ?> (<?= $itemGst['cgst_rate']; ?>%): <?= priceFormatted($itemGst['cgst_amount'], $orderProduct->product_currency); ?></small><br>
                                                                <small><?= trans("sgst"); ?> (<?= $itemGst['sgst_rate']; ?>%): <?= priceFormatted($itemGst['sgst_amount'], $orderProduct->product_currency); ?></small>
                                                            <?php else: ?>
                                                                <small><?= trans("igst"); ?> (<?= $itemGst['igst_rate']; ?>%): <?= priceFormatted($itemGst['igst_amount'], $orderProduct->product_currency); ?></small>
                                                            <?php endif;
                                                        endif; ?>
                                                    </td>
                                                <?php else: ?>
                                                    <td style="width: 18%; padding: 12px 8px; border-bottom: 1px solid #ddd; vertical-align: top; text-align: right;">-</td>
                                                <?php endif; ?>
                                                <td style="width: 15%; padding: 12px 8px; border-bottom: 1px solid #ddd; vertical-align: top; text-align: right; white-space: nowrap; font-weight: 600;"><?= priceFormatted($orderProduct->product_total_price, $orderProduct->product_currency); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                    <br>
                                    <br>
                                    <p style="color: #555;"></p>
                                    <p style='text-align: center;margin-top: 40px;'>
                                        <a href="<?= generateUrl('order_details') . '/' . $order->order_number; ?>" style='font-size: 14px;text-decoration: none;padding: 14px 40px;background-color: <?= $generalSettings->site_color; ?>;color: #ffffff !important; border-radius: 3px;'>
                                            <?= trans("see_order_details"); ?>
                                        </a>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            <?php endif;
        endif;
    endif; ?>
</table>
<?= view('email/_footer'); ?>
