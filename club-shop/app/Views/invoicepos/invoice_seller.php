<?php
use App\Models\ProductModel;
$productModel = model(ProductModel::class);
function show($v){ return (!empty($v)) ? esc($v) : '--'; }

$currency = $order->price_currency ?? 'INR';
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>POS Bill</title>

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
    <?= show($baseSettings->contact_address); ?><br>
    GSTIN: <?= show($baseSettings->gst_number ?? null); ?>
</div>

<hr>

<?= trans("invoice"); ?> No: <?= show($order->order_number); ?><br>
<?= trans("date"); ?>: <?= formatDate($order->created_at); ?><br>
POS: <?= show($invoice->client_state); ?>

<hr>

<b><?= trans("customer"); ?></b><br>
<?= show(($invoice->client_first_name ?? '').' '.($invoice->client_last_name ?? '')); ?><br>
<?= show($invoice->client_address); ?><br>
<?= show($invoice->client_city); ?><br>
<?= show($invoice->client_state); ?><br>
Ph: <?= show($invoice->client_phone_number); ?>

<hr>

<table>
<?php foreach($orderProducts as $p): ?>

<tr>
<td>
<?= show($p->product_title); ?><br>
<span class="small"><?= trans("sku"); ?>: <?= show($p->product_sku); ?> <br>
<?= trans("qty"); ?>: <?= show($p->product_quantity); ?></span>
</td>
<td class="right"><?= priceFormatted($p->product_unit_price, $currency); ?></td>
</tr>

<?php if($p->is_bundle && !empty($p->bundle_items)):
$bundleItems = json_decode($p->bundle_items, true);
foreach($bundleItems as $row):
$product = $productModel->getActiveProduct($row['product_id']);
?>
<tr>
<td style="padding-left:10px;">
└ <?= show($product->title); ?> <br> 
<?= trans("sku"); ?>: <?= show($product->sku); ?> <br> 
<?= trans("qty"); ?>: <?= $row['qty']; ?>
</td>
<td class="right">
<?= priceFormatted($product->price_discounted > 0 ? $product->price_discounted : $product->price, $currency); ?>
</td>
</tr>
<?php endforeach; endif; ?>

<tr><td colspan="2"><hr></td></tr>

<?php endforeach; ?>
</table>

<table>
<tr><td><?= trans("subtotal"); ?></td><td class="right"><?= priceFormatted($order->price_subtotal, $currency); ?></td></tr>
<tr><td><?= trans("vat"); ?></td><td class="right"><?= priceFormatted($order->price_vat, $currency); ?></td></tr>
<tr class="total"><td><?= trans("total"); ?></td><td class="right"><?= priceFormatted($order->price_total, $currency); ?></td></tr>
</table>

<hr>

<?= trans("payment"); ?>: <?= getPaymentMethod($order->payment_method); ?><br>
<?= trans("status"); ?>: <?= getPaymentStatus($order->payment_status); ?>

<hr>

<div class="center bold">** <?= trans("vat"); ?> <?= trans("invoice"); ?> **</div>

</body>
</html>
