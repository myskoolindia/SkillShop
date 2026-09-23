<!DOCTYPE html>
<html>
<head>
<title>Barcode Print</title>

<style>
    @page {
        size: 78mm 40mm;
        margin: 0;
    }

    html, body {
        width: 78mm;
        height: 40mm;
        margin: 0;
        padding: 0;
    }

    .label {
        width: 78mm;
        height: 40mm;
        padding: 1.5mm;
        box-sizing: border-box;

        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .barcode {
        width: 100%;
        /* height: 0mm;   give most space to barcode */
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .barcode img {
        width: 100%;
        height: 15mm;
        /* object-fit: contain; */
    }

    .code {
        font-size: 20px;        /* smaller text */
        margin-top: 0;        /* remove gap */
        line-height: 1;
        letter-spacing: 0.5px;
    }
    .pagebreak { page-break-after: always; }

    @media print {
        .print-bar { display: none; }
    }
    </style>


</head>
<body>

<?php for ($i = 0; $i < $qty; $i++): ?>
    <div class="label">
       

        <div class="barcode">
            <img src="data:image/png;base64,<?= generateBarcodeImage($product->sku) ?>">
        </div>

        <div class="text">
            <div class="code"><?= htmlspecialchars($product->sku); ?></div>
        </div>
    </div>

    <!-- FORCE NEXT LABEL FEED -->
    <div style="page-break-after: always;"></div>
<?php endfor; ?>

<div class="print-bar">
    <button onclick="window.print()">Print</button>
</div>


</body>
</html>
