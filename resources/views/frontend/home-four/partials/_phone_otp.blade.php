{{--
  WhatsApp OTP Phone Verification Partial
  ----------------------------------------
  Usage: @include('frontend.home-four.partials._phone_otp', [
      'formId'      => 'leadForm',          -- ID of the parent form
      'phoneInputId'=> 'lead_phone',        -- ID of the phone <input> in that form
      'submitBtnId' => 'leadSubmitBtn',     -- ID of the form's submit button
      'inputClass'  => 'vl-field-input',   -- (optional) extra class on inputs
  ])

  The partial renders:
    1. Phone input with "Send OTP" button
    2. Hidden OTP entry row (shown after send)
    3. Verified badge (shown after verify)

  It also injects a self-scoped <script> that handles the full flow.
  The submit button stays disabled until verification succeeds.
--}}

@php
  $formId       = $formId       ?? 'enquiryForm';
  $phoneInputId = $phoneInputId ?? 'otp_phone';
  $submitBtnId  = $submitBtnId  ?? 'submitBtn';
  $inputClass   = $inputClass   ?? '';
  // unique prefix so multiple partials on the same page don't clash
  $uid = 'otp_' . Str::random(6);
@endphp

{{-- ── Hidden verified flag (submitted with form) ── --}}
<input type="hidden" name="phone_verified" id="{{ $uid }}_verified_flag" value="0">

