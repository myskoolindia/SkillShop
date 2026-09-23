<?php
$csrfName  = csrf_token();
$csrfHash  = csrf_hash();
$baseUrl   = rtrim(base_url(), '/') . '/';
$posListUrl = dashboardUrl('pos-sales');
$sym       = !empty($currency) ? esc($currency->symbol) : '₹';

// Order passed from controller
$o = $order ?? null;
?>
<style>
.pos-card{background:#fff;border-radius:6px;box-shadow:0 1px 6px rgba(0,0,0,.1);margin-bottom:18px}
.pos-card-header{padding:12px 16px;border-bottom:1px solid #f0f0f0;font-weight:700;font-size:14px;color:#333}
.pos-card-body{padding:14px 16px}
.pos-label{display:block;font-size:13px;font-weight:600;color:#555;margin-bottom:4px}
.pos-input{width:100%;padding:6px 10px;border:1px solid #ccd0d5;border-radius:4px;font-size:14px;height:34px;box-sizing:border-box}
.pos-input:focus{border-color:#3c8dbc;outline:none;box-shadow:0 0 0 2px rgba(60,141,188,.2)}
.pos-input.is-invalid{border-color:#dd4b39!important}
.pos-input[readonly]{background:#f5f7fa}
.pos-error{color:#dd4b39;font-size:12px;margin-top:2px;display:none}
.pos-select{width:100%;padding:6px 10px;border:1px solid #ccd0d5;border-radius:4px;font-size:14px;height:34px;box-sizing:border-box;background:#fff}
.pos-row{display:flex;flex-wrap:wrap;margin:0 -6px}
.pos-col{padding:0 6px;box-sizing:border-box}
.pos-col-3{width:25%}.pos-col-4{width:33.333%}.pos-col-6{width:50%}.pos-col-12{width:100%}
@media(max-width:768px){.pos-col-3,.pos-col-4,.pos-col-6{width:100%}}
.pos-field{margin-bottom:12px}
.pos-summary{background:#f8fafc;border-radius:6px;padding:14px}
.pos-sum-row{display:flex;justify-content:space-between;padding:6px 0;font-size:14px;border-bottom:1px solid #eef0f3}
.pos-sum-row:last-child{border-bottom:none}
.pos-sum-row.grand{font-weight:700;font-size:15px;color:#222;border-top:2px dashed #c8d0da;border-bottom:none;padding-top:8px;margin-top:4px}
.pos-toast{position:fixed;top:18px;right:18px;z-index:9999;min-width:260px;max-width:340px;padding:12px 16px;border-radius:5px;font-size:14px;color:#fff;box-shadow:0 4px 14px rgba(0,0,0,.18);display:none;animation:fadeIn .25s}
.pos-toast.success{background:#00a65a}.pos-toast.error{background:#dd4b39}.pos-toast.info{background:#3c8dbc}
@keyframes fadeIn{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:translateY(0)}}
.receipt-paper{width:300px;margin:0 auto;padding:12px;background:#fff;color:#000;font-family:"Courier New",monospace;font-size:11px;line-height:1.4}
.receipt-paper p,.receipt-paper h5{margin:0;padding:0}
.receipt-center{text-align:center}
.receipt-line{border-top:1px dashed #000;margin:7px 0}
#receiptWrapper{display:none}
@media print{body *{visibility:hidden!important}#receiptWrapper,#receiptWrapper *{visibility:visible!important}#receiptWrapper{display:block!important;position:fixed;inset:0;background:#fff;z-index:99999}}
</style>

<div id="posToast" class="pos-toast"></div>

<?php if (empty($o)): ?>
<div class="alert alert-danger">Order not found. <a href="<?= $posListUrl ?>">Back to POS Orders</a></div>
<?php else: ?>

<div style="padding:0 4px">

  <!-- Header -->
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:8px">
    <h4 style="margin:0;font-size:1.1rem;font-weight:700;color:#333">
      <i class="fa fa-money" style="color:#f39c12"></i> &nbsp;Collect Balance Payment
      <small style="font-size:.8rem;color:#888;font-weight:400">&nbsp;— <?= esc($o->order_number) ?></small>
    </h4>
    <div>
      <a href="<?= $posListUrl ?>" class="btn btn-sm btn-default"><i class="fa fa-list"></i> POS Orders</a>
      <button class="btn btn-sm btn-default" type="button" onclick="printReceipt()"><i class="fa fa-print"></i> Print Receipt</button>
    </div>
  </div>

  <div class="row">
    <!-- LEFT -->
    <div class="col-lg-8 col-md-12">

      <!-- Order Info (read-only) -->
      <div class="pos-card">
        <div class="pos-card-header"><i class="fa fa-info-circle" style="color:#3c8dbc"></i> &nbsp;Order Details</div>
        <div class="pos-card-body">
          <div class="pos-row">
            <div class="pos-col pos-col-4 pos-field">
              <label class="pos-label">Customer</label>
              <input class="pos-input" readonly value="<?= esc($o->customer_name) ?>">
            </div>
            <div class="pos-col pos-col-4 pos-field">
              <label class="pos-label">Phone</label>
              <input class="pos-input" readonly value="<?= esc($o->customer_phone) ?>">
            </div>
            <div class="pos-col pos-col-4 pos-field">
              <label class="pos-label">Order Date</label>
              <input class="pos-input" readonly value="<?= date('d M Y', strtotime($o->created_at)) ?>">
            </div>
          </div>
          <div class="pos-row">
            <div class="pos-col pos-col-3 pos-field">
              <label class="pos-label">Grand Total</label>
              <input class="pos-input" readonly value="<?= $sym . number_format((float)$o->grand_total, 2) ?>">
            </div>
            <div class="pos-col pos-col-3 pos-field">
              <label class="pos-label">Total Paid</label>
              <input class="pos-input" id="info_paid" readonly value="<?= $sym . number_format((float)$o->amount_paid, 2) ?>">
            </div>
            <div class="pos-col pos-col-3 pos-field">
              <label class="pos-label">Balance Due</label>
              <input class="pos-input" id="info_balance" readonly value="<?= $sym . number_format((float)$o->balance_due, 2) ?>" style="font-weight:700;color:#dd4b39">
            </div>
          </div>
        </div>
      </div>

      <!-- Payment Form -->
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
              <label class="pos-label">Pay Amount <span style="color:#dd4b39">*</span></label>
              <input type="number" class="pos-input" id="pay_amount" value="<?= number_format((float)$o->balance_due, 2, '.', '') ?>" min="0.01" step="0.01" oninput="calcCashChange()">
              <span class="pos-error" id="err_pay_amount">Enter a valid amount</span>
            </div>
            <div class="pos-col pos-col-3 pos-field">
              <label class="pos-label">Payment Method</label>
              <select class="pos-select" id="payment_method" onchange="handlePaymentUI()">
                <option value="UPI">UPI</option>
                <option value="Card">Card</option>
                <option value="Cash">Cash</option>
                <option value="Bank Transfer">Bank Transfer</option>
                <option value="Cheque">Cheque</option>
              </select>
            </div>
          </div>

          <!-- UPI -->
          <div id="upiSection" class="pos-row">
            <div class="pos-col pos-col-4 pos-field">
              <label class="pos-label">UPI Reference <span style="color:#dd4b39">*</span></label>
              <input type="text" class="pos-input" id="upi_ref" placeholder="UPI Ref No">
              <span class="pos-error" id="err_upi_ref">UPI reference required</span>
            </div>
          </div>
          <!-- Card -->
          <div id="cardSection" class="pos-row" style="display:none">
            <div class="pos-col pos-col-4 pos-field">
              <label class="pos-label">Card Last 4 <span style="color:#dd4b39">*</span></label>
              <input type="text" class="pos-input" id="card_last4" placeholder="1234" maxlength="4">
              <span class="pos-error" id="err_card_last4">Enter last 4 digits</span>
            </div>
            <div class="pos-col pos-col-4 pos-field">
              <label class="pos-label">Approval Ref</label>
              <input type="text" class="pos-input" id="card_ref" placeholder="Approval / Ref">
            </div>
          </div>
          <!-- Cash -->
          <div id="cashSection" class="pos-row" style="display:none">
            <div class="pos-col pos-col-4 pos-field">
              <label class="pos-label">Cash Received <span style="color:#dd4b39">*</span></label>
              <input type="number" class="pos-input" id="cash_received" placeholder="0.00" min="0" step="0.01" oninput="calcCashChange()">
              <span class="pos-error" id="err_cash_received">Cash received required</span>
            </div>
            <div class="pos-col pos-col-4 pos-field">
              <label class="pos-label">Change</label>
              <input type="text" class="pos-input" id="change_amount" readonly value="0.00">
            </div>
          </div>
          <!-- Bank -->
          <div id="bankSection" class="pos-row" style="display:none">
            <div class="pos-col pos-col-4 pos-field">
              <label class="pos-label">Bank Ref / UTR <span style="color:#dd4b39">*</span></label>
              <input type="text" class="pos-input" id="bank_ref" placeholder="UTR / Bank Ref">
              <span class="pos-error" id="err_bank_ref">Bank reference required</span>
            </div>
          </div>
          <!-- Cheque -->
          <div id="chequeSection" class="pos-row" style="display:none">
            <div class="pos-col pos-col-3 pos-field">
              <label class="pos-label">Bank Name <span style="color:#dd4b39">*</span></label>
              <input type="text" class="pos-input" id="cheque_bank_name" placeholder="Bank Name">
              <span class="pos-error" id="err_cheque_bank_name">Bank name required</span>
            </div>
            <div class="pos-col pos-col-3 pos-field">
              <label class="pos-label">Cheque No <span style="color:#dd4b39">*</span></label>
              <input type="text" class="pos-input" id="cheque_no" placeholder="Cheque Number">
              <span class="pos-error" id="err_cheque_no">Cheque number required</span>
            </div>
            <div class="pos-col pos-col-3 pos-field">
              <label class="pos-label">Cheque Date <span style="color:#dd4b39">*</span></label>
              <input type="date" class="pos-input" id="cheque_date">
              <span class="pos-error" id="err_cheque_date">Cheque date required</span>
            </div>
            <div class="pos-col pos-col-3 pos-field">
              <label class="pos-label">Cheque Amount <span style="color:#dd4b39">*</span></label>
              <input type="number" class="pos-input" id="cheque_amount" min="0" step="0.01" placeholder="0.00">
              <span class="pos-error" id="err_cheque_amount">Cheque amount required</span>
            </div>
          </div>

          <div style="margin-top:10px">
            <button class="btn btn-sm btn-success" type="button" onclick="capturePayment(event)">
              <i class="fa fa-check"></i> Capture Payment
            </button>
          </div>
        </div>
      </div>

    </div><!-- /col-lg-8 -->

    <!-- RIGHT: Summary -->
    <div class="col-lg-4 col-md-12">
      <div style="position:sticky;top:10px">
        <div class="pos-card">
          <div class="pos-card-header"><i class="fa fa-calculator" style="color:#3c8dbc"></i> &nbsp;Payment Summary</div>
          <div class="pos-card-body">
            <div class="pos-summary">
              <div class="pos-sum-row"><span>Grand Total</span><span><?= $sym . number_format((float)$o->grand_total, 2) ?></span></div>
              <div class="pos-sum-row"><span>Previously Paid</span><span id="sum_paid"><?= $sym . number_format((float)$o->amount_paid, 2) ?></span></div>
              <div class="pos-sum-row grand" style="color:#dd4b39"><span>Balance Due</span><span id="sum_balance"><?= $sym . number_format((float)$o->balance_due, 2) ?></span></div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- Receipt (print only) -->
<div id="receiptWrapper">
  <div class="receipt-paper" id="receiptContent"></div>
</div>

<script>
const BASE_URL  = '<?= $baseUrl ?>';
const CSRF_NAME = '<?= $csrfName ?>';
let   csrfHash  = '<?= $csrfHash ?>';
const SYM       = '<?= $sym ?>';
const ORDER_ID  = <?= (int)$o->id ?>;
const GRAND_TOTAL = <?= (float)$o->grand_total ?>;

let lastPayment = null;

function toast(msg, type = 'success') {
  const el = document.getElementById('posToast');
  el.textContent = msg;
  el.className = 'pos-toast ' + type;
  el.style.display = 'block';
  clearTimeout(el._t);
  el._t = setTimeout(() => { el.style.display = 'none'; }, 4000);
}

function postJson(url, data) {
  return fetch(BASE_URL + url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfHash },
    body: JSON.stringify(data)
  })
  .then(r => r.json())
  .then(res => { if (res[CSRF_NAME]) csrfHash = res[CSRF_NAME]; return res; })
  .catch(() => { toast('Network error. Please try again.', 'error'); return { status: 0 }; });
}

function showErr(id, msg) {
  const el = document.getElementById(id); if (!el) return;
  if (msg) el.textContent = msg;
  el.style.display = 'block';
  const inp = el.previousElementSibling;
  if (inp && inp.classList) inp.classList.add('is-invalid');
}
function clearAllErrors() {
  document.querySelectorAll('.pos-error').forEach(e => e.style.display = 'none');
  document.querySelectorAll('.is-invalid').forEach(e => e.classList.remove('is-invalid'));
}

function handlePaymentUI() {
  const m = document.getElementById('payment_method').value;
  ['upiSection','cardSection','cashSection','bankSection','chequeSection'].forEach(id => document.getElementById(id).style.display = 'none');
  if (m === 'UPI')           document.getElementById('upiSection').style.display    = '';
  if (m === 'Card')          document.getElementById('cardSection').style.display   = '';
  if (m === 'Cash')          document.getElementById('cashSection').style.display   = '';
  if (m === 'Bank Transfer') document.getElementById('bankSection').style.display   = '';
  if (m === 'Cheque')        document.getElementById('chequeSection').style.display = '';
}

function calcCashChange() {
  if (document.getElementById('payment_method').value !== 'Cash') return;
  const rec = parseFloat(document.getElementById('cash_received').value) || 0;
  const pay = parseFloat(document.getElementById('pay_amount').value)    || 0;
  document.getElementById('change_amount').value = Math.max(rec - pay, 0).toFixed(2);
}

function validate() {
  clearAllErrors();
  let ok = true;
  const amount  = parseFloat(document.getElementById('pay_amount').value) || 0;
  const balance = parseFloat(document.getElementById('info_balance').value.replace(/[^0-9.]/g,'')) || 0;
  if (amount <= 0) { showErr('err_pay_amount', 'Enter a valid amount greater than 0'); ok = false; }
  else if (amount > balance + 0.001) { showErr('err_pay_amount', 'Amount cannot exceed balance (' + SYM + balance.toFixed(2) + ')'); ok = false; }
  if (!document.getElementById('payment_date').value) { showErr('err_payment_date', 'Payment date required'); ok = false; }
  const m = document.getElementById('payment_method').value;
  if (m === 'UPI' && !document.getElementById('upi_ref').value.trim()) { showErr('err_upi_ref'); ok = false; }
  if (m === 'Card') {
    const l4 = document.getElementById('card_last4').value.trim();
    if (!l4 || !/^\d{4}$/.test(l4)) { showErr('err_card_last4', 'Enter exactly 4 digits'); ok = false; }
  }
  if (m === 'Cash') {
    const rec = parseFloat(document.getElementById('cash_received').value) || 0;
    if (rec < amount) { showErr('err_cash_received', 'Cash received cannot be less than pay amount'); ok = false; }
  }
  if (m === 'Bank Transfer' && !document.getElementById('bank_ref').value.trim()) { showErr('err_bank_ref'); ok = false; }
  if (m === 'Cheque') {
    if (!document.getElementById('cheque_bank_name').value.trim()) { showErr('err_cheque_bank_name'); ok = false; }
    if (!document.getElementById('cheque_no').value.trim())        { showErr('err_cheque_no');        ok = false; }
    if (!document.getElementById('cheque_date').value)             { showErr('err_cheque_date');      ok = false; }
    const cAmt = parseFloat(document.getElementById('cheque_amount').value) || 0;
    if (cAmt <= 0) { showErr('err_cheque_amount', 'Enter a valid cheque amount'); ok = false; }
    else if (cAmt > balance + 0.001) { showErr('err_cheque_amount', 'Cheque amount cannot exceed balance (' + SYM + balance.toFixed(2) + ')'); ok = false; }
  }
  return ok;
}

function capturePayment(event) {
  if (!validate()) { toast('Please fix the highlighted errors.', 'error'); return; }

  const amount = parseFloat(document.getElementById('pay_amount').value);
  const m      = document.getElementById('payment_method').value;
  let ref = '';
  if (m === 'UPI')           ref = document.getElementById('upi_ref').value.trim();
  else if (m === 'Card')     ref = document.getElementById('card_ref').value.trim();
  else if (m === 'Bank Transfer') ref = document.getElementById('bank_ref').value.trim();
  else if (m === 'Cheque')   ref = document.getElementById('cheque_no').value.trim();

  const chequeData = m === 'Cheque' ? {
    cheque_bank_name: document.getElementById('cheque_bank_name').value.trim(),
    cheque_no:        document.getElementById('cheque_no').value.trim(),
    cheque_date:      document.getElementById('cheque_date').value,
    cheque_amount:    parseFloat(document.getElementById('cheque_amount').value) || 0,
  } : {};

  const btn = event.currentTarget;
  btn.disabled = true;
  btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';

  postJson('Dashboard/posBalancePayment', {
    order_id: ORDER_ID, amount, payment_method: m, payment_reference: ref,
    payment_date: document.getElementById('payment_date').value,
  }).then(res => {
    btn.disabled = false;
    btn.innerHTML = '<i class="fa fa-check"></i> Capture Payment';
    if (res.status == 1) {
      const newBalance = parseFloat(res.new_balance);
      const newPaid    = parseFloat(res.new_paid);
      document.getElementById('info_paid').value    = SYM + newPaid.toFixed(2);
      document.getElementById('info_balance').value = SYM + newBalance.toFixed(2);
      document.getElementById('sum_paid').textContent    = SYM + newPaid.toFixed(2);
      document.getElementById('sum_balance').textContent = SYM + newBalance.toFixed(2);
      document.getElementById('pay_amount').value = newBalance > 0 ? newBalance.toFixed(2) : '0.00';
      lastPayment = {
        order_number: '<?= esc($o->order_number) ?>',
        customer_name: '<?= esc($o->customer_name) ?>',
        customer_phone: '<?= esc($o->customer_phone) ?>',
        grand_total: GRAND_TOTAL,
        prev_paid: newPaid - amount,
        amount, method: m, reference: ref,
        payment_date: document.getElementById('payment_date').value,
        new_paid: newPaid, new_balance: newBalance,
      };
      renderReceipt();
      if (res.is_paid) {
        toast('Order fully paid!', 'success');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-check"></i> Fully Paid';
      } else {
        toast('Payment captured! New balance: ' + SYM + newBalance.toFixed(2), 'success');
      }
    } else {
      toast('Error: ' + (res.msg || 'Failed'), 'error');
    }
  });
}

function num(v) { return (parseFloat(v) || 0).toFixed(2); }

function renderReceipt() {
  if (!lastPayment) return;
  const p = lastPayment;
  document.getElementById('receiptContent').innerHTML =
    `<div class="receipt-center"><h5>MySkool POS</h5><p>Balance Payment Receipt</p></div>
<div class="receipt-line"></div>
<p><strong>Order:</strong> ${p.order_number}</p>
<p><strong>Date:</strong> ${p.payment_date || '-'}</p>
<p><strong>Customer:</strong> ${p.customer_name || '-'}</p>
<p><strong>Mobile:</strong> ${p.customer_phone || '-'}</p>
<div class="receipt-line"></div>
<p><strong>Grand Total:</strong> ${SYM}${num(p.grand_total)}</p>
<p><strong>Prev Paid:</strong> ${SYM}${num(p.prev_paid)}</p>
<p><strong>Received Now:</strong> ${SYM}${num(p.amount)}</p>
<p><strong>New Balance:</strong> ${SYM}${num(p.new_balance)}</p>
<div class="receipt-line"></div>
<p><strong>Method:</strong> ${p.method}</p>
${p.reference ? '<p><strong>Ref:</strong> ' + p.reference + '</p>' : ''}
<div class="receipt-line"></div>
<div class="receipt-center"><p>Thank you</p></div>`;
}

function printReceipt() { renderReceipt(); window.print(); }

// Init
document.getElementById('payment_date').value = new Date().toISOString().split('T')[0];
handlePaymentUI();
</script>

<?php endif; ?>
