<?php
$csrfName = csrf_token();
$csrfHash = csrf_hash();
$baseUrl  = rtrim(base_url(), '/') . '/';
$newOrderUrl = dashboardUrl('pos-new-order');
$posListUrl  = dashboardUrl('pos-sales');
?>
<style>
.pos-badge { display:inline-block; padding:3px 8px; border-radius:3px; font-size:12px; font-weight:600; }
.pos-badge-processing { background:#f39c12; color:#fff; }
.pos-badge-shipped    { background:#3c8dbc; color:#fff; }
.pos-badge-delivered  { background:#00a65a; color:#fff; }
.pos-badge-paid       { background:#00a65a; color:#fff; }
.pos-badge-partial    { background:#dd4b39; color:#fff; }
.pos-status-select { padding:4px 8px; border:1px solid #ccd0d5; border-radius:3px; font-size:13px; background:#fff; cursor:pointer; }
/* Delivery modal */
.delivery-modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:9999; align-items:center; justify-content:center; }
.delivery-modal-overlay.active { display:flex; }
.delivery-modal { background:#fff; border-radius:6px; padding:24px; width:100%; max-width:480px; box-shadow:0 8px 30px rgba(0,0,0,.2); position:relative; }
.delivery-modal h4 { margin:0 0 16px; font-size:1rem; font-weight:700; color:#333; }
.dm-field { margin-bottom:12px; }
.dm-label { display:block; font-size:13px; font-weight:600; color:#555; margin-bottom:4px; }
.dm-input { width:100%; padding:6px 10px; border:1px solid #ccd0d5; border-radius:4px; font-size:14px; height:34px; box-sizing:border-box; }
.dm-input:focus { border-color:#3c8dbc; outline:none; }
.dm-close { position:absolute; top:10px; right:14px; background:none; border:none; font-size:20px; cursor:pointer; color:#888; line-height:1; }
</style>

<!-- Delivery Details Modal (shared for shipped + delivered) -->
<div class="delivery-modal-overlay" id="deliveryModalOverlay">
  <div class="delivery-modal">
    <button class="dm-close" onclick="closeDeliveryModal()">&times;</button>
    <h4><i class="fa fa-truck" style="color:#00a65a"></i> &nbsp;Details</h4>
    <input type="hidden" id="dm_order_id">
    <input type="hidden" id="dm_fulfillment">
    <div class="dm-field" id="dm_tracking_row"><label class="dm-label">Tracking Code <span style="color:#dd4b39">*</span></label><input class="dm-input" id="dm_tracking_code" placeholder="e.g. DTDC123456"></div>
    <div class="dm-field" id="dm_tracking_url_row"><label class="dm-label">Tracking URL <span style="color:#dd4b39">*</span></label><input class="dm-input" id="dm_tracking_url" type="url" placeholder="https://track.courier.com/..."></div>
    <div class="dm-field"><label class="dm-label">Delivery Date <span style="color:#dd4b39">*</span></label><input class="dm-input" id="dm_date" type="date"></div>
    <div class="dm-field"><label class="dm-label">Name <span style="color:#dd4b39">*</span></label><input class="dm-input" id="dm_name" placeholder="Name of person who received"></div>
    <div class="dm-field"><label class="dm-label">Phone <span style="color:#dd4b39">*</span></label><input class="dm-input" id="dm_phone" placeholder="Phone number"></div>
    <div class="dm-field" id="dm_amount_row"><label class="dm-label">Amount <span style="color:#dd4b39">*</span></label><input class="dm-input" id="dm_amount" type="number" min="0" step="0.01" placeholder="0.00"></div>
    <div style="border-top:1px solid #eee; margin-top:12px; padding-top:12px;">
      <h5 style="margin:0 0 10px; font-weight:700; color:#555;"><i class="fa fa-map-marker" style="color:#3c8dbc"></i> Shipping Address</h5>
      <div class="dm-field"><label class="dm-label">Address Line 1 <span style="color:#dd4b39">*</span></label><input class="dm-input" id="dm_shipping_line1" placeholder="Address"></div>
      <div style="display:flex; gap:8px;">
        <div class="dm-field" style="flex:1;"><label class="dm-label">City <span style="color:#dd4b39">*</span></label><input class="dm-input" id="dm_shipping_city" placeholder="City"></div>
        <div class="dm-field" style="flex:1;"><label class="dm-label">State <span style="color:#dd4b39">*</span></label><input class="dm-input" id="dm_shipping_state" placeholder="State"></div>
        <div class="dm-field" style="flex:1;"><label class="dm-label">Pincode <span style="color:#dd4b39">*</span></label><input class="dm-input" id="dm_shipping_pincode" placeholder="Pincode" maxlength="10"></div>
      </div>
    </div>
    <div style="margin-top:16px;display:flex;gap:8px">
      <button class="btn btn-sm btn-success" onclick="saveDeliveryDetails()"><i class="fa fa-save"></i> Save</button>
      <button class="btn btn-sm btn-default" onclick="closeDeliveryModal()">Cancel</button>
    </div>
  </div>
</div>

<div class="box">
    <div class="box-header with-border">
        <div class="left">
            <h3 class="box-title">POS Orders</h3>
        </div>
        <div class="right">
            <a href="<?= $newOrderUrl ?>" class="btn btn-sm btn-success">
                <i class="fa fa-plus"></i> New POS Order
            </a>
        </div>
    </div>
    <div class="box-body">
        <div class="row table-filter-container">
            <div class="col-sm-12">
                <button type="button" class="btn btn-default filter-toggle collapsed m-b-10" data-toggle="collapse" data-target="#collapseFilter" aria-expanded="false">
                    <i class="fa fa-filter"></i>&nbsp;&nbsp;<?= trans("filter"); ?>
                </button>
                <div class="collapse navbar-collapse" id="collapseFilter">
                    <form action="<?= generateDashUrl('pos_sales'); ?>" method="get" id="formVendorSales">
                        <?php if (!empty(inputGet('st'))): ?>
                            <input type="hidden" name="st" value="<?= esc(inputGet('st')); ?>">
                        <?php endif;
                        // if ($page == 'sales'): 
                        ?>
                            <div class="item-table-filter">
                                <label><?= trans("payment_status"); ?></label>
                                <select name="payment_status" class="form-control custom-select">
                                    <option value="" selected><?= trans("all"); ?></option>
                                    <option value="1" <?= inputGet('payment_status') == '1' ? 'selected' : ''; ?>><?= trans("Paid"); ?></option>
                                    <option value="0" <?= inputGet('payment_status') == '0' ? 'selected' : ''; ?>><?= trans("Partial"); ?></option>
                                </select>
                            </div>
                            <div class="item-table-filter">
                                <label>Fulfillment Status</label>
                                <select name="fulfillment_status" class="form-control custom-select">
                                    <option value="" selected><?= trans("all"); ?></option>
                                    <option value="processing" <?= inputGet('fulfillment_status') == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="shipped" <?= inputGet('fulfillment_status') == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="delivered" <?= inputGet('fulfillment_status') == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                </select>
                            </div>
                        <!-- <?//php endif; ?> -->
                        <div class="item-table-filter item-table-filter-large">
                            <label><?= trans("search"); ?></label>
                            <div class="item-table-filter-search">
                                <input name="q" class="form-control" placeholder="" type="search" value="<?= strSlug(esc(inputGet('q'))); ?>">
                                <button type="submit" class="btn bg-purple"><?= trans("filter"); ?></button>
                                <!-- <div class="btn-group table-export">
                                    <button type="button" class="btn btn-default dropdown-toggle btn-table-export" data-toggle="dropdown"><?= trans("export"); ?>&nbsp;&nbsp;<i class="fa fa-caret-down"></i></button>
                                    <ul class="dropdown-menu" role="menu">
                                        <li>
                                            <button type="button" class="btn-export-data" data-export-form="formVendorSales" data-export-type="vendor_sales" data-export-file-type="csv" data-section="vn">CSV</button>
                                        </li>
                                        <li>
                                            <button type="button" class="btn-export-data" data-export-form="formVendorSales" data-export-type="vendor_sales" data-export-file-type="xml" data-section="vn">XML</button>
                                        </li>
                                        <li>
                                            <button type="button" class="btn-export-data" data-export-form="formVendorSales" data-export-type="vendor_sales" data-export-file-type="excel" data-section="vn"><?= trans("excel"); ?>&nbsp;(.xlsx)</button>
                                        </li>
                                    </ul>
                                </div> -->
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped" role="grid">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Converted By</th>
                        <th>Grand Total</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th>Payment</th>
                        <th>Fulfillment</th>
                        <th>Date</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><strong><?= esc($order->order_number) ?></strong></td>
                        <td><?= esc($order->customer_name) ?></td>
                        <td><?= esc($order->customer_phone) ?></td>
                        <td><?= esc($order->converted_by ?: '-') ?></td>
                        <td><strong><?= number_format((float)$order->grand_total, 2) ?></strong></td>
                        <td><?= number_format((float)$order->amount_paid, 2) ?></td>
                        <td><?= number_format((float)$order->balance_due, 2) ?></td>
                        <td>
                            <?php if ($order->status == 1): ?>
                                <span class="pos-badge pos-badge-paid">Paid</span>
                            <?php else: ?>
                                <span class="pos-badge pos-badge-partial">Partial</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                            $fStatus = $order->fulfillment_status ?: 'processing'; 
                            $shipping = json_decode($order->shipping_address ?? '{}', true) ?: [];
                            ?>
                            <select class="pos-status-select" 
                                    data-order-id="<?= $order->id ?>" 
                                    data-tracking-code="<?= esc($order->delivery_tracking_code ?? '') ?>"
                                    data-tracking-url="<?= esc($order->delivery_tracking_url ?? '') ?>"
                                    data-delivery-date="<?= esc($order->delivery_date ?? '') ?>"
                                    data-delivery-name="<?= esc($order->delivery_name ?? '') ?>"
                                    data-delivery-phone="<?= esc($order->delivery_phone ?? '') ?>"
                                    data-delivery-amount="<?= esc($order->delivery_amount ?? '') ?>"
                                    data-shipping-line1="<?= esc($shipping['line1'] ?? '') ?>"
                                    data-shipping-city="<?= esc($shipping['city'] ?? '') ?>"
                                    data-shipping-state="<?= esc($shipping['state'] ?? '') ?>"
                                    data-shipping-pincode="<?= esc($shipping['pincode'] ?? '') ?>"
                                    onchange="updateFulfillment(this)">
                                <?php if ($fStatus == 'processing'): ?>
                                    <option value="processing" selected>Processing</option>
                                    <option value="shipped">Shipped</option>
                                <?php elseif ($fStatus == 'shipped'): ?>
                                    <option value="shipped" selected>Shipped</option>
                                    <option value="delivered">Delivered</option>
                                <?php elseif ($fStatus == 'delivered'): ?>
                                    <option value="delivered" selected>Delivered</option>
                                <?php endif; ?>
                            </select>
                        </td>
                        <td><?= date('d M Y', strtotime($order->created_at)) ?></td>
                        <td>
                            <div class="btn-group">
                              <a href="<?= dashboardUrl('pos-edit-order/' . $order->id) ?>" class="btn btn-xs btn-primary" title="Edit Order">
                                  <i class="fa fa-edit"></i> Edit
                              </a>
                              <?php if ($order->status != 1): ?>
                              <a href="<?= dashboardUrl('pos-balance') ?>?id=<?= $order->id ?>" class="btn btn-xs btn-warning" title="Collect Balance Payment">
                                  <i class="fa fa-money"></i> Collect Balance
                              </a>
                              <?php else: ?>
                              <span class="pos-badge pos-badge-paid" style="font-size:.7rem">Fully Paid</span>
                              <?php endif; ?>
                              
                              <?php if ($fStatus != 'processing'): ?>
                               <button type="button" class="btn btn-xs btn-default btn-fulfillment-details" onclick="viewFulfillmentDetails(this)" 
                                       data-order-id="<?= $order->id ?>"
                                       data-fulfillment="<?= esc($fStatus) ?>"
                                       data-tracking-code="<?= esc($order->delivery_tracking_code ?? '') ?>"
                                       data-tracking-url="<?= esc($order->delivery_tracking_url ?? '') ?>"
                                       data-delivery-date="<?= esc($order->delivery_date ?? '') ?>"
                                       data-delivery-name="<?= esc($order->delivery_name ?? '') ?>"
                                       data-delivery-phone="<?= esc($order->delivery_phone ?? '') ?>"
                                       data-delivery-amount="<?= esc($order->delivery_amount ?? '') ?>"
                                       data-shipping-line1="<?= esc($shipping['line1'] ?? '') ?>"
                                       data-shipping-city="<?= esc($shipping['city'] ?? '') ?>"
                                       data-shipping-state="<?= esc($shipping['state'] ?? '') ?>"
                                       data-shipping-pincode="<?= esc($shipping['pincode'] ?? '') ?>">
                                   <i class="fa fa-truck"></i> Details
                               </button>
                               <?php endif; ?>
                               
                              <button type="button" class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown" title="Print Options">
                                  <i class="fa fa-print"></i> <span class="caret"></span>
                              </button>
                              <ul class="dropdown-menu dropdown-menu-right">
                                  <li><a href="<?= dashboardUrl('pos-print') ?>?id=<?= $order->id ?>&type=invoice" target="_blank"><i class="fa fa-file-text-o"></i> &nbsp;Invoice</a></li>
                                  <li><a href="<?= dashboardUrl('pos-print') ?>?id=<?= $order->id ?>&type=receipt" target="_blank"><i class="fa fa-receipt"></i> &nbsp;POS Receipt</a></li>
                                  <li><a href="<?= dashboardUrl('pos-print') ?>?id=<?= $order->id ?>&type=address" target="_blank"><i class="fa fa-map-marker"></i> &nbsp;Delivery Address</a></li>
                              </ul>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (empty($orders)): ?>
            <p class="text-center">No POS orders found. <a href="<?= $newOrderUrl ?>">Create your first order</a>.</p>
        <?php endif; ?>

        <div class="row">
            <div class="col-sm-12">
                <?php if (!empty($orders)): ?>
                    <div class="number-of-entries">
                        <span>Total:</span>&nbsp;&nbsp;<strong><?= $numRows ?></strong>
                    </div>
                <?php endif; ?>
                <div class="table-pagination">
                    <?= $pager->links ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const BASE_URL_PO  = '<?= $baseUrl ?>';
let   csrfHashPO   = '<?= $csrfHash ?>';
const CSRF_NAME_PO = '<?= $csrfName ?>';

/* ── Fulfillment status update ── */
function updateSelectOptions(sel, status) {
    sel.innerHTML = '';
    if (status === 'processing') {
        sel.appendChild(new Option('Processing', 'processing', true, true));
        sel.appendChild(new Option('Shipped', 'shipped'));
    } else if (status === 'shipped') {
        sel.appendChild(new Option('Shipped', 'shipped', true, true));
        sel.appendChild(new Option('Delivered', 'delivered'));
    } else if (status === 'delivered') {
        sel.appendChild(new Option('Delivered', 'delivered', true, true));
    }
}

function updateFulfillment(sel) {
    const status = sel.value;

    // shipped or delivered → open modal first, don't save yet
    if (status === 'shipped' || status === 'delivered') {
        openDeliveryModal(sel.dataset.orderId, {
            tracking_code: sel.dataset.trackingCode  || '',
            tracking_url:  sel.dataset.trackingUrl   || '',
            date:          sel.dataset.deliveryDate  || '',
            name:          sel.dataset.deliveryName  || '',
            phone:         sel.dataset.deliveryPhone || '',
            amount:        sel.dataset.deliveryAmount|| '',
            shipping_line1: sel.dataset.shippingLine1 || '',
            shipping_city:  sel.dataset.shippingCity  || '',
            shipping_state: sel.dataset.shippingState || '',
            shipping_pincode: sel.dataset.shippingPincode || '',
        }, sel);
        return;
    }

    // processing → save immediately, no modal
    saveFulfillmentStatus(sel, status);
}

function saveFulfillmentStatus(sel, status) {
    const orderId = sel.dataset.orderId;
    fetch(BASE_URL_PO + 'Dashboard/posUpdateFulfillmentStatus', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfHashPO },
        body: JSON.stringify({ order_id: orderId, fulfillment_status: status })
    })
    .then(r => r.json())
    .then(res => {
        if (res[CSRF_NAME_PO]) csrfHashPO = res[CSRF_NAME_PO];
        if (res.status != 1) {
            alert('Failed to update status');
            sel.value = sel.dataset.prev || 'processing';
        } else {
            sel.dataset.prev = status;
            updateSelectOptions(sel, status);
        }
    })
    .catch(() => alert('Network error'));
}
document.querySelectorAll('.pos-status-select').forEach(s => { 
    s.dataset.prev = s.value; 
    updateSelectOptions(s, s.value);
});

/* ── Delivery modal (shared for shipped + delivered) ── */
let _currentSel = null;

function openDeliveryModal(orderId, data, sel) {
    _currentSel = sel || null;
    const status = sel ? sel.value : (data.status || '');
    document.getElementById('dm_order_id').value      = orderId;
    document.getElementById('dm_fulfillment').value   = status;
    document.getElementById('dm_tracking_code').value = data.tracking_code || '';
    document.getElementById('dm_tracking_url').value  = data.tracking_url  || '';
    document.getElementById('dm_date').value           = data.date          || '';
    document.getElementById('dm_name').value           = data.name          || '';
    document.getElementById('dm_phone').value          = data.phone         || '';
    document.getElementById('dm_amount').value         = data.amount        || '';
    document.getElementById('dm_shipping_line1').value = data.shipping_line1 || '';
    document.getElementById('dm_shipping_city').value  = data.shipping_city  || '';
    document.getElementById('dm_shipping_state').value = data.shipping_state || '';
    document.getElementById('dm_shipping_pincode').value = data.shipping_pincode || '';

    // Show tracking fields only for 'shipped', hide for 'delivered'
    const showTracking = (status === 'shipped');
    document.getElementById('dm_tracking_row').style.display    = showTracking ? '' : 'none';
    document.getElementById('dm_tracking_url_row').style.display = showTracking ? '' : 'none';
    document.getElementById('dm_amount_row').style.display = showTracking ? '' : 'none';

    document.getElementById('deliveryModalOverlay').classList.add('active');
}

function closeDeliveryModal() {
    // Revert dropdown if user cancels
    if (_currentSel) {
        _currentSel.value = _currentSel.dataset.prev || 'processing';
        _currentSel = null;
    }
    document.getElementById('deliveryModalOverlay').classList.remove('active');
}

function saveDeliveryDetails() {
    const orderId           = document.getElementById('dm_order_id').value;
    const fulfillmentStatus = document.getElementById('dm_fulfillment').value;
    const name  = document.getElementById('dm_name').value.trim();
    const phone = document.getElementById('dm_phone').value.trim();

    // Reset all input borders
    const inputIds = [
        'dm_tracking_code', 'dm_tracking_url', 'dm_date', 'dm_name', 'dm_phone', 'dm_amount',
        'dm_shipping_line1', 'dm_shipping_city', 'dm_shipping_state', 'dm_shipping_pincode'
    ];
    inputIds.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.border = '1px solid #ccd0d5';
    });

    let ok = true;
    let firstErrorField = null;

    inputIds.forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        
        // Check if the element is visible in the modal
        const isVisible = el.offsetParent !== null;
        if (!isVisible) return;

        const val = el.value.trim();
        if (!val) {
            el.style.border = '1px solid #dd4b39';
            if (!firstErrorField) firstErrorField = el;
            ok = false;
        } else if (id === 'dm_amount') {
            const amt = parseFloat(val);
            if (isNaN(amt) || amt < 0) {
                el.style.border = '1px solid #dd4b39';
                if (!firstErrorField) firstErrorField = el;
                ok = false;
            }
        }
    });

    if (!ok) {
        if (firstErrorField) firstErrorField.focus();
        return;
    }
    const payload = {
        order_id:           orderId,
        fulfillment_status: fulfillmentStatus,
        tracking_code:      document.getElementById('dm_tracking_code').value.trim(),
        tracking_url:       document.getElementById('dm_tracking_url').value.trim(),
        date:               document.getElementById('dm_date').value,
        name:               document.getElementById('dm_name').value.trim(),
        phone:              document.getElementById('dm_phone').value.trim(),
        amount:             parseFloat(document.getElementById('dm_amount').value) || 0,
        shipping_line1:     document.getElementById('dm_shipping_line1').value.trim(),
        shipping_city:      document.getElementById('dm_shipping_city').value.trim(),
        shipping_state:     document.getElementById('dm_shipping_state').value.trim(),
        shipping_pincode:   document.getElementById('dm_shipping_pincode').value.trim(),
    };
    fetch(BASE_URL_PO + 'Dashboard/posUpdateDeliveryDetails', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfHashPO },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(res => {
        if (res[CSRF_NAME_PO]) csrfHashPO = res[CSRF_NAME_PO];
        if (res.status == 1) {
            // Update attributes on both the select dropdown and the details button
            const selEl = document.querySelector(`.pos-status-select[data-order-id="${orderId}"]`);
            if (selEl) {
                selEl.dataset.prev = fulfillmentStatus;
                selEl.dataset.trackingCode   = payload.tracking_code;
                selEl.dataset.trackingUrl    = payload.tracking_url;
                selEl.dataset.deliveryDate   = payload.date;
                selEl.dataset.deliveryName   = payload.name;
                selEl.dataset.deliveryPhone  = payload.phone;
                selEl.dataset.deliveryAmount = payload.amount;
                selEl.dataset.shippingLine1  = payload.shipping_line1;
                selEl.dataset.shippingCity   = payload.shipping_city;
                selEl.dataset.shippingState  = payload.shipping_state;
                selEl.dataset.shippingPincode = payload.shipping_pincode;
                updateSelectOptions(selEl, fulfillmentStatus);
            }
            const btnEl = document.querySelector(`.btn-fulfillment-details[data-order-id="${orderId}"]`);
            if (btnEl) {
                btnEl.dataset.fulfillment = fulfillmentStatus;
                btnEl.dataset.trackingCode   = payload.tracking_code;
                btnEl.dataset.trackingUrl    = payload.tracking_url;
                btnEl.dataset.deliveryDate   = payload.date;
                btnEl.dataset.deliveryName   = payload.name;
                btnEl.dataset.deliveryPhone  = payload.phone;
                btnEl.dataset.deliveryAmount = payload.amount;
                btnEl.dataset.shippingLine1  = payload.shipping_line1;
                btnEl.dataset.shippingCity   = payload.shipping_city;
                btnEl.dataset.shippingState  = payload.shipping_state;
                btnEl.dataset.shippingPincode = payload.shipping_pincode;
            } else if (fulfillmentStatus !== 'processing') {
                window.location.reload();
                return;
            }
            _currentSel = null;
            document.getElementById('deliveryModalOverlay').classList.remove('active');
        } else {
            alert('Error: ' + (res.msg || 'Could not save'));
        }
    })
    .catch(() => alert('Network error'));
}

function saveShippedDetails() { saveDeliveryDetails(); }

function viewFulfillmentDetails(btn) {
    _currentSel = null; // Don't revert dropdown on cancel since we didn't change dropdown
    openDeliveryModal(btn.dataset.orderId, {
        status:        btn.dataset.fulfillment   || '',
        tracking_code: btn.dataset.trackingCode  || '',
        tracking_url:  btn.dataset.trackingUrl   || '',
        date:          btn.dataset.deliveryDate  || '',
        name:          btn.dataset.deliveryName  || '',
        phone:         btn.dataset.deliveryPhone || '',
        amount:        btn.dataset.deliveryAmount|| '',
        shipping_line1: btn.dataset.shippingLine1 || '',
        shipping_city:  btn.dataset.shippingCity  || '',
        shipping_state: btn.dataset.shippingState || '',
        shipping_pincode: btn.dataset.shippingPincode || '',
    }, null);
}

// Close on overlay click
document.getElementById('deliveryModalOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeDeliveryModal();
});
</script>