{{-- ── Phone row ── --}}
<div class="{{ $uid }}_phone_row" style="display:flex; gap:8px; align-items:flex-end;">
  <div style="flex:1;">
    <label for="{{ $phoneInputId }}"
           style="display:block; font-size:12px; font-weight:700; text-transform:uppercase;
                  letter-spacing:.06em; color:#374151; margin-bottom:6px;">
      Phone Number <span style="color:#ef4444;">*</span>
    </label>
    <input type="tel"
           id="{{ $phoneInputId }}"
           name="phone"
           required
           placeholder="+91 98765 43210"
           autocomplete="tel"
           class="{{ $inputClass }}"
           style="width:100%; border-radius:10px; border:1.5px solid #d1d5db;
                  background:#f9fafb; padding:11px 14px; font-size:14px;
                  transition:border-color .2s; outline:none;"
           onfocus="this.style.borderColor='#6366f1'"
           onblur="this.style.borderColor='#d1d5db'">
  </div>
  <button type="button"
          id="{{ $uid }}_send_btn"
          onclick="{{ $uid }}_sendOtp()"
          style="flex-shrink:0; padding:11px 18px; border-radius:10px;
                 background:linear-gradient(135deg,#25D366,#128C7E);
                 color:#fff; font-size:13px; font-weight:700; border:none;
                 cursor:pointer; white-space:nowrap; display:flex;
                 align-items:center; gap:7px; transition:opacity .2s;">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
      <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
    </svg>
    Send OTP
  </button>
</div>

{{-- ── Status / timer line ── --}}
<div id="{{ $uid }}_status" style="font-size:12px; margin-top:6px; min-height:18px;"></div>

{{-- ── OTP entry row (hidden until OTP is sent) ── --}}
<div id="{{ $uid }}_otp_row" style="display:none; margin-top:10px;">
  <label style="display:block; font-size:12px; font-weight:700; text-transform:uppercase;
                letter-spacing:.06em; color:#374151; margin-bottom:6px;">
    Enter 6-Digit OTP
  </label>
  <div style="display:flex; gap:8px; align-items:center;">
    <input type="text"
           id="{{ $uid }}_otp_input"
           maxlength="6"
           inputmode="numeric"
           pattern="\d{6}"
           placeholder="- - - - - -"
           autocomplete="one-time-code"
           class="{{ $inputClass }}"
           style="flex:1; border-radius:10px; border:1.5px solid #d1d5db;
                  background:#f9fafb; padding:11px 14px; font-size:18px;
                  letter-spacing:6px; text-align:center; outline:none;
                  transition:border-color .2s;"
           onfocus="this.style.borderColor='#6366f1'"
           onblur="this.style.borderColor='#d1d5db'">
    <button type="button"
            id="{{ $uid }}_verify_btn"
            onclick="{{ $uid }}_verifyOtp()"
            style="flex-shrink:0; padding:11px 20px; border-radius:10px;
                   background:linear-gradient(135deg,#6366f1,#4f46e5);
                   color:#fff; font-size:13px; font-weight:700; border:none;
                   cursor:pointer; white-space:nowrap; transition:opacity .2s;">
      Verify
    </button>
  </div>
  <div style="margin-top:8px; font-size:12px; color:#6b7280;">
    Didn't receive it?
    <button type="button"
            id="{{ $uid }}_resend_btn"
            onclick="{{ $uid }}_sendOtp(true)"
            style="background:none; border:none; color:#6366f1; font-weight:700;
                   cursor:pointer; padding:0; font-size:12px; text-decoration:underline;">
      Resend OTP
    </button>
    <span id="{{ $uid }}_resend_timer" style="color:#9ca3af;"></span>
  </div>
</div>

{{-- ── Verified badge (hidden until verified) ── --}}
<div id="{{ $uid }}_verified_badge" style="display:none; margin-top:10px;
     padding:10px 16px; background:#f0fdf4; border:1.5px solid #86efac;
     border-radius:10px; display:none; align-items:center; gap:10px;">
  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5">
    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
  </svg>
  <div>
    <p style="font-size:13px; font-weight:700; color:#15803d; margin:0;">WhatsApp number verified ✓</p>
    <p id="{{ $uid }}_verified_phone_display"
       style="font-size:11px; color:#16a34a; margin:0;"></p>
  </div>
</div>

{{-- ── Self-scoped script ── --}}
<script>
(function () {
  var UID          = '{{ $uid }}';
  var FORM_ID      = '{{ $formId }}';
  var PHONE_ID     = '{{ $phoneInputId }}';
  var SUBMIT_ID    = '{{ $submitBtnId }}';
  var SEND_URL     = '{{ route("phone.otp.send") }}';
  var VERIFY_URL   = '{{ route("phone.otp.verify") }}';
  var CSRF         = document.querySelector('meta[name="csrf-token"]')?.content || '';

  var resendTimer  = null;

  function el(id) { return document.getElementById(id); }

  function setStatus(msg, color) {
    var s = el(UID + '_status');
    if (s) { s.textContent = msg; s.style.color = color || '#6b7280'; }
  }

  function setSendBtnState(loading) {
    var btn = el(UID + '_send_btn');
    if (!btn) return;
    btn.disabled    = loading;
    btn.style.opacity = loading ? '0.6' : '1';
    btn.innerHTML   = loading
      ? '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> Sending…'
      : '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg> Send OTP';
  }

  function startResendTimer(seconds) {
    clearInterval(resendTimer);
    var remaining = seconds;
    var timerEl   = el(UID + '_resend_timer');
    var resendBtn = el(UID + '_resend_btn');
    if (resendBtn) resendBtn.disabled = true;

    resendTimer = setInterval(function () {
      remaining--;
      if (timerEl) timerEl.textContent = ' (' + remaining + 's)';
      if (remaining <= 0) {
        clearInterval(resendTimer);
        if (timerEl)   timerEl.textContent = '';
        if (resendBtn) resendBtn.disabled  = false;
      }
    }, 1000);
  }

  // ── Send OTP ──────────────────────────────────────────────
  window[UID + '_sendOtp'] = function (isResend) {
    var phoneInput = el(PHONE_ID);
    if (!phoneInput || !phoneInput.value.trim()) {
      setStatus('Please enter your phone number first.', '#ef4444');
      if (phoneInput) phoneInput.focus();
      return;
    }

    setSendBtnState(true);
    setStatus('Sending OTP via WhatsApp…', '#6b7280');

    var fd = new FormData();
    fd.append('phone', phoneInput.value.trim());
    fd.append('_token', CSRF);

    fetch(SEND_URL, { method: 'POST', headers: { 'Accept': 'application/json' }, body: fd })
      .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
      .then(function (res) {
        setSendBtnState(false);
        if (res.ok && res.data.status === 'sent') {
          setStatus('OTP sent! Check your WhatsApp.', '#16a34a');
          el(UID + '_otp_row').style.display = 'block';
          el(UID + '_otp_input').focus();
          startResendTimer(60);
          // Debug mode: auto-fill OTP
          if (res.data.debug_otp) {
            el(UID + '_otp_input').value = res.data.debug_otp;
            setStatus('OTP sent! (Debug: ' + res.data.debug_otp + ')', '#f59e0b');
          }
        } else if (res.data.status === 'rate_limited') {
          setStatus(res.data.message, '#f59e0b');
          startResendTimer(res.data.wait || 60);
          el(UID + '_otp_row').style.display = 'block';
        } else {
          setStatus(res.data.message || 'Failed to send OTP. Please try again.', '#ef4444');
        }
      })
      .catch(function () {
        setSendBtnState(false);
        setStatus('Network error. Please check your connection.', '#ef4444');
      });
  };

  // ── Verify OTP ────────────────────────────────────────────
  window[UID + '_verifyOtp'] = function () {
    var phoneInput = el(PHONE_ID);
    var otpInput   = el(UID + '_otp_input');
    var verifyBtn  = el(UID + '_verify_btn');

    var otp = otpInput ? otpInput.value.trim() : '';
    if (otp.length !== 6 || !/^\d+$/.test(otp)) {
      setStatus('Please enter the 6-digit OTP.', '#ef4444');
      return;
    }

    verifyBtn.disabled   = true;
    verifyBtn.textContent = 'Verifying…';
    setStatus('Verifying…', '#6b7280');

    var fd = new FormData();
    fd.append('phone', phoneInput.value.trim());
    fd.append('otp',   otp);
    fd.append('_token', CSRF);

    fetch(VERIFY_URL, { method: 'POST', headers: { 'Accept': 'application/json' }, body: fd })
      .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
      .then(function (res) {
        verifyBtn.disabled    = false;
        verifyBtn.textContent = 'Verify';

        if (res.data.status === 'verified') {
          // ── Success ──
          setStatus('', '');
          el(UID + '_otp_row').style.display       = 'none';
          el(UID + '_phone_row_wrap') && (el(UID + '_phone_row_wrap').style.pointerEvents = 'none');

          // Lock phone input
          if (phoneInput) { phoneInput.readOnly = true; phoneInput.style.background = '#f0fdf4'; }

          // Show badge
          var badge = el(UID + '_verified_badge');
          if (badge) {
            badge.style.display = 'flex';
            var disp = el(UID + '_verified_phone_display');
            if (disp) disp.textContent = phoneInput.value.trim();
          }

          // Set hidden flag
          var flag = el(UID + '_verified_flag');
          if (flag) flag.value = '1';

          // Unlock submit button
          var submitBtn = el(SUBMIT_ID);
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
            submitBtn.style.cursor  = 'pointer';
          }

          clearInterval(resendTimer);

        } else {
          // ── Failure ──
          setStatus(res.data.message || 'Incorrect OTP.', '#ef4444');
          if (otpInput) { otpInput.value = ''; otpInput.focus(); }

          if (res.data.status === 'expired' || res.data.status === 'blocked') {
            el(UID + '_otp_row').style.display = 'none';
            clearInterval(resendTimer);
          }
        }
      })
      .catch(function () {
        verifyBtn.disabled    = false;
        verifyBtn.textContent = 'Verify';
        setStatus('Network error. Please try again.', '#ef4444');
      });
  };

  // ── On page load: lock the form submit button until verified ──
  document.addEventListener('DOMContentLoaded', function () {
    var submitBtn = el(SUBMIT_ID);
    if (submitBtn) {
      submitBtn.disabled      = true;
      submitBtn.style.opacity = '0.45';
      submitBtn.style.cursor  = 'not-allowed';
      submitBtn.title         = 'Please verify your phone number first';
    }

    // Allow OTP input submit on Enter
    var otpInput = el(UID + '_otp_input');
    if (otpInput) {
      otpInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); window[UID + '_verifyOtp'](); }
      });
    }
  });
}());
</script>
