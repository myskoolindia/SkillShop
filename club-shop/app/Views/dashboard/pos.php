<?php
$sym      = !empty($currency) ? esc($currency->symbol) : '₹';
$csrfName = csrf_token();
$csrfHash = csrf_hash();
$baseUrl  = rtrim(base_url(), '/') . '/';
$posListUrl = dashboardUrl('pos-sales');
?>
<style>
/* ── POS layout ─────────────────────────────────────── */
.pos-card{background:#fff;border-radius:6px;box-shadow:0 1px 6px rgba(0,0,0,.1);margin-bottom:18px}
.pos-card-header{padding:12px 16px;border-bottom:1px solid #f0f0f0;font-weight:700;font-size:14px;color:#333}
.pos-card-body{padding:14px 16px}
.pos-label{display:block;font-size:13px;font-weight:600;color:#555;margin-bottom:4px}
.pos-input{width:100%;padding:6px 10px;border:1px solid #ccd0d5;border-radius:4px;font-size:14px;height:34px;box-sizing:border-box}
.pos-input:focus{border-color:#3c8dbc;outline:none;box-shadow:0 0 0 2px rgba(60,141,188,.2)}
.pos-input.is-invalid{border-color:#dd4b39!important}
.pos-error{color:#dd4b39;font-size:12px;margin-top:2px;display:none}
.pos-select{width:100%;padding:6px 10px;border:1px solid #ccd0d5;border-radius:4px;font-size:14px;height:34px;box-sizing:border-box;background:#fff}
.pos-select:focus{border-color:#3c8dbc;outline:none}
.pos-row{display:flex;flex-wrap:wrap;margin:0 -6px}
.pos-col{padding:0 6px;box-sizing:border-box}
.pos-col-3{width:25%}.pos-col-4{width:33.333%}.pos-col-6{width:50%}.pos-col-12{width:100%}
@media(max-width:768px){.pos-col-3,.pos-col-4,.pos-col-6{width:100%}}
.pos-field{margin-bottom:12px}
/* summary */
.pos-summary{background:#f8fafc;border-radius:6px;padding:14px}
.pos-sum-row{display:flex;justify-content:space-between;padding:6px 0;font-size:14px;border-bottom:1px solid #eef0f3}
.pos-sum-row:last-child{border-bottom:none}
.pos-sum-row.grand{font-weight:700;font-size:15px;color:#222;border-top:2px dashed #c8d0da;border-bottom:none;padding-top:8px;margin-top:4px}
/* items table */
.pos-table{width:100%;border-collapse:collapse;font-size:14px}
.pos-table th{background:#f5f7fa;padding:8px 7px;text-align:left;border:1px solid #e0e4ea;white-space:nowrap;font-weight:600;color:#444}
.pos-table td{padding:5px 4px;border:1px solid #e8eaed;vertical-align:middle}
.pos-table input,.pos-table select{width:100%;padding:4px 6px;border:1px solid #ccd0d5;border-radius:3px;font-size:14px;height:32px;box-sizing:border-box}
.pos-table .td-calc{text-align:right;font-weight:600;white-space:nowrap;padding-right:8px;font-size:14px}
.product-cell{position:relative;min-width:160px}
/* floating dropdown rendered via JS, no .dropdown-box needed */
/* receipt */
.receipt-paper{width:300px;margin:0 auto;padding:12px;background:#fff;color:#000;font-family:"Courier New",monospace;font-size:11px;line-height:1.4}
.receipt-paper p,.receipt-paper h5{margin:0;padding:0}
.receipt-center{text-align:center}
.receipt-line{border-top:1px dashed #000;margin:7px 0}
.receipt-table{width:100%;border-collapse:collapse}
.receipt-table th,.receipt-table td{padding:2px 0;vertical-align:top}
.receipt-table th{text-align:left;border-bottom:1px dashed #000}
.receipt-table td:last-child,.receipt-table th:last-child{text-align:right}
#receiptWrapper{display:none}
/* sticky sidebar */
.pos-sticky{position:sticky;top:10px}
/* toast */
.pos-toast{position:fixed;top:18px;right:18px;z-index:9999;min-width:260px;max-width:340px;padding:12px 16px;border-radius:5px;font-size:14px;color:#fff;box-shadow:0 4px 14px rgba(0,0,0,.18);display:none;animation:fadeIn .25s}
.pos-toast.success{background:#00a65a}.pos-toast.error{background:#dd4b39}.pos-toast.info{background:#3c8dbc}
@keyframes fadeIn{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:translateY(0)}}
@media print{body *{visibility:hidden!important}#receiptWrapper,#receiptWrapper *{visibility:visible!important}#receiptWrapper{display:block!important;position:fixed;inset:0;background:#fff;z-index:99999}}
</style>

<!-- Toast notification -->
<div id="posToast" class="pos-toast"></div>

<div style="padding:0 4px">

  <!-- Page header -->
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:8px">
    <h4 style="margin:0;font-size:1.1rem;font-weight:700;color:#333"><i class="fa fa-cash-register" style="color:#3c8dbc"></i> &nbsp;New POS Order</h4>
    <div>
      <a href="<?= $posListUrl ?>" class="btn btn-sm btn-default"><i class="fa fa-list"></i> POS Orders</a>
      <button class="btn btn-sm btn-default" type="button" onclick="printReceipt()"><i class="fa fa-print"></i> Print Receipt</button>
    </div>
  </div>

  <div class="row">
    <!-- LEFT COLUMN -->
    <div class="col-lg-8 col-md-12">
      <div class="pos-card">
        <div class="pos-card-header"><i class="fa fa-user" style="color:#3c8dbc"></i> &nbsp;Customer Details</div>
        <div class="pos-card-body">
          <div class="pos-row">
            <div class="pos-col pos-col-4 pos-field">
              <label class="pos-label">Customer Name <span style="color:#dd4b39">*</span></label>
              <input class="pos-input" id="customer_name" placeholder="Full Name">
              <span class="pos-error" id="err_customer_name">Customer name is required</span>
            </div>
            <div class="pos-col pos-col-4 pos-field">
              <label class="pos-label">Mobile <span style="color:#dd4b39">*</span></label>
              <input class="pos-input" id="customer_phone" placeholder="Mobile Number" maxlength="15">
              <span class="pos-error" id="err_customer_phone">Valid mobile number required</span>
            </div>
            <div class="pos-col pos-col-4 pos-field">
              <label class="pos-label">Email</label>
              <input class="pos-input" id="customer_email" placeholder="Email Address" type="email">
              <span class="pos-error" id="err_customer_email">Enter a valid email</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Billing Address -->
      <div class="pos-card">
        <div class="pos-card-header"><i class="fa fa-map-marker" style="color:#3c8dbc"></i> &nbsp;Billing Address</div>
        <div class="pos-card-body">
          <div class="pos-row">
            <div class="pos-col pos-col-12 pos-field"><label class="pos-label">Address</label><input class="pos-input" id="billing_address_line1" placeholder="Street / Area"></div>
            <div class="pos-col pos-col-4 pos-field"><label class="pos-label">City</label><input class="pos-input" id="billing_city" placeholder="City"></div>
            <div class="pos-col pos-col-4 pos-field"><label class="pos-label">State</label><input class="pos-input" id="billing_state" placeholder="State"></div>
            <div class="pos-col pos-col-4 pos-field"><label class="pos-label">Pincode</label><input class="pos-input" id="billing_pincode" placeholder="Pincode" maxlength="10"></div>
          </div>
        </div>
      </div>

      <!-- Shipping Address -->
      <div class="pos-card">
        <div class="pos-card-header" style="display:flex;justify-content:space-between;align-items:center">
          <span><i class="fa fa-truck" style="color:#3c8dbc"></i> &nbsp;Shipping Address</span>
          <label style="font-weight:400;font-size:.82rem;margin:0;cursor:pointer">
            <input type="checkbox" id="sameAddress" checked onchange="toggleShipping()" style="margin-right:4px">Same as billing
          </label>
        </div>
        <div class="pos-card-body" id="shippingSection" style="display:none">
          <div class="pos-row">
            <div class="pos-col pos-col-12 pos-field"><label class="pos-label">Address</label><input class="pos-input" id="shipping_address_line1" placeholder="Street / Area"></div>
            <div class="pos-col pos-col-4 pos-field"><label class="pos-label">City</label><input class="pos-input" id="shipping_city" placeholder="City"></div>
            <div class="pos-col pos-col-4 pos-field"><label class="pos-label">State</label><input class="pos-input" id="shipping_state" placeholder="State"></div>
            <div class="pos-col pos-col-4 pos-field"><label class="pos-label">Pincode</label><input class="pos-input" id="shipping_pincode" placeholder="Pincode" maxlength="10"></div>
          </div>
        </div>
      </div>

      <!-- Items -->
      <div class="pos-card">
        <div class="pos-card-header" style="display:flex;justify-content:space-between;align-items:center">
          <span><i class="fa fa-list" style="color:#3c8dbc"></i> &nbsp;Items <span style="color:#dd4b39">*</span></span>
          <button class="btn btn-xs btn-primary" type="button" onclick="addRow()"><i class="fa fa-plus"></i> Add Item</button>
        </div>
        <div class="pos-card-body" style="padding:10px 12px">
          <div style="overflow-x:auto">
            <table class="pos-table" id="itemsTable">
              <thead>
                <tr>
                  <th style="min-width:160px">Product <span style="color:#dd4b39">*</span></th>
                  <th style="min-width:80px">Price <span style="color:#dd4b39">*</span></th>
                  <th style="min-width:60px">Qty <span style="color:#dd4b39">*</span></th>
                  <th style="min-width:80px">Discount (<?= $sym ?>)</th>
                  <th style="min-width:70px">GST %</th>
                  <th style="min-width:70px;text-align:right">GST Amt</th>
                  <th style="min-width:80px;text-align:right">Total</th>
                  <th style="width:36px"></th>
                </tr>
              </thead>
              <tbody id="items"></tbody>
            </table>
          </div>
          <span class="pos-error" id="err_items" style="display:none">Add at least one item with product name and price</span>
        </div>
      </div>

      <!-- Payment Details -->
      <div class="pos-card">
        <div class="pos-card-header"><i class="fa fa-credit-card" style="color:#3c8dbc"></i> &nbsp;Payment Details</div>
        <div class="pos-card-body">
          <div class="pos-row">
            <div class="pos-col pos-col-3 pos-field">
              <label class="pos-label">Payment Date <span style="color:#dd4b39">*</span></label>
              <input type="date" class="pos-input" id="payment_date">
              <span class="pos-error" id="err_payment_date">Payment date required</span>
            </div>
            <div class="pos-col pos-col-3 pos-field">
              <label class="pos-label">Amount Paid</label>
              <input type="number" class="pos-input" id="amount_paid" value="0" min="0" step="0.01" oninput="calculate();calcChange();">
            </div>
            <div class="pos-col pos-col-3 pos-field">
              <label class="pos-label">Payment Method</label>
              <select class="pos-select" id="payment_method" onchange="handlePaymentUI()">
                <option value="UPI">UPI</option><option value="Card">Card</option><option value="Cash">Cash</option><option value="Bank Transfer">Bank Transfer</option><option value="Cheque">Cheque</option>
              </select>
            </div>
          </div>
          <!-- UPI -->
          <div id="upiSection" class="pos-row">
            <div class="pos-col pos-col-4 pos-field"><label class="pos-label">UPI ID</label><input class="pos-input" id="upi_id" placeholder="name@upi"></div>
            <div class="pos-col pos-col-4 pos-field"><label class="pos-label">Transaction Reference <span style="color:#dd4b39">*</span></label><input class="pos-input" id="upi_ref" placeholder="UPI Ref No"><span class="pos-error" id="err_upi_ref">UPI reference required</span></div>
          </div>
          <!-- Card -->
          <div id="cardSection" class="pos-row" style="display:none">
            <div class="pos-col pos-col-4 pos-field"><label class="pos-label">Card Last 4 <span style="color:#dd4b39">*</span></label><input class="pos-input" id="card_last4" placeholder="1234" maxlength="4"><span class="pos-error" id="err_card_last4">Enter last 4 digits</span></div>
            <div class="pos-col pos-col-4 pos-field"><label class="pos-label">Approval / Reference</label><input class="pos-input" id="card_ref" placeholder="Approval / Ref No"></div>
          </div>
          <!-- Cash -->
          <div id="cashSection" class="pos-row" style="display:none">
            <div class="pos-col pos-col-4 pos-field"><label class="pos-label">Cash Received <span style="color:#dd4b39">*</span></label><input class="pos-input" id="cash_received" type="number" placeholder="0.00" min="0" step="0.01" oninput="calcChange()"><span class="pos-error" id="err_cash_received">Cash received required</span></div>
            <div class="pos-col pos-col-4 pos-field"><label class="pos-label">Change</label><input class="pos-input" id="change_amount" readonly value="0.00" style="background:#f5f5f5"></div>
          </div>
          <!-- Bank -->
          <div id="bankSection" class="pos-row" style="display:none">
            <div class="pos-col pos-col-4 pos-field"><label class="pos-label">Bank Reference / UTR <span style="color:#dd4b39">*</span></label><input class="pos-input" id="bank_ref" placeholder="UTR / Bank Ref"><span class="pos-error" id="err_bank_ref">Bank reference required</span></div>
          </div>
          <!-- Cheque -->
          <div id="chequeSection" class="pos-row" style="display:none">
            <div class="pos-col pos-col-3 pos-field"><label class="pos-label">Bank Name <span style="color:#dd4b39">*</span></label><input class="pos-input" id="cheque_bank_name" placeholder="Bank Name"><span class="pos-error" id="err_cheque_bank_name">Bank name required</span></div>
            <div class="pos-col pos-col-3 pos-field"><label class="pos-label">Cheque No <span style="color:#dd4b39">*</span></label><input class="pos-input" id="cheque_no" placeholder="Cheque Number"><span class="pos-error" id="err_cheque_no">Cheque number required</span></div>
            <div class="pos-col pos-col-3 pos-field"><label class="pos-label">Cheque Date <span style="color:#dd4b39">*</span></label><input type="date" class="pos-input" id="cheque_date"><span class="pos-error" id="err_cheque_date">Cheque date required</span></div>
            <div class="pos-col pos-col-3 pos-field"><label class="pos-label">Cheque Amount <span style="color:#dd4b39">*</span></label><input type="number" class="pos-input" id="cheque_amount" min="0" step="0.01" placeholder="0.00"><span class="pos-error" id="err_cheque_amount">Cheque amount required</span></div>
          </div>
          <div style="margin-top:10px;display:flex;gap:8px;flex-wrap:wrap">
            <button class="btn btn-sm btn-success" type="button" onclick="saveOrder(event)"><i class="fa fa-save"></i> Save Order</button>
            <button class="btn btn-sm btn-default" type="button" onclick="printReceipt()"><i class="fa fa-print"></i> Print Receipt</button>
          </div>
        </div>
      </div>

    </div><!-- /col-lg-8 -->

    <!-- RIGHT COLUMN: Summary -->
    <div class="col-lg-4 col-md-12">
      <div class="pos-sticky">
        <div class="pos-card">
          <div class="pos-card-header"><i class="fa fa-calculator" style="color:#3c8dbc"></i> &nbsp;Order Summary</div>
          <div class="pos-card-body">
            <div class="pos-summary">
              <div class="pos-sum-row"><span>Subtotal</span><span><?= $sym ?> <span id="subtotal">0.00</span></span></div>
              <div class="pos-sum-row"><span>Total GST</span><span><?= $sym ?> <span id="total_tax">0.00</span></span></div>
              <div class="pos-sum-row" style="color:#00a65a"><span>Discount</span><span>- <?= $sym ?> <span id="total_discount_preview">0.00</span></span></div>
              <div class="pos-sum-row" style="align-items:center">
                <span>Shipping</span>
                <span style="display:flex;align-items:center;gap:4px">
                  <?= $sym ?>
                  <input type="number" id="shipping_charges" value="0" min="0" step="0.01"
                    style="width:80px;padding:3px 6px;border:1px solid #ccd0d5;border-radius:3px;font-size:14px;text-align:right"
                    oninput="calculate()">
                </span>
              </div>
              <div class="pos-sum-row grand"><span>Grand Total</span><span><?= $sym ?> <span id="grand_total">0.00</span></span></div>
              <div class="pos-sum-row"><span>Amount Paid</span><span><?= $sym ?> <span id="paid_preview">0.00</span></span></div>
              <div class="pos-sum-row" style="color:#dd4b39;font-weight:600"><span>Balance Due</span><span><?= $sym ?> <span id="balance_due">0.00</span></span></div>
            </div>
            <p style="font-size:.75rem;color:#999;margin-top:10px;margin-bottom:0">Save order to persist. Print receipt after saving.</p>
          </div>
        </div>
      </div>
    </div>

  </div><!-- /row -->
</div>

<!-- Receipt (print only) -->
<div id="receiptWrapper">
  <div class="receipt-paper" id="receiptContent"></div>
</div>

<script>
/* ── Config ─────────────────────────────────────────── */
const BASE_URL  = '<?= $baseUrl ?>';
const CSRF_NAME = '<?= $csrfName ?>';
let   csrfHash  = '<?= $csrfHash ?>';
const SYM       = '<?= $sym ?>';

/* ── State ──────────────────────────────────────────── */
let lastSavedOrder = null;

/* ── DOM refs ───────────────────────────────────────── */
const subtotalEl    = document.getElementById('subtotal');
const totalTaxEl    = document.getElementById('total_tax');
const grandTotalEl  = document.getElementById('grand_total');
const amountPaidEl  = document.getElementById('amount_paid');
const balanceDueEl  = document.getElementById('balance_due');
const paidPreviewEl = document.getElementById('paid_preview');

/* ── Toast ──────────────────────────────────────────── */
function toast(msg, type = 'success') {
  const el = document.getElementById('posToast');
  el.textContent = msg;
  el.className = 'pos-toast ' + type;
  el.style.display = 'block';
  clearTimeout(el._t);
  el._t = setTimeout(() => { el.style.display = 'none'; }, 3800);
}

/* ── AJAX helper ────────────────────────────────────── */
function postJson(url, data) {
  return fetch(BASE_URL + url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': csrfHash
    },
    body: JSON.stringify(data)
  })
  .then(r => r.json())
  .then(res => { if (res[CSRF_NAME]) csrfHash = res[CSRF_NAME]; return res; })
  .catch(() => { toast('Network error. Please try again.', 'error'); return { status: 0 }; });
}

/* ── Validation helpers ─────────────────────────────── */
function showErr(id, msg) {
  const el = document.getElementById(id);
  if (!el) return;
  if (msg) el.textContent = msg;
  el.style.display = 'block';
  const inp = el.previousElementSibling;
  if (inp && inp.classList) inp.classList.add('is-invalid');
}
function clearErr(id) {
  const el = document.getElementById(id);
  if (!el) return;
  el.style.display = 'none';
  const inp = el.previousElementSibling;
  if (inp && inp.classList) inp.classList.remove('is-invalid');
}
function clearAllErrors() {
  document.querySelectorAll('.pos-error').forEach(e => { e.style.display = 'none'; });
  document.querySelectorAll('.is-invalid').forEach(e => e.classList.remove('is-invalid'));
}

/* ── Dates ──────────────────────────────────────────── */
function setTodayDates() {
  const today = new Date().toISOString().split('T')[0];
  document.getElementById('payment_date').value = today;
}

/* ── Items table ────────────────────────────────────── */
function addRow(item = null) {
  const row = document.createElement('tr');
  row.innerHTML = `
    <td class="product-cell">
      <input class="product_name" onkeyup="searchProduct(this)" onfocus="searchProduct(this)" placeholder="Search or type product" style="width:100%;padding:4px 6px;border:1px solid #ccd0d5;border-radius:3px;font-size:14px;height:32px;box-sizing:border-box">
    </td>
    <td><input type="number" class="unit_price" oninput="calculate()" value="${item ? item.unit_price : ''}" min="0" step="0.01" placeholder="0.00"></td>
    <td><input type="number" class="quantity" oninput="calculate()" value="${item ? item.quantity : 1}" min="1" step="1"></td>
    <td><input type="number" class="discount_price" oninput="calculate()" value="${item ? (item.discount_price || 0) : 0}" min="0" step="0.01" placeholder="0.00"></td>
    <td>
      <select class="tax_percent" onchange="calculate()">
        <option value="0">0%</option><option value="5">5%</option><option value="12">12%</option><option value="18">18%</option><option value="28">28%</option>
      </select>
    </td>
    <td class="td-calc tax_amount">0.00</td>
    <td class="td-calc line_total">0.00</td>
    <td style="text-align:center"><button class="btn btn-xs btn-danger" type="button" onclick="removeRow(this)" title="Remove"><i class="fa fa-times"></i></button></td>
  `;
  document.getElementById('items').appendChild(row);
  if (item) {
    row.querySelector('.product_name').value     = item.product_name || '';
    row.querySelector('.unit_price').value       = item.unit_price   || '';
    row.querySelector('.quantity').value         = item.quantity     || 1;
    row.querySelector('.discount_price').value = item.discount_price || 0;
    row.querySelector('.tax_percent').value      = item.tax_percent  ?? 18;
  } else {
    row.querySelector('.tax_percent').value = 0;
  }
  calculate();
}

function removeRow(btn) {
  const rows = document.querySelectorAll('#items tr');
  if (rows.length <= 1) { toast('At least one item is required.', 'error'); return; }
  btn.closest('tr').remove();
  calculate();
}

/* ── Floating product search dropdown ───────────────── */
// Single shared dropdown appended to body to escape table overflow clipping
const _dd = document.createElement('div');
_dd.id = 'posProductDd';
_dd.style.cssText = 'position:fixed;z-index:99999;background:#fff;border:1px solid #ccd0d5;border-radius:0 0 4px 4px;box-shadow:0 4px 12px rgba(0,0,0,.15);display:none;';
document.body.appendChild(_dd);

let _ddInput = null; // the input currently owning the dropdown

function positionDd(input) {
  const r = input.getBoundingClientRect();
  _dd.style.top    = (r.bottom) + 'px';
  _dd.style.left   = r.left + 'px';
  // _dd.style.width  = Math.max(r.width, 280) + 'px';
}

function searchProduct(input) {
  const val = input.value.trim();
  _ddInput = input;
  if (val.length < 2) { _dd.style.display = 'none'; return; }
  postJson('Dashboard/posProductSearch', { query: val }).then(res => {
    if (_ddInput !== input) return; // stale response
    if (!res.products || !res.products.length) {
      _dd.innerHTML = '<div style="padding:8px 12px;color:#999;font-size:14px;">No products found</div>';
    } else {
      _dd.innerHTML = res.products.map(p =>
        `<div class="dd-item" style="padding:8px 12px;cursor:pointer;font-size:14px;border-bottom:1px solid #f0f0f0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"
          onmousedown="pickProduct(event,'${escQ(p.name)}',${p.price})">`
        + `<strong>${htmlEsc(p.name)}</strong>`
        + (p.sku ? ` <small style="color:#999">[${htmlEsc(p.sku)}]</small>` : '')
        + ` &mdash; <span style="color:#3c8dbc">${SYM}${p.price}</span>`
        + `</div>`
      ).join('');
    }
    positionDd(input);
    _dd.style.display = 'block';
  });
}

function pickProduct(e, name, price) {
  e.preventDefault(); // prevent blur before we fill the value
  if (_ddInput) {
    _ddInput.value = name;
    // find unit_price in same row
    const row = _ddInput.closest('tr');
    if (row) row.querySelector('.unit_price').value = price;
    calculate();
  }
  _dd.style.display = 'none';
  _ddInput = null;
}

// Reposition on scroll/resize
window.addEventListener('scroll', () => { if (_ddInput) positionDd(_ddInput); }, true);
window.addEventListener('resize', () => { if (_ddInput) positionDd(_ddInput); });

function escQ(s) { return s.replace(/\\/g,'\\\\').replace(/'/g,"\\'"); }
function htmlEsc(s) { const d=document.createElement('div'); d.textContent=s; return d.innerHTML; }

/* Close dropdown on outside click */
document.addEventListener('click', e => {
  if (e.target !== _ddInput) {
    _dd.style.display = 'none';
    _ddInput = null;
  }
});

/* ── Calculations ───────────────────────────────────── */
function calculate() {
  let subtotal = 0, totalTax = 0, totalDiscount = 0;
  document.querySelectorAll('#items tr').forEach(row => {
    const price    = parseFloat(row.querySelector('.unit_price').value)    || 0;
    const qty      = parseFloat(row.querySelector('.quantity').value)      || 0;
    const discAmt  = parseFloat(row.querySelector('.discount_price').value)|| 0;
    const tax      = parseFloat(row.querySelector('.tax_percent').value)   || 0;
    const base     = price * qty;
    const net      = Math.max(0, base - discAmt);
    const taxAmt   = net * tax / 100;
    row.querySelector('.tax_amount').textContent = taxAmt.toFixed(2);
    row.querySelector('.line_total').textContent = (net + taxAmt).toFixed(2);
    subtotal      += net;
    totalTax      += taxAmt;
    totalDiscount += discAmt;
  });
  const shipping = parseFloat(document.getElementById('shipping_charges').value) || 0;
  const grand    = subtotal + totalTax + shipping;
  const paid     = parseFloat(amountPaidEl.value) || 0;
  subtotalEl.textContent    = subtotal.toFixed(2);
  totalTaxEl.textContent    = totalTax.toFixed(2);
  grandTotalEl.textContent  = grand.toFixed(2);
  paidPreviewEl.textContent = paid.toFixed(2);
  balanceDueEl.textContent  = Math.max(0, grand - paid).toFixed(2);
  // Update discount display in summary
  const discEl = document.getElementById('total_discount_preview');
  if (discEl) discEl.textContent = totalDiscount.toFixed(2);
}

/* ── Payment method UI ──────────────────────────────── */
function handlePaymentUI() {
  const m = document.getElementById('payment_method').value;
  ['upiSection','cardSection','cashSection','bankSection','chequeSection'].forEach(id => document.getElementById(id).style.display = 'none');
  if (m === 'UPI')           document.getElementById('upiSection').style.display    = '';
  if (m === 'Card')          document.getElementById('cardSection').style.display   = '';
  if (m === 'Cash')          document.getElementById('cashSection').style.display   = '';
  if (m === 'Bank Transfer') document.getElementById('bankSection').style.display   = '';
  if (m === 'Cheque')        document.getElementById('chequeSection').style.display = '';
}

function calcChange() {
  if (document.getElementById('payment_method').value !== 'Cash') return;
  const rec  = parseFloat(document.getElementById('cash_received').value) || 0;
  const paid = parseFloat(amountPaidEl.value) || 0;
  document.getElementById('change_amount').value = Math.max(rec - paid, 0).toFixed(2);
}

/* ── Shipping toggle ────────────────────────────────── */
function toggleShipping() {
  const same = document.getElementById('sameAddress').checked;
  document.getElementById('shippingSection').style.display = same ? 'none' : 'block';
  if (same) syncShipping();
}
function syncShipping() {
  ['address_line1','city','state','pincode'].forEach(f => {
    const s = document.getElementById('shipping_' + f);
    const b = document.getElementById('billing_' + f);
    if (s && b) s.value = b.value;
  });
}
['billing_address_line1','billing_city','billing_state','billing_pincode'].forEach(id => {
  document.addEventListener('input', e => {
    if (e.target.id === id && document.getElementById('sameAddress').checked) syncShipping();
  });
});

/* ── Validate new order ─────────────────────────────── */
function validateOrder() {
  clearAllErrors();
  let ok = true;

  // Customer name
  if (!document.getElementById('customer_name').value.trim()) {
    showErr('err_customer_name'); ok = false;
  }
  // Phone
  const phone = document.getElementById('customer_phone').value.trim();
  if (!phone || !/^\+?[\d\s\-]{7,15}$/.test(phone)) {
    showErr('err_customer_phone', phone ? 'Enter a valid mobile number (7–15 digits)' : 'Mobile number is required'); ok = false;
  }
  // Email (optional but validate format if filled)
  const email = document.getElementById('customer_email').value.trim();
  if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    showErr('err_customer_email', 'Enter a valid email address'); ok = false;
  }
  // Items
  const items = getItemsData();
  const validItems = items.filter(i => i.product_name.trim() && i.unit_price > 0 && i.quantity > 0);
  if (!validItems.length) {
    document.getElementById('err_items').style.display = 'block'; ok = false;
  }
  // Payment date
  if (!document.getElementById('payment_date').value) {
    showErr('err_payment_date'); ok = false;
  }
  // Payment method specific
  const m = document.getElementById('payment_method').value;
  if (m === 'UPI' && !document.getElementById('upi_ref').value.trim()) {
    showErr('err_upi_ref'); ok = false;
  }
  if (m === 'Card') {
    const last4 = document.getElementById('card_last4').value.trim();
    if (!last4 || !/^\d{4}$/.test(last4)) { showErr('err_card_last4', 'Enter exactly 4 digits'); ok = false; }
  }
  if (m === 'Cash') {
    const rec = parseFloat(document.getElementById('cash_received').value) || 0;
    const paid = parseFloat(amountPaidEl.value) || 0;
    if (rec < paid) { showErr('err_cash_received', 'Cash received cannot be less than amount paid'); ok = false; }
  }
  if (m === 'Bank Transfer' && !document.getElementById('bank_ref').value.trim()) {
    showErr('err_bank_ref'); ok = false;
  }
  if (m === 'Cheque') {
    if (!document.getElementById('cheque_bank_name').value.trim()) { showErr('err_cheque_bank_name'); ok = false; }
    if (!document.getElementById('cheque_no').value.trim())        { showErr('err_cheque_no');        ok = false; }
    if (!document.getElementById('cheque_date').value)             { showErr('err_cheque_date');      ok = false; }
    const cAmt = parseFloat(document.getElementById('cheque_amount').value) || 0;
    if (cAmt <= 0) { showErr('err_cheque_amount', 'Enter a valid cheque amount'); ok = false; }
    else {
      const balanceDue = parseFloat(balanceDueEl.textContent) || 0;
      if (cAmt > balanceDue + 0.001) { showErr('err_cheque_amount', 'Cheque amount cannot exceed balance (' + SYM + balanceDue.toFixed(2) + ')'); ok = false; }
    }
  }

  return ok;
}

/* ── Build payment reference ────────────────────────── */
function buildPaymentRef() {
  const m = document.getElementById('payment_method').value;
  if (m === 'UPI')           return document.getElementById('upi_ref').value.trim();
  if (m === 'Card')          return document.getElementById('card_ref').value.trim();
  if (m === 'Bank Transfer') return document.getElementById('bank_ref').value.trim();
  if (m === 'Cheque')        return document.getElementById('cheque_no').value.trim();
  return '';
}

function buildChequeData() {
  const m = document.getElementById('payment_method').value;
  if (m !== 'Cheque') return {};
  return {
    cheque_bank_name: document.getElementById('cheque_bank_name').value.trim(),
    cheque_no:        document.getElementById('cheque_no').value.trim(),
    cheque_date:      document.getElementById('cheque_date').value,
    cheque_amount:    parseFloat(document.getElementById('cheque_amount').value) || 0,
  };
}

/* ── Get items data ─────────────────────────────────── */
function getItemsData() {
  return Array.from(document.querySelectorAll('#items tr')).map(row => ({
    product_name:   row.querySelector('.product_name').value.trim(),
    unit_price:     parseFloat(row.querySelector('.unit_price').value)     || 0,
    quantity:       parseFloat(row.querySelector('.quantity').value)       || 0,
    discount_price: parseFloat(row.querySelector('.discount_price').value) || 0,
    tax_percent:    parseFloat(row.querySelector('.tax_percent').value)    || 0,
    tax_amount:     parseFloat(row.querySelector('.tax_amount').textContent)|| 0,
    line_total:     parseFloat(row.querySelector('.line_total').textContent)|| 0,
  }));
}

/* ── Save order ─────────────────────────────────────── */
function saveOrder(event) {
  if (!validateOrder()) {
    toast('Please fix the highlighted errors.', 'error');
    // scroll to first error
    const first = document.querySelector('.pos-error[style*="block"], .is-invalid');
    if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
    return;
  }

  const items = getItemsData().filter(i => i.product_name && i.unit_price > 0);
  const billingAddr = {
    line1:   document.getElementById('billing_address_line1').value.trim(),
    city:    document.getElementById('billing_city').value.trim(),
    state:   document.getElementById('billing_state').value.trim(),
    pincode: document.getElementById('billing_pincode').value.trim(),
  };
  const shippingAddr = document.getElementById('sameAddress').checked ? billingAddr : {
    line1:   document.getElementById('shipping_address_line1').value.trim(),
    city:    document.getElementById('shipping_city').value.trim(),
    state:   document.getElementById('shipping_state').value.trim(),
    pincode: document.getElementById('shipping_pincode').value.trim(),
  };

  const btn = event.currentTarget;
  btn.disabled = true;
  btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';

  postJson('Dashboard/savePosOrder', {
    customer_name:     document.getElementById('customer_name').value.trim(),
    customer_phone:    document.getElementById('customer_phone').value.trim(),
    customer_email:    document.getElementById('customer_email').value.trim(),
    billing_address:   billingAddr,
    shipping_address:  shippingAddr,
    items,
    subtotal:          parseFloat(subtotalEl.textContent)   || 0,
    total_tax:         parseFloat(totalTaxEl.textContent)   || 0,
    total_discount:    items.reduce((s, i) => s + (i.discount_price || 0), 0),
    shipping_charges:  parseFloat(document.getElementById('shipping_charges').value) || 0,
    grand_total:       parseFloat(grandTotalEl.textContent) || 0,
    amount_paid:       parseFloat(amountPaidEl.value)       || 0,
    payment_method:    document.getElementById('payment_method').value,
    payment_reference: buildPaymentRef(),
    payment_date:      document.getElementById('payment_date').value,
    ...buildChequeData(),
  }).then(res => {
    btn.disabled = false;
    btn.innerHTML = '<i class="fa fa-save"></i> Save Order';
    if (res.status == 1) {
      lastSavedOrder = {
        order_number:      res.order_number,
        order_id:          res.order_id,
        customer_name:     document.getElementById('customer_name').value.trim(),
        customer_phone:    document.getElementById('customer_phone').value.trim(),
        payment_date:      document.getElementById('payment_date').value,
        payment_method:    document.getElementById('payment_method').value,
        items,
        subtotal:          parseFloat(subtotalEl.textContent)   || 0,
        total_tax:         parseFloat(totalTaxEl.textContent)   || 0,
        shipping_charges:  parseFloat(document.getElementById('shipping_charges').value) || 0,
        grand_total:       parseFloat(grandTotalEl.textContent) || 0,
        amount_paid:       parseFloat(amountPaidEl.value)       || 0,
      };
      lastBalancePayment = null;
      renderReceipt();
      toast('Order saved! #' + res.order_number + (res.balance_due > 0 ? ' — Balance: ' + SYM + parseFloat(res.balance_due).toFixed(2) : ' — Fully paid'), 'success');
      setTimeout(() => { window.location.href = '<?= $posListUrl ?>'; }, 1500);
    } else {
      toast('Error: ' + (res.msg || 'Could not save order'), 'error');
    }
  });
}

/* ── Receipt ────────────────────────────────────────── */
function num(v) { return (parseFloat(v) || 0).toFixed(2); }

function renderReceipt() {
  const el = document.getElementById('receiptContent');
  let html = '';
  if (lastSavedOrder) {
    const o = lastSavedOrder;
    html = `<div class="receipt-center"><h5>MySkool POS</h5><p>Sales Receipt</p></div>
<div class="receipt-line"></div>
<p><strong>Order:</strong> ${o.order_number}</p>
<p><strong>Date:</strong> ${o.payment_date || '-'}</p>
<p><strong>Customer:</strong> ${o.customer_name || '-'}</p>
<p><strong>Mobile:</strong> ${o.customer_phone || '-'}</p>
<div class="receipt-line"></div>
<table class="receipt-table"><thead><tr><th>Item</th><th>Amt</th></tr></thead><tbody>
${(o.items||[]).map(i=>`<tr><td>${i.product_name}<br><small>${i.quantity}x${SYM}${num(i.unit_price)} GST${i.tax_percent}%</small></td><td>${SYM}${num(i.line_total)}</td></tr>`).join('')}
</tbody></table>
<div class="receipt-line"></div>
<p><strong>Subtotal:</strong> ${SYM}${num(o.subtotal)}</p>
<p><strong>GST:</strong> ${SYM}${num(o.total_tax)}</p>
${o.shipping_charges > 0 ? `<p><strong>Shipping:</strong> ${SYM}${num(o.shipping_charges)}</p>` : ''}
<p><strong>Grand Total:</strong> ${SYM}${num(o.grand_total)}</p>
<p><strong>Paid:</strong> ${SYM}${num(o.amount_paid)}</p>
<p><strong>Balance:</strong> ${SYM}${num(Math.max(0, o.grand_total - o.amount_paid))}</p>
<div class="receipt-line"></div>
<p><strong>Method:</strong> ${o.payment_method}</p>
<div class="receipt-line"></div>
<div class="receipt-center"><p>Thank you</p></div>`;
  } else {
    html = `<div class="receipt-center"><h5>MySkool POS</h5><p>No receipt data yet</p></div>`;
  }
  el.innerHTML = html;
}

function printReceipt() { renderReceipt(); window.print(); }

/* ── Init ───────────────────────────────────────────── */
(function init() {
  setTodayDates();
  addRow();
  handlePaymentUI();
  toggleShipping();
  calculate();
  renderReceipt();
})();
</script>
