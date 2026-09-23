<!DOCTYPE html>
<html lang="<?= $activeLang->short_form; ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <title>Shipping Label - #<?= esc($order->order_number); ?></title>

    <style>
        /* ===== THERMAL LABEL PRINT SETTINGS ===== */
        @page {
            size: 100mm 150mm;   /* standard courier label */
            margin: 0;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* ===== LABEL ===== */
        .label {
            width: 100mm;
            height: 150mm;
            border: 1px solid #000;
            padding: 6mm;
            page-break-after: always;
            overflow: hidden;
        }

        .header {
            text-align: center;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 6px;
            letter-spacing: .3px;
        }

        .barcode {
            width: 100%;
            height: 60px;
            object-fit: contain;
            margin: 6px 0 2px;
        }

        .barcode-text {
            text-align: center;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .row {
            display: flex;
            gap: 6px;
        }
        .col { flex: 1; }

        .box {
            border: 1px solid #000;
            padding: 5px;
            margin-bottom: 6px;
        }

        .box-title {
            font-weight: 700;
            font-size: 11px;
            margin-bottom: 3px;
        }

        .line { margin: 1px 0; font-size: 11px; }
        .big { font-size: 13px; font-weight: 700; }

        .divider {
            border: 0;
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        .products {
            font-size: 10px;
            line-height: 12px;
        }

        .muted { font-size: 10px; color: #111; }

        .print-button {
            display: block;
            margin: 16px auto;
            padding: 8px 16px;
        }

        @media print {
            .print-button { display: none !important; }
        }
    </style>
</head>
<body>

<?php
// -------------------- SAFE ESCAPE SHORTCUT --------------------
if (!function_exists('e')) {
    function e($str) { return esc((string)$str); }
}

// -------------------- FROM (SELLER) DETAILS --------------------
$fromName  = $generalSettings->application_name ?? ($baseSettings->site_title ?? 'Seller');
$fromAddr  = $baseSettings->contact_address ?? '';
$fromPhone = $baseSettings->contact_phone ?? '';

// -------------------- SHIPPING (TO) DETAILS --------------------
// In your project shipping often stored serialized
$shipping = !empty($order->shipping) ? unserializeData($order->shipping) : null;
if (!is_object($shipping)) { $shipping = (object)[]; }

$toName    = trim(($shipping->sFirstName ?? '') . ' ' . ($shipping->sLastName ?? ''));
$toPhone   = $shipping->sPhoneNumber ?? '';
$toAddr    = $shipping->sAddress ?? '';
$toCity    = $shipping->sCity ?? '';
$toState   = $shipping->sState ?? '';
$toZip     = $shipping->sZipCode ?? '';
$toCountry = $shipping->sCountry ?? '';

// -------------------- ORDER INFO --------------------
$orderNo   = $order->order_number ?? '';
$orderDate = !empty($order->created_at) ? formatDate($order->created_at) : '';
$currency  = $order->price_currency ?? '';

$isCod     = (($order->payment_method ?? '') === 'cash_on_delivery');
$payMode   = $isCod ? 'COD' : 'Prepaid';
$codAmount = $isCod ? priceFormatted($order->price_total, $currency) : '';

// -------------------- PRODUCTS SUMMARY (FROM invoiceItems like invoice page) --------------------
$totalQty = 0;
$lines = [];
$maxLines = 3;

if (!empty($invoiceItems) && is_array($invoiceItems)) {
    foreach ($invoiceItems as $item) {
        if (empty($item['id'])) continue;

        $op = getOrderProduct($item['id']);
        if (empty($op)) continue;

        $qty = (int)($op->product_quantity ?? 0);
        $totalQty += $qty;

        if (count($lines) < $maxLines) {
            // SKU logic same as invoice page
            $sku = !empty($item['sku']) ? $item['sku'] : '';
            if (empty($sku)) {
                $p = getProduct($op->product_id);
                if (!empty($p)) $sku = $p->sku;
            }

            $line = ($op->product_title ?? 'Item') . ' x' . $qty;
            if (!empty($sku)) $line .= ' (SKU: ' . $sku . ')';
            $lines[] = $line;
        }
    }
}

// -------------------- BARCODE TEXT --------------------
// If you have AWB/Tracking in DB, replace $barcodeText with that.
$barcodeText = $orderNo;
?>

<div class="label">
    <div class="header">SHIPPING / COURIER LABEL</div>

    <!-- BARCODE (Uses your existing barcode generator) -->
    <img class="barcode"
         src="data:image/png;base64,<?= e(generateBarcodeImage($barcodeText)); ?>"
         alt="Barcode">
    <div class="barcode-text"><?= e($barcodeText); ?></div>

    <div class="row">
        <!-- FROM -->
        <div class="col box">
            <div class="box-title">FROM (Seller)</div>
            <div class="line big"><?= e($fromName); ?></div>
            <div class="line"><?= e($fromAddr); ?></div>
            <div class="line"><b>Phone:</b> <?= e($fromPhone); ?></div>
        </div>

        <!-- ORDER -->
        <div class="col box">
            <div class="box-title">ORDER</div>
            <div class="line"><b>Order #:</b> <?= e($orderNo); ?></div>
            <div class="line"><b>Date:</b> <?= e($orderDate); ?></div>
            <div class="line"><b>Items:</b> <?= (int)$totalQty; ?></div>
            <div class="line"><b>Mode:</b> <?= e($payMode); ?></div>
            <?php if ($isCod): ?>
                <div class="line"><b>COD:</b> <?= e($codAmount); ?></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- TO -->
    <div class="box">
        <div class="box-title">TO (Recipient)</div>
        <div class="line big"><?= e($toName); ?></div>
        <div class="line"><?= e($toAddr); ?></div>
        <div class="line"><?= e($toCity); ?>, <?= e($toState); ?> - <b><?= e($toZip); ?></b></div>
        <div class="line"><?= e($toCountry); ?></div>
        <div class="line"><b>Phone:</b> <?= e($toPhone); ?></div>
    </div>

    <hr class="divider">

    <!-- PRODUCTS (optional small) -->
    <div class="products">
        <b>Contents (Top <?= count($lines); ?>):</b><br>
        <?php if (!empty($lines)): ?>
            <?php foreach ($lines as $ln): ?>
                • <?= e($ln); ?><br>
            <?php endforeach; ?>
            <?php if (count($invoiceItems) > $maxLines): ?>
                <span class="muted">+ more items…</span>
            <?php endif; ?>
        <?php else: ?>
            <span class="muted">No items</span>
        <?php endif; ?>
    </div>

    <!-- OPTIONAL: Shipping fee -->
    <?php if (!empty($order->price_shipping) && $order->price_shipping > 0): ?>
        <div class="muted" style="margin-top:6px;">
            Shipping: <?= priceFormatted($order->price_shipping, $currency); ?>
        </div>
    <?php endif; ?>
</div>

<button class="print-button" onclick="window.print()">Print</button>

</body>
</html>
