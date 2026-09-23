<?php
$sym      = !empty($currency) ? esc($currency->symbol) : '₹';
$items    = json_decode($order->items ?? '[]', true) ?: [];
$billing  = json_decode($order->billing_address  ?? '{}', true) ?: [];
$shipping = json_decode($order->shipping_address ?? '{}', true) ?: [];
$siteName = $generalSettings->application_name ?? ($baseSettings->site_title ?? '');
$siteAddr = $baseSettings->contact_address ?? '';
$siteEmail= $baseSettings->contact_email   ?? '';
$sitePhone= $baseSettings->contact_phone   ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>POS <?= ucfirst(esc($type)) ?> — <?= esc($order->order_number) ?></title>
<?php if ($type === 'invoice'): ?>
<link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>">
<?php endif; ?>
<style>
/* ── Receipt (narrow thermal) ── */
.receipt { max-width: 320px; margin: 0 auto; font-family: "Courier New", monospace; font-size: 12px; color:#000; }
.receipt h1 { font-size: 16px; text-align: center; margin:0 0 2px; }
.receipt .meta { text-align: center; color:#555; font-size:11px; margin-bottom:4px; }
.receipt hr { border:none; border-top:1px dashed #000; margin:8px 0; }
.receipt table { width:100%; border-collapse:collapse; font-size:11px; }
.receipt table td { border:none; padding:2px 0; vertical-align:top; }
.receipt table td:last-child { text-align:right; white-space:nowrap; }
.receipt .thank { text-align:center; margin-top:10px; font-weight:600; }

/* ── Invoice (Bootstrap page) ── */
body.invoice-page { font-size:15px; }
.logo img { width:160px; height:auto; }
.container-invoice { max-width:900px; margin:0 auto; }
.order-total { width:400px; max-width:100%; float:right; padding:20px; }
.order-total .col-left { font-weight:600; }
.order-total .col-right { text-align:right; }
#btn_print { min-width:180px; }
table { border-bottom:1px solid #dee2e6; }
table th { font-size:14px; white-space:nowrap; }

/* ── Address label — matches _label_print.php ── */
@page { size: 100mm 150mm; margin: 0; }
body.label-page { margin:0; padding:0; font-family:Arial,sans-serif; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
body.label-page * { box-sizing:border-box; }
.label { width:100mm; height:150mm; border:1px solid #000; padding:6mm; page-break-after:always; overflow:hidden; }
.label .header { text-align:center; font-size:14px; font-weight:700; margin-bottom:6px; letter-spacing:.3px; }
.label .barcode { width:100%; height:60px; object-fit:contain; margin:6px 0 2px; }
.label .barcode-text { text-align:center; font-size:12px; font-weight:700; margin-bottom:6px; }
.label .lrow { display:flex; gap:6px; }
.label .lcol { flex:1; }
.label .box { border:1px solid #000; padding:5px; margin-bottom:6px; }
.label .box-title { font-weight:700; font-size:11px; margin-bottom:3px; }
.label .line { margin:1px 0; font-size:11px; }
.label .big { font-size:13px; font-weight:700; }
.label .divider { border:0; border-top:1px dashed #000; margin:6px 0; }
.label .products { font-size:10px; line-height:12px; }
.label .muted { font-size:10px; color:#111; }
.print-button { display:block; margin:16px auto; padding:8px 16px; cursor:pointer; }
@media print { .print-button, #btn_print, .hidden-print { display:none !important; } body { padding:0; } }
</style>
</head>

<?php if ($type === 'receipt'): ?>
<!-- ══════════════════════════════════════════════════════
     POS RECEIPT (narrow thermal style)
══════════════════════════════════════════════════════ -->
<body>
<div class="receipt">
  <h1><?= esc($siteName) ?></h1>
  <p class="meta">Sales Receipt</p>
  <hr>
  <p><strong>Order:</strong> <?= esc($order->order_number) ?></p>
  <p><strong>Date:</strong> <?= date('d M Y, h:i A', strtotime($order->created_at)) ?></p>
  <p><strong>Customer:</strong> <?= esc($order->customer_name) ?></p>
  <?php if (!empty($order->customer_phone)): ?>
    <p><strong>Mobile:</strong> <?= esc($order->customer_phone) ?></p>
  <?php endif; ?>
  <hr>
  <table>
    <thead><tr><th>Item</th><th>Amt</th></tr></thead>
    <tbody>
    <?php foreach ($items as $item): ?>
      <tr>
        <td>
          <?= esc($item['product_name'] ?? '') ?><br>
          <small><?= (int)($item['quantity'] ?? 1) ?>×<?= $sym . number_format((float)($item['unit_price'] ?? 0), 2) ?>
          <?php if (!empty($item['discount_price'])): ?> -<?= $item['discount_price'] ?><?php endif; ?>
          GST <?= (int)($item['tax_percent'] ?? 0) ?>%</small>
        </td>
        <td><?= $sym . number_format((float)($item['line_total'] ?? 0), 2) ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <hr>
  <table>
    <tr><td>Subtotal</td><td><?= $sym . number_format((float)$order->subtotal, 2) ?></td></tr>
    <tr><td>GST</td><td><?= $sym . number_format((float)$order->total_tax, 2) ?></td></tr>
    <?php if (!empty($order->shipping_charges) && $order->shipping_charges > 0): ?>
    <tr><td>Shipping</td><td><?= $sym . number_format((float)$order->shipping_charges, 2) ?></td></tr>
    <?php endif; ?>
    <tr><td><strong>Grand Total</strong></td><td><strong><?= $sym . number_format((float)$order->grand_total, 2) ?></strong></td></tr>
    <tr><td>Paid</td><td><?= $sym . number_format((float)$order->amount_paid, 2) ?></td></tr>
    <?php if ($order->balance_due > 0): ?>
    <tr><td style="color:#c00"><strong>Balance Due</strong></td><td style="color:#c00"><strong><?= $sym . number_format((float)$order->balance_due, 2) ?></strong></td></tr>
    <?php endif; ?>
  </table>
  <hr>
  <p><strong>Method:</strong> <?= esc($order->payment_method) ?></p>
  <?php if (!empty($order->payment_reference)): ?>
    <p><strong>Ref:</strong> <?= esc($order->payment_reference) ?></p>
  <?php endif; ?>
  <hr>
  <p class="thank">Thank you for your purchase!</p>
</div>
<script>window.onload = function(){ window.print(); };</script>
</body>

<?php elseif ($type === 'invoice'): ?>
<!-- ══════════════════════════════════════════════════════
     INVOICE — matches invoice_seller.php style
══════════════════════════════════════════════════════ -->
<body class="invoice-page">
<div class="container" style="width:898px;max-width:898px;min-width:898px;">
  <div class="row">
    <div class="col-12">
      <div class="container-invoice">
        <div class="card">
          <div class="card-body invoice p-0">

            <div class="row">
              <div class="col-12">
                <h1 style="text-align:center;font-size:36px;font-weight:400;margin-top:20px;">TAX INVOICE</h1>
              </div>
            </div>

            <!-- Logo + site info | Invoice number + date -->
            <div class="row" style="padding:45px 30px;">
              <div class="col-6">
                <div class="logo"><img src="<?= getLogo() ?>" alt="logo"></div>
                <div>
                  <?php if (!empty($siteAddr)): ?><p style="margin-bottom:5px;"><?= esc($siteAddr) ?></p><?php endif; ?>
                  <?php if (!empty($siteEmail)): ?><p style="margin-bottom:5px;"><?= esc($siteEmail) ?></p><?php endif; ?>
                  <?php if (!empty($sitePhone)): ?><p style="margin-bottom:5px;"><?= esc($sitePhone) ?></p><?php endif; ?>
                </div>
              </div>
              <div class="col-6">
                <div class="float-right">
                  <p class="font-weight-bold mb-1"><span style="display:inline-block;width:100px;">Invoice:</span>#<?= esc($order->order_number) ?></p>
                  <p class="font-weight-bold"><span style="display:inline-block;width:100px;">Date:</span><?= date('d M Y', strtotime($order->created_at)) ?></p>
                </div>
              </div>
            </div>

            <!-- Client info | Payment details -->
            <div class="row" style="padding:0 30px 45px;">
              <div class="col-6">
                <p class="font-weight-bold mb-3">Client Information</p>
                <p class="mb-1"><?= esc($order->customer_name) ?></p>
                <?php if (!empty($billing['line1'])): ?>
                  <p class="mb-1"><?= esc($billing['line1']) ?></p>
                <?php endif; ?>
                <?php
                  $bLine = implode(', ', array_filter([$billing['city'] ?? '', $billing['state'] ?? '', $billing['pincode'] ?? '']));
                  if ($bLine): ?><p class="mb-1"><?= esc($bLine) ?></p>
                <?php endif; ?>
                <?php if (!empty($order->customer_email)): ?>
                  <p class="mb-1"><?= esc($order->customer_email) ?></p>
                <?php endif; ?>
                <?php if (!empty($order->customer_phone)): ?>
                  <p class="mb-1"><?= esc($order->customer_phone) ?></p>
                <?php endif; ?>
              </div>
              <div class="col-6">
                <div class="float-right">
                  <p class="font-weight-bold mb-3">Payment Details</p>
                  <p class="mb-1">
                    <span style="display:inline-block;min-width:158px;">Payment Status:</span>
                    <strong style="color:<?= $order->status == 1 ? '#00a65a' : '#dd4b39' ?>">
                      <?= $order->status == 1 ? 'Paid' : ($order->balance_due > 0 ? 'Partial' : 'Pending') ?>
                    </strong>
                  </p>
                  <p class="mb-1"><span style="display:inline-block;min-width:158px;">Payment Method:</span><?= esc($order->payment_method) ?></p>
                  <p class="mb-1"><span style="display:inline-block;min-width:158px;">Currency:</span><?= esc($currency->currency_code ?? '') ?></p>
                  <p class="mb-1"><span style="display:inline-block;min-width:158px;">Fulfillment:</span><?= ucfirst(esc($order->fulfillment_status ?? 'processing')) ?></p>
                </div>
              </div>
            </div>

            <!-- Items table -->
            <div class="row p-4">
              <div class="col-md-12">
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                      <tr>
                        <th class="border-0 font-weight-bold">#</th>
                        <th class="border-0 font-weight-bold">Product</th>
                        <th class="border-0 font-weight-bold">Qty</th>
                        <th class="border-0 font-weight-bold">Unit Price</th>
                        <th class="border-0 font-weight-bold">Disc</th>
                        <th class="border-0 font-weight-bold">GST %</th>
                        <th class="border-0 font-weight-bold">GST Amt</th>
                        <th class="border-0 font-weight-bold">Total</th>
                      </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($items as $i => $item): ?>
                      <tr style="font-size:15px;">
                        <td><?= $i + 1 ?></td>
                        <td><?= esc($item['product_name'] ?? '') ?></td>
                        <td><?= (int)($item['quantity'] ?? 1) ?></td>
                        <td style="white-space:nowrap"><?= $sym . number_format((float)($item['unit_price'] ?? 0), 2) ?></td>
                        <td><?= number_format((float)($item['discount_price'] ?? 0), 1) ?></td>
                        <td><?= number_format((float)($item['tax_percent'] ?? 0), 0) ?>%</td>
                        <td style="white-space:nowrap"><?= $sym . number_format((float)($item['tax_amount'] ?? 0), 2) ?></td>
                        <td style="white-space:nowrap"><?= $sym . number_format((float)($item['line_total'] ?? 0), 2) ?></td>
                      </tr>
                    <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- Totals -->
            <div class="row">
              <div class="col-12">
                <div class="order-total float-right">
                  <div class="row mb-2">
                    <div class="col-7 col-left">Subtotal</div>
                    <div class="col-5 col-right"><strong><?= $sym . number_format((float)$order->subtotal, 2) ?></strong></div>
                  </div>
                  <?php if (!empty($order->total_tax)): ?>
                  <div class="row mb-2">
                    <div class="col-7 col-left">Total GST</div>
                    <div class="col-5 col-right"><strong><?= $sym . number_format((float)$order->total_tax, 2) ?></strong></div>
                  </div>
                  <?php endif; ?>
                  <?php if (!empty($order->shipping_charges) && $order->shipping_charges > 0): ?>
                  <div class="row mb-2">
                    <div class="col-7 col-left">Shipping</div>
                    <div class="col-5 col-right"><strong><?= $sym . number_format((float)$order->shipping_charges, 2) ?></strong></div>
                  </div>
                  <?php endif; ?>
                  <div class="row mb-2">
                    <div class="col-7 col-left">Grand Total</div>
                    <div class="col-5 col-right"><strong><?= $sym . number_format((float)$order->grand_total, 2) ?></strong></div>
                  </div>
                  <div class="row mb-2">
                    <div class="col-7 col-left">Amount Paid</div>
                    <div class="col-5 col-right"><strong><?= $sym . number_format((float)$order->amount_paid, 2) ?></strong></div>
                  </div>
                  <?php if ($order->balance_due > 0): ?>
                  <div class="row mb-2">
                    <div class="col-7 col-left" style="color:#dd4b39">Balance Due</div>
                    <div class="col-5 col-right" style="color:#dd4b39"><strong><?= $sym . number_format((float)$order->balance_due, 2) ?></strong></div>
                  </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>

            <!-- Payment history -->
            <?php if (!empty($payments)): ?>
            <div class="row p-4" style="clear:both;">
              <div class="col-12">
                <p class="font-weight-bold mb-2">Payment History</p>
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                      <tr>
                        <th class="border-0">Date</th>
                        <th class="border-0">Method</th>
                        <th class="border-0">Reference</th>
                        <th class="border-0 text-right">Amount</th>
                      </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($payments as $p): ?>
                      <tr>
                        <td><?= date('d M Y', strtotime($p->payment_date ?: $p->created_at)) ?></td>
                        <td><?= esc($p->payment_method) ?></td>
                        <td><?= esc($p->payment_reference) ?></td>
                        <td class="text-right" style="white-space:nowrap"><?= $sym . number_format((float)$p->amount, 2) ?></td>
                      </tr>
                    <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <?php endif; ?>

          </div><!-- /card-body -->
        </div><!-- /card -->
      </div>
    </div>
  </div>
</div>
<div class="container" style="margin-bottom:80px;">
  <div class="row">
    <div class="col-12 text-center mt-3">
      <button id="btn_print" class="btn btn-secondary btn-md hidden-print">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="16" height="16" fill="none" stroke="currentcolor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" style="margin-top:-4px;">
          <path d="M7 25 L2 25 2 9 30 9 30 25 25 25 M7 19 L7 30 25 30 25 19 Z M25 9 L25 2 7 2 7 9 M22 14 L25 14"/>
        </svg>
        &nbsp;&nbsp;Print
      </button>
    </div>
  </div>
</div>
<script src="<?= base_url('assets/js/jquery-3.5.1.min.js') ?>"></script>
<script>$(document).on('click','#btn_print',function(){ window.print(); });</script>
</body>

<?php else: /* address label — same layout as dashboard/sales/_label_print.php */ ?>
<!-- ══════════════════════════════════════════════════════
     ADDRESS / COURIER LABEL — matches _label_print.php
══════════════════════════════════════════════════════ -->
<body class="label-page">
<?php
// FROM = site/shop
$fromName  = $siteName;
$fromAddr  = $siteAddr;
$fromPhone = $sitePhone;

// TO = customer shipping address (JSON in pos_orders)
$toName    = $order->customer_name  ?? '';
$toPhone   = $order->customer_phone ?? '';
$toLine1   = $shipping['line1']   ?? '';
$toCity    = $shipping['city']    ?? '';
$toState   = $shipping['state']   ?? '';
$toZip     = $shipping['pincode'] ?? '';
$toCountry = $shipping['country'] ?? '';

$orderNo   = $order->order_number ?? '';
$orderDate = date('d M Y', strtotime($order->created_at));

// Build product lines (max 3)
$maxLines  = 3;
$lines     = [];
$totalQty  = 0;
foreach ($items as $item) {
    $qty = (int)($item['quantity'] ?? 1);
    $totalQty += $qty;
    if (count($lines) < $maxLines) {
        $lines[] = esc($item['product_name'] ?? 'Item') . ' x' . $qty;
    }
}
$extraItems = count($items) - count($lines);
?>

<div class="label">
    <div class="header">SHIPPING / COURIER LABEL</div>

    <img class="barcode"
         src="data:image/png;base64,<?= esc(generateBarcodeImage($orderNo)) ?>"
         alt="Barcode">
    <div class="barcode-text"><?= esc($orderNo) ?></div>

    <div class="lrow">
        <!-- FROM -->
        <div class="lcol box">
            <div class="box-title">FROM (Seller)</div>
            <div class="line big"><?= esc($fromName) ?></div>
            <?php if (!empty($fromAddr)): ?>
            <div class="line"><?= esc($fromAddr) ?></div>
            <?php endif; ?>
            <?php if (!empty($fromPhone)): ?>
            <div class="line"><b>Phone:</b> <?= esc($fromPhone) ?></div>
            <?php endif; ?>
        </div>

        <!-- ORDER -->
        <div class="lcol box">
            <div class="box-title">ORDER</div>
            <div class="line"><b>Order #:</b> <?= esc($orderNo) ?></div>
            <div class="line"><b>Date:</b> <?= $orderDate ?></div>
            <div class="line"><b>Items:</b> <?= $totalQty ?></div>
            <div class="line"><b>Mode:</b> <?= esc($order->payment_method ?? '') ?></div>
        </div>
    </div>

    <!-- TO -->
    <div class="box">
        <div class="box-title">TO (Recipient)</div>
        <div class="line big"><?= esc($toName) ?></div>
        <?php if (!empty($toLine1)): ?>
        <div class="line"><?= esc($toLine1) ?></div>
        <?php endif; ?>
        <?php if (!empty($toCity) || !empty($toState) || !empty($toZip)): ?>
        <div class="line"><?= esc(implode(', ', array_filter([$toCity, $toState]))) ?><?= !empty($toZip) ? ' - <b>' . esc($toZip) . '</b>' : '' ?></div>
        <?php endif; ?>
        <?php if (!empty($toCountry)): ?>
        <div class="line"><?= esc($toCountry) ?></div>
        <?php endif; ?>
        <?php if (!empty($toPhone)): ?>
        <div class="line"><b>Phone:</b> <?= esc($toPhone) ?></div>
        <?php endif; ?>
    </div>

    <hr class="divider">

    <div class="products">
        <b>Contents:</b><br>
        <?php foreach ($lines as $ln): ?>
            • <?= $ln ?><br>
        <?php endforeach; ?>
        <?php if ($extraItems > 0): ?>
            <span class="muted">+ <?= $extraItems ?> more item(s)…</span>
        <?php endif; ?>
    </div>
</div>

<button class="print-button" onclick="window.print()">Print Label</button>
</body>
<?php endif; ?>
</html>
