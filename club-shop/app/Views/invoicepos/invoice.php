<?php
use App\Models\ProductModel;
$productModel = model(ProductModel::class);
function show($v){ return (!empty($v)) ? esc($v) : '--'; }

$ship = $order->shipping ?? null;
$first = $orderProducts[0] ?? null;
$currency = $order->price_currency ?? 'INR';
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>GST Invoice</title>

<style>
body{
    width:220px;
    margin:0;
    padding:8px;
    font-family:monospace;
    font-size:11px;
}
.center{text-align:center}
.right{text-align:right}
.bold{font-weight:bold}
hr{border:none;border-top:1px dashed #000;margin:4px 0}
table{width:100%;border-collapse:collapse}
td{padding:1px 0;vertical-align:top}
.total{font-size:13px;font-weight:bold}
.small{font-size:10px}
</style>
</head>

<body onload="window.print()">

<div class="center bold">
    <?= show($invoiceItems[0]['seller'] ?? null); ?>
</div>
<div class="center">
    <?= show($baseSettings->contact_address ?? null); ?><br>
    GSTIN: <?= show($baseSettings->gst_number ?? null); ?>
</div>

<hr>

<?= trans("invoice"); ?> No: <?= show($order->order_number ?? null); ?><br>
<?= trans("date"); ?>: <?= show(formatDate($order->created_at ?? null)); ?><br>
POS: <?= show($invoice->client_state ?? null); ?>

<hr>

<b><?= trans("customer"); ?></b><br>
<?= show(($invoice->client_first_name ?? '').' '.($invoice->client_last_name ?? '')); ?><br>
<?= show($invoice->client_city ?? null); ?>, <?= show($invoice->client_state ?? null); ?>

<hr>

<table>
<?php foreach($orderProducts as $p): ?>

<tr>
    <td>
        <?= show($p->product_title); ?><br>
        <span class="small">
            <?= trans("sku"); ?>: <?= show($p->product_sku); ?> <br>
            <?= trans("qty"); ?>: <?= show($p->product_quantity); ?>
        </span>
    </td>
    <td class="right">
        <?= show(priceFormatted($p->product_unit_price, $currency)); ?>
    </td>
</tr>

<?php if($p->is_bundle && !empty($p->bundle_items)): ?>
<?php
$bundleItems = json_decode($p->bundle_items, true);
if(is_array($bundleItems)):
    foreach($bundleItems as $row):
        $product = $productModel->getActiveProduct($row['product_id']);
?>
<tr>
    <td style="padding-left:10px;">
        └ <?= show($product->title ?? 'Bundle Item'); ?><br>
        <span class="small"><?= trans("sku"); ?>: <?= show($product->sku); ?></span><br>
        <span class="small"><?= trans("qty"); ?>: <?= show($row['qty']); ?></span>
    </td>
    <td class="right"><?= show(priceFormatted($product->price_discounted > 0 ? $product->price_discounted : $product->price, $currency)); ?></td>
</tr>
<?php endforeach; endif; ?>
<?php endif; ?>

<tr><td colspan="2"><hr></td></tr>

<?php endforeach; ?>

</table>


<hr>

<table>
<tr>
    <td><?= trans("subtotal"); ?></td>
    <td class="right"><?= show(priceFormatted($order->price_subtotal ?? 0, $currency)); ?></td>
</tr>
<?php if (!empty($order->price_vat) && $order->price_vat > 0):
    $orderShipping = unserializeData($order->shipping ?? '');
    $clientState = !empty($invoice->client_state) ? $invoice->client_state : (!empty($orderShipping->sState) ? $orderShipping->sState : (!empty($orderShipping->bState) ? $orderShipping->bState : null));
    $firstSellerId = !empty($orderProducts[0]->seller_id) ? $orderProducts[0]->seller_id : null;
    $orderGst = calculateGstDetails(0, $order->price_vat, $firstSellerId, $clientState);
    if ($orderGst['is_interstate']): ?>
        <tr>
            <td><?= trans("igst"); ?></td>
            <td class="right"><?= show(priceFormatted($orderGst['igst_amount'], $currency)); ?></td>
        </tr>
    <?php else: ?>
        <tr>
            <td><?= trans("cgst"); ?></td>
            <td class="right"><?= show(priceFormatted($orderGst['cgst_amount'], $currency)); ?></td>
        </tr>
        <tr>
            <td><?= trans("sgst"); ?></td>
            <td class="right"><?= show(priceFormatted($orderGst['sgst_amount'], $currency)); ?></td>
        </tr>
    <?php endif; ?>
    <tr>
        <td><?= trans("total_gst"); ?></td>
        <td class="right"><?= show(priceFormatted($order->price_vat, $currency)); ?></td>
    </tr>
<?php endif; ?>
<tr class="total">
    <td><?= trans("total"); ?></td>
    <td class="right"><?= show(priceFormatted($order->price_total ?? 0, $currency)); ?></td>
</tr>
</table>

<hr>

<?= trans("payment"); ?>: <?= show(getPaymentMethod($order->payment_method ?? null)); ?><br>
<?= trans("status"); ?>: <?= show(getPaymentStatus($order->payment_status ?? null)); ?>

<hr>

<div class="center bold">** <?= trans("vat"); ?> <?= trans("invoice"); ?> **</div>

</body>
</html>
